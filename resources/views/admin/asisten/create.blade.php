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
            <h3 style="font-size:17px;font-weight:800;color:#2e1065;margin:0 0 4px;">Tambah Asisten Laboratorium</h3>
            <p style="font-size:12px;color:#94a3b8;margin:0;">Isi formulir lengkap untuk menambahkan data asisten baru.</p>
        </div>

        @if($errors->any())
        <div class="alert-error" style="margin-bottom:16px;"><i data-lucide="alert-circle" style="width:15px;height:15px;"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.asisten.store') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label class="form-label">Kode Asisten</label>
                    <input type="text" name="kode_asisten" value="{{ old('kode_asisten') }}" required class="form-input" placeholder="Contoh: A-01">
                </div>
                <div>
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim') }}" required class="form-input" placeholder="Contoh: 2343063">
                </div>
            </div>
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Nama lengkap asisten">
            </div>
            <div>
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-input" placeholder="email@wicida.ac.id">
            </div>
            <div>
                <label class="form-label">Kata Sandi <span style="font-weight:400;text-transform:none;color:#94a3b8;">(kosongkan untuk default: password123)</span></label>
                <input type="password" name="password" class="form-input" placeholder="••••••••">
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Akun asisten hanya untuk keperluan data — login hanya oleh Admin & Kalab.</p>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;margin-top:4px;">
                <i data-lucide="save" style="width:15px;height:15px;"></i> Simpan Data Asisten
            </button>
        </form>
    </div>
</div>
@endsection
