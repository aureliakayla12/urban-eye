<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Category;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Notification;
use App\Services\BadgeAwardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with([
            'user',
            'category',
            'district',
            'village',
        ])->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $reports = $query->paginate(10)->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        $statuses = [
            'menunggu',
            'diproses',
            'selesai',
            'ditolak',
        ];

        $verificationStatuses = [
            'pending',
            'valid',
            'hoax',
        ];

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'categories' => $categories,
            'statuses' => $statuses,
            'verificationStatuses' => $verificationStatuses,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'verification_status' => $request->verification_status,
                'category_id' => $request->category_id,
            ],
        ]);
    }

    public function show($id)
    {
        $report = Report::with([
            'user',
            'category',
            'district',
            'village',
            'assignments.officer',
            'assignments.assignedBy',
        ])->findOrFail($id);

        $officers = User::role('petugas')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);

        return Inertia::render('Admin/Reports/Show', [
            'report' => $report,
            'officers' => $officers,
        ]);
    }

    public function edit($id)
    {
        $report = Report::with([
            'user',
            'category',
            'district',
            'village',
        ])->findOrFail($id);

        $categories = Category::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Reports/Edit', [
            'report' => $report,
            'categories' => $categories,
        ]);
    }

        public function update(
            Request $request,
            BadgeAwardService $badgeAwardService,
            $id
        ) {
        $report = Report::findOrFail($id);

        // Assign petugas
        if ($request->filled('officer_id')) {
            if ($report->verification_status !== 'valid') {
                return back()
                    ->withErrors([
                        'officer_id' => 'Laporan harus diverifikasi valid sebelum ditugaskan kepada petugas.',
                    ])
                    ->with(
                        'error',
                        'Petugas belum dapat ditugaskan karena laporan belum diverifikasi valid.'
                    );
            }

            $officer = User::role('petugas')
                ->where('id', $request->officer_id)
                ->first();

            if (!$officer) {
                return back()
                    ->withErrors([
                        'officer_id' => 'Petugas yang dipilih tidak valid.',
                    ])
                    ->with('error', 'Petugas yang dipilih tidak valid.');
            }

            $assignment = $report->assignments()->latest()->first();
            $previousOfficerId = $assignment?->officer_id;

            if ($assignment) {
                $assignment->update([
                    'officer_id' => $officer->id,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status' => 'ditugaskan',
                ]);
            } else {
                $report->assignments()->create([
                    'officer_id' => $officer->id,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status' => 'ditugaskan',
                ]);
            }

            
            // Catat riwayat dan kirim notifikasi jika petugas baru
            // ditugaskan atau petugas yang menangani berubah.
            if ((int) $previousOfficerId !== (int) $officer->id) {
                $report->statusHistories()->create([
                    'changed_by' => auth()->id(),
                    'old_status' => $report->status,
                    'new_status' => $report->status,
                    'note' => "Petugas ditugaskan kepada {$officer->name}.",
                    'changed_at' => now(),
                ]);

                // Notifikasi kepada Petugas yang ditugaskan.
                Notification::create([
                    'user_id' => $officer->id,
                    'report_id' => $report->id,
                    'title' => 'Tugas Laporan Baru',
                    'message' => "Kamu ditugaskan untuk menangani laporan \"{$report->title}\".",
                    'is_read' => false,
                ]);

                // Beri tahu Masyarakat siapa yang menangani laporannya.
                Notification::create([
                    'user_id' => $report->user_id,
                    'report_id' => $report->id,
                    'title' => 'Petugas Ditugaskan',
                    'message' => "Laporan kamu telah ditugaskan kepada {$officer->name}.",
                    'is_read' => false,
                ]);
            }


            return redirect()
                ->route('admin.reports.show', $report->id)
                ->with('success', 'Petugas berhasil ditugaskan.');
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'address' => [
                'required',
                'string',
            ],

            'status' => [
                'required',
                'in:menunggu,diproses,selesai,ditolak',
            ],

            'verification_status' => [
                'required',
                'in:pending,valid,hoax',
            ],
        ]);

        $previousVerificationStatus = $report->verification_status;
        $previousStatus = $report->status;

        $report->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'] ?? null,
            'address' => $validated['address'],
            'status' => $validated['status'],
            'verification_status' => $validated['verification_status'],
        ]);

        // Catat perubahan status dan verifikasi ke riwayat laporan.
        $statusChanged = $previousStatus !== $report->status;

        $verificationChanged =
            $previousVerificationStatus !== $report->verification_status;

        if ($statusChanged || $verificationChanged) {
            $notes = [];

            if ($verificationChanged) {
                $verificationLabels = [
                    'pending' => 'Menunggu verifikasi',
                    'valid' => 'Terverifikasi valid',
                    'hoax' => 'Dinyatakan tidak valid (hoaks)',
                ];

                $oldVerificationLabel =
                    $verificationLabels[$previousVerificationStatus]
                    ?? $previousVerificationStatus;

                $newVerificationLabel =
                    $verificationLabels[$report->verification_status]
                    ?? $report->verification_status;

                $notes[] = "Verifikasi diubah dari {$oldVerificationLabel} menjadi {$newVerificationLabel} oleh Admin.";
            }

            if ($statusChanged) {
                $statusLabels = [
                    'menunggu' => 'Menunggu',
                    'diproses' => 'Sedang diproses',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                ];

                $oldStatusLabel =
                    $statusLabels[$previousStatus] ?? $previousStatus;

                $newStatusLabel =
                    $statusLabels[$report->status] ?? $report->status;

                $notes[] = "Status laporan diubah dari {$oldStatusLabel} menjadi {$newStatusLabel} oleh Admin.";
            }

            $report->statusHistories()->create([
                'changed_by' => auth()->id(),
                'old_status' => $previousStatus,
                'new_status' => $report->status,
                'note' => implode(' ', $notes),
                'changed_at' => now(),
            ]);

        }

       
        // Hitung saldo poin khusus untuk laporan ini.
        // Poin tambah bernilai positif, sedangkan poin kurang bernilai negatif.
        $reportPoints = (int) UserPoint::where('report_id', $report->id)
            ->where(function ($query) {
                $query->whereNull('description')
                    ->orWhereNotIn('description', [
                        'Penalti poin karena laporan terbukti hoaks.',
                        'Pengembalian penalti karena status laporan tidak lagi hoaks.',
                    ]);
            })
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
                ) as balance
            ")
            ->value('balance');

       
        // Tentukan saldo poin yang seharusnya berdasarkan status terbaru.
        // Laporan valid bernilai 10 poin; status lainnya bernilai 0.
        $targetReportPoints =
            $validated['verification_status'] === 'valid' ? 10 : 0;

        // Hitung selisih antara saldo seharusnya dan saldo saat ini.
        $pointAdjustment = $targetReportPoints - $reportPoints;

        // Jika poin perlu ditambahkan.
        if ($pointAdjustment > 0) {
            UserPoint::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'points' => $pointAdjustment,
                'type' => 'tambah',
                'description' => 'Poin dari laporan yang telah diverifikasi valid.',
            ]);

            // Notifikasi kepada Masyarakat saat mendapatkan poin.
            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'title' => 'Poin Bertambah!',
                'message' => "Kamu mendapatkan {$pointAdjustment} poin karena laporan \"{$report->title}\" telah diverifikasi valid.",
                'is_read' => false,
            ]);
        }

        // Jika poin perlu dikurangi atau disesuaikan.
        elseif ($pointAdjustment < 0) {
            if ($validated['verification_status'] === 'hoax') {
                $description =
                    'Pengurangan poin karena laporan ditetapkan sebagai hoaks.';
            } elseif ($validated['verification_status'] === 'valid') {
                $description =
                    'Penyesuaian saldo poin laporan menjadi 10 poin.';
            } else {
                $description =
                    'Poin ditarik karena laporan tidak lagi berstatus valid.';
            }

            UserPoint::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'points' => abs($pointAdjustment),
                'type' => 'kurang',
                'description' => $description,
            ]);
        }

        // Penalti tambahan untuk laporan yang baru ditetapkan sebagai hoaks.
        $penaltyDescription = 'Penalti poin karena laporan terbukti hoaks.';
        $penaltyReversalDescription =
            'Pengembalian penalti karena status laporan tidak lagi hoaks.';

        // Hitung saldo penalti khusus untuk laporan ini.
        $penaltyBalance = (int) UserPoint::where('report_id', $report->id)
            ->where('user_id', $report->user_id)
            ->whereIn('description', [
                $penaltyDescription,
                $penaltyReversalDescription,
            ])
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
                ) as balance
            ")
            ->value('balance');

        // Jika baru berubah menjadi hoax, terapkan penalti maksimal 10 poin.
        if (
            $validated['verification_status'] === 'hoax'
            && $previousVerificationStatus !== 'hoax'
            && $penaltyBalance >= 0
        ) {
            $currentTotalPoints = (int) UserPoint::where(
                'user_id',
                $report->user_id
            )
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

            // Poin tidak boleh menjadi negatif akibat penalti ini.
            $penaltyPoints = min(10, max(0, $currentTotalPoints));

            if ($penaltyPoints > 0) {
                UserPoint::create([
                    'user_id' => $report->user_id,
                    'report_id' => $report->id,
                    'points' => $penaltyPoints,
                    'type' => 'kurang',
                    'description' => $penaltyDescription,
                ]);
            }
        }

        // Jika status hoax dibatalkan, kembalikan penalti yang pernah diterapkan.
        elseif (
            $previousVerificationStatus === 'hoax'
            && $validated['verification_status'] !== 'hoax'
            && $penaltyBalance < 0
        ) {
            UserPoint::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'points' => abs($penaltyBalance),
                'type' => 'tambah',
                'description' => $penaltyReversalDescription,
            ]);
        }

        // Evaluasi badge setelah saldo poin dan penalti diperbarui.
        $badgeAwardService->checkAndAward($report->user_id);

        // Notifikasi kepada Masyarakat jika status verifikasi berubah.
        if ($previousVerificationStatus !== $validated['verification_status']) {
            $verificationMessages = [
                'pending' => [
                    'Verifikasi Laporan Diperbarui',
                    "Laporan \"{$report->title}\" sedang menunggu verifikasi Admin.",
                ],
                'valid' => [
                    'Laporan Terverifikasi',
                    "Laporan \"{$report->title}\" telah diverifikasi dan dinyatakan valid oleh Admin.",
                ],
                'hoax' => [
                    'Laporan Tidak Valid',
                    "Laporan \"{$report->title}\" dinyatakan tidak valid (hoaks) oleh Admin.",
                ],
            ];

            [$title, $message] = $verificationMessages[
                $validated['verification_status']
            ];

            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }

        // Notifikasi kepada Masyarakat jika status laporan berubah.
        if ($previousStatus !== $validated['status']) {
            $statusLabels = [
                'menunggu' => 'menunggu',
                'diproses' => 'sedang diproses',
                'selesai' => 'selesai',
                'ditolak' => 'ditolak',
            ];

            $oldStatusLabel = $statusLabels[$previousStatus] ?? $previousStatus;
            $newStatusLabel = $statusLabels[$validated['status']]
                ?? $validated['status'];

            Notification::create([
                'user_id' => $report->user_id,
                'report_id' => $report->id,
                'title' => 'Status Laporan Diperbarui',
                'message' => "Status laporan \"{$report->title}\" berubah dari {$oldStatusLabel} menjadi {$newStatusLabel}.",
                'is_read' => false,
            ]);
        }

        return redirect()
            ->route('admin.reports.show', $report->id)
            ->with('success', 'Laporan berhasil diperbarui.');

    }


    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        $report->delete();

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}