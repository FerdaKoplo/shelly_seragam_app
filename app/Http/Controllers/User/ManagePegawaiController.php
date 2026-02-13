<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagePegawaiController extends Controller
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
        // ->withQueryString();

        return view('pages.user.admin.manage-pegawai.index', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|unique:user,username|max:255',
            'password' => 'required|string',
            'status'   => 'required|in:Active,Inactive',
        ]);

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
        $user = User::findOrFail($user_id); //

        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user,username,' . $user_id . ',user_id',
            'status'   => 'required|in:Active,Inactive',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data); //
        return back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    // logic to remove a user
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id); //
        $user->delete(); //
        return back()->with('success', 'Pegawai telah dihapus.');
    }
}
