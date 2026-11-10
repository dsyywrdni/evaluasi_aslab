@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Blok Tombol Kembali -->
    <div>
        <a href="{{ route('admin.kriteria.index') }}" class="inline-flex items-center gap-2 text-sm text-purple-700 hover:text-purple-900 font-medium transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <!-- Blok Utama Form Card -->
    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div>
            <h3 class="text-lg font-bold text-purple-900">Ubah Kriteria Utama</h3>
            <p class="text-xs text-slate-400">Silakan ubah formulir di bawah ini dengan lengkap untuk memperbarui parameter kriteria.</p>
        </div>

        <!-- Validasi Kesalahan -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs p-4 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.kriteria.update', $kriteria->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Input Nama Kriteria -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600 uppercase">Nama Kriteria</label>
                <input type="text" name="nama_kriteria" value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition" placeholder="Contoh: Aspek Kehadiran">
            </div>

            <!-- Input Persentase Kriteria -->
            <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-600 uppercase">Bobot Persentase Kriteria (%)</label>
                <input type="number" name="persentase_kriteria" value="{{ old('persentase_kriteria', $kriteria->persentase_kriteria) }}" required min="1" max="100" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition" placeholder="Contoh: 40">
                <span class="text-[10px] text-slate-400 mt-1 block">Kontribusi kriteria ini terhadap akumulasi nilai akhir (total seluruh kriteria harus 100%).</span>
            </div>

            <!-- Input Bobot CF & SF -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                <!-- Input CF -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase">Bobot Core Factor (CF) %</label>
                    <input type="number" name="bobot_cf" value="{{ old('bobot_cf', $kriteria->bobot_cf) }}" required min="0" max="100" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition">
                </div>
                <!-- Input SF -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600 uppercase">Bobot Secondary Factor (SF) %</label>
                    <input type="number" name="bobot_sf" value="{{ old('bobot_sf', $kriteria->bobot_sf) }}" required min="0" max="100" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100 transition">
                </div>
            </div>
            <span class="text-[10px] text-slate-400 block -mt-2">Ketentuan rasio teori: Jumlah kombinasi CF + SF wajib tepat 100% (Misal: 60% dan 40%).</span>

            <!-- Tombol Submit -->
            <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-purple-100 transition">
                Perbarui Kriteria
            </button>
        </form>
    </div>

</div>
@endsection