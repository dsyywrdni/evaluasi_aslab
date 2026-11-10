@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Tombol Kembali -->
    <div>
        <!-- Arahkan rute kembali secara dinamis sesuai rute asal penilai -->
        <a href="{{ Auth::user()->isAdmin() ? route('admin.penilaian.hasil') : route('kalab.penilaian.hasil') }}" class="inline-flex items-center gap-2 text-sm text-purple-700 hover:text-purple-900 font-medium transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Hasil
        </a>
    </div>

    <!-- Informasi Profil Asisten -->
    <div class="bg-gradient-to-r from-purple-800 to-indigo-900 p-6 rounded-2xl text-white shadow-md">
        <h3 class="text-xl font-bold">Rincian Perhitungan Analisis GAP</h3>
        <p class="text-purple-200 text-sm mt-1">Breakdown nilai kriteria asisten laboratorium berdasarkan metode Profile Matching.</p>
        <div class="mt-4 flex flex-wrap gap-6 text-xs text-purple-100 border-t border-purple-700/50 pt-4">
            <div>
                <span class="block text-purple-300">Nama Asisten</span>
                <span class="text-sm font-semibold">{{ $aslab->name }}</span>
            </div>
            <div>
                <span class="block text-purple-300">Kode Aslab</span>
                <span class="text-sm font-semibold">{{ $aslab->kode_asisten ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-purple-300">Skor Akhir (Yi)</span>
                <!-- Menggunakan pengaman jika data bernilai null / belum dinilai -->
                <span class="text-sm font-semibold text-amber-300 font-mono">
                    {{ $myResult ? ($myResult['grand_total'] ?? '0.00') : '0.00' }}
                </span>
            </div>
            <div>
                <span class="block text-purple-300">Kategori Rekomendasi</span>
                @if($myResult && !empty($myResult['kategori']))
                    <span class="inline-block mt-0.5 px-2.5 py-0.5 text-xs font-bold rounded-lg {{ $myResult['kategori'] === 'Pengembangan' ? 'bg-emerald-500/20 text-emerald-200 border border-emerald-400/40' : 'bg-amber-500/20 text-amber-200 border border-amber-400/40' }}">
                        {{ $myResult['kategori'] }}
                    </span>
                @else
                    <span class="text-sm font-semibold text-purple-200">-</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Rincian Gap Per Kriteria secara Dinamis -->
    <div class="space-y-6">
        @if($myResult && isset($myResult['kriterias']))
            @foreach($myResult['kriterias'] as $kId => $kriteria)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-purple-50/50 flex justify-between items-center border-b border-slate-100">
                        <h4 class="text-sm font-bold text-purple-900 uppercase">{{ $kriteria['nama_kriteria'] }}</h4>
                        <span class="text-xs font-bold text-purple-700">Nilai Total: {{ $kriteria['total'] }}</span>
                    </div>

                    <div class="p-6 divide-y divide-slate-100">
                        @if(isset($kriteria['details']))
                            @foreach($kriteria['details'] as $detail)
                                <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between text-sm">
                                    <div>
                                        <span class="font-semibold text-slate-800 block">{{ $detail['nama_sub'] }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wide">{{ $detail['jenis'] }} factor</span>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="text-center">
                                            <span class="text-slate-400 block text-[10px]">Aktual vs Target</span>
                                            <span class="font-bold text-slate-700">{{ $detail['aktual'] }} / {{ $detail['target'] }}</span>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-slate-400 block text-[10px]">Selisih (GAP)</span>
                                            <span class="font-bold {{ $detail['gap'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $detail['gap'] > 0 ? '+'.$detail['gap'] : $detail['gap'] }}
                                            </span>
                                        </div>
                                        <div class="text-center bg-purple-100 px-3 py-1.5 rounded-xl">
                                            <span class="text-purple-400 block text-[9px] font-bold">Bobot</span>
                                            <span class="font-bold text-purple-700">{{ $detail['bobot_gap'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <!-- Pesan jika belum ada rincian evaluasi pada periode yang dipilih -->
            <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center shadow-sm text-slate-400">
                <i data-lucide="info" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                Belum ada rincian evaluasi untuk asisten ini pada {{ $currentSemester->nama_semester ?? 'periode ini' }}.
            </div>
        @endif
    </div>
</div>
@endsection
