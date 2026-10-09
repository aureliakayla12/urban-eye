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

        // Simpan status verifikasi sebelum diubah
        $previousVerificationStatus = $report->verification_status;

        $report->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'] ?? null,
            'address' => $validated['address'],
            'status' => $validated['status'],
            'verification_status' => $validated['verification_status'],
        ]);

        // Jika laporan baru berubah menjadi valid
        if (
            $validated['verification_status'] === 'valid'
            && $previousVerificationStatus !== 'valid'
        ) {
            // Berikan 10 poin hanya satu kali untuk laporan tersebut
            $alreadyReceivedPoints = UserPoint::where('report_id', $report->id)
                ->where('type', 'tambah')
                ->exists();

            if (!$alreadyReceivedPoints) {
                UserPoint::create([
                    'user_id' => $report->user_id,
                    'report_id' => $report->id,
                    'points' => 10,
                    'type' => 'tambah',
                    'description' => 'Poin dari laporan yang telah diverifikasi valid.',
                ]);
            }

            // Cek dan berikan badge yang sudah memenuhi syarat
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