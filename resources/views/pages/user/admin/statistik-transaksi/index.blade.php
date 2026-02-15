@extends('layouts.user.layout')
@section('title', 'Statistik Transaksi')
@section('content')

    <div class="flex justify-center mt-6">
        <div class="flex flex-col">
            <div>
                <form action="">
                    <select name="tahun" id="" class="border-2 border-black rounded-md px-4 py-2">
                        <option value="">Januari</option>
                        <option value="">Februari</option>
                        <option value="">Maret</option>
                        <option value="">April</option>
                        <option value="">Mei</option>
                        <option value="">Juni</option>
                        <option value="">Juli</option>
                        <option value="">Agustus</option>
                        <option value="">September</option>
                        <option value="">Oktober</option>
                        <option value="">November</option>
                        <option value="">Desember</option>
                    </select>
                </form>
            </div>

            <div>
                <div class="bg-black">
                    <svg width="48" height="43" viewBox="0 0 48 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M31.5417 0.737061L46.944 5.87108V18.7061H39.2428V39.2422C39.2428 39.923 38.9723 40.576 38.4909 41.0574C38.0095 41.5388 37.3566 41.8092 36.6758 41.8092H11.0053C10.3244 41.8092 9.6715 41.5388 9.19008 41.0574C8.70867 40.576 8.43821 39.923 8.43821 39.2422V18.7061H0.737061V5.87108L16.1394 0.737061C16.1394 2.7795 16.9507 4.73829 18.395 6.18251C19.8392 7.62673 21.798 8.43809 23.8405 8.43809C25.883 8.43809 27.8418 7.62673 29.286 6.18251C30.7303 4.73829 31.5417 2.7795 31.5417 0.737061Z"
                            stroke="white" stroke-width="1.47412" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </div>
            </div>
        </div>
    </div>

@endsection