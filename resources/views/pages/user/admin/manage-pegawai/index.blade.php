@extends('layouts.user.layout')
@section('title', 'Manage Pegawai')
@section('content')

<div class="flex-col bg-gray-50 min-h-screen" x-data="{ showForm: false, pegawaiIsActive: true }">

    {{-- TABLE HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <form action="{{ route('manage.pegawai') }}" method="GET" class="flex gap-4">
            <input type="text" name="search" placeholder="Cari Nama/Username..."
                class="border border-gray-300 rounded px-4 py-2 w-64 focus:outline-none focus:ring-1 focus:ring-gray-400">
            <select name="status" class="border border-gray-300 rounded px-4 py-2 bg-white">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="non-aktif">Non-Aktif</option>
            </select>
            <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">Filter</button>
        </form>

    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-sm overflow-hidden mb-10">
        <table class="w-full text-left border-collapse">
            <thead class="text-gray-600 font-normal">
                <tr>
                    <th class="py-4 px-6 font-medium">Nama</th>
                    <th class="py-4 px-6 font-medium">Username</th>
                    <th class="py-4 px-6 font-medium">Status</th>
                    <th class="py-4 px-6 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                {{--
                @foreach($pegawai as $p) --}}
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-6"> $p->name </td>
                    <td class="py-4 px-6 text-gray-500">$p->username</td>
                    <td class="py-4 px-6">$p->status</td>
                    <td class="py-4 px-6 flex justify-center gap-2">
                        <button class="bg-[#4A90E2] text-white px-4 py-1 rounded text-sm hover:bg-blue-600">Edit</button>
                        <button class="bg-[#C04D41] text-white px-4 py-1 rounded text-sm hover:bg-red-700">Hapus</button>
                    </td>
                </tr>
                {{-- @endforeach
                --}}
            </tbody>
        </table>
    </div>

    {{-- BTN ADD --}}
    <div class="flex justify-end items-center mb-6">
        <button @click="showForm = !showForm" class="bg-[#333333] text-white px-6 py-2 rounded shadow-sm hover:bg-black transition">
            Tambah Pegawai
        </button>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{-- {{ $pegawai->links() }} --}}
    </div>

    {{-- FORM --}}
    <div x-show="showForm" x-transition
        class="mt-10 max-w-2xl border border-gray-300 rounded-lg p-8 bg-[#F9FAFB] shadow-sm">
        <h2 class="text-xl font-sans mb-6 text-gray-700">Form Pegawai</h2>

        <!-- TODO -->
        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-3 items-center">
                <label class="text-gray-600">Nama:</label>
                <input type="text" name="name" class="col-span-2 bg-[#E9EDF0] border-gray-300 rounded p-2 outline-none">
            </div>

            <div class="grid grid-cols-3 items-center">
                <label class="text-gray-600">Username:</label>
                <input type="text" name="username" class="col-span-2 bg-[#E9EDF0] border-gray-300 rounded p-2 outline-none">
            </div>

            <div class="grid grid-cols-3 items-center">
                <label class="text-gray-600">Password:</label>
                <input type="password" name="password" class="col-span-2 bg-[#E9EDF0] border-gray-300 rounded p-2 outline-none">
            </div>

            <div class="flex justify-end gap-0 pt-2">
                <input type="hidden" name="status" :value="pegawaiIsActive ? 'aktif' : 'non-aktif'">
                <button
                    type="button"
                    @click="pegawaiIsActive = false"
                    class="border px-4 py-1 text-xs rounded-l transition-colors"
                    :class="!pegawaiIsActive ? 'bg-[#333333] text-white border-gray-800' : 'bg-white text-gray-600 border-gray-400'">
                    Non Aktif
                </button>

                <button
                    type="button"
                    @click="pegawaiIsActive = true"
                    class="border px-4 py-1 text-xs rounded-r transition-colors"
                    :class="pegawaiIsActive ? 'bg-[#333333] text-white border-gray-800' : 'bg-white text-gray-600 border-gray-400'">
                    Aktif
                </button>
            </div>

            <div class="flex justify-center gap-4 pt-10">
                <button type="button" @click="showForm = false" class="border border-black px-12 py-2 rounded hover:bg-gray-100">Batal</button>
                <button type="submit" class="bg-[#333333] text-white px-12 py-2 rounded hover:bg-black">Simpan</button>
            </div>
        </form>
    </div>

</div>


@endsection