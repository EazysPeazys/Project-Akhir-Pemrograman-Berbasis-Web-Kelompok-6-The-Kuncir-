<?php session_start(); include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami – Rumah Adat Budaya Kota Samarinda</title>
    <link rel="stylesheet" href="Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.anim-box {
    opacity: 0;
    transform: translateY(38px);
    transition: opacity .6s ease, transform .6s ease;
}
.anim-box.anim-visible {
    opacity: 1;
    transform: translateY(0);
}

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
.hero-intro-btn {
    opacity: 0;
    transform: translateY(-16px);
    animation: heroFadeDown .7s ease forwards;
    animation-delay: .95s;
}
@keyframes heroFadeDown {
    to { opacity: 1; transform: translateY(0); }
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

<div class="page-header-hero">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="section-tag hero-intro-tag"><i class="fas fa-feather-alt"></i> Tentang Kami</div>
        <h1 class="page-hero-title hero-intro-title">Gerbang Budaya<br><em>Kota Samarinda</em></h1>
        <p class="hero-intro-sub">Temukan rahasia di balik ukiran dan filosofi hunian asli masyarakat Kutai, Dayak, dan Banjar melalui pengalaman wisata budaya yang otentik dan penuh makna.</p>
    </div>
</div>

<section class="about-section" id="about" style="padding-top:80px;padding-bottom:80px;">
    <div class="container">
        <div class="about-grid">
            <div class="about-images anim-left">
                <div class="img-main">
                    <img src="assets/Rumah_Adat_Banjar_Dayak_Kutai_Kota_Samarinda.jpeg" alt="Rumah Adat Samarinda" onerror="this.src='https://picsum.photos/600/400?grayscale'">
                    <div class="img-badge">
                        <i class="fas fa-award"></i>
                        <span>Rumah Adat Budaya Kota Samarinda</span>
                    </div>
                </div>
                <div class="img-secondary">
                    <img src="assets/Gerbang_Utama_Rumah_adat_budaya_Kota_Samarinda.jpeg" alt="Gerbang Utama Rumah Adat" onerror="this.src='https://picsum.photos/300/300?grayscale&random=2'">
                </div>
            </div>
            <div class="about-content anim-right">
                <div class="section-tag"><i class="fas fa-feather-alt"></i> Tentang Kami</div>
                <h2 class="section-title anim-up">Menjaga Warisan<br><em>Budaya Kota Samarinda</em></h2>
                <div class="about-text">
                    <p>Rumah Adat Budaya Kota Samarinda merupakan pusat pelestarian kebudayaan asli masyarakat Kutai, Dayak dan Banjar di Kota Samarinda, Provinsi Kalimantan Timur. Berdiri sejak tahun 2020, tempat ini menjadi salah satu pusat kebudayaan dan ruang sakral pertemuan tradisi dan modernitas.</p>
                    <p>Rumah Adat Budaya Samarinda dibangun untuk memfasilitasi dan melestarikan keberagaman seni dan budaya di Kota Samarinda dalam skala lokal, nasional, dan internasional.</p>
                    <p>Kami hadir sebagai wadah bagi masyarakat untuk mengenal, merayakan, dan mewariskan kekayaan budaya leluhur kepada generasi penerus. Dari upacara adat, pertunjukan seni, hingga pameran kebudayaan yang semua terpusat di sini.</p>
                </div>
                <div class="about-features">
                    <div class="feature-item anim-box">
                        <div class="feature-icon"><i class="fas fa-home"></i></div>
                        <div class="feature-text">
                            <strong>Arsitektur Autentik</strong>
                            <span>Menggambarkan keindahan bangunan tradisional Kalimantan yang kaya nilai budaya dan filosofi leluhur.</span>
                        </div>
                    </div>
                    <div class="feature-item anim-box">
                        <div class="feature-icon"><i class="fas fa-music"></i></div>
                        <div class="feature-text">
                            <strong>Pertunjukan Seni &amp; Budaya</strong>
                            <span>Menyajikan beragam seni tradisional seperti tari, musik, dan ritual adat yang penuh nilai tradisi.</span>
                        </div>
                    </div>
                    <div class="feature-item anim-box">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <div class="feature-text">
                            <strong>Ruang Komunitas</strong>
                            <span>Menjadi tempat berkumpulnya masyarakat untuk menyelenggarakan berbagai kegiatan adat dan budaya.</span>
                        </div>
                    </div>
                </div>
                <a href="contact.php" class="btn-primary hero-intro-btn" style="margin-top:28px; display:inline-flex;">
                    <i class="fas fa-arrow-right"></i> Selengkapnya
                </a>
            </div>
        </div>
    </div>
</section>

<div class="rumah-adat-divider">
    <div class="container">
        <div class="divider-ornament">
            <span class="divider-line"></span>
            <span class="divider-icon"><i class="fas fa-landmark"></i></span>
            <span class="divider-line"></span>
        </div>
    </div>
</div>

<section class="rumah-adat-section" id="rumah-adat">
    <div class="container">
        <div class="section-header centered" style="margin-bottom:60px;">
            <div class="section-tag"><i class="fas fa-map-marked-alt"></i> Warisan Budaya</div>
            <h2 class="section-title">Tiga Rumah Adat<br><em>Kebanggaan Samarinda</em></h2>
            <p class="section-desc">Kompleks Rumah Adat Budaya Kota Samarinda menaungi tiga bangunan adat yang mencerminkan kekayaan tiga suku besar di Kalimantan Timur</p>
        </div>

        <div class="rumah-adat-card anim-box" id="banjar">
            <div class="rumah-adat-img-wrap">
                <img src="assets/Rumah_Adat_Banjar.jpeg" alt="Rumah Adat Banjar" onerror="this.src='https://picsum.photos/700/500?random=20'">
                <div class="rumah-adat-label banjar-label"><i class="fas fa-home"></i> Rumah Adat Banjar</div>
            </div>
            <div class="rumah-adat-content">
                <div class="rumah-adat-badge banjar-badge">Suku Banjar</div>
                <h3 class="rumah-adat-title">Rumah Adat <em>Banjar</em></h3>
                <div class="rumah-adat-desc">
                    <p>Rumah Adat Banjar atau yang dikenal dengan nama <strong>Rumah Bubungan Tinggi</strong> merupakan representasi budaya masyarakat Banjar yang telah bermukim di Kalimantan sejak berabad-abad silam. Ciri khasnya adalah atap berbentuk segitiga runcing yang menjulang tinggi ke langit, melambangkan keagungan dan martabat pemilikinya.</p>
                    <p>Bangunan ini seluruhnya terbuat dari kayu ulin (kayu besi) yang terkenal kuat dan tahan lama, dengan ornamen ukiran khas Banjar yang menghiasi bagian tiang, dinding, dan pagar. Rumah panggung ini memiliki kolong yang cukup tinggi sebagai bentuk adaptasi terhadap lingkungan rawa dan sungai di Kalimantan.</p>
                    <p>Di kompleks Rumah Adat Budaya Kota Samarinda, bangunan ini berfungsi sebagai ruang pameran koleksi budaya Banjar, sekaligus tempat penyelenggaraan upacara dan kegiatan adat masyarakat Banjar yang masih aktif dilestarikan hingga kini.</p>
                </div>
                <div class="rumah-adat-facts">
                    <div class="fact-item">
                        <i class="fas fa-tree"></i>
                        <div><strong>Material</strong><span>Kayu Ulin (Kayu Besi)</span></div>
                    </div>
                    <div class="fact-item">
                        <i class="fas fa-drafting-compass"></i>
                        <div><strong>Arsitektur</strong><span>Bubungan Tinggi</span></div>
                    </div>
                    <div class="fact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Asal</strong><span>Kalimantan Selatan &amp; Timur</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rumah-adat-card reverse anim-box" id="dayak">
            <div class="rumah-adat-img-wrap">
                <img src="assets/Rumah_Adat_Dayak.jpeg" alt="Rumah Adat Dayak" onerror="this.src='https://picsum.photos/700/500?random=21'">
                <div class="rumah-adat-label dayak-label"><i class="fas fa-home"></i> Rumah Adat Dayak</div>
            </div>
            <div class="rumah-adat-content">
                <div class="rumah-adat-badge dayak-badge">Suku Dayak</div>
                <h3 class="rumah-adat-title">Rumah Adat <em>Dayak</em></h3>
                <div class="rumah-adat-desc">
                    <p>Rumah Adat Dayak atau <strong>Rumah Lamin</strong> adalah simbol kekuatan dan kebersamaan suku Dayak yang merupakan penduduk asli Pulau Kalimantan. Bangunan ini terkenal dengan ukurannya yang panjang dan besar, karena pada dasarnya dirancang untuk dihuni oleh banyak keluarga secara bersama-sama dalam satu atap.</p>
                    <p>Keistimewaan utama Rumah Lamin terletak pada ornamen dan ukiran motif khas Dayak yang penuh makna filosofis — mulai dari motif tumbuhan pakis, hewan mitologi, hingga simbol spiritual yang dipercaya memberi perlindungan bagi penghuni. Tangga masuk berbentuk batang kayu berukir menjadi ikon yang paling mudah dikenali.</p>
                    <p>Di kompleks ini, Rumah Adat Dayak menjadi pusat kegiatan seni pertunjukan seperti tari Kancet Lasan, upacara Gawai, dan berbagai ritual adat lainnya. Bangunan ini juga menyimpan koleksi senjata tradisional, pakaian adat, dan artefak budaya Dayak yang bernilai tinggi.</p>
                </div>
                <div class="rumah-adat-facts">
                    <div class="fact-item">
                        <i class="fas fa-tree"></i>
                        <div><strong>Material</strong><span>Kayu Ulin &amp; Bambu</span></div>
                    </div>
                    <div class="fact-item">
                        <i class="fas fa-drafting-compass"></i>
                        <div><strong>Arsitektur</strong><span>Rumah Lamin (Panjang)</span></div>
                    </div>
                    <div class="fact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Asal</strong><span>Pedalaman Kalimantan</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rumah-adat-card anim-box" id="kutai">
            <div class="rumah-adat-img-wrap">
                <img src="assets/Rumah_Adat_Kutai.jpeg" alt="Rumah Adat Kutai" onerror="this.src='https://picsum.photos/700/500?random=22'">
                <div class="rumah-adat-label kutai-label"><i class="fas fa-home"></i> Rumah Adat Kutai</div>
            </div>
            <div class="rumah-adat-content">
                <div class="rumah-adat-badge kutai-badge">Suku Kutai</div>
                <h3 class="rumah-adat-title">Rumah Adat <em>Kutai</em></h3>
                <div class="rumah-adat-desc">
                    <p>Rumah Adat Kutai atau <strong>Rumah Lamin Adat Kesultanan Kutai</strong> mencerminkan kejayaan Kerajaan Kutai Kartanegara — kerajaan Hindu tertua di Nusantara yang berdiri sejak abad ke-4 Masehi. Bangunan ini memancarkan nuansa kerajaan yang megah dengan tiang-tiang tinggi dan ornamen kerawang yang halus di setiap sudutnya.</p>
                    <p>Ciri khas yang paling menonjol adalah teras lebar yang mengelilingi bangunan dan kerawang ukir motif khas Kutai yang menghiasi pagar dan dinding. Material utama dari kayu ulin pilihan memberikan kesan kokoh sekaligus elegan, mencerminkan status sosial dan kekayaan budaya masyarakat Kutai.</p>
                    <p>Dalam kompleks Rumah Adat Budaya Kota Samarinda, Rumah Adat Kutai difungsikan sebagai ruang utama penerimaan tamu kehormatan dan penyelenggaraan upacara adat Kesultanan. Di sinilah digelar berbagai ritual budaya seperti Erau, perayaan adat terbesar Kutai yang menjadi daya tarik wisatawan dari seluruh Indonesia bahkan mancanegara.</p>
                </div>
                <div class="rumah-adat-facts">
                    <div class="fact-item">
                        <i class="fas fa-tree"></i>
                        <div><strong>Material</strong><span>Kayu Ulin Pilihan</span></div>
                    </div>
                    <div class="fact-item">
                        <i class="fas fa-drafting-compass"></i>
                        <div><strong>Arsitektur</strong><span>Panggung Kerawang</span></div>
                    </div>
                    <div class="fact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Asal</strong><span>Kutai Kartanegara, Kaltim</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta-strip">
    <div class="container">
        <div class="about-cta-inner anim-fadeup">
            <div class="about-cta-text">
                <h3>Ingin Tahu Informasi Lebih Lengkap?</h3>
                <p>Kunjungi kami langsung atau hubungi untuk informasi Rumah Adat Budaya dan reservasi Sekarang!</p>
            </div>
            <div class="about-cta-btns">
                <a href="contact.php" class="btn-primary"><i class="fas fa-envelope"></i> Hubungi Kami</a>
                <a href="gallery.php" class="btn-outline"><i class="fas fa-images"></i> Lihat Galeri</a>
            </div>
        </div>
    </div>
</section>

<style>
.rumah-adat-divider { background: var(--bg-light); padding: 10px 0 0; }
.divider-ornament { display: flex; align-items: center; gap: 20px; padding: 0 0 40px; }
.divider-line { flex: 1; height: 1.5px; background: linear-gradient(90deg, transparent, var(--border), transparent); }
.divider-icon { width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; box-shadow: 0 4px 16px rgba(139,46,0,0.25); flex-shrink: 0; }

.rumah-adat-section { background: var(--bg-light); padding: 0 0 100px; }

.rumah-adat-card { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-bottom: 90px; }

.rumah-adat-card.reverse { direction: rtl; }
.rumah-adat-card.reverse > * { direction: ltr; }
.rumah-adat-card:last-child { margin-bottom: 0; }

.rumah-adat-img-wrap { position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); aspect-ratio: 4/3; }
.rumah-adat-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.7s ease; }
.rumah-adat-card:hover .rumah-adat-img-wrap img { transform: scale(1.05); }

.rumah-adat-label { position: absolute; bottom: 18px; left: 18px; padding: 9px 18px; border-radius: var(--radius-xl); font-family: var(--font-ui); font-size: 0.78rem; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(0,0,0,0.25); backdrop-filter: blur(8px); color: white; }
.banjar-label  { background: rgba(139,46,0,0.85); border: 1px solid rgba(255,255,255,0.2); }
.dayak-label   { background: rgba(30,90,40,0.85);  border: 1px solid rgba(255,255,255,0.2); }
.kutai-label   { background: rgba(100,70,0,0.85);  border: 1px solid rgba(255,255,255,0.2); }

.rumah-adat-badge { display: inline-block; font-family: var(--font-ui); font-size: 0.72rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; padding: 5px 14px; border-radius: 50px; margin-bottom: 14px; }
.banjar-badge { background: rgba(139,46,0,0.1);   color: var(--primary); border: 1px solid rgba(139,46,0,0.25); }
.dayak-badge  { background: rgba(30,100,50,0.1);  color: #1e6432;        border: 1px solid rgba(30,100,50,0.25); }
.kutai-badge  { background: rgba(180,130,0,0.12); color: #7a5500;        border: 1px solid rgba(180,130,0,0.3); }

.rumah-adat-title { font-family: var(--font-display); font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 700; color: var(--text-dark); line-height: 1.2; margin-bottom: 20px; }
.rumah-adat-title em { color: var(--primary); font-style: italic; }

.rumah-adat-desc p { font-size: 1rem; color: var(--text-med); line-height: 1.85; margin-bottom: 14px; }
.rumah-adat-desc p:last-child { margin-bottom: 0; }

.rumah-adat-facts { display: flex; gap: 24px; flex-wrap: wrap; margin-top: 24px; padding-top: 22px; border-top: 1.5px solid var(--border); }
.fact-item { display: flex; align-items: flex-start; gap: 10px; }
.fact-item > i { width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.85rem; flex-shrink: 0; margin-top: 2px; }
.fact-item div { display: flex; flex-direction: column; }
.fact-item strong { font-family: var(--font-ui); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; color: var(--text-light); margin-bottom: 2px; }
.fact-item span { font-family: var(--font-ui); font-size: 0.88rem; font-weight: 600; color: var(--text-dark); }

.about-cta-strip { background: linear-gradient(135deg, var(--primary), var(--primary-light)); padding: 60px 0; }
.about-cta-inner { display: flex; align-items: center; justify-content: space-between; gap: 40px; flex-wrap: wrap; }
.about-cta-text h3 { font-family: var(--font-display); font-size: clamp(1.3rem, 2.5vw, 1.8rem); font-weight: 700; color: white; margin-bottom: 8px; }
.about-cta-text p { color: rgba(255,255,255,0.8); font-size: 1rem; }
.about-cta-btns { display: flex; gap: 14px; flex-wrap: wrap; flex-shrink: 0; }
.about-cta-btns .btn-primary { background: white; color: var(--primary); box-shadow: none; }
.about-cta-btns .btn-primary:hover { background: var(--secondary); color: var(--bg-dark); }
.about-cta-btns .btn-outline { color: white; border-color: rgba(255,255,255,0.6); }
.about-cta-btns .btn-outline:hover { background: white; color: var(--primary); }

@media (max-width: 900px) {
    .rumah-adat-card, .rumah-adat-card.reverse { grid-template-columns: 1fr; direction: ltr; gap: 30px; }
    .rumah-adat-img-wrap { aspect-ratio: 16/9; }
    .about-cta-inner { flex-direction: column; text-align: center; }
    .about-cta-btns { justify-content: center; }
}
@media (max-width: 600px) {
    .rumah-adat-facts { flex-direction: column; gap: 14px; }
}
</style>
<?php include 'footer.php'; ?>

<script>
(function(){
    function runAnim() {
        var els = document.querySelectorAll('.anim-box');
        if (!els.length) return;
        var obs = new IntersectionObserver(function(entries){
            entries.forEach(function(entry, i){
                if (entry.isIntersecting) {
                    setTimeout(function(){ entry.target.classList.add('anim-visible'); }, i * 120);
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
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><style>
.anim-left {
    opacity: 0;
    transform: translateX(-55px);
    transition: opacity .85s cubic-bezier(.22,.61,.36,1), transform .85s cubic-bezier(.22,.61,.36,1);
}
.anim-right {
    opacity: 0;
    transform: translateX(55px);
    transition: opacity .85s cubic-bezier(.22,.61,.36,1), transform .85s cubic-bezier(.22,.61,.36,1);
}
.anim-left.anim-visible,
.anim-right.anim-visible {
    opacity: 1 !important;
    transform: translateX(0) !important;
}
</style>
<script>
(function(){
    function runAllAnim() {
        var scrollEls = document.querySelectorAll('.anim-box, .anim-fadeup');
        if (scrollEls.length) {
            var obs = new IntersectionObserver(function(entries){
                var hits = [];
                entries.forEach(function(e){ if(e.isIntersecting) hits.push(e.target); });
                hits.forEach(function(el, i){
                    setTimeout(function(){ el.classList.add('anim-visible'); }, i * 130);
                    obs.unobserve(el);
                });
            }, { threshold: 0.08 });
            scrollEls.forEach(function(el){ obs.observe(el); });
        }

        var imgEl     = document.querySelector('.about-grid .about-images.anim-left');
        var contentEl = document.querySelector('.about-grid .about-content.anim-right');

        if (imgEl) {
            setTimeout(function(){ imgEl.classList.add('anim-visible'); }, 300);
        }
        if (contentEl) {
            setTimeout(function(){
                contentEl.classList.add('anim-visible');
                contentEl.querySelectorAll('.about-features .feature-item.anim-box').forEach(function(fi, i){
                    setTimeout(function(){ fi.classList.add('anim-visible'); }, 500 + i * 170);
                });
            }, 500);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){ setTimeout(runAllAnim, 120); });
    } else {
        setTimeout(runAllAnim, 120);
    }
})();
</script>