<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RewardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Gamification/Rewards/Index');
    }

    public function create()
    {
        return Inertia::render('Admin/Gamification/Rewards/Create');
    }

    public function store()
    {
        //
    }

    public function edit($id)
    {
        return Inertia::render('Admin/Gamification/Rewards/Edit', [
            'id' => $id,
        ]);
    }

    public function update($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function points()
    {
        return Inertia::render('Admin/Gamification/Points');
    }

    public function leaderboard()
    {
        return Inertia::render('Admin/Gamification/Leaderboard');
    }
}