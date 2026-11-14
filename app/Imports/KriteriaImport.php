<?php

namespace App\Imports;

use App\Models\Kriteria;
use App\Models\RubrikPenilaian;
use App\Models\Semester;
use App\Models\SubKriteria;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KriteriaImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|null
    {
        // 1. Ambil semester aktif
        $sem = Semester::currentEditable();

        if (!$sem) {
            return null;
        }

        $namaKriteria = trim($row['nama_kriteria'] ?? '');
        $namaSub      = trim($row['nama_sub'] ?? '');

        if (empty($namaKriteria) || empty($namaSub)) {
            return null;
        }

        // 2. Kriteria – update jika sudah ada, buat jika belum
        $kriteria = Kriteria::where('semester_id', $sem->id)
            ->where('nama_kriteria', $namaKriteria)
            ->first();

        if ($kriteria) {
            $kriteria->update([
                'persentase_kriteria' => $row['persentase_kriteria'] ?? $kriteria->persentase_kriteria,
                'bobot_cf'            => $row['bobot_cf'] ?? $kriteria->bobot_cf,
                'bobot_sf'            => $row['bobot_sf'] ?? $kriteria->bobot_sf,
            ]);
        } else {
            $kriteria = Kriteria::create([
                'semester_id'         => $sem->id,
                'nama_kriteria'       => $namaKriteria,
                'persentase_kriteria' => $row['persentase_kriteria'] ?? 0,
                'bobot_cf'            => $row['bobot_cf'] ?? 60,
                'bobot_sf'            => $row['bobot_sf'] ?? 40,
            ]);
        }

        // 3. Sub-Kriteria – update jika sudah ada, buat jika belum
        $jenisRaw = strtolower(trim($row['jenis'] ?? ''));
        $jenis    = ($jenisRaw === 'core factor' || $jenisRaw === 'core') ? 'core' : 'secondary';

        $sub = SubKriteria::where('kriteria_id', $kriteria->id)
            ->where('nama_sub', $namaSub)
            ->first();

        if ($sub) {
            $sub->update([
                'nilai_target' => $row['nilai_target'] ?? $sub->nilai_target,
                'jenis'        => $jenis,
            ]);
        } else {
            $sub = SubKriteria::create([
                'kriteria_id'  => $kriteria->id,
                'nama_sub'     => $namaSub,
                'nilai_target' => $row['nilai_target'] ?? 5,
                'jenis'        => $jenis,
            ]);
        }

        // 4. Rubrik Penilaian – updateOrCreate agar tidak melanggar unique(sub_kriteria_id, skor)
        for ($skor = 1; $skor <= 5; $skor++) {
            $kolomKey = 'deskripsi_' . $skor;

            if (isset($row[$kolomKey]) && !empty(trim($row[$kolomKey]))) {
                RubrikPenilaian::updateOrCreate(
                    [
                        'sub_kriteria_id' => $sub->id,
                        'skor'            => $skor,
                    ],
                    [
                        'deskripsi' => trim($row[$kolomKey]),
                    ]
                );
            }
        }

        return null;
    }
}
