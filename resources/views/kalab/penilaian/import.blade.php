@extends('layouts.app')
@section('content')
<div style="max-width:660px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    <a href="{{ route('kalab.penilaian.index') }}"
        style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#7c3aed;text-decoration:none;width:fit-content;transition:color .15s;"
        onmouseover="this.style.color='#5b21b6'" onmouseout="this.style.color='#7c3aed'">
        <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Daftar Penilaian
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
                Import Nilai Evaluasi dari Excel
            </h3>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                Unggah file Excel berisi nilai evaluasi asisten untuk semua sub-kriteria di semester aktif.
            </p>
        </div>

        <form action="{{ route('kalab.penilaian.import_post') }}" enctype="multipart/form-data" method="POST"
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
            Setiap baris mewakili satu asisten. Kolom pertama adalah <code>kode_asisten</code>, diikuti kolom-kolom bernama sesuai <strong>nama sub-kriteria</strong> yang ada di database (case insensitive).
        </p>

        <div style="overflow-x:auto;">
            <table style="border-collapse:collapse;font-size:12px;min-width:500px;">
                <thead>
                    <tr style="background:#f5f3ff;">
                        <th style="padding:8px 12px;text-align:left;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">kode_asisten</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">Nama Sub-Kriteria 1</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">Nama Sub-Kriteria 2</th>
                        <th style="padding:8px 12px;text-align:left;border:1px solid #e9d5ff;font-weight:700;color:#5b21b6;">...</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">A01</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">4</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">3</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">...</td>
                    </tr>
                    <tr style="background:#faf9ff;">
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">A02</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">5</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">4</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top:14px;padding:10px 14px;background:#f5f3ff;border-radius:8px;border-left:3px solid #7c3aed;">
            <p style="font-size:11px;color:#5b21b6;margin:0;line-height:1.6;">
                <strong>Catatan:</strong>
                Nama header kolom harus <strong>sama persis</strong> (case insensitive) dengan <code>nama_sub</code> sub-kriteria di database.
                Kolom yang tidak dikenali akan diabaikan. Nilai harus berupa angka <strong>1–5</strong>.
                Import bersifat idempotent — nilai yang sudah ada akan diperbarui.
            </p>
        </div>

        <div style="margin-top:12px;padding:10px 14px;background:#f0fdf4;border-radius:8px;border-left:3px solid #16a34a;">
            <p style="font-size:11px;color:#166534;margin:0;line-height:1.6;">
                <strong>Tips:</strong>
                Lihat daftar sub-kriteria yang tersedia di menu
                <a href="{{ route('admin.kriteria.index') }}" style="color:#15803d;font-weight:600;">Kriteria &amp; Sub-Kriteria</a>
                untuk memastikan nama kolom di Excel sudah sesuai.
            </p>
        </div>
    </div>
</div>
@endsection
