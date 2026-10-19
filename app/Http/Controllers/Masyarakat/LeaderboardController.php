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

        $pointTotals = UserPoint::query()
            ->select('user_id')
            ->selectRaw("
                SUM(
                    CASE
                        WHEN type = 'tambah' THEN points
                        WHEN type = 'kurang' THEN -points
                        ELSE 0
                    END
                ) as total_points
            ")
            ->groupBy('user_id');


        $validReportTotals = DB::table('reports')
            ->select('user_id')
            ->selectRaw('COUNT(*) as valid_reports_count')
            ->where('verification_status', 'valid')
            ->groupBy('user_id');


       $rankedUsers = User::role('masyarakat')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reports')
                    ->whereColumn('reports.user_id', 'users.id')
                    ->where('reports.verification_status', 'valid');
            })
            ->leftJoinSub(
                $pointTotals,
                'point_totals',
                'users.id',
                '=',
                'point_totals.user_id'
            )
            ->leftJoinSub(
                $validReportTotals,
                'valid_report_totals',
                'users.id',
                '=',
                'valid_report_totals.user_id'
            )
            ->select(
                'users.id',
                'users.name',
                DB::raw('COALESCE(point_totals.total_points, 0) as points'),
                DB::raw('COALESCE(valid_report_totals.valid_reports_count, 0) as valid_reports_count')
            )
            
            ->orderByDesc('valid_reports_count')
            ->orderByDesc('points')
            ->orderBy('users.id')
            ->get();


        $userBadges = UserBadge::with('badge')
            ->whereIn('user_id', $rankedUsers->pluck('id'))
            ->get()
            ->groupBy('user_id');


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
                    'validReportsCount' => (int) $item->valid_reports_count,
                    'rank' => $index + 1,
                    'badges' => $badges,
                ];
            });

        
        $currentUser = $rankedUsers->firstWhere(
            'id',
            (int) $user->id
        );

        $reportCount = DB::table('reports')
            ->where('user_id', $user->id)
            ->count();

        $validReportsCount = DB::table('reports')
            ->where('user_id', $user->id)
            ->where('verification_status', 'valid')
            ->count();

        $currentUserPoints = (int) (
            UserPoint::query()
                ->where('user_id', $user->id)
                ->selectRaw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN type = 'tambah' THEN points
                                WHEN type = 'kurang' THEN -points
                                ELSE 0
                            END
                        ),
                        0
                    ) as total_points
                ")
                ->value('total_points') ?? 0
        );

        $currentUserData = [
            'id' => (int) $user->id,
            'name' => $user->name,
            'points' => $currentUserPoints,
            'rank' => $currentUser['rank'] ?? null,
            'hasReports' => $reportCount > 0,
            'reportCount' => (int) $reportCount,
            'validReportsCount' => (int) $validReportsCount,
            'badges' => $currentUser['badges'] ?? [],
        ];

        return Inertia::render('Masyarakat/Leaderboard', [
            'leaderboard' => $rankedUsers->take(10)->values(),
            'currentUser' => $currentUserData,
        ]);
    }
}
