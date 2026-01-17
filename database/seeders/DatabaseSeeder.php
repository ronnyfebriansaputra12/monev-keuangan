<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- DATA USER PLO (Sesuai Inisial) ---
        $ploUsers = [
            [
                'name'     => 'Dimas',
                'email'    => 'dimas@example.com',
                'plo_code' => 'D',
            ],
            [
                'name'     => 'Yanda',
                'email'    => 'yanda@example.com',
                'plo_code' => 'Y',
            ],
            [
                'name'     => 'Ghea',
                'email'    => 'ghea@example.com',
                'plo_code' => 'G',
            ],
            [
                'name'     => 'Komar',
                'email'    => 'komar@example.com',
                'plo_code' => 'K',
            ],
            [
                'name'     => 'Lusi',
                'email'    => 'lusi@example.com',
                'plo_code' => 'L',
            ],
        ];

        foreach ($ploUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // Cek berdasarkan email
                [
                    'name'     => $user['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'PLO',
                    'plo_code' => $user['plo_code'],
                ]
            );
        }

        // --- DATA USER ROLE LAIN ---
        $otherUsers = [
            [
                'name'  => 'Verifikator Utama',
                'email' => 'verifikator@example.com',
                'role'  => 'Verifikator',
            ],
            [
                'name'  => 'Bendahara Utama',
                'email' => 'bendahara@example.com',
                'role'  => 'Bendahara',
            ],
            [
                'name'  => 'PPK Utama',
                'email' => 'ppk@example.com',
                'role'  => 'PPK',
            ],
            [
                'name'  => 'PPSPM Utama',
                'email' => 'ppspm@example.com',
                'role'  => 'PPSPM',
            ],
            [
                'name'  => 'Superadmin Utama',
                'email' => 'superadmin@example.com',
                'role'  => 'Superadmin',
            ],
                        [
                'name'  => 'PPBJ',
                'email' => 'ppbj@example.com',
                'role'  => 'PPBJ',
            ],
        ];

        foreach ($otherUsers as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'password' => Hash::make('password'),
                    'role'     => $user['role'],
                    'plo_code' => null,
                ]
            );
        }
    }
}