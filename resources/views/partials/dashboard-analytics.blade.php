<div style="max-width:1200px;margin:0 auto;display:flex;flex-direction:column;gap:24px;">

    {{-- ===== HERO BANNER ===== --}}
    <div style="background:linear-gradient(135deg,#3b0764 0%,#5b21b6 55%,#7c3aed 100%);border-radius:20px;padding:32px 36px;position:relative;overflow:hidden;box-shadow:0 8px 32px rgba(109,40,217,.3);">
        <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.05);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-60px;right:60px;width:150px;height:150px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
        <div style="position:relative;z-index:1;">
            <div style="font-size:11px;font-weight:700;color:#c4b5fd;letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px;">{{ $judul }} · STMIK Widya Cipta Dharma</div>
            <h2 style="font-size:24px;font-weight:800;color:#fff;margin:0 0 8px;letter-spacing:-.02em;">Dashboard Evaluasi Kinerja Asisten</h2>
            <p style="font-size:13px;color:#c4b5fd;margin:0;">
                Periode ditinjau: <strong style="color:#fff;">{{ $currentSemester->nama_semester ?? '-' }}</strong>
                <span style="margin-left:6px;font-size:11px;color:#a78bfa;">{{ $isArchive ? '(arsip — hanya baca)' : '(semester aktif)' }}</span>
            </p>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
        @php
            $cards = [
                ['Asisten Periode Ini', $totalAslab,                    'users',        '#ede9fe','#7c3aed','#f5f3ff'],
                ['Sudah Dinilai',       $totalDinilai.' / '.$totalAslab,'check-circle', '#dcfce7','#15803d','#f0fdf4'],
                ['Total Kriteria',      $totalKriteria,                  'list-todo',    '#dbeafe','#1d4ed8','#eff6ff'],
                ['Rerata Skor (Yi)',    number_format($selectedAvgScore,3),'award',      '#fef3c7','#92400e','#fffbeb'],
            ];
        @endphp
        @foreach($cards as [$label,$value,$icon,$iconBg,$iconColor,$bg])
        <div style="background:{{ $bg }};border-radius:16px;border:1.5px solid rgba(0,0,0,.04);padding:20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $iconBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-lucide="{{ $icon }}" style="width:20px;height:20px;color:{{ $iconColor }};"></i>
            </div>
            <div>
                <div style="font-size:10.5px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;">{{ $label }}</div>
                <div style="font-size:22px;font-weight:800;color:#1e293b;margin-top:2px;letter-spacing:-.02em;">{{ $value }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===== CHART TREN ===== --}}
    <div style="background:#fff;border-radius:18px;border:1.5px solid #ede9fe;padding:24px;box-shadow:0 2px 8px rgba(109,40,217,.06);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;gap:16px;border-bottom:1px solid #f3f0ff;padding-bottom:16px;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:#2e1065;margin:0 0 3px;">Grafik Tren Penilaian Antar Periode</h3>
                <p style="font-size:12px;color:#94a3b8;margin:0;">Rata-rata evaluasi seluruh asisten dari semester ke semester</p>
            </div>
            <div style="display:flex;gap:4px;background:#f1f5f9;padding:4px;border-radius:10px;">
                <button type="button" id="btnAvg" onclick="toggleMetric('avg')"
                    style="font-family:'Poppins',sans-serif;font-size:11.5px;font-weight:700;padding:6px 12px;border-radius:7px;background:#fff;color:#4c1d95;border:none;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.1);transition:all .15s;">
                    Rata-Rata
                </button>
                <button type="button" id="btnBoth" onclick="toggleMetric('both')"
                    style="font-family:'Poppins',sans-serif;font-size:11.5px;font-weight:600;padding:6px 12px;border-radius:7px;background:transparent;color:#64748b;border:none;cursor:pointer;transition:all .15s;">
                    Rata-Rata & Tertinggi
                </button>
            </div>
        </div>
        <div style="height:260px;position:relative;"><canvas id="trendChart"></canvas></div>
    </div>

    {{-- ===== KRITERIA & TOP 3 ===== --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        {{-- Kriteria --}}
        <div style="background:#fff;border-radius:18px;border:1.5px solid #ede9fe;padding:24px;box-shadow:0 2px 8px rgba(109,40,217,.06);">
            <div style="border-bottom:1px solid #f3f0ff;padding-bottom:14px;margin-bottom:16px;">
                <h3 style="font-size:15px;font-weight:700;color:#2e1065;margin:0 0 4px;">Capaian per Kriteria</h3>
                <p style="font-size:12px;color:#94a3b8;margin:0 0 12px;">Rerata nilai tiap kriteria per periode</p>
                <div style="display:flex;flex-wrap:wrap;gap:4px;background:#f1f5f9;padding:4px;border-radius:10px;width:fit-content;">
                    @foreach($criteriaData as $i => $c)
                    <button type="button" data-ktab="{{ $i }}" onclick="renderKriteria({{ $i }})"
                        style="font-family:'Poppins',sans-serif;font-size:11px;font-weight:600;padding:5px 10px;border-radius:7px;border:none;cursor:pointer;transition:all .15s;">
                        {{ \Illuminate\Support\Str::words($c['nama'], 1, '') }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f8f7ff;border-radius:10px;margin-bottom:14px;font-size:12px;">
                <span style="font-weight:600;color:#475569;">Aspek: <strong id="kriteriaLabel" style="color:#6d28d9;">—</strong></span>
                <span style="color:#94a3b8;">Skala 1.00 – 5.00</span>
            </div>
            <div id="kriteriaRows" style="display:flex;flex-direction:column;gap:14px;"></div>
        </div>

        {{-- Top 3 --}}
        <div style="background:#fff;border-radius:18px;border:1.5px solid #ede9fe;padding:24px;box-shadow:0 2px 8px rgba(109,40,217,.06);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #f3f0ff;padding-bottom:14px;margin-bottom:16px;">
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#2e1065;margin:0 0 3px;">Peringkat Terbaik</h3>
                    <p style="font-size:12px;color:#94a3b8;margin:0;">{{ $currentSemester->nama_semester ?? '-' }} — Top 3 Profile Matching</p>
                </div>
                <a href="{{ route($hasilRoute) }}" style="font-size:12px;font-weight:700;color:#7c3aed;text-decoration:none;display:flex;align-items:center;gap:4px;flex-shrink:0;">
                    Lihat Semua <i data-lucide="arrow-right" style="width:13px;height:13px;"></i>
                </a>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @forelse($topThree as $idx => $row)
                @php
                    $n = $idx + 1;
                    $rankColors = [
                        1 => ['bg'=>'#fbbf24','text'=>'#fff','ring'=>'#fde68a'],
                        2 => ['bg'=>'#94a3b8','text'=>'#fff','ring'=>'#e2e8f0'],
                        3 => ['bg'=>'#b45309','text'=>'#fff','ring'=>'#fde68a'],
                    ];
                    $rc = $rankColors[$n] ?? ['bg'=>'#e2e8f0','text'=>'#475569','ring'=>'#e2e8f0'];
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;background:{{ $n===1?'#fefce8':'#f8fafc' }};border:1.5px solid {{ $n===1?'#fde68a':'#e2e8f0' }};border-radius:14px;padding:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:38px;height:38px;border-radius:50%;background:{{ $rc['bg'] }};border:2px solid {{ $rc['ring'] }};display:flex;align-items:center;justify-content:center;font-weight:900;font-size:13px;color:{{ $rc['text'] }};flex-shrink:0;">
                            #{{ $n }}
                        </div>
                        <div>
                            <div style="font-size:13.5px;font-weight:700;color:#1e293b;">{{ $row['aslab']->name }}</div>
                            <div style="font-size:11px;color:#64748b;font-weight:500;margin-top:1px;">{{ $row['aslab']->kode_asisten }} · {{ $row['aslab']->nim }}</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:9.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Skor Akhir (Yi)</div>
                        <div style="font-size:18px;font-weight:900;color:#4c1d95;font-variant-numeric:tabular-nums;letter-spacing:-.02em;">{{ number_format($row['grand_total'],3) }}</div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:40px 0;font-size:13px;color:#94a3b8;">
                    <i data-lucide="inbox" style="width:36px;height:36px;color:#cbd5e1;display:block;margin:0 auto 8px;"></i>
                    Belum ada asisten yang dinilai.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    const trend    = @json($trendData);
    const criteria = @json($criteriaData);
    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    const chartInstance = new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [
                {
                    label: 'Rata-Rata Nilai (Yi)', data: trend.avgScores,
                    borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.1)',
                    fill: true, tension: .4, borderWidth: 2.5,
                    pointBackgroundColor: '#fff', pointBorderColor: '#7c3aed', pointBorderWidth: 2.5, pointRadius: 5
                },
                {
                    label: 'Nilai Tertinggi', data: trend.topScores,
                    borderColor: '#f59e0b', borderDash: [5,4],
                    fill: false, tension: .4, borderWidth: 2, pointRadius: 4, hidden: true
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#2e1065', titleFont: {family:'Poppins',weight:'700'}, bodyFont: {family:'Poppins'} } },
            scales: {
                y: { min: 0, max: 5, ticks: { stepSize: 1, font: {family:'Poppins',size:11} }, grid: { color: '#f3f0ff' } },
                x: { grid: { display: false }, ticks: { font: {family:'Poppins',size:11} } }
            }
        }
    });

    function toggleMetric(mode) {
        chartInstance.data.datasets[1].hidden = (mode !== 'both');
        const on  = 'font-family:Poppins,sans-serif;font-size:11.5px;font-weight:700;padding:6px 12px;border-radius:7px;background:#fff;color:#4c1d95;border:none;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.1);transition:all .15s;';
        const off = 'font-family:Poppins,sans-serif;font-size:11.5px;font-weight:600;padding:6px 12px;border-radius:7px;background:transparent;color:#64748b;border:none;cursor:pointer;transition:all .15s;';
        document.getElementById('btnBoth').style.cssText = mode === 'both' ? on : off;
        document.getElementById('btnAvg').style.cssText  = mode === 'both' ? off : on;
        chartInstance.update();
    }

    function renderKriteria(i) {
        const item = criteria[i];
        if (!item) { document.getElementById('kriteriaRows').innerHTML = '<p style="font-size:12px;color:#94a3b8;">Belum ada data.</p>'; return; }
        document.getElementById('kriteriaLabel').textContent = item.nama;
        document.getElementById('kriteriaRows').innerHTML = item.scores.map(s => {
            const pct = Math.min(100, Math.max(0, (s.score / 5) * 100));
            const col  = pct >= 70 ? '#7c3aed' : pct >= 40 ? '#f59e0b' : '#ef4444';
            return `<div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                    <span style="font-size:12px;font-weight:600;color:#1e293b;">${esc(s.semester_nama)}${s.is_active?'<span style="font-size:10px;font-weight:700;color:#7c3aed;background:#ede9fe;padding:2px 8px;border-radius:99px;margin-left:6px;">Ditinjau</span>':''}</span>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;font-variant-numeric:tabular-nums;">${Number(s.score).toFixed(2)}<span style="font-size:10px;color:#94a3b8;font-weight:400;"> / 5.00</span></span>
                </div>
                <div style="background:#f1f5f9;border-radius:99px;height:8px;overflow:hidden;">
                    <div style="background:${col};height:100%;border-radius:99px;width:${pct}%;transition:width .4s;"></div>
                </div>
            </div>`;
        }).join('');

        document.querySelectorAll('[data-ktab]').forEach(b => {
            const on  = 'font-family:Poppins,sans-serif;font-size:11px;font-weight:700;padding:5px 10px;border-radius:7px;background:#fff;color:#4c1d95;border:none;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,.1);';
            const off = 'font-family:Poppins,sans-serif;font-size:11px;font-weight:600;padding:5px 10px;border-radius:7px;background:transparent;color:#64748b;border:none;cursor:pointer;';
            b.style.cssText = (+b.dataset.ktab === i) ? on : off;
        });
    }
    renderKriteria(0);
</script>
