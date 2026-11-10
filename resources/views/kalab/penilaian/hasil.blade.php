@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header Informasi -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-purple-900">Hasil Evaluasi Akhir & Perengkingan (Kepala Lab)</h3>
            <p class="text-sm text-slate-500">
                Melihat data periode:
                <strong class="text-purple-700">{{ $currentSemester->nama_semester ?? '-' }}</strong>
            </p>
        </div>

        <!-- Rekap Ringkas Asisten Dinilai -->
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-purple-50 text-purple-900 text-xs font-bold rounded-xl border border-purple-200">
                <i data-lucide="check-circle" class="w-4 h-4 text-purple-700"></i>
                Rekap: {{ collect($rankings)->where('is_evaluated', true)->count() }} dari {{ count($rankings) }} Asisten Dinilai
            </span>
        </div>
    </div>

    <!-- Tabel Ranking Dinamis -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-purple-50/50">
            <h4 class="text-sm font-semibold text-purple-900 uppercase tracking-wide">
                Peringkat Kinerja Asisten Laboratorium
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 text-xs font-bold uppercase">
                        <th class="py-4 px-6 text-center w-20">Peringkat</th>
                        <th class="py-4 px-6 text-center">Kode Aslab</th>
                        <th class="py-4 px-6">Nama Asisten</th>
                        <th class="py-4 px-6">NIM</th>

                        <!-- Header Kriteria Dinamis -->
                        @foreach($kriterias as $kriteria)
                            <th class="py-4 px-6 text-center">
                                Rerata {{ $kriteria->nama_kriteria }}
                            </th>
                        @endforeach

                        <th class="py-4 px-6 text-center bg-purple-50 text-purple-950 font-bold">
                            Skor Akhir (Yi)
                        </th>
                        <th class="py-4 px-6 text-center">Kategori</th>
                        <th class="py-4 px-6 text-center w-36">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($rankings as $row)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Peringkat -->
                            <td class="py-4 px-6 text-center">
                                @if($row['is_evaluated'])
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-xs
                                        {{ $row['rank'] == 1 ? 'bg-amber-100 text-amber-800' : ($row['rank'] == 2 ? 'bg-slate-100 text-slate-700' : 'bg-slate-50 text-slate-500') }}">
                                        {{ $row['rank'] }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Kode Asisten -->
                            <td class="py-4 px-6 text-center">
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-lg tracking-wider">
                                    {{ $row['aslab']->kode_asisten ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Nama Asisten -->
                            <td class="py-4 px-6 font-semibold text-slate-800">
                                {{ $row['aslab']->name }}
                            </td>

                            <!-- NIM -->
                            <td class="py-4 px-6 text-slate-500">
                                {{ $row['aslab']->nim ?? '-' }}
                            </td>

                            <!-- Nilai Per Kriteria -->
                            @foreach($kriterias as $kriteria)
                                <td class="py-4 px-6 text-center text-slate-600 font-medium">
                                    {{ ($row['is_evaluated'] && isset($row['kriterias'][$kriteria->id])) ? number_format($row['kriterias'][$kriteria->id]['total'] ?? 0, 2) : '-' }}
                                </td>
                            @endforeach

                            <!-- Skor Akhir -->
                            <td class="py-4 px-6 text-center bg-purple-50/40 font-bold text-purple-700 text-base">
                                {{ $row['is_evaluated'] ? number_format($row['grand_total'], 3) : '-' }}
                            </td>

                            <!-- Kategori Rekomendasi -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
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

                            <!-- Aksi (Hanya Lihat Detail GAP) -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($row['is_evaluated'])
                                    <a href="{{ route('kalab.penilaian.detail', $row['aslab']->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-xs font-semibold transition"
                                       title="Lihat Detail Analisis GAP">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>Detail GAP</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum Dinilai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + $kriterias->count() + 1 }}" class="py-12 text-center text-slate-400">
                                <i data-lucide="info" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                                Belum ada data asisten pada {{ $currentSemester->nama_semester ?? 'periode ini' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
