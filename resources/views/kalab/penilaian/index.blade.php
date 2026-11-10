@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <!-- Header & Rekap Jumlah Asisten -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Input Penilaian Kinerja Asisten</h3>
            <p class="text-sm text-slate-500 mt-0.5">
                Pilih asisten laboratorium di bawah ini untuk menginput, mengubah, atau menghapus nilai evaluasi kinerja.
            </p>
        </div>

        <!-- Indikator Rekap Jumlah Asisten -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-800 text-xs font-bold rounded-xl border border-purple-100">
                <i data-lucide="users" class="w-3.5 h-3.5 text-purple-600"></i>
                Total: {{ $aslabs->count() }} Asisten
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-800 text-xs font-bold rounded-xl border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Sudah: {{ $aslabs->where('status_penilaian', 'Sudah Dinilai')->count() }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                Belum: {{ $aslabs->where('status_penilaian', 'Belum Dinilai')->count() }}
            </span>
        </div>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm p-4 rounded-xl flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabel Daftar Aslab -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 text-xs font-bold uppercase whitespace-nowrap">
                        <th class="py-4 px-6">Kode Aslab</th>
                    <th class="py-4 px-6">Nama Asisten</th>
                    <th class="py-4 px-6">NIM</th>
                    <th class="py-4 px-6 text-center w-60">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($aslabs as $aslab)
                        <tr class="hover:bg-purple-50/20 transition">
                            <!-- Kode Aslab -->
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-lg tracking-wider font-mono">
                                    {{ $aslab->kode_asisten ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Nama Asisten -->
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                {{ $aslab->name }}
                            </td>

                            <!-- NIM -->
                            <td class="py-4 px-6 text-slate-500">
                                {{ $aslab->nim ?? '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($isArchive)
                                    <span class="text-xs text-slate-400 italic">Arsip · Hanya Baca</span>
                                @else
                                    <div class="inline-flex items-center justify-center gap-2">
                                        @if($aslab->status_penilaian === 'Sudah Dinilai')
                                            {{-- Tombol Edit Nilai --}}
                                            <a href="{{ route('kalab.penilaian.create', $aslab->id) }}"
                                               class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-3.5 py-2 rounded-xl shadow-xs transition">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                Edit Nilai
                                            </a>

                                            {{-- Tombol Hapus Penilaian --}}
                                            <form action="{{ route('kalab.penilaian.destroy', $aslab->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh data penilaian untuk asisten {{ $aslab->name }} pada semester ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold text-xs px-3 py-2 rounded-xl transition"
                                                    title="Hapus Penilaian Asisten">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            {{-- Tombol Beri Nilai --}}
                                            <a href="{{ route('kalab.penilaian.create', $aslab->id) }}"
                                               class="inline-flex items-center gap-1.5 bg-purple-700 hover:bg-purple-800 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-sm transition">
                                                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                                Beri Nilai
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">
                                <i data-lucide="info" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Tidak ada data asisten laboratorium.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
