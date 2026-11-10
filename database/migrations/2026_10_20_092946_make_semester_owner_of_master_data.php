<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kolom yang dipakai kode tetapi tidak ada di migration awal
        if (!Schema::hasColumn('users', 'status_aslab')) {
            Schema::table('users', function (Blueprint $t) {
                $t->enum('status_aslab', ['aktif', 'cuti', 'alumni'])->default('aktif')->after('role');
            });
        }

        if (!Schema::hasColumn('kriterias', 'bobot_cf')) {
            Schema::table('kriterias', function (Blueprint $t) {
                $t->unsignedTinyInteger('bobot_cf')->default(60)->after('persentase_kriteria');
                $t->unsignedTinyInteger('bobot_sf')->default(40)->after('bobot_cf');
            });
        }

        if (!Schema::hasColumn('kriterias', 'semester_id')) {
            Schema::table('kriterias', function (Blueprint $t) {
                $t->foreignId('semester_id')->nullable()->after('id')
                  ->constrained('semesters')->cascadeOnDelete();
            });
        }

        // 2. Kloning kriteria global menjadi kriteria per semester + petakan ulang evaluasi
        $semesters = DB::table('semesters')->orderBy('id')->get();
        $templates = DB::table('kriterias')->whereNull('semester_id')->get();

        if ($semesters->isNotEmpty() && $templates->isNotEmpty()) {
            foreach ($semesters as $sem) {
                $map = [];

                foreach ($templates as $k) {
                    $newK = DB::table('kriterias')->insertGetId([
                        'semester_id'         => $sem->id,
                        'nama_kriteria'       => $k->nama_kriteria,
                        'persentase_kriteria' => $k->persentase_kriteria,
                        'bobot_cf'            => $k->bobot_cf ?? 60,
                        'bobot_sf'            => $k->bobot_sf ?? 40,
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);

                    foreach (DB::table('sub_kriterias')->where('kriteria_id', $k->id)->get() as $s) {
                        $map[$s->id] = DB::table('sub_kriterias')->insertGetId([
                            'kriteria_id'  => $newK,
                            'nama_sub'     => $s->nama_sub,
                            'nilai_target' => $s->nilai_target,
                            'jenis'        => $s->jenis,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    }
                }

                foreach ($map as $old => $new) {
                    DB::table('evaluasis')
                        ->where('semester_id', $sem->id)
                        ->where('sub_kriteria_id', $old)
                        ->update(['sub_kriteria_id' => $new]);
                }
            }

            // Kriteria global lama dihapus (sub-kriteria lama ikut terhapus lewat cascade)
            DB::table('kriterias')->whereNull('semester_id')->delete();
        }

        // 3. Isi tabel pivot aslab_semester
        $aslabs = DB::table('users')->where('role', 'aslab')->get();

        foreach ($semesters as $sem) {
            $evaluated = DB::table('evaluasis')->where('semester_id', $sem->id)
                ->pluck('aslab_id')->unique()->flip();

            foreach ($aslabs as $a) {
                $punyaNilai = $evaluated->has($a->id);
                if (!$punyaNilai && $sem->status !== 'aktif') continue; // semester lama: hanya yang punya nilai

                DB::table('aslab_semester')->insertOrIgnore([
                    'semester_id' => $sem->id,
                    'aslab_id'    => $a->id,
                    'status'      => $sem->status === 'aktif' ? ($a->status_aslab ?? 'aktif') : 'aktif',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // 4. Satu nilai per sub-kriteria per aslab per semester (hapus duplikat lama, simpan yang terbaru)
        DB::statement('DELETE e1 FROM evaluasis e1
            JOIN evaluasis e2
              ON e1.aslab_id = e2.aslab_id
             AND e1.sub_kriteria_id = e2.sub_kriteria_id
             AND e1.semester_id = e2.semester_id
             AND e1.id < e2.id');

        Schema::table('evaluasis', function (Blueprint $t) {
            $t->unique(['aslab_id', 'sub_kriteria_id', 'semester_id'], 'evaluasi_unik');
        });
    }

    public function down(): void {}
};