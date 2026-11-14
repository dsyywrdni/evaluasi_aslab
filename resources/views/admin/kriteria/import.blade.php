@extends('layouts.app')
@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    <a href="{{ route('admin.kriteria.index') }}"
        style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#7c3aed;text-decoration:none;width:fit-content;transition:color .15s;"
        onmouseover="this.style.color='#5b21b6'" onmouseout="this.style.color='#7c3aed'">
        <i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Kriteria
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
                Import Kriteria &amp; Rubrik dari Excel
            </h3>
            <p style="font-size:12px;color:#94a3b8;margin:0;">
                Unggah file Excel untuk mengisi kriteria, sub-kriteria, dan rubrik penilaian secara massal ke semester aktif.
            </p>
        </div>

        <form action="{{ route('admin.kriteria.import_post') }}" enctype="multipart/form-data" method="POST"
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
            Setiap baris mewakili satu <strong>sub-kriteria</strong>. Baris pertama harus berisi header kolom berikut:
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
                    @foreach([
                        ['nama_kriteria','Nama kriteria utama (jika sama dengan baris sebelumnya, dianggap satu kriteria)','Wajib'],
                        ['persentase_kriteria','Persentase bobot kriteria (0–100)','—'],
                        ['bobot_cf','Bobot Core Factor (0–100, default 60)','—'],
                        ['bobot_sf','Bobot Secondary Factor (0–100, default 40)','—'],
                        ['nama_sub','Nama sub-kriteria','Wajib'],
                        ['nilai_target','Nilai target sub-kriteria (default 5)','—'],
                        ['jenis','Jenis: <code>core</code> atau <code>secondary</code>','—'],
                        ['deskripsi_1','Deskripsi rubrik skor 1','—'],
                        ['deskripsi_2','Deskripsi rubrik skor 2','—'],
                        ['deskripsi_3','Deskripsi rubrik skor 3','—'],
                        ['deskripsi_4','Deskripsi rubrik skor 4','—'],
                        ['deskripsi_5','Deskripsi rubrik skor 5','—'],
                    ] as $i => $col)
                    <tr @if($i % 2 === 1) style="background:#faf9ff;" @endif>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;"><code>{{ $col[0] }}</code></td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;color:#64748b;">{!! $col[1] !!}</td>
                        <td style="padding:7px 12px;border:1px solid #ede9fe;text-align:center;">
                            @if($col[2] === 'Wajib')
                                <span class="badge-purple" style="font-size:10px;padding:2px 7px;">Wajib</span>
                            @else
                                <span style="color:#94a3b8;font-size:11px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:14px;padding:10px 14px;background:#f5f3ff;border-radius:8px;border-left:3px solid #7c3aed;">
            <p style="font-size:11px;color:#5b21b6;margin:0;line-height:1.6;">
                <strong>Catatan:</strong>
                Import bersifat idempotent — aman dijalankan ulang. Kriteria/sub-kriteria yang sudah ada akan diperbarui, rubrik akan di-<em>upsert</em> berdasarkan pasangan sub-kriteria dan skor.
                Hanya bekerja pada <strong>semester aktif</strong> saat ini.
            </p>
        </div>
    </div>
</div>
@endsection
