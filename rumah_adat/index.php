<?php
session_start();
include 'koneksi.php';

$result_activities = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE status != 'dibatalkan' ORDER BY tanggal ASC LIMIT 5");

$total_ulasan = 0;
$avg_rating   = 0;
$avg_row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c, AVG(rating) avg FROM ulasan"));
if ($avg_row) {
    $total_ulasan = $avg_row['c'];
    $avg_rating   = round($avg_row['avg'], 1);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda – Rumah Adat Budaya Kota Samarinda</title>
    <link rel="stylesheet" href="Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div id="siteIntro" style="position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;overflow:hidden;transition:opacity 0.7s ease,visibility 0.7s ease;background:radial-gradient(ellipse at 50% 40%,#3a1200 0%,#1C0A00 55%,#0a0300 100%);">
    <div id="introParticles" style="position:absolute;inset:0;pointer-events:none;"></div>
    <div style="position:relative;z-index:2;text-align:center;display:flex;flex-direction:column;align-items:center;gap:14px;animation:introUp 0.8s ease forwards;">
        <div style="display:flex;align-items:center;gap:12px;width:260px;">
            <span style="flex:1;height:1px;background:linear-gradient(90deg,transparent,#C9890A,transparent);"></span>
            <i class="fas fa-gem" style="color:#C9890A;font-size:0.85rem;"></i>
            <span style="flex:1;height:1px;background:linear-gradient(90deg,transparent,#C9890A,transparent);"></span>
        </div>
        <div style="position:relative;width:90px;height:90px;">
            <div style="position:absolute;inset:0;border-radius:50%;border:2px solid transparent;border-top-color:#C9890A;border-right-color:rgba(201,137,10,0.4);animation:spinRing 1.5s linear infinite;"></div>
            <div style="position:absolute;inset:10px;border-radius:50%;background:linear-gradient(135deg,#8B2E00,#B84A1A);display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;box-shadow:0 0 30px rgba(139,46,0,0.5);"><i class="fas fa-landmark"></i></div>
        </div>
        <h1 style="font-family:'Cinzel',serif;font-size:clamp(1.6rem,4vw,2.6rem);font-weight:900;color:white;letter-spacing:3px;text-shadow:0 0 40px rgba(201,137,10,0.4);line-height:1.2;animation:introFade 0.8s ease 0.4s both;">Rumah Adat Budaya</h1>
        <p style="font-family:'Crimson Pro',serif;font-size:1.1rem;font-style:italic;color:#C9890A;letter-spacing:4px;text-transform:uppercase;animation:introFade 0.8s ease 0.6s both;">Kota Samarinda</p>
        <div style="display:flex;align-items:center;gap:12px;width:260px;animation:introFade 0.8s ease 0.3s both;">
            <span style="flex:1;height:1px;background:linear-gradient(90deg,transparent,#C9890A,transparent);"></span>
            <span style="width:5px;height:5px;border-radius:50%;background:#C9890A;opacity:0.7;"></span>
            <span style="width:5px;height:5px;border-radius:50%;background:#C9890A;opacity:0.7;"></span>
            <span style="width:5px;height:5px;border-radius:50%;background:#C9890A;opacity:0.7;"></span>
            <span style="flex:1;height:1px;background:linear-gradient(90deg,transparent,#C9890A,transparent);"></span>
        </div>
        <div style="width:200px;height:2px;background:rgba(255,255,255,0.1);border-radius:2px;overflow:hidden;margin-top:8px;">
            <div id="introBar" style="height:100%;width:0;background:linear-gradient(90deg,#8B2E00,#C9890A);border-radius:2px;transition:width 2s cubic-bezier(0.4,0,0.2,1);"></div>
        </div>
    </div>
</div>
<style>
@keyframes introUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
@keyframes introFade{from{opacity:0}to{opacity:1}}
@keyframes spinRing{to{transform:rotate(360deg)}}
.ip{position:absolute;border-radius:50%;opacity:0;animation:floatP linear infinite;}
@keyframes floatP{0%{transform:translateY(100vh) scale(0);opacity:0}10%{opacity:0.7}90%{opacity:0.3}100%{transform:translateY(-20px) scale(1.5);opacity:0}}

.hero-scroll-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-top: 48px;
    cursor: pointer;
    user-select: none;
    animation: heroFade 1s ease 1.2s both;
}

@keyframes heroFade { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

.scroll-hint-text {
    font-family: 'Nunito', sans-serif;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.45);
    transition: color 0.3s;
}

.hero-scroll-indicator:hover .scroll-hint-text {
    color: rgba(201,137,10,0.9);
}

.scroll-mouse {
    width: 24px;
    height: 38px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 12px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 6px;
    transition: border-color 0.3s;
}

.hero-scroll-indicator:hover .scroll-mouse {
    border-color: rgba(201,137,10,0.7);
}

.scroll-wheel {
    width: 4px;
    height: 8px;
    background: rgba(255,255,255,0.6);
    border-radius: 2px;
    animation: scrollWheel 1.8s ease infinite;
}

@keyframes scrollWheel {
    0%   { transform: translateY(0); opacity: 1; }
    60%  { transform: translateY(10px); opacity: 0; }
    61%  { transform: translateY(0); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

.scroll-arrows {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
    line-height: 1;
}

.scroll-arrows i {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.25);
    animation: arrowBounce 1.8s ease infinite;
    display: block;
}

.scroll-arrows i:nth-child(1) { animation-delay: 0s; }
.scroll-arrows i:nth-child(2) { animation-delay: 0.2s; }

@keyframes arrowBounce {
    0%, 100% { transform: translateY(0);  opacity: 0.25; }
    50%       { transform: translateY(4px); opacity: 0.8; }
}

.hero-scroll-indicator:hover .scroll-arrows i {
    color: rgba(201,137,10,0.7);
}

</style>
<script>
(function(){
    if(sessionStorage.getItem('introShown')){document.getElementById('siteIntro').style.display='none';return;}
    sessionStorage.setItem('introShown','1');
    var c=document.getElementById('introParticles');
    for(var i=0;i<28;i++){var p=document.createElement('div');p.className='ip';p.style.left=Math.random()*100+'vw';p.style.animationDuration=(2+Math.random()*3)+'s';p.style.animationDelay=(Math.random()*2)+'s';var sz=(2+Math.random()*4)+'px';p.style.width=p.style.height=sz;p.style.background='#C9890A';c.appendChild(p);}
    setTimeout(function(){document.getElementById('introBar').style.width='100%';},100);
    setTimeout(function(){var el=document.getElementById('siteIntro');el.style.opacity='0';el.style.visibility='hidden';setTimeout(function(){if(el.parentNode)el.parentNode.removeChild(el);},800);},2800);
})();
</script>

<?php include 'navbar.php'; ?>

<section class="hero" id="beranda">
    <div class="hero-overlay"></div>
    <div class="hero-pattern"></div>
    <div class="hero-content">
        <div class="hero-badge"><i class="fas fa-gem"></i> WARISAN BUDAYA KOTA SAMARINDA</div>
        <h1 class="hero-title">
            Rumah Adat<br>
            <em>Budaya Kota</em><br>
            Samarinda
        </h1>
        <p class="hero-desc">
            Melestarikan warisan leluhur Kutai, Dayak &amp; Banjar di Kota Samarinda.<br>
            Tempat bersejarah untuk perayaan budaya, adat istiadat, dan perhelatan akbar.
        </p>
        <div class="hero-buttons">
            <a href="#tentang" class="btn-primary" onclick="smoothTo('tentang')"><i class="fas fa-compass"></i> Jelajahi</a>
            <a href="#kontak" class="btn-secondary" onclick="smoothTo('kontak')"><i class="fas fa-envelope"></i> Hubungi Kami</a>
        </div>

        <div class="hero-scroll-indicator" onclick="smoothTo('tentang')">
    <div class="hero-scroll">
        <span>Scroll ke bawah</span>
        <div class="scroll-arrow"><i class="fas fa-chevron-down"></i></div>
    </div>
            <div class="scroll-arrows">
                <i class="fas fa-chevron-down"></i>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

    </div>
    <div class="hero-stats">
        <div class="stat-item">
            <span class="stat-number" data-target="2020" data-suffix="">0</span>
            <span class="stat-label">TAHUN BERDIRI</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-number" data-target="50" data-suffix="+">0</span>
            <span class="stat-label">EVENT / TAHUN</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-number" data-target="10000" data-suffix="K+">0</span>
            <span class="stat-label">PENGUNJUNG</span>
        </div>
    </div>
</section>

<section class="about-section" id="tentang" style="padding:100px 0;">
    <div class="container">
        <div class="about-grid">
            <div class="about-images">
                <div class="img-main">
                    <img src="assets/Rumah_Adat_Banjar_Dayak_Kutai_Kota_Samarinda.jpeg" alt="Rumah Adat Samarinda" onerror="this.src='https://picsum.photos/600/400?grayscale'">
                    <div class="img-badge"><i class="fas fa-award"></i><span>Rumah Adat Budaya Kota Samarinda</span></div>
                </div>
                <div class="img-secondary">
                    <img src="assets/Gerbang_Utama_Rumah_adat_budaya_Kota_Samarinda.jpeg" alt="Gerbang Utama" onerror="this.src='https://picsum.photos/300/300?grayscale'">
                </div>
            </div>
            <div class="about-content">
                <div class="section-tag"><i class="fas fa-feather-alt"></i> Tentang Kami</div>
                <h2 class="section-title">Menjaga Warisan<br><em>Leluhur Kota Samarinda</em></h2>
                <div class="about-text">
                    <p>Rumah Adat Budaya Kota Samarinda merupakan pusat pelestarian kebudayaan asli masyarakat Kutai, Dayak dan Banjar di Kota Samarinda, Provinsi Kalimantan Timur. Berdiri sejak tahun 2020, tempat ini menjadi salah satu pusat kebudayaan dan ruang sakral pertemuan tradisi dan modernitas.</p>
                    <p>Kami hadir sebagai wadah bagi masyarakat untuk mengenal, merayakan, dan mewariskan kekayaan budaya leluhur kepada generasi penerus.</p>
                </div>
                <div class="about-features">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-home"></i></div>
                        <div class="feature-text"><strong>Arsitektur Autentik</strong><span>Bangunan tradisional Kalimantan yang kaya nilai budaya dan filosofi leluhur.</span></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-music"></i></div>
                        <div class="feature-text"><strong>Pertunjukan Seni &amp; Budaya</strong><span>Tari, musik, dan ritual adat yang penuh nilai tradisi.</span></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <div class="feature-text"><strong>Ruang Komunitas</strong><span>Tempat berkumpul untuk kegiatan adat dan budaya bersama.</span></div>
                    </div>
                </div>
                <a href="about.php" class="btn-primary"><i class="fas fa-arrow-right"></i> Selengkapnya</a>
            </div>
        </div>
    </div>
</section>

<section class="activities-section" id="kegiatan" style="padding:100px 0;">
    <div class="activities-bg"></div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="section-header centered" style="margin-bottom:44px;">
            <div class="section-tag" style="background:rgba(201,137,10,0.15);border-color:rgba(201,137,10,0.3);color:var(--secondary);"><i class="fas fa-calendar-alt"></i> Jadwal</div>
            <h2 class="section-title" style="color:white;">Kegiatan &amp; <em style="color:var(--secondary);">Acara</em></h2>
            <p class="section-desc" style="color:rgba(255,255,255,0.6);">Berbagai kegiatan budaya yang diselenggarakan di Rumah Adat Budaya Kota Samarinda</p>
        </div>
        <div class="activities-grid">
            <?php if ($result_activities && mysqli_num_rows($result_activities) > 0):
                while ($row = mysqli_fetch_assoc($result_activities)): ?>
            <div class="activity-card anim-fadeup">
                <div class="activity-date">
                    <span class="date-day"><?= date('d', strtotime($row['tanggal'])) ?></span>
                    <span class="date-month"><?= date('M', strtotime($row['tanggal'])) ?></span>
                    <span class="date-year"><?= date('Y', strtotime($row['tanggal'])) ?></span>
                </div>
                <div class="activity-info">
                    <span class="activity-category"><i class="fas fa-tag"></i> <?= htmlspecialchars($row['kategori'] ?? 'Budaya') ?></span>
                    <h3><?= htmlspecialchars($row['nama_kegiatan']) ?></h3>
                    <p><?= htmlspecialchars($row['deskripsi'] ?? '') ?></p>
                    <div class="activity-meta">
                        <span><i class="fas fa-clock"></i> <?= $row['jam_mulai'] ?> – <?= $row['jam_selesai'] ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['lokasi'] ?? 'Aula Utama') ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div class="no-activity"><i class="fas fa-calendar-times"></i><p>Belum ada jadwal kegiatan saat ini.</p></div>
            <?php endif; ?>
        </div>
        <div class="kegiatan-cta-wrap" style="text-align:center;margin-top:32px;">
            <a href="activities.php" class="btn-primary"><i class="fas fa-calendar-alt"></i> Lihat Semua Kegiatan</a>
        </div>
    </div>
</section>

<section id="galeri" style="padding:100px 0;background:var(--bg-light);">
    <div class="container">
        <div class="section-header centered" style="margin-bottom:44px;">
            <div class="section-tag"><i class="fas fa-images"></i> Galeri Foto</div>
            <h2 class="section-title">Potret <em>Keindahan Budaya</em></h2>
            <p class="section-desc">Koleksi foto momen bersejarah Rumah Adat Budaya Kota Samarinda</p>
        </div>
        <div class="op-gallery-grid anim-fadeup">
            <?php
            $photos = [
                ['src'=>'assets/Rumah_Adat_Banjar_Dayak_Kutai_Kota_Samarinda.jpeg','label'=>'Tampak Depan Kompleks','span'=>2],
                ['src'=>'assets/Rumah_Adat_Banjar.jpeg','label'=>'Rumah Adat Banjar','span'=>1],
                ['src'=>'assets/Rumah_Adat_Dayak.jpeg','label'=>'Rumah Adat Dayak','span'=>1],
                ['src'=>'assets/Rumah_Adat_Kutai.jpeg','label'=>'Rumah Adat Kutai','span'=>1],
                ['src'=>'assets/Gerbang_Utama_Rumah_adat_budaya_Kota_Samarinda.jpeg','label'=>'Gerbang Utama','span'=>1],
            ];
            foreach($photos as $p): ?>
            <div class="op-gallery-item <?= $p['span']===2?'op-span2':'' ?>" onclick="openGalleryLightbox('<?= $p['src'] ?>','<?= $p['label'] ?>')">
                <img src="<?= $p['src'] ?>" alt="<?= $p['label'] ?>" onerror="this.src='https://picsum.photos/600/400?random=<?= rand(1,99) ?>'">
                <div class="op-gallery-overlay"><i class="fas fa-expand"></i><span><?= $p['label'] ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:32px;">
            <a href="gallery.php" class="btn-primary"><i class="fas fa-images"></i> Lihat Galeri Lengkap</a>
        </div>
    </div>
    <!-- Lightbox -->
    <div id="opLightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:16px;">
        <button onclick="document.getElementById('opLightbox').style.display='none'" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:white;width:44px;height:44px;border-radius:50%;font-size:1rem;cursor:pointer;"><i class="fas fa-times"></i></button>
        <img id="opLbImg" src="" style="max-width:90vw;max-height:78vh;border-radius:12px;object-fit:contain;">
        <p id="opLbCap" style="color:rgba(255,255,255,.7);font-family:'Nunito',sans-serif;font-size:.95rem;"></p>
    </div>
</section>

<section id="fasilitas" style="padding:100px 0;background:white;">
    <div class="container">
        <div class="section-header centered" style="margin-bottom:44px;">
            <div class="section-tag"><i class="fas fa-building"></i> Fasilitas</div>
            <h2 class="section-title">Fasilitas <em>Tersedia</em></h2>
            <p class="section-desc">Berbagai fasilitas lengkap untuk kenyamanan pengunjung dan penyelenggara acara</p>
        </div>
        <div class="fasilitas-grid">
            <?php
            $fasilitas = [
                ['fas fa-restroom','Toilet Umum','Fasilitas toilet bersih dan terawat tersedia di beberapa titik area komplek, terpisah antara pria dan wanita.'],
                ['fas fa-parking','Area Parkir Luas','Halaman parkir luas mampu menampung ratusan kendaraan roda dua maupun roda empat, termasuk bus rombongan.'],
                ['fas fa-microphone-alt','Sound System','Peralatan audio profesional untuk mendukung pertunjukan seni, seminar, hingga perhelatan akbar budaya.'],
                ['fas fa-lightbulb','Tata Cahaya','Sistem pencahayaan panggung modern yang dapat disesuaikan dengan kebutuhan acara.'],
                ['fas fa-chair','Kursi &amp; Meja','Ketersediaan kursi dan meja dalam jumlah besar untuk berbagai format acara.'],
                ['fas fa-shield-alt','Keamanan 24 Jam','Petugas keamanan berjaga 24 jam penuh untuk keamanan seluruh pengunjung.'],
            ];
            foreach($fasilitas as $f): ?>
            <div class="fasilitas-card anim-fadeup">
                <div class="fasilitas-icon"><i class="<?= $f[0] ?>"></i></div>
                <h3><?= $f[1] ?></h3>
                <p><?= $f[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:40px;">
            <a href="fasilitas.php" class="btn-primary"><i class="fas fa-building"></i> Selengkapnya</a>
        </div>
    </div>
</section>

<?php
$ulasan_home = mysqli_query($koneksi, "SELECT u.*, COALESCE(us.nama_lengkap, u.nama_tamu, 'Pengunjung') as display_name FROM ulasan u LEFT JOIN users us ON u.user_id = us.id AND u.user_id IS NOT NULL ORDER BY u.created_at DESC LIMIT 6");
$avg_home = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c, ROUND(AVG(rating),1) avg FROM ulasan"));
?>
<section id="ulasan" style="padding:100px 0;background:var(--bg-light);">
    <div class="container">
        <div class="section-header centered" style="margin-bottom:50px;">
            <div class="section-tag anim-fadeup"><i class="fas fa-star"></i> Ulasan</div>
            <h2 class="section-title anim-fadeup">Kata Mereka <em>Tentang Kami</em></h2>
            <p class="section-desc anim-fadeup">Pengalaman nyata dari para pengunjung Rumah Adat Budaya Kota Samarinda</p>
        </div>
        <div class="home-rating-summary anim-fadeup">
            <div class="home-rating-big">
                <span class="home-rating-number"><?= $avg_home['avg'] ?? '0' ?></span>
                <div style="display:flex;gap:4px;justify-content:center;margin:6px 0;">
                    <?php $avg_val = floatval($avg_home['avg'] ?? 0);
                    for($s=1;$s<=5;$s++): ?>
                        <i class="fas fa-star" style="color:<?= $s<=$avg_val?'#C9890A':'rgba(201,137,10,0.25)' ?>;font-size:1.1rem;"></i>
                    <?php endfor; ?>
                </div>
                <span class="home-rating-count"><?= $avg_home['c'] ?? 0 ?> ulasan</span>
            </div>
        </div>
        <div class="home-ulasan-grid">
        <?php if ($ulasan_home && mysqli_num_rows($ulasan_home) > 0):
            while ($u = mysqli_fetch_assoc($ulasan_home)): ?>
            <div class="home-ulasan-card anim-fadeup">
                <div style="display:flex;gap:3px;margin-bottom:4px;">
                    <?php for($s=1;$s<=5;$s++): ?>
                        <i class="fas fa-star" style="color:<?= $s<=$u['rating']?'#C9890A':'rgba(201,137,10,0.25)' ?>;font-size:0.85rem;"></i>
                    <?php endfor; ?>
                </div>
                <p class="home-ulasan-text">&ldquo;<?= htmlspecialchars($u['komentar']) ?>&rdquo;</p>
                <div class="home-ulasan-author">
                    <div class="home-ulasan-avatar"><?= strtoupper(mb_substr($u['display_name'],0,1)) ?></div>
                    <div>
                        <strong><?= htmlspecialchars($u['display_name']) ?></strong>
                        <span><?= date('d M Y', strtotime($u['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div style="text-align:center;padding:40px;color:var(--text-light);grid-column:1/-1;">
                <i class="fas fa-comment-slash" style="font-size:2rem;margin-bottom:12px;opacity:.4;display:block;"></i>
                <p>Belum ada ulasan. Jadilah yang pertama!</p>
            </div>
            <?php endif; ?>
        </div>
        <div style="text-align:center;margin-top:44px;" class="anim-fadeup">
            <a href="ulasan.php" class="btn-primary" style="margin-right:14px;"><i class="fas fa-pen"></i> Beri Ulasan</a>
            <a href="ulasan.php" class="btn-outline"><i class="fas fa-comments"></i> Lihat Semua Ulasan</a>
        </div>
    </div>
</section>
<style>
.home-rating-summary{display:flex;justify-content:center;margin-bottom:44px;}
.home-rating-big{text-align:center;background:white;border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px 60px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;align-items:center;gap:8px;}
.home-rating-number{font-family:var(--font-display);font-size:3.8rem;font-weight:900;color:var(--secondary);line-height:1;}
.home-rating-count{font-family:var(--font-ui);font-size:0.82rem;color:var(--text-light);font-weight:600;}
.home-ulasan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.home-ulasan-card{background:white;border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;gap:14px;transition:transform 0.3s ease,box-shadow 0.3s ease;}
.home-ulasan-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);}
.home-ulasan-text{font-family:var(--font-body);font-size:1rem;color:var(--text-med);line-height:1.7;flex:1;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;}
.home-ulasan-author{display:flex;align-items:center;gap:12px;padding-top:14px;border-top:1px solid var(--border);}
.home-ulasan-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-weight:700;font-size:1rem;flex-shrink:0;}
.home-ulasan-author strong{display:block;font-family:var(--font-ui);font-size:0.88rem;font-weight:700;color:var(--text-dark);}
.home-ulasan-author span{font-family:var(--font-ui);font-size:0.75rem;color:var(--text-light);}
@media(max-width:900px){.home-ulasan-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:560px){.home-ulasan-grid{grid-template-columns:1fr;}}
</style>

<section class="contact-section" id="kontak" style="padding:100px 0;">
    <div class="container">
        <div class="section-header centered" style="margin-bottom:44px;">
            <div class="section-tag" style="background:rgba(201,137,10,0.15);border-color:rgba(201,137,10,0.3);color:var(--secondary);"><i class="fas fa-handshake"></i> Hubungi Kami</div>
            <h2 class="section-title" style="color:white;">Informasi &amp; <em style="color:var(--secondary);">Lokasi</em></h2>
            <p class="section-desc" style="color:rgba(255,255,255,0.6);">Kami siap membantu Anda merencanakan acara budaya yang berkesan</p>
        </div>
        <div class="contact-grid">
            <div class="contact-info">
                <div class="info-card anim-fadeup">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text"><h4>Alamat</h4><p>Jl. Kadrie Oening No.8 Air Hitam,<br>Kec. Samarinda Ulu, Kota Samarinda,<br>Kalimantan Timur 75243</p></div>
                </div>
                <div class="info-card anim-fadeup">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-text"><h4>Telepon</h4><p>+62 822 5497 0390 (Admin)</p></div>
                </div>
                <div class="info-card anim-fadeup">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text"><h4>Email</h4><p>cagarbudayasamarinda@gmail.com</p></div>
                </div>
                <div class="info-card anim-fadeup">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-text"><h4>Jam Operasional</h4><p>Senin – Kamis : 08.30 – 15.00 WITA<br>Jumat : 08.30 – 11.00 WITA</p></div>
                </div>
                <div class="social-links" style="padding:0;">
                    <h4 style="font-family:var(--font-ui);font-size:.85rem;font-weight:700;color:var(--secondary);text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;">Media Sosial</h4>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/rumahadatkotasamarinda" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/share/1AafgfSLMb/" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/6282254970390" target="_blank" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="contact-map-wrapper anim-fadeup">
                <div class="map-container">
                    <iframe src="https://www.google.com/maps?q=-0.4722297,117.1295933&hl=id&z=15&output=embed"
                        width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div class="reserve-cta">
                    <h3>Ingin Menggelar Acara di Sini?</h3>
                    <p>Reservasi tempat untuk event budaya, pernikahan adat, atau kegiatan lainnya.</p>
                    <a href="https://wa.me/6282254970390" target="_blank" class="btn-primary">
                        <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<style>
.anim-fadeup{opacity:0;transform:translateY(38px);transition:opacity .6s ease,transform .6s ease;}
.anim-fadeup.anim-visible{opacity:1;transform:translateY(0);}

.op-gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;grid-auto-rows:220px;}
.op-span2{grid-column:span 2;}
.op-gallery-item{position:relative;border-radius:var(--radius-md);overflow:hidden;cursor:pointer;background:var(--border);}
.op-gallery-item img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;}
.op-gallery-item:hover img{transform:scale(1.06);}
.op-gallery-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(28,10,0,.75) 0%,transparent 50%);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-end;padding:16px;opacity:0;transition:opacity .3s;}
.op-gallery-item:hover .op-gallery-overlay{opacity:1;}
.op-gallery-overlay i{color:white;font-size:1.3rem;margin-bottom:6px;}
.op-gallery-overlay span{font-family:var(--font-ui);font-size:.82rem;font-weight:700;color:white;}

.activity-card{grid-template-columns:100px 1fr !important;}

@media(max-width:768px){
    .op-gallery-grid{grid-template-columns:1fr 1fr;grid-auto-rows:160px;}
    .op-span2{grid-column:span 2;}
}
@media(max-width:480px){
    .op-gallery-grid{grid-template-columns:1fr;grid-auto-rows:200px;}
    .op-span2{grid-column:span 1;}
}
</style>

<script>
function smoothTo(id) {
    event.preventDefault();
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
}

function animateCounter(el) {
    const target = +el.getAttribute('data-target');
    const suffix = el.getAttribute('data-suffix') || '';
    const steps = 60, duration = 1200;
    let current = 0, count = 0;
    const timer = setInterval(() => {
        count++; current += target / steps;
        if (count >= steps) {
            clearInterval(timer);
            el.textContent = suffix === 'K+' ? '10K+' : Math.floor(target) + suffix;
        } else {
            el.textContent = suffix === 'K+' ? Math.floor(current/1000)+'K+' : Math.floor(current)+suffix;
        }
    }, duration / steps);
}
const statObs = new IntersectionObserver(e => {
    e.forEach(en => { if(en.isIntersecting){ document.querySelectorAll('.stat-number[data-target]').forEach(el=>animateCounter(el)); statObs.disconnect(); }});
}, {threshold:0.1});
const statsEl = document.querySelector('.hero-stats');
if(statsEl) statObs.observe(statsEl);

(function(){
    function run(){
        var els = document.querySelectorAll('.anim-fadeup');
        if(!els.length) return;
        var obs = new IntersectionObserver(function(entries){
            entries.forEach(function(entry,idx){
                if(entry.isIntersecting){
                    setTimeout(function(){ entry.target.classList.add('anim-visible'); }, idx*110);
                    obs.unobserve(entry.target);
                }
            });
        },{threshold:0.08});
        els.forEach(function(el){ obs.observe(el); });
    }
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',function(){setTimeout(run,200);});
    else setTimeout(run,200);
})();

function openGalleryLightbox(src, cap) {
    var lb = document.getElementById('opLightbox');
    document.getElementById('opLbImg').src = src;
    document.getElementById('opLbCap').textContent = cap;
    lb.style.display = 'flex';
}
document.getElementById('opLightbox').addEventListener('click', function(e){
    if(e.target === this) this.style.display = 'none';
});
</script>

<style>
.about-grid .about-images {
    opacity: 0;
    transform: translateX(-65px);
    transition: opacity .9s cubic-bezier(.22,.61,.36,1),
                transform .9s cubic-bezier(.22,.61,.36,1);
}
.about-grid .about-images.sa-in { opacity: 1 !important; transform: translateX(0) !important; }

.about-grid .about-content {
    opacity: 0;
    transform: translateX(65px);
    transition: opacity .9s cubic-bezier(.22,.61,.36,1) .25s,
                transform .9s cubic-bezier(.22,.61,.36,1) .25s;
}
.about-grid .about-content.sa-in { opacity: 1 !important; transform: translateX(0) !important; }

.about-features .feature-item {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity .55s ease, transform .55s ease;
}
.about-features .feature-item.sa-fi { opacity: 1 !important; transform: translateY(0) !important; }

.about-content > .btn-primary {
    opacity: 0;
    transform: translateY(18px);
    transition: opacity .5s ease .8s, transform .5s ease .8s;
}
.about-content.sa-in > .btn-primary { opacity: 1 !important; transform: translateY(0) !important; }

.activities-section .section-header {
    opacity: 0;
    transform: translateY(-36px);
    transition: opacity .7s ease, transform .7s ease;
}
.activities-section .section-header.sa-in { opacity: 1 !important; transform: translateY(0) !important; }

section.activities-section .activities-grid .activity-card {
    opacity: 0 !important;
    transform: translateY(42px) !important;
    transition: opacity .65s ease, transform .65s ease,
                box-shadow .35s ease, border-color .35s ease !important;
    position: relative;
    overflow: hidden;
}
section.activities-section .activities-grid .activity-card.sa-in {
    opacity: 1 !important;
    transform: translateY(0) !important;
}
section.activities-section .activities-grid .activity-card.sa-in::after {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0; width: 3px;
    background: linear-gradient(to bottom, #C9890A, #8B2E00);
    border-radius: 0 2px 2px 0;
    opacity: 0; transform: scaleY(0); transform-origin: top;
    transition: opacity .3s, transform .35s;
}
section.activities-section .activities-grid .activity-card.sa-in:hover::after { opacity: 1; transform: scaleY(1); }
section.activities-section .activities-grid .activity-card.sa-in:hover {
    transform: translateX(5px) !important;
    box-shadow: 0 8px 32px rgba(0,0,0,.28) !important;
    border-color: rgba(201,137,10,.4) !important;
}

.kegiatan-cta-wrap {
    opacity: 0;
    transform: translateY(22px);
    transition: opacity .55s ease, transform .55s ease;
}
.kegiatan-cta-wrap.sa-in { opacity: 1 !important; transform: translateY(0) !important; }

.home-rating-summary {
    opacity: 0;
    transform: scale(.88) translateY(20px);
    transition: opacity .7s ease, transform .7s cubic-bezier(.34,1.56,.64,1);
}
.home-rating-summary.sa-in { opacity: 1 !important; transform: scale(1) translateY(0) !important; }

.home-ulasan-card {
    opacity: 0 !important;
    transform: translateY(38px) !important;
    transition: opacity .6s ease, transform .6s ease,
                box-shadow .3s ease !important;
}
.home-ulasan-card.sa-in { opacity: 1 !important; transform: translateY(0) !important; }
.home-ulasan-card.sa-in:hover { transform: translateY(-5px) !important; box-shadow: var(--shadow-md) !important; }

#ulasan [style*="margin-top:44px"] {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity .5s ease, transform .5s ease;
}
#ulasan [style*="margin-top:44px"].sa-in { opacity: 1 !important; transform: translateY(0) !important; }
</style>

<script>
(function () {
    function onEnter(el, cb) {
        if (!el) return;
        var io = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { cb(en.target); obs.unobserve(en.target); }
            });
        }, { threshold: 0, rootMargin: '0px 0px -60px 0px' });
        io.observe(el);
    }

    function onEnterAll(els, delay) {
        if (!els.length) return;
        var io = new IntersectionObserver(function (entries) {
            var vis = [];
            entries.forEach(function (en) { if (en.isIntersecting) vis.push(en.target); });
            vis.forEach(function (el, i) {
                setTimeout(function () { el.classList.add('sa-in'); }, i * (delay || 130));
                io.unobserve(el);
            });
        }, { threshold: 0, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function (el) { io.observe(el); });
    }

    function init() {
        var aboutImg     = document.querySelector('.about-grid .about-images');
        var aboutContent = document.querySelector('.about-grid .about-content');

        if (aboutImg) {
            setTimeout(function () { aboutImg.classList.add('sa-in'); }, 300);
        }
        if (aboutContent) {
            setTimeout(function () {
                aboutContent.classList.add('sa-in');
                aboutContent.querySelectorAll('.about-features .feature-item').forEach(function (fi, i) {
                    setTimeout(function () { fi.classList.add('sa-fi'); }, 500 + i * 170);
                });
            }, 500);
        }

        onEnter(document.querySelector('.activities-section .section-header'), function (el) {
            el.classList.add('sa-in');
        });
        onEnterAll(document.querySelectorAll('.activities-section .activities-grid .activity-card'), 160);
        onEnter(document.querySelector('.kegiatan-cta-wrap'), function (el) {
            setTimeout(function () { el.classList.add('sa-in'); }, 200);
        });

        onEnter(document.querySelector('.home-rating-summary'), function (el) {
            el.classList.add('sa-in');
        });
        onEnterAll(document.querySelectorAll('.home-ulasan-card'), 130);
        onEnter(document.querySelector('#ulasan [style*="margin-top:44px"]'), function (el) {
            setTimeout(function () { el.classList.add('sa-in'); }, 150);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(init, 120); });
    } else {
        setTimeout(init, 120);
    }
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>