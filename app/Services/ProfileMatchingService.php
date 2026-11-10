<?php

namespace App\Services;

use App\Models\User;
use App\Models\Kriteria;
use App\Models\Evaluasi;
use App\Models\Semester;

class ProfileMatchingService
{
    private function getBobotGap($gap)
    {
        $map = [
            0  => 5.0, // Tidak ada selisih (sesuai target)
            1  => 4.5, // Kelebihan 1 tingkat
            -1 => 4.0, // Kekurangan 1 tingkat
            2  => 3.5, // Kelebihan 2 tingkat
            -2 => 3.0, // Kekurangan 2 tingkat
            3  => 2.5, // Kelebihan 3 tingkat
            -3 => 2.0, // Kekurangan 3 tingkat
            4  => 1.5, // Kelebihan 4 tingkat
            -4 => 1.0, // Kekurangan 4 tingkat
        ];

        if ($gap > 4) return 1.5;
        if ($gap < -4) return 1.0;

        return $map[$gap] ?? 1.0;
    }

    /**
     * Perhitungan Profile Matching per semester.
     * Rumus GAP, NCF/NSF, dan bobot tetap sama; yang berubah hanya sumber datanya
     * (kriteria, asisten, dan nilai semuanya difilter per semester).
     */
    public function calculate($semesterId = null)
    {
        // 1. Tentukan semester lebih dulu (sebelum dipakai di query)
        $semesterId = $semesterId
            ?: (Semester::where('status', 'aktif')->value('id') ?? Semester::latest('id')->value('id'));

        if (!$semesterId) return [];

        // 2. Asisten yang terdaftar aktif di semester ini, atau yang sudah punya nilai di semester ini
        $aslabs = User::where('role', 'aslab')
            ->where(function ($q) use ($semesterId) {
                $q->whereHas('semesters', fn ($s) => $s->where('semesters.id', $semesterId)
                                                       ->where('aslab_semester.status', 'aktif'))
                  ->orWhereHas('evaluasisAsAslab', fn ($e) => $e->where('semester_id', $semesterId));
            })
            ->orderBy('kode_asisten')
            ->get();

        // 3. Kriteria milik semester ini
        $kriterias = Kriteria::with('subKriterias')
            ->where('semester_id', $semesterId)
            ->get();

        // 4. Ambil semua nilai semester ini sekali saja (bukan query di dalam loop)
        $allSubIds   = $kriterias->flatMap(fn ($k) => $k->subKriterias->pluck('id'));
        $evalByAslab = Evaluasi::where('semester_id', $semesterId)->get()->groupBy('aslab_id');

        $results = [];

        foreach ($aslabs as $aslab) {
            // Rata-rata nilai per sub-kriteria (jika ada lebih dari satu penilai)
            $nilaiBySub = $evalByAslab->get($aslab->id, collect())
                ->groupBy('sub_kriteria_id')
                ->map(fn ($rows) => $rows->avg('nilai_aktual'));

            // "Sudah dinilai" = SEMUA sub-kriteria terisi (bukan sekadar 1)
            $isEvaluated = $allSubIds->isNotEmpty()
                && $allSubIds->every(fn ($id) => $nilaiBySub->has($id));

            $aslabResult = [
                'aslab'        => $aslab,
                'kriterias'    => [],
                'grand_total'  => 0,
                'is_evaluated' => $isEvaluated,
            ];

            $grandTotal = 0;

            foreach ($kriterias as $kriteria) {
                // Belum dinilai lengkap: isi 0, lewati rumus
                if (!$isEvaluated) {
                    $aslabResult['kriterias'][$kriteria->id] = [
                        'nama_kriteria'    => $kriteria->nama_kriteria,
                        'details'          => [],
                        'ncf'              => 0,
                        'nsf'              => 0,
                        'total'            => 0,
                        'bobot_persentase' => $kriteria->persentase_kriteria,
                    ];
                    continue;
                }

                $cfSum = 0; $cfCount = 0;
                $sfSum = 0; $sfCount = 0;
                $details = [];

                foreach ($kriteria->subKriterias as $sub) {
                    $aktual   = $nilaiBySub->get($sub->id, 0);
                    $gap      = $aktual - $sub->nilai_target;
                    $bobotGap = $this->getBobotGap((int) round($gap));

                    if ($sub->jenis === 'core') {
                        $cfSum += $bobotGap;
                        $cfCount++;
                    } else {
                        $sfSum += $bobotGap;
                        $sfCount++;
                    }

                    $details[$sub->id] = [
                        'nama_sub'  => $sub->nama_sub,
                        'aktual'    => $aktual,
                        'target'    => $sub->nilai_target,
                        'gap'       => $gap,
                        'bobot_gap' => $bobotGap,
                        'jenis'     => $sub->jenis,
                    ];
                }

                $ncf = $cfCount > 0 ? ($cfSum / $cfCount) : 0;
                $nsf = $sfCount > 0 ? ($sfSum / $sfCount) : 0;

                $persenCF = ($kriteria->bobot_cf ?? 60) / 100;
                $persenSF = ($kriteria->bobot_sf ?? 40) / 100;

                $totalKriteria = ($persenCF * $ncf) + ($persenSF * $nsf);

                $aslabResult['kriterias'][$kriteria->id] = [
                    'nama_kriteria'    => $kriteria->nama_kriteria,
                    'details'          => $details,
                    'ncf'              => round($ncf, 2),
                    'nsf'              => round($nsf, 2),
                    'total'            => round($totalKriteria, 2),
                    'bobot_persentase' => $kriteria->persentase_kriteria,
                ];

                $grandTotal += $totalKriteria * ($kriteria->persentase_kriteria / 100);
            }

            $aslabResult['grand_total'] = round($grandTotal, 3);
            $aslabResult['kategori']    = self::getKategori($aslabResult['grand_total'], $isEvaluated);
            $results[] = $aslabResult;
        }

        // Urutkan perangkingan
        usort($results, fn ($a, $b) => $b['grand_total'] <=> $a['grand_total']);

        foreach ($results as $index => &$res) {
            $res['rank'] = $index + 1;
        }
        unset($res);

        return $results;
    }

    public static function getKategori($grandTotal, $isEvaluated = true): ?string
    {
        if (!$isEvaluated || $grandTotal === null) {
            return null;
        }

        return ((float)$grandTotal >= 4.00) ? 'Pelatihan' : 'Pengembangan';
    }

    /**
     * Satu sumber data untuk dashboard Admin dan Kalab (angkanya pasti sama).
     * Kriteria dikelompokkan berdasarkan NAMA karena id kriteria berbeda di tiap semester.
     */
    public function getDashboardData(?int $selectedId): array
    {
        $semesters = Semester::orderBy('id')->get();

        $labels = [];
        $avgScores = [];
        $topScores = [];
        $perSemester = [];
        $byId = [];

        // Hitung calculate() cukup SATU kali per semester, lalu dipakai ulang
        foreach ($semesters as $sem) {
            $rankings = collect($this->calculate($sem->id));
            $byId[$sem->id] = $rankings;
            $evaluated = $rankings->where('is_evaluated', true);

            $labels[]    = $sem->nama_semester . ($sem->status === 'aktif' ? ' (Aktif)' : '');
            $avgScores[] = $evaluated->isNotEmpty() ? round($evaluated->avg('grand_total'), 3) : 0;
            $topScores[] = $evaluated->isNotEmpty() ? round($evaluated->max('grand_total'), 3) : 0;

            $perSemester[$sem->id] = $evaluated
                ->flatMap(fn ($r) => array_values($r['kriterias']))
                ->groupBy('nama_kriteria')
                ->map(fn ($g) => round($g->avg('total'), 2))
                ->all();
        }

        $names = collect($perSemester)->flatMap(fn ($x) => array_keys($x))->unique()->values();

        $criteriaData = $names->map(fn ($nama) => [
            'nama'   => $nama,
            'scores' => $semesters->map(fn ($sem) => [
                'semester_nama' => $sem->nama_semester,
                'is_active'     => $sem->id === $selectedId,
                'score'         => $perSemester[$sem->id][$nama] ?? 0,
            ])->all(),
        ])->all();

        $rankings  = $byId[$selectedId] ?? collect();
        $evaluated = $rankings->where('is_evaluated', true);

        return [
            'trendData'        => compact('labels', 'avgScores', 'topScores'),
            'criteriaData'     => $criteriaData,
            'topThree'         => $evaluated->take(3)->values(),
            'totalAslab'       => $rankings->count(),
            'totalDinilai'     => $evaluated->count(),
            'totalKriteria'    => Kriteria::where('semester_id', $selectedId)->count(),
            'selectedAvgScore' => $evaluated->isNotEmpty() ? round($evaluated->avg('grand_total'), 3) : 0,
        ];
    }
}
