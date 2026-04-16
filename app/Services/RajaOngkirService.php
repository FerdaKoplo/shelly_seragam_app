<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('RAJA_ONGKIR_KEY');
        $this->baseUrl = env('RAJA_ONGKIR_BASE_URL');
    }

    public function getCost($origin, $destination, $weight, $courier)
    {
        $response = Http::withHeaders(['key' => $this->apiKey])
            ->post($this->baseUrl . 'cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]);

        return $response->json()['rajaongkir']['results'] ?? [];
    }

    public function trackWaybill($waybill, $courier)
    {
        $response = Http::withHeaders(['key' => $this->apiKey])
            ->post($this->baseUrl . 'waybill', [
                'waybill' => $waybill,
                'courier' => $courier
            ]);

        return $response->json()['rajaongkir']['result'] ?? null;
    }
}