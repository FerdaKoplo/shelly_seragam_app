@extends('layouts.user.layout')
@section('title', 'Statistik Transaksi')
@section('content')
    <div class="px-4 md:px-12 pb-20">
        <div class="flex flex-col lg:flex-row mt-6 gap-8 lg:gap-16">

            <div class="flex flex-col gap-5 w-full lg:w-1/3 shrink-0">
                <div class="relative">
                    <form action="{{ route('statistik.transaksi') }}" method="GET">
                        <div class="relative">
                            <select data-cy="month-filter-main" name="bulan" onchange="this.form.submit()"
                                class="w-full text-xl md:text-2xl border-2 appearance-none border-black rounded-md py-2 pl-4 pr-10 font-medium focus:outline-none cursor-pointer hover:bg-gray-50 bg-white">
                                <option value="">Semua Bulan</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none flex items-center">
                                <svg width="16" height="14" viewBox="0 0 16 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9.36413 12.75C8.59433 14.0833 6.66983 14.0833 5.90003 12.75L0.270868 3C-0.498933 1.66667 0.463317 -1.54465e-06 2.00292 -1.41006e-06L13.2612 -4.25822e-07C14.8008 -2.91226e-07 15.7631 1.66667 14.9933 3L9.36413 12.75Z"
                                        fill="#1A1919" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>

                @php
                    $stats = [
                        ['title' => 'Total Products', 'value' => $totalRegularProducts, 'bg' => 'bg-[#E08D65]'],
                        ['title' => 'Custom Order', 'value' => $totalCustomOrders, 'bg' => 'bg-[#8B8BC3]'],
                        ['title' => 'Product Sold', 'value' => $totalProductSold, 'bg' => 'bg-[#D66D7F]'],
                        ['title' => 'Total Orders', 'value' => $totalOrders, 'bg' => 'bg-[#63B39B]'],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 w-full gap-4 md:gap-6">
                    @foreach ($stats as $stat)
                        <div data-cy="stat-card-{{ Str::slug($stat['title']) }}"
                            class="{{ $stat['bg'] }} rounded-xl p-4 md:p-5 text-white shadow-md flex items-center gap-4 transition hover:scale-105">
                            <div class="p-1 md:p-2 rounded-lg shrink-0">
                                <svg class="w-8 h-8 md:w-12 md:h-11" viewBox="0 0 48 43" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M31.5417 0.737061L46.944 5.87108V18.7061H39.2428V39.2422C39.2428 39.923 38.9723 40.576 38.4909 41.0574C38.0095 41.5388 37.3566 41.8092 36.6758 41.8092H11.0053C10.3244 41.8092 9.6715 41.5388 9.19008 41.0574C8.70867 40.576 8.43821 39.923 8.43821 39.2422V18.7061H0.737061V5.87108L16.1394 0.737061C16.1394 2.7795 16.9507 4.73829 18.395 6.18251C19.8392 7.62673 21.798 8.43809 23.8405 8.43809C25.883 8.43809 27.8418 7.62673 29.286 6.18251C30.7303 4.73829 31.5417 2.7795 31.5417 0.737061Z"
                                        stroke="white" stroke-width="1.47412" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-[10px] md:text-xs uppercase tracking-wider opacity-90 truncate">
                                    {{ $stat['title'] }}
                                </h3>
                                <p class="text-xl md:text-2xl font-bold">
                                    {{ $stat['value'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col w-full">
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 w-full">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <h2 class="text-lg md:text-xl font-bold text-gray-800">Penjualan Produk</h2>
                        <div class="relative">
                            <form action="{{ route('statistik.transaksi') }}" method="GET">
                                <div class="relative group border border-gray-200 rounded-md px-2">
                                    <select data-cy="month-filter-chart" name="bulan" onchange="this.form.submit()"
                                        class="appearance-none bg-transparent pl-3 pr-8 py-1 text-sm font-bold text-gray-700 hover:text-black focus:outline-none cursor-pointer">
                                        <option value="">All Months</option>
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-gray-700 group-hover:text-black">
                                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 1L5 5L9 1" />
                                        </svg>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="relative h-64 md:h-[28rem] w-full">
                        <canvas id="salesChart" data-cy="sales-chart"></canvas>
                    </div>
                </div>

                <div class="flex items-center justify-center lg:justify-end mt-7">
                    <h1 data-cy="total-revenue" class="text-xl md:text-3xl font-bold text-center lg:text-right">
                        Total Revenue : <span
                            class="block sm:inline">{{ 'Rp ' . number_format($totalRevenue, 0, ',', '.') }}</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                <h1 class="text-3xl md:text-4xl font-bold">Data Transaksi</h1>
                <a data-cy="export-spreadsheet-btn"
                    href="{{ route('statistik.transaksi.export', ['bulan' => request('bulan')]) }}"
                    class="bg-[#27AE60] text-center text-white px-6 md:px-12 py-3 rounded-md hover:bg-green-700 transition font-bold text-sm md:text-base">
                    Unduh Spreadsheet
                </a>
            </div>

            <div class="overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
                <table data-cy="transactions-table" class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-700 text-sm tracking-wide">
                            <th class="px-6 py-4 font-bold">ID</th>
                            <th class="px-6 py-4 font-bold">Nama Customer</th>
                            <th class="px-6 py-4 font-bold">Jenis Produk</th>
                            <th class="px-6 py-4 font-bold">Status Transaksi</th>
                            <th class="px-6 py-4 font-bold">Status Pengiriman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($allTransactions as $transaction)
                            @php
                                $hasRegular = $transaction->produkTransaksis->count() > 0;
                                $hasCustom = $transaction->orderKustoms->count() > 0;
                                $jenisProduk = $hasRegular && $hasCustom ? 'Mix' : ($hasCustom ? 'Kustom' : ($hasRegular ? 'Katalog' : '-'));
                            @endphp
                            <tr data-cy="transaction-row" class="hover:bg-gray-50 transition">
                                <td data-cy="transaction-id" class="px-6 py-4 text-sm font-medium text-gray-900">
                                    #TRX{{ str_pad($transaction->transaksi_id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td data-cy="customer-name" class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->nama_customer }}</td>
                                <td data-cy="product-type" class="px-6 py-4 text-sm text-gray-600">{{ $jenisProduk }}</td>
                                <td data-cy="transaction-status" class="px-6 py-4 text-sm text-gray-600">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-bold {{ $transaction->status == 'Paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td data-cy="shipping-status" class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->pengiriman->status_pengiriman ?? 'Belum Dikirim' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td data-cy="empty-transactions" colspan="5"
                                    class="px-6 py-10 text-center text-gray-500 font-medium">Tidak ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6" data-cy="transactions-pagination">
                {{ $allTransactions->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesData = @json($salesData);
            const maxValue = Math.max(...salesData) > 0 ? Math.max(...salesData) + 10 : 100;
            const backgroundData = salesData.map(() => maxValue);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
                    datasets: [{
                        label: 'Penjualan',
                        data: salesData,
                        backgroundColor: '#EBCD5E',
                        barThickness: window.innerWidth < 768 ? 6 : 12,
                        borderRadius: 20,
                        order: 1
                    },
                    {
                        label: 'Target',
                        data: backgroundData,
                        backgroundColor: '#F2F7FF',
                        barThickness: window.innerWidth < 768 ? 6 : 12,
                        borderRadius: 20,
                        order: 2
                    }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: { beginAtZero: true, max: maxValue, grid: { borderDash: [5, 5] } }
                    }
                }
            });
        });
    </script>
@endsection