<?php

namespace Tests\Unit;

use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RajaOngkirServiceCacheTest extends TestCase
{
    public function test_search_destinations_caches_empty_array_and_hits_cache(): void
    {
        putenv('RAJA_ONGKIR_KEY=test');
        putenv('RAJA_ONGKIR_BASE_URL=https://example.test/api/v1/');
        putenv('RAJA_ONGKIR_CACHE_TTL_SECONDS=60');

        $_ENV['RAJA_ONGKIR_KEY'] = 'test';
        $_ENV['RAJA_ONGKIR_BASE_URL'] = 'https://example.test/api/v1/';
        $_ENV['RAJA_ONGKIR_CACHE_TTL_SECONDS'] = '60';

        Cache::flush();

        Http::fake(fn () => Http::response(['data' => []], 200));

        $service = app(RajaOngkirService::class);

        $first = $service->searchDomesticDestination('bandung', 20, 0);
        $second = $service->searchDomesticDestination('bandung', 20, 0);

        $this->assertSame([], $first);
        $this->assertSame([], $second);
        Http::assertSentCount(1);
    }
}
