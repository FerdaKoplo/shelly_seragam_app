<?php

namespace App\Services;

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
        $response = Http::withHeaders(['key' => $this->apiKey])
            ->acceptJson()
            ->get($this->baseUrl . 'destination/domestic-destination', [
                'search' => $search,
                'limit' => $limit,
                'offset' => $offset,
            ]);

        if (!$response->successful()) {
            return [];
        }

        // Komerce response commonly uses meta/data. Some legacy RajaOngkir wrappers use rajaongkir/results.
        $data = $response->json('data');
        if (is_array($data)) return $data;

        $results = $response->json('rajaongkir.results');
        return is_array($results) ? $results : [];
    }

    /**
     * Calculate domestic shipping cost.
     * Komerce v1 endpoint: calculate/domestic-cost
     */
    public function calculateDomesticCost(int $origin, int $destination, int $weight, string $courier, string $price = 'lowest'): array
    {
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
            return [];
        }

        $data = $response->json('data');
        if (is_array($data)) return $data;

        $results = $response->json('rajaongkir.results');
        return is_array($results) ? $results : [];
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
