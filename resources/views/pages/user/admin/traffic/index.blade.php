@extends('layouts.user.layout')
@section('title', 'Traffic Website')
@section('content')

@php
    $embedUrl = (string) config('services.google_analytics.traffic_embed_url', env('GA_TRAFFIC_EMBED_URL', ''));
@endphp

<div class="space-y-6" data-cy="traffic-page">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold" data-cy="traffic-title">Statistik Pengunjung</h1>
        @if($embedUrl)
            <a href="{{ $embedUrl }}" target="_blank" rel="noopener"    data-cy="open-traffic-new-tab"
                class="text-sm font-medium text-gray-600 hover:text-black underline underline-offset-4">
                Buka di tab baru
            </a>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm"    data-cy="traffic-container">
        @if(!$embedUrl)
            <div class="p-6" data-cy="traffic-not-configured">
                <p class="font-bold mb-2" data-cy="traffic-config-warning">Embed Google Analytics belum dikonfigurasi.</p>
                <p class="text-sm text-gray-700" data-cy="traffic-config-description">
                    Tambahkan URL embed (Looker Studio / Google Analytics) ke env <code>GA_TRAFFIC_EMBED_URL</code>,
                    lalu reload halaman ini.
                </p>
            </div>
        @else
            <iframe
                title="Traffic Website"
                src="{{ $embedUrl }}"
                data-cy="traffic-iframe"
                class="w-full"
                style="height: calc(100vh - 260px);"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen>
            </iframe>
        @endif
    </div>
</div>

@endsection