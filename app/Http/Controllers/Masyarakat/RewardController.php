<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class RewardController extends Controller
{
    public function index()
    {
        $user = auth()->user();


        // TOTAL POIN
        
        $totalPoints = UserPoint::where('user_id', $user->id)
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
                ) as total
            ")
            ->value('total');

        $totalPoints = (int) $totalPoints;



        // PERINGKAT
        // Hanya masyarakat yang sudah membuat minimal satu laporan.

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
                DB::raw('COALESCE(point_totals.total_points, 0) as total_points')
            )
            ->orderByDesc('total_points')
            ->orderBy('users.id')
            ->get();

        $rankIndex = $rankedUsers->search(function ($item) use ($user) {
            return (int) $item->id === (int) $user->id;
        });

        // null artinya pengguna belum pernah membuat laporan, jadi belum punya peringkat.
        $rank = $rankIndex !== false ? $rankIndex + 1 : null;
        $totalParticipants = $rankedUsers->count();



        // BADGE

        $badges = UserBadge::with('badge')
            ->where('user_id', $user->id)
            ->orderByDesc('earned_at')
            ->get()
            ->map(function ($userBadge) {
                return [
                    'id' => $userBadge->badge?->id,
                    'name' => $userBadge->badge?->name,
                    'description' => $userBadge->badge?->description,
                    'icon' => $userBadge->badge?->icon,
                    'earned_at' => $userBadge->earned_at?->format('d M Y'),
                ];
            })
            ->values();

        // =========================
        // REWARD
        // =========================

        $rewards = Reward::where('status', true)
            ->orderBy('point_cost')
            ->get()
            ->map(function ($reward) use ($totalPoints) {
                return [
                    'id' => $reward->id,
                    'name' => $reward->name,
                    'reward_type' => $reward->reward_type,
                    'description' => $reward->description,
                    'point_cost' => (int) $reward->point_cost,
                    'stock' => (int) $reward->stock,
                    'image' => $reward->image,
                    'can_redeem' => $totalPoints >= $reward->point_cost
                        && $reward->stock > 0,
                ];
            })
            ->values();


        // REWARD BERIKUTNYA

       $nextReward = $rewards->first(function ($reward) use ($totalPoints) {
            return $reward['stock'] > 0
            && $reward['point_cost'] > $totalPoints;
        });

        if ($nextReward) {
            $nextRewardData = [
                'name' => $nextReward['name'],
                'current_points' => $totalPoints,
                'target_points' => $nextReward['point_cost'],
                'remaining_points' => max(
                    $nextReward['point_cost'] - $totalPoints,
                    0
                ),
            ];
        } else {
            $nextRewardData = null;
        }


        // RIWAYAT POIN

        $pointHistory = UserPoint::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'points' => (int) $point->points,
                    'type' => $point->type,
                    'description' => $point->description,
                    'date' => $point->created_at?->format('d M Y | H:i'),
                ];
            })
            ->values();



        // RIWAYAT PENUKARAN REWARD

        $redemptionHistory = RewardRedemption::with('reward')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($redemption) {
                return [
                    'id' => $redemption->id,
                    'reward_name' => $redemption->reward?->name ?? 'Reward',
                    'points_used' => (int) $redemption->points_used,
                    'status' => $redemption->status,
                    'date' => $redemption->created_at?->format('d M Y | H:i'),
                ];
            })
            ->values();

        return Inertia::render('Masyarakat/Rewards', [
            'totalPoints' => $totalPoints,
            'rank' => $rank,
            'totalParticipants' => $totalParticipants,
            'badges' => $badges,
            'rewards' => $rewards,
            'nextReward' => $nextRewardData,
            'pointHistory' => $pointHistory,
            'redemptionHistory' => $redemptionHistory,
        ]);
    }


    // TUKAR REWARD

    public function redeem(Reward $reward)
    {
        $userId = auth()->id();

        DB::transaction(function () use ($reward, $userId) {
            // Kunci data agar permintaan bersamaan tidak memakai
            // poin atau stok yang sama secara bersamaan.
            $user = User::whereKey($userId)
                ->lockForUpdate()
                ->firstOrFail();

            $reward = Reward::whereKey($reward->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan reward masih aktif dan stok tersedia.
            if (!$reward->status || $reward->stock < 1) {
                throw ValidationException::withMessages([
                    'reward' => 'Reward ini sudah tidak tersedia.',
                ]);
            }

            // Hitung total poin yang dimiliki.
            $earnedPoints = UserPoint::where('user_id', $user->id)
                ->where('type', 'tambah')
                ->sum('points');

            $spentPoints = UserPoint::where('user_id', $user->id)
                ->where('type', 'kurang')
                ->sum('points');

            $totalPoints = (int) $earnedPoints - (int) $spentPoints;

            // Pastikan poin mencukupi.
            if ($totalPoints < $reward->point_cost) {
                throw ValidationException::withMessages([
                    'reward' => 'Poin kamu belum cukup untuk menukar reward ini.',
                ]);
            }

            // Simpan permintaan penukaran.
            RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_used' => $reward->point_cost,
                'status' => 'pending',
                'redeemed_at' => now(),
            ]);

            // Kurangi stok reward.
            $reward->decrement('stock');

            // Catat pengurangan poin.
            UserPoint::create([
                'user_id' => $user->id,
                'report_id' => null,
                'points' => $reward->point_cost,
                'type' => 'kurang',
                'description' => 'Penukaran reward: ' . $reward->name,
            ]);
        });

        return redirect()
            ->route('masyarakat.rewards.index')
            ->with('success', 'Penukaran reward berhasil diajukan.');
    }
}
