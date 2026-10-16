<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/Index', [
            'settings' => [
                'app_name' => 'Urban Eye',
                'app_description' => 'Platform pelaporan dan pemantauan masalah lingkungan.',
                'reward_enabled' => Setting::getValue('reward_enabled', '1') === '1',
                'admin_whatsapp' => Setting::getValue('admin_whatsapp', ''),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'reward_enabled' => ['required', 'boolean'],
            'admin_whatsapp' => ['nullable', 'string', 'max:20'],
        ]);

        Setting::setValue(
            'reward_enabled',
            $validated['reward_enabled'] ? '1' : '0'
        );

        Setting::setValue(
            'admin_whatsapp',
            $validated['admin_whatsapp'] ?? ''
        );

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Password berhasil diubah!');
    }
}