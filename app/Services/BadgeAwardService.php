<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPoint;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BadgeAwardService
{
    /**
     * Evaluasi semua badge untuk satu pengguna.
     * Mengembalikan badge yang baru diperoleh.
     */
    public function checkAndAward(int $userId): Collection
    {
        $user = User::role('masyarakat')->find($userId);

        if (!$user) {
            return collect();
        }

        $validReportsCount = DB::table('reports')
            ->where('user_id', $userId)
            ->where('verification_status', 'valid')
            ->count();

        $totalPoints = (int) (
            UserPoint::query()
                ->where('user_id', $userId)
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

        $newlyAwarded = collect();

        foreach (Badge::all() as $badge) {
            $pointsRequirementMet =
                $badge->required_points <= 0
                || $totalPoints >= $badge->required_points;

            $reportsRequirementMet =
                $badge->required_reports <= 0
                || $validReportsCount >= $badge->required_reports;

            if (!$pointsRequirementMet || !$reportsRequirementMet) {
                continue;
            }

            // Jangan menghapus atau memberikan ulang badge yang sudah dimiliki.
            $userBadge = UserBadge::firstOrCreate(
                [
                    'user_id' => $userId,
                    'badge_id' => $badge->id,
                ],
                [
                    'earned_at' => now(),
                ]
            );
            
            if ($userBadge->wasRecentlyCreated) {
                // Kirim notifikasi hanya untuk badge yang baru diperoleh.
                Notification::create([
                    'user_id' => $userId,
                    'title' => 'Badge Baru Diperoleh!',
                    'message' => "Selamat! Kamu berhasil mendapatkan badge \"{$badge->name}\".",
                    'is_read' => false,
                ]);

                $newlyAwarded->push($userBadge->load('badge'));
            }
        }

        return $newlyAwarded;
    }

    /**
     * Evaluasi semua pengguna masyarakat yang sudah terdaftar.
     */
    public function checkAndAwardAll(): void
    {
        User::role('masyarakat')
            ->select('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $this->checkAndAward((int) $user->id);
                }
            });
    }
}
