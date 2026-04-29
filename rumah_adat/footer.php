<?php if (defined('FOOTER_INCLUDED')) return; define('FOOTER_INCLUDED', true); ?>
<footer class="footer">
    <div class="footer-pattern"></div>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">
                    <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:44px;height:44px;flex-shrink:0;">
                        <circle cx="22" cy="22" r="20.5" stroke="#C9890A" stroke-width="1" stroke-dasharray="4 2"/>
                        <circle cx="22" cy="22" r="16" fill="url(#fLogoGrad)"/>
                        <polygon points="22,7 35,18 9,18" fill="#C9890A" opacity="0.9"/>
                        <polygon points="22,11 32,18 12,18" fill="#8B2E00" opacity="0.95"/>
                        <rect x="12" y="18" width="20" height="11" rx="1" fill="#6B1E00"/>
                        <rect x="19" y="23" width="6" height="6" rx="1" fill="#C9890A" opacity="0.85"/>
                        <rect x="13" y="18" width="2" height="11" rx="1" fill="#8B2E00"/>
                        <rect x="29" y="18" width="2" height="11" rx="1" fill="#8B2E00"/>
                        <rect x="14" y="29" width="2" height="4" rx="1" fill="#C9890A" opacity="0.7"/>
                        <rect x="21" y="29" width="2" height="4" rx="1" fill="#C9890A" opacity="0.7"/>
                        <rect x="28" y="29" width="2" height="4" rx="1" fill="#C9890A" opacity="0.7"/>
                        <circle cx="22" cy="6.5" r="1.5" fill="#C9890A"/>
                        <rect x="14" y="20" width="4" height="3" rx="0.8" fill="#C9890A" opacity="0.6"/>
                        <rect x="26" y="20" width="4" height="3" rx="0.8" fill="#C9890A" opacity="0.6"/>
                        <path d="M12 34 Q15.5 32.5 19 34 Q22 35.5 25 34 Q28.5 32.5 32 34" stroke="#C9890A" stroke-width="0.8" fill="none" opacity="0.6"/>
                        <defs>
                            <radialGradient id="fLogoGrad" cx="50%" cy="40%" r="60%">
                                <stop offset="0%" stop-color="#3a1200"/>
                                <stop offset="100%" stop-color="#1C0A00"/>
                            </radialGradient>
                        </defs>
                    </svg>
                    <div class="logo-text">
                        <span class="logo-main">Rumah Adat</span>
                        <span class="logo-sub">Budaya Kota Samarinda</span>
                    </div>
                </div>
                <p>Harmoni tiga pilar budaya dalam satu atap yang megah. Dedikasi tulus untuk menjaga napas peradaban asli Kalimantan Timur agar tak lekang oleh waktu.</p>
                <div class="footer-socials">
                    <a href="https://www.instagram.com/rumahadatkotasamarinda" target="_blank" class="footer-social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.facebook.com/share/1AafgfSLMb/" target="_blank" class="footer-social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://wa.me/6282254970390" target="_blank" class="footer-social-btn"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="index.php"><i class="fas fa-chevron-right"></i> Beranda</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right"></i> Tentang Kami</a></li>
                    <li><a href="activities.php"><i class="fas fa-chevron-right"></i> Kegiatan</a></li>
                    <li><a href="gallery.php"><i class="fas fa-chevron-right"></i> Galeri</a></li>
                    <li><a href="fasilitas.php"><i class="fas fa-chevron-right"></i> Fasilitas</a></li>
                    <li><a href="ulasan.php"><i class="fas fa-chevron-right"></i> Ulasan</a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Kontak</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Layanan</h4>
                <ul>
                    <li><a href="about.php#rumah-adat"><i class="fas fa-chevron-right"></i> Sejarah Rumah Adat</a></li>
                    <li><a href="gallery.php"><i class="fas fa-chevron-right"></i> Koleksi Galeri</a></li>
                    <li><a href="activities.php"><i class="fas fa-chevron-right"></i> Jadwal Kegiatan</a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Hubungi Kami</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Rumah Adat Budaya Kota Samarinda. All Rights Reserved.</p>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.25);">Dinas Pendidikan dan Kebudayaan Kota Samarinda</p>
        </div>
    </div>
</footer>

<button class="scroll-top" id="scrollTop" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
.footer-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.footer-brand p {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.45);
    line-height: 1.75;
    margin-bottom: 18px;
}
.footer-socials {
    display: flex;
    gap: 10px;
}
.footer-social-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(201,137,10,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.6);
    font-size: 0.9rem;
    transition: all 0.3s ease;
    text-decoration: none;
}
.footer-social-btn:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    transform: translateY(-3px);
}
.footer-links h4 {
    font-family: var(--font-ui);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--secondary);
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(201,137,10,0.15);
}
.footer-links ul {
    display: flex;
    flex-direction: column;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-links a {
    font-family: var(--font-ui);
    font-size: 0.88rem;
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}
.footer-links a i {
    font-size: 0.55rem;
    color: var(--secondary);
    opacity: 0.6;
    transition: all 0.25s;
}
.footer-links a:hover {
    color: var(--secondary);
    padding-left: 4px;
}
.footer-links a:hover i {
    opacity: 1;
}
.footer-bottom {
    padding: 22px 0;
    border-top: 1px solid rgba(201,137,10,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.footer-bottom p {
    font-family: var(--font-ui);
    font-size: 0.82rem;
    color: rgba(255,255,255,0.3);
}
</style>

<script>
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
    document.getElementById('hamburger').classList.toggle('active');
}
function scrollToTop() { window.scrollTo({top:0,behavior:'smooth'}); }
window.addEventListener('scroll', () => {
    const btn = document.getElementById('scrollTop');
    if (btn) btn.style.opacity = window.scrollY > 300 ? '1' : '0';
});
</script>