@extends('layouts.user.layout')
@section('title', 'Statistik Transaksi')
@section('content')


    <div class="px-12">
        <div class="flex mt-6 gap-16 ">
            <div class="flex flex-col gap-5 w-full">
                <div class="relative">
                    <form action="{{ route('statistik.transaksi') }}" method="GET">
                        <div class="relative">
                            <select name="bulan" onchange="this.form.submit()"
                                class="w-full text-2xl border-2 appearance-none border-black rounded-md py-2 pl-4 pr-10 font-medium focus:outline-none cursor-pointer hover:bg-gray-50 bg-white">
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
                        [
                            'title' => 'Total Products',
                            'value' => $totalRegularProducts,
                            'bg' => 'bg-[#E08D65]',
                        ],
                        [
                            'title' => 'Custom Order',
                            'value' => $totalCustomOrders,
                            'bg' => 'bg-[#8B8BC3]',
                        ],
                        [
                            'title' => 'Product Sold',
                            'value' => $totalProductSold,
                            'bg' => 'bg-[#D66D7F]',
                        ],
                        [
                            'title' => 'Total Orders',
                            'value' => $totalOrders,
                            'bg' => 'bg-[#63B39B]',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 w-full  gap-6 ">
                    @foreach ($stats as $stat)
                        <div
                            class="{{ $stat['bg'] }} rounded-xl p-5 text-white shadow-md flex items-center gap-4 transition hover:scale-105">

                            <div class="p-2 rounded-lg">
                                <svg width="48" height="43" viewBox="0 0 48 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M31.5417 0.737061L46.944 5.87108V18.7061H39.2428V39.2422C39.2428 39.923 38.9723 40.576 38.4909 41.0574C38.0095 41.5388 37.3566 41.8092 36.6758 41.8092H11.0053C10.3244 41.8092 9.6715 41.5388 9.19008 41.0574C8.70867 40.576 8.43821 39.923 8.43821 39.2422V18.7061H0.737061V5.87108L16.1394 0.737061C16.1394 2.7795 16.9507 4.73829 18.395 6.18251C19.8392 7.62673 21.798 8.43809 23.8405 8.43809C25.883 8.43809 27.8418 7.62673 29.286 6.18251C30.7303 4.73829 31.5417 2.7795 31.5417 0.737061Z"
                                        stroke="white" stroke-width="1.47412" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>

                            <div>
                                <h3 class="font-bold text-sm uppercase tracking-wider opacity-90">
                                    {{ $stat['title'] }}
                                </h3>
                                <p class="text-2xl font-bold">
                                    {{ $stat['value'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col w-full">

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 w-full">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Penjualan Produk</h2>

                        <div class="relative">
                            <form action="{{ route('statistik.transaksi') }}" method="GET">
                                <div class="relative group">
                                    <select name="bulan" onchange="this.form.submit()"
                                        class="appearance-none bg-transparent pl-3 pr-8 py-1 text-sm font-bold text-gray-700 hover:text-black focus:outline-none cursor-pointer">
                                        <option value="">All Months</option>
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div
                                        class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-700 group-hover:text-black">
                                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 1L5 5L9 1" />
                                        </svg>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="relative h-[28rem] w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="flex items-end font-inter justify-end mt-7">
                    <h1 class="text-3xl">
                        Total Revenue : {{ 'Rp ' . number_format($totalRevenue, 0, ',', '.') }}
                    </h1>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <h1 class="text-4xl">
                Data Transaksi
            </h1>

            <div>
                <table>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($allTransactions as $transaction)
                            @php
                                $hasRegular = $transaction->produkTransaksis->count() > 0;
                                $hasCustom = $transaction->orderKustoms->count() > 0;

                                $jenisProduk = '-';
                                if ($hasRegular && $hasCustom) {
                                    $jenisProduk = 'Mix (Katalog + Kustom)';
                                } elseif ($hasCustom) {
                                    $jenisProduk = 'Kustom';
                                } elseif ($hasRegular) {
                                    $jenisProduk = 'Katalog';
                                }
                            @endphp

                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    #TRX{{ str_pad($transaction->transaksi_id, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->nama_customer }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $jenisProduk }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->status  }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->pengiriman->status_pengiriman ?? 'Belum Dikirim' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');

            // 1. Data Setup
            const labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

            // This is your actual data (e.g., from Laravel)
            const salesData = @json($salesData);
            const maxValue = Math.max(...salesData) > 0 ? Math.max(...salesData) + 10 : 100; // fallback to 100 if empty
            const backgroundData = salesData.map(() => maxValue);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Penjualan',
                            data: salesData,
                            backgroundColor: '#EBCD5E', // The Gold/Yellow color
                            hoverBackgroundColor: '#DKB940',
                            barThickness: 12,
                            borderRadius: 20, // Makes it fully rounded
                            borderSkipped: false, // Ensures bottom is also rounded
                            order: 1
                        },
                        {
                            // THE BACKGROUND TRACK (Light Blue)
                            label: 'Target',
                            data: backgroundData,
                            backgroundColor: '#F2F7FF', // Very light blue/gray
                            hoverBackgroundColor: '#F2F7FF',
                            barThickness: 12, // Must match the dataset above
                            borderRadius: 20,
                            borderSkipped: false,
                            // Important: Draw this BEHIND
                            order: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },

                    plugins: {
                        legend: {
                            display: false // Hide the legend box at the top
                        },
                        tooltip: {
                            filter: function (tooltipItem) {
                                return tooltipItem.datasetIndex === 0;
                            },
                            backgroundColor: '#323232',
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            stacked: true, // Helps with alignment
                            grid: {
                                display: false, // Hide X grid lines
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF', // Gray text
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: maxValue, // Ensure Y axis fits the background bar
                            grid: {
                                color: '#F3F4F6', // Very light gray grid lines
                                borderDash: [5, 5], // Dashed lines
                                drawBorder: false
                            },
                            ticks: {
                                color: '#9CA3AF',
                                stepSize: 100
                            },
                            border: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection