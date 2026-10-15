<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Hitung saldo poin dari riwayat transaksi poin.
        $pointTotals = UserPoint::query()
            ->select(
                'user_id',
                DB::raw("
                    SUM(
                        CASE
                            WHEN type = 'tambah' THEN points
                            WHEN type = 'kurang' THEN -points
                            ELSE 0
                        END
                    ) as total_points
                ")
            )
            ->groupBy('user_id');

        // Hanya masyarakat yang pernah membuat setidaknya satu laporan
        // yang boleh memiliki peringkat dan muncul di leaderboard.
        $rankedUsers = User::role('masyarakat')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reports')
                    ->whereColumn('reports.user_id', 'users.id');
            })
            ->leftJoinSub(
                $pointTotals,
                'point_totals',
                'users.id',
                '=',
                'point_totals.user_id'
            )
            ->select(
                'users.id',
                'users.name',
                DB::raw('COALESCE(point_totals.total_points, 0) as points')
            )
            ->orderByDesc('points')
            ->orderBy('users.id')
            ->get();

        // Ambil badge yang sudah diperoleh oleh pengguna yang memenuhi syarat ranking.
        $userBadges = UserBadge::with('badge')
            ->whereIn('user_id', $rankedUsers->pluck('id'))
            ->get()
            ->groupBy('user_id');

        // Beri nomor peringkat secara berurutan berdasarkan total poin.
        $rankedUsers = $rankedUsers
            ->values()
            ->map(function ($item, $index) use ($userBadges) {
                $badges = $userBadges
                    ->get($item->id, collect())
                    ->map(function ($userBadge) {
                        if (!$userBadge->badge) {
                            return null;
                        }

                        return [
                            'name' => $userBadge->badge->name,
                            'icon' => $userBadge->badge->icon,
                        ];
                    })
                    ->filter()
                    ->values();

                return [
                    'id' => (int) $item->id,
                    'name' => $item->name,
                    'points' => (int) $item->points,
                    'rank' => $index + 1,
                    'badges' => $badges,
                ];
            });

        // Peringkat pengguna login mengikuti daftar leaderboard yang sama.
        $currentUser = $rankedUsers->firstWhere('id', (int) $user->id);
        $reportCount = DB::table('reports')
            ->where('user_id', $user->id)
            ->count();

        $currentUserPoints = (int) (UserPoint::query()
            ->where('user_id', $user->id)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'tambah' THEN points WHEN type = 'kurang' THEN -points ELSE 0 END), 0) as total_points")
            ->value('total_points') ?? 0);

        $currentUserData = [
            'id' => (int) $user->id,
            'name' => $user->name,
            'points' => $currentUserPoints,
            'rank' => $currentUser['rank'] ?? null,
            'hasReports' => $reportCount > 0,
            'reportCount' => (int) $reportCount,
        ];

        return Inertia::render('Masyarakat/Leaderboard', [
            'leaderboard' => $rankedUsers->take(10)->values(),
            'currentUser' => $currentUserData,
        ]);
    }
}
