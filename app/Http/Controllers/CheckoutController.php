<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\DetalleVentaProducto;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Muestra la pantalla de checkout simulado (resumen del carrito + método).
     */
    public function mostrar()
    {
        $carrito = Carrito::firstOrCreate(
            ['usuario_id' => auth()->id(), 'estado' => 'activo'],
            ['usuario_id' => auth()->id(), 'estado' => 'activo']
        );

        $detalles = $carrito->detalles()->with('producto')->get();
        $total    = $detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        $metodos = MetodoPago::where('estado', true)->orderBy('nombre')->get();

        return view('checkout.index', compact('carrito', 'detalles', 'total', 'metodos'));
    }

    /**
     * Procesa un pago SIMULADO. No consulta ninguna pasarela real.
     */
    public function procesar(Request $request)
    {
        $request->validate([
            'metodo_pago_id' => 'required|exists:metodo_pago,id',
            'resultado'      => 'nullable|in:aprobado,rechazado',
        ]);

        $carrito = Carrito::where('usuario_id', auth()->id())
            ->where('estado', 'activo')
            ->first();

        if (!$carrito) {
            return redirect()->route('carrito.index')->with('error', 'No hay carrito activo.');
        }

        $detalles = $carrito->detalles()->with('producto')->get();

        if ($detalles->isEmpty()) {
            return redirect()->route('carrito.index')->with('error', 'Tu carrito está vacío.');
        }

        // Modo demo: el usuario puede forzar el resultado; si no, alta probabilidad de aprobado.
        $resultado = $request->resultado ?? (random_int(1, 10) <= 8 ? 'aprobado' : 'rechazado');

        if ($resultado === 'rechazado') {
            return view('checkout.resultado', [
                'estado'   => 'rechazado',
                'mensaje'  => 'Pago simulado rechazado. En un sistema real, se mostraría el motivo devuelto por la pasarela.',
                'venta'    => null,
            ]);
        }

        try {
            $venta = DB::transaction(function () use ($carrito, $detalles, $request) {
                $total = $detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

                $venta = Venta::create([
                    'usuario_id' => auth()->id(),
                    'total'      => $total,
                    'estado'     => 'pagada',
                    'fecha'      => now(),
                ]);

                foreach ($detalles as $d) {
                    DetalleVentaProducto::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $d->producto_id,
                        'cantidad'        => $d->cantidad,
                        'precio_unitario' => $d->precio_unitario,
                        'subtotal'        => $d->cantidad * $d->precio_unitario,
                        'iva'             => 0,
                    ]);
                }

                Pago::create([
                    'venta_id'           => $venta->id,
                    'metodo_pago_id'     => $request->metodo_pago_id,
                    'numero_comprobante' => 'DEMO-' . strtoupper(bin2hex(random_bytes(4))),
                    'valor'              => $total,
                    'fecha_pago'         => now(),
                    'estado'             => 'aprobado',
                ]);

                $carrito->update(['estado' => 'finalizado']);

                return $venta;
            });
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('carrito.index')
                ->with('error', 'No se pudo procesar la simulación de pago.');
        }

        return view('checkout.resultado', [
            'estado'  => 'aprobado',
            'mensaje' => 'Pago simulado exitosamente. No se realizó ningún cobro real.',
            'venta'   => $venta,
        ]);
    }
}
