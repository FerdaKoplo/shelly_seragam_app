<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', '=', 'Pegawai');

        // Search by 'nama' or 'username'
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by 'status' (Aktif/Non Aktif)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pegawai = $query->paginate(10)
            ->appends(request()->except('page')); // alternative to withQueryString to ignore annoying IDE error

        return view('pages.user.admin.manage-pegawai.index', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|unique:user,username|max:255',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'max:20'
            ],
            'status'   => 'required|in:Active,Inactive',
        ];

        $messages = [
            'nama.required'     => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan oleh pegawai lain.',
            'password.required' => 'Password wajib diisi.',
            'password.max'      => 'Password tidak boleh lebih dari 20 karakter.',
            'status.required'   => 'Status wajib dipilih.',
            'status.in'         => 'Status yang dipilih tidak valid.',
            'password' => 'Password harus terdiri dari minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter spesial (@$!%*#?&).',
        ];

        $validated = $request->validate($rules, $messages);

        $usernameExist = User::where('username', $request->email)->exists();

        if($usernameExist) {
            return back()->with('error', 'Pegawai dengan ');
        }


        User::create([
            'nama'     => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'status'   => $validated['status'],
            'role'     => 'Pegawai',
        ]);

        return back()->with('success', 'Pegawai berhasil ditambahkan.');
    }
    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $rules = [
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user,username,' . $user_id . ',user_id',
            'status'   => 'required|in:Active,Inactive',
        ];

        $messages = [
            'nama.required'   => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh pegawai lain.',
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status yang dipilih tidak valid.',
            'password.max'    => 'Password tidak boleh lebih dari 20 karakter.',
        ];

        // jika password diisi ada aturan password
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                Password::min(8)->mixedCase()->numbers()->symbols(),
                'max:20'
            ];
            // pesan error khusus untuk password jika diisi
            $messages['password.required'] = 'Password wajib diisi.';
            $messages['password'] = 'Password harus terdiri dari minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter spesial (@$!%*#?&).';
        }

        $data = $request->validate($rules, $messages);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    // logic to remove a user
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        if ($user->transaksis()->exists()) {
            return back()->with('error', 'Pegawai tidak dapat dihapus karena memiliki transaksi aktif');
        }
        $user->delete();
        return back()->with('success', 'Pegawai telah dihapus.');
    }
}
