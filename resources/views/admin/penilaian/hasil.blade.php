@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Informasi -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-lg font-bold text-purple-900">
                    Hasil Evaluasi & Perankingan Kinerja Aslab
                </h3>
                <span class="px-2.5 py-0.5 bg-purple-100 text-purple-800 text-[11px] font-bold rounded-lg border border-purple-200">
                    Penilai: Kepala Lab
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Melihat data periode:
                <strong class="text-purple-700">
                    {{ $currentSemester->nama_semester ?? '-' }}
                </strong>
                <span class="text-xs text-slate-400 ml-1">· Penilaian diinput oleh Kepala Laboratorium</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Rekap Singkat Asisten Dinilai -->
            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200">
                <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                {{ collect($rankings)->where('is_evaluated', true)->count() }} dari {{ count($rankings) }} Asisten Dinilai
            </span>

            <!-- Tombol Cetak Laporan (Hanya Admin) -->
            <a href="{{ route('admin.penilaian.cetak') }}"
                target="_blank"
                class="flex items-center gap-1.5 bg-purple-700 hover:bg-purple-800 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Notifikasi Berhasil -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Notifikasi Error -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm p-4 rounded-xl">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Keterangan Rentang Nilai Kategori -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center text-purple-700 shrink-0">
                <i data-lucide="info" class="w-4 h-4"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-purple-950 uppercase tracking-wide">
                    Keterangan Rentang Nilai Akhir & Kategori Evaluasi
                </h4>
                <p class="text-xs text-slate-500">
                    Klasifikasi hasil evaluasi untuk pembinaan dan pengembangan asisten laboratorium:
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
            <div class="flex items-center justify-between px-3.5 py-2.5 bg-emerald-50/50 border border-emerald-200 rounded-xl">
                <div>
                    <span class="block text-[11px] text-slate-500 font-medium">Rentang Nilai</span>
                    <span class="font-bold text-slate-800 font-mono text-sm">4,50 – 5,00</span>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg border border-emerald-200">
                    Pengembangan
                </span>
            </div>

            <div class="flex items-center justify-between px-3.5 py-2.5 bg-emerald-50/50 border border-emerald-200 rounded-xl">
                <div>
                    <span class="block text-[11px] text-slate-500 font-medium">Rentang Nilai</span>
                    <span class="font-bold text-slate-800 font-mono text-sm">4,00 – 4,49</span>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg border border-emerald-200">
                    Pengembangan
                </span>
            </div>

            <div class="flex items-center justify-between px-3.5 py-2.5 bg-amber-50/50 border border-amber-200 rounded-xl">
                <div>
                    <span class="block text-[11px] text-slate-500 font-medium">Rentang Nilai</span>
                    <span class="font-bold text-slate-800 font-mono text-sm">3,50 – 3,99</span>
                </div>
                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-lg border border-amber-200">
                    Pelatihan
                </span>
            </div>

            <div class="flex items-center justify-between px-3.5 py-2.5 bg-amber-50/50 border border-amber-200 rounded-xl">
                <div>
                    <span class="block text-[11px] text-slate-500 font-medium">Rentang Nilai</span>
                    <span class="font-bold text-slate-800 font-mono text-sm">&lt; 3,50</span>
                </div>
                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-lg border border-amber-200">
                    Pelatihan
                </span>
            </div>
        </div>
    </div>

    <!-- Tabel Ranking Terpadu -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-purple-50/50 flex justify-between items-center">
            <h4 class="text-sm font-semibold text-purple-900 uppercase tracking-wide">
                Peringkat Kinerja Asisten Berdasarkan Metode Profile Matching
            </h4>
            <span class="text-xs text-slate-500">
                Data dinilai langsung oleh Kepala Laboratorium
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 text-xs font-bold uppercase">
                        <th class="py-4 px-4 text-center">Rank</th>
                        <th class="py-4 px-4 text-center">Kode</th>
                        <th class="py-4 px-6">Nama Asisten</th>
                        <th class="py-4 px-4 text-center">Status</th>

                        @foreach($kriterias as $k)
                            <th class="py-4 px-4 text-center">
                                Rerata {{ $k->nama_kriteria }}
                            </th>
                        @endforeach

                        <th class="py-4 px-4 text-center bg-purple-50 font-bold text-purple-950">
                            Skor (Yi)
                        </th>
                        <th class="py-4 px-4 text-center">Kategori</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($rankings as $row)
                        <tr class="hover:bg-slate-50 transition">

                            <!-- Ranking -->
                            <td class="py-4 px-4 text-center font-bold">
                                @if($row['is_evaluated'])
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                                        {{ $row['rank'] == 1 ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($row['rank'] == 2 ? 'bg-slate-100 text-slate-700' : 'bg-slate-50 text-slate-600') }}">
                                        {{ $row['rank'] }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Kode Asisten -->
                            <td class="py-4 px-4 text-center">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-bold rounded-lg font-mono">
                                    {{ $row['aslab']->kode_asisten ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Nama Asisten -->
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                {{ $row['aslab']->name }}
                            </td>

                            <!-- Status Penilaian -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($row['is_evaluated'])
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full whitespace-nowrap border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-full whitespace-nowrap border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>

                            <!-- Nilai Per Kriteria -->
                            @foreach($kriterias as $k)
                                <td class="py-4 px-4 text-center font-mono text-slate-600">
                                    {{ ($row['is_evaluated'] && isset($row['kriterias'][$k->id])) ? number_format($row['kriterias'][$k->id]['total'] ?? 0, 2) : '-' }}
                                </td>
                            @endforeach

                            <!-- Skor Akhir -->
                            <td class="py-4 px-4 text-center font-bold text-purple-700 bg-purple-50/50 font-mono text-base">
                                {{ $row['is_evaluated'] ? number_format($row['grand_total'], 3) : '-' }}
                            </td>

                            <!-- Kategori Rekomendasi -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($row['is_evaluated'] && !empty($row['kategori']))
                                    @if($row['kategori'] === 'Pengembangan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Pengembangan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Pelatihan
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>

                            <!-- Aksi (Hanya Lihat Detail GAP, Penilaian hanya oleh Kalab) -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($row['is_evaluated'])
                                    <a href="{{ route('admin.penilaian.detail', $row['aslab']->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-xs font-semibold transition"
                                        title="Lihat Detail Analisis GAP">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>Detail GAP</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Menunggu Kalab</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 7 + $kriterias->count() }}"
                                class="py-8 text-center text-slate-400">
                                <i data-lucide="info" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                Belum ada data asisten laboratorium.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
