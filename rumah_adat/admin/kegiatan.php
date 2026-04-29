<?php
require_once 'check_auth.php';
require_once '../koneksi.php';
$msg = ''; $msg_type = '';

// ═══════════════════════════════════════════════════
//  HELPER: Fungsi Validasi Server-Side
// ═══════════════════════════════════════════════════
function only_letters_spaces($str) {
    // Hanya huruf (termasuk huruf Indonesia), spasi, titik, koma
    return preg_match('/^[\p{L}\s\.\,\-\']+$/u', $str);
}
function valid_phone($hp) {
    // Harus diawali 08, hanya digit, panjang 10-15
    return preg_match('/^08[0-9]{8,13}$/', $hp);
}
function no_forbidden_symbols($str) {
    // Larang: { } [ ] # @ $ % ^ & * ( ) < > / \ | ~ ` " = + ; ! ?
    return !preg_match('/[\{\}\[\]#@\$%\^&\*\(\)<>\/\\\|~`"=\+;!\?]/', $str);
}
function validate_kegiatan($p, &$errors) {
    // Nama Kegiatan
    $nm = trim($p['nama_kegiatan'] ?? '');
    if (empty($nm)) $errors[] = "Nama kegiatan wajib diisi.";
    elseif (strlen($nm) < 5) $errors[] = "Nama kegiatan minimal 5 karakter.";
    elseif (strlen($nm) > 200) $errors[] = "Nama kegiatan maksimal 200 karakter.";
    elseif (!no_forbidden_symbols($nm)) $errors[] = "Nama kegiatan mengandung simbol yang tidak diizinkan.";

    // Deskripsi
    $ds = trim($p['deskripsi'] ?? '');
    if (!empty($ds)) {
        if (strlen($ds) < 20) $errors[] = "Deskripsi minimal 20 karakter.";
        if (!no_forbidden_symbols($ds)) $errors[] = "Deskripsi mengandung simbol yang tidak diizinkan.";
    }

    // Tanggal
    if (empty($p['tanggal'])) $errors[] = "Tanggal wajib diisi.";

    // Jam
    if (empty($p['jam_mulai'])) $errors[] = "Jam mulai wajib diisi.";
    if (empty($p['jam_selesai'])) $errors[] = "Jam selesai wajib diisi.";
    if (!empty($p['jam_mulai']) && !empty($p['jam_selesai']) && $p['jam_selesai'] <= $p['jam_mulai'])
        $errors[] = "Jam selesai harus lebih dari jam mulai.";

    // Nama Penyelenggara - HANYA HURUF
    $np = trim($p['nama_penyelenggara'] ?? '');
    if (!empty($np)) {
        if (!only_letters_spaces($np)) $errors[] = "Nama penyelenggara hanya boleh berisi huruf, spasi, titik, atau koma — tidak boleh mengandung angka atau simbol.";
        elseif (strlen($np) < 3) $errors[] = "Nama penyelenggara minimal 3 karakter.";
        elseif (strlen($np) > 150) $errors[] = "Nama penyelenggara maksimal 150 karakter.";
    }

    // No HP - format 08xxx
    $hp = trim($p['no_hp'] ?? '');
    if (!empty($hp)) {
        if (!valid_phone($hp)) $errors[] = "No. HP tidak valid. Harus diawali '08' dan hanya berisi angka (10–15 digit). Contoh: 081234567890.";
    }

    // Jumlah Peserta - hanya angka positif
    $jp = $p['jumlah_peserta'] ?? '';
    if ($jp === '' || $jp === null) {
        $errors[] = "Jumlah peserta wajib diisi.";
    } elseif (!ctype_digit(strval($jp)) || (int)$jp < 0) {
        $errors[] = "Jumlah peserta harus berupa angka positif (tidak boleh negatif atau mengandung huruf/simbol).";
    } elseif ((int)$jp > 1000000) {
        $errors[] = "Jumlah peserta terlalu besar.";
    }
}

$kategori_list = ['Pernikahan Adat','Upacara Adat','Festival Budaya','Seminar','Pertunjukan Seni','Pameran','Rapat / Pertemuan','Lainnya'];
$lokasi_list   = ['Aula Utama','Pendopo','Ruang Serbaguna','Panggung Terbuka','Ruang Pertemuan'];

// ── CREATE ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['act_tambah'])) {
    $errors = [];
    validate_kegiatan($_POST, $errors);
    if ($errors) {
        $msg = implode('<br>• ', array_merge(['<strong>Perbaiki kesalahan berikut:</strong>'], $errors));
        $msg_type = 'error';
    } else {
        $nm = mysqli_real_escape_string($koneksi, trim($_POST['nama_kegiatan']));
        $ds = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
        $tg = $_POST['tanggal'];
        $jm = $_POST['jam_mulai'];
        $js = $_POST['jam_selesai'];
        $lk = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
        $kt = mysqli_real_escape_string($koneksi, $_POST['kategori']);
        $np = mysqli_real_escape_string($koneksi, trim($_POST['nama_penyelenggara']));
        $hp = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));
        $jp = (int)$_POST['jumlah_peserta'];
        $st = mysqli_real_escape_string($koneksi, $_POST['status']);
        $uid = $_SESSION['user_id'];
        mysqli_query($koneksi,"INSERT INTO kegiatan (user_id,nama_kegiatan,deskripsi,tanggal,jam_mulai,jam_selesai,lokasi,kategori,nama_penyelenggara,no_hp,jumlah_peserta,status,created_at) VALUES ('$uid','$nm','$ds','$tg','$jm','$js','$lk','$kt','$np','$hp','$jp','$st',NOW())");
        $msg = "Kegiatan berhasil ditambahkan!"; $msg_type = "success";
    }
}

// ── UPDATE ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['act_edit'])) {
    $errors = [];
    validate_kegiatan($_POST, $errors);
    if ($errors) {
        $msg = implode('<br>• ', array_merge(['<strong>Perbaiki kesalahan berikut:</strong>'], $errors));
        $msg_type = 'error';
    } else {
        $id = (int)$_POST['edit_id'];
        $nm = mysqli_real_escape_string($koneksi, trim($_POST['nama_kegiatan']));
        $ds = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
        $tg = $_POST['tanggal'];
        $jm = $_POST['jam_mulai'];
        $js = $_POST['jam_selesai'];
        $lk = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
        $kt = mysqli_real_escape_string($koneksi, $_POST['kategori']);
        $np = mysqli_real_escape_string($koneksi, trim($_POST['nama_penyelenggara']));
        $hp = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));
        $jp = (int)$_POST['jumlah_peserta'];
        $st = mysqli_real_escape_string($koneksi, $_POST['status']);
        mysqli_query($koneksi,"UPDATE kegiatan SET nama_kegiatan='$nm',deskripsi='$ds',tanggal='$tg',jam_mulai='$jm',jam_selesai='$js',lokasi='$lk',kategori='$kt',nama_penyelenggara='$np',no_hp='$hp',jumlah_peserta='$jp',status='$st',updated_at=NOW() WHERE id='$id'");
        $msg = "Kegiatan berhasil diperbarui!"; $msg_type = "success";
    }
}

// ── DELETE ──────────────────────────────────────────
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id=(int)$_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM kegiatan WHERE id='$id'");
    $msg = "Kegiatan berhasil dihapus."; $msg_type = "warning";
}

$filter = isset($_GET['status']) ? mysqli_real_escape_string($koneksi,$_GET['status']) : '';
$where  = $filter ? "WHERE status='$filter'" : '';
$data   = mysqli_query($koneksi,"SELECT k.*,u.nama_lengkap FROM kegiatan k LEFT JOIN users u ON k.user_id=u.id $where ORDER BY k.tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kegiatan – Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--primary:#8B2E00;--primary-light:#B84A1A;--secondary:#C9890A;--bg-dark:#1C0A00;--bg-med:#2D1500;--bg-light:#FDF8F0;--text-dark:#1C0A00;--text-med:#4A2800;--text-light:#7A5C3A;--text-white:#FDF8F0;--border:#E8D4B0;--shadow-sm:0 2px 10px rgba(139,46,0,.08);--shadow-md:0 8px 30px rgba(139,46,0,.15);--font-display:'Cinzel',serif;--font-ui:'Nunito',sans-serif;--sb-w:260px;--tb-h:64px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:var(--font-ui);background:#f4ede2;color:var(--text-dark);display:flex;min-height:100vh}
.sidebar{width:var(--sb-w);background:var(--bg-dark);position:fixed;top:0;left:0;height:100vh;display:flex;flex-direction:column;z-index:200;border-right:1px solid rgba(201,137,10,.15);overflow-y:auto}
.sidebar-logo{display:flex;align-items:center;gap:12px;padding:20px 22px;border-bottom:1px solid rgba(201,137,10,.15)}
.sb-title{font-family:var(--font-display);font-size:.9rem;font-weight:700;color:#fff}
.sb-sub{font-size:.68rem;color:#C9890A;font-style:italic;letter-spacing:1px}
.sb-nav{padding:16px 12px;display:flex;flex-direction:column;gap:3px}
.sb-group{font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.25);padding:12px 10px 4px}
.sb-link{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;font-size:.85rem;font-weight:600;color:rgba(255,255,255,.65);text-decoration:none;transition:all .25s}
.sb-link i{width:18px;text-align:center;font-size:.9rem}
.sb-link:hover{background:rgba(201,137,10,.12);color:#C9890A}
.sb-link.active{background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;box-shadow:0 4px 15px rgba(139,46,0,.3)}
.sb-link.active i{color:#fff}
.sb-logout{color:rgba(231,76,60,.7);margin-top:8px}
.sb-logout:hover{background:rgba(231,76,60,.12);color:#e74c3c}
.main-wrap{margin-left:var(--sb-w);flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{height:var(--tb-h);background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:100;box-shadow:var(--shadow-sm)}
.topbar-title{font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--text-dark)}
.topbar-user{display:flex;align-items:center;gap:10px;font-size:.85rem;color:var(--text-med)}
.topbar-user .avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-family:var(--font-display);font-size:.9rem}
.content{padding:28px;flex:1}
.table-card{background:#fff;border-radius:14px;border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:24px}
.tc-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.tc-header h3{font-family:var(--font-display);font-size:1rem;color:var(--text-dark)}
.tbl{width:100%;border-collapse:collapse}
.tbl th{padding:12px 18px;text-align:left;font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);background:#fdf7ee;border-bottom:1px solid var(--border)}
.tbl td{padding:14px 18px;font-size:.9rem;color:var(--text-med);border-bottom:1px solid #f0e8d8;vertical-align:middle}
.tbl tr:last-child td{border-bottom:none}
.tbl tr:hover td{background:#fdf7ee}
.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:700}
.badge-pending{background:rgba(243,156,18,.1);color:#b7770d;border:1px solid rgba(243,156,18,.3)}
.badge-confirmed{background:rgba(39,174,96,.1);color:#1e8449;border:1px solid rgba(39,174,96,.3)}
.badge-cancelled,.badge-dibatalkan{background:rgba(231,76,60,.1);color:#c0392b;border:1px solid rgba(231,76,60,.3)}
.btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-family:var(--font-ui);font-size:.82rem;font-weight:700;cursor:pointer;border:none;text-decoration:none;transition:all .2s}
.btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 15px rgba(139,46,0,.3)}
.btn-danger{background:rgba(231,76,60,.1);color:#c0392b;border:1px solid rgba(231,76,60,.3)}
.btn-danger:hover{background:#e74c3c;color:#fff}
.btn-warning{background:rgba(243,156,18,.1);color:#b7770d;border:1px solid rgba(243,156,18,.3)}
.btn-warning:hover{background:#f39c12;color:#fff}
.btn-sm{padding:6px 12px;font-size:.78rem}
.btn-outline{background:transparent;border:2px solid var(--border);color:var(--text-light)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}
.form-card{background:#fff;border-radius:14px;border:1px solid var(--border);box-shadow:var(--shadow-sm);padding:28px;margin-bottom:24px}
.form-card h3{font-family:var(--font-display);font-size:1rem;color:var(--text-dark);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.fg{margin-bottom:16px;position:relative}
.fg label{display:block;font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-med);margin-bottom:6px}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;background:#fdf7ee;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-ui);font-size:.9rem;color:var(--text-dark);outline:none;transition:all .3s}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--primary);background:#fff;box-shadow:0 0 0 3px rgba(139,46,0,.07)}
.fg input.is-invalid,.fg textarea.is-invalid{border-color:#e74c3c;background:rgba(231,76,60,.04)}
.fg input.is-valid,.fg textarea.is-valid{border-color:#27ae60}
.fg textarea{min-height:90px;resize:vertical}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.field-hint{font-size:.73rem;color:var(--text-light);margin-top:4px;display:flex;align-items:center;gap:5px}
.field-error{font-size:.73rem;color:#c0392b;margin-top:4px;display:none;align-items:center;gap:5px;font-weight:600}
.field-error.show{display:flex}
.char-counter{font-size:.7rem;color:var(--text-light);text-align:right;margin-top:2px}
.char-counter.warn{color:#b7770d}
.char-counter.danger{color:#c0392b;font-weight:700}
.alert{padding:14px 18px;border-radius:10px;font-size:.88rem;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;font-weight:600;line-height:1.6}
.alert-success{background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#1e8449}
.alert-error{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);color:#c0392b}
.alert-warning{background:rgba(243,156,18,.1);border:1px solid rgba(243,156,18,.3);color:#b7770d}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:all .3s}
.modal-overlay.show{opacity:1;visibility:visible}
.modal-box{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;box-shadow:0 30px 80px rgba(0,0,0,.3);transform:scale(.95);transition:all .3s}
.modal-overlay.show .modal-box{transform:scale(1)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-family:var(--font-display);font-size:1.05rem;color:var(--text-dark)}
.modal-close{background:none;border:none;font-size:1.1rem;color:var(--text-light);cursor:pointer;padding:4px;border-radius:6px}
.modal-close:hover{background:#f0e8d8;color:var(--text-dark)}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
.sidebar-logo-icon{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#8B2E00,#B84A1A);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;}
.sidebar-logo-text{display:flex;flex-direction:column;line-height:1.2;}
.sidebar-logo-main{font-family:'Cinzel',serif;font-size:.85rem;font-weight:700;color:white;letter-spacing:.5px;}
.sidebar-logo-sub{font-size:.65rem;color:#C9890A;font-style:italic;letter-spacing:1px;}
.sidebar-section{padding:14px 12px 4px;font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.3);}
.sidebar-menu{padding:0 8px;list-style:none;}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:.82rem;font-weight:600;color:rgba(255,255,255,.65);transition:all .25s;margin-bottom:2px;cursor:pointer;text-decoration:none;}
.sidebar-link i{width:18px;text-align:center;font-size:.9rem;color:rgba(201,137,10,.6);transition:color .25s;}
.sidebar-link:hover{background:rgba(201,137,10,.1);color:white;}
.sidebar-link:hover i{color:#C9890A;}
.sidebar-link.active{background:linear-gradient(135deg,rgba(139,46,0,.6),rgba(184,74,26,.4));color:white;}
.sidebar-link.active i{color:#C9890A;}
.sidebar-link.danger{color:rgba(231,76,60,.7);}
.sidebar-link.danger:hover{background:rgba(231,76,60,.12);color:#e74c3c;}
.sidebar-bottom{padding:12px 8px 16px;border-top:1px solid rgba(201,137,10,.1);}
@media(max-width:768px){.sidebar{transform:translateX(-100%)}.main-wrap{margin-left:0}.form-row{grid-template-columns:1fr}}
</style>
</head>
<body>
<?php include __DIR__.'/sidebar.php'; ?>
<div class="main-wrap">
    <div class="topbar">
        <div class="topbar-title"><i class="fas fa-calendar-alt" style="color:var(--secondary);margin-right:8px"></i>Kelola Kegiatan</div>
        <div class="topbar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
        </div>
    </div>
    <div class="content">
        <?php if($msg): ?>
        <div class="alert alert-<?= $msg_type ?>">
            <i class="fas fa-<?= $msg_type==='success'?'check-circle':($msg_type==='warning'?'exclamation-triangle':'times-circle') ?>"></i>
            <div><?= $msg ?></div>
        </div>
        <?php endif; ?>

        <!-- ═══ FORM TAMBAH ═══ -->
        <div class="form-card">
            <h3><i class="fas fa-plus-circle" style="color:var(--secondary);margin-right:8px"></i>Tambah Kegiatan Baru</h3>
            <form method="POST" id="formTambah" novalidate>
                <div class="form-row">
                    <div class="fg">
                        <label>Nama Kegiatan *</label>
                        <input type="text" name="nama_kegiatan" id="t_nm" required maxlength="200"
                               placeholder="Contoh: Festival Budaya 2025"
                               oninput="validateNamaKegiatan(this,'t_nm_err','t_nm_cnt')"
                               onblur="validateNamaKegiatan(this,'t_nm_err','t_nm_cnt')">
                        <div class="char-counter" id="t_nm_cnt">0 / 200</div>
                        <div class="field-error" id="t_nm_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                    </div>
                    <div class="fg">
                        <label>Kategori *</label>
                        <select name="kategori" required>
                            <?php foreach($kategori_list as $k): ?><option value="<?=$k?>"><?=$k?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="fg">
                    <label>Deskripsi Kegiatan <span style="color:var(--text-light);font-weight:400">(min. 20 karakter jika diisi)</span></label>
                    <textarea name="deskripsi" id="t_ds" maxlength="2000"
                              placeholder="Deskripsikan kegiatan secara singkat dan jelas..."
                              oninput="validateDeskripsi(this,'t_ds_err','t_ds_cnt')"
                              onblur="validateDeskripsi(this,'t_ds_err','t_ds_cnt')"></textarea>
                    <div class="char-counter" id="t_ds_cnt">0 / 2000</div>
                    <div class="field-error" id="t_ds_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                </div>
                <div class="form-row">
                    <div class="fg">
                        <label>Tanggal *</label>
                        <input type="date" name="tanggal" required>
                    </div>
                    <div class="fg">
                        <label>Lokasi *</label>
                        <select name="lokasi" required>
                            <?php foreach($lokasi_list as $l): ?><option value="<?=$l?>"><?=$l?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fg">
                        <label>Jam Mulai *</label>
                        <input type="time" name="jam_mulai" id="t_jm" required onchange="validateJam('t_jm','t_js','t_jam_err')">
                    </div>
                    <div class="fg">
                        <label>Jam Selesai *</label>
                        <input type="time" name="jam_selesai" id="t_js" required onchange="validateJam('t_jm','t_js','t_jam_err')">
                        <div class="field-error" id="t_jam_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fg">
                        <label>Nama Penyelenggara
                            <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(hanya huruf)</span>
                        </label>
                        <input type="text" name="nama_penyelenggara" id="t_np" maxlength="150"
                               placeholder="Contoh: Dinas Kebudayaan Kota Samarinda"
                               oninput="validateNamaPenyelenggara(this,'t_np_err')"
                               onblur="validateNamaPenyelenggara(this,'t_np_err')">
                        <div class="field-hint"><i class="fas fa-info-circle"></i> Hanya boleh huruf, spasi, titik, dan koma</div>
                        <div class="field-error" id="t_np_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                    </div>
                    <div class="fg">
                        <label>No. HP
                            <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(format 08xxx)</span>
                        </label>
                        <input type="tel" name="no_hp" id="t_hp" maxlength="15"
                               placeholder="Contoh: 081234567890"
                               oninput="validateHP(this,'t_hp_err')"
                               onblur="validateHP(this,'t_hp_err')">
                        <div class="field-hint"><i class="fas fa-info-circle"></i> Diawali '08', hanya angka, 10–15 digit</div>
                        <div class="field-error" id="t_hp_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fg">
                        <label>Jumlah Peserta
                            <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(angka positif)</span>
                        </label>
                        <input type="number" name="jumlah_peserta" id="t_jp" value="1" min="1" max="1000000"
                               oninput="validateJumlahPeserta(this,'t_jp_err')"
                               onblur="validateJumlahPeserta(this,'t_jp_err')">
                        <div class="field-error" id="t_jp_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                    </div>
                    <div class="fg">
                        <label>Status</label>
                        <select name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="act_tambah" class="btn btn-primary" onclick="return validateFormTambah()">
                    <i class="fas fa-save"></i> Simpan Kegiatan
                </button>
            </form>
        </div>

        <!-- ═══ FILTER ═══ -->
        <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
            <?php
            $tabs=[['','Semua'],['pending','Pending'],['confirmed','Confirmed'],['dibatalkan','Dibatalkan']];
            foreach($tabs as [$v,$l]):
                $active=$filter===$v;
            ?>
            <a href="?status=<?=$v?>" class="btn btn-sm <?=$active?'btn-primary':'btn-outline'?>"><?=$l?></a>
            <?php endforeach; ?>
        </div>

        <!-- ═══ TABLE ═══ -->
        <div class="table-card">
            <div class="tc-header">
                <h3>Daftar Kegiatan</h3>
                <span style="font-size:.82rem;color:var(--text-light)"><?= mysqli_num_rows($data) ?> data</span>
            </div>
            <div style="overflow-x:auto">
            <table class="tbl">
                <thead><tr><th>#</th><th>Nama Kegiatan</th><th>Tanggal</th><th>Lokasi</th><th>Penyelenggara</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php $no=1; while($r=mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?=$no++?></td>
                    <td><strong><?=htmlspecialchars($r['nama_kegiatan'])?></strong><br><small style="color:var(--text-light)"><?=htmlspecialchars($r['kategori'])?></small></td>
                    <td><?=date('d M Y',strtotime($r['tanggal']))?><br><small><?=$r['jam_mulai']?> – <?=$r['jam_selesai']?></small></td>
                    <td><?=htmlspecialchars($r['lokasi'])?></td>
                    <td><?=htmlspecialchars($r['nama_penyelenggara']??$r['nama_lengkap']??'-')?></td>
                    <td><span class="badge badge-<?=$r['status']?>"><?=ucfirst($r['status'])?></span></td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button class="btn btn-warning btn-sm" onclick="openEditKegiatan(<?=htmlspecialchars(json_encode($r))?>)"><i class="fas fa-edit"></i></button>
                            <a href="?hapus=<?=$r['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kegiatan ini?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL EDIT ═══ -->
<div class="modal-overlay" id="editModal">
<div class="modal-box">
    <div class="modal-header">
        <h3><i class="fas fa-edit" style="color:var(--secondary);margin-right:8px"></i>Edit Kegiatan</h3>
        <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="formEdit" novalidate>
        <input type="hidden" name="edit_id" id="eId">
        <div class="form-row">
            <div class="fg">
                <label>Nama Kegiatan *</label>
                <input type="text" name="nama_kegiatan" id="eNm" required maxlength="200"
                       oninput="validateNamaKegiatan(this,'e_nm_err','e_nm_cnt')"
                       onblur="validateNamaKegiatan(this,'e_nm_err','e_nm_cnt')">
                <div class="char-counter" id="e_nm_cnt">0 / 200</div>
                <div class="field-error" id="e_nm_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
            </div>
            <div class="fg">
                <label>Kategori</label>
                <select name="kategori" id="eKt">
                    <?php foreach($kategori_list as $k): ?><option value="<?=$k?>"><?=$k?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="fg">
            <label>Deskripsi <span style="color:var(--text-light);font-weight:400">(min. 20 karakter jika diisi)</span></label>
            <textarea name="deskripsi" id="eDs" maxlength="2000"
                      oninput="validateDeskripsi(this,'e_ds_err','e_ds_cnt')"
                      onblur="validateDeskripsi(this,'e_ds_err','e_ds_cnt')"></textarea>
            <div class="char-counter" id="e_ds_cnt">0 / 2000</div>
            <div class="field-error" id="e_ds_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
        </div>
        <div class="form-row">
            <div class="fg"><label>Tanggal *</label><input type="date" name="tanggal" id="eTg" required></div>
            <div class="fg">
                <label>Lokasi</label>
                <select name="lokasi" id="eLk">
                    <?php foreach($lokasi_list as $l): ?><option value="<?=$l?>"><?=$l?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="fg">
                <label>Jam Mulai *</label>
                <input type="time" name="jam_mulai" id="eJm" required onchange="validateJam('eJm','eJs','e_jam_err')">
            </div>
            <div class="fg">
                <label>Jam Selesai *</label>
                <input type="time" name="jam_selesai" id="eJs" required onchange="validateJam('eJm','eJs','e_jam_err')">
                <div class="field-error" id="e_jam_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
            </div>
        </div>
        <div class="form-row">
            <div class="fg">
                <label>Nama Penyelenggara <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(hanya huruf)</span></label>
                <input type="text" name="nama_penyelenggara" id="eNp" maxlength="150"
                       oninput="validateNamaPenyelenggara(this,'e_np_err')"
                       onblur="validateNamaPenyelenggara(this,'e_np_err')">
                <div class="field-error" id="e_np_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
            </div>
            <div class="fg">
                <label>No. HP <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(format 08xxx)</span></label>
                <input type="tel" name="no_hp" id="eHp" maxlength="15"
                       oninput="validateHP(this,'e_hp_err')"
                       onblur="validateHP(this,'e_hp_err')">
                <div class="field-error" id="e_hp_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
            </div>
        </div>
        <div class="form-row">
            <div class="fg">
                <label>Jumlah Peserta <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(angka positif)</span></label>
                <input type="number" name="jumlah_peserta" id="eJp" min="1" max="1000000"
                       oninput="validateJumlahPeserta(this,'e_jp_err')"
                       onblur="validateJumlahPeserta(this,'e_jp_err')">
                <div class="field-error" id="e_jp_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
            </div>
            <div class="fg">
                <label>Status</label>
                <select name="status" id="eSt">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Batal</button>
            <button type="submit" name="act_edit" class="btn btn-primary" onclick="return validateFormEdit()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
</div>

<script>
// ══════════════════════════════════════════════════
//  KONSTANTA VALIDASI
// ══════════════════════════════════════════════════
const FORBIDDEN_SYMBOLS = /[\{\}\[\]#@\$%\^&\*\(\)<>\/\\|~`"=\+;!\?]/;
// Boleh: huruf (termasuk huruf berdiakritik), spasi, titik, koma, strip, apostrof
const LETTERS_ONLY      = /^[\p{L}\s\.\,\-']+$/u;

function showErr(errId, msg) {
    const el = document.getElementById(errId);
    if (!el) return;
    el.querySelector('span').textContent = msg;
    el.classList.add('show');
}
function clearErr(errId) {
    const el = document.getElementById(errId);
    if (!el) return;
    el.classList.remove('show');
}
function setInvalid(input) { input.classList.add('is-invalid'); input.classList.remove('is-valid'); }
function setValid(input)   { input.classList.add('is-valid');   input.classList.remove('is-invalid'); }
function clearState(input) { input.classList.remove('is-valid','is-invalid'); }

// ── Counter karakter ──
function updateCounter(input, counterId, max) {
    const el = document.getElementById(counterId);
    if (!el) return;
    const len = input.value.length;
    el.textContent = len + ' / ' + max;
    el.className = 'char-counter' + (len > max*0.9 ? (len >= max ? ' danger' : ' warn') : '');
}

// ── Validasi Nama Kegiatan ──
function validateNamaKegiatan(input, errId, cntId) {
    updateCounter(input, cntId, 200);
    const v = input.value.trim();
    if (!v) { setInvalid(input); showErr(errId, 'Nama kegiatan wajib diisi.'); return false; }
    if (v.length < 5) { setInvalid(input); showErr(errId, 'Nama kegiatan minimal 5 karakter.'); return false; }
    if (FORBIDDEN_SYMBOLS.test(v)) { setInvalid(input); showErr(errId, 'Tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) < > dll.'); return false; }
    setValid(input); clearErr(errId); return true;
}

// ── Validasi Deskripsi ──
function validateDeskripsi(input, errId, cntId) {
    updateCounter(input, cntId, 2000);
    const v = input.value.trim();
    if (v.length === 0) { clearState(input); clearErr(errId); return true; } // boleh kosong
    if (v.length < 20) { setInvalid(input); showErr(errId, 'Deskripsi minimal 20 karakter jika diisi (sekarang ' + v.length + ' karakter).'); return false; }
    if (FORBIDDEN_SYMBOLS.test(v)) { setInvalid(input); showErr(errId, 'Deskripsi tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) < > dll.'); return false; }
    setValid(input); clearErr(errId); return true;
}

// ── Validasi Nama Penyelenggara ──
function validateNamaPenyelenggara(input, errId) {
    const v = input.value.trim();
    if (v.length === 0) { clearState(input); clearErr(errId); return true; } // opsional
    if (/\d/.test(v)) { setInvalid(input); showErr(errId, 'Nama penyelenggara tidak boleh mengandung angka.'); return false; }
    if (FORBIDDEN_SYMBOLS.test(v)) { setInvalid(input); showErr(errId, 'Tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) < > dll.'); return false; }
    if (!LETTERS_ONLY.test(v)) { setInvalid(input); showErr(errId, 'Hanya boleh berisi huruf, spasi, titik, dan koma.'); return false; }
    if (v.length < 3) { setInvalid(input); showErr(errId, 'Nama penyelenggara minimal 3 karakter.'); return false; }
    setValid(input); clearErr(errId); return true;
}

// ── Validasi No HP ──
function validateHP(input, errId) {
    // Hanya izinkan pengetikan angka
    input.value = input.value.replace(/[^0-9]/g, '');
    const v = input.value.trim();
    if (v.length === 0) { clearState(input); clearErr(errId); return true; } // opsional
    if (!v.startsWith('08')) { setInvalid(input); showErr(errId, "No. HP harus diawali '08'. Contoh: 081234567890"); return false; }
    if (v.length < 10 || v.length > 15) { setInvalid(input); showErr(errId, 'No. HP harus terdiri dari 10–15 digit angka.'); return false; }
    setValid(input); clearErr(errId); return true;
}

// ── Validasi Jumlah Peserta ──
function validateJumlahPeserta(input, errId) {
    // Blokir pengetikan tanda minus dan karakter non-digit
    input.value = input.value.replace(/[^0-9]/g, '');
    const v = parseInt(input.value);
    if (isNaN(v) || input.value === '') { setInvalid(input); showErr(errId, 'Jumlah peserta wajib diisi dengan angka.'); return false; }
    if (v < 1) { setInvalid(input); showErr(errId, 'Jumlah peserta minimal 1 orang.'); return false; }
    if (v > 1000000) { setInvalid(input); showErr(errId, 'Jumlah peserta terlalu besar.'); return false; }
    setValid(input); clearErr(errId); return true;
}

// ── Validasi Jam ──
function validateJam(mulaiId, selesaiId, errId) {
    const jm = document.getElementById(mulaiId)?.value;
    const js = document.getElementById(selesaiId)?.value;
    if (jm && js && js <= jm) {
        showErr(errId, 'Jam selesai harus lebih dari jam mulai.');
        return false;
    }
    clearErr(errId); return true;
}

// ── Validasi form tambah sebelum submit ──
function validateFormTambah() {
    let ok = true;
    ok = validateNamaKegiatan(document.getElementById('t_nm'), 't_nm_err', 't_nm_cnt') && ok;
    ok = validateDeskripsi(document.getElementById('t_ds'), 't_ds_err', 't_ds_cnt') && ok;
    ok = validateNamaPenyelenggara(document.getElementById('t_np'), 't_np_err') && ok;
    ok = validateHP(document.getElementById('t_hp'), 't_hp_err') && ok;
    ok = validateJumlahPeserta(document.getElementById('t_jp'), 't_jp_err') && ok;
    ok = validateJam('t_jm', 't_js', 't_jam_err') && ok;
    if (!ok) { document.querySelector('.form-card').scrollIntoView({behavior:'smooth'}); }
    return ok;
}

// ── Validasi form edit sebelum submit ──
function validateFormEdit() {
    let ok = true;
    ok = validateNamaKegiatan(document.getElementById('eNm'), 'e_nm_err', 'e_nm_cnt') && ok;
    ok = validateDeskripsi(document.getElementById('eDs'), 'e_ds_err', 'e_ds_cnt') && ok;
    ok = validateNamaPenyelenggara(document.getElementById('eNp'), 'e_np_err') && ok;
    ok = validateHP(document.getElementById('eHp'), 'e_hp_err') && ok;
    ok = validateJumlahPeserta(document.getElementById('eJp'), 'e_jp_err') && ok;
    ok = validateJam('eJm', 'eJs', 'e_jam_err') && ok;
    return ok;
}

// ── Buka modal edit ──
function openEditKegiatan(r) {
    document.getElementById('eId').value = r.id;
    document.getElementById('eNm').value = r.nama_kegiatan;
    document.getElementById('eDs').value = r.deskripsi || '';
    document.getElementById('eTg').value = r.tanggal;
    document.getElementById('eJm').value = r.jam_mulai;
    document.getElementById('eJs').value = r.jam_selesai;
    document.getElementById('eNp').value = r.nama_penyelenggara || '';
    document.getElementById('eHp').value = r.no_hp || '';
    document.getElementById('eJp').value = r.jumlah_peserta || 1;
    document.getElementById('eKt').value = r.kategori;
    document.getElementById('eLk').value = r.lokasi;
    document.getElementById('eSt').value = r.status;
    // Reset state validasi
    ['eNm','eDs','eNp','eHp','eJp'].forEach(id => clearState(document.getElementById(id)));
    ['e_nm_err','e_ds_err','e_np_err','e_hp_err','e_jp_err','e_jam_err'].forEach(id => clearErr(id));
    // Update counter
    updateCounter(document.getElementById('eNm'), 'e_nm_cnt', 200);
    updateCounter(document.getElementById('eDs'), 'e_ds_cnt', 2000);
    document.getElementById('editModal').classList.add('show');
}
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal('editModal');
});
</script>
</body></html>
