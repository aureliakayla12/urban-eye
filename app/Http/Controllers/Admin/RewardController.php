<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'reward_type' => ['required', 'in:pulsa,bibit,tumbler,totebag'],
            'description' => ['nullable', 'string'],
            'point_cost' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('rewards', 'public');
        } else {
            $validated['image'] = null;
        }

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
            'reward_type' => ['required', 'in:pulsa,bibit,tumbler,totebag'],
            'description' => ['nullable', 'string'],
            'point_cost' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'boolean'],
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }

            $validated['image'] = $request->file('image')
                ->store('rewards', 'public');
        }

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

   
    public function points()
    {
        return Inertia::render('Admin/Gamification/Points');
    }


    public function leaderboard()
    {
        $validReportTotals = DB::table('reports')
            ->select('user_id')
            ->selectRaw('COUNT(*) as valid_reports_count')
            ->where('verification_status', 'valid')
            ->groupBy('user_id');

        $pointTotals = DB::table('user_points')
            ->select('user_id')
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
            ->groupBy('user_id');

        $leaderboard = User::role('masyarakat')
            ->joinSub(
                $validReportTotals,
                'valid_report_totals',
                'users.id',
                '=',
                'valid_report_totals.user_id'
            )
            ->leftJoinSub(
                $pointTotals,
                'point_totals',
                'users.id',
                '=',
                'point_totals.user_id'
            )
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'valid_report_totals.valid_reports_count',
            ])
            ->selectRaw('COALESCE(point_totals.total_points, 0) as total_points')
            ->orderByDesc('valid_reports_count')
            ->orderByDesc('total_points')
            ->orderBy('users.id')
            ->limit(10)
            ->get();

        $leaderboard = $leaderboard->values()->map(
            function ($user, $index) {
                $user->rank = $index + 1;

                return $user;
            }
        );

        return Inertia::render('Admin/Gamification/Leaderboard', [
            'leaderboard' => $leaderboard,
        ]);
    }
}