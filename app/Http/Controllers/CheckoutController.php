<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DetalleVentaProducto;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaEnvio;
use App\Services\AuditoriaService;
use App\Services\ShippingService;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        private ShippingService $shipping,
        private WompiService $wompi,
    ) {}

    /**
     * Muestra el checkout con formulario de envío + resumen del carrito.
     * El envío se muestra en 0 hasta que el usuario seleccione depto+ciudad.
     */
    public function mostrar()
    {
        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles()->with('producto')->get();

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal   = $detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);
        $ciudadesDept = $this->shipping->ciudadesPorDepartamento();
        $usuario    = auth()->user();

        return view('checkout.index', [
            'carrito'      => $carrito,
            'detalles'     => $detalles,
            'subtotal'     => $subtotal,
            'ciudadesDept' => $ciudadesDept,
            'usuario'      => $usuario,
            'wompiPublicKey' => $this->wompi->publicKey(),
        ]);
    }

    /**
     * Endpoint AJAX para recalcular envío al cambiar departamento/ciudad.
     */
    public function calcularEnvio(Request $request)
    {
        $data = $request->validate([
            'departamento' => 'nullable|string|max:80',
            'ciudad'       => 'nullable|string|max:80',
        ]);

        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles;
        $subtotal = $detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

        $envio = $this->shipping->calcular($data['departamento'] ?? null, $data['ciudad'] ?? null, $subtotal);

        return response()->json([
            'subtotal'     => $subtotal,
            'envio'        => $envio,
            'total'        => $subtotal + (float) ($envio['valor'] ?? 0),
            'configurable' => $envio === null,
        ]);
    }

    /**
     * Procesa el checkout: crea la orden pendiente en una transacción
     * y redirige a Wompi (o al modo demostración si no hay llave configurada).
     */
    public function procesar(Request $request)
    {
        $data = $request->validate([
            'nombre_envio'       => 'required|string|max:120',
            'telefono_envio'     => 'required|string|max:40',
            'correo_envio'       => 'required|email|max:120',
            'departamento_envio' => 'required|string|max:80',
            'ciudad_envio'       => 'required|string|max:80',
            'direccion_envio'    => 'required|string|max:300',
            'referencia_envio'   => 'nullable|string|max:300',
        ]);

        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles()->with('producto.inventario')->get();

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        // Backend siempre RECALCULA — no confía en nada del frontend.
        $subtotal = 0.0;
        $errores  = [];
        foreach ($detalles as $d) {
            $producto = $d->producto;
            if (!$producto || !$producto->estado) {
                $errores[] = "El producto \"{$d->producto?->nombre}\" ya no está disponible.";
                continue;
            }
            $stock = $producto->inventario?->stock_actual ?? 0;
            if ($d->cantidad > $stock) {
                $errores[] = "Solo quedan {$stock} unidades de \"{$producto->nombre}\".";
                continue;
            }
            $subtotal += $d->cantidad * $producto->precio;
        }

        if ($errores) {
            return back()->with('error', implode(' ', $errores))->withInput();
        }

        $envio = $this->shipping->calcular(
            $data['departamento_envio'],
            $data['ciudad_envio'],
            $subtotal
        );
        if ($envio === null) {
            return back()->with('error', 'No hay tarifa de envío para el destino. Contacta a soporte.')->withInput();
        }

        $total = $subtotal + (float) $envio['valor'];

        // Transacción atómica: crear Venta + Detalle + VentaEnvio
        try {
            $venta = DB::transaction(function () use ($carrito, $detalles, $subtotal, $envio, $total, $data) {
                $venta = Venta::create([
                    'usuario_id' => auth()->id(),
                    'total'      => $total,
                    'estado'     => 'pendiente',
                    'fecha'      => now(),
                ]);

                foreach ($detalles as $d) {
                    DetalleVentaProducto::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $d->producto_id,
                        'cantidad'        => $d->cantidad,
                        'precio_unitario' => $d->producto->precio,
                        'subtotal'        => $d->cantidad * $d->producto->precio,
                        'iva'             => 0,
                    ]);
                }

                $numeroOrden = sprintf('PM-ORD-%06d', $venta->id);
                $wompiRef    = $this->wompi->nuevaReferencia($venta->id);

                VentaEnvio::create([
                    'venta_id'           => $venta->id,
                    'numero_orden'       => $numeroOrden,
                    'wompi_reference'    => $wompiRef,
                    'payment_status'     => 'PENDING',
                    'estado_pedido'      => 'pendiente',
                    'subtotal'           => $subtotal,
                    'envio'              => $envio['valor'],
                    'total'              => $total,
                    'nombre_envio'       => $data['nombre_envio'],
                    'telefono_envio'     => $data['telefono_envio'],
                    'correo_envio'       => $data['correo_envio'],
                    'departamento_envio' => $data['departamento_envio'],
                    'ciudad_envio'       => $data['ciudad_envio'],
                    'direccion_envio'    => $data['direccion_envio'],
                    'referencia_envio'   => $data['referencia_envio'] ?? null,
                    'tarifa_envio_id'    => $envio['tarifa_id'] ?? null,
                ]);

                // La tabla `carrito` solo acepta 'activo' o 'finalizado' (constraint CHECK).
                // Al crear la orden, el carrito actual se cierra; la próxima visita
                // firstOrCreate() abrirá uno nuevo en estado 'activo'.
                $carrito->update(['estado' => 'finalizado']);

                return $venta;
            });
        } catch (\Throwable $e) {
            Log::error('Checkout falló: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear la orden. Intenta de nuevo.')->withInput();
        }

        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Venta',
            'registro_id'       => $venta->id,
            'registro_etiqueta' => $venta->envio->numero_orden,
            'descripcion'       => "Orden {$venta->envio->numero_orden} creada, esperando pago",
            'valores_anteriores'=> [],
            'valores_nuevos'    => ['total' => $total, 'envio' => $envio['valor']],
        ]);

        // Si Wompi NO está configurado → modo demostración
        if (!$this->wompi->publicKey()) {
            return redirect()->route('checkout.demo', $venta->envio->numero_orden);
        }

        // Redirigir a Wompi Web Checkout
        $amountCents = $this->wompi->toCents($total);
        $signature   = $this->wompi->signature($venta->envio->wompi_reference, $amountCents);

        $params = [
            'public-key'          => $this->wompi->publicKey(),
            'currency'            => $this->wompi->currency(),
            'amount-in-cents'     => $amountCents,
            'reference'           => $venta->envio->wompi_reference,
            'signature:integrity' => $signature,
            'redirect-url'        => route('checkout.resultado', $venta->envio->numero_orden),
            'customer-data:email'      => $data['correo_envio'],
            'customer-data:full-name'  => $data['nombre_envio'],
            'customer-data:phone-number' => $data['telefono_envio'],
            'shipping-address:address-line-1' => $data['direccion_envio'],
            'shipping-address:city'           => $data['ciudad_envio'],
            'shipping-address:country'        => 'CO',
            'shipping-address:region'         => $data['departamento_envio'],
            'shipping-address:name'           => $data['nombre_envio'],
            'shipping-address:phone-number'   => $data['telefono_envio'],
        ];

        return redirect()->away($this->wompi->checkoutUrl($params));
    }

    /**
     * Página que Wompi llama al terminar (por redirect-url).
     * Consulta el estado real por API si tenemos transaction_id en query.
     * NO marca la orden como pagada solo por regresar — eso lo hace el webhook.
     */
    public function resultado(Request $request, string $numeroOrden)
    {
        $envio = VentaEnvio::with('venta.detalleProductos.producto')
            ->where('numero_orden', $numeroOrden)
            ->firstOrFail();

        // Solo el dueño puede ver
        if ($envio->venta->usuario_id !== auth()->id()) abort(403);

        // Si Wompi nos manda el ID, actualizamos referencia rápido (webhook confirmará)
        $trxId = $request->query('id');
        if ($trxId && !$envio->wompi_transaction_id) {
            $envio->update(['wompi_transaction_id' => $trxId]);
        }

        return view('checkout.resultado', compact('envio'));
    }

    /**
     * Modo demostración cuando Wompi no está configurado.
     */
    public function demo(Request $request, string $numeroOrden)
    {
        $envio = VentaEnvio::with('venta')->where('numero_orden', $numeroOrden)->firstOrFail();
        if ($envio->venta->usuario_id !== auth()->id()) abort(403);
        return view('checkout.demo', compact('envio'));
    }

    /**
     * Aprueba/rechaza manualmente una orden en modo demo (sandbox interno).
     */
    public function demoConfirmar(Request $request, string $numeroOrden)
    {
        $envio = VentaEnvio::with('venta')->where('numero_orden', $numeroOrden)->firstOrFail();
        if ($envio->venta->usuario_id !== auth()->id()) abort(403);

        $resultado = $request->input('resultado', 'aprobado');

        DB::transaction(function () use ($envio, $resultado) {
            if ($resultado === 'aprobado') {
                $envio->update([
                    'payment_status'       => 'APPROVED',
                    'estado_pedido'        => 'confirmado',
                    'fecha_pago'           => now(),
                    'wompi_payment_method' => 'DEMO',
                    'wompi_transaction_id' => 'DEMO-' . strtoupper(bin2hex(random_bytes(4))),
                ]);
                $envio->venta->update(['estado' => 'pagada']);
            } else {
                $envio->update(['payment_status' => 'DECLINED']);
                $envio->venta->update(['estado' => 'cancelada']);
            }
        });

        return redirect()->route('checkout.resultado', $envio->numero_orden);
    }

    // ─── Helpers ────────────────────────────────────────────

    private function carritoActivo(): Carrito
    {
        return Carrito::firstOrCreate(
            ['usuario_id' => auth()->id(), 'estado' => 'activo'],
            ['usuario_id' => auth()->id(), 'estado' => 'activo']
        );
    }
}
