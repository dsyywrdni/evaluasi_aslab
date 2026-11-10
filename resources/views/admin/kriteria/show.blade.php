@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    {{-- Tombol Kembali --}}
    <div>
        <a href="{{ route('admin.kriteria.index') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-purple-700 hover:text-purple-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Kriteria
        </a>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl">
        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Alert Error --}}
    @if(isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 text-sm px-4 py-3 rounded-xl space-y-1">
        <div class="font-bold flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> Terjadi kesalahan:
        </div>
        <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Header Info Kriteria --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-100">
                Kriteria Utama
            </span>
            <h2 class="text-xl font-bold text-slate-800 mt-2">{{ $kriteria->nama_kriteria }}</h2>
            <p class="text-xs text-slate-500 mt-1">
                Kelola sub-kriteria dan definisikan indikator / rubrik penilaian skor 1–5 secara terpadu.
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 flex-wrap">
            <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-800 border border-purple-100">
                Bobot: <strong class="text-purple-900">{{ $kriteria->persentase_kriteria }}%</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-800 border border-purple-100">
                CF: <strong class="text-purple-900">{{ $kriteria->bobot_cf }}%</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-800 border border-purple-100">
                SF: <strong class="text-purple-900">{{ $kriteria->bobot_sf }}%</strong>
            </span>
        </div>
    </div>

    {{-- Daftar Sub-Kriteria & Rubrik --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-purple-600"></i>
                Daftar Sub-Kriteria & Rubrik ({{ $kriteria->subKriterias->count() }})
            </h3>
            <span class="text-xs text-slate-400">
                Klik tombol <strong>Edit Sub & Rubrik</strong> untuk mengubah data & deskripsi
            </span>
        </div>

        @forelse($kriteria->subKriterias as $sub)
        @php
            $rubrikCount = $sub->rubrikPenilaians->count();
            $isOpen = session('opened_sub_id') == $sub->id;
            $skorLabels = [
                1 => 'Sangat Kurang',
                2 => 'Kurang',
                3 => 'Cukup',
                4 => 'Baik',
                5 => 'Sangat Baik'
            ];
        @endphp
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition hover:border-purple-200">
            {{-- Header Row Sub-Kriteria --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-5 py-4 gap-3 bg-white">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0 border border-purple-200">
                        {{ $loop->iteration }}
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 truncate">{{ $sub->nama_sub }}</h4>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $sub->jenis === 'core' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $sub->jenis === 'core' ? 'Core Factor (CF)' : 'Secondary Factor (SF)' }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium">
                                Target: <strong class="text-purple-800 font-bold">{{ $sub->nilai_target }}</strong>
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-md font-semibold {{ $rubrikCount === 5 ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                <i data-lucide="{{ $rubrikCount === 5 ? 'check-circle-2' : 'alert-circle' }}" class="w-3 h-3 inline mr-1"></i>
                                Rubrik: {{ $rubrikCount }}/5 Terisi
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                    <button type="button"
                        onclick="toggleSubPanel({{ $sub->id }})"
                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-2 rounded-xl border border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100 hover:border-purple-300 transition">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span id="btn-text-{{ $sub->id }}">{{ $isOpen ? 'Tutup Form' : 'Edit Sub & Rubrik' }}</span>
                    </button>

                    <form action="{{ route('admin.sub_kriteria.destroy', $sub->id) }}" method="POST"
                          onsubmit="return confirm('Hapus sub-kriteria ini beserta seluruh rubriknya?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-9 h-9 flex items-center justify-center rounded-xl text-rose-500 hover:bg-rose-50 hover:text-rose-700 border border-transparent hover:border-rose-200 transition"
                            title="Hapus Sub-Kriteria">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Ringkasan Rubrik Singkat (Jika Panel Tertutup) --}}
            <div id="preview-bar-{{ $sub->id }}" class="{{ $isOpen ? 'hidden' : '' }} px-5 py-2.5 bg-slate-50/70 border-t border-slate-100 text-xs flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 overflow-hidden text-slate-500 truncate">
                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-purple-600 shrink-0"></i>
                    @if($rubrikCount > 0)
                        <span class="truncate">
                            @foreach($sub->rubrikPenilaians as $r)
                                <strong class="text-slate-700 font-semibold">[{{ $r->skor }}]</strong> {{ \Illuminate\Support\Str::limit($r->deskripsi, 25) }}@if(!$loop->last) &bull; @endif
                            @endforeach
                        </span>
                    @else
                        <span class="text-amber-700 italic">Deskripsi rubrik 1–5 belum diisi. Klik "Edit Sub & Rubrik" untuk mengisi.</span>
                    @endif
                </div>
                <button type="button" onclick="toggleSubPanel({{ $sub->id }})" class="text-purple-700 font-bold hover:underline shrink-0 text-[11px]">
                    {{ $rubrikCount > 0 ? 'Lihat / Ubah' : 'Isi Sekarang' }} &rarr;
                </button>
            </div>

            {{-- Panel Edit Sub-Kriteria + Rubrik (Senada, Serasi, Tidak Warna-Warni) --}}
            <div id="edit-panel-{{ $sub->id }}" class="{{ $isOpen ? '' : 'hidden' }} border-t border-purple-100 bg-purple-50/20 px-6 py-5">
                <form action="{{ route('admin.sub_kriteria.update', $sub->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="bg-white p-4 rounded-xl border border-purple-100 space-y-4">
                        <div class="font-bold text-xs uppercase text-purple-900 tracking-wide flex items-center gap-1.5 pb-2 border-b border-slate-100">
                            <i data-lucide="settings" class="w-3.5 h-3.5 text-purple-600"></i>
                            Parameter Sub-Kriteria
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700">Nama Sub-Kriteria</label>
                            <input type="text" name="nama_sub" value="{{ old('nama_sub', $sub->nama_sub) }}" required
                                class="w-full px-3.5 py-2.5 border border-slate-200 bg-white rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-700">Nilai Target (Standar)</label>
                                <select name="nilai_target" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 bg-white rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition font-medium">
                                    <option value="5" @selected($sub->nilai_target == 5)>5 (Sangat Baik)</option>
                                    <option value="4" @selected($sub->nilai_target == 4)>4 (Baik)</option>
                                    <option value="3" @selected($sub->nilai_target == 3)>3 (Cukup)</option>
                                    <option value="2" @selected($sub->nilai_target == 2)>2 (Kurang)</option>
                                    <option value="1" @selected($sub->nilai_target == 1)>1 (Sangat Kurang)</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-700">Tipe Pengelompokkan</label>
                                <select name="jenis" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 bg-white rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition font-medium">
                                    <option value="core" @selected($sub->jenis === 'core')>Core Factor (CF)</option>
                                    <option value="secondary" @selected($sub->jenis === 'secondary')>Secondary Factor (SF)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section Rubrik Penilaian (Tema Senada: Ungu & Slate Elegan) --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-xs font-bold text-purple-950 uppercase tracking-wide flex items-center gap-1.5">
                                    <i data-lucide="book-open" class="w-3.5 h-3.5 text-purple-600"></i>
                                    Deskripsi Rubrik Penilaian (Skor 1 – 5)
                                </h5>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Deskripsi panduan ini akan muncul pada role Kepala Lab saat menilai asisten.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            @for($skor = 1; $skor <= 5; $skor++)
                                @php
                                    $rubrikItem = $sub->rubrikPenilaians->firstWhere('skor', $skor);
                                @endphp
                                <div class="bg-white border border-purple-100 rounded-xl p-3.5 space-y-2 shadow-xs transition focus-within:border-purple-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-md bg-purple-100 text-purple-800 font-extrabold text-xs flex items-center justify-center border border-purple-200">
                                                {{ $skor }}
                                            </span>
                                            <span class="text-xs font-bold text-slate-800">
                                                Skor {{ $skor }} &mdash; {{ $skorLabels[$skor] }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">Indikator capaian</span>
                                    </div>
                                    <textarea name="deskripsi[{{ $skor }}]"
                                        rows="2"
                                        placeholder="Tuliskan indikator perilaku / kondisi asisten untuk skor {{ $skor }} ({{ $skorLabels[$skor] }})..."
                                        class="w-full text-xs font-normal text-slate-700 bg-slate-50/60 border border-slate-200 rounded-lg px-3 py-2 focus:bg-white focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition resize-none leading-relaxed">{{ $rubrikItem?->deskripsi ?? '' }}</textarea>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button"
                            onclick="toggleSubPanel({{ $sub->id }})"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-md shadow-purple-200 transition">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            Simpan Perubahan & Rubrik
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white p-10 rounded-2xl border border-slate-200 text-center shadow-sm text-slate-400">
            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
            Belum ada sub-kriteria. Gunakan formulir di bawah untuk menambahkan sub-kriteria baru.
        </div>
        @endforelse
    </div>

    {{-- Form Tambah Sub-Kriteria Baru (Lengkap dengan Rubrik Sekaligus) --}}
    <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200 shadow-sm space-y-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-100">
                Formulir
            </span>
            <h3 class="text-base font-bold text-slate-800 mt-2">Tambah Sub-Kriteria Baru</h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Tambahkan parameter sub-kriteria untuk: <strong class="text-purple-700">{{ $kriteria->nama_kriteria }}</strong>. Anda juga bisa langsung mengisi rubrik penilaiannya.
            </p>
        </div>

        <form action="{{ route('admin.sub_kriteria.store', $kriteria->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-700">Nama Sub-Kriteria</label>
                <input type="text" name="nama_sub" required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition"
                    placeholder="Contoh: Kehadiran kelas praktikum atau ketepatan waktu">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Nilai Target (Standar)</label>
                    <select name="nilai_target" required
                        class="w-full px-4 py-3 border border-slate-200 bg-white rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition font-medium">
                        <option value="5">5 (Sangat Baik)</option>
                        <option value="4" selected>4 (Baik)</option>
                        <option value="3">3 (Cukup)</option>
                        <option value="2">2 (Kurang)</option>
                        <option value="1">1 (Sangat Kurang)</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Tipe Pengelompokkan</label>
                    <select name="jenis" required
                        class="w-full px-4 py-3 border border-slate-200 bg-white rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition font-medium">
                        <option value="core">Core Factor (CF)</option>
                        <option value="secondary">Secondary Factor (SF)</option>
                    </select>
                </div>
            </div>

            {{-- Accordion Opsional: Isi Rubrik Sekaligus --}}
            <div class="border border-purple-100 rounded-xl overflow-hidden bg-purple-50/20">
                <button type="button" onclick="toggleNewRubrik()"
                    class="w-full px-4 py-3 flex items-center justify-between text-left text-xs font-bold text-purple-900 hover:bg-purple-50/60 transition">
                    <span class="flex items-center gap-2">
                        <i data-lucide="book-open" class="w-3.5 h-3.5 text-purple-600"></i>
                        Isi Deskripsi Rubrik Penilaian Sekaligus (Skor 1–5)
                        <span class="text-[10px] font-normal text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Bisa diisi nanti</span>
                    </span>
                    <i data-lucide="chevron-down" id="new-rubrik-icon" class="w-4 h-4 text-purple-600 transition-transform"></i>
                </button>

                <div id="new-rubrik-section" class="hidden p-4 pt-1 space-y-2.5 border-t border-purple-100 bg-white">
                    @for($skor = 1; $skor <= 5; $skor++)
                        @php
                            $labels = [1 => 'Sangat Kurang', 2 => 'Kurang', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik'];
                        @endphp
                        <div class="border border-purple-100/80 rounded-xl p-3 bg-purple-50/10 space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded bg-purple-100 text-purple-800 font-extrabold text-[11px] flex items-center justify-center">
                                    {{ $skor }}
                                </span>
                                <span class="text-xs font-bold text-slate-800">Skor {{ $skor }} &mdash; {{ $labels[$skor] }}</span>
                            </div>
                            <textarea name="deskripsi[{{ $skor }}]" rows="2"
                                placeholder="Indikator asisten untuk skor {{ $skor }}..."
                                class="w-full text-xs font-normal text-slate-700 bg-white border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition resize-none leading-relaxed"></textarea>
                        </div>
                    @endfor
                </div>
            </div>

            <button type="submit"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-purple-100 transition flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Simpan Sub-Kriteria Baru
            </button>
        </form>
    </div>

</div>

<script>
function toggleSubPanel(id) {
    const panel = document.getElementById('edit-panel-' + id);
    const preview = document.getElementById('preview-bar-' + id);
    const btnText = document.getElementById('btn-text-' + id);

    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        if (preview) preview.classList.add('hidden');
        if (btnText) btnText.textContent = 'Tutup Form';
    } else {
        panel.classList.add('hidden');
        if (preview) preview.classList.remove('hidden');
        if (btnText) btnText.textContent = 'Edit Sub & Rubrik';
    }
}

function toggleNewRubrik() {
    const sec = document.getElementById('new-rubrik-section');
    const ico = document.getElementById('new-rubrik-icon');
    sec.classList.toggle('hidden');
    ico.classList.toggle('rotate-180');
}
</script>
@endsection
