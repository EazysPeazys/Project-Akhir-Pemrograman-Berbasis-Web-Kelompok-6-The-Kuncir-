<?php session_start(); include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak – Rumah Adat Budaya Kota Samarinda</title>
    <link rel="stylesheet" href="Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

.hero-intro-tag {
    opacity: 0;
    transform: translateY(-24px);
    animation: heroFadeDown .7s ease forwards;
    animation-delay: .2s;
}
.hero-intro-title {
    opacity: 0;
    transform: translateY(-30px);
    animation: heroFadeDown .8s ease forwards;
    animation-delay: .45s;
}
.hero-intro-sub {
    opacity: 0;
    transform: translateY(-20px);
    animation: heroFadeDown .7s ease forwards;
    animation-delay: .7s;
}

@keyframes heroFadeDown {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.anim-box {
    opacity: 0;
    transform: translateY(42px);
    transition: opacity .6s cubic-bezier(.22,.61,.36,1),
                transform .6s cubic-bezier(.22,.61,.36,1);
}
.anim-box.anim-visible {
    opacity: 1;
    transform: translateY(0);
}

.anim-fadeup {
    opacity: 0;
    transform: translateY(42px);
    transition: opacity .7s cubic-bezier(.22,.61,.36,1),
                transform .7s cubic-bezier(.22,.61,.36,1);
}
.anim-fadeup.anim-visible {
    opacity: 1;
    transform: translateY(0);
}
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-header-hero contact-hero">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="section-tag hero-intro-tag"><i class="fas fa-handshake"></i> Hubungi Kami</div>
        <h1 class="page-hero-title hero-intro-title">Informasi &amp; <em>Lokasi</em></h1>
        <p class="hero-intro-sub">Kami siap membantu Anda merencanakan acara budaya yang berkesan</p>
    </div>
</div>

<section class="contact-section" id="contact" style="padding-top:80px;">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <div class="info-card anim-box">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text">
                        <h4>Alamat</h4>
                        <p>Jl. Kadrie Oening No.8 Air Hitam,<br>Kec. Samarinda Ulu, Kota Samarinda,<br>Kalimantan Timur 75243</p>
                    </div>
                </div>
                <div class="info-card anim-box">
                    <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-text">
                        <h4>Telepon</h4>
                        <p>+62 822 5497 0390 (Admin)</p>
                    </div>
                </div>
                <div class="info-card anim-box">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text">
                        <h4>Email</h4>
                        <p>cagarbudayasamarinda@gmail.com</p>
                    </div>
                </div>
                <div class="info-card anim-box">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-text">
                        <h4>Jam Operasional</h4>
                        <p>Senin – Kamis : 08.30 – 15.00 WITA<br>Jumat : 08.30 – 11.00 WITA</p>
                    </div>
                </div>
                <div class="social-links anim-box">
                    <h4>Media Sosial</h4>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/rumahadatkotasamarinda" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/share/1AafgfSLMb/" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/6282254970390" target="_blank" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="contact-map-wrapper anim-fadeup">
                <div class="map-container anim-box">
                    <iframe src="https://www.google.com/maps?q=-0.4722297,117.1295933&hl=id&z=15&output=embed"
                        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <div class="reserve-cta anim-box">
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

<script>
(function () {
    function initScrollAnim() {
        var els = document.querySelectorAll('.anim-box, .anim-fadeup');
        if (!els.length) return;

        var obs = new IntersectionObserver(function (entries) {
            var visible = [];
            entries.forEach(function (entry) {
                if (entry.isIntersecting) visible.push(entry.target);
            });
            visible.forEach(function (el, i) {
                setTimeout(function () {
                    el.classList.add('anim-visible');
                }, i * 130);
                obs.unobserve(el);
            });
        }, { threshold: 0.08 });

        els.forEach(function (el) { obs.observe(el); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(initScrollAnim, 100);
        });
    } else {
        setTimeout(initScrollAnim, 100);
    }
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>