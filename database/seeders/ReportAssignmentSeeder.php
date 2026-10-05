<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportAssignment;

class ReportAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        ReportAssignment::create([
            'report_id' => 1,
            'officer_id' => 4,
            'assigned_by' => 1,
            'assigned_at' => now()->subHours(3),
            'accepted_at' => null,
            'finished_at' => null,
            'status' => 'ditugaskan',
            'proof_photo' => null,
            'note' => null,
        ]);

        ReportAssignment::create([
            'report_id' => 2,
            'officer_id' => 4,
            'assigned_by' => 1,
            'assigned_at' => now()->subHours(4),
            'accepted_at' => now()->subHours(3),
            'finished_at' => null,
            'status' => 'diproses',
            'proof_photo' => null,
            'note' => 'Sedang dalam proses penanganan.',
        ]);

        ReportAssignment::create([
            'report_id' => 3,
            'officer_id' => 4,
            'assigned_by' => 1,
            'assigned_at' => now()->subDay(),
            'accepted_at' => now()->subHours(8),
            'finished_at' => now()->subHours(2),
            'status' => 'selesai',
            'proof_photo' => null,
            'note' => 'Tugas telah selesai ditangani.',
        ]);
    }
}
