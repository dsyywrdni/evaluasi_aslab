<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bersihkan dulu jika tabel sempat tersangkut setengah jalan
        Schema::dropIfExists('aslab_semester');

        // 2. Buat tabel penghubung
        Schema::create('aslab_semester', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('semester_id')->index();
            $table->unsignedBigInteger('aslab_id')->index();
            $table->enum('status', ['aktif', 'cuti', 'alumni'])->default('aktif');
            $table->timestamps();

            // Kunci unik agar 1 aslab tidak dobel di 1 semester
            $table->unique(['semester_id', 'aslab_id']);

            // Relasi aman
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('aslab_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aslab_semester');
    }
};