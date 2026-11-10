@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-6 pb-20">

    {{-- Header Asisten --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 border border-purple-200 shadow-sm">
                <i data-lucide="user-check" class="w-7 h-7"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-md border border-purple-100">
                        Formulir Penilaian Asisten
                    </span>
                    @if(count($evaluasis) > 0)
                        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-md border border-amber-200">
                            Mode Edit Nilai
                        </span>
                    @else
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                            Mode Beri Nilai Baru
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $aslab->name }}</h2>
                <p class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-2">
                    <span>Kode Aslab: <strong class="text-purple-700 font-semibold font-mono">{{ $aslab->kode_asisten ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>NIM: <strong class="text-slate-700 font-semibold">{{ $aslab->nim ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>Penilai: <strong class="text-slate-700 font-semibold">Kepala Laboratorium</strong></span>
                </p>
            </div>
        </div>
        <a href="{{ route('kalab.penilaian.index') }}"
           class="inline-flex items-center justify-center gap-2 text-xs font-semibold px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-purple-50 hover:text-purple-700 hover:border-purple-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Errors --}}
    @if(isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-5 py-4 rounded-xl space-y-1.5 shadow-sm">
        <div class="font-bold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
            Mohon lengkapi seluruh penilaian:
        </div>
        <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Petunjuk Singkat --}}
    <div class="flex items-start gap-3 bg-purple-50/70 border border-purple-100 text-purple-900 text-xs px-5 py-3.5 rounded-xl">
        <i data-lucide="info" class="w-4 h-4 text-purple-600 mt-0.5 shrink-0"></i>
        <div class="leading-relaxed">
            Pilihlah skala nilai <strong>1 sampai 5</strong> untuk setiap indikator sub-kriteria di bawah ini.
            Jika memerlukan acuan indikator penilaian, klik tombol <strong>"Lihat Rubrik"</strong> pada masing-masing sub-kriteria.
        </div>
    </div>

    <form id="form-penilaian" action="{{ route('kalab.penilaian.store') }}" method="POST">
        @csrf
        <input type="hidden" name="aslab_id" value="{{ $aslab->id }}">

        @php
            $subCount = 0;
            $scoreLabels = [
                1 => 'Sangat Kurang',
                2 => 'Kurang',
                3 => 'Cukup',
                4 => 'Baik',
                5 => 'Sangat Baik'
            ];
        @endphp

        <div class="space-y-6">
            @foreach($kriterias as $kriteria)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                {{-- Header Kriteria --}}
                <div class="px-6 py-4 bg-purple-50/70 border-b border-purple-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                            <i data-lucide="folder" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-base font-bold text-purple-950 truncate">{{ $kriteria->nama_kriteria }}</h3>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-purple-800 shrink-0">
                        <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-lg">Bobot: {{ $kriteria->persentase_kriteria }}%</span>
                        <span class="px-2.5 py-1 bg-white border border-purple-200 rounded-lg">CF: {{ $kriteria->bobot_cf }}% &bull; SF: {{ $kriteria->bobot_sf }}%</span>
                    </div>
                </div>

                {{-- List Sub-Kriteria --}}
                <div class="divide-y divide-slate-100">
                    @foreach($kriteria->subKriterias as $sub)
                    @php
                        $subCount++;
                        $existingVal = old("nilai.{$sub->id}", $evaluasis[$sub->id] ?? null);
                    @endphp
                    <div class="p-6 space-y-4 hover:bg-slate-50/30 transition">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                            {{-- Info Sub-Kriteria (Kiri) --}}
                            <div class="lg:col-span-6 xl:col-span-7 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-sm sm:text-base font-bold text-slate-800 leading-snug">
                                        {{ $sub->nama_sub }}
                                    </h4>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 pt-0.5">
                                    <span class="text-[11px] px-2.5 py-0.5 rounded-full font-semibold {{ $sub->jenis === 'core' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ $sub->jenis === 'core' ? 'Core Factor' : 'Secondary Factor' }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200">
                                        Target Standar: <strong class="text-purple-800 font-bold font-mono">{{ $sub->nilai_target }}</strong>
                                    </span>
                                    <button type="button"
                                        onclick="toggleRubrikKalab({{ $sub->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full border border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100 hover:border-purple-300 transition shrink-0 ml-auto sm:ml-0">
                                        <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                        <span id="rubrik-btn-text-{{ $sub->id }}">Lihat Rubrik</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Pilihan Angka Skor 1-5 (Kanan) --}}
                            <div class="lg:col-span-6 xl:col-span-5">
                                <div class="grid grid-cols-5 gap-2 sm:gap-2.5 w-full">
                                    @for($s = 1; $s <= 5; $s++)
                                    @php $isSelected = ($existingVal !== null && (int)$existingVal === $s); @endphp
                                    <div class="relative">
                                        <input type="radio" 
                                               id="nilai_{{ $sub->id }}_{{ $s }}" 
                                               name="nilai[{{ $sub->id }}]" 
                                               value="{{ $s }}"
                                               class="sr-only peer"
                                               {{ $isSelected ? 'checked' : '' }}
                                               required>
                                        <label for="nilai_{{ $sub->id }}_{{ $s }}"
                                               class="flex flex-col items-center justify-center py-2.5 px-1 rounded-xl border-2 border-slate-200 bg-white cursor-pointer select-none transition-all duration-150
                                                      hover:border-purple-400 hover:bg-purple-50/60
                                                      peer-checked:bg-purple-700 peer-checked:border-purple-700 peer-checked:text-white peer-checked:shadow-md peer-checked:shadow-purple-300/40">
                                            <span class="text-base sm:text-lg font-extrabold text-slate-800 peer-checked:text-white leading-tight transition-colors">
                                                {{ $s }}
                                            </span>
                                            <span class="text-[10px] font-medium text-slate-500 peer-checked:text-purple-100 text-center leading-tight transition-colors truncate w-full mt-0.5">
                                                {{ $scoreLabels[$s] }}
                                            </span>
                                        </label>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        {{-- Panel Drawer Rubrik Penilaian --}}
                        <div id="rubrik-kalab-{{ $sub->id }}" class="hidden pt-2">
                            <div class="bg-purple-50/40 border border-purple-100 rounded-2xl p-4 sm:p-5 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-purple-100">
                                    <p class="text-xs font-bold text-purple-950 uppercase tracking-wide flex items-center gap-2">
                                        <i data-lucide="book-open" class="w-4 h-4 text-purple-600"></i>
                                        Panduan Rubrik Penilaian: {{ $sub->nama_sub }}
                                    </p>
                                    <button type="button" onclick="toggleRubrikKalab({{ $sub->id }})" class="text-slate-400 hover:text-slate-600 text-xs flex items-center gap-1">
                                        <span>Tutup</span>
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-5 gap-2.5 pt-1">
                                    @for($skor = 1; $skor <= 5; $skor++)
                                    @php
                                        $rItem = $sub->rubrikPenilaians->firstWhere('skor', $skor);
                                        $hasCustom = !empty(trim((string)$rItem?->deskripsi));
                                    @endphp
                                    <div class="bg-white border border-purple-100 rounded-xl p-3 flex flex-col justify-between shadow-xs hover:border-purple-200 transition">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-800 font-extrabold text-xs flex items-center justify-center border border-purple-200 shrink-0">
                                                    {{ $skor }}
                                                </span>
                                                <span class="text-xs font-bold text-slate-800 truncate">
                                                    {{ $scoreLabels[$skor] }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] leading-relaxed {{ $hasCustom ? 'text-slate-600' : 'text-slate-400 italic' }}">
                                                {{ $hasCustom ? $rItem->deskripsi : 'Indikator khusus belum diatur oleh admin. Mengikuti standar skor ' . $skor . ' (' . $scoreLabels[$skor] . ').' }}
                                            </p>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Bar Bawah / Tombol Submit Sticky --}}
        <div class="mt-8 sticky bottom-4 z-30 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white/95 backdrop-blur-md rounded-2xl border border-purple-200 shadow-xl px-6 py-4">
            <div>
                <p class="text-sm font-semibold text-slate-800">
                    Progres: <strong id="filled-count" class="text-purple-700 text-base font-extrabold">0</strong> / {{ $subCount }} Sub-Kriteria Dinilai
                </p>
                <div id="progress-hint" class="text-xs text-slate-400 mt-0.5">
                    Memeriksa kelengkapan nilai...
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('kalab.penilaian.index') }}"
                   class="w-full sm:w-auto text-center px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-xs transition">
                    Batal
                </a>

                <button type="submit" id="btn-submit"
                    class="w-full sm:w-auto bg-purple-700 hover:bg-purple-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs sm:text-sm px-8 py-3 rounded-xl shadow-lg shadow-purple-200 transition flex items-center gap-2 justify-center">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Penilaian</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Toggle drawer rubrik kalab
function toggleRubrikKalab(id) {
    const el = document.getElementById('rubrik-kalab-' + id);
    const txt = document.getElementById('rubrik-btn-text-' + id);
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (txt) txt.textContent = 'Tutup Rubrik';
    } else {
        el.classList.add('hidden');
        if (txt) txt.textContent = 'Lihat Rubrik';
    }
    if (window.lucide) lucide.createIcons();
}

// Handler Counter & Submit State
(function () {
    const form      = document.getElementById('form-penilaian');
    const counter   = document.getElementById('filled-count');
    const hint      = document.getElementById('progress-hint');
    const btnSubmit = document.getElementById('btn-submit');
    const total     = {{ $subCount }};

    function update() {
        if (!form) return;
        const radios   = form.querySelectorAll('input[type="radio"]:checked');
        const checkedNames = new Set();
        radios.forEach(r => checkedNames.add(r.name));
        const count = checkedNames.size;

        if (counter) counter.textContent = count;

        const isComplete = (count >= total && total > 0);

        if (btnSubmit) {
            btnSubmit.disabled = !isComplete;
        }

        if (hint) {
            if (isComplete) {
                hint.innerHTML = '<span class="text-emerald-600 font-semibold flex items-center gap-1.5"><i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Seluruh sub-kriteria telah dinilai. Siap untuk disimpan.</span>';
            } else {
                const sisa = total - count;
                hint.innerHTML = '<span class="text-amber-600 font-medium flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> ' + sisa + ' sub-kriteria belum dinilai.</span>';
            }
            if (window.lucide) lucide.createIcons();
        }
    }

    form.querySelectorAll('input[type="radio"]').forEach(r => {
        r.addEventListener('change', update);
    });

    // Inisialisasi awal saat halaman termuat
    update();
})();
</script>
@endsection
