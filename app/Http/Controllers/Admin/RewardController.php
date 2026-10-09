<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\RewardRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::latest()->get();

        return Inertia::render('Admin/Gamification/Rewards/Index', [
            'rewards' => $rewards,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Gamification/Rewards/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reward_type' => ['required', 'in:voucher,pulsa,bibit,lainnya'],
            'description' => ['nullable', 'string'],
            'point_cost' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ]);

        Reward::create($validated);

        return redirect()
            ->route('admin.gamification.rewards')
            ->with('success', 'Reward berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);

        return Inertia::render('Admin/Gamification/Rewards/Edit', [
            'reward' => $reward,
        ]);
    }

    public function update(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reward_type' => ['required', 'in:voucher,pulsa,bibit,lainnya'],
            'description' => ['nullable', 'string'],
            'point_cost' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ]);

        $reward->update($validated);

        return redirect()
            ->route('admin.gamification.rewards')
            ->with('success', 'Reward berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);

        $reward->delete();

        return redirect()
            ->route('admin.gamification.rewards')
            ->with('success', 'Reward berhasil dihapus.');
    }

    /**
     * Halaman poin.
     */
    public function points()
    {
        return Inertia::render('Admin/Gamification/Points');
    }

    /**
     * Halaman leaderboard.
     */
    public function leaderboard()
    {
        $leaderboard = User::role('masyarakat')
            ->select([
                'users.id',
                'users.name',
                'users.email',
            ])
            ->selectSub(function ($query) {
                $query->from('user_points')
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
                        )
                    ")
                    ->whereColumn('user_points.user_id', 'users.id');
            }, 'total_points')
            ->orderByDesc('total_points')
            ->orderBy('users.name')
            ->take(10)
            ->get();

        return Inertia::render('Admin/Gamification/Leaderboard', [
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Daftar penukaran reward.
     */
    public function redemptions()
    {
        $redemptions = RewardRedemption::with([
            'user:id,name,email',
            'reward:id,name,reward_type,point_cost',
        ])
            ->latest()
            ->get();

        return Inertia::render('Admin/Gamification/Redemptions/Index', [
            'redemptions' => $redemptions,
        ]);
    }

    /**
     * Approve penukaran reward.
     */
    public function approveRedemption($id)
    {
        DB::transaction(function () use ($id) {
            $redemption = RewardRedemption::with('reward')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($redemption->status !== 'pending') {
                abort(422, 'Penukaran reward ini sudah diproses.');
            }

            $reward = Reward::lockForUpdate()
                ->findOrFail($redemption->reward_id);

            if (!$reward->status) {
                abort(422, 'Reward sedang tidak aktif.');
            }

            if ($reward->stock <= 0) {
                abort(422, 'Stok reward sudah habis.');
            }

            $totalPoints = UserPoint::where('user_id', $redemption->user_id)
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

            if ($totalPoints < $redemption->points_used) {
                abort(422, 'Poin user tidak mencukupi.');
            }

            UserPoint::create([
                'user_id' => $redemption->user_id,
                'report_id' => null,
                'points' => $redemption->points_used,
                'type' => 'kurang',
                'description' => 'Penukaran reward: ' . $reward->name,
            ]);

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

    /**
     * Menolak penukaran reward.
     */
    public function rejectRedemption($id)
    {
        $redemption = RewardRedemption::findOrFail($id);

        if ($redemption->status !== 'pending') {
            return back()->withErrors([
                'redemption' => 'Penukaran reward ini sudah diproses.',
            ]);
        }

        $redemption->update([
            'status' => 'rejected',
        ]);

        return redirect()
            ->route('admin.gamification.redemptions')
            ->with('success', 'Penukaran reward berhasil ditolak.');
    }

    /**
     * Menandai reward sudah diambil.
     */
    public function takeRedemption($id)
    {
        $redemption = RewardRedemption::findOrFail($id);

        if ($redemption->status !== 'approved') {
            return back()->withErrors([
                'redemption' => 'Hanya reward yang sudah disetujui yang dapat ditandai sebagai diambil.',
            ]);
        }

        $redemption->update([
            'status' => 'taken',
        ]);

        return redirect()
            ->route('admin.gamification.redemptions')
            ->with('success', 'Reward berhasil ditandai sebagai sudah diambil.');
    }
}