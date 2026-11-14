<?php

namespace App\Http\Controllers\Concerns;

use App\Imports\EvaluasiImport;
use App\Models\Evaluasi;
use App\Models\Kriteria;
use App\Models\Semester;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait HandlesPenilaian
{
    // 'admin' atau 'kalab' (menentukan folder view dan nama route)
    abstract protected function prefix(): string;

    protected function semester(): Semester
    {
        $s = Semester::current();
        abort_if(!$s, 404, 'Belum ada semester. Buat semester terlebih dahulu.');
        return $s;
    }

    public function index()
    {
        $sem = $this->semester();
        $aslabs = collect($this->pmService->calculate($sem->id))
            ->map(function ($r) {
                $r['aslab']->status_penilaian = $r['is_evaluated'] ? 'Sudah Dinilai' : 'Belum Dinilai';
                return $r['aslab'];
            })
            ->sortBy('kode_asisten')
            ->values();

        return view("{$this->prefix()}.penilaian.index", compact('aslabs'));
    }

    public function create($aslab_id)
    {
        $sem   = $this->semester();
        $aslab = User::where('id', $aslab_id)->where('role', 'aslab')->firstOrFail();

        // Eager-load sub-kriteria beserta rubrik dari DB
        $kriterias = Kriteria::with('subKriterias.rubrikPenilaians')
            ->where('semester_id', $sem->id)
            ->get();

        $evaluasis = Evaluasi::where('aslab_id', $aslab->id)
            ->where('semester_id', $sem->id)
            ->pluck('nilai_aktual', 'sub_kriteria_id')
            ->toArray();

        return view("{$this->prefix()}.penilaian.input", compact('aslab', 'kriterias', 'evaluasis'));
    }

    public function store(Request $request)
    {
        // Semester arsip otomatis ditolak (403)
        $sem = Semester::currentEditable();

        $request->validate([
            'aslab_id' => 'required|exists:users,id',
            'nilai'    => 'required|array',
            'nilai.*'  => 'required|integer|min:1|max:5',
        ]);

        // Hanya sub-kriteria milik semester ini yang boleh dinilai
        $validSubIds = SubKriteria::whereHas('kriteria', fn ($q) => $q->where('semester_id', $sem->id))
            ->pluck('id');

        if ($validSubIds->diff(array_keys($request->nilai))->isNotEmpty()) {
            return back()->withErrors(['nilai' => 'Semua sub-kriteria wajib dinilai.'])->withInput();
        }

        foreach ($request->nilai as $subId => $nilai) {
            if (!$validSubIds->contains($subId)) {
                continue;
            }

            Evaluasi::updateOrCreate(
                [
                    'aslab_id'        => $request->aslab_id,
                    'sub_kriteria_id' => $subId,
                    'semester_id'     => $sem->id,
                ],
                [
                    'evaluator_id' => Auth::id(),
                    'nilai_aktual' => $nilai,
                ]
            );
        }

        $aslab = User::find($request->aslab_id);
        $namaAslab = $aslab ? "untuk asisten {$aslab->name}" : "";

        return redirect()->route("{$this->prefix()}.penilaian.index")
            ->with('success', "Penilaian {$namaAslab} berhasil disimpan.");
    }

    public function destroy($aslab_id)
    {
        // Semester arsip otomatis ditolak (403)
        $sem = Semester::currentEditable();

        $aslab = User::where('id', $aslab_id)->where('role', 'aslab')->firstOrFail();

        Evaluasi::where('aslab_id', $aslab->id)
            ->where('semester_id', $sem->id)
            ->delete();

        return redirect()->route("{$this->prefix()}.penilaian.index")
            ->with('success', "Penilaian untuk asisten {$aslab->name} berhasil dihapus.");
    }

    public function hasil()
    {
        $sem       = $this->semester();
        $rankings  = $this->pmService->calculate($sem->id);
        $kriterias = Kriteria::with('subKriterias')->where('semester_id', $sem->id)->get();
        $semesters = Semester::orderByDesc('id')->get();

        // Nilai lama, dipakai untuk mengisi modal "Edit Nilai"
        $existingEvaluasis = Evaluasi::where('semester_id', $sem->id)
            ->get()
            ->groupBy('aslab_id')
            ->map(fn ($rows) => $rows->pluck('nilai_aktual', 'sub_kriteria_id'));

        return view("{$this->prefix()}.penilaian.hasil",
            compact('rankings', 'kriterias', 'semesters', 'existingEvaluasis'));
    }

    public function detail($aslab_id)
    {
        $sem   = $this->semester();
        $aslab = User::where('id', $aslab_id)->where('role', 'aslab')->firstOrFail();

        $myResult = collect($this->pmService->calculate($sem->id))
            ->first(fn ($r) => $r['aslab']->id == $aslab->id);

        return view('admin.penilaian.detail', compact('aslab', 'myResult'));
    }

    public function importGet()
    {
        return view("{$this->prefix()}.penilaian.import");
    }

    public function importPost(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'excel_file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $sem = Semester::currentEditable();

        try {
            Excel::import(
                new EvaluasiImport(Auth::id(), $sem->id),
                $request->file('excel_file')
            );

            return redirect()->route("{$this->prefix()}.penilaian.index")
                ->with('success', 'Data evaluasi berhasil diimpor dari file Excel.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengimpor file: ' . $th->getMessage()]);
        }
    }
}
