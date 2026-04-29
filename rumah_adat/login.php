<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    header("Location: " . $redirect);
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['act_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username/email dan password wajib diisi.";
    } else {
        $u = mysqli_real_escape_string($koneksi, $username);
        $row = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT * FROM users WHERE (username='$u' OR email='$u') LIMIT 1"
        ));
        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user_id']    = $row['id'];
            $_SESSION['username']   = $row['username'];
            $_SESSION['nama']       = $row['nama_lengkap'];
            $_SESSION['role']       = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header("Location: " . $redirect);
            }
            exit();
        } else {
            $error = "Username/email atau password salah.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['act_register'])) {
    $nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama_lengkap'] ?? ''));
    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username'] ?? ''));
    $email    = trim(mysqli_real_escape_string($koneksi, $_POST['email'] ?? ''));
    $no_hp    = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirm  = $_POST['konfirmasi_password'] ?? '';

    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $konfirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT id FROM users WHERE username='$username' OR email='$email' LIMIT 1"
        ));
        if ($cek) {
            $error = "Username atau email sudah digunakan.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($koneksi,
                "INSERT INTO users (username, email, nama_lengkap, no_hp, password, role, created_at)
                    VALUES ('$username', '$email', '$nama', '$no_hp', '$hash', 'user', NOW())"
            );
            $new_id = mysqli_insert_id($koneksi);
            $_SESSION['user_id']  = $new_id;
            $_SESSION['username'] = $username;
            $_SESSION['nama']     = $nama;
            $_SESSION['role']     = 'user';
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header("Location: " . $redirect . "?registered=1");
            exit();
        }
    }
}

$active_tab = isset($_GET['tab']) && $_GET['tab'] === 'register' ? 'register' : 'login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Daftar – Rumah Adat Budaya Kota Samarinda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B2E00;
            --primary-light: #B84A1A;
            --secondary: #C9890A;
            --bg-light: #FDF8F0;
            --border: #E8D4B0;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #1C0A00 0%, #3a1200 50%, #1C0A00 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('assets/Rumah_Adat_Banjar_Dayak_Kutai_Kota_Samarinda.jpeg') center/cover no-repeat;
            opacity: 0.12;
            z-index: 0;
        }
        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo-title {
            font-family: 'Cinzel', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #C9890A;
            letter-spacing: 1px;
            margin-top: 12px;
        }
        .auth-logo-sub {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.5);
            letter-spacing: 2px;
        }
        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        }
        .auth-tabs {
            display: flex;
            border-radius: 12px;
            background: var(--bg-light);
            padding: 4px;
            margin-bottom: 28px;
        }
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 9px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            color: #7A5C3A;
            text-decoration: none;
        }
        .auth-tab.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 4px 12px rgba(139,46,0,0.25);
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #4A2800;
            margin-bottom: 6px;
        }
        .form-control {
            background: var(--bg-light);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.92rem;
            color: #1C0A00;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(139,46,0,0.08);
        }
        .input-group-text {
            background: var(--bg-light);
            border: 1.5px solid var(--border);
            border-right: none;
            color: #7A5C3A;
        }
        .input-group .form-control {
            border-left: none;
        }
        .btn-auth {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(139,46,0,0.25);
            margin-top: 8px;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139,46,0,0.35);
        }
        .alert-custom {
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .back-link a:hover { color: #C9890A; }
        .divider-or {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #7A5C3A;
            font-size: 0.8rem;
        }
        .divider-or::before, .divider-or::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        /* Toggle password visibility */
        .toggle-pw {
            cursor: pointer;
            background: var(--bg-light);
            border: 1.5px solid var(--border);
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0 14px;
            color: #7A5C3A;
            transition: all 0.2s;
        }
        .toggle-pw:hover { color: var(--primary); }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-logo">
        <svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg" width="56" height="56">
            <circle cx="27" cy="27" r="25.5" stroke="#C9890A" stroke-width="1.2" stroke-dasharray="4 2"/>
            <circle cx="27" cy="27" r="20" fill="url(#logoGrad2)"/>
            <polygon points="27,9 43,22 11,22" fill="#C9890A" opacity="0.9"/>
            <polygon points="27,13 39,22 15,22" fill="#8B2E00" opacity="0.95"/>
            <rect x="15" y="22" width="24" height="14" rx="1" fill="#6B1E00"/>
            <rect x="23" y="29" width="8" height="7" rx="1.5" fill="#C9890A" opacity="0.85"/>
            <defs>
                <radialGradient id="logoGrad2" cx="50%" cy="40%" r="60%">
                    <stop offset="0%" stop-color="#3a1200"/>
                    <stop offset="100%" stop-color="#1C0A00"/>
                </radialGradient>
            </defs>
        </svg>
        <div class="auth-logo-title">Rumah Adat Budaya</div>
        <div class="auth-logo-sub">Kota Samarinda</div>
    </div>

    <div class="auth-card" id="authApp">
        <!-- Tabs -->
        <div class="auth-tabs">
            <a href="?tab=login<?= isset($_GET['redirect']) ? '&redirect='.urlencode($_GET['redirect']) : '' ?>"
                class="auth-tab <?= $active_tab === 'login' ? 'active' : '' ?>">
                <i class="fas fa-sign-in-alt me-1"></i> Masuk
            </a>
            <a href="?tab=register<?= isset($_GET['redirect']) ? '&redirect='.urlencode($_GET['redirect']) : '' ?>"
                class="auth-tab <?= $active_tab === 'register' ? 'active' : '' ?>">
                <i class="fas fa-user-plus me-1"></i> Daftar
            </a>
        </div>

        <?php if ($error): ?>
        <div class="alert-custom" style="background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);color:#c0392b;">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert-custom" style="background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#1e8449;">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- FORM LOGIN -->
        <?php if ($active_tab === 'login'): ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control"
                            placeholder="Masukkan username atau email" required
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" id="loginPw"
                            placeholder="Masukkan password" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('loginPw', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" name="act_login" class="btn-auth">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk
            </button>
        </form>
        <div class="divider-or">atau</div>
        <p class="text-center mb-0" style="font-size:0.87rem;color:#7A5C3A;">
            Belum punya akun?
            <a href="?tab=register<?= isset($_GET['redirect']) ? '&redirect='.urlencode($_GET['redirect']) : '' ?>"
                style="color:var(--primary);font-weight:700;text-decoration:none;">Daftar sekarang</a>
        </p>

        <?php else: ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" name="nama_lengkap" class="form-control"
                            placeholder="Nama lengkap Anda" required
                            value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control"
                            placeholder="Username unik" required
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="col-6">
                    <label class="form-label">No. HP</label>
                    <input type="tel" name="no_hp" class="form-control"
                            placeholder="08xxxxxxxxxx"
                            value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control"
                            placeholder="email@anda.com" required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">Password *</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="regPw"
                                placeholder="Min. 6 karakter" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('regPw', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label">Konfirmasi *</label>
                    <div class="input-group">
                        <input type="password" name="konfirmasi_password" class="form-control" id="regPw2"
                                placeholder="Ulangi password" required>
                        <button type="button" class="toggle-pw" onclick="togglePw('regPw2', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="submit" name="act_register" class="btn-auth">
                <i class="fas fa-user-plus me-2"></i> Buat Akun
            </button>
        </form>
        <div class="divider-or">atau</div>
        <p class="text-center mb-0" style="font-size:0.87rem;color:#7A5C3A;">
            Sudah punya akun?
            <a href="?tab=login<?= isset($_GET['redirect']) ? '&redirect='.urlencode($_GET['redirect']) : '' ?>"
                style="color:var(--primary);font-weight:700;text-decoration:none;">Masuk di sini</a>
        </p>
        <?php endif; ?>
    </div>

    <div class="back-link">
        <a href="index.php"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
</body>
</html>