<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;
    protected $authHeaders;

    public function __construct()
    {
        $this->apiKey = (string) env('RAJA_ONGKIR_KEY', '');
        $baseUrl = (string) env('RAJA_ONGKIR_BASE_URL', '');
        $this->baseUrl = rtrim($baseUrl, '/') . '/';

        // Some providers use `key` (RajaOngkir), others use `x-api-key` (Komerce/Komship).
        // Sending both keeps compatibility with either gateway.
        $this->authHeaders = $this->apiKey !== ''
            ? ['key' => $this->apiKey, 'x-api-key' => $this->apiKey]
            : [];
    }

    /**
     * Search domestic destination/origin by keyword.
     * Komerce v1 endpoint: destination/domestic-destination?search=...&limit=...&offset=...
     */
    public function searchDomesticDestination(string $search, int $limit = 20, int $offset = 0): array
    {
        $ttlSeconds = (int) env('RAJA_ONGKIR_CACHE_TTL_SECONDS', 86400);
        $cacheKey = 'rajaongkir:destinations:' . sha1(mb_strtolower(trim($search)) . "|{$limit}|{$offset}");

        if ($ttlSeconds > 0) {
            // Cache hit should also work for an empty array result (valid "no matches" response).
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (config('app.debug')) {
                    Log::debug('RajaOngkir destinations CACHE HIT', [
                        'cache_key' => $cacheKey,
                        'search' => $search,
                        'limit' => $limit,
                        'offset' => $offset,
                        'ttl_seconds' => $ttlSeconds,
                    ]);
                }
                return is_array($cached) ? $cached : [];
            }
            if (config('app.debug')) {
                Log::debug('RajaOngkir destinations CACHE MISS', [
                    'cache_key' => $cacheKey,
                    'search' => $search,
                    'limit' => $limit,
                    'offset' => $offset,
                    'ttl_seconds' => $ttlSeconds,
                ]);
            }
        }

        $response = Http::withHeaders($this->authHeaders)
            ->acceptJson()
            ->get($this->baseUrl . 'destination/domestic-destination', [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset,
            ]);

        if (!$response->successful()) {
            $errorPayload = [[
                '_error' => true,
                'status' => $response->status(),
                'message' => 'RajaOngkir request failed',
                'body' => $response->json() ?? $response->body(),
            ]];

            // Avoid hammering the upstream when rate-limited or temporarily failing.
            if ($ttlSeconds > 0 && in_array($response->status(), [429, 500, 502, 503, 504], true)) {
                Cache::put($cacheKey, $errorPayload, 60);
            }

            \Log::warning('RajaOngkir destinations request failed', [
                'status' => $response->status(),
                'url' => $this->baseUrl . 'destination/domestic-destination',
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset,
                'body' => $response->json() ?? $response->body(),
            ]);
            return $errorPayload;
        }

        // Komerce response commonly uses meta/data. Some legacy RajaOngkir wrappers use rajaongkir/results.
        $data = $response->json('data');
        if (is_array($data)) {
            if ($ttlSeconds > 0) Cache::put($cacheKey, $data, $ttlSeconds);
            return $data;
        }

        $results = $response->json('rajaongkir.results');
        if (is_array($results)) {
            if ($ttlSeconds > 0) Cache::put($cacheKey, $results, $ttlSeconds);
            return $results;
        }

        if ($ttlSeconds > 0) Cache::put($cacheKey, [], $ttlSeconds);
        return [];
    }

    /**
     * Calculate domestic shipping cost.
     * Komerce v1 endpoint: calculate/domestic-cost
     */
    public function calculateDomesticCost(int $origin, int $destination, int $weight, string $courier, string $price = 'lowest'): array
    {
        $ttlSeconds = (int) env('RAJA_ONGKIR_CACHE_TTL_SECONDS', 86400);
        $cacheKey = 'rajaongkir:cost:' . sha1("{$origin}|{$destination}|{$weight}|{$courier}|{$price}");

        if ($ttlSeconds > 0) {
            // Cache hit should also work for an empty array result (valid "no services" response).
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (config('app.debug')) {
                    Log::debug('RajaOngkir cost CACHE HIT', [
                        'cache_key' => $cacheKey,
                        'origin' => $origin,
                        'destination' => $destination,
                        'weight' => $weight,
                        'courier' => $courier,
                        'price' => $price,
                        'ttl_seconds' => $ttlSeconds,
                    ]);
                }
                return is_array($cached) ? $cached : [];
            }
            if (config('app.debug')) {
                Log::debug('RajaOngkir cost CACHE MISS', [
                    'cache_key' => $cacheKey,
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier,
                    'price' => $price,
                    'ttl_seconds' => $ttlSeconds,
                ]);
            }
        }

        $response = Http::withHeaders($this->authHeaders)
            ->acceptJson()
            ->asForm()
            ->post($this->baseUrl . 'calculate/domestic-cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'price' => $price,
            ]);

        if (!$response->successful()) {
            \Log::warning('RajaOngkir cost request failed', [
                'status' => $response->status(),
                'url' => $this->baseUrl . 'calculate/domestic-cost',
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
                'price' => $price,
                'body' => $response->json() ?? $response->body(),
            ]);
            return [[
                '_error' => true,
                'status' => $response->status(),
                'message' => 'RajaOngkir request failed',
                'body' => $response->json() ?? $response->body(),
            ]];
        }

        $data = $response->json('data');
        if (is_array($data)) {
            if ($ttlSeconds > 0) Cache::put($cacheKey, $data, $ttlSeconds);
            return $data;
        }

        $results = $response->json('rajaongkir.results');
        if (is_array($results)) {
            if ($ttlSeconds > 0) Cache::put($cacheKey, $results, $ttlSeconds);
            return $results;
        }

        if ($ttlSeconds > 0) Cache::put($cacheKey, [], $ttlSeconds);
        return [];
    }

    public function getCost($origin, $destination, $weight, $courier)
    {
        // Backward-compat wrapper for older callers.
        return $this->calculateDomesticCost((int) $origin, (int) $destination, (int) $weight, (string) $courier);
    }

    public function trackWaybill($waybill, $courier)
    {
        $response = Http::withHeaders($this->authHeaders)
            ->acceptJson()
            ->asForm()
            ->post($this->baseUrl . 'waybill', [
                'waybill' => $waybill,
                'courier' => $courier
            ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json('data');
        if (is_array($data)) return $data;

        $result = $response->json('rajaongkir.result');
        return is_array($result) ? $result : null;
    }
}
