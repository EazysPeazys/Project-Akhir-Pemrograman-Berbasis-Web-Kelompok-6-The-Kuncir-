# 🏛️ Rumah Adat Budaya Kota Samarinda | Website Pelestarian Budaya 🌿

Selamat datang di repository **Website Rumah Adat Budaya Kota Samarinda**. Website ini adalah platform informasi dan reservasi digital untuk melestarikan budaya Kutai, Dayak, dan Banjar di Kota Samarinda, Kalimantan Timur.

---

## 👤 Anggota Team The Kuncir (Kelompok 6) Kelas C

| Nama | NIM |
|------|-----|
| **Nabil Daffa Athalasyah** | 2409116090 |
| **Moreno Ferdinand Farhantino** | 2409116097 |
| **Danial Hirzan Akbary** | 2409116098 |
| **Reswara Ganendra Rashi Dewa** | 2409116100 |

- **Mata Kuliah**: Project Akhir Praktikum Pemrograman Berbasis Web 2026
- **Tech Stack**: PHP · MySQL · CSS · Hosting (InfinityFree.com)

---

## 📝 Deskripsi Website

**Website Rumah Adat Budaya Kota Samarinda** hadir sebagai platform digital untuk mempromosikan dan melestarikan warisan budaya tiga suku besar Kalimantan Timur — **Kutai, Dayak, dan Banjar** — kepada masyarakat luas.

Website ini menyediakan informasi lengkap tentang rumah adat, jadwal kegiatan budaya, galeri foto, fasilitas yang tersedia, serta sistem reservasi tempat bagi masyarakat yang ingin menyelenggarakan acara di Rumah Adat Budaya Kota Samarinda. Dengan tampilan yang modern dan responsif, website ini dirancang agar mudah diakses oleh semua kalangan.

---

## 🚀 Fitur Utama

1. **Beranda (Home)** — Hero section dengan animasi intro, statistik, dan preview kegiatan mendatang.
2. **Tentang Kami (About Us)** — Profil lengkap tiga rumah adat: Banjar, Dayak, dan Kutai beserta visi & misi.
3. **Kegiatan (Activities)** — Jadwal kegiatan budaya dengan filter kategori dan sistem reservasi.
4. **Galeri (Gallery)** — Slideshow foto interaktif dengan filter per kategori dan lightbox viewer.
5. **Fasilitas** — Informasi ruangan dan fasilitas tersedia beserta jam operasional.
6. **Ulasan (Rating)** — Sistem rating bintang dan komentar terbuka untuk semua pengunjung.
7. **Kontak** — Peta lokasi Google Maps, informasi kontak, dan tombol WhatsApp.
8. **Dashboard User** — Manajemen reservasi pribadi (CRUD), profil, dan ulasan.
9. **Dashboard Admin** — Kelola kegiatan, galeri foto, manajemen ulasan, dan laporan.

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Fungsi |
|-----------|--------|
| **PHP Native** | Backend & server-side logic |
| **MySQL** | Database management |
| **HTML5** | Struktur halaman web |
| **CSS** | Styling & animasi UI |
| **JavaScript (Vanilla)** | Interaktivitas & DOM manipulation |
| **Font Awesome 6.5** | Icon library |
| **Google Fonts** | Typography (Cinzel, Crimson Pro, Nunito) |
| **Google Maps Embed** | Integrasi peta lokasi |

---

## 🗂️ Struktur Project

```
📁 Website-Rumah-Adat-Budaya-Samarinda/
│
├── 📁 admin/
│   ├── dashboard.php          ← Dashboard admin utama
│   ├── kegiatan.php           ← Kelola kegiatan/acara
│   ├── galeri.php             ← Kelola foto galeri
│   └── ulasan.php             ← Kelola ulasan pengunjung
│
├── 📁 assets/
│   ├── 📁 gallery/            ← Foto galeri upload admin
│   ├── Rumah_Adat_Banjar.jpeg
│   ├── Rumah_Adat_Dayak.jpeg
│   ├── Rumah_Adat_Kutai.jpeg
│   └── ... (foto lainnya)
│
├── index.php                  ← Beranda / Home
├── about.php                  ← Tentang Kami
├── activities.php             ← Kegiatan & Acara
├── gallery.php                ← Galeri Foto
├── fasilitas.php              ← Fasilitas
├── ulasan.php                 ← Rating & Ulasan
├── contact.php                ← Kontak & Lokasi
├── dashboard.php              ← Dashboard User
├── login.php                  ← Login & Register
├── logout.php                 ← Logout
├── navbar.php                 ← Navbar (shared)
├── footer.php                 ← Footer (shared)
├── koneksi.php                ← Koneksi Database
└── Style.css                  ← Main stylesheet
```

---

## 🗄️ Database

**Nama Database**: `rumah_adat_samarinda`

| Tabel | Fungsi |
|-------|--------|
| `users` | Data akun pengguna (user & admin) |
| `kegiatan` | Data kegiatan/acara & reservasi |
| `ulasan` | Data ulasan & rating pengunjung |
| `galeri` | Data foto galeri (upload admin) |

---

## 📸 Dokumentasi Website

| Beranda | Tentang Kami | Kegiatan |
|:-------:|:------------:|:--------:|
| ![Beranda](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Beranda.png) | ![Tentang Kami](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Tentang%20Kami.png) | ![Kegiatan](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Kegiatan.png) |

| Galeri | Fasilitas | Ulasan |
|:------:|:---------:|:------:|
| ![Galeri](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Galeri.png) | ![Fasilitas](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Fasilitas.png) | ![Ulasan](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Ulasan.png) |

| Kontak | Dashboard User | Dashboard Admin |
|:------:|:--------------:|:---------------:|
| ![Kontak](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Kontak.png) | ![Dashboard User](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Dashboard%20User.png) | ![Dashboard Admin](https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-/blob/88470659f76596ee98f53075e11b84b5f0cdb3e3/Dashboard%20Admin.png) |

---

## ⚙️ Cara Menjalankan Project

### Prasyarat
- **XAMPP** (PHP 8.x + MySQL) atau **Laragon**
- Browser modern (Chrome, Firefox, Edge)

### Langkah-langkah

**1. Clone repository ini**
```bash
git clone https://github.com/EazysPeazys/Project-Akhir-Pemrograman-Berbasis-Web-Kelompok-6-The-Kuncir-.git
```

**2. Pindahkan folder ke direktori server**
Untuk XAMPP:
C:/xampp/htdocs/
Untuk Laragon:
C:/laragon/www/

**3. Import database**
- Buka `phpMyAdmin` → `http://localhost/phpmyadmin`
- Buat database baru: `rumah_adat_samarinda`
- Import file: `database_fixed.sql`

**4. Edit koneksi database di `koneksi.php`**
```php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "rumah_adat_samarinda";
```

**5. Akses website di browser**
http://localhost/Website-Rumah-Adat-Budaya-Samarinda/

**6. Login sebagai Admin**
Username : admin
Password : admin123

---

## 🌐 Live Demo

> 🔗 **[rumahadatbudayasamarinda.great-site.net](https://rumahadatsamarinda.wuaze.com/)**

---

## 📄 Lisensi

Project ini dibuat untuk memenuhi tugas **Project Akhir Praktikum Mata Kuliah Pemrograman Berbasis Web** — Universitas Mulawarman 2026.

---

*Dibuat oleh **Team The Kuncir** (Kelompok 6) — Sistem Informasi, Kelas C, 2024*
---
