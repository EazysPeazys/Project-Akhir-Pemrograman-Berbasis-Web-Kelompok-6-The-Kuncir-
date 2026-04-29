<?php
session_start();
include 'koneksi.php';

// Pastikan tabel galeri ada
mysqli_query($koneksi,"CREATE TABLE IF NOT EXISTS galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    kategori VARCHAR(50) DEFAULT 'bangunan',
    nama_file VARCHAR(255),
    urutan INT DEFAULT 0,
    created_at DATETIME DEFAULT NOW(),
    updated_at DATETIME
)");

// Ambil semua foto dari database, urut by urutan lalu terbaru
$galeri_db = mysqli_query($koneksi, "SELECT * FROM galeri ORDER BY urutan ASC, id DESC");
$galeri_rows = [];
$kat_labels = ['bangunan'=>'Bangunan','kegiatan'=>'Kegiatan / Acara','lainnya'=>'Lainnya'];
while ($gr = mysqli_fetch_assoc($galeri_db)) {
    $src = $gr['nama_file'];
    if (!empty($src)) {
        if (!preg_match('/^https?:\/\//', $src) && strpos($src,'assets') !== 0) {
            $src = 'assets/gallery/' . $src;
        }
    } else {
        $src = 'https://picsum.photos/1200/700?random=' . $gr['id'];
    }
    $galeri_rows[] = [
        'cat'      => $gr['kategori'],
        'catLabel' => $kat_labels[$gr['kategori']] ?? ucfirst($gr['kategori']),
        'src'      => $src,
        'fallback' => 'https://picsum.photos/1200/700?random=' . $gr['id'],
        'title'    => $gr['judul'],
        'desc'     => $gr['deskripsi'] ?? '',
    ];
}

// Fallback foto statis jika database kosong
$fallback_static = [
    ['cat'=>'bangunan','catLabel'=>'Bangunan','src'=>'assets/Rumah_Adat_Banjar_Dayak_Kutai_Kota_Samarinda.jpeg','fallback'=>'assets/Gerbang_Utama_Rumah_adat_budaya_Kota_Samarinda.jpeg','title'=>'Kompleks Rumah Adat Budaya Kota Samarinda','desc'=>'Tampak depan kompleks Rumah Adat Budaya Kota Samarinda yang menampilkan tiga bangunan adat dari suku Kutai, Dayak, dan Banjar secara berdampingan — simbol kerukunan budaya di Kalimantan Timur.'],
    ['cat'=>'bangunan','catLabel'=>'Bangunan','src'=>'assets/Gerbang_Utama_Rumah_adat_budaya_Kota_Samarinda.jpeg','fallback'=>'https://picsum.photos/1200/700?random=30','title'=>'Gerbang Utama Rumah Adat Budaya Kota Samarinda','desc'=>'Gerbang utama Rumah Adat Kota Samarinda yang berada di Jl. Kadrie Oening No.8, Air Hitam, Samarinda.'],
    ['cat'=>'bangunan','catLabel'=>'Bangunan','src'=>'assets/Rumah_Adat_Banjar.jpeg','fallback'=>'https://picsum.photos/1200/700?random=20','title'=>'Rumah Adat Banjar – Bubungan Tinggi','desc'=>'Rumah Adat Banjar dengan ciri khas atap Bubungan Tinggi yang menjulang. Dibangun dari kayu ulin berkualitas tinggi dengan ornamen ukiran khas Banjar.'],
    ['cat'=>'bangunan','catLabel'=>'Bangunan','src'=>'assets/Rumah_Adat_Dayak.jpeg','fallback'=>'https://picsum.photos/1200/700?random=21','title'=>'Rumah Adat Dayak – Rumah Lamin','desc'=>'Rumah Lamin khas suku Dayak dengan ornamen ukiran motif khas yang penuh makna filosofis.'],
    ['cat'=>'bangunan','catLabel'=>'Bangunan','src'=>'assets/Rumah_Adat_Kutai.jpeg','fallback'=>'https://picsum.photos/1200/700?random=22','title'=>'Rumah Adat Kutai – Lamin Kesultanan','desc'=>'Rumah Adat Kutai mencerminkan kejayaan Kerajaan Kutai Kartanegara, kerajaan Hindu tertua di Nusantara.'],
    ['cat'=>'kegiatan','catLabel'=>'Kegiatan / Acara','src'=>'assets/Pentas_Seni_Budaya_Samarinda_2023.jpeg','fallback'=>'https://picsum.photos/1200/700?random=31','title'=>'Pentas Seni Budaya Kota Samarinda 2023','desc'=>'Pentas Seni Budaya Kota Samarinda Tahun 2023 yang diselenggarakan oleh Dinas Pendidikan dan Kebudayaan Kota Samarinda.'],
    ['cat'=>'kegiatan','catLabel'=>'Kegiatan / Acara','src'=>'assets/Samarinda_Cultural_Fest_2025.jpeg','fallback'=>'https://picsum.photos/1200/700?random=32','title'=>'Samarinda Cultural Fest 2025','desc'=>'Samarinda Cultural Fest 2025 "Gelar Adat Tradisi" yang digelar pada 22–27 Juli 2025 oleh Pemerintah Kota Samarinda.'],
];

$gallery_final = !empty($galeri_rows) ? $galeri_rows : $fallback_static;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto – Rumah Adat Budaya Kota Samarinda</title>
    <link rel="stylesheet" href="Style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.hero-intro-tag { opacity:0; transform:translateY(-24px); animation:heroFadeDown .7s ease forwards; animation-delay:.2s; }
.hero-intro-title { opacity:0; transform:translateY(-30px); animation:heroFadeDown .8s ease forwards; animation-delay:.45s; }
.hero-intro-sub { opacity:0; transform:translateY(-20px); animation:heroFadeDown .7s ease forwards; animation-delay:.7s; }
@keyframes heroFadeDown { to { opacity:1; transform:translateY(0); } }
</style>

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
</style>
</head>
<body>


<?php include 'navbar.php'; ?>

<div class="page-header-hero gallery-hero">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="section-tag hero-intro-tag"><i class="fas fa-images"></i> Galeri</div>
        <h1 class="page-hero-title hero-intro-title">Potret <em>Keindahan Budaya</em></h1>
        <p class="hero-intro-sub">Koleksi foto dan momen bersejarah dari Rumah Adat Budaya Kota Samarinda</p>
    </div>
</div>

<section class="gallery-section" id="gallery" style="padding-top:80px;padding-bottom:100px;">
    <div class="container">

        <div class="gallery-filter">
            <button class="filter-btn active" data-filter="all">Semua</button>
            <button class="filter-btn" data-filter="bangunan">Bangunan</button>
            <button class="filter-btn" data-filter="kegiatan">Kegiatan / Acara</button>
            <button class="filter-btn" data-filter="lainnya">Lainnya</button>
        </div>

        <div class="gallery-slideshow-wrap anim-fadeup">
            <div class="gallery-viewer anim-box">
                <button class="gallery-nav prev" id="galleryPrev" onclick="changeSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="gallery-main-img-wrap">
                    <img id="galleryMainImg" src="" alt="" class="gallery-main-img">
                    <div class="gallery-main-overlay" id="galleryOverlay"></div>
                </div>
                <button class="gallery-nav next" id="galleryNext" onclick="changeSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="gallery-counter">
                    <span id="galleryCurrent">1</span> / <span id="galleryTotal">1</span>
                </div>

                <div class="gallery-caption-box">
                    <div class="gallery-caption-cat" id="galleryCatLabel"></div>
                    <h3 class="gallery-caption-title" id="galleryTitle"></h3>
                    <p class="gallery-caption-desc" id="galleryDesc"></p>
                </div>
            </div>

            <div class="gallery-thumbs anim-box" id="galleryThumbs"></div>
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

<style>
.gallery-slideshow-wrap {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.gallery-viewer {
    position: relative;
    background: var(--bg-dark);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    min-height: 520px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gallery-main-img-wrap {
    width: 100%;
    height: 520px;
    position: relative;
    overflow: hidden;
}
.gallery-main-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: opacity 0.45s ease, transform 0.45s ease;
}
.gallery-main-img.transitioning {
    opacity: 0;
    transform: scale(1.04);
}
.gallery-main-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(28,10,0,0.85) 0%, rgba(28,10,0,0.1) 50%, transparent 100%);
}

.gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    border: 1.5px solid rgba(255,255,255,0.25);
    color: white;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(8px);
}
.gallery-nav.prev { left: 20px; }
.gallery-nav.next { right: 20px; }
.gallery-nav:hover {
    background: var(--primary);
    border-color: var(--primary);
    transform: translateY(-50%) scale(1.1);
}

.gallery-counter {
    position: absolute;
    top: 18px;
    right: 20px;
    font-family: var(--font-ui);
    font-size: 0.82rem;
    font-weight: 700;
    color: rgba(255,255,255,0.7);
    background: rgba(0,0,0,0.4);
    padding: 5px 14px;
    border-radius: 20px;
    backdrop-filter: blur(8px);
    z-index: 5;
}

.gallery-caption-box {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 30px 36px 28px;
    z-index: 5;
}
.gallery-caption-cat {
    display: inline-block;
    font-family: var(--font-ui);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--secondary);
    background: rgba(201,137,10,0.15);
    border: 1px solid rgba(201,137,10,0.35);
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 10px;
}
.gallery-caption-title {
    font-family: var(--font-display);
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
    margin-bottom: 6px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.4);
}
.gallery-caption-desc {
    font-family: var(--font-body);
    font-size: 0.98rem;
    color: rgba(255,255,255,0.75);
    line-height: 1.65;
    max-width: 680px;
}

.gallery-thumbs {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 10px;
}
.gallery-thumb {
    height: 80px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    cursor: pointer;
    border: 2.5px solid transparent;
    transition: all 0.3s ease;
    opacity: 0.65;
}
.gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}
.gallery-thumb:hover { opacity: 0.9; transform: translateY(-3px); }
.gallery-thumb.active {
    border-color: var(--secondary);
    opacity: 1;
    box-shadow: 0 0 14px rgba(201,137,10,0.4);
}
.gallery-thumb.active img { transform: scale(1.05); }

.gallery-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
}
.gallery-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--border);
    cursor: pointer;
    transition: all 0.3s;
}
.gallery-dot.active {
    background: var(--secondary);
    transform: scale(1.4);
}

.gallery-no-photo {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 520px;
    color: rgba(255,255,255,0.3);
    gap: 16px;
}
.gallery-no-photo i { font-size: 3rem; }

@media (max-width: 768px) {
    .gallery-main-img-wrap, .gallery-viewer { min-height: 280px; }
    .gallery-main-img-wrap { height: 280px; }
    .gallery-caption-box { padding: 20px; }
    .gallery-caption-title { font-size: 1.1rem; }
    .gallery-thumbs { grid-template-columns: repeat(auto-fill, minmax(72px, 1fr)); }
    .gallery-thumb { height: 60px; }
}
</style>

<script>
// Data galeri langsung dari database (sinkron dengan admin)
const galleryData = <?php echo json_encode($gallery_final, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

let currentFilter = 'all';
let filteredData = [...galleryData];
let currentIdx = 0;

function getFiltered() {
    return currentFilter === 'all'
        ? galleryData
        : galleryData.filter(d => d.cat === currentFilter);
}

function renderThumbs() {
    const thumbsEl = document.getElementById('galleryThumbs');
    thumbsEl.innerHTML = '';
    filteredData.forEach((item, i) => {
        const div = document.createElement('div');
        div.className = 'gallery-thumb' + (i === currentIdx ? ' active' : '');
        div.onclick = () => goToSlide(i);
        const img = document.createElement('img');
        img.src = item.src;
        img.alt = item.title;
        img.onerror = function(){ this.src = item.fallback; };
        div.appendChild(img);
        thumbsEl.appendChild(div);
    });
}

function updateMain(idx, animate = true) {
    const item = filteredData[idx];
    if (!item) return;

    const imgEl = document.getElementById('galleryMainImg');
    const titleEl = document.getElementById('galleryTitle');
    const descEl = document.getElementById('galleryDesc');
    const catEl = document.getElementById('galleryCatLabel');
    const currEl = document.getElementById('galleryCurrent');
    const totalEl = document.getElementById('galleryTotal');

    if (animate) {
        imgEl.classList.add('transitioning');
        setTimeout(() => {
            imgEl.src = item.src;
            imgEl.onerror = function(){ this.src = item.fallback; };
            titleEl.textContent = item.title;
            descEl.textContent = item.desc;
            catEl.textContent = item.catLabel;
            currEl.textContent = idx + 1;
            totalEl.textContent = filteredData.length;
            imgEl.classList.remove('transitioning');
        }, 280);
    } else {
        imgEl.src = item.src;
        imgEl.onerror = function(){ this.src = item.fallback; };
        titleEl.textContent = item.title;
        descEl.textContent = item.desc;
        catEl.textContent = item.catLabel;
        currEl.textContent = idx + 1;
        totalEl.textContent = filteredData.length;
    }

    document.querySelectorAll('.gallery-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === idx);
    });

    const activeThumb = document.querySelectorAll('.gallery-thumb')[idx];
    if (activeThumb) {
        const thumbsContainer = document.getElementById('galleryThumbs');
        if (thumbsContainer) {
            const thumbLeft = activeThumb.offsetLeft;
            const thumbWidth = activeThumb.offsetWidth;
            const containerWidth = thumbsContainer.offsetWidth;
            const scrollLeft = thumbLeft - (containerWidth / 2) + (thumbWidth / 2);
            thumbsContainer.scrollTo({ left: scrollLeft, behavior: 'smooth' });
        }
    }
}

function goToSlide(idx) {
    currentIdx = idx;
    updateMain(idx);
}

function changeSlide(dir) {
    currentIdx = (currentIdx + dir + filteredData.length) % filteredData.length;
    updateMain(currentIdx);
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        filteredData = getFiltered();
        currentIdx = 0;
        renderThumbs();
        updateMain(0, false);
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') changeSlide(-1);
    if (e.key === 'ArrowRight') changeSlide(1);
});

filteredData = getFiltered();
renderThumbs();
updateMain(0, false);
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