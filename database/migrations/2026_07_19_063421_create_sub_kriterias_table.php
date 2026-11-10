<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->nullable()
                ->constrained('semesters')->cascadeOnDelete()->after('id');
            $table->string('nama_kriteria');
            $table->integer('persentase_kriteria'); // Persentase bobot kriteria (misal: 60% dan 40%)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kriterias');
    }
};
