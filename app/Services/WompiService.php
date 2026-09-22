<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Integración con Wompi Sandbox.
 * Doc oficial:  https://docs.wompi.co/docs/colombia/inicio-rapido/
 *
 * Estrategia elegida: Web Checkout redirect (GET a https://checkout.wompi.co/p/)
 * Es la integración más segura para nuestro backend: no manejamos tarjeta,
 * Wompi valida todo, y regresa al usuario con el estado + envía un webhook.
 *
 * Variables .env:
 *   WOMPI_ENV=sandbox        (o "production")
 *   WOMPI_PUBLIC_KEY=pub_test_xxx
 *   WOMPI_PRIVATE_KEY=prv_test_xxx      (no se usa en Web Checkout, dejar por si acaso)
 *   WOMPI_INTEGRITY_SECRET=test_integrity_xxx
 *   WOMPI_EVENTS_SECRET=test_events_xxx
 *   WOMPI_CURRENCY=COP
 */
class WompiService
{
    public function isSandbox(): bool
    {
        return env('WOMPI_ENV', 'sandbox') === 'sandbox';
    }

    public function checkoutBaseUrl(): string
    {
        // Wompi mantiene el mismo checkout para sandbox y prod;
        // el ambiente lo determina la public key (pub_test_ / pub_prod_).
        return 'https://checkout.wompi.co/p/';
    }

    public function apiBaseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.wompi.co/v1'
            : 'https://production.wompi.co/v1';
    }

    public function publicKey(): ?string
    {
        return env('WOMPI_PUBLIC_KEY');
    }

    public function currency(): string
    {
        return env('WOMPI_CURRENCY', 'COP');
    }

    /**
     * Genera la firma de integridad exigida por Web Checkout:
     *   sha256(reference + amountInCents + currency + integritySecret)
     * Doc: https://docs.wompi.co/docs/colombia/widget-checkout-web/#firma-de-integridad
     */
    public function signature(string $reference, int $amountInCents, ?string $currency = null): string
    {
        $secret   = env('WOMPI_INTEGRITY_SECRET', '');
        $currency = $currency ?: $this->currency();
        return hash('sha256', $reference . $amountInCents . $currency . $secret);
    }

    /**
     * URL para redirigir al usuario al Web Checkout.
     * Monta un formulario GET con todos los parámetros obligatorios.
     */
    public function checkoutUrl(array $params): string
    {
        // Parámetros mínimos según doc oficial.
        $requeridos = ['public-key', 'currency', 'amount-in-cents', 'reference', 'signature:integrity', 'redirect-url'];
        foreach ($requeridos as $k) {
            if (empty($params[$k])) throw new \InvalidArgumentException("Falta $k para Wompi checkout");
        }
        return $this->checkoutBaseUrl() . '?' . http_build_query($params);
    }

    /**
     * Verifica la firma del webhook.
     * Wompi envía el evento con un header `Checksum` (SHA256) construido con
     * los datos indicados + el events secret.
     * Doc: https://docs.wompi.co/docs/colombia/eventos/#verificar-el-evento
     */
    public function verificarEvento(array $payload, string $checksumRecibido): bool
    {
        $secret = env('WOMPI_EVENTS_SECRET', '');
        if (empty($secret)) return false;

        $signature   = $payload['signature']  ?? [];
        $properties  = $signature['properties'] ?? [];
        $timestamp   = $payload['timestamp']   ?? '';
        $data        = $payload['data']        ?? [];

        $concat = '';
        foreach ($properties as $prop) {
            $concat .= $this->extraer($data, $prop);
        }
        $concat .= $timestamp . $secret;

        $calc = hash('sha256', $concat);
        $ok   = hash_equals($calc, strtolower($checksumRecibido));

        if (!$ok) {
            Log::warning('Wompi: firma de webhook inválida', [
                'esperado' => substr($calc, 0, 12) . '…',
                'recibido' => substr($checksumRecibido, 0, 12) . '…',
            ]);
        }
        return $ok;
    }

    /**
     * Consulta el estado real de una transacción por su ID.
     * Se usa desde la página de resultado como fallback si el webhook no llegó todavía.
     * Sandbox: https://sandbox.wompi.co/v1/transactions/{id}
     */
    public function consultarTransaccion(string $transactionId): ?array
    {
        $url = $this->apiBaseUrl() . '/transactions/' . urlencode($transactionId);
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ]);
            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code !== 200 || !$body) return null;
            $json = json_decode($body, true);
            return $json['data'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Wompi consultarTransaccion: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Referencia legible + única para asociar orden ↔ transacción.
     * Formato: PM-ORD-YYYYMMDD-XXXXXX
     */
    public function nuevaReferencia(int $ventaId): string
    {
        return sprintf('PM-ORD-%s-%06d', now()->format('Ymd'), $ventaId);
    }

    /**
     * Convierte pesos (float) a centavos (int) sin problemas de coma flotante.
     */
    public function toCents(float|int $pesos): int
    {
        return (int) round($pesos * 100);
    }

    // ────────────────────────────────────────────────────────────
    // Helpers privados
    // ────────────────────────────────────────────────────────────

    private function extraer(array $data, string $ruta): string
    {
        // "transaction.id" → $data['transaction']['id']
        $ref = $data;
        foreach (explode('.', $ruta) as $k) {
            if (!is_array($ref) || !array_key_exists($k, $ref)) return '';
            $ref = $ref[$k];
        }
        return is_scalar($ref) ? (string) $ref : '';
    }
}
