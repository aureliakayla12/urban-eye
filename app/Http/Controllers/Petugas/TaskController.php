<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ReportAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = ReportAssignment::with([
            'report',
            'report.category',
            'report.user',
        ])
            ->where('officer_id', Auth::id())
            ->latest('assigned_at')
            ->get();

        return Inertia::render('Petugas/Tasks', [
            'tasks' => $tasks,
        ]);
    }

    public function show($id)
    {
        $task = ReportAssignment::with([
            'report',
            'report.category',
            'report.user',
            'report.district',
            'report.village',
            'report.images',
            'officer',
        ])
            ->where('id', $id)
            ->where('officer_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Petugas/DetailLaporan', [
            'task' => $task,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ditugaskan,diproses,selesai',
            'note' => 'nullable|string',
        ]);

        $task = ReportAssignment::where('id', $id)
            ->where('officer_id', Auth::id())
            ->firstOrFail();

        $data = [
            'status' => $request->status,
            'note' => $request->note,
        ];

        if ($request->status === 'diproses' && !$task->accepted_at) {
            $data['accepted_at'] = now();
        }

        if ($request->status === 'selesai' && !$task->finished_at) {
            $data['finished_at'] = now();
        }

        $task->update($data);

        // Sinkronisasi status laporan
        if ($task->report) {
            $task->report->update([
                'status' => $request->status === 'ditugaskan'
                    ? 'menunggu'
                    : $request->status,
            ]);
        }

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }
}
