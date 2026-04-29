<?php
// ══════════════════════════════════════════════════════
//  KONEKSI DATABASE
//  Ganti nilai di bawah sesuai environment yang dipakai
// ══════════════════════════════════════════════════════

// ── DETEKSI OTOMATIS: localhost vs hosting ─────────────
$host_now     = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
$is_localhost = (
    in_array($host_now, ['localhost', '127.0.0.1', '::1'])
    || str_contains($host_now, 'localhost')
    || str_contains($host_now, '127.0.0.1')
    || str_ends_with($host_now, '.test')    // Laragon: project_fix.test
    || str_ends_with($host_now, '.local')   // alternatif virtual host
    || str_ends_with($host_now, '.dev')     // alternatif virtual host
    || php_uname('n') === gethostname()     // fallback: jalan via CLI
);

if ($is_localhost) {
    // ── LOCALHOST (XAMPP / Laragon) ────────────────────
    $host     = "localhost";
    $user     = "root";
    $password = "";          // Laragon default: kosong. Kalau pakai password, isi di sini
    $database = "rumah_adat_samarinda";
} else {
    // ── INFINITYFREE HOSTING ───────────────────────────
    $host     = "sql107.infinityfree.com";
    $user     = "if0_41640933";
    $password = "nabildaffa2005";
    $database = "if0_41640933_rumahadatbudayasamarinda";
}

// ── KONEKSI ────────────────────────────────────────────
$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("<div style='font-family:sans-serif;padding:20px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;color:#856404;max-width:600px;margin:20px auto'>
        <strong>⚠️ Koneksi Database Gagal!</strong><br><br>
        <strong>Host yang dideteksi:</strong> " . htmlspecialchars($host_now) . "<br>
        <strong>Mode:</strong> " . ($is_localhost ? 'Localhost' : 'Hosting') . "<br>
        <strong>Host DB:</strong> $host<br>
        <strong>Database:</strong> $database<br>
        <strong>Error:</strong> " . mysqli_connect_error() . "<br><br>
        <small>Jika masih error di localhost, pastikan database <strong>rumah_adat_samarinda</strong> sudah dibuat di phpMyAdmin dan sudah di-import file SQL-nya.</small>
    </div>");
}

mysqli_set_charset($koneksi, "utf8");
?>