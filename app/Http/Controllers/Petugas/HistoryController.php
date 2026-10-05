<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ReportAssignment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $history = ReportAssignment::with([
            'report.category',
            'report.user',
        ])
            ->where('officer_id', Auth::id())
            ->latest('updated_at')
            ->get();

        return Inertia::render('Petugas/History', [
            'history' => $history,
        ]);
    }
}
