<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAssignment;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPoint;
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

        // ROLE ADMIN

        if ($role === 'admin') {
            $data['stats'] = [
                'total' => Report::count(),
                'mendesak' => Report::whereIn('urgency_level', [
                    'sangat_mendesak',
                    'mendesak',
                ])->count(),
                'diproses' => Report::where('status', 'diproses')->count(),
                'selesai' => Report::where('status', 'selesai')->count(),
            ];

            $data['laporan_terbaru'] = Report::with('category')
                ->latest()
                ->take(5)
                ->get();

            $data['kategori'] = Report::query()
                ->select('category_id', DB::raw('COUNT(*) as total'))
                ->with('category')
                ->whereNotNull('category_id')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->get();

            $data['aktivitas_petugas'] = ReportAssignment::with(['officer', 'report'])
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
                        'user' => ['name' => $assignment->officer?->name],
                        'description' => $description,
                        'report' => $assignment->report?->title,
                        'time' => $assignment->updated_at?->diffForHumans(),
                    ];
                });
        }

        // ROLE PETUGAS

        if ($role === 'petugas') {
            $data['stats'] = [
                'tugas_hari_ini' => ReportAssignment::where('officer_id', $user->id)
                    ->whereDate('assigned_at', today())
                    ->count(),
                'ditugaskan' => ReportAssignment::where('officer_id', $user->id)
                    ->where('status', 'ditugaskan')
                    ->count(),
                'diproses' => ReportAssignment::where('officer_id', $user->id)
                    ->where('status', 'diproses')
                    ->count(),
                'selesai' => ReportAssignment::where('officer_id', $user->id)
                    ->where('status', 'selesai')
                    ->count(),
            ];

            $data['tugas'] = ReportAssignment::with(['report.category', 'report.user'])
                ->where('officer_id', $user->id)
                ->latest('assigned_at')
                ->take(5)
                ->get();
        }

       //ROLE MASYARAKAT
        if ($role === 'masyarakat') {
            $reports = Report::query()
                ->with('category')
                ->where('user_id', $user->id);

            $data['stats'] = [
                'total_laporan' => (clone $reports)->count(),
                'menunggu' => (clone $reports)->where('status', 'menunggu')->count(),
                'diproses' => (clone $reports)->where('status', 'diproses')->count(),
                'selesai' => (clone $reports)->where('status', 'selesai')->count(),
            ];

            $data['laporan_terbaru'] = (clone $reports)
                ->latest('created_at')
                ->take(3)
                ->get();

            $data['peta_laporan'] = Report::with('category')
                ->where('user_id', $user->id)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->latest('created_at')
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

            $lifetimePoints = UserPoint::query()
                ->where('user_id', $user->id)
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'tambah' THEN points WHEN type = 'kurang' THEN -points ELSE 0 END), 0) as total_points")
                ->value('total_points');

            $data['poin'] = (int) ($lifetimePoints ?? 0);

            $userBadges = UserBadge::with('badge')
                ->where('user_id', $user->id)
                ->orderByDesc('earned_at')
                ->orderByDesc('created_at')
                ->get();

            $data['badge'] = $userBadges->first()?->badge?->name ?? 'Belum Ada';
            $data['badge_count'] = $userBadges->count();

            $communityUsersWithReports = User::role('masyarakat')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('reports')
                        ->whereColumn('reports.user_id', 'users.id');
                });

            $lifetimePointTotals = UserPoint::query()
                ->select('user_id')
                ->selectRaw("SUM(CASE WHEN type = 'tambah' THEN points WHEN type = 'kurang' THEN -points ELSE 0 END) as total_points")
                ->groupBy('user_id');

            $rankedUsers = (clone $communityUsersWithReports)
                ->leftJoinSub($lifetimePointTotals, 'point_totals', 'users.id', '=', 'point_totals.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('COALESCE(point_totals.total_points, 0) as points')
                )
                ->orderByDesc('points')
                ->orderBy('users.id')
                ->get();

            $currentUserRankIndex = $rankedUsers->search(
                fn ($rankedUser) => (int) $rankedUser->id === (int) $user->id
            );
            $data['peringkat'] = $currentUserRankIndex === false
                ? '-'
                : $currentUserRankIndex + 1;

            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();

            $weeklyPointTotals = UserPoint::query()
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->select('user_id')
                ->selectRaw("SUM(CASE WHEN type = 'tambah' THEN points WHEN type = 'kurang' THEN -points ELSE 0 END) as weekly_points")
                ->groupBy('user_id');

            $data['leaderboard'] = (clone $communityUsersWithReports)
                ->joinSub($weeklyPointTotals, 'weekly_point_totals', 'users.id', '=', 'weekly_point_totals.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('COALESCE(weekly_point_totals.weekly_points, 0) as points')
                )
                ->orderByDesc('points')
                ->orderBy('users.id')
                ->take(3)
                ->get()
                ->map(fn ($leader) => [
                    'id' => (int) $leader->id,
                    'name' => $leader->name,
                    'points' => (int) $leader->points,
                ])
                ->values();
        }

        return Inertia::render('Dashboard', $data);
    }
}
