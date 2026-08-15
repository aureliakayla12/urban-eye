<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        Reward::create([
            'name' => 'Voucher Belanja',
            'reward_type' => 'voucher',
            'description' => 'Voucher belanja sebagai apresiasi untuk masyarakat yang aktif berkontribusi.',
            'point_cost' => 100,
            'stock' => 20,
            'image' => null,
            'status' => 1,
        ]);

        Reward::create([
            'name' => 'Pulsa',
            'reward_type' => 'pulsa',
            'description' => 'Pulsa sebagai apresiasi untuk masyarakat yang telah mengumpulkan poin.',
            'point_cost' => 250,
            'stock' => 15,
            'image' => null,
            'status' => 1,
        ]);

        Reward::create([
            'name' => 'Bibit Tanaman',
            'reward_type' => 'bibit',
            'description' => 'Bibit tanaman untuk mendukung kepedulian masyarakat terhadap lingkungan.',
            'point_cost' => 500,
            'stock' => 10,
            'image' => null,
            'status' => 1,
        ]);

        Reward::create([
            'name' => 'Paket Peduli Lingkungan',
            'reward_type' => 'lainnya',
            'description' => 'Paket hadiah untuk masyarakat dengan kontribusi tinggi terhadap lingkungan.',
            'point_cost' => 1000,
            'stock' => 5,
            'image' => null,
            'status' => 1,
        ]);
    }
}