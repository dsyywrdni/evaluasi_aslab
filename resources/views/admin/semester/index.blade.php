@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-purple-900">Kelola Periode / Semester Penilaian</h3>
        <p class="text-sm text-slate-500">Buat periode semester baru dan aktifkan salah satu untuk memfokuskan proses evaluasi kinerja.</p>
    </div>

    <!-- Alert Sukses atau Gagal -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm p-4 rounded-xl flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: FORM BUAT SEMESTER BARU -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <div class="mb-4">
                <h4 class="text-sm font-bold text-purple-950 uppercase tracking-wide">Tambah Semester</h4>
                <p class="text-[11px] text-slate-400">Buat nama periode semester baru secara manual di bawah ini.</p>
            </div>

            <form action="{{ route('admin.semester.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase">Nama Semester / Periode</label>
                    <input type="text" name="nama_semester" required class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition" placeholder="Contoh: Genap 2026/2027">
                </div>
                <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm py-3 rounded-xl shadow-md transition">
                    Simpan Semester
                </button>
            </form>

            <p class="text-[11px] text-slate-400 mt-3">
                Kriteria dan asisten (selain alumni) otomatis disalin dari semester sebelumnya. Perubahan di semester ini tidak memengaruhi arsip lama.
            </p>
        </div>

        <!-- KOLOM KANAN: DAFTAR SEMESTER & ATUR STATUS -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-fit">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                        <th class="py-4 px-6">Nama Semester</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($semesters as $semester)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-semibold text-slate-800">{{ $semester->nama_semester }}</td>
                            
                            <!-- BADGE STATUS AKTIF / TIDAK AKTIF -->
                            <td class="py-4 px-6 text-center">
                                @if($semester->status === 'aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                                        Aktif Berjalan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-500 text-xs font-semibold rounded-full">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- TOMBOL UNTUK MENGAKTIFKAN / MENGHAPUS SEMESTER -->
                            <td class="py-4 px-6 text-center flex items-center justify-center gap-2">
                                <a href="{{ route('admin.dashboard', ['semester_id' => $semester->id]) }}"
                                class="flex items-center gap-1 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs px-3 py-1.5 rounded-xl transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Tinjau
                                </a>
                                @if($semester->status !== 'aktif')
                                    <form action="{{ route('admin.semester.aktifkan', $semester->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-1 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs px-3 py-1.5 rounded-xl transition">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Aktifkan
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.semester.destroy', $semester->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data semester ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium italic">Sedang Berjalan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">Belum ada data semester.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection