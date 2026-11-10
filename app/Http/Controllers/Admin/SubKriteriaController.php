<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\RubrikPenilaian;
use App\Models\Semester;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    public function store(Request $request, $kriteria_id)
    {
        $sem      = Semester::currentEditable();
        $kriteria = Kriteria::where('semester_id', $sem->id)->findOrFail($kriteria_id);

        $data = $request->validate([
            'nama_sub'     => 'required|string|max:255',
            'nilai_target' => 'required|integer|min:1|max:5',
            'jenis'        => 'required|in:core,secondary',
            'deskripsi'    => 'nullable|array',
            'deskripsi.*'  => 'nullable|string|max:1000',
        ]);

        $sub = $kriteria->subKriterias()->create([
            'nama_sub'     => $data['nama_sub'],
            'nilai_target' => $data['nilai_target'],
            'jenis'        => $data['jenis'],
        ]);

        if (!empty($request->deskripsi) && is_array($request->deskripsi)) {
            foreach ($request->deskripsi as $skor => $deskripsi) {
                $skorInt = (int) $skor;
                if ($skorInt >= 1 && $skorInt <= 5 && !empty(trim((string)$deskripsi))) {
                    RubrikPenilaian::create([
                        'sub_kriteria_id' => $sub->id,
                        'skor'            => $skorInt,
                        'deskripsi'       => trim($deskripsi),
                    ]);
                }
            }
        }

        return redirect()->route('admin.kriteria.show', $kriteria_id)
                         ->with('success', 'Sub-Kriteria berhasil ditambahkan.')
                         ->with('opened_sub_id', $sub->id);
    }

    public function update(Request $request, $id)
    {
        $sem = Semester::currentEditable();

        $sub = SubKriteria::whereHas('kriteria', fn ($q) => $q->where('semester_id', $sem->id))
            ->findOrFail($id);

        $data = $request->validate([
            'nama_sub'     => 'required|string|max:255',
            'nilai_target' => 'required|integer|min:1|max:5',
            'jenis'        => 'required|in:core,secondary',
            'deskripsi'    => 'nullable|array',
            'deskripsi.*'  => 'nullable|string|max:1000',
        ]);

        $sub->update([
            'nama_sub'     => $data['nama_sub'],
            'nilai_target' => $data['nilai_target'],
            'jenis'        => $data['jenis'],
        ]);

        if (is_array($request->deskripsi)) {
            foreach ($request->deskripsi as $skor => $deskripsi) {
                $skorInt = (int) $skor;
                if ($skorInt < 1 || $skorInt > 5) {
                    continue;
                }
                if (empty(trim((string)$deskripsi))) {
                    RubrikPenilaian::where('sub_kriteria_id', $sub->id)
                        ->where('skor', $skorInt)
                        ->delete();
                } else {
                    RubrikPenilaian::updateOrCreate(
                        ['sub_kriteria_id' => $sub->id, 'skor' => $skorInt],
                        ['deskripsi'       => trim($deskripsi)]
                    );
                }
            }
        }

        return back()->with('success', 'Sub-Kriteria dan Rubrik Penilaian berhasil diperbarui.')
                     ->with('opened_sub_id', $sub->id);
    }

    public function destroy($id)
    {
        $sem = Semester::currentEditable();

        SubKriteria::whereHas('kriteria', fn ($q) => $q->where('semester_id', $sem->id))
            ->findOrFail($id)
            ->delete();

        return back()->with('success', 'Sub-Kriteria berhasil dihapus.');
    }

    /**
     * Simpan / update rubrik penilaian (skor 1–5) untuk satu sub-kriteria.
     */
    public function storeRubrik(Request $request, $sub_id)
    {
        $sem = Semester::currentEditable();

        $sub = SubKriteria::whereHas('kriteria', fn ($q) => $q->where('semester_id', $sem->id))
            ->findOrFail($sub_id);

        $request->validate([
            'deskripsi'   => 'required|array|size:5',
            'deskripsi.*' => 'nullable|string|max:1000',
        ]);

        foreach ($request->deskripsi as $skor => $deskripsi) {
            $skorInt = (int) $skor;
            if ($skorInt < 1 || $skorInt > 5) {
                continue;
            }
            if (empty(trim((string) $deskripsi))) {
                RubrikPenilaian::where('sub_kriteria_id', $sub->id)
                                ->where('skor', $skorInt)
                                ->delete();
            } else {
                RubrikPenilaian::updateOrCreate(
                    ['sub_kriteria_id' => $sub->id, 'skor' => $skorInt],
                    ['deskripsi'       => trim($deskripsi)]
                );
            }
        }

        return back()->with('success', 'Rubrik penilaian berhasil disimpan.')
                     ->with('opened_sub_id', $sub->id);
    }
}
