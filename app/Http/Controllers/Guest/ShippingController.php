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
            'search' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        // Simple per-session throttling to avoid upstream 429.
        $search = (string) $request->input('search');
        $throttleKey = 'shipping:destinations:' . sha1($request->session()->getId() . '|' . mb_strtolower(trim($search)));
        if (!cache()->add($throttleKey, 1, 1)) {
            return response()->json([
                'data' => [[
                    '_error' => true,
                    'status' => 429,
                    'message' => 'Too many requests',
                    'body' => ['message' => 'Throttled (client)'],
                ]],
                'ok' => false,
            ], 429);
        }

        $results = $rajaOngkir->searchDomesticDestination($search);

        $hasError = isset($results[0]) && is_array($results[0]) && (($results[0]['_error'] ?? false) === true);

        return response()->json([
            'data' => $results,
            'ok' => !$hasError,
        ], $hasError ? 502 : 200);
    }

    public function cost(Request $request, RajaOngkirService $rajaOngkir)
    {
        $request->validate([
            'destination' => ['required', 'integer', 'min:1'],
            // RajaOngkir/Komerce domestic-cost endpoint expects weight in grams (integer).
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string', 'max:200'],
        ]);

        // Simple per-session throttling to avoid upstream 429.
        $throttleKey = 'shipping:cost:' . sha1($request->session()->getId() . '|' . $request->integer('destination') . '|' . $request->integer('weight') . '|' . (string) $request->input('courier'));
        if (!cache()->add($throttleKey, 1, 2)) {
            return response()->json([
                'data' => [[
                    '_error' => true,
                    'status' => 429,
                    'message' => 'Too many requests',
                    'body' => ['message' => 'Throttled (client)'],
                ]],
                'ok' => false,
            ], 429);
        }

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
            'ok' => !(isset($results[0]) && is_array($results[0]) && (($results[0]['_error'] ?? false) === true)),
        ], (isset($results[0]) && is_array($results[0]) && (($results[0]['_error'] ?? false) === true)) ? 502 : 200);
    }
}
