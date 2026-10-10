<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::latest()->get();

        return Inertia::render('Admin/Gamification/Badges/Index', [
            'badges' => $badges,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Gamification/Badges/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'required_points' => ['required', 'integer', 'min:0'],
            'required_reports' => ['required', 'integer', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        Badge::create($validated);

        return redirect()
            ->route('admin.gamification.badges')
            ->with('success', 'Badge berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $badge = Badge::findOrFail($id);

        return Inertia::render('Admin/Gamification/Badges/Edit', [
            'badge' => $badge,
        ]);
    }

    public function update(Request $request, $id)
    {
        $badge = Badge::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'required_points' => ['required', 'integer', 'min:0'],
            'required_reports' => ['required', 'integer', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        $badge->update($validated);

        return redirect()
            ->route('admin.gamification.badges')
            ->with('success', 'Badge berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $badge = Badge::findOrFail($id);

        $badge->delete();

        return redirect()
            ->route('admin.gamification.badges')
            ->with('success', 'Badge berhasil dihapus.');
    }
}