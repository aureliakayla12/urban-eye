<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HelpController extends Controller
{
    public function index()
    {
        return Inertia::render('Petugas/Help');
    }
}
