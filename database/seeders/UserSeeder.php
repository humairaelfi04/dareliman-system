<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sdit2.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepsek@sdit2.com',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah',
        ]);

        User::create([
            'name' => 'Bendahara Aset',
            'email' => 'bendahara@sdit2.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
        ]);
    }
}
