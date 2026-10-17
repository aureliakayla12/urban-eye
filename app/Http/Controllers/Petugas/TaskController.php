<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ReportAssignment;
use App\Models\Notification;
use App\Models\User;
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
            'report.images',
        ])
            ->where('officer_id', Auth::id())
            ->whereIn('status', ['ditugaskan', 'diproses'])
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
        $validated = $request->validate([
            'status' => 'required|in:ditugaskan,diproses,selesai',
            'note' => 'nullable|string',
        ]);

        $task = ReportAssignment::where('id', $id)
            ->where('officer_id', Auth::id())
            ->firstOrFail();

        // Simpan status sebelumnya untuk mencegah notifikasi berulang.
        $previousStatus = $task->status;

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

        // Sinkronisasi status laporan dan kirim notifikasi.
        $report = $task->report;

        if ($report) {
            $report->update([
                'status' => $request->status === 'ditugaskan'
                    ? 'menunggu'
                    : $request->status,
            ]);

            // Kirim notifikasi hanya jika status tugas benar-benar berubah.
            if ($previousStatus !== $request->status) {
                if ($request->status === 'diproses') {
                    Notification::create([
                        'user_id' => $report->user_id,
                        'report_id' => $report->id,
                        'title' => 'Laporan Sedang Ditangani',
                        'message' => "Laporan \"{$report->title}\" sedang ditangani oleh Petugas.",
                        'is_read' => false,
                    ]);
                } elseif ($request->status === 'selesai') {
                    // Notifikasi untuk Masyarakat pemilik laporan
                    Notification::create([
                        'user_id' => $report->user_id,
                        'report_id' => $report->id,
                        'title' => 'Penanganan Laporan Selesai',
                        'message' => "Penanganan laporan \"{$report->title}\" telah selesai.",
                        'is_read' => false,
                    ]);

                    // Notifikasi untuk semua Admin
                    $admins = User::role('admin')->get();

                    foreach ($admins as $admin) {
                        Notification::create([
                            'user_id' => $admin->id,
                            'report_id' => $report->id,
                            'title' => 'Penanganan Laporan Selesai',
                            'message' => "Petugas telah menyelesaikan penanganan laporan \"{$report->title}\".",
                            'is_read' => false,
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }
}
