<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        District::create([
            'name' => 'Beji',
        ]);

        District::create([
            'name' => 'Cimanggis',
        ]);

        District::create([
            'name' => 'Pancoran Mas',
        ]);

        District::create([
            'name' => 'Sawangan',
        ]);

        District::create([
            'name' => 'Sukmajaya',
        ]);
    }
}