<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        $reports = Report::with([
            'category',
            'district',
            'village',
        ])
            ->latest()
            ->get();

        return Inertia::render('Petugas/Map', [
            'reports' => $reports,
        ]);
    }
}
