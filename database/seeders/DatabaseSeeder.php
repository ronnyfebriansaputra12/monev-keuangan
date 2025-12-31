<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Contoh User PLO
        \App\Models\User::updateOrCreate([
            'name' => 'Petugas DMS',
            'email' => 'plo.dms@example.com',
            'password' => bcrypt('password'),
            'role' => 'PLO',
            'plo_code' => 'DMS',
        ]);

        // Contoh User Verifikator
        \App\Models\User::updateOrCreate([
            'name' => 'Verifikator Utama',
            'email' => 'verifikator@example.com',
            'password' => bcrypt('password'),
            'role' => 'Verifikator',
            'plo_code' => null, // Verifikator tidak butuh plo_code
        ]);

        \App\Models\User::updateOrCreate([
            'name' => 'Bendahara Utama',
            'email' => 'bendahara@example.com',
            'password' => bcrypt('password'),
            'role' => 'Bendahara',
            'plo_code' => null, // Bendahara tidak butuh plo_code
        ]);
        \App\Models\User::updateOrCreate([
            'name' => 'PPK',
            'email' => 'ppk@example.com',
            'password' => bcrypt('password'),
            'role' => 'PPK',
            'plo_code' => null, // Bendahara tidak butuh plo_code
        ]);
        \App\Models\User::updateOrCreate([
            'name' => 'PPSPM',
            'email' => 'ppspm@example.com',
            'password' => bcrypt('password'),
            'role' => 'PPSPM',
            'plo_code' => null, // Bendahara tidak butuh plo_code
        ]);
        \App\Models\User::updateOrCreate([
            'name' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'Superadmin',
            'plo_code' => null, // Bendahara tidak butuh plo_code
        ]);
    }
}
