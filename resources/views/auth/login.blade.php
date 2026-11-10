<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SPK Kinerja Aslab LABCOM WICIDA</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-labcom.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; margin: 0; }
        h1,h2,h3,h4,h5,h6 { font-weight: 700; letter-spacing: -.02em; }
    </style>
</head>
<body style="min-height:100vh;display:flex;background:linear-gradient(135deg,#1e1b4b 0%,#3b0764 40%,#2e1065 100%);">

    {{-- Decorative blobs --}}
    <div style="position:fixed;top:-100px;left:-100px;width:400px;height:400px;background:rgba(139,92,246,.15);border-radius:50%;pointer-events:none;filter:blur(60px);"></div>
    <div style="position:fixed;bottom:-120px;right:-80px;width:350px;height:350px;background:rgba(109,40,217,.2);border-radius:50%;pointer-events:none;filter:blur(50px);"></div>

    <div style="margin:auto;width:100%;max-width:420px;padding:24px 16px;position:relative;z-index:1;">

        {{-- Card --}}
        <div style="background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.28);">

            {{-- Header Card --}}
            <div style="background:linear-gradient(135deg,#4c1d95 0%,#6d28d9 60%,#7c3aed 100%);padding:36px 32px;text-align:center;">
                <div style="width:76px;height:76px;margin:0 auto 16px;background:#fff;border-radius:20px;padding:8px;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(0,0,0,.2);border:2.5px solid rgba(196,181,253,.4);">
                    <img src="{{ asset('images/logo-labcom.png') }}" alt="Logo LABCOM WICIDA" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <h1 style="font-size:22px;font-weight:800;color:#fff;margin:0;letter-spacing:-.01em;">LABCOM WICIDA</h1>
                <p style="font-size:12px;color:#c4b5fd;margin:6px 0 0;font-weight:500;">Sistem Pendukung Keputusan Evaluasi Aslab</p>
                <p style="font-size:11px;color:#a78bfa;margin:3px 0 0;">STMIK Widya Cipta Dharma</p>
            </div>

            {{-- Form --}}
            <div style="padding:28px 32px 32px;">
                <h2 style="font-size:16px;font-weight:700;color:#1e1b4b;margin:0 0 4px;">Masuk ke Sistem</h2>
                <p style="font-size:12px;color:#94a3b8;margin:0 0 24px;">Masukkan kredensial akun Anda untuk melanjutkan</p>

                @if($errors->any())
                <div style="display:flex;align-items:flex-start;gap:10px;background:#fef2f2;border:1.5px solid #fecaca;color:#991b1b;padding:12px 14px;border-radius:12px;font-size:12.5px;margin-bottom:18px;">
                    <i data-lucide="alert-circle" style="width:15px;height:15px;flex-shrink:0;margin-top:1px;"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                    @csrf
                    <div>
                        <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Alamat Email</label>
                        <div style="position:relative;">
                            <i data-lucide="mail" style="width:15px;height:15px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none;"></i>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                placeholder="nama@wicida.ac.id"
                                style="width:100%;padding:11px 14px 11px 40px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;transition:border .18s,box-shadow .18s;background:#fff;"
                                onfocus="this.style.borderColor='#7c3aed';this.style.boxShadow='0 0 0 3px rgba(124,58,237,.12)'"
                                onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px;">Kata Sandi</label>
                        <div style="position:relative;">
                            <i data-lucide="lock" style="width:15px;height:15px;position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none;"></i>
                            <input type="password" name="password" required
                                placeholder="••••••••"
                                id="pwInput"
                                style="width:100%;padding:11px 42px 11px 40px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Poppins',sans-serif;font-size:13.5px;outline:none;transition:border .18s,box-shadow .18s;background:#fff;"
                                onfocus="this.style.borderColor='#7c3aed';this.style.boxShadow='0 0 0 3px rgba(124,58,237,.12)'"
                                onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                            <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:2px;" title="Tampilkan/Sembunyikan">
                                <i data-lucide="eye" id="eyeIcon" style="width:15px;height:15px;"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        style="display:flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;font-family:'Poppins',sans-serif;font-weight:700;font-size:14px;padding:13px;border-radius:11px;border:none;cursor:pointer;box-shadow:0 6px 20px rgba(109,40,217,.35);transition:transform .15s,box-shadow .15s;margin-top:4px;"
                        onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(109,40,217,.4)'"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 6px 20px rgba(109,40,217,.35)'">
                        <i data-lucide="log-in" style="width:15px;height:15px;"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                <div style="margin-top:20px;text-align:center;border-top:1px solid #f1f5f9;padding-top:16px;">
                    <p style="font-size:11.5px;color:#94a3b8;">Akses hanya untuk <strong style="color:#7c3aed;">Admin</strong> & <strong style="color:#7c3aed;">Kepala Laboratorium</strong></p>
                </div>
            </div>
        </div>

        <p style="text-align:center;font-size:11px;color:rgba(196,181,253,.5);margin-top:20px;">
            © {{ date('Y') }} LABCOM WICIDA · STMIK Widya Cipta Dharma
        </p>
    </div>

    <script>
        lucide.createIcons();
        let shown = false;
        function togglePw() {
            const inp = document.getElementById('pwInput');
            const ico = document.getElementById('eyeIcon');
            shown = !shown;
            inp.type = shown ? 'text' : 'password';
            ico.setAttribute('data-lucide', shown ? 'eye-off' : 'eye');
            lucide.createIcons();
        }
    </script>
</body>
</html>
