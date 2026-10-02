<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class MasyarakatController extends Controller
{
    public function index()
    {
        $users = User::role('masyarakat')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Users/Masyarakat/Index', [
            'users' => $users,
        ]);
    }
}