@extends('layouts.app')
@section('content')
<div style="max-width:1100px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    {{-- Page Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#2e1065;margin:0 0 4px;">Data Asisten Laboratorium</h2>
            <p style="font-size:13px;color:#94a3b8;margin:0;">Kelola dan pantau daftar asisten yang terdaftar pada semester ini.</p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
            @unless($isArchive)
            <a href="{{ route('admin.asisten.create') }}"
                style="display:inline-flex;align-items:center;gap:7px;background:#6d28d9;color:#fff;font-family:'Poppins',sans-serif;font-weight:700;font-size:13px;padding:10px 18px;border-radius:11px;text-decoration:none;box-shadow:0 4px 14px rgba(109,40,217,.25);white-space:nowrap;transition:background .15s;"
                onmouseover="this.style.background='#5b21b6'" onmouseout="this.style.background='#6d28d9'">
                <i data-lucide="user-plus" style="width:15px;height:15px;"></i> Tambah Asisten
            </a>
            @endunless
        </div>
    </div>

    {{-- Stats Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:14px;">
        <div style="background:#fff;border:1.5px solid #ede9fe;border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;color:#6d28d9;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="users" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;">Total Asisten</div>
                <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $totalSemua }}</div>
            </div>
        </div>

        <div style="background:#fff;border:1.5px solid #bbf7d0;border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;color:#15803d;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="check-circle" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:0.04em;">Aktif Bertugas</div>
                <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $totalAktif }}</div>
            </div>
        </div>

        <div style="background:#fff;border:1.5px solid #fde68a;border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <div style="width:40px;height:40px;border-radius:10px;background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="pause-circle" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:0.04em;">Cuti Semester</div>
                <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $totalCuti }}</div>
            </div>
        </div>

        <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">
            <div style="width:40px;height:40px;border-radius:10px;background:#f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="award" style="width:20px;height:20px;"></i>
            </div>
            <div>
                <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;">Purna Tugas</div>
                <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $totalAlumni }}</div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;flex-shrink:0;"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error"><i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i> {{ $errors->first() }}</div>
    @endif

    {{-- Table Card --}}
    <div class="card" style="overflow:hidden;">
        <div style="padding:16px 20px;background:#f8f7ff;border-bottom:1.5px solid #ede9fe;display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i data-lucide="users" style="width:15px;height:15px;color:#7c3aed;"></i>
                <span style="font-size:12px;font-weight:700;color:#4c1d95;text-transform:uppercase;letter-spacing:.05em;">Daftar Asisten Laboratorium</span>
            </div>
            <span style="font-size:11px;color:#64748b;font-weight:600;">
                Periode: <strong style="color:#6d28d9;">{{ $currentSemester->nama_semester ?? '-' }}</strong>
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Kode Aslab</th>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>Email</th>
                        <th style="text-align:center;">Status Keaktifan</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aslabs as $aslab)
                    <tr>
                        <td style="text-align:center;">
                            <span style="background:#ede9fe;color:#6d28d9;font-weight:700;font-size:11.5px;padding:3px 12px;border-radius:8px;letter-spacing:.04em;white-space:nowrap;">
                                {{ $aslab->kode_asisten }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:#1e293b;">{{ $aslab->name }}</td>
                        <td style="color:#64748b;">{{ $aslab->nim }}</td>
                        <td style="color:#64748b;font-size:12px;">{{ $aslab->email }}</td>
                        <td style="text-align:center;">
                            @php $st = $aslab->status_semester ?? 'aktif'; @endphp
                            <form action="{{ route('admin.asisten.status', $aslab->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <select name="status_aslab" onchange="this.form.submit()" @disabled($isArchive)
                                    style="font-family:'Poppins',sans-serif;font-size:12px;font-weight:700;padding:5px 10px;border-radius:99px;border:1.5px solid;cursor:pointer;outline:none;
                                    {{ $st==='aktif'?'background:#dcfce7;color:#15803d;border-color:#86efac;':''.($st==='cuti'?'background:#fef3c7;color:#92400e;border-color:#fcd34d;':'background:#f1f5f9;color:#475569;border-color:#cbd5e1;') }}">
                                    <option value="aktif" @selected($st==='aktif')>Aktif Bertugas</option>
                                    <option value="cuti" @selected($st==='cuti')>Cuti Semester</option>
                                    <option value="alumni" @selected($st==='alumni')>Purna Tugas</option>
                                </select>
                            </form>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:inline-flex;align-items:center;gap:6px;">
                                @unless($isArchive)
                                <a href="{{ route('admin.asisten.edit', $aslab->id) }}"
                                    style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:9px;background:#fef3c7;color:#b45309;text-decoration:none;transition:background .15s;"
                                    onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'" title="Edit">
                                    <i data-lucide="edit-2" style="width:14px;height:14px;"></i>
                                </a>
                                <form action="{{ route('admin.asisten.destroy', $aslab->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus asisten ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:9px;background:#fee2e2;color:#b91c1c;border:none;cursor:pointer;transition:background .15s;"
                                        onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Hapus">
                                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                    </button>
                                </form>
                                @else
                                <span style="font-size:11.5px;color:#94a3b8;font-style:italic;">Arsip</span>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">
                            <i data-lucide="inbox" style="width:36px;height:36px;color:#cbd5e1;display:block;margin:0 auto 10px;"></i>
                            Tidak ada data asisten pada semester ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
