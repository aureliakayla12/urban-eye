<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $beji = District::where('name', 'Beji')->first();
        $cimanggis = District::where('name', 'Cimanggis')->first();
        $pancoranMas = District::where('name', 'Pancoran Mas')->first();
        $sawangan = District::where('name', 'Sawangan')->first();
        $sukmajaya = District::where('name', 'Sukmajaya')->first();

        Village::create([
            'district_id' => $beji->id,
            'name' => 'Beji',
        ]);

        Village::create([
            'district_id' => $beji->id,
            'name' => 'Beji Timur',
        ]);

        Village::create([
            'district_id' => $beji->id,
            'name' => 'Kemiri Muka',
        ]);

        Village::create([
            'district_id' => $cimanggis->id,
            'name' => 'Cisalak Pasar',
        ]);

        Village::create([
            'district_id' => $cimanggis->id,
            'name' => 'Curug',
        ]);

        Village::create([
            'district_id' => $cimanggis->id,
            'name' => 'Tugu',
        ]);

        Village::create([
            'district_id' => $pancoranMas->id,
            'name' => 'Depok',
        ]);

        Village::create([
            'district_id' => $pancoranMas->id,
            'name' => 'Depok Jaya',
        ]);

        Village::create([
            'district_id' => $pancoranMas->id,
            'name' => 'Pancoran Mas',
        ]);

        Village::create([
            'district_id' => $sawangan->id,
            'name' => 'Sawangan',
        ]);

        Village::create([
            'district_id' => $sawangan->id,
            'name' => 'Sawangan Baru',
        ]);

        Village::create([
            'district_id' => $sawangan->id,
            'name' => 'Cinangka',
        ]);

        Village::create([
            'district_id' => $sukmajaya->id,
            'name' => 'Abadijaya',
        ]);

        Village::create([
            'district_id' => $sukmajaya->id,
            'name' => 'Bakti Jaya',
        ]);

        Village::create([
            'district_id' => $sukmajaya->id,
            'name' => 'Mekarjaya',
        ]);
    }
}