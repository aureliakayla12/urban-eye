<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        Reward::create([
            'name' => 'Voucher Pulsa',
            'reward_type' => 'pulsa',
            'description' => 'Voucher pulsa sebagai apresiasi bagi masyarakat yang berpartisipasi menjaga kebersihan dan lingkungan sekitar.',
            'point_cost' => 100,
            'stock' => 20,
            'image' => null,
            'status' => 1,
        ]);

        Reward::create([
            'name' => 'Bibit Tanaman',
            'reward_type' => 'bibit',
            'description' => 'Bibit tanaman untuk mendukung penghijauan dan mengajak masyarakat ikut menjaga kelestarian lingkungan.',
            'point_cost' => 250,
            'stock' => 15,
            'image' => null,
            'status' => 1,
        ]);

        Reward::create([
            'name' => 'Tumbler UrbanEye',
            'reward_type' => 'tumbler',
            'description' => 'Tumbler eksklusif UrbanEye sebagai apresiasi bagi masyarakat yang aktif berpartisipasi menjaga kebersihan dan kelestarian lingkungan.',
            'point_cost' => 500,
            'stock' => 10,
            'image' => null,
            'status' => 1,
        ]);

        Reward::create([
            'name' => 'Totebag UrbanEye',
            'reward_type' => 'totebag',
            'description' => 'Totebag eksklusif UrbanEye untuk mendukung kebiasaan mengurangi penggunaan kantong plastik dan menjaga lingkungan tetap bersih.',
            'point_cost' => 1000,
            'stock' => 5,
            'image' => null,
            'status' => 1,
        ]);
    }
}