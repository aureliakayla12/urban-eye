<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(
            ['name' => 'Sampah'],
            ['description' => 'Permasalahan sampah di lingkungan.']
        );

        Category::firstOrCreate(
            ['name' => 'Jalan Rusak'],
            ['description' => 'Kerusakan jalan yang mengganggu masyarakat.']
        );

        Category::firstOrCreate(
            ['name' => 'Drainase'],
            ['description' => 'Permasalahan saluran drainase atau genangan air.']
        );

        Category::firstOrCreate(
            ['name' => 'Fasilitas Umum'],
            ['description' => 'Kerusakan atau masalah pada fasilitas umum.']
        );

        Category::firstOrCreate(
            ['name' => 'Lampu Jalan'],
            ['description' => 'Permasalahan lampu penerangan jalan.']
        );
    }
}