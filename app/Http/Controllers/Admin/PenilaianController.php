<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesPenilaian;
use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Semester;
use App\Services\ProfileMatchingService;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    use HandlesPenilaian;

    public function __construct(protected ProfileMatchingService $pmService)
    {
    }

    protected function prefix(): string
    {
        return 'admin';
    }

    /**
     * Admin dialihkan langsung ke halaman hasil perankingan.
     */
    public function index()
    {
        return redirect()->route('admin.penilaian.hasil');
    }

    /**
     * Role admin tidak berhak melakukan penilaian (hanya Kepala Lab).
     */
    public function create($aslab_id)
    {
        abort(403, 'Akses ditolak. Penilaian kinerja hanya dapat dilakukan oleh Kepala Laboratorium.');
    }

    /**
     * Role admin tidak berhak menyimpan penilaian.
     */
    public function store(Request $request)
    {
        abort(403, 'Akses ditolak. Penilaian kinerja hanya dapat dilakukan oleh Kepala Laboratorium.');
    }

    /**
     * Role admin tidak berhak menghapus penilaian.
     */
    public function destroy($aslab_id)
    {
        abort(403, 'Akses ditolak. Penilaian kinerja hanya dapat dikelola oleh Kepala Laboratorium.');
    }

    public function cetak(Request $request)
    {
        $selectedSemesterId = $request->query('semester_id')
            ?: (Semester::current()?->id ?? Semester::where('status', 'aktif')->value('id') ?? Semester::latest('id')->value('id'));

        $rankings = $this->pmService->calculate($selectedSemesterId);
        $kriterias = Kriteria::where('semester_id', $selectedSemesterId)->get();
        $semesterAktif = Semester::find($selectedSemesterId) ?? Semester::where('status', 'aktif')->first();

        return view('admin.penilaian.cetak', compact('rankings', 'kriterias', 'semesterAktif'));
    }
}
