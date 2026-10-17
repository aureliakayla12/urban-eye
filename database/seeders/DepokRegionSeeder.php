<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\District;
use App\Models\Village;

class DepokRegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'Beji' => [
                'Beji',
                'Beji Timur',
                'Kemiri Muka',
                'Kukusan',
                'Pondok Cina',
                'Tanah Baru',
            ],

            'Bojongsari' => [
                'Bojongsari',
                'Bojongsari Baru',
                'Curug',
                'Duren Mekar',
                'Duren Seribu',
                'Pondok Petir',
                'Serua',
            ],

            'Cilodong' => [
                'Cilodong',
                'Jatimulya',
                'Kalibaru',
                'Kalimulya',
                'Sukamaju',
            ],

            'Cimanggis' => [
                'Cisalak Pasar',
                'Curug',
                'Harjamukti',
                'Mekarsari',
                'Pasir Gunung Selatan',
                'Tugu',
            ],

            'Cinere' => [
                'Cinere',
                'Gandul',
                'Pangkalan Jati',
                'Pangkalan Jati Baru',
            ],

            'Cipayung' => [
                'Bojong Pondok Terong',
                'Cipayung',
                'Cipayung Jaya',
                'Pondok Jaya',
                'Ratujaya',
            ],

            'Limo' => [
                'Grogol',
                'Krukut',
                'Limo',
                'Meruyung',
            ],

            'Pancoran Mas' => [
                'Depok',
                'Depok Jaya',
                'Mampang',
                'Pancoran Mas',
                'Rangkapan Jaya',
                'Rangkapan Jaya Baru',
            ],

            'Sawangan' => [
                'Bedahan',
                'Cinangka',
                'Kedaung',
                'Pasir Putih',
                'Pengasinan',
                'Sawangan',
                'Sawangan Baru',
            ],

            'Sukmajaya' => [
                'Abadijaya',
                'Baktijaya',
                'Cisalak',
                'Mekarjaya',
                'Sukmajaya',
                'Tirtajaya',
            ],

            'Tapos' => [
                'Cilangkap',
                'Cimpaeun',
                'Jatijajar',
                'Leuwinanggung',
                'Sukamaju Baru',
                'Sukatani',
                'Tapos',
            ],
        ];

        DB::transaction(function () use ($regions) {
            foreach ($regions as $districtName => $villageNames) {
                $district = District::firstOrCreate([
                    'name' => $districtName,
                ]);

                foreach ($villageNames as $villageName) {
                    Village::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $villageName,
                    ]);
                }
            }
        });
    }
}