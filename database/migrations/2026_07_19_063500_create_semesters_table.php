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
        Schema::create('sub_kriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriterias')->onDelete('cascade');
            $table->string('nama_sub');
            $table->integer('nilai_target'); // Nilai target standar (misal: nilai 1 s/d 5)
            $table->enum('jenis', ['core', 'secondary']); // Jenis faktor pembagi dalam Profile Matching
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
