<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        Report::create([
            'user_id' => 3,
            'category_id' => null,
            'district_id' => null,
            'village_id' => 1,
            'title' => 'Tumpukan sampah di pinggir jalan',
            'description' => 'Terdapat tumpukan sampah di pinggir jalan yang mengganggu kebersihan dan kenyamanan masyarakat.',
            'photo' => 'reports/default.jpg',
            'latitude' => -6.20000000,
            'longitude' => 106.81666600,
            'address' => 'Jl. Margonda Raya, Depok',
            'ai_category' => null,
            'ai_confidence' => null,
            'ai_response' => null,
            'classified_at' => null,
            'verification_status' => 'pending',
            'status' => 'menunggu',
        ]);

        Report::create([
            'user_id' => 3,
            'category_id' => null,
            'district_id' => null,
            'village_id' => 2,
            'title' => 'Jalan Berlubang',
            'description' => 'Jalan berlubang di area perumahan yang membahayakan pengendara dan pejalan kaki.',
            'photo' => 'reports/default.jpg',
            'latitude' => -6.21000000,
            'longitude' => 106.82000000,
            'address' => 'Jl. Raya Bogor, Depok',
            'ai_category' => null,
            'ai_confidence' => null,
            'ai_response' => null,
            'classified_at' => null,
            'verification_status' => 'pending',
            'status' => 'menunggu',
        ]);

        Report::create([
            'user_id' => 3,
            'category_id' => null,
            'district_id' => null,
            'village_id' => 3,
            'title' => 'Saluran Drainase Tersumbat',
            'description' => 'Saluran drainase di area pemukiman tersumbat sehingga menyebabkan genangan air.',
            'photo' => 'reports/default.jpg',
            'latitude' => -6.22000000,
            'longitude' => 106.82500000,
            'address' => 'Jl. Melati',
            'ai_category' => null,
            'ai_confidence' => null,
            'ai_response' => null,
            'classified_at' => null,
            'verification_status' => 'pending',
            'status' => 'menunggu',
        ]);
    }
}
