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
        Schema::create('evaluasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade'); // Admin atau Kalab
            $table->foreignId('aslab_id')->constrained('users')->onDelete('cascade'); // Aslab yang dinilai
            $table->foreignId('sub_kriteria_id')->constrained('sub_kriterias')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->integer('nilai_aktual'); // Nilai ril yang diinput penilai (misal: 1 s/d 5)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};
