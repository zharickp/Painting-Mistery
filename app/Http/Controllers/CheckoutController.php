<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DetalleVentaProducto;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\TipoDocumento;
use App\Models\Venta;
use App\Services\AuditoriaService;
use App\Services\OrdenEstadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Compra en la tienda.
 *
 *   1. mostrar()   datos del comprador + método de pago
 *   2. procesar()  crea la venta pendiente y su pago pendiente
 *   3. pago()      confirmación del pago
 *   4. pagar()     registra el pago y confirma la venta
 *   5. resultado() muestra cómo terminó la compra
 */
class CheckoutController extends Controller
{
    public function __construct(private OrdenEstadoService $estados) {}

    public function mostrar()
    {
        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles()->with('producto')->get();

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        return view('checkout.index', [
            'detalles'       => $detalles,
            'total'          => $detalles->sum(fn ($d) => $d->cantidad * $d->producto->precio),
            'usuario'        => auth()->user(),
            'tiposDocumento' => TipoDocumento::orderBy('nombre')->get(),
            'metodosPago'    => MetodoPago::paraCheckout()->get(),
        ]);
    }

    public function procesar(Request $request)
    {
        $data = $request->validate([
            'nombre_cliente'   => 'required|string|max:120',
            'telefono_cliente' => 'required|string|max:40',
            'correo_cliente'   => 'required|email|max:120',
            'tipo_documento'   => 'required|string|max:10',
            'numero_documento' => 'required|string|max:20',
            'metodo_pago_id'   => ['required', Rule::exists('metodo_pago', 'id')->where('estado', true)->where('en_linea', true)],
            'acepto_terminos'  => 'accepted',
        ], [
            'metodo_pago_id.required'  => 'Elige un método de pago.',
            'acepto_terminos.accepted' => 'Debes aceptar los términos y condiciones para continuar.',
        ]);

        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles()->with(['producto.inventario', 'producto.tipoIva'])->get();

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        // Se recalcula todo en el servidor: no se confía en los valores del navegador.
        $total   = 0.0;
        $errores = [];
        foreach ($detalles as $d) {
            $producto = $d->producto;
            if (! $producto || ! $producto->estado) {
                $errores[] = "El producto \"{$d->producto?->nombre}\" ya no está disponible.";
                continue;
            }
            $stock = $producto->inventario?->stock_actual ?? 0;
            if ($d->cantidad > $stock) {
                $errores[] = "Solo quedan {$stock} unidades de \"{$producto->nombre}\".";
                continue;
            }
            $total += $d->cantidad * $producto->precio;
        }

        if ($errores) {
            return back()->with('error', implode(' ', $errores))->withInput();
        }

        try {
            $venta = DB::transaction(function () use ($carrito, $detalles, $total, $data) {
                $venta = Venta::create([
                    'usuario_id'       => auth()->id(),
                    'total'            => $total,
                    'estado'           => 'pendiente',
                    'fecha'            => now(),
                    'nombre_cliente'   => $data['nombre_cliente'],
                    'telefono_cliente' => $data['telefono_cliente'],
                    'correo_cliente'   => $data['correo_cliente'],
                    'tipo_documento'   => $data['tipo_documento'],
                    'numero_documento' => $data['numero_documento'],
                ]);

                // El número de orden depende del id, que solo existe después de insertar.
                // saveQuietly: no genera una entrada de auditoría extra por este ajuste interno.
                $venta->forceFill(['numero_orden' => sprintf('PM-ORD-%06d', $venta->id)])->saveQuietly();

                foreach ($detalles as $d) {
                    $subtotal = $d->cantidad * $d->producto->precio;

                    DetalleVentaProducto::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $d->producto_id,
                        'cantidad'        => $d->cantidad,
                        'precio_unitario' => $d->producto->precio,
                        'subtotal'        => $subtotal,
                        // El precio ya incluye el IVA: aquí se guarda cuánto de ese valor es impuesto.
                        'iva'             => $d->producto->tipoIva?->ivaIncluido($subtotal) ?? 0,
                    ]);
                }

                Pago::create([
                    'venta_id'           => $venta->id,
                    'metodo_pago_id'     => $data['metodo_pago_id'],
                    'numero_comprobante' => $this->estados->nuevoComprobante($venta),
                    'valor'              => $total,
                    'estado'             => 'pendiente',
                ]);

                // La tabla `carrito` solo acepta 'activo' o 'finalizado' (CHECK).
                $carrito->update(['estado' => 'finalizado']);

                return $venta;
            });
        } catch (\Throwable $e) {
            Log::error('Checkout falló: ' . $e->getMessage());
            return back()->with('error', 'No se pudo crear la compra. Inténtalo de nuevo.')->withInput();
        }

        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Venta',
            'registro_id'       => $venta->id,
            'registro_etiqueta' => $venta->numero_orden,
            'descripcion'       => "Venta {$venta->numero_orden} creada, pendiente de pago",
            'valores_nuevos'    => ['total' => $total],
        ]);

        return redirect()->route('checkout.pago', $venta->numero_orden)->withCookie($this->cookieVaciarCarrito());
    }

    /** Confirmación del pago. */
    public function pago(string $numeroOrden)
    {
        $venta = $this->ventaDelUsuario($numeroOrden);

        if ($venta->estado !== 'pendiente') {
            return redirect()->route('checkout.resultado', $venta->numero_orden);
        }

        $venta->load('pago.metodoPago');

        return view('checkout.pago', compact('venta'));
    }

    public function pagar(string $numeroOrden)
    {
        $venta = $this->ventaDelUsuario($numeroOrden);

        if ($venta->estado !== 'pendiente') {
            return redirect()->route('checkout.resultado', $venta->numero_orden);
        }

        // Integración con pasarela de pagos: alcance futuro. Cuando exista, aquí se
        // envía el cobro y la venta se confirma o se cancela según su respuesta.
        $this->estados->confirmarPago($venta, null, 'tienda');

        return redirect()->route('checkout.resultado', $venta->numero_orden);
    }

    public function resultado(string $numeroOrden)
    {
        $venta = $this->ventaDelUsuario($numeroOrden);
        $venta->load(['detalleProductos.producto', 'detalleCursos.curso', 'pago.metodoPago']);

        return view('checkout.resultado', compact('venta'));
    }

    // ─── Helpers ────────────────────────────────────────────

    private function ventaDelUsuario(string $numeroOrden): Venta
    {
        $venta = Venta::where('numero_orden', $numeroOrden)->firstOrFail();
        abort_if($venta->usuario_id !== auth()->id(), 403);

        return $venta;
    }

    /** Señal legible por JS (cookie sin cifrar, ver bootstrap/app.php) para vaciar el carrito local. */
    private function cookieVaciarCarrito(): \Symfony\Component\HttpFoundation\Cookie
    {
        return cookie('pm_vaciar_carrito', '1', 120, '/', null, false, false);
    }

    private function carritoActivo(): Carrito
    {
        return Carrito::firstOrCreate(
            ['usuario_id' => auth()->id(), 'estado' => 'activo'],
            ['usuario_id' => auth()->id(), 'estado' => 'activo']
        );
    }
}
