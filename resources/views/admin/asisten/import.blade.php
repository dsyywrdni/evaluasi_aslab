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

        <form action="{{ route('admin.asisten.import_post') }}" enctype="multipart/form-data" method="POST" style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div>
                <label class="form-label">File Excel</label>
                <input type="file" name="excel_file" class="form-control" id="">
            </div>
            <button type="submit" class="btn-success" style="width:100%;margin-top:4px;">
                <i data-lucide="save" style="width:15px;height:15px;"></i> Import Excel
            </button>
        </form>
    </div>
</div>
@endsection
