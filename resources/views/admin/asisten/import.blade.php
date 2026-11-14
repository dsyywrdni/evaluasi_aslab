@extends('layouts.app')
@section('content')
<div style="max-width:640px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    <a href="{{ route('admin.asisten.index') }}"
        style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#7c3aed;text-decoration:none;width:fit-content;transition:color .15s;"
        onmouseover="this.style.color='#5b21b6'" onmouseout="this.style.color='#7c3aed'">
        <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Daftar Asisten
    </a>

    {{-- Pesan sukses --}}
    @if(session('success'))
    <div class="alert-success" style="display:flex;align-items:center;gap:8px;">
        <i data-lucide="check-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Pesan error --}}
    @if($errors->any())
    <div class="alert-error" style="display:flex;align-items:flex-start;gap:8px;">
        <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;margin-top:1px;"></i>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Form Upload --}}
    <div class="card" style="padding:28px 32px;">
        <div style="margin-bottom:22px;padding-bottom:16px;border-bottom:1px solid #f3f0ff;">
            <h3 style="font-size:17px;font-weight:800;color:#2e1065;margin:0 0 4px;">
                Import Data Asisten dari Excel
            </h3>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                Unggah file Excel untuk menambahkan atau memperbarui data asisten laboratorium sekaligus mendaftarkannya ke semester aktif.
            </p>
        </div>

        <form action="{{ route('admin.asisten.import_post') }}" enctype="multipart/form-data" method="POST"
              style="display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div>
                <label class="form-label">File Excel <span style="color:#ef4444;">*</span></label>
                <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv">
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">Format: .xlsx, .xls, atau .csv. Maks 5 MB.</p>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;margin-top:4px;display:flex;align-items:center;justify-content:center;gap:6px;">
                <i data-lucide="upload" style="width:15px;height:15px;"></i> Import Sekarang
            </button>
        </form>
    </div>

    {{-- Panduan Format --}}
    <div class="card" style="padding:24px 32px;">
        <div style="margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i data-lucide="file-spreadsheet" style="width:17px;height:17px;color:#7c3aed;"></i>
            <h4 style="font-size:14px;font-weight:700;color:#2e1065;margin:0;">Panduan Format File Excel</h4>
        </div>

        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">
            Pastikan baris pertama file adalah <strong>header kolom</strong> sesuai nama berikut (huruf kecil, tanpa spasi ekstra):
        </p>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                <thead>
                    <tr style="background:#f5f3ff;">
                        <th style="padding:8px 12px;text-align:left;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">Kolom</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">Keterangan</th>
                        <th style="padding:8px 12px;text-align:center;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">Wajib?</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;"><code>no</code></td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">Nomor urut (dipakai untuk membuat kode otomatis)</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;text-align:center;color:#64748b;">—</td>
                    </tr>
                    <tr style="background:#faf9ff;">
                        <td style="padding:7px 12px;border:1px solid #ede9fe;"><code>nama</code></td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">Nama lengkap asisten</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;text-align:center;">
                            <span class="badge-purple" style="font-size:10px;padding:2px 7px;">Wajib</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;"><code>nim</code></td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">Nomor Induk Mahasiswa</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;text-align:center;color:#64748b;">—</td>
                    </tr>
                    <tr style="background:#faf9ff;">
                        <td style="padding:7px 12px;border:1px solid #ede9fe;"><code>kode_asisten</code></td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">Kode unik asisten (mis. <code>A01</code>). Jika kosong, dibuat otomatis dari kolom <code>no</code>.</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;text-align:center;color:#64748b;">—</td>
                    </tr>
                    <tr>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;"><code>email</code></td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">Email asisten. Jika kosong, dibuat otomatis dari nama (<code>nama@wicida.ac.id</code>).</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;text-align:center;color:#64748b;">—</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top:14px;padding:10px 14px;background:#f5f3ff;border-radius:8px;border-left:3px solid #7c3aed;">
            <p style="font-size:11px;color:#5b21b6;margin:0;line-height:1.6;">
                <strong>Catatan:</strong>
                Import bersifat idempotent — aman dijalankan ulang. Asisten yang sudah ada (berdasarkan <code>kode_asisten</code> atau <code>email</code>) akan diperbarui datanya, bukan diduplikasi. Password default: <code>password123</code>.
            </p>
        </div>

        <div style="margin-top:12px;padding:10px 14px;background:#fef9c3;border-radius:8px;border-left:3px solid #ca8a04;">
            <p style="font-size:11px;color:#854d0e;margin:0;"><strong>Contoh baris data:</strong></p>
            <div style="overflow-x:auto;margin-top:6px;">
                <table style="font-size:11px;border-collapse:collapse;">
                    <tr style="background:#fef3c7;">
                        <td style="padding:4px 10px;border:1px solid #fde68a;font-weight:700;">no</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;font-weight:700;">nama</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;font-weight:700;">nim</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;font-weight:700;">kode_asisten</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;font-weight:700;">email</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 10px;border:1px solid #fde68a;">1</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;">Budi Santoso</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;">2021001</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;">A01</td>
                        <td style="padding:4px 10px;border:1px solid #fde68a;">budisantoso@wicida.ac.id</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
