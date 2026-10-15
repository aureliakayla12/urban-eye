<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Category;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Badge;
use App\Models\UserBadge;
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

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        // Assign petugas
        if ($request->filled('officer_id')) {
            if ($report->verification_status !== 'valid') {
                return back()->withErrors([
                    'officer_id' => 'Laporan harus diverifikasi valid sebelum ditugaskan kepada petugas.',
                ]);
            }

            $officer = User::role('petugas')
                ->where('id', $request->officer_id)
                ->first();

            if (!$officer) {
                return back()->withErrors([
                    'officer_id' => 'Petugas yang dipilih tidak valid.',
                ]);
            }

            $assignment = $report->assignments()->latest()->first();

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

        $report->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'] ?? null,
            'address' => $validated['address'],
            'status' => $validated['status'],
            'verification_status' => $validated['verification_status'],
        ]);

       
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

        // Periksa badge setelah laporan valid dan saldo poinnya disesuaikan.
        if ($validated['verification_status'] === 'valid') {
            $this->checkAndAwardBadges($report->user_id);
        }

        return redirect()
            ->route('admin.reports.show', $report->id)
            ->with('success', 'Laporan berhasil diperbarui.');

    }

    /**
     * Mengecek seluruh badge dan memberikan badge
     * yang syaratnya sudah terpenuhi oleh user.
     */
    private function checkAndAwardBadges($userId)
    {
        // Hitung jumlah laporan valid milik user
        $validReportsCount = Report::where('user_id', $userId)
            ->where('verification_status', 'valid')
            ->count();

        // Hitung total poin berdasarkan ledger user_points
        $totalPoints = UserPoint::where('user_id', $userId)
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

        // Ambil semua badge yang tersedia
        $badges = Badge::all();

        foreach ($badges as $badge) {
            $reportsRequirementMet =
                $badge->required_reports <= 0
                || $validReportsCount >= $badge->required_reports;

            $pointsRequirementMet =
                $badge->required_points <= 0
                || $totalPoints >= $badge->required_points;

            // Jika semua syarat terpenuhi
            if ($reportsRequirementMet && $pointsRequirementMet) {
                // Jangan berikan badge yang sama dua kali
                UserBadge::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'badge_id' => $badge->id,
                    ],
                    [
                        'earned_at' => now(),
                    ]
                );
            }
        }
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