@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Kelola Kriteria & Rubrik Penilaian</h3>
            <p class="text-sm text-slate-500 mt-0.5">Atur kriteria utama, nilai target standar, faktor CF/SF, serta indikator rubrik penilaian.</p>
        </div>

        @unless($isArchive)
            <a href="{{ route('admin.kriteria.create') }}" class="flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-md transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Kriteria
            </a>
            <a href="{{ route('admin.kriteria.import_get') }}"
                style="display:inline-flex;align-items:center;gap:7px;background:#6d28d9;color:#fff;font-family:'Poppins',sans-serif;font-weight:700;font-size:13px;padding:10px 18px;border-radius:11px;text-decoration:none;box-shadow:0 4px 14px rgba(109,40,217,.25);white-space:nowrap;transition:background .15s;"
                onmouseover="this.style.background='#21b653'" onmouseout="this.style.background='#28d957'">
                <i data-lucide="user-plus" style="width:15px;height:15px;"></i> Import Kriteria
            </a>
        @endunless
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($kriterias->isNotEmpty() && $totalPersen != 100)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm p-4 rounded-xl">
            Total bobot kriteria pada periode ini <strong>{{ $totalPersen }}%</strong>. Harus tepat 100% agar perhitungan skor akhir (Yi) akurat.
        </div>
    @endif

    <div class="space-y-6">
        @forelse($kriterias as $kriteria)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Header Card Kriteria -->
                <div class="px-6 py-4 bg-purple-50/50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 font-extrabold text-xs flex items-center justify-center border border-purple-200">
                                {{ $loop->iteration }}
                            </span>
                            <h4 class="text-base font-bold text-slate-900">{{ $kriteria->nama_kriteria }}</h4>
                        </div>

                        <div class="flex flex-wrap gap-2.5 mt-2 text-xs text-slate-500 font-medium">
                            <span class="bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                Kontribusi: <strong class="text-purple-700">{{ $kriteria->persentase_kriteria }}%</strong>
                            </span>
                            <span class="bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                Core Factor: <strong class="text-purple-700">{{ $kriteria->bobot_cf }}%</strong>
                            </span>
                            <span class="bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                Secondary Factor: <strong class="text-purple-700">{{ $kriteria->bobot_sf }}%</strong>
                            </span>
                        </div>
                    </div>

                    @unless($isArchive)
                        <div class="flex items-end  gap-2">
                            <div>
                                <form action="{{ route('admin.sub_kriteria.import', $kriteria->id) }}" enctype="multipart/form-data" method="post">
                                    @csrf
                                    <label class="block mb-2.5 text-sm font-medium text-heading" for="small_size">Upload Excel Sub-kriteria</label>
                                    <input class="cursor-pointer bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full shadow-xs placeholder:text-body" name="excel_file" accept=".xlsx, .excel, .csv " id="small_size" type="file">
                                     <button type="submit" class="flex items-center gap-1.5 bg-purple-700 hover:bg-purple-800 text-white font-semibold text-xs px-3.5 py-2 rounded-xl shadow-sm transition" style="width:100%;margin-top:4px;display:flex;align-items:center;justify-content:center;gap:6px;">
                                        <i data-lucide="upload" style="width:15px;height:15px;"></i> Import Sub-kriteria
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('admin.kriteria.show', $kriteria->id) }}"
                               class="flex items-center gap-1.5 bg-purple-700 hover:bg-purple-800 text-white font-semibold text-xs px-3.5 py-2 rounded-xl shadow-sm transition">
                                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                                Kelola Sub & Rubrik
                            </a>

                            <a href="{{ route('admin.kriteria.edit', $kriteria->id) }}"
                               class="flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-3 py-2 rounded-xl transition">
                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                Edit
                            </a>

                            <form action="{{ route('admin.kriteria.destroy', $kriteria->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus kriteria ini beserta seluruh sub-kriteria dan rubriknya?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition"
                                    title="Hapus Kriteria">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @endunless
                </div>

                <!-- Sub-Kriterianya -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-500 text-xs font-bold uppercase bg-slate-50/60">
                                <th class="py-3 px-6">Nama Sub-Kriteria</th>
                                <th class="py-3 px-6 text-center">Nilai Target</th>
                                <th class="py-3 px-6 text-center">Faktor</th>
                                <th class="py-3 px-6 text-center">Status Rubrik</th>
                                <th class="py-3 px-6 text-center w-28">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($kriteria->subKriterias as $sub)
                                @php
                                    $rCount = $sub->rubrikPenilaians->count();
                                @endphp
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3 px-6 text-slate-800 font-semibold">
                                        {{ $sub->nama_sub }}
                                    </td>

                                    <td class="py-3 px-6 text-center font-bold text-slate-700">
                                        {{ $sub->nilai_target }}
                                    </td>

                                    <td class="py-3 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $sub->jenis === 'core' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $sub->jenis === 'core' ? 'CF' : 'SF' }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-6 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $rCount === 5 ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $rCount }}/5 Terisi
                                        </span>
                                    </td>

                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @unless($isArchive)
                                                <a href="{{ route('admin.kriteria.show', $kriteria->id) }}"
                                                   class="p-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition"
                                                   title="Edit Sub & Rubrik">
                                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                </a>

                                                <form action="{{ route('admin.sub_kriteria.destroy', $sub->id) }}" method="POST"
                                                      onsubmit="return confirm('Hapus sub-kriteria ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Hapus Sub-Kriteria">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs">
                                        Belum ada sub-kriteria. Klik tombol "Kelola Sub & Rubrik" di kanan atas card untuk menambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center shadow-sm text-slate-400">
                <i data-lucide="info" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                Tidak ada data kriteria utama. Silakan tambahkan kriteria baru.
            </div>
        @endforelse
    </div>
</div>
@endsection
