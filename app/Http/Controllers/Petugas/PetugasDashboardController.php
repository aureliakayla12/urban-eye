<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PetugasDashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/Dashboard');
    }
}
