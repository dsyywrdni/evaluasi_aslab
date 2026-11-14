<?php

namespace App\Imports;

use App\Models\Evaluasi;
use App\Models\Semester;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Model;

class EvaluasiImport implements ToModel, WithHeadingRow
{
    protected int $evaluatorId;
    protected int $semesterId;

    /** Cache sub-kriteria agar tidak query per baris */
    protected ?array $subKriteriaMap = null;

    public function __construct(int $evaluatorId, int $semesterId)
    {
        $this->evaluatorId = $evaluatorId;
        $this->semesterId  = $semesterId;
    }

    /**
     * Bangun map: nama_sub (dinormalisasi sama seperti WithHeadingRow) => SubKriteria
     * WithHeadingRow mengubah spasi → underscore dan lowercase.
     * Hanya ambil sub-kriteria yang ada di semester ini.
     */
    protected function getSubKriteriaMap(): array
    {
        if ($this->subKriteriaMap === null) {
            $this->subKriteriaMap = SubKriteria::whereHas('kriteria', function ($q) {
                $q->where('semester_id', $this->semesterId);
            })->get()->keyBy(function ($s) {
                // Normalisasi sama persis dengan WithHeadingRow:
                // lowercase + trim + spasi → underscore + hapus karakter non-alfanumerik kecuali underscore
                $normalized = strtolower(trim($s->nama_sub));
                $normalized = preg_replace('/\s+/', '_', $normalized);
                $normalized = preg_replace('/[^a-z0-9_]/', '', $normalized);
                return $normalized;
            })->toArray();
        }
        return $this->subKriteriaMap;
    }

    public function model(array $row): Model|null
    {
        $kodeAsisten = trim($row['kode_asisten'] ?? '');

        if (empty($kodeAsisten)) {
            return null;
        }

        // Cari user berdasarkan kode_asisten
        $user = User::where('kode_asisten', $kodeAsisten)->where('role', 'aslab')->first();

        // Jika tidak ditemukan, buat user baru dengan data minimal
        if (!$user) {
            $user = User::create([
                'name'         => 'Asisten ' . $kodeAsisten,
                'email'        => strtolower(str_replace(' ', '', $kodeAsisten)) . '@wicida.ac.id',
                'password'     => Hash::make('password123'),
                'kode_asisten' => $kodeAsisten,
                'role'         => 'aslab',
                'status_aslab' => 'aktif',
            ]);
        }

        // Pastikan user terdaftar di semester aktif
        $user->semesters()->syncWithoutDetaching([
            $this->semesterId => [
                'status'     => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $subKriteriaMap = $this->getSubKriteriaMap();

        // Loop setiap kolom selain kode_asisten
        foreach ($row as $header => $nilai) {
            if ($header === 'kode_asisten' || $nilai === null || $nilai === '') {
                continue;
            }

            // Header dari WithHeadingRow sudah dinormalisasi (lowercase, spasi→underscore)
            // Langsung pakai sebagai key tanpa transformasi tambahan
            $key = (string) $header;

            if (!isset($subKriteriaMap[$key])) {
                continue; // Kolom tidak cocok dengan nama sub-kriteria manapun, skip
            }

            $subK = $subKriteriaMap[$key];

            Evaluasi::updateOrCreate(
                [
                    'aslab_id'        => $user->id,
                    'sub_kriteria_id' => $subK instanceof Model ? $subK->id : $subK['id'],
                    'semester_id'     => $this->semesterId,
                ],
                [
                    'evaluator_id' => $this->evaluatorId,
                    'nilai_aktual' => (int) $nilai,
                ]
            );
        }

        return null;
    }
}
