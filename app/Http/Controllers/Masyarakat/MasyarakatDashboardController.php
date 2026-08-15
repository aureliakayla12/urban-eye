<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class MasyarakatDashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Masyarakat/Dashboard');
    }
}
