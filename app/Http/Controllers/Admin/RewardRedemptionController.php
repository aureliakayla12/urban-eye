<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RewardRedemptionController extends Controller
{
    public function index()
    {
        $redemptions = RewardRedemption::with([
            'user:id,name,email',
            'reward:id,name,reward_type,point_cost,stock',
        ])
            ->latest()
            ->get();

        return Inertia::render('Admin/Gamification/Redemptions/Index', [
            'redemptions' => $redemptions,
        ]);
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            $redemption = RewardRedemption::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($redemption->status !== 'pending') {
                abort(422, 'Penukaran ini sudah diproses.');
            }

            // Kunci akun agar beberapa penukaran dari pengguna
            // yang sama tidak menggunakan saldo yang sama bersamaan.
            $user = User::query()
                ->lockForUpdate()
                ->findOrFail($redemption->user_id);

            $reward = Reward::query()
                ->lockForUpdate()
                ->findOrFail($redemption->reward_id);

            if (!$reward->status) {
                abort(422, 'Reward sedang tidak aktif.');
            }

            if ($reward->stock <= 0) {
                abort(422, 'Stok reward sudah habis.');
            }

            // Harga pada pengajuan harus cocok dengan harga reward
            // yang berlaku. Jika harga berubah, jangan proses otomatis.
            if ((int) $redemption->points_used !== (int) $reward->point_cost) {
                abort(
                    422,
                    'Harga reward berubah. Tolak pengajuan ini dan minta pengguna mengajukan kembali.'
                );
            }

            $totalPoints = UserPoint::query()
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
                    ) as total
                ")
                ->value('total');

            if ((int) $totalPoints < (int) $redemption->points_used) {
                abort(422, 'Poin pengguna tidak mencukupi.');
            }

            // Catat pengurangan poin di ledger.
            UserPoint::create([
                'user_id' => $user->id,
                'report_id' => null,
                'points' => $redemption->points_used,
                'type' => 'kurang',
                'description' => 'Penukaran reward: ' . $reward->name,
            ]);

            // Kurangi stok dan ubah status dalam transaksi yang sama.
            $reward->decrement('stock');

            $redemption->update([
                'status' => 'approved',
                'redeemed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.gamification.redemptions')
            ->with('success', 'Penukaran reward berhasil disetujui.');
    }

    public function reject($id)
    {
        DB::transaction(function () use ($id) {
            $redemption = RewardRedemption::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($redemption->status !== 'pending') {
                abort(422, 'Hanya penukaran yang masih menunggu yang dapat ditolak.');
            }

            $redemption->update([
                'status' => 'rejected',
            ]);
        });

        return redirect()
            ->route('admin.gamification.redemptions')
            ->with('success', 'Penukaran reward ditolak.');
    }

    public function taken($id)
    {
        DB::transaction(function () use ($id) {
            $redemption = RewardRedemption::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($redemption->status !== 'approved') {
                abort(422, 'Hanya penukaran yang sudah disetujui yang dapat ditandai sudah diambil.');
            }

            $redemption->update([
                'status' => 'taken',
            ]);
        });

        return redirect()
            ->route('admin.gamification.redemptions')
            ->with('success', 'Reward berhasil ditandai sudah diambil.');
    }
}