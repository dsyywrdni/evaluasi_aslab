<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Asisten - STMIK Widya Cipta Dharma</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-labcom.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Poppins"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700 !important;
        }

        /* ================= ATURAN OPTIMASI KHUSUS SAAT DICETAK KERTAS ================= */
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Hilangkan bayangan, border, dan padding luar agar murni kertas putih */
            .print-card {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            /* Perkecil font dan rapatkan kolom tabel agar muat A4 lanskap */
            table {
                width: 100% !important;
                font-size: 9px !important;
                table-layout: auto !important;
            }

            th {
                font-size: 8px !important;
                white-space: normal !important;
                word-wrap: break-word !important;
                line-height: 1.2 !important;
                padding: 6px 4px !important;
            }

            td {
                padding: 6px 6px !important;
                border: 1px solid #cbd5e1 !important;
            }

            /* Paksa output ke ukuran kertas A4 lanskap */
            @page {
                size: A4 landscape;
                margin: 1.2cm 1cm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 p-8">

    @php
        // Logika penerjemah bulan ke Bahasa Indonesia
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggalResmi = now()->format('d') . ' ' . $bulanIndo[(int)now()->format('m')] . ' ' . now()->format('Y');
    @endphp

    <!-- Kontainer Laporan -->
    <div class="max-w-6xl mx-auto bg-white p-12 rounded-lg shadow-md border border-slate-200 print-card">

        <!-- ================= KOP SURAT RESMI STMIK WICIDA ================= -->
        <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
            <!-- Logo Wicida -->
            <div class="w-16 h-16 shrink-0">
                <img src="{{ asset('images/logo-labcom.png') }}"
                     alt="Logo Labcom Wicida"
                     class="w-full h-full object-contain">
            </div>

            <!-- Info Kampus -->
            <div class="text-center flex-1 pr-12">
                <h1 class="text-sm font-bold uppercase tracking-wider text-slate-800">
                    SEKOLAH TINGGI MANAJEMEN INFORMATIKA DAN KOMPUTER
                </h1>
                <h2 class="text-lg font-black uppercase tracking-widest text-purple-900">
                    WIDYA CIPTA DHARMA
                </h2>
                <p class="text-[9px] text-slate-500 font-medium mt-1 leading-relaxed">
                    Jl. M. Yamin No. 25 Samarinda - Kalimantan Timur 75123 Telp. 0541 - 736071 Fax. 203492,734468 <br>
                    E-mail : wicida@wicida.ac.id
                </p>
            </div>
        </div>

        <!-- ================= JUDUL LAPORAN ================= -->
        <div class="text-center my-6 space-y-1">
            <h3 class="text-sm font-bold uppercase underline tracking-wide">
                LAPORAN HASIL EVALUASI KINERJA ASISTEN LABORATORIUM
            </h3>
            <p class="text-xs text-slate-600 font-medium">
                Periode Penilaian: <strong>{{ $semesterAktif->nama_semester ?? '-' }}</strong>
            </p>
        </div>

        <!-- ================= TABEL HASIL PENILAIAN LANSKAP ================= -->
        <div class="mt-6">
            <table class="w-full text-left border border-collapse border-slate-300 text-[10px] table-auto">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-300">
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-12">Peringkat</th>
                        <th class="py-2.5 px-3 border border-slate-300 text-center w-20">Kode Aslab</th>
                        <th class="py-2.5 px-3 border border-slate-300">Nama Lengkap</th>
                        <th class="py-2.5 px-3 border border-slate-300 w-24">NIM</th>

                        <!-- Kolom kriteria dinamis -->
                        @foreach($kriterias as $kriteria)
                            <th class="py-2.5 px-2 border border-slate-300 text-center max-w-[120px]">
                                Rerata {{ $kriteria->nama_kriteria }}
                            </th>
                        @endforeach

                        <th class="py-2.5 px-3 border border-slate-300 text-center bg-slate-50 font-bold w-20">
                            Skor Akhir (Yi)
                        </th>
                        <th class="py-2.5 px-3 border border-slate-300 text-center font-bold w-28">
                            Kategori
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rankings as $row)
                        <tr class="border-b border-slate-300">
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-semibold">
                                {{ $row['rank'] }}
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-bold tracking-wider text-purple-900">
                                {{ $row['aslab']->kode_asisten }}
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 font-semibold">
                                {{ $row['aslab']->name }}
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-slate-600">
                                {{ $row['aslab']->nim }}
                            </td>

                            <!-- Nilai kriteria -->
                            @foreach($kriterias as $kriteria)
                                @php
                                    $score = $row['kriterias'][$kriteria->id]['total'] ?? 0;
                                @endphp
                                <td class="py-2.5 px-3 border border-slate-300 text-center font-medium text-slate-700">
                                    {{ number_format($score, 2) }}
                                </td>
                            @endforeach

                            <td class="py-2.5 px-3 border border-slate-300 text-center font-bold text-slate-900 bg-slate-50/50">
                                {{ number_format($row['grand_total'], 3) }}
                            </td>
                            <td class="py-2.5 px-3 border border-slate-300 text-center font-bold {{ ($row['kategori'] ?? '') === 'Pengembangan' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $row['kategori'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 5 + $kriterias->count() + 1 }}" class="py-8 text-center text-slate-400">
                                Belum ada data evaluasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ================= KETERANGAN RENTANG NILAI LAPORAN ================= -->
        <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded text-[9px] text-slate-700 leading-normal">
            <p class="font-bold uppercase tracking-wide text-slate-800 mb-1">
                Keterangan Rentang Nilai Akhir & Kategori Evaluasi:
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <div>• <strong>4,50 – 5,00</strong> : Pengembangan</div>
                <div>• <strong>4,00 – 4,49</strong> : Pengembangan</div>
                <div>• <strong>3,50 – 3,99</strong> : Pelatihan</div>
                <div>• <strong>&lt; 3,50</strong> : Pelatihan</div>
            </div>
        </div>

        <!-- ================= KOLOM TANDA TANGAN KEPALA LAB ================= -->
        <div class="mt-8 flex justify-end text-xs leading-relaxed">
            <div class="w-64 text-center space-y-1">
                <p>Samarinda, {{ $tanggalResmi }}</p>
                <p class="font-semibold">Mengetahui,</p>
                <p class="font-bold uppercase text-slate-800">
                    Kepala Laboratorium Komputer
                </p>

                <!-- Area tanda tangan -->
                <div class="h-16"></div>

                <!-- Nama Kepala Lab -->
                <p class="font-bold underline text-slate-800 text-sm">
                    Ivan Haristyawan, S.T, M.M
                </p>
                <p class="text-slate-500 font-medium">NIK.23.07.1.118</p>
            </div>
        </div>

    </div>

    <!-- Script Otomatis Membuka Dialog Cetak -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
