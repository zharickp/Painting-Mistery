<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DetalleVentaProducto;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaEnvio;
use App\Services\AuditoriaService;
use App\Services\PagoOrdenService;
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
        private PagoOrdenService $pagoOrden,
    ) {}

    /**
     * Muestra el checkout con formulario de envío + resumen del carrito.
     * El envío ya se conoce al renderizar (depende del subtotal del
     * carrito, no del destino), así que la barra de progreso hacia el
     * envío gratis se muestra de inmediato, sin esperar ninguna llamada AJAX.
     */
    public function mostrar()
    {
        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles()->with('producto')->get();

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal     = $detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);
        $envio        = $this->shipping->calcularPorSubtotal($subtotal);
        $ciudadesDept = $this->shipping->ciudadesPorDepartamento();
        $usuario      = auth()->user();
        $tiposDocumento = \App\Models\TipoDocumento::orderBy('nombre')->get();

        return view('checkout.index', [
            'carrito'        => $carrito,
            'detalles'       => $detalles,
            'subtotal'       => $subtotal,
            'envio'          => $envio,
            'total'          => $subtotal + $envio['valor'],
            'ciudadesDept'   => $ciudadesDept,
            'usuario'        => $usuario,
            'tiposDocumento' => $tiposDocumento,
            'wompiPublicKey' => $this->wompi->publicKey(),
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
            'tipo_documento'     => 'required|string|max:10',
            'numero_documento'   => 'required|string|max:20',
            'departamento_envio' => 'required|string|max:80',
            'ciudad_envio'       => 'required|string|max:80',
            'direccion_envio'    => 'required|string|max:300',
            'referencia_envio'   => 'nullable|string|max:300',
            'acepto_terminos'    => 'accepted',
        ], [
            'acepto_terminos.accepted' => 'Debes aceptar los términos y condiciones para continuar.',
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

        // El envío depende del valor del carrito, no del destino — siempre
        // devuelve un valor (nunca null), así que no hace falta validar
        // "tarifa no configurada" como antes.
        $envio = $this->shipping->calcularPorSubtotal($subtotal);
        $total = $subtotal + (float) $envio['valor'];

        // Transacción atómica: crear Venta + Detalle + VentaEnvio.
        // Guardamos $ventaEnvio (el objeto recién creado, en memoria) en vez de
        // volver a pedirlo después vía $venta->envio: justo al crear la Venta,
        // el observer de Auditable dispara auditoriaEtiqueta(), que consulta
        // esa relación cuando el VentaEnvio hermano AÚN no existe — evitamos
        // depender de la propiedad mágica para no toparnos con ese caché.
        try {
            [$venta, $ventaEnvio] = DB::transaction(function () use ($carrito, $detalles, $subtotal, $envio, $total, $data) {
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

                $ventaEnvio = VentaEnvio::create([
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
                    'tipo_documento'     => $data['tipo_documento'],
                    'numero_documento'   => $data['numero_documento'],
                    'acepto_terminos'    => true,
                    'departamento_envio' => $data['departamento_envio'],
                    'ciudad_envio'       => $data['ciudad_envio'],
                    'direccion_envio'    => $data['direccion_envio'],
                    'referencia_envio'   => $data['referencia_envio'] ?? null,
                ]);

                // La tabla `carrito` solo acepta 'activo' o 'finalizado' (constraint CHECK).
                // Al crear la orden, el carrito actual se cierra; la próxima visita
                // firstOrCreate() abrirá uno nuevo en estado 'activo'.
                $carrito->update(['estado' => 'finalizado']);

                return [$venta, $ventaEnvio];
            });
        } catch (\Throwable $e) {
            Log::error('Checkout falló: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear la orden. Intenta de nuevo.')->withInput();
        }

        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Venta',
            'registro_id'       => $venta->id,
            'registro_etiqueta' => $ventaEnvio->numero_orden,
            'descripcion'       => "Orden {$ventaEnvio->numero_orden} creada, esperando pago",
            'valores_anteriores'=> [],
            'valores_nuevos'    => ['total' => $total, 'envio' => $envio['valor']],
        ]);

        // Modo demostración: controlado explícitamente por PAGO_MODO_DEMO en .env,
        // o automáticamente si aún no hay llaves de Wompi configuradas.
        if ($this->wompi->debeUsarDemo()) {
            return redirect()->route('checkout.demo', $ventaEnvio->numero_orden);
        }

        // Redirigir a Wompi Web Checkout
        $amountCents = $this->wompi->toCents($total);
        $signature   = $this->wompi->signature($ventaEnvio->wompi_reference, $amountCents);

        $params = [
            'public-key'          => $this->wompi->publicKey(),
            'currency'            => $this->wompi->currency(),
            'amount-in-cents'     => $amountCents,
            'reference'           => $ventaEnvio->wompi_reference,
            'signature:integrity' => $signature,
            'redirect-url'        => route('checkout.resultado', $ventaEnvio->numero_orden),
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
     *
     * El webhook es la fuente de verdad "oficial" del pago, pero en
     * desarrollo local Wompi no puede alcanzar http://127.0.0.1 para
     * enviarlo. Por eso, aquí SIEMPRE se consulta el estado real de la
     * transacción directamente a la API de Wompi (nunca se confía en
     * el simple hecho de que el usuario haya vuelto) y se aplica con la
     * misma lógica que usa el webhook — así el resultado se refleja
     * igual, tengas o no un dominio público configurado.
     */
    public function resultado(Request $request, string $numeroOrden)
    {
        $envio = VentaEnvio::with('venta.detalleProductos.producto')
            ->where('numero_orden', $numeroOrden)
            ->firstOrFail();

        if ($envio->venta->usuario_id !== auth()->id()) abort(403);

        $trxId = $envio->wompi_transaction_id ?: $request->query('id');

        if ($trxId && $envio->payment_status !== 'APPROVED') {
            $trx = $this->wompi->consultarTransaccion($trxId);
            if ($trx) {
                $this->pagoOrden->aplicar(
                    $envio,
                    $trx['status'] ?? 'PENDING',
                    $trx['id'] ?? $trxId,
                    $trx['payment_method_type'] ?? null,
                    'consulta-directa'
                );
                $envio->refresh();
            }
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

        $svc = app(\App\Services\OrdenEstadoService::class);

        if ($resultado === 'aprobado') {
            $svc->confirmarPago($envio, null, 'demo', 'DEMO', 'DEMO-' . strtoupper(bin2hex(random_bytes(4))));
        } else {
            $svc->cancelar($envio, null, 'Pago rechazado en modo demo', 'demo', 'DECLINED');
        }

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
