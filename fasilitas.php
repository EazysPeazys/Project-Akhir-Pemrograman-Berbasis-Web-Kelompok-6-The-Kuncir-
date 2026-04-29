<?php session_start(); include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas Kami – Rumah Adat Budaya Kota Samarinda</title>
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

<div class="page-header-hero fasilitas-hero">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="section-tag hero-intro-tag"><i class="fas fa-building"></i> Fasilitas</div>
        <h1 class="page-hero-title hero-intro-title">Sarana &amp; <em>Fasilitas</em></h1>
        <p class="hero-intro-sub">Berbagai fasilitas lengkap untuk kenyamanan pengunjung dan penyelenggara acara</p>
    </div>
</div>

<section class="fasilitas-section" id="fasilitas" style="padding-top:80px;">
    <div class="container">
        <div class="fasilitas-grid">
            <div class="fasilitas-card anim-box">
                <div class="fasilitas-icon"><i class="fas fa-restroom"></i></div>
                <h3>Toilet Umum</h3>
                <p>Fasilitas toilet bersih dan terawat tersedia di beberapa titik area komplek, terpisah antara pria dan wanita untuk kenyamanan seluruh pengunjung.</p>
            </div>
            <div class="fasilitas-card anim-box">
                <div class="fasilitas-icon"><i class="fas fa-parking"></i></div>
                <h3>Area Parkir Luas</h3>
                <p>Halaman parkir yang luas mampu menampung ratusan kendaraan roda dua maupun roda empat, termasuk bus dan kendaraan besar untuk rombongan acara.</p>
            </div>
            <div class="fasilitas-card anim-box">
                <div class="fasilitas-icon"><i class="fas fa-microphone-alt"></i></div>
                <h3>Sound System</h3>
                <p>Peralatan audio profesional tersedia untuk mendukung berbagai kegiatan, mulai dari pertunjukan seni, seminar, hingga perhelatan akbar budaya.</p>
            </div>
            <div class="fasilitas-card anim-box">
                <div class="fasilitas-icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Tata Cahaya</h3>
                <p>Sistem pencahayaan panggung modern yang dapat disesuaikan dengan kebutuhan acara, mendukung suasana pertunjukan yang memukau.</p>
            </div>
            <div class="fasilitas-card anim-box">
                <div class="fasilitas-icon"><i class="fas fa-chair"></i></div>
                <h3>Kursi & Meja</h3>
                <p>Ketersediaan kursi dan meja dalam jumlah besar untuk mendukung berbagai format acara, mulai dari pernikahan adat hingga seminar dan rapat resmi.</p>
            </div>
            <div class="fasilitas-card anim-box">
                <div class="fasilitas-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Keamanan 24 Jam</h3>
                <p>Petugas keamanan berjaga selama 24 jam penuh untuk memastikan keamanan dan ketertiban seluruh pengunjung dan penyelenggara acara.</p>
            </div>
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