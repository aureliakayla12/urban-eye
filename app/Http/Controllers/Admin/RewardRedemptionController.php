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

            $redemption->update([
                'status' => 'approved',
                'redeemed_at' => $redemption->redeemed_at ?? now(),
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
                abort(
                    422,
                    'Hanya penukaran yang masih menunggu yang dapat ditolak.'
                );
            }

            $user = User::query()
                ->lockForUpdate()
                ->findOrFail($redemption->user_id);

            $reward = Reward::query()
                ->lockForUpdate()
                ->findOrFail($redemption->reward_id);

            UserPoint::create([
                'user_id' => $user->id,
                'report_id' => null,
                'points' => $redemption->points_used,
                'type' => 'tambah',
                'description' => 'Pengembalian poin karena penukaran reward ditolak: '
                    . $reward->name,
            ]);

            $reward->increment('stock');

            $redemption->update([
                'status' => 'rejected',
            ]);
        });

        return redirect()
            ->route('admin.gamification.redemptions')
            ->with('success', 'Penukaran reward ditolak. Poin dan stok telah dikembalikan.');
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