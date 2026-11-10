<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::orderBy('id', 'desc')->get();
        return view('admin.semester.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_semester' => 'required|string|max:255|unique:semesters',
        ]);

        DB::transaction(function () use ($request) {
            $new = Semester::create([
                'nama_semester' => $request->nama_semester,
                'status'        => 'tidak_aktif',
            ]);

            $prev = Semester::where('id', '<', $new->id)->latest('id')->first();
            if ($prev) {
                // Salin kriteria + sub-kriteria dari semester sebelumnya
                foreach (Kriteria::with('subKriterias')->where('semester_id', $prev->id)->get() as $k) {
                    $nk = $k->replicate();
                    $nk->semester_id = $new->id;
                    $nk->save();

                    foreach ($k->subKriterias as $s) {
                        $ns = $s->replicate();
                        $ns->kriteria_id = $nk->id;
                        $ns->save();
                    }
                }
            }

            // Daftarkan seluruh asisten ke semester baru yang dibuat
            $new->ensureAllAslabsRegistered();
        });

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester baru dibuat. Kriteria dan seluruh asisten berhasil didaftarkan.');
    }

    // Mengaktifkan satu semester dan menonaktifkan yang lain
    public function aktifkan($id)
    {
        Semester::query()->update(['status' => 'tidak_aktif']);

        $semester = Semester::findOrFail($id);
        $semester->update(['status' => 'aktif']);

        // Pastikan seluruh asisten dengan role aslab terdaftar secara otomatis di semester yang aktif ini
        $semester->ensureAllAslabsRegistered();

        // Header ikut pindah ke semester aktif yang baru
        session(['selected_semester_id' => $semester->id]);

        return redirect()->route('admin.semester.index')
            ->with('success', "Semester {$semester->nama_semester} sekarang aktif berjalan.");
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);

        if ($semester->status === 'aktif') {
            return back()->withErrors(['error' => 'Semester yang sedang aktif tidak diperbolehkan untuk dihapus.']);
        }

        $semester->delete();

        // Jika yang dihapus sedang ditinjau, kembali ke semester aktif
        if (session('selected_semester_id') == $id) {
            session()->forget('selected_semester_id');
        }

        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil dihapus dari sistem.');
    }
}
