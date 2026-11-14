<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\KriteriaImport;
use App\Models\Kriteria;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class KriteriaController extends Controller
{
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama_kriteria'       => 'required|string|max:255',
            'persentase_kriteria' => 'required|integer|min:1|max:100',
            'bobot_cf'            => 'required|integer|min:1|max:100',
            'bobot_sf'            => 'required|integer|min:1|max:100',
        ]);

        if ($data['bobot_cf'] + $data['bobot_sf'] !== 100) {
            throw ValidationException::withMessages([
                'bobot_cf' => 'Jumlah bobot Core Factor dan Secondary Factor harus tepat 100%.',
            ]);
        }

        return $data;
    }

    // Hanya kriteria milik semester aktif yang boleh diubah
    private function find($id): Kriteria
    {
        return Kriteria::where('semester_id', Semester::currentEditable()->id)->findOrFail($id);
    }

    public function index()
    {
        $kriterias = Kriteria::with('subKriterias')
            ->where('semester_id', Semester::current()?->id)
            ->get();

        $totalPersen = $kriterias->sum('persentase_kriteria');

        return view('admin.kriteria.index', compact('kriterias', 'totalPersen'));
    }

    public function create()
    {
        Semester::currentEditable();
        return view('admin.kriteria.create');
    }

    public function store(Request $request)
    {
        $sem  = Semester::currentEditable();
        $data = $this->validated($request) + ['semester_id' => $sem->id];

        Kriteria::create($data);

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria baru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kriteria = $this->find($id);
        $kriteria->load('subKriterias.rubrikPenilaians');

        return view('admin.kriteria.show', compact('kriteria'));
    }

    public function edit($id)
    {
        return view('admin.kriteria.edit', ['kriteria' => $this->find($id)]);
    }

    public function update(Request $request, $id)
    {
        $kriteria = $this->find($id);
        $kriteria->update($this->validated($request));

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->find($id)->delete();

        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }

    public function importExcelGet(Request $request)
    {
        return view('admin.kriteria.import');
    }

    public function importExcelPost(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'excel_file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            Excel::import(new KriteriaImport, $request->file('excel_file'));

            return redirect()->route('admin.kriteria.index')
                ->with('success', 'Kriteria, sub-kriteria, dan rubrik penilaian berhasil diimpor.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengimpor file: ' . $th->getMessage()]);
        }
    }
}
