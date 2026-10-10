<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Inertia\Inertia;

class DetailLaporanController extends Controller
{
    public function show(Report $report)
    {
        $report->load([
            'user',
            'category',
            'district',
            'village',
            'images',
            'assignments.officer',
        ]);

        return Inertia::render('Petugas/DetailLaporan', [
            'report' => $report,
        ]);
    }
}
