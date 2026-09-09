<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'masyarakat';
        
        // Debug: cek apakah data terkirim
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
            ],
            'stats' => [
                'test' => 'Data berhasil dikirim!',
                'role_detected' => $role,
                'total_users' => User::count(),
            ]
        ];
        
        // Log untuk debugging
        \Log::info('Dashboard data:', $data);
        
        return Inertia::render('Dashboard', $data);
    }
}