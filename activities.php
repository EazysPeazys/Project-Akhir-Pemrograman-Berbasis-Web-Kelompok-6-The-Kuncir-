<?php session_start(); include 'koneksi.php';
$result_activities = mysqli_query($koneksi, "SELECT * FROM kegiatan WHERE status != 'dibatalkan' ORDER BY tanggal ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan & Acara – Rumah Adat Budaya Kota Samarinda</title>
    <link rel="stylesheet" href="Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.hero-intro-tag {
    opacity: 0; transform: translateY(-24px);
    animation: heroFadeDown .7s ease forwards; animation-delay: .2s;
}
.hero-intro-title {
    opacity: 0; transform: translateY(-30px);
    animation: heroFadeDown .8s ease forwards; animation-delay: .45s;
}
.hero-intro-sub {
    opacity: 0; transform: translateY(-20px);
    animation: heroFadeDown .7s ease forwards; animation-delay: .7s;
}
@keyframes heroFadeDown { to { opacity: 1; transform: translateY(0); } }
.anim-box { opacity:0; transform:translateY(38px); transition:opacity .6s cubic-bezier(.22,.61,.36,1),transform .6s cubic-bezier(.22,.61,.36,1); }
.anim-box.anim-visible { opacity:1; transform:translateY(0); }
.anim-fadeup { opacity:0; transform:translateY(38px); transition:opacity .7s cubic-bezier(.22,.61,.36,1),transform .7s cubic-bezier(.22,.61,.36,1); }
.anim-fadeup.anim-visible { opacity:1; transform:translateY(0); }
</style>
</head>
<body>


<?php include 'navbar.php'; ?>

<div class="page-header-hero activities-hero">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="section-tag hero-intro-tag"><i class="fas fa-calendar-star"></i> Jadwal Kegiatan</div>
        <h1 class="page-hero-title hero-intro-title">Kegiatan &amp; <em>Acara</em></h1>
        <p class="hero-intro-sub">Berbagai kegiatan budaya yang diselenggarakan di Rumah Adat Budaya Kota Samarinda</p>
    </div>
</div>

<section class="activities-section" id="activities" style="padding-top:80px;">
    <div class="activities-bg"></div>
    <div class="container">
        <div class="activities-grid">
            <?php if (mysqli_num_rows($result_activities) > 0):
                while ($row = mysqli_fetch_assoc($result_activities)):
                    $hariInd = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                    $hariIndo = $hariInd[date('l', strtotime($row['tanggal']))] ?? date('l', strtotime($row['tanggal']));
            ?>
            <div class="activity-card anim-box">
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
                        <span><i class="fas fa-clock"></i> <?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['lokasi'] ?? 'Aula Utama') ?></span>
                    </div>
                </div>
</div>
            <?php endwhile; else: ?>
            <div class="no-activity">
                <i class="fas fa-calendar-times"></i>
                <p>Belum ada jadwal kegiatan yang tersedia saat ini.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<style>
.anim-fadeup {
    opacity: 0;
    transform: translateY(38px);
    transition: opacity .6s ease, transform .6s ease;
}
.anim-fadeup.anim-visible {
    opacity: 1;
    transform: translateY(0);
}
</style>

<script>
(function(){
    function runAnim() {
        var els = document.querySelectorAll('.anim-fadeup');
        if (!els.length) return;
        var obs = new IntersectionObserver(function(entries){
            entries.forEach(function(entry, idx){
                if (entry.isIntersecting) {
                    setTimeout(function(){ entry.target.classList.add('anim-visible'); }, idx * 110);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        els.forEach(function(el){ obs.observe(el); });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){ setTimeout(runAnim, 150); });
    } else {
        setTimeout(runAnim, 150);
    }
})();
</script>
<?php include 'footer.php'; ?>
<script>
</script>

<script>
(function(){
    function runAnim() {
        var els = document.querySelectorAll('.anim-box:not(.anim-visible)');
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry, i) {
                if (entry.isIntersecting) {
                    setTimeout(function(){ entry.target.classList.add('anim-visible'); }, i * 120);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        els.forEach(function(el){ obs.observe(el); });
    }
    document.addEventListener('DOMContentLoaded', function(){ setTimeout(runAnim, 150); });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>