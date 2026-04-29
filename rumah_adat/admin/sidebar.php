<?php
$cur = basename($_SERVER['PHP_SELF']);
function isActive($file) {
    global $cur;
    return $cur === $file ? 'active' : '';
}
?>
<style>
.sidebar{width:260px;background:#1C0A00;position:fixed;top:0;left:0;height:100vh;display:flex;flex-direction:column;z-index:200;border-right:1px solid rgba(201,137,10,.15);overflow-y:auto;}
.sb-logo{display:flex;align-items:center;gap:12px;padding:20px 18px;border-bottom:1px solid rgba(201,137,10,.15);}
.sb-logo-icon{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#8B2E00,#B84A1A);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;}
.sb-logo-text{display:flex;flex-direction:column;line-height:1.3;}
.sb-logo-main{font-family:'Cinzel',serif;font-size:.85rem;font-weight:700;color:white;letter-spacing:.5px;}
.sb-logo-sub{font-size:.65rem;color:#C9890A;font-style:italic;letter-spacing:1px;}
.sb-section{padding:14px 18px 4px;font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.3);}
.sb-menu{padding:0 10px;list-style:none;margin:0;}
.sb-menu a,.sb-menu a:link,.sb-menu a:visited{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-family:'Nunito',sans-serif;font-size:.84rem;font-weight:600;color:rgba(255,255,255,.65)!important;text-decoration:none!important;transition:all .25s;margin-bottom:2px;}
.sb-menu a i{width:18px;text-align:center;font-size:.9rem;color:rgba(201,137,10,.7)!important;transition:color .25s;}
.sb-menu a:hover{background:rgba(201,137,10,.12)!important;color:#C9890A!important;}
.sb-menu a:hover i{color:#C9890A!important;}
.sb-menu a.active,.sb-menu a.active:link,.sb-menu a.active:visited{background:linear-gradient(135deg,#8B2E00,#B84A1A)!important;color:#fff!important;box-shadow:0 4px 15px rgba(139,46,0,.3);}
.sb-menu a.active i{color:#fff!important;}
.sb-menu a.sb-logout,.sb-menu a.sb-logout:link,.sb-menu a.sb-logout:visited{color:rgba(231,76,60,.8)!important;}
.sb-menu a.sb-logout:hover{background:rgba(231,76,60,.12)!important;color:#e74c3c!important;}
.sb-menu a.sb-logout i{color:rgba(231,76,60,.7)!important;}
.sb-menu a.sb-logout:hover i{color:#e74c3c!important;}
.sb-spacer{flex:1;}
.sb-bottom{padding:12px 10px 16px;border-top:1px solid rgba(201,137,10,.1);}
@media(max-width:900px){#sidebarToggle{display:flex!important;align-items:center;justify-content:center;}}
</style>

<div class="sidebar" id="adminSidebar">
    <div class="sb-logo">
        <div class="sb-logo-icon"><i class="fas fa-landmark"></i></div>
        <div class="sb-logo-text">
            <span class="sb-logo-main">Rumah Adat</span>
            <span class="sb-logo-sub">Admin Panel</span>
        </div>
    </div>

    <div class="sb-section">Menu Utama</div>
    <ul class="sb-menu">
        <li><a href="index.php" class="<?php echo isActive('index.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    </ul>

    <div class="sb-section">Kelola Konten</div>
    <ul class="sb-menu">
        <li><a href="kegiatan.php" class="<?php echo isActive('kegiatan.php'); ?>"><i class="fas fa-calendar-alt"></i> Kegiatan / Acara</a></li>
        <li><a href="galeri.php"   class="<?php echo isActive('galeri.php'); ?>"><i class="fas fa-images"></i> Galeri Foto</a></li>
        <li><a href="ulasan.php"   class="<?php echo isActive('ulasan.php'); ?>"><i class="fas fa-star"></i> Ulasan</a></li>
    </ul>

    <div class="sb-section">Akun</div>
    <ul class="sb-menu">
        <li><a href="ganti_pw.php" class="<?php echo isActive('ganti_pw.php'); ?>"><i class="fas fa-key"></i> Ganti Password</a></li>
        <li><a href="../index.php" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a></li>
    </ul>

    <div class="sb-spacer"></div>
    <div class="sb-bottom">
        <ul class="sb-menu">
            <li><a href="logout.php" class="sb-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>

<button id="sidebarToggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')"
    style="display:none;position:fixed;top:14px;left:14px;z-index:300;background:#8B2E00;color:white;border:none;width:40px;height:40px;border-radius:8px;font-size:1rem;cursor:pointer;">
    <i class="fas fa-bars"></i>
</button>