<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Export/Index', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function download(Request $request, string $format)
    {
        abort_unless(in_array($format, ['excel', 'pdf'], true), 404);

        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'kategori_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => [
                'nullable',
                'in:menunggu,diproses,selesai,ditolak',
            ],
        ]);

        $query = Report::query()
            ->with(['user', 'category', 'district', 'village'])
            ->when($filters['tanggal_mulai'] ?? null, function ($query, $tanggal) {
                $query->whereDate('created_at', '>=', $tanggal);
            })
            ->when($filters['tanggal_selesai'] ?? null, function ($query, $tanggal) {
                $query->whereDate('created_at', '<=', $tanggal);
            })
            ->when($filters['kategori_id'] ?? null, function ($query, $kategori) {
                $query->where('category_id', $kategori);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest();

        $filename = 'laporan-urban-eye-' . now()->format('Y-m-d-His');

        if ($format === 'excel') {
            return Excel::download(
                new ReportsExport($filters),
                $filename . '.xlsx'
            );
        }

        $reports = $query->get();

        $pdf = Pdf::loadView('exports.reports', [
            'reports' => $reports,
            'filters' => $filters,
            'tanggalCetak' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }
}