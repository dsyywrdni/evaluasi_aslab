@extends('layouts.app')
@section('content')
<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    <a href="{{ route('admin.asisten.index') }}"
        style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#7c3aed;text-decoration:none;width:fit-content;transition:color .15s;"
        onmouseover="this.style.color='#5b21b6'" onmouseout="this.style.color='#7c3aed'">
        <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Daftar Asisten
    </a>

    <div class="card" style="padding:28px 32px;">
        <div style="margin-bottom:22px;padding-bottom:16px;border-bottom:1px solid #f3f0ff;">
            <h3 style="font-size:17px;font-weight:800;color:#2e1065;margin:0 0 4px;">Ubah Data Asisten Laboratorium</h3>
            <p style="font-size:12px;color:#94a3b8;margin:0;">Perbarui informasi asisten: <strong style="color:#6d28d9;">{{ $aslab->name }}</strong></p>
        </div>

        @if($errors->any())
        <div class="alert-error" style="margin-bottom:16px;"><i data-lucide="alert-circle" style="width:15px;height:15px;"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.asisten.update', $aslab->id) }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">Kode Asisten</label>
                    <input type="text" name="kode_asisten" value="{{ old('kode_asisten', $aslab->kode_asisten) }}" required class="form-input" placeholder="A-01">
                </div>
                <div>
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim', $aslab->nim) }}" required class="form-input" placeholder="2343063">
                </div>
            </div>
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $aslab->name) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $aslab->email) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">Kata Sandi Baru <span style="font-weight:400;text-transform:none;color:#94a3b8;">(kosongkan jika tidak ingin ganti)</span></label>
                <input type="password" name="password" class="form-input" placeholder="••••••••">
            </div>
            <div>
                <label class="form-label">Status Keaktifan — {{ $currentSemester->nama_semester ?? '-' }}</label>
                <select name="status_aslab" @disabled($isArchive) class="form-input" style="background:#fff;cursor:pointer;">
                    <option value="aktif" @selected(old('status_aslab',$statusSemester)==='aktif')>Aktif Bertugas</option>
                    <option value="cuti"  @selected(old('status_aslab',$statusSemester)==='cuti')>Cuti Semester</option>
                    <option value="alumni" @selected(old('status_aslab',$statusSemester)==='alumni')>Purna Tugas / Alumni</option>
                </select>
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Status berlaku per semester dan tidak mempengaruhi arsip lama.{{ $isArchive ? ' (Arsip tidak dapat diubah.)' : '' }}</p>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;margin-top:4px;">
                <i data-lucide="save" style="width:15px;height:15px;"></i> Perbarui Data Asisten
            </button>
        </form>
    </div>
</div>
@endsection
