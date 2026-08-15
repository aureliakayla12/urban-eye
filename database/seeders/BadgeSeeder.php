<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        Badge::create([
            'name' => 'Pelapor Baru',
            'description' => 'Diperoleh setelah membuat laporan pertama.',
            'required_points' => 10,
            'required_reports' => 1,
            'icon' => 'badge-new',
        ]);

        Badge::create([
            'name' => 'Pelapor Aktif',
            'description' => 'Diperoleh setelah aktif membuat laporan permasalahan lingkungan.',
            'required_points' => 50,
            'required_reports' => 5,
            'icon' => 'badge-active',
        ]);

        Badge::create([
            'name' => 'Peduli Lingkungan',
            'description' => 'Diperoleh setelah memberikan kontribusi melalui beberapa laporan.',
            'required_points' => 100,
            'required_reports' => 10,
            'icon' => 'badge-environment',
        ]);

        Badge::create([
            'name' => 'Pengawas Kota',
            'description' => 'Diperoleh setelah aktif membantu melaporkan berbagai permasalahan lingkungan.',
            'required_points' => 250,
            'required_reports' => 25,
            'icon' => 'badge-city',
        ]);
    }
}