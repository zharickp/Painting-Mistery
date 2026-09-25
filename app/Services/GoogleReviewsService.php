<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleReviewsService
{
    public function configurado(): bool
    {
        return filled(config('services.google_places.key')) && filled(config('services.google_places.place_id'));
    }

    /** @return array{rating: float, total: int, reviews: array<int, array>}|null */
    public function obtener(): ?array
    {
        if (! $this->configurado()) {
            return null;
        }

        return Cache::remember('google_reviews', now()->addHours(3), function () {
            try {
                $resp = Http::timeout(8)
                    ->withHeaders([
                        'X-Goog-Api-Key'   => config('services.google_places.key'),
                        'X-Goog-FieldMask' => 'rating,userRatingCount,reviews',
                    ])
                    ->get('https://places.googleapis.com/v1/places/' . config('services.google_places.place_id'), [
                        'languageCode' => 'es',
                    ]);

                $r = $resp->json();
                if (! $resp->ok() || ! is_array($r)) {
                    Log::warning('Google Reviews: respuesta inválida', ['error' => $resp->json('error.message')]);
                    return null;
                }

                return [
                    'rating'  => (float) ($r['rating'] ?? 0),
                    'total'   => (int) ($r['userRatingCount'] ?? 0),
                    'reviews' => collect($r['reviews'] ?? [])
                        ->filter(fn ($x) => filled($x['text']['text'] ?? null))
                        ->map(fn ($x) => [
                            'nombre' => $x['authorAttribution']['displayName'] ?? 'Cliente',
                            'foto'   => $x['authorAttribution']['photoUri'] ?? null,
                            'texto'  => $x['text']['text'],
                            'stars'  => (int) ($x['rating'] ?? 5),
                            'cuando' => $x['relativePublishTimeDescription'] ?? '',
                        ])->values()->all(),
                ];
            } catch (\Throwable $e) {
                Log::warning('Google Reviews: ' . $e->getMessage());
                return null;
            }
        });
    }
}
