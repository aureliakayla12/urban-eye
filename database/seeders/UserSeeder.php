<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@urbaneye.com'],
            [
                'name' => 'Admin UrbanEye',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('admin');

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@urbaneye.com'],
            [
                'name' => 'Petugas UrbanEye',
                'password' => Hash::make('password'),
            ]
        );

        $petugas->assignRole('petugas');

        $masyarakat = User::firstOrCreate(
            ['email' => 'masyarakat@urbaneye.com'],
            [
                'name' => 'Masyarakat UrbanEye',
                'password' => Hash::make('password'),
            ]
        );

        $masyarakat->assignRole('masyarakat');
    }
}