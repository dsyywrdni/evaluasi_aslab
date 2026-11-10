<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Role Admin
        User::firstOrCreate(
            ['email' => 'admin@wicida.ac.id'],
            [
                'name' => 'Administrator SPK',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Role Kalab (Kepala Laboratorium)
        User::firstOrCreate(
            ['email' => 'kalab@wicida.ac.id'],
            [
                'name' => 'Kepala Lab STMIK',
                'password' => Hash::make('password123'),
                'role' => 'kalab',
            ]
        );

        // Data Asisten Laboratorium (Alternatif yang dievaluasi)
        User::firstOrCreate(
            ['email' => 'aslab1@wicida.ac.id'],
            [
                'name' => 'Desy Ayu',
                'password' => Hash::make('password123'),
                'nim' => '2343063',
                'kode_asisten' => 'DS',
                'role' => 'aslab',
                'status_aslab' => 'aktif',
            ]
        );

        User::firstOrCreate(
            ['email' => 'aslab2@wicida.ac.id'],
            [
                'name' => 'Nadia Sandy',
                'password' => Hash::make('password123'),
                'nim' => '2343078',
                'kode_asisten' => 'NS',
                'role' => 'aslab',
                'status_aslab' => 'aktif',
            ]
        );

        // Pastikan seluruh aslab terdaftar secara otomatis ke semester aktif
        Semester::syncAllAslabsToActive();
    }
}
