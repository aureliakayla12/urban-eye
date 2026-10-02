<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PetugasController extends Controller
{
    public function index()
    {
        $users = User::role('petugas')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Users/Petugas/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Petugas/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole('petugas');

        return redirect()
            ->route('admin.users.petugas.index')
            ->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::role('petugas')->findOrFail($id);

        return Inertia::render('Admin/Users/Petugas/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::role('petugas')->findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()
            ->route('admin.users.petugas.index')
            ->with('success', 'Petugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::role('petugas')->findOrFail($id);

        $user->delete();

        return redirect()
            ->route('admin.users.petugas.index')
            ->with('success', 'Petugas berhasil dihapus.');
    }
}