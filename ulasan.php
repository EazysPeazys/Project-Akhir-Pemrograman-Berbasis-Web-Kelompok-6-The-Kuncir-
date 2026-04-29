<?php
session_start();
include 'koneksi.php';

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$is_logged_in = isset($_SESSION['user_id']);
$ulasan_error = '';

// Pastikan kolom status ada (auto-migrate jika belum ada)
$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM ulasan LIKE 'status'");
if (mysqli_num_rows($check_col) === 0) {
    mysqli_query($koneksi, "ALTER TABLE ulasan ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER komentar");
    mysqli_query($koneksi, "ALTER TABLE ulasan ADD COLUMN alasan_tolak TEXT NULL AFTER status");
    mysqli_query($koneksi, "UPDATE ulasan SET status='approved'");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ulasan'])) {
    // Wajib login untuk membuat ulasan
    if (!$is_logged_in) {
        header("Location: login.php?redirect=" . urlencode("ulasan.php"));
        exit();
    }
    $nama     = trim(mysqli_real_escape_string($koneksi, $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengunjung'));
    $rating   = (int)$_POST['rating'];
    $komentar = trim(mysqli_real_escape_string($koneksi, $_POST['komentar']));
    if ($rating < 1 || $rating > 5) {
        $ulasan_error = "Pilih rating bintang terlebih dahulu.";
    } elseif (strlen($komentar) < 10) {
        $ulasan_error = "Komentar minimal 10 karakter.";
    } else {
        $user_id_val = (int)$_SESSION['user_id'];
        mysqli_query($koneksi, "INSERT INTO ulasan (user_id, nama_tamu, rating, komentar, status, created_at) VALUES ($user_id_val, '$nama', '$rating', '$komentar', 'pending', NOW())");
        header("Location: ulasan.php?ok=1");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_ulasan']) && $is_admin) {
    $id       = (int)$_POST['edit_id'];
    $nama     = trim(mysqli_real_escape_string($koneksi, $_POST['edit_nama']));
    $rating   = (int)$_POST['edit_rating'];
    $komentar = trim(mysqli_real_escape_string($koneksi, $_POST['edit_komentar']));
    if ($rating >= 1 && $rating <= 5 && strlen($komentar) >= 10) {
        mysqli_query($koneksi, "UPDATE ulasan SET nama_tamu='$nama', rating='$rating', komentar='$komentar', updated_at=NOW() WHERE id='$id'");
    }
    header("Location: ulasan.php?edited=1");
    exit();
}

if (isset($_GET['hapus']) && is_numeric($_GET['hapus']) && $is_admin) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM ulasan WHERE id='$id'");
    header("Location: ulasan.php?deleted=1");
    exit();
}

$ulasan_list = mysqli_query($koneksi, "SELECT u.*, COALESCE(us.nama_lengkap, u.nama_tamu, 'Pengunjung') as display_name FROM ulasan u LEFT JOIN users us ON u.user_id = us.id AND u.user_id IS NOT NULL WHERE u.status='approved' ORDER BY u.created_at DESC");
$total_ulasan = mysqli_num_rows($ulasan_list);
$avg_rating = 0;
if ($total_ulasan > 0) {
    $avg_row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT AVG(rating) avg FROM ulasan WHERE status='approved'"));
    $avg_rating = round($avg_row['avg'], 1);
    mysqli_data_seek($ulasan_list, 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan Pengunjung – Rumah Adat Budaya Kota Samarinda</title>
    <link rel="stylesheet" href="Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* === HERO ANIMATIONS === */
.hero-intro-tag { opacity:0; transform:translateY(-24px); animation:heroFadeDown .7s ease forwards; animation-delay:.2s; }
.hero-intro-title { opacity:0; transform:translateY(-30px); animation:heroFadeDown .8s ease forwards; animation-delay:.45s; }
.hero-intro-sub { opacity:0; transform:translateY(-20px); animation:heroFadeDown .7s ease forwards; animation-delay:.7s; }

/* Subtitle paragraph animasi */
.hero-subtitle-anim {
    opacity:0; transform:translateY(20px); filter:blur(6px);
    animation: subtitleReveal .9s cubic-bezier(.22,1,.36,1) forwards;
    animation-delay:.85s;
}
@keyframes subtitleReveal { to { opacity:1; transform:translateY(0); filter:blur(0); } }

/* Stats box */
.stats-anim {
    opacity:0; transform:translateY(30px) scale(0.96);
    animation: statsReveal .8s cubic-bezier(.34,1.56,.64,1) forwards;
    animation-delay:1.1s;
}
@keyframes statsReveal { to { opacity:1; transform:translateY(0) scale(1); } }

/* Tiap item muncul satu per satu */
.uhs-item-anim { opacity:0; transform:translateY(16px); animation: itemPop .6s cubic-bezier(.34,1.56,.64,1) forwards; }
.uhs-item-anim:nth-child(1) { animation-delay:1.3s; }
.uhs-item-anim:nth-child(3) { animation-delay:1.5s; }
.uhs-item-anim:nth-child(5) { animation-delay:1.7s; }
@keyframes itemPop { to { opacity:1; transform:translateY(0); } }

/* Divider fade */
.uhs-div-anim { opacity:0; animation: divFade .4s ease forwards; }
.uhs-div-anim:nth-child(2) { animation-delay:1.45s; }
.uhs-div-anim:nth-child(4) { animation-delay:1.65s; }
@keyframes divFade { to { opacity:1; } }

/* Number glow */
@keyframes numGlow {
    0%   { text-shadow: 0 0 0 transparent; }
    50%  { text-shadow: 0 0 20px rgba(201,137,10,.9), 0 0 40px rgba(201,137,10,.5); }
    100% { text-shadow: 0 0 8px rgba(201,137,10,.3); }
}
.uhs-num-glow { animation: numGlow 1s ease forwards; animation-delay:.3s; }

@keyframes heroFadeDown { to { opacity:1; transform:translateY(0); } }
</style>
</head>
<body>


<?php include 'navbar.php'; ?>

<div class="page-header-hero ulasan-hero">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="section-tag hero-intro-tag"><i class="fas fa-star"></i> Ulasan Pengunjung</div>
        <h1 class="page-hero-title hero-intro-title">Apa Kata <em>Mereka</em>?</h1>
        <p class="hero-subtitle-anim">Intip serunya pengalaman para penjelajah yang sudah mampir ke Rumah Adat Budaya Samarinda.</p>
        <div class="ulasan-hero-stats stats-anim">
            <div class="uhs-item uhs-item-anim">
                <i class="fas fa-star"></i>
                <span class="uhs-num-glow"><?= $avg_rating > 0 ? $avg_rating : '–' ?></span>
                <small>Rating Rata-rata</small>
            </div>
            <div class="uhs-divider uhs-div-anim"></div>
            <div class="uhs-item uhs-item-anim">
                <i class="fas fa-comments"></i>
                <span class="uhs-num-glow"><?= $total_ulasan ?></span>
                <small>Total Ulasan</small>
            </div>
            <div class="uhs-divider uhs-div-anim"></div>
            <div class="uhs-item uhs-item-anim">
                <i class="fas fa-users"></i>
                <span class="uhs-num-glow">10K+</span>
                <small>Pengunjung</small>
            </div>
        </div>
    </div>
</div>

<style>
.ulasan-hero-stats {
    display: flex;
    align-items: center;
    gap: 0;
    margin-top: 28px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 50px;
    padding: 14px 32px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}
.uhs-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 0 24px;
}
.uhs-item i {
    color: #C9890A;
    font-size: 1rem;
}
.uhs-item span {
    font-family: "Cinzel", serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    line-height: 1;
}
.uhs-item small {
    font-family: "Nunito", sans-serif;
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.6);
    white-space: nowrap;
}
.uhs-divider {
    width: 1px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    flex-shrink: 0;
}
@media(max-width:600px){
    .ulasan-hero-stats { padding: 12px 16px; }
    .uhs-item { padding: 0 12px; }
    .uhs-item span { font-size: 1.2rem; }
}
</style>

<section class="ulasan-section" style="padding: 80px 0;">
    <div class="container">

        <?php if (isset($_GET['ok'])): ?>
        <div class="ul-flash success"><i class="fas fa-check-circle"></i> Terima kasih! Ulasan Anda berhasil dikirim dan sedang menunggu persetujuan admin.</div>
        <?php elseif (isset($_GET['edited'])): ?>
        <div class="ul-flash success"><i class="fas fa-check-circle"></i> Ulasan berhasil diperbarui.</div>
        <?php elseif (isset($_GET['deleted'])): ?>
        <div class="ul-flash warning"><i class="fas fa-trash"></i> Ulasan berhasil dihapus.</div>
        <?php endif; ?>
        <?php if ($ulasan_error): ?>
        <div class="ul-flash error"><i class="fas fa-exclamation-circle"></i> <?= $ulasan_error ?></div>
        <?php endif; ?>

        <?php if ($total_ulasan > 0): ?>
        <div class="ul-summary animate-box">
            <div class="ul-score-big">
                <div class="ul-score-num"><?= $avg_rating ?></div>
                <div class="ul-stars-row">
                    <?php for ($i=1;$i<=5;$i++): ?>
                    <i class="fas fa-star <?= $avg_rating>=$i?'s-filled':'s-empty' ?>"></i>
                    <?php endfor; ?>
                </div>
                <div class="ul-count"><?= $total_ulasan ?> ulasan</div>
            </div>
            <div class="ul-bars">
                <?php for ($s=5;$s>=1;$s--):
                    $cnt = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) c FROM ulasan WHERE rating=$s AND status='approved'"))['c'];
                    $pct = $total_ulasan > 0 ? round($cnt/$total_ulasan*100) : 0;
                ?>
                <div class="ul-bar-row">
                    <span class="ul-bar-lbl"><?= $s ?> <i class="fas fa-star s-filled" style="font-size:.7rem"></i></span>
                    <div class="ul-bar-track"><div class="ul-bar-fill" style="width:<?= $pct ?>%"></div></div>
                    <span class="ul-bar-cnt"><?= $cnt ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="ul-form-card animate-box">
            <h3 class="ul-form-title"><i class="fas fa-pen"></i> Tulis Ulasan Anda</h3>
            <?php if (!$is_logged_in): ?>
            <div style="text-align:center;padding:28px 20px;background:var(--bg-light);border-radius:14px;border:1.5px dashed var(--border);">
                <div style="font-size:2.5rem;margin-bottom:12px;opacity:.5"><i class="fas fa-lock"></i></div>
                <h4 style="font-family:var(--font-display);font-size:1rem;color:var(--text-dark);margin-bottom:8px;">Login Diperlukan</h4>
                <p style="font-family:var(--font-ui);font-size:.88rem;color:var(--text-light);margin-bottom:18px;">Anda harus masuk terlebih dahulu untuk menulis ulasan.</p>
                <a href="login.php?redirect=ulasan.php" style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;border-radius:50px;font-family:var(--font-ui);font-size:.9rem;font-weight:700;text-decoration:none;box-shadow:0 4px 15px rgba(139,46,0,.25);">
                    <i class="fas fa-sign-in-alt"></i> Masuk / Daftar
                </a>
            </div>
            <?php else: ?>
            <form method="POST" action="">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding:12px 16px;background:rgba(39,174,96,.07);border:1px solid rgba(39,174,96,.2);border-radius:10px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-family:var(--font-display);font-size:.9rem;flex-shrink:0;"><?= strtoupper(substr($_SESSION['username']??'U',0,1)) ?></div>
                    <div><div style="font-family:var(--font-ui);font-size:.88rem;font-weight:700;color:var(--text-dark);"><?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username'] ?? '') ?></div>
                    <div style="font-size:.74rem;color:var(--text-light);">Menulis sebagai anggota terdaftar</div></div>
                </div>
                <div class="ul-form-group">
                    <label>BERIKAN PENILAIAN *</label>
                    <div id="vueStarApp" class="ul-stars-input">
                        <i v-for="i in 5" :key="i" class="fas fa-star ul-star-item"
                           :style="{color: starActive(i) ? 'var(--secondary)' : '#ddd', transform: starActive(i)?'scale(1.15)':'scale(1)'}"
                           @mouseover="setHover(i)" @mouseout="clearHover" @click="setRatingVue(i)"
                           style="cursor:pointer;font-size:1.8rem;transition:all .2s;"></i>
                        <span class="ul-star-label" style="font-family:var(--font-ui);font-size:.88rem;font-weight:700;color:var(--text-light);margin-left:8px;">{{ starLabel }}</span>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="0">
                </div>
                <div class="ul-form-group">
                    <label>KOMENTAR / ULASAN *</label>
                    <textarea name="komentar" class="ul-textarea" placeholder="Ceritakan pengalaman Anda berkunjung ke Rumah Adat Budaya Kota Samarinda..." required></textarea>
                </div>
                <div class="ul-form-footer">
                    <div class="ul-poster-info">
                        <div class="ul-avatar-icon"><i class="fas fa-user"></i></div>
                        <span>Ulasan terbuka untuk semua pengunjung</span>
                    </div>
                    <button type="submit" name="submit_ulasan" class="ul-btn-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Ulasan
                    </button>
                </div>
            </form>
            <?php endif; // end else is_logged_in ?>
        </div>

        <div class="ul-section-header">
            <h2 class="ul-section-title"><i class="fas fa-comments"></i> Semua Ulasan</h2>
            <p class="ul-section-sub">Dibaca dan dirasakan langsung oleh para pengunjung kami</p>
        </div>
        <div class="ul-list">
            <?php if ($total_ulasan > 0):
                $no = 0;
                while ($ul = mysqli_fetch_assoc($ulasan_list)):
                    $no++;
            ?>
            <div class="ul-card <?= $no > 4 ? 'ul-hidden' : '' ?>" id="card-<?= $ul['id'] ?>">
                <div class="ul-card-header">
                    <div class="ul-avatar-circle"><?= strtoupper(substr($ul['display_name'], 0, 1)) ?></div>
                    <div class="ul-card-meta">
                        <div class="ul-card-name"><?= htmlspecialchars($ul['display_name']) ?></div>
                        <div class="ul-card-date"><i class="fas fa-clock"></i> <?= date('d M Y', strtotime($ul['created_at'])) ?></div>
                    </div>
                    <div class="ul-card-stars">
                        <?php for($i=1;$i<=5;$i++): ?>
                        <i class="fas fa-star <?= $ul['rating']>=$i?'s-filled':'s-empty' ?>"></i>
                        <?php endfor; ?>
                        <span class="ul-card-score"><?= $ul['rating'] ?>/5</span>
                    </div>
                    <?php if ($is_admin): ?>
                    <div class="ul-card-actions">
                        <button class="ul-btn-edit" title="Edit" onclick="openEdit(<?= $ul['id'] ?>,'<?= htmlspecialchars(addslashes($ul['display_name'])) ?>',<?= $ul['rating'] ?>,'<?= htmlspecialchars(addslashes($ul['komentar'])) ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="ulasan.php?hapus=<?= $ul['id'] ?>" class="ul-btn-hapus" title="Hapus"
                            onclick="return confirm('Hapus ulasan dari <?= htmlspecialchars(addslashes($ul['display_name'])) ?>?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <p class="ul-card-text"><?= nl2br(htmlspecialchars($ul['komentar'])) ?></p>
            </div>
            <?php endwhile;
            if ($total_ulasan > 4): ?>
            <div class="ul-load-more" id="loadMoreBtn">
                <button class="ul-btn-more" onclick="loadMore()">
                    <i class="fas fa-chevron-down"></i> Lihat Lebih Banyak (<?= $total_ulasan - 4 ?> lagi)
                </button>
            </div>
            <?php endif;
            else: ?>
            <div class="ul-empty">
                <i class="fas fa-comment-slash"></i>
                <p>Belum ada ulasan. Jadilah yang pertama memberikan ulasan!</p>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($is_admin): ?>
        <p style="text-align:center;margin-top:20px;font-family:var(--font-ui);font-size:.82rem;color:var(--text-light);">
            <i class="fas fa-shield-alt" style="color:var(--primary)"></i> Anda login sebagai <strong>Admin</strong> — dapat mengedit dan menghapus ulasan.
        </p>
        <?php endif; ?>
    </div>

    <style>
    .ul-flash{padding:14px 20px;border-radius:12px;font-family:var(--font-ui);font-size:.9rem;margin-bottom:28px;display:flex;align-items:center;gap:10px;font-weight:600}
    .ul-flash.success{background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#1e8449}
    .ul-flash.warning{background:rgba(243,156,18,.1);border:1px solid rgba(243,156,18,.3);color:#b7770d}
    .ul-flash.error{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);color:#c0392b}
    .ul-summary{display:flex;align-items:center;gap:40px;background:white;border-radius:20px;padding:32px 40px;margin-bottom:32px;box-shadow:var(--shadow-sm);border:1px solid var(--border);flex-wrap:wrap}
    .ul-score-big{text-align:center;min-width:120px}
    .ul-score-num{font-family:var(--font-display);font-size:3.5rem;font-weight:900;color:var(--primary);line-height:1}
    .ul-stars-row{display:flex;gap:4px;justify-content:center;margin:8px 0 6px}
    .ul-count{font-family:var(--font-ui);font-size:.82rem;color:var(--text-light)}
    .ul-bars{flex:1;min-width:200px;display:flex;flex-direction:column;gap:8px}
    .ul-bar-row{display:flex;align-items:center;gap:10px}
    .ul-bar-lbl{font-family:var(--font-ui);font-size:.8rem;color:var(--text-med);width:30px;text-align:right;font-weight:600}
    .ul-bar-track{flex:1;height:8px;background:#F0E8D8;border-radius:50px;overflow:hidden}
    .ul-bar-fill{height:100%;background:linear-gradient(90deg,var(--secondary),var(--primary-light));border-radius:50px;transition:width 1s}
    .ul-bar-cnt{font-family:var(--font-ui);font-size:.78rem;color:var(--text-light);width:20px}
    .s-filled{color:var(--secondary)}
    .s-empty{color:#ddd}
    .ul-form-card{background:white;border-radius:20px;padding:36px;box-shadow:var(--shadow-sm);border:1px solid var(--border);border-top:4px solid var(--primary);margin-bottom:40px}
    .ul-form-title{font-family:var(--font-display);font-size:1.2rem;color:var(--text-dark);margin-bottom:24px;display:flex;align-items:center;gap:10px}
    .ul-form-title i{color:var(--primary)}
    .ul-form-group{margin-bottom:20px}
    .ul-form-group label{display:block;font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-med);margin-bottom:8px}
    .ul-input{width:100%;padding:14px 16px;background:var(--bg-light);border:1.5px solid var(--border);border-radius:12px;font-family:var(--font-ui);font-size:.95rem;color:var(--text-dark);outline:none;transition:all .3s}
    .ul-input:focus{border-color:var(--primary);background:white;box-shadow:0 0 0 3px rgba(139,46,0,.07)}
    .ul-textarea{width:100%;padding:14px 16px;background:var(--bg-light);border:1.5px solid var(--border);border-radius:12px;font-family:var(--font-body);font-size:1rem;color:var(--text-dark);outline:none;resize:vertical;min-height:120px;transition:all .3s}
    .ul-textarea:focus{border-color:var(--primary);background:white;box-shadow:0 0 0 3px rgba(139,46,0,.07)}
    .ul-stars-input{display:flex;align-items:center;gap:6px}
    .ul-star-item{font-size:1.8rem;color:#ddd;cursor:pointer;transition:all .2s}
    .ul-star-item:hover,.ul-star-item.selected{color:var(--secondary);transform:scale(1.15)}
    .ul-star-label{font-family:var(--font-ui);font-size:.88rem;font-weight:700;color:var(--text-light);margin-left:8px}
    .ul-form-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-top:8px}
    .ul-poster-info{display:flex;align-items:center;gap:10px;font-family:var(--font-ui);font-size:.88rem;color:var(--text-light)}
    .ul-avatar-icon{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-size:.9rem;flex-shrink:0}
    .ul-btn-submit{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;border:none;border-radius:50px;font-family:var(--font-ui);font-size:.9rem;font-weight:700;cursor:pointer;transition:all .3s;box-shadow:0 4px 15px rgba(139,46,0,.25)}
    .ul-btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(139,46,0,.35)}
    .ul-btn-cancel{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:transparent;color:var(--text-light);border:2px solid var(--border);border-radius:50px;font-family:var(--font-ui);font-size:.9rem;font-weight:700;cursor:pointer;transition:all .3s}
    .ul-btn-cancel:hover{border-color:var(--text-light);color:var(--text-dark)}
    .ul-list{display:grid;grid-template-columns:repeat(2,1fr);gap:24px}

    .ul-card{
        background:white;
        border-radius:20px;
        padding:28px;
        border:1px solid var(--border);
        box-shadow:var(--shadow-sm);
        transition:transform .35s ease, box-shadow .35s ease;
        position:relative;
        overflow:hidden;
        opacity:0;
        transform:translateY(40px);
    }
    .ul-card.card-visible{
        opacity:1;
        transform:translateY(0);
        transition:opacity .55s ease, transform .55s ease, box-shadow .35s ease;
    }
    .ul-card:hover{transform:translateY(-6px) !important;box-shadow:var(--shadow-md)}

    .ul-card::before{
        content:'';
        position:absolute;
        top:0;left:0;right:0;
        height:3px;
        background:linear-gradient(90deg,var(--primary),var(--secondary));
        opacity:0;
        transition:opacity .3s;
    }
    .ul-card:hover::before{opacity:1}

    .ul-hidden{display:none !important}
    .ul-card-header{display:flex;align-items:flex-start;gap:14px;margin-bottom:16px}
    .ul-avatar-circle{
        width:46px;height:46px;
        border-radius:50%;
        background:linear-gradient(135deg,var(--primary),var(--primary-light));
        display:flex;align-items:center;justify-content:center;
        color:white;font-weight:700;font-family:var(--font-display);
        font-size:1.1rem;flex-shrink:0;
        box-shadow:0 4px 12px rgba(139,46,0,.25);
    }
    .ul-card-meta{flex:1}
    .ul-card-name{font-family:var(--font-ui);font-weight:700;font-size:.92rem;color:var(--text-dark)}
    .ul-card-date{font-size:.74rem;color:var(--text-light);margin-top:3px;display:flex;align-items:center;gap:5px}
    .ul-card-stars{display:flex;align-items:center;gap:3px;flex-shrink:0}
    .ul-card-score{
        font-family:var(--font-display);font-size:.8rem;font-weight:700;
        color:white;margin-left:6px;
        background:var(--primary);
        padding:2px 8px;border-radius:20px;
    }
    .ul-card-actions{display:flex;gap:6px;flex-shrink:0}
    .ul-btn-edit,.ul-btn-hapus{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.8rem;cursor:pointer;transition:all .2s;border:none;text-decoration:none}
    .ul-btn-edit{background:rgba(52,152,219,.1);color:#3498db}
    .ul-btn-edit:hover{background:#3498db;color:white}
    .ul-btn-hapus{background:rgba(231,76,60,.1);color:#e74c3c}
    .ul-btn-hapus:hover{background:#e74c3c;color:white}
    .ul-card-text{
        font-size:.97rem;color:var(--text-med);line-height:1.78;
        font-family:var(--font-body);font-style:italic;
        position:relative;z-index:1;
    }
    .ul-load-more{grid-column:1/-1;text-align:center;margin-top:8px}
    .ul-btn-more{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:transparent;border:2px solid var(--border);border-radius:50px;font-family:var(--font-ui);font-size:.88rem;font-weight:700;color:var(--text-med);cursor:pointer;transition:all .3s}
    .ul-btn-more:hover{border-color:var(--primary);color:var(--primary)}
    .ul-empty{grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--text-light)}
    .ul-empty i{font-size:3rem;margin-bottom:16px;display:block;opacity:.3}
    .ul-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:all .3s}
    .ul-modal-overlay.show{opacity:1;visibility:visible}
    .ul-modal-box{background:white;border-radius:20px;padding:36px;width:100%;max-width:520px;box-shadow:0 30px 80px rgba(0,0,0,.3);transform:scale(.95);transition:all .3s}
    .ul-modal-overlay.show .ul-modal-box{transform:scale(1)}
    .ul-modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
    .ul-modal-header h3{font-family:var(--font-display);font-size:1.2rem;color:var(--text-dark);display:flex;align-items:center;gap:10px}
    .ul-modal-header h3 i{color:var(--primary)}
    .ul-modal-close{background:none;border:none;font-size:1.1rem;color:var(--text-light);cursor:pointer;padding:6px;border-radius:8px;transition:all .2s}
    .ul-modal-close:hover{background:var(--bg-light);color:var(--text-dark)}
    .ul-modal-footer{display:flex;gap:12px;justify-content:flex-end;margin-top:24px}
    .ul-section-header{margin-bottom:28px;padding-bottom:18px;border-bottom:1.5px solid var(--border);}
    .ul-section-title{font-family:var(--font-display);font-size:1.3rem;color:var(--text-dark);display:flex;align-items:center;gap:10px;margin-bottom:6px;}
    .ul-section-title i{color:var(--primary);font-size:1.1rem;}
    .ul-section-sub{font-family:var(--font-ui);font-size:.85rem;color:var(--text-light);}
    .animate-box{
        opacity:0;
        transform:translateY(35px);
        transition:opacity .6s ease, transform .6s ease;
    }
    .animate-box.box-visible{
        opacity:1;
        transform:translateY(0);
    }
    @media(max-width:768px){.ul-list{grid-template-columns:1fr}.ul-summary{flex-direction:column;gap:20px}}
    </style>
</section>

<?php if ($is_admin): ?>
<div class="ul-modal-overlay" id="editModal">
    <div class="ul-modal-box">
        <div class="ul-modal-header">
            <h3><i class="fas fa-edit"></i> Edit Ulasan</h3>
            <button onclick="closeEdit()" class="ul-modal-close"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="edit_id" id="editId">
            <div class="ul-form-group">
                <label>NAMA</label>
                <input type="text" name="edit_nama" id="editNama" class="ul-input" required>
            </div>
            <div class="ul-form-group">
                <label>PENILAIAN</label>
                <div class="ul-stars-input" id="editStarsInput">
                    <?php for ($i=1;$i<=5;$i++): ?>
                    <i class="fas fa-star ul-star-item" data-val="<?= $i ?>" onclick="setEditRating(<?= $i ?>)"></i>
                    <?php endfor; ?>
                    <span class="ul-star-label" id="editStarLabel">Pilih bintang</span>
                </div>
                <input type="hidden" name="edit_rating" id="editRatingInput" value="0">
            </div>
            <div class="ul-form-group">
                <label>KOMENTAR</label>
                <textarea name="edit_komentar" id="editKomentar" class="ul-textarea" required></textarea>
            </div>
            <div class="ul-modal-footer">
                <button type="button" onclick="closeEdit()" class="ul-btn-cancel">Batal</button>
                <button type="submit" name="edit_ulasan" class="ul-btn-submit"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>

<script>
const starLabels = ['','Sangat Buruk','Buruk','Cukup','Bagus','Sangat Bagus'];

function setRating(val) {
    document.getElementById('ratingInput').value = val;
    document.getElementById('starLabel').textContent = starLabels[val];
    document.querySelectorAll('#starsInput .ul-star-item').forEach((s,i) => {
        s.classList.toggle('selected', i < val);
        s.style.color = i < val ? 'var(--secondary)' : '#ddd';
    });
}
document.querySelectorAll('#starsInput .ul-star-item').forEach((s,i) => {
    s.addEventListener('mouseover', () => {
        document.querySelectorAll('#starsInput .ul-star-item').forEach((x,j) => x.style.color = j<=i ? 'var(--secondary)' : '#ddd');
    });
    s.addEventListener('mouseout', () => {
        const val = +document.getElementById('ratingInput').value;
        document.querySelectorAll('#starsInput .ul-star-item').forEach((x,j) => x.style.color = j<val ? 'var(--secondary)' : '#ddd');
    });
});

function setEditRating(val) {
    document.getElementById('editRatingInput').value = val;
    document.getElementById('editStarLabel').textContent = starLabels[val];
    document.querySelectorAll('#editStarsInput .ul-star-item').forEach((s,i) => {
        s.classList.toggle('selected', i < val);
        s.style.color = i < val ? 'var(--secondary)' : '#ddd';
    });
}

function openEdit(id, nama, rating, komentar) {
    document.getElementById('editId').value = id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editKomentar').value = komentar;
    setEditRating(rating);
    document.getElementById('editModal').classList.add('show');
}
function closeEdit() {
    document.getElementById('editModal').classList.remove('show');
}
const modal = document.getElementById('editModal');
if (modal) modal.addEventListener('click', e => { if (e.target === modal) closeEdit(); });

function loadMore() {
    const hidden = document.querySelectorAll('.ul-hidden');
    hidden.forEach((el, i) => {
        setTimeout(() => {
            el.classList.remove('ul-hidden');
            el.style.opacity = '0';
            el.style.transform = 'translateY(16px)';
            requestAnimationFrame(() => {
                el.style.transition = 'opacity .4s ease, transform .4s ease';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        }, i * 80);
    });
    document.getElementById('loadMoreBtn').style.display = 'none';
}

function triggerBoxAnim() {
    const boxes = document.querySelectorAll('.animate-box');
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((entry, idx) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('box-visible');
                }, idx * 150);
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });
    boxes.forEach(box => obs.observe(box));
}

function triggerCardAnim() {
    const cards = document.querySelectorAll('.ul-card:not(.ul-hidden)');
    const obs = new IntersectionObserver((entries) => {
        entries.forEach((entry, idx) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('card-visible');
                }, idx * 100);
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });
    cards.forEach(card => obs.observe(card));
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(triggerBoxAnim, 100);
    setTimeout(triggerCardAnim, 300);
});
</script>

<script>
// Count-up animation untuk angka statistik
function countUp(el, target, duration, suffix) {
    const isFloat = String(target).includes('.');
    const decimals = isFloat ? 1 : 0;
    const start = 0;
    const startTime = performance.now();
    function update(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease out cubic
        const ease = 1 - Math.pow(1 - progress, 3);
        const current = start + (target - start) * ease;
        el.textContent = isFloat ? current.toFixed(decimals) : Math.floor(current);
        if (suffix) el.textContent += suffix;
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

// Trigger saat stats box muncul (setelah animasi delay 1.3s)
setTimeout(function() {
    const items = document.querySelectorAll('.uhs-item-anim');
    items.forEach(function(item) {
        const span = item.querySelector('.uhs-num-glow');
        if (!span) return;
        const raw = span.textContent.trim();
        if (raw === '–') return;
        if (raw.includes('K+')) {
            countUp(span, 10, 1000, 'K+');
        } else {
            const num = parseFloat(raw);
            if (!isNaN(num)) countUp(span, num, 1000, '');
        }
    });
}, 1300);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Vue 3 - Komponen rating bintang interaktif
const { createApp, ref, computed } = Vue;

createApp({
    setup() {
        const hoverRating = ref(0);
        const selectedRating = ref(0);
        const starLabelsArr = ['','Sangat Buruk','Buruk','Cukup','Bagus','Sangat Bagus'];
        const starLabel = computed(() => starLabelsArr[hoverRating.value || selectedRating.value] || 'Pilih bintang');

        function setHover(val) { hoverRating.value = val; }
        function clearHover() { hoverRating.value = 0; }
        function setRatingVue(val) {
            selectedRating.value = val;
            const inp = document.getElementById('ratingInput');
            if (inp) inp.value = val;
        }
        function starActive(i) {
            return (hoverRating.value || selectedRating.value) >= i;
        }

        return { hoverRating, selectedRating, starLabel, setHover, clearHover, setRatingVue, starActive };
    }
}).mount('#vueStarApp');
</script>
</body>
</html>