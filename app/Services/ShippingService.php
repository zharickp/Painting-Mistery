<?php

namespace App\Services;

use App\Models\TarifaEnvio;
use Illuminate\Support\Facades\Cache;

/**
 * Cálculo centralizado del envío. El backend es la ÚNICA fuente de verdad:
 * el frontend puede mostrar una estimación pero el checkout siempre
 * consulta este servicio antes de crear la orden.
 */
class ShippingService
{
    /**
     * Calcula el envío a partir del valor del carrito (no de la ciudad):
     * escalas de $20.000 a $30.000 según lo que se vaya a llevar, y
     * envío GRATIS al superar el umbral configurado (config/envios.php).
     * Este es el método que usa el checkout actualmente.
     */
    public function calcularPorSubtotal(float $subtotal): array
    {
        $umbral  = (float) config('envios.umbral_envio_gratis');
        $escalas = config('envios.escalas', []);

        if ($subtotal >= $umbral) {
            return [
                'valor'     => 0.0,
                'es_gratis' => true,
                'umbral'    => $umbral,
                'faltante'  => 0.0,
                'progreso'  => 100,
            ];
        }

        $valor = end($escalas)['valor'] ?? 0;
        foreach ($escalas as $escala) {
            if ($subtotal < $escala['hasta']) {
                $valor = $escala['valor'];
                break;
            }
        }

        return [
            'valor'     => (float) $valor,
            'es_gratis' => false,
            'umbral'    => $umbral,
            'faltante'  => round($umbral - $subtotal, 0),
            'progreso'  => $umbral > 0 ? (int) min(100, round(($subtotal / $umbral) * 100)) : 0,
        ];
    }

    /**
     * MÉTODO ANTERIOR (por ciudad/departamento) — se mantiene por si se
     * necesita en el futuro y para no romper el admin de Tarifas de Envío,
     * pero el checkout ya NO lo usa: ahora el costo depende del valor del
     * carrito (ver calcularPorSubtotal), no del destino.
     *
     * Devuelve el costo de envío para un destino y un subtotal.
     * Estrategia:
     *  1) Busca una tarifa activa cuya ciudad coincida (case-insensitive).
     *  2) Si no hay ciudad específica, busca una tarifa "comodín" del departamento
     *     (ciudad IS NULL) activa.
     *  3) Si tampoco hay, usa la tarifa "Otro" (departamento IS NULL, ciudad IS NULL).
     *  4) Si NO hay ninguna tarifa configurada, devuelve null y el checkout
     *     debe pedirle al usuario que contacte a soporte (no inventamos precios).
     * Si la tarifa tiene umbral_envio_gratis y el subtotal lo supera, retorna 0.
     */
    public function calcular(?string $departamento, ?string $ciudad, float $subtotal): ?array
    {
        $departamento = $departamento ? trim($departamento) : null;
        $ciudad       = $ciudad ? trim($ciudad) : null;

        $tarifa = $this->buscarTarifa($departamento, $ciudad);

        if (!$tarifa) return null;

        $precio = (float) $tarifa->precio_base;
        $gratis = false;

        if ($tarifa->umbral_envio_gratis !== null && $subtotal >= (float) $tarifa->umbral_envio_gratis) {
            $precio = 0.0;
            $gratis = true;
        }

        return [
            'tarifa_id'  => $tarifa->id,
            'valor'      => round($precio, 2),
            'etiqueta'   => $this->etiqueta($tarifa),
            'es_gratis'  => $gratis,
            'umbral'     => $tarifa->umbral_envio_gratis !== null ? (float) $tarifa->umbral_envio_gratis : null,
        ];
    }

    /**
     * Ciudades disponibles agrupadas por departamento. Útil para poblar
     * los selects del checkout. Cacheado 10 minutos.
     */
    public function ciudadesPorDepartamento(): array
    {
        return Cache::remember('shipping.ciudades', 600, function () {
            $rows = TarifaEnvio::where('activo', true)
                ->orderBy('departamento')->orderBy('ciudad')
                ->get(['departamento', 'ciudad']);

            $out = [];
            foreach ($rows as $r) {
                $dep = $r->departamento ?: 'Otros';
                $out[$dep] = $out[$dep] ?? [];
                if ($r->ciudad) $out[$dep][] = $r->ciudad;
            }
            foreach ($out as $k => $v) $out[$k] = array_values(array_unique($v));

            return $out;
        });
    }

    /**
     * Invalida el cache. Llamar desde el observer de TarifaEnvio si hace falta.
     */
    public function invalidarCache(): void
    {
        Cache::forget('shipping.ciudades');
    }

    private function buscarTarifa(?string $departamento, ?string $ciudad): ?TarifaEnvio
    {
        // 1) Coincidencia exacta ciudad + departamento
        if ($ciudad && $departamento) {
            $t = TarifaEnvio::where('activo', true)
                ->whereRaw('LOWER(departamento) = ?', [mb_strtolower($departamento)])
                ->whereRaw('LOWER(ciudad) = ?', [mb_strtolower($ciudad)])
                ->first();
            if ($t) return $t;
        }

        // 2) Comodín del departamento (ciudad NULL)
        if ($departamento) {
            $t = TarifaEnvio::where('activo', true)
                ->whereRaw('LOWER(departamento) = ?', [mb_strtolower($departamento)])
                ->whereNull('ciudad')
                ->first();
            if ($t) return $t;
        }

        // 3) Comodín global (departamento NULL, ciudad NULL) — "Otro destino"
        return TarifaEnvio::where('activo', true)
            ->whereNull('departamento')
            ->whereNull('ciudad')
            ->first();
    }

    private function etiqueta(TarifaEnvio $t): string
    {
        if ($t->ciudad && $t->departamento) return $t->ciudad . ', ' . $t->departamento;
        if ($t->departamento) return $t->departamento;
        return 'Otro destino';
    }
}
