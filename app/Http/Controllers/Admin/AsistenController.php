<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AslabImport;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class AsistenController extends Controller
{
    public function index()
    {
        $sem = Semester::current();
        // Excel::import(new AslabImport, "aslab.xlsx");
        // Pastikan seluruh asisten dengan role 'aslab' terdaftar secara otomatis ke semester aktif
        if ($sem && $sem->status === 'aktif') {
            $sem->ensureAllAslabsRegistered();
        } else {
            Semester::syncAllAslabsToActive();
        }

        // Jika $sem belum ditentukan, ambil semester aktif atau yang terbaru
        if (!$sem) {
            $sem = Semester::where('status', 'aktif')->first() ?? Semester::latest('id')->first();
            if ($sem && $sem->status === 'aktif') {
                $sem->ensureAllAslabsRegistered();
            }
        }

        $aslabs = User::where('role', 'aslab')
            ->whereHas('semesters', fn ($q) => $q->where('semesters.id', $sem?->id))
            ->with(['semesters' => fn ($q) => $q->where('semesters.id', $sem?->id)])
            ->orderBy('kode_asisten')
            ->get();

        // Status diambil dari pivot semester yang ditinjau, bukan dari kolom global
        $aslabs->each(fn ($a) => $a->status_semester = $a->semesters->first()?->pivot->status ?? 'aktif');

        $totalSemua  = $aslabs->count();
        $totalAktif  = $aslabs->where('status_semester', 'aktif')->count();
        $totalCuti   = $aslabs->where('status_semester', 'cuti')->count();
        $totalAlumni = $aslabs->where('status_semester', 'alumni')->count();
        $isArchive   = (bool) ($sem && $sem->status !== 'aktif');

        return view('admin.asisten.index', compact(
            'aslabs', 'totalSemua', 'totalAktif', 'totalCuti', 'totalAlumni', 'isArchive'
        ));
    }

    public function create()
    {
        Semester::currentEditable();
        return view('admin.asisten.create');
    }

    public function store(Request $request)
    {
        $sem = Semester::currentEditable();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'nim'          => 'required|string|unique:users',
            'kode_asisten' => 'required|string|unique:users',
            'password'     => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'nim'          => $request->nim,
            'kode_asisten' => $request->kode_asisten,
            'password'     => Hash::make($request->password ?: 'password123'),
            'role'         => 'aslab',
            'status_aslab' => 'aktif',
        ]);

        // Daftarkan ke semester aktif lewat pivot
        $user->semesters()->syncWithoutDetaching([$sem->id => ['status' => 'aktif']]);

        return redirect()->route('admin.asisten.index')
            ->with('success', 'Asisten laboratorium baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $aslab = User::where('id', $id)->where('role', 'aslab')->firstOrFail();
        $sem   = Semester::current();

        $statusSemester = $sem ? $aslab->getStatusDiSemester($sem->id) : 'aktif';

        return view('admin.asisten.edit', compact('aslab', 'statusSemester'));
    }

    public function update(Request $request, $id)
    {
        $aslab = User::where('id', $id)->where('role', 'aslab')->firstOrFail();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email,' . $id,
            'nim'          => 'required|string|unique:users,nim,' . $id,
            'kode_asisten' => 'required|string|unique:users,kode_asisten,' . $id,
            'status_aslab' => 'nullable|in:aktif,cuti,alumni',
        ]);

        $aslab->update($request->only('name', 'email', 'nim', 'kode_asisten'));

        if ($request->filled('password')) {
            $aslab->update(['password' => Hash::make($request->password)]);
        }

        // Status hanya berubah untuk semester yang ditinjau, dan hanya jika semester aktif
        $sem = Semester::current();
        if ($request->filled('status_aslab')) {
            $aslab->update(['status_aslab' => $request->status_aslab]);
            if ($sem && $sem->status === 'aktif') {
                $aslab->semesters()->syncWithoutDetaching([
                    $sem->id => ['status' => $request->status_aslab],
                ]);
            }
        }

        return redirect()->route('admin.asisten.index')->with('success', 'Data asisten berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aslab = User::where('id', $id)->where('role', 'aslab')->firstOrFail();

        // Asisten yang punya riwayat nilai tidak boleh dihapus agar arsip tetap utuh
        if ($aslab->evaluasisAsAslab()->exists()) {
            return back()->withErrors([
                'error' => 'Asisten ini sudah punya riwayat penilaian. Ubah statusnya menjadi Alumni, jangan dihapus, agar arsip semester lama tidak rusak.',
            ]);
        }

        $aslab->delete();

        return redirect()->route('admin.asisten.index')->with('success', 'Data asisten berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $sem = Semester::currentEditable();

        $request->validate(['status_aslab' => 'required|in:aktif,cuti,alumni']);

        $aslab = User::where('id', $id)->where('role', 'aslab')->firstOrFail();
        $aslab->update(['status_aslab' => $request->status_aslab]);
        $aslab->semesters()->syncWithoutDetaching([
            $sem->id => ['status' => $request->status_aslab],
        ]);

        return back()->with('success', "Status {$aslab->name} pada {$sem->nama_semester} berhasil diubah.");
    }
    
    public function importExcelGet(Request $request)
    {
        return view('admin.asisten.import');
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

        $sem = Semester::currentEditable();

        try {
            Excel::import(new AslabImport($sem->id), $request->file('excel_file'));

            return redirect()->route('admin.asisten.index')
                ->with('success', 'Data asisten berhasil diimpor dan didaftarkan ke semester aktif.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengimpor file: ' . $th->getMessage()]);
        }
    }
}
