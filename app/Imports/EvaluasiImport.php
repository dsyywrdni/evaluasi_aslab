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

    protected ?array $subKriteriaMap = null;

    public function __construct(int $evaluatorId, int $semesterId)
    {
        $this->evaluatorId = $evaluatorId;
        $this->semesterId  = $semesterId;
    }

    protected function getSubKriteriaMap(): array
    {
        if ($this->subKriteriaMap === null) {
            $this->subKriteriaMap = SubKriteria::whereHas('kriteria', function ($q) {
                $q->where('semester_id', $this->semesterId);
            })->get()->keyBy(function ($s) {
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

        $user = User::where('kode_asisten', $kodeAsisten)->where('role', 'aslab')->first();

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
            $key = (string) $header;

            if (!isset($subKriteriaMap[$key])) {
                continue;
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
