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
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('reports')
                    ->whereColumn('reports.user_id', 'users.id');
            })
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
            ->get()
            ->filter(fn ($user) => (int) $user->total_points > 0)
            ->values();

        // Hitung peringkat dengan metode dense ranking.
        $rank = 0;
        $previousPoints = null;

        $leaderboard = $leaderboard->map(
            function ($user) use (&$rank, &$previousPoints) {
                $points = (int) $user->total_points;

                if (
                    $previousPoints === null ||
                    $points !== $previousPoints
                ) {
                    $rank++;
                    $previousPoints = $points;
                }

                $user->rank = $rank;

                return $user;
            }
        )
            // Tampilkan semua pengguna yang masuk peringkat 1–10.
            ->filter(fn ($user) => $user->rank <= 10)
            ->values();

        return Inertia::render('Admin/Gamification/Leaderboard', [
            'leaderboard' => $leaderboard,
        ]);
    }
}