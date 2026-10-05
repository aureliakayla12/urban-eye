<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ReportAssignment;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    public function index()
    {
        $tasks = ReportAssignment::where('officer_id', Auth::id())
            ->get();

        $total = $tasks->count();

        $diproses = $tasks->where('status', 'diproses')->count();

        $selesai = $tasks->where('status', 'selesai')->count();

        $ditugaskan = $tasks->where('status', 'ditugaskan')->count();

        return Inertia::render('Petugas/Statistics', [
            'statistics' => [
                'total' => $total,
                'diproses' => $diproses,
                'selesai' => $selesai,
                'ditugaskan' => $ditugaskan,
            ],
        ]);
    }
}
