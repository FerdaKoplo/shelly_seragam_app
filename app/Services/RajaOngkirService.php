<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) env('RAJA_ONGKIR_KEY', '');
        $baseUrl = (string) env('RAJA_ONGKIR_BASE_URL', '');
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
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
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && $cached !== []) {
                return $cached;
            }
        }

        $response = Http::withHeaders(['key' => $this->apiKey])
            ->acceptJson()
            ->get($this->baseUrl . 'destination/domestic-destination', [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset,
            ]);

        if (!$response->successful()) {
            return [[
                '_error' => true,
                'status' => $response->status(),
                'message' => 'RajaOngkir request failed',
                'body' => $response->json() ?? $response->body(),
            ]];
        }

        // Komerce response commonly uses meta/data. Some legacy RajaOngkir wrappers use rajaongkir/results.
        $data = $response->json('data');
        if (is_array($data)) {
            if ($ttlSeconds > 0 && $data !== []) {
                Cache::put($cacheKey, $data, $ttlSeconds);
            }
            return $data;
        }

        $results = $response->json('rajaongkir.results');
        if (is_array($results)) {
            if ($ttlSeconds > 0 && $results !== []) {
                Cache::put($cacheKey, $results, $ttlSeconds);
            }
            return $results;
        }

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
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && $cached !== []) {
                return $cached;
            }
        }

        $response = Http::withHeaders(['key' => $this->apiKey])
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
            return [[
                '_error' => true,
                'status' => $response->status(),
                'message' => 'RajaOngkir request failed',
                'body' => $response->json() ?? $response->body(),
            ]];
        }

        $data = $response->json('data');
        if (is_array($data)) {
            if ($ttlSeconds > 0 && $data !== []) {
                Cache::put($cacheKey, $data, $ttlSeconds);
            }
            return $data;
        }

        $results = $response->json('rajaongkir.results');
        if (is_array($results)) {
            if ($ttlSeconds > 0 && $results !== []) {
                Cache::put($cacheKey, $results, $ttlSeconds);
            }
            return $results;
        }

        return [];
    }

    public function getCost($origin, $destination, $weight, $courier)
    {
        // Backward-compat wrapper for older callers.
        return $this->calculateDomesticCost((int) $origin, (int) $destination, (int) $weight, (string) $courier);
    }

    public function trackWaybill($waybill, $courier)
    {
        $response = Http::withHeaders(['key' => $this->apiKey])
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
