<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubrik_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_kriteria_id')
                  ->constrained('sub_kriterias')
                  ->cascadeOnDelete();
            $table->tinyInteger('skor')->unsigned(); // 1–5
            $table->text('deskripsi');
            $table->timestamps();

            // Setiap sub-kriteria hanya punya 1 baris per skor
            $table->unique(['sub_kriteria_id', 'skor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubrik_penilaians');
    }
};
