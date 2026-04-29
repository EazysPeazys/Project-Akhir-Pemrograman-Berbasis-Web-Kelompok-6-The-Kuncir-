<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user_id']);
$is_admin_nav  = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<nav class="navbar scrolled" id="navbar">
    <div class="nav-container">

        <a href="index.php" class="nav-logo">
            <div class="nav-logo-svg-wrap">
                <svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav-logo-svg">
                    <circle cx="27" cy="27" r="25.5" stroke="#C9890A" stroke-width="1.2" stroke-dasharray="4 2"/>
                    <circle cx="27" cy="27" r="20" fill="url(#logoGrad)" />
                    <polygon points="27,9 43,22 11,22" fill="#C9890A" opacity="0.9"/>
                    <polygon points="27,13 39,22 15,22" fill="#8B2E00" opacity="0.95"/>
                    <rect x="15" y="22" width="24" height="14" rx="1" fill="#6B1E00"/>
                    <rect x="23" y="29" width="8" height="7" rx="1.5" fill="#C9890A" opacity="0.85"/>
                    <rect x="16" y="22" width="2.5" height="14" rx="1" fill="#8B2E00"/>
                    <rect x="35.5" y="22" width="2.5" height="14" rx="1" fill="#8B2E00"/>
                    <rect x="17" y="36" width="3" height="5" rx="1" fill="#C9890A" opacity="0.7"/>
                    <rect x="25.5" y="36" width="3" height="5" rx="1" fill="#C9890A" opacity="0.7"/>
                    <rect x="34" y="36" width="3" height="5" rx="1" fill="#C9890A" opacity="0.7"/>
                    <circle cx="27" cy="8.5" r="1.8" fill="#C9890A"/>
                    <rect x="17" y="25" width="5" height="4" rx="1" fill="#C9890A" opacity="0.6"/>
                    <rect x="32" y="25" width="5" height="4" rx="1" fill="#C9890A" opacity="0.6"/>
                    <path d="M15 42 Q19 40 23 42 Q27 44 31 42 Q35 40 39 42" stroke="#C9890A" stroke-width="1" fill="none" opacity="0.7"/>
                    <defs>
                        <radialGradient id="logoGrad" cx="50%" cy="40%" r="60%">
                            <stop offset="0%" stop-color="#3a1200"/>
                            <stop offset="100%" stop-color="#1C0A00"/>
                        </radialGradient>
                    </defs>
                </svg>
            </div>
            <div class="logo-text">
                <span class="logo-main">Rumah Adat Budaya</span>
                <span class="logo-sub">Kota Samarinda</span>
            </div>
        </a>

        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php"      class="nav-link <?= $current_page==='index.php'?'active':'' ?>">Beranda</a></li>
            <li><a href="about.php"      class="nav-link <?= $current_page==='about.php'?'active':'' ?>">Tentang Kami</a></li>
            <li><a href="activities.php" class="nav-link <?= $current_page==='activities.php'?'active':'' ?>">Kegiatan</a></li>
            <li><a href="gallery.php"    class="nav-link <?= $current_page==='gallery.php'?'active':'' ?>">Galeri</a></li>
            <li><a href="fasilitas.php"  class="nav-link <?= $current_page==='fasilitas.php'?'active':'' ?>">Fasilitas</a></li>
            <li><a href="ulasan.php"     class="nav-link <?= $current_page==='ulasan.php'?'active':'' ?>">Ulasan</a></li>
            <li><a href="contact.php"    class="nav-link <?= $current_page==='contact.php'?'active':'' ?>">Kontak</a></li>
        </ul>

        <div class="nav-actions">
            <?php if ($is_logged_in): ?>
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#8B2E00,#B84A1A);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem;flex-shrink:0;"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
                <span style="color:rgba(255,255,255,0.7);font-size:.8rem;font-family:'Nunito',sans-serif;max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                <?php if ($is_admin_nav): ?><a href="admin/index.php" class="nav-btn-login" style="background:linear-gradient(135deg,#C9890A,#A06A05);font-size:.78rem;"><i class="fas fa-tachometer-alt"></i> Admin</a><?php endif; ?>
                <a href="logout.php" class="nav-btn-login" style="background:rgba(231,76,60,.2);color:#e74c3c;border:1px solid rgba(231,76,60,.3);" title="Keluar"><i class="fas fa-sign-out-alt"></i></a>
            </div>
            <?php else: ?>
            <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="nav-btn-login"><i class="fas fa-sign-in-alt"></i> Masuk</a>
            <?php endif; ?>
            <button class="hamburger" id="hamburger" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<style>
.navbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    background: rgba(18, 6, 0, 0.97);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(201,137,10,0.18);
    padding: 0;
}

.nav-container {
    width: 96%;
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 68px;
    gap: 24px;
}

.nav-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    flex-shrink: 0;
}

.nav-logo-svg-wrap {
    position: relative;
    flex-shrink: 0;
}

.nav-logo-svg {
    width: 50px;
    height: 50px;
    filter: drop-shadow(0 0 8px rgba(201,137,10,0.35));
    transition: filter 0.3s ease, transform 0.3s ease;
}

.nav-logo:hover .nav-logo-svg {
    filter: drop-shadow(0 0 14px rgba(201,137,10,0.7));
    transform: rotate(-3deg) scale(1.06);
}

.logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.logo-main {
    font-family: 'Cinzel', serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.logo-sub {
    font-family: 'Crimson Pro', serif;
    font-size: 0.72rem;
    color: #C9890A;
    letter-spacing: 1.5px;
    font-style: italic;
    white-space: nowrap;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 2px;
    list-style: none;
    margin: 0;
    padding: 0;
    margin-left: auto;
}

.nav-link {
    font-family: 'Nunito', sans-serif;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    color: rgba(255,255,255,0.78);
    padding: 8px 13px;
    border-radius: 6px;
    transition: all 0.25s ease;
    position: relative;
    text-decoration: none;
    white-space: nowrap;
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: #C9890A;
    border-radius: 2px;
    transition: width 0.25s ease;
}

.nav-link:hover,
.nav-link.active {
    color: #C9890A;
    background: rgba(201,137,10,0.08);
}

.nav-link:hover::after,
.nav-link.active::after {
    width: 65%;
}

.nav-actions { display: flex; align-items: center; gap: 10px; }
.hamburger   { display: none; }

.hamburger {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 8px;
    background: none;
    border: none;
    cursor: pointer;
}

.hamburger span {
    display: block;
    width: 24px;
    height: 2px;
    background: #fff;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px,5px); }
.hamburger.active span:nth-child(2) { opacity: 0; }
.hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px,-5px); }

@media (max-width: 900px) {
    .hamburger { display: flex; }

    .nav-menu {
        position: fixed;
        top: 0; right: -100%;
        width: 72%;
        max-width: 280px;
        height: 100vh;
        background: rgba(18,6,0,0.98);
        flex-direction: column;
        align-items: flex-start;
        padding: 80px 20px 24px;
        gap: 4px;
        border-left: 1px solid rgba(201,137,10,0.2);
        transition: right 0.4s ease;
        z-index: 99;
        margin-left: 0;
    }

    .nav-menu.active { right: 0; }

    .nav-link {
        width: 100%;
        padding: 12px 16px;
        font-size: 0.95rem;
    }

    .logo-main { font-size: 0.85rem; }
    .nav-logo-svg { width: 42px; height: 42px; }
}

@media (max-width: 480px) {
    .logo-sub { display: none; }
    .logo-main { font-size: 0.82rem; }
}

.nav-btn-login {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 20px;
    background: linear-gradient(135deg,#8B2E00,#B84A1A);
    color: white; font-size: .82rem; font-weight: 700;
    font-family: 'Nunito', sans-serif; text-decoration: none;
    transition: all .25s; white-space:nowrap;
}
.nav-btn-login:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(139,46,0,.35); color:white; }
</style>