<?php

namespace Database\Seeders;

use App\Models\Semester;
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
        $semester = Semester::firstOrCreate(
            ['nama_semester' => 'Genap 2025/2026'],
            ['status' => 'aktif']
        );

        $this->call([
            UserSeeder::class,
        ]);

        // Pastikan seluruh asisten terdaftar otomatis ke semester aktif
        Semester::syncAllAslabsToActive();
    }
}
