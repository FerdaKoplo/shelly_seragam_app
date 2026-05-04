<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function destinations(Request $request, RajaOngkirService $rajaOngkir)
    {
        $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $results = $rajaOngkir->searchDomesticDestination((string) $request->input('search'));

        return response()->json([
            'data' => $results,
        ]);
    }

    public function cost(Request $request, RajaOngkirService $rajaOngkir)
    {
        $request->validate([
            'destination' => ['required', 'integer', 'min:1'],
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string', 'max:200'],
        ]);

        $origin = (int) env('RAJA_ONGKIR_ORIGIN_ID', 444);
        $results = $rajaOngkir->calculateDomesticCost(
            $origin,
            (int) $request->integer('destination'),
            (int) $request->integer('weight'),
            (string) $request->input('courier'),
            'lowest'
        );

        return response()->json([
            'data' => $results,
        ]);
    }
}

