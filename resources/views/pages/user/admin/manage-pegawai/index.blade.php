@extends('layouts.user.layout')
@section('title', 'Manage Pegawai')
@section('content')

<div class="px-4 md:px-12 pb-20"
    x-data="{
        showForm: false,
        isEdit: false,
        actionUrl: '{{ route('manage.pegawai.store') }}',
        formData: {
            nama: '',
            username: '',
            password: '',
        },
        pegawaiIsActive: true,

        editPegawai(p) {
            this.isEdit = true;
            this.showForm = true;
            this.formData.nama = p.nama;
            this.formData.username = p.username;
            this.formData.password = ''; 
            this.pegawaiIsActive = (p.status === 'Active');
            this.actionUrl = '/admin/manage-pegawai/' + p.user_id;
            if(window.innerWidth < 768) {
                setTimeout(() => {
                    document.getElementById('pegawaiForm').scrollIntoView({ behavior: 'smooth' });
                }, 100);
            }
        },

        resetForm() {
            this.isEdit = false;
            this.showForm = true;
            this.formData.nama = '';
            this.formData.username = '';
            this.formData.password = '';
            this.pegawaiIsActive = true;
            this.actionUrl = '{{ route('manage.pegawai.store') }}';
             if(window.innerWidth < 768) {
                setTimeout(() => {
                    document.getElementById('pegawaiForm').scrollIntoView({ behavior: 'smooth' });
                }, 100);
            }
        }
    }">

    <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 mb-6 mt-6">
        <form action="{{ route('manage.pegawai') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/Username..."
                class="border border-gray-300 rounded px-4 py-2 w-full sm:w-64 focus:outline-none focus:ring-1 focus:ring-black text-sm">
            
            <div class="flex gap-2">
                <select name="status" class="flex-1 border border-gray-300 rounded px-4 py-2 bg-white text-sm">
                    <option value="">Semua Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Aktif</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                <button type="submit" id="submitFilter" class="bg-gray-800 text-white px-6 py-2 rounded text-sm hover:bg-black transition">Filter</button>
            </div>
        </form>

        <button @click="resetForm()" class="bg-[#333333] text-white px-6 py-2 rounded shadow-sm hover:bg-black transition text-sm font-bold">
            + Tambah Pegawai
        </button>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider">Nama</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider">Username</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pegawai as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 text-sm font-medium text-gray-900"> {{ $p->nama }} </td>
                        <td data-cy="table-pgw-name" class="py-4 px-6 text-sm text-gray-500">{{ $p->username }} </td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $p->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 flex justify-center gap-2">
                            <button @click="editPegawai({{ json_encode($p) }})"
                                class="bg-[#4A90E2] text-white px-4 py-1.5 rounded text-xs font-bold hover:bg-blue-600 transition">
                                Edit
                            </button>
                            <form action="{{ route('manage.pegawai.destroy', $p->user_id) }}" method="POST" onsubmit="return confirm('Hapus pegawai ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-[#C04D41] text-white px-4 py-1.5 rounded text-xs font-bold hover:bg-red-700 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-gray-400 italic">Data pegawai tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-10">
        {{ $pegawai->appends(request()->query())->links('vendor.pagination.custom') }}
    </div>

    <div x-show="showForm" x-transition id="pegawaiForm" x-cloak
        class="mt-10 w-full max-w-2xl border border-gray-300 rounded-2xl p-6 md:p-10 bg-white shadow-xl mx-auto mb-20">
        
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800" x-text="isEdit ? 'Edit Data Pegawai' : 'Registrasi Pegawai Baru'"></h2>
            <button @click="showForm = false" class="text-gray-400 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form :action="actionUrl" method="POST" class="space-y-6">
            @csrf
            <template x-if="isEdit">
                @method('PUT')
            </template>

            <div class="flex flex-col md:grid md:grid-cols-3 md:items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" x-model="formData.nama" required
                    class="md:col-span-2 bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-black outline-none transition">
            </div>

            <div class="flex flex-col md:grid md:grid-cols-3 md:items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Username</label>
                <input type="text" name="username" x-model="formData.username" required
                    class="md:col-span-2 bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-black outline-none transition">
            </div>

            <div class="flex flex-col md:grid md:grid-cols-3 md:items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Password</label>
                <div class="md:col-span-2">
                    <input type="password" name="password" x-model="formData.password"
                        :placeholder="isEdit ? '(Biarkan kosong jika tidak diubah)' : 'Masukkan password'"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-black outline-none transition">
                </div>
            </div>

            <div class="flex flex-col md:grid md:grid-cols-3 md:items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Status Akun</label>
                <div class="md:col-span-2 flex">
                    <input type="hidden" name="status" :value="pegawaiIsActive ? 'Active' : 'Inactive'">
                    <button type="button" @click="pegawaiIsActive = false"
                        class="flex-1 border py-2 text-xs font-bold rounded-l-lg transition-all"
                        :class="!pegawaiIsActive ? 'bg-black text-white border-black' : 'bg-white text-gray-400 border-gray-200 hover:bg-gray-50'">
                        NON-AKTIF
                    </button>
                    <button type="button" @click="pegawaiIsActive = true"
                        class="flex-1 border py-2 text-xs font-bold rounded-r-lg transition-all"
                        :class="pegawaiIsActive ? 'bg-black text-white border-black' : 'bg-white text-gray-400 border-gray-200 hover:bg-gray-50'">
                        AKTIF
                    </button>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-3 pt-6">
                <button type="button" @click="showForm = false" 
                    class="flex-1 sm:flex-none border border-black px-12 py-3 rounded-lg font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" 
                    class="flex-1 sm:flex-none bg-black text-white px-12 py-3 rounded-lg font-bold hover:bg-gray-800 transition shadow-lg">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection