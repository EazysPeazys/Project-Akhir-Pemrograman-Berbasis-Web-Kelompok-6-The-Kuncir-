<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}
require_once '../koneksi.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi.";
    } else {
        $q = mysqli_query($koneksi, "SELECT * FROM users WHERE (username='$username' OR email='$username') AND role='admin' LIMIT 1");
        if ($q && mysqli_num_rows($q) === 1) {
            $user = mysqli_fetch_assoc($q);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                header("Location: index.php");
                exit();
            } else { $error = "Password salah."; }
        } else { $error = "Akun admin tidak ditemukan."; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin – Rumah Adat Budaya Samarinda</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Nunito',sans-serif;background:#1C0A00;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;position:relative;overflow:hidden}
body::before{content:'';position:absolute;inset:0;background:url('../assets/Rumah_Adat_Banjar_Dayak_Kutai_Kota_Samarinda.jpeg') center/cover no-repeat;opacity:.15}
.card{position:relative;z-index:1;background:rgba(255,255,255,.04);border:1px solid rgba(201,137,10,.25);border-radius:20px;padding:48px 40px;width:100%;max-width:420px;backdrop-filter:blur(20px);box-shadow:0 30px 80px rgba(0,0,0,.5)}
.logo{text-align:center;margin-bottom:36px}
.logo svg{width:56px;height:56px;filter:drop-shadow(0 0 12px rgba(201,137,10,.5))}
.logo-main{display:block;font-family:'Cinzel',serif;font-size:1.1rem;font-weight:700;color:#fff;margin-top:12px}
.logo-sub{display:block;font-size:.75rem;color:#C9890A;font-style:italic;letter-spacing:1px;margin-top:2px}
.badge-admin{display:inline-block;background:rgba(139,46,0,.3);border:1px solid rgba(139,46,0,.5);color:#ffb347;font-size:.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-top:10px}
h2{font-family:'Cinzel',serif;font-size:1.3rem;color:#fff;text-align:center;margin-bottom:6px;margin-top:20px}
.subtitle{text-align:center;color:rgba(255,255,255,.45);font-size:.88rem;margin-bottom:28px}
.alert{padding:12px 16px;border-radius:10px;font-size:.88rem;margin-bottom:20px;display:flex;align-items:center;gap:10px;background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);color:#e74c3c}
.fg{margin-bottom:18px}
.fg label{display:block;font-size:.75rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#C9890A;margin-bottom:8px}
.fg input{width:100%;padding:13px 16px;background:rgba(255,255,255,.06);border:1.5px solid rgba(201,137,10,.2);border-radius:10px;font-family:'Nunito',sans-serif;font-size:.95rem;color:#fff;outline:none;transition:all .3s}
.fg input:focus{border-color:#C9890A;background:rgba(201,137,10,.06);box-shadow:0 0 0 3px rgba(201,137,10,.1)}
.fg input::placeholder{color:rgba(255,255,255,.3)}
.pw-wrap{position:relative}
.pw-wrap input{padding-right:44px}
.pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;font-size:.9rem;padding:4px}
.btn-login{width:100%;padding:14px;background:linear-gradient(135deg,#8B2E00,#B84A1A);color:#fff;border:none;border-radius:50px;font-family:'Nunito',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;transition:all .3s;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:10px}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(139,46,0,.4)}
.back-link{text-align:center;margin-top:24px}
.back-link a{font-size:.82rem;color:rgba(255,255,255,.35);text-decoration:none;transition:color .2s}
.back-link a:hover{color:#C9890A}
</style>
</head>
<body>
<div class="card">
    <div class="logo">
        <svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="27" cy="27" r="25.5" stroke="#C9890A" stroke-width="1.2" stroke-dasharray="4 2"/>
            <circle cx="27" cy="27" r="20" fill="url(#lg)"/>
            <polygon points="27,9 43,22 11,22" fill="#C9890A" opacity="0.9"/>
            <polygon points="27,13 39,22 15,22" fill="#8B2E00" opacity="0.95"/>
            <rect x="15" y="22" width="24" height="14" rx="1" fill="#6B1E00"/>
            <rect x="23" y="29" width="8" height="7" rx="1.5" fill="#C9890A" opacity="0.85"/>
            <defs><radialGradient id="lg" cx="50%" cy="40%" r="60%"><stop offset="0%" stop-color="#3a1200"/><stop offset="100%" stop-color="#1C0A00"/></radialGradient></defs>
        </svg>
        <span class="logo-main">Rumah Adat Budaya</span>
        <span class="logo-sub">Kota Samarinda</span>
        <div><span class="badge-admin"><i class="fas fa-shield-alt"></i> Admin Panel</span></div>
    </div>
    <h2>Masuk sebagai Admin</h2>
    <p class="subtitle">Halaman ini khusus untuk pengelola website</p>
    <?php if ($error): ?>
    <div class="alert"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="fg">
            <label>Username / Email</label>
            <input type="text" name="username" placeholder="Masukkan username admin"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autocomplete="username">
        </div>
        <div class="fg">
            <label>Password</label>
            <div class="pw-wrap">
                <input type="password" name="password" id="pwInput" placeholder="Masukkan password" required autocomplete="current-password">
                <button type="button" class="pw-toggle" onclick="togglePw()"><i class="fas fa-eye" id="pwIcon"></i></button>
            </div>
        </div>
        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard</button>
    </form>
    <div class="back-link"><a href="../index.php"><i class="fas fa-arrow-left"></i> Kembali ke Website</a></div>
</div>
<script>
function togglePw(){
    var i=document.getElementById('pwInput'),ic=document.getElementById('pwIcon');
    if(i.type==='password'){i.type='text';ic.className='fas fa-eye-slash';}
    else{i.type='password';ic.className='fas fa-eye';}
}
</script>
</body>
</html>