<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAssignment;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'masyarakat';

        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($role === 'admin') {
            $data['stats'] = [
                'total' => Report::count(),

                // Belum ada kolom priority di reports
                'mendesak' => 0,

                'diproses' => Report::where('status', 'diproses')
                    ->count(),

                'selesai' => Report::where('status', 'selesai')
                    ->count(),
            ];

            $data['laporan_terbaru'] = Report::with('category')
                ->latest()
                ->take(5)
                ->get();

            $data['kategori'] = Report::query()
                ->select(
                    'category_id',
                    DB::raw('COUNT(*) as total')
                )
                ->with('category')
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->get();

            $data['aktivitas_petugas'] = ReportAssignment::with([
                    'officer',
                    'report',
                ])
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($assignment) {
                    if ($assignment->status === 'selesai') {
                        $description = 'menyelesaikan laporan';
                    } elseif ($assignment->status === 'diproses') {
                        $description = 'sedang menangani laporan';
                    } else {
                        $description = 'menerima tugas laporan';
                    }

                    return [
                        'id' => $assignment->id,
                        'user' => [
                            'name' => $assignment->officer?->name,
                        ],
                        'description' => $description,
                        'report' => $assignment->report?->title,
                        'time' => $assignment->updated_at?->diffForHumans(),
                    ];
                });
        }

        /*
        |--------------------------------------------------------------------------
        | PETUGAS
        |--------------------------------------------------------------------------
        */

        if ($role === 'petugas') {
            $data['stats'] = [
                'tugas_hari_ini' => ReportAssignment::where(
                        'officer_id',
                        $user->id
                    )
                    ->whereDate('assigned_at', today())
                    ->count(),

                'ditugaskan' => ReportAssignment::where(
                        'officer_id',
                        $user->id
                    )
                    ->where('status', 'ditugaskan')
                    ->count(),

                'diproses' => ReportAssignment::where(
                        'officer_id',
                        $user->id
                    )
                    ->where('status', 'diproses')
                    ->count(),

                'selesai' => ReportAssignment::where(
                        'officer_id',
                        $user->id
                    )
                    ->where('status', 'selesai')
                    ->count(),
            ];

            $data['tugas'] = ReportAssignment::with([
                    'report.category',
                    'report.user',
                ])
                ->where('officer_id', $user->id)
                ->latest('assigned_at')
                ->take(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | MASYARAKAT
        |--------------------------------------------------------------------------
        */

       if ($role === 'masyarakat') {
    $reports = Report::with('category')
        ->where('user_id', $user->id);

    $data['stats'] = [
        'total_laporan' => (clone $reports)->count(),

        'menunggu' => (clone $reports)
            ->where('status', 'menunggu')
            ->count(),

        'diproses' => (clone $reports)
            ->where('status', 'diproses')
            ->count(),

        'selesai' => (clone $reports)
            ->where('status', 'selesai')
            ->count(),
    ];

    $data['laporan_terbaru'] = (clone $reports)
        ->latest()
        ->take(5)
        ->get();

    $data['peta_laporan'] = Report::with('category')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->latest()
        ->get([
            'id',
            'category_id',
            'title',
            'photo',
            'latitude',
            'longitude',
            'address',
            'status',
        ]);

    /*
     * Data gamifikasi akan digunakan saat fitur
     * poin, badge, dan leaderboard Sprint 3 selesai.
     */
    $data['poin'] = 0;
    $data['badge'] = 0;
    $data['peringkat'] = '-';
    $data['leaderboard'] = [];
}

        return Inertia::render('Dashboard', $data);
    }
}