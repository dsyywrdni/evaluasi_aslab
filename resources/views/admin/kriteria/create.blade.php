@extends('layouts.app')
@section('content')
<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    <a href="{{ route('admin.kriteria.index') }}"
        style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#7c3aed;text-decoration:none;width:fit-content;"
        onmouseover="this.style.color='#5b21b6'" onmouseout="this.style.color='#7c3aed'">
        <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Kriteria
    </a>

    <div class="card" style="padding:28px 32px;">
        <div style="margin-bottom:22px;padding-bottom:16px;border-bottom:1px solid #f3f0ff;">
            <h3 style="font-size:17px;font-weight:800;color:#2e1065;margin:0 0 4px;">Tambah Kriteria Utama</h3>
            <p style="font-size:12px;color:#94a3b8;margin:0;">Buat kriteria evaluasi beserta proporsi bobot perhitungannya.</p>
        </div>

        @if($errors->any())
        <div class="alert-error" style="margin-bottom:16px;"><i data-lucide="alert-circle" style="width:15px;height:15px;"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.kriteria.store') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div>
                <label class="form-label">Nama Kriteria</label>
                <input type="text" name="nama_kriteria" required class="form-input" placeholder="Contoh: Aspek Kehadiran">
            </div>
            <div>
                <label class="form-label">Bobot Persentase Kriteria (%)</label>
                <input type="number" name="persentase_kriteria" required min="1" max="100" class="form-input" placeholder="Contoh: 40">
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Kontribusi kriteria ini terhadap nilai akhir. Total seluruh kriteria harus 100%.</p>
            </div>
            <div style="border-top:1px solid #f3f0ff;padding-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">Bobot Core Factor (CF) %</label>
                    <input type="number" name="bobot_cf" required value="60" min="1" max="100" class="form-input">
                </div>
                <div>
                    <label class="form-label">Bobot Secondary Factor (SF) %</label>
                    <input type="number" name="bobot_sf" required value="40" min="1" max="100" class="form-input">
                </div>
            </div>
            <p style="font-size:11px;color:#94a3b8;margin:-8px 0 0;">CF + SF harus tepat 100% (misal: 60% + 40%).</p>
            <button type="submit" class="btn-primary" style="width:100%;margin-top:4px;">
                <i data-lucide="save" style="width:15px;height:15px;"></i> Simpan Kriteria
            </button>
        </form>
    </div>
</div>
@endsection
