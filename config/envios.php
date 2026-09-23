<?php

/**
 * Tarifas nacionales de envío, escalonadas por el valor del carrito
 * (no por ciudad/departamento — el costo de transporte de estos
 * productos varía más por peso/volumen que por destino).
 *
 * Lógica (ver ShippingService::calcularPorSubtotal):
 *   subtotal < escalas[0].hasta        -> escalas[0].valor
 *   subtotal < escalas[1].hasta        -> escalas[1].valor
 *   ...
 *   subtotal >= umbral_envio_gratis    -> envío GRATIS
 *
 * Son valores iniciales de referencia (el mínimo real que cobran la
 * mayoría de transportadoras nacionales). Ajústalos aquí o vía .env
 * sin tocar el código.
 */
return [

    'umbral_envio_gratis' => (float) env('ENVIO_UMBRAL_GRATIS', 400000),

    'escalas' => [
        ['hasta' => 150000, 'valor' => 20000],
        ['hasta' => 300000, 'valor' => 25000],
        ['hasta' => 400000, 'valor' => 30000],
    ],

];
