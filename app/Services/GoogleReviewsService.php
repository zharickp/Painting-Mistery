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
                $resp = Http::timeout(8)->get('https://maps.googleapis.com/maps/api/place/details/json', [
                    'place_id' => config('services.google_places.place_id'),
                    'fields'   => 'rating,user_ratings_total,reviews',
                    'language' => 'es',
                    'reviews_sort' => 'newest',
                    'key'      => config('services.google_places.key'),
                ]);

                $r = $resp->json('result');
                if (! $resp->ok() || ! $r) {
                    Log::warning('Google Reviews: respuesta inválida', ['status' => $resp->json('status')]);
                    return null;
                }

                return [
                    'rating'  => (float) ($r['rating'] ?? 0),
                    'total'   => (int) ($r['user_ratings_total'] ?? 0),
                    'reviews' => collect($r['reviews'] ?? [])
                        ->filter(fn ($x) => filled($x['text'] ?? null))
                        ->map(fn ($x) => [
                            'nombre' => $x['author_name'] ?? 'Cliente',
                            'foto'   => $x['profile_photo_url'] ?? null,
                            'texto'  => $x['text'],
                            'stars'  => (int) ($x['rating'] ?? 5),
                            'cuando' => $x['relative_time_description'] ?? '',
                        ])->values()->all(),
                ];
            } catch (\Throwable $e) {
                Log::warning('Google Reviews: ' . $e->getMessage());
                return null;
            }
        });
    }
}
