<?php

namespace App\Exports;

use App\Models\Report;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        return Report::query()
            ->with(['user', 'category', 'district', 'village'])
            ->when($this->filters['tanggal_mulai'] ?? null, function ($query, $tanggal) {
                $query->whereDate('created_at', '>=', $tanggal);
            })
            ->when($this->filters['tanggal_selesai'] ?? null, function ($query, $tanggal) {
                $query->whereDate('created_at', '<=', $tanggal);
            })
            ->when($this->filters['kategori_id'] ?? null, function ($query, $kategori) {
                $query->where('category_id', $kategori);
            })
            ->when($this->filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul Laporan',
            'Deskripsi',
            'Kategori',
            'Pelapor',
            'Kecamatan',
            'Kelurahan',
            'Alamat',
            'Tanggal Laporan',
            'Status',
            'Status Verifikasi',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->title,
            $report->description,
            $report->category?->name ?? '-',
            $report->user?->name ?? '-',
            $report->district?->name ?? '-',
            $report->village?->name ?? '-',
            $report->address ?? '-',
            $report->created_at?->format('d-m-Y H:i') ?? '-',
            $report->status ?? '-',
            $report->verification_status ?? '-',
        ];
    }
}