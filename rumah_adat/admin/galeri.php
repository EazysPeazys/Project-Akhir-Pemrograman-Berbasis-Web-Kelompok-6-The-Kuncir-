<?php
require_once 'check_auth.php';
require_once '../koneksi.php';
$msg=''; $msg_type='';

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

// ─── HELPER VALIDASI GALERI ─────────────────────────────────
function galeri_no_symbols($s){ return !preg_match('/[\{\}\[\]#@\$%\^&\*\(\)<>\/\\|~`"=\+;!\?]/', $s); }

// ─── SERVER-SIDE: Validasi Tambah ────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['act_tambah'])) {
    $judul = trim(mysqli_real_escape_string($koneksi,$_POST['judul']));
    $desk  = trim(mysqli_real_escape_string($koneksi,$_POST['deskripsi']));
    $kat   = mysqli_real_escape_string($koneksi,$_POST['kategori']);
    $urut  = max(0,(int)($_POST['urutan']??0));
    $nama_file = '';

    // Validasi judul
    if (empty($judul)) { $msg="Judul foto wajib diisi."; $msg_type="error"; }
    elseif (strlen($judul)<3) { $msg="Judul minimal 3 karakter."; $msg_type="error"; }
    elseif (strlen($judul)>200) { $msg="Judul maksimal 200 karakter."; $msg_type="error"; }
    elseif (!galeri_no_symbols($judul)) { $msg="Judul mengandung simbol yang tidak diizinkan ({ } # @ \$ % & * ( ) < > dll)."; $msg_type="error"; }

    // Validasi deskripsi jika diisi
    if ($msg==='' && !empty($desk)) {
        if (strlen($desk)<10) { $msg="Deskripsi minimal 10 karakter."; $msg_type="error"; }
        elseif (!galeri_no_symbols($desk)) { $msg="Deskripsi mengandung simbol yang tidak diizinkan."; $msg_type="error"; }
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error']===0) {
        $allowed = ['image/jpeg','image/png','image/webp','image/jpg'];
        $max_size = 20 * 1024 * 1024; // 20MB
        if ($_FILES['foto']['size'] > $max_size) {
            $msg = "Ukuran file terlalu besar! Maksimal 20MB."; $msg_type = "error";
        } elseif (in_array($_FILES['foto']['type'],$allowed)) {
            $ext = pathinfo($_FILES['foto']['name'],PATHINFO_EXTENSION);
            $nama_file = 'galeri_'.time().'_'.rand(1000,9999).'.'.$ext;
            $target = '../assets/gallery/'.$nama_file;
            if (!is_dir('../assets/gallery')) mkdir('../assets/gallery',0755,true);
            move_uploaded_file($_FILES['foto']['tmp_name'],$target);
        } else { $msg="Format file tidak didukung (jpg/png/webp)."; $msg_type="error"; }
    }
    // URL foto field dihapus - hanya upload

    if ($msg==='' && (!empty($judul))) {
        mysqli_query($koneksi,"INSERT INTO galeri (judul,deskripsi,kategori,nama_file,urutan,created_at) VALUES ('$judul','$desk','$kat','$nama_file','$urut',NOW())");
        $msg="Foto berhasil ditambahkan!"; $msg_type="success";
    }
}

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['act_edit'])) {
    $id   = (int)$_POST['edit_id'];
    $judul= trim(mysqli_real_escape_string($koneksi,$_POST['judul']));
    $desk = trim(mysqli_real_escape_string($koneksi,$_POST['deskripsi']));
    $kat  = mysqli_real_escape_string($koneksi,$_POST['kategori']);
    $urut = max(0,(int)($_POST['urutan']??0));
    $nama_file = mysqli_real_escape_string($koneksi,$_POST['nama_file_lama']);
    // Validasi server edit
    if (empty($judul)) { $msg="Judul foto wajib diisi."; $msg_type="error"; }
    elseif (!galeri_no_symbols($judul)) { $msg="Judul mengandung simbol yang tidak diizinkan."; $msg_type="error"; }
    elseif (!empty($desk) && strlen($desk)<10) { $msg="Deskripsi minimal 10 karakter."; $msg_type="error"; }
    elseif (!empty($desk) && !galeri_no_symbols($desk)) { $msg="Deskripsi mengandung simbol yang tidak diizinkan."; $msg_type="error"; }

    if (isset($_FILES['foto']) && $_FILES['foto']['error']===0) {
        $allowed=['image/jpeg','image/png','image/webp','image/jpg'];
        $max_size = 20 * 1024 * 1024; // 20MB
        if ($_FILES['foto']['size'] > $max_size) {
            $msg = "Ukuran file terlalu besar! Maksimal 20MB."; $msg_type = "error";
        } elseif (in_array($_FILES['foto']['type'],$allowed)) {
            $ext=pathinfo($_FILES['foto']['name'],PATHINFO_EXTENSION);
            $nama_file='galeri_'.time().'_'.rand(1000,9999).'.'.$ext;
            $target='../assets/gallery/'.$nama_file;
            if (!is_dir('../assets/gallery')) mkdir('../assets/gallery',0755,true);
            move_uploaded_file($_FILES['foto']['tmp_name'],$target);
        }
    }
    // URL foto field dihapus - hanya upload

    mysqli_query($koneksi,"UPDATE galeri SET judul='$judul',deskripsi='$desk',kategori='$kat',nama_file='$nama_file',urutan='$urut',updated_at=NOW() WHERE id='$id'");
    $msg="Foto berhasil diperbarui!"; $msg_type="success";
}

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id=(int)$_GET['hapus'];
    $r=mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT nama_file FROM galeri WHERE id='$id'"));
    if ($r && $r['nama_file'] && file_exists('../assets/gallery/'.$r['nama_file'])) {
        unlink('../assets/gallery/'.$r['nama_file']);
    }
    mysqli_query($koneksi,"DELETE FROM galeri WHERE id='$id'");
    $msg="Foto berhasil dihapus."; $msg_type="warning";
}

$filter = isset($_GET['kat']) ? mysqli_real_escape_string($koneksi,$_GET['kat']) : '';
$where  = $filter ? "WHERE kategori='$filter'" : '';
$data   = mysqli_query($koneksi,"SELECT * FROM galeri $where ORDER BY urutan ASC, id DESC");
$kat_list = ['bangunan'=>'Bangunan','kegiatan'=>'Kegiatan / Acara','lainnya'=>'Lainnya'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Galeri – Admin</title>
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

.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px}
.stat-card{background:#fff;border-radius:14px;padding:22px 20px;border:1px solid var(--border);box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:16px}
.stat-icon{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;flex-shrink:0}
.stat-num{font-family:var(--font-display);font-size:1.8rem;font-weight:700;color:var(--text-dark);line-height:1}
.stat-lbl{font-size:.75rem;color:var(--text-light);text-transform:uppercase;letter-spacing:1px;margin-top:4px}

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
.badge-cancelled,.badge-ditolak{background:rgba(231,76,60,.1);color:#c0392b;border:1px solid rgba(231,76,60,.3)}

.btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-family:var(--font-ui);font-size:.82rem;font-weight:700;cursor:pointer;border:none;text-decoration:none;transition:all .2s}
.btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 15px rgba(139,46,0,.3)}
.btn-success{background:rgba(39,174,96,.1);color:#1e8449;border:1px solid rgba(39,174,96,.3)}
.btn-success:hover{background:#27ae60;color:#fff}
.btn-danger{background:rgba(231,76,60,.1);color:#c0392b;border:1px solid rgba(231,76,60,.3)}
.btn-danger:hover{background:#e74c3c;color:#fff}
.btn-warning{background:rgba(243,156,18,.1);color:#b7770d;border:1px solid rgba(243,156,18,.3)}
.btn-warning:hover{background:#f39c12;color:#fff}
.btn-sm{padding:6px 12px;font-size:.78rem}
.btn-outline{background:transparent;border:2px solid var(--border);color:var(--text-light)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}

.form-card{background:#fff;border-radius:14px;border:1px solid var(--border);box-shadow:var(--shadow-sm);padding:28px;margin-bottom:24px}
.form-card h3{font-family:var(--font-display);font-size:1rem;color:var(--text-dark);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-med);margin-bottom:6px}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;background:#fdf7ee;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-ui);font-size:.9rem;color:var(--text-dark);outline:none;transition:all .3s}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--primary);background:#fff;box-shadow:0 0 0 3px rgba(139,46,0,.07)}
.fg textarea{min-height:90px;resize:vertical}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}

.alert{padding:12px 16px;border-radius:10px;font-size:.88rem;margin-bottom:18px;display:flex;align-items:center;gap:10px;font-weight:600}
.alert-success{background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#1e8449}
.alert-error{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);color:#c0392b}
.alert-warning{background:rgba(243,156,18,.1);border:1px solid rgba(243,156,18,.3);color:#b7770d}

.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:all .3s}
.modal-overlay.show{opacity:1;visibility:visible}
.modal-box{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:500px;box-shadow:0 30px 80px rgba(0,0,0,.3);transform:scale(.95);transition:all .3s}
.modal-overlay.show .modal-box{transform:scale(1)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-family:var(--font-display);font-size:1.05rem;color:var(--text-dark)}
.modal-close{background:none;border:none;font-size:1.1rem;color:var(--text-light);cursor:pointer;padding:4px;border-radius:6px}
.modal-close:hover{background:#f0e8d8;color:var(--text-dark)}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}

.gallery-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px}
.gallery-admin-item{background:#fff;border-radius:12px;border:1px solid var(--border);overflow:hidden;box-shadow:var(--shadow-sm);transition:all .3s}
.gallery-admin-item:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.gallery-admin-img{width:100%;height:160px;object-fit:cover;display:block}
.gallery-admin-body{padding:12px}
.gallery-admin-cat{font-size:.68rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--secondary);margin-bottom:4px}
.gallery-admin-title{font-family:var(--font-display);font-size:.85rem;color:var(--text-dark);margin-bottom:8px;line-height:1.3}
.gallery-admin-actions{display:flex;gap:6px}

@media(max-width:1024px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.sidebar{transform:translateX(-100%)}.main-wrap{margin-left:0}.form-row{grid-template-columns:1fr}}

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
.sidebar-spacer{flex:1;}
.sidebar-bottom{padding:12px 8px 16px;border-top:1px solid rgba(201,137,10,.1);}
.sb-w{--sb-w:260px;}
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-wrap">
    <div class="topbar">
        <div class="topbar-title"><i class="fas fa-images" style="color:var(--secondary);margin-right:8px"></i>Kelola Galeri Foto</div>
        <div class="topbar-user"><div class="avatar"><?=strtoupper(substr($_SESSION['username'],0,1))?></div><span><?=htmlspecialchars($_SESSION['username'])?></span></div>
    </div>
    <div class="content">
        <?php if($msg): ?><div class="alert alert-<?=$msg_type?>"><i class="fas fa-check-circle"></i> <?=$msg?></div><?php endif; ?>

        <div class="form-card">
            <h3><i class="fas fa-plus-circle" style="color:var(--secondary);margin-right:8px"></i>Tambah Foto Baru</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="fg">
                    <label>Judul Foto *</label>
                    <input type="text" name="judul" id="g_judul" required maxlength="200"
                           placeholder="Contoh: Rumah Adat Kutai"
                           oninput="validateGaleriJudul(this,'g_judul_err','g_judul_cnt')"
                           onblur="validateGaleriJudul(this,'g_judul_err','g_judul_cnt')">
                    <div style="font-size:.7rem;color:var(--text-light);text-align:right;margin-top:2px" id="g_judul_cnt">0 / 200</div>
                    <div style="font-size:.73rem;color:#c0392b;margin-top:4px;display:none;align-items:center;gap:5px;font-weight:600" id="g_judul_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                </div>
                    <div class="fg"><label>Kategori *</label>
                        <select name="kategori">
                            <?php foreach($kat_list as $v=>$l): ?><option value="<?=$v?>"><?=$l?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="fg">
                    <label>Deskripsi <span style="color:var(--text-light);font-weight:400;font-size:.68rem">(min. 10 karakter jika diisi)</span></label>
                    <textarea name="deskripsi" id="g_desk" maxlength="1000" placeholder="Deskripsi foto..."
                              oninput="validateGaleriDesk(this,'g_desk_err','g_desk_cnt')"
                              onblur="validateGaleriDesk(this,'g_desk_err','g_desk_cnt')"></textarea>
                    <div style="font-size:.7rem;color:var(--text-light);text-align:right;margin-top:2px" id="g_desk_cnt">0 / 1000</div>
                    <div style="font-size:.73rem;color:#c0392b;margin-top:4px;display:none;align-items:center;gap:5px;font-weight:600" id="g_desk_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
                </div>
                <div class="fg">
                    <label>Upload Foto (jpg/png/webp) — Maks. 20MB</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" onchange="previewImg(this,'prevNew')" style="border:1.5px dashed var(--border);padding:14px;border-radius:8px;background:#fdf7ee;width:100%;cursor:pointer;">
                    <img id="prevNew" src="" alt="" style="display:none;margin-top:10px;max-height:160px;border-radius:10px;object-fit:cover;box-shadow:0 2px 10px rgba(0,0,0,.1)">
                    <small style="color:var(--text-light);font-size:.75rem;margin-top:6px;display:block"><i class="fas fa-info-circle"></i> Format: JPG, PNG, WEBP. Ukuran maksimal 20MB.</small>
                </div>
                <div class="fg" style="max-width:200px"><label>Urutan Tampil</label><input type="number" name="urutan" value="0" min="0" oninput="this.value=this.value.replace(/[^0-9]/g,'');if(parseInt(this.value)<0)this.value=0;"></div>
                <button type="submit" name="act_tambah" class="btn btn-primary" onclick="return validateGaleriForm()"><i class="fas fa-save"></i> Simpan Foto</button>
            </form>
        </div>

        <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
            <a href="galeri.php" class="btn btn-sm <?=$filter===''?'btn-primary':'btn-outline'?>">Semua</a>
            <?php foreach($kat_list as $v=>$l): ?>
            <a href="?kat=<?=$v?>" class="btn btn-sm <?=$filter===$v?'btn-primary':'btn-outline'?>"><?=$l?></a>
            <?php endforeach; ?>
        </div>

        <div class="gallery-admin-grid">
        <?php while($r=mysqli_fetch_assoc($data)):
            $src = $r['nama_file'];
            if (!str_starts_with($src,'http') && !str_starts_with($src,'assets')) {
                $src = '../assets/gallery/'.$r['nama_file'];
            } elseif (!str_starts_with($src,'http')) {
                $src = '../'.$src;
            }
        ?>
        <div class="gallery-admin-item">
            <img src="<?=htmlspecialchars($src)?>" alt="<?=htmlspecialchars($r['judul'])?>"
                    class="gallery-admin-img" onerror="this.src='https://picsum.photos/400/300?grayscale&random=<?=$r['id']?>'">
            <div class="gallery-admin-body">
                <div class="gallery-admin-cat"><?=$kat_list[$r['kategori']]??$r['kategori']?></div>
                <div class="gallery-admin-title"><?=htmlspecialchars($r['judul'])?></div>
                <div class="gallery-admin-actions">
                    <button class="btn btn-warning btn-sm" onclick="openEditGaleri(<?=htmlspecialchars(json_encode($r))?>)"><i class="fas fa-edit"></i> Edit</button>
                    <a href="?hapus=<?=$r['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus foto ini?')"><i class="fas fa-trash"></i></a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php if(mysqli_num_rows($data)===0): ?>
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--text-light)">
            <i class="fas fa-images" style="font-size:3rem;opacity:.3;display:block;margin-bottom:12px"></i>
            <p>Belum ada foto. Tambahkan foto pertama!</p>
        </div>
        <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editModal">
<div class="modal-box" style="max-width:600px">
    <div class="modal-header">
        <h3><i class="fas fa-edit" style="color:var(--secondary);margin-right:8px"></i>Edit Foto</h3>
        <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" id="eId">
        <input type="hidden" name="nama_file_lama" id="eFileLama">
        <div class="form-row">
            <div class="fg">
        <label>Judul Foto *</label>
        <input type="text" name="judul" id="eJudul" required maxlength="200"
               oninput="if(GALERI_FORBIDDEN.test(this.value.trim()))galeriSetInvalid(this);else galeriSetValid(this)">
        <div style="font-size:.73rem;color:#777;margin-top:3px">Tidak boleh menggunakan simbol seperti { } # @ $ % dll.</div>
    </div>
            <div class="fg"><label>Kategori</label>
                <select name="kategori" id="eKat">
                    <?php foreach($kat_list as $v=>$l): ?><option value="<?=$v?>"><?=$l?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="fg">
        <label>Deskripsi <span style="color:#7A5C3A;font-weight:400;font-size:.68rem">(min. 10 karakter jika diisi)</span></label>
        <textarea name="deskripsi" id="eDesk" maxlength="1000"
                  oninput="validateGaleriDesk(this,'','')"></textarea>
    </div>
        <div class="fg">
            <label>Ganti Foto (opsional)</label>
            <input type="file" name="foto" accept="image/*" onchange="previewImg(this,'prevEdit')">
            <img id="prevEdit" src="" alt="" style="display:none;margin-top:8px;max-height:120px;border-radius:8px;object-fit:cover">
        </div>
        <!-- URL field dihapus, hanya upload foto -->
        <div class="fg" style="max-width:200px"><label>Urutan</label><input type="number" name="urutan" id="eUrut" min="0"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Batal</button>
            <button type="submit" name="act_edit" class="btn btn-primary" onclick="return validateEditGaleri()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
</div>

<script>
function previewImg(input,previewId){
    var prev=document.getElementById(previewId);
    if(input.files&&input.files[0]){
        var reader=new FileReader();
        reader.onload=function(e){prev.src=e.target.result;prev.style.display='block';};
        reader.readAsDataURL(input.files[0]);
    }
}
function openEditGaleri(r){
    document.getElementById('eId').value=r.id;
    document.getElementById('eJudul').value=r.judul;
    document.getElementById('eDesk').value=r.deskripsi||'';
    document.getElementById('eKat').value=r.kategori;
    document.getElementById('eUrut').value=r.urutan||0;
    document.getElementById('eFileLama').value=r.nama_file||'';
    document.getElementById('prevEdit').style.display='none';
    document.getElementById('editModal').classList.add('show');
}
function closeModal(id){document.getElementById(id).classList.remove('show');}
document.getElementById('editModal').addEventListener('click',function(e){if(e.target===this)closeModal('editModal');});
</script>
<script>
const GALERI_FORBIDDEN = /[\{\}\[\]#@\$%\^&\*\(\)<>\/\\|~`"=\+;!\?]/;
function galeriSetInvalid(el){el.style.borderColor='#e74c3c';el.style.background='rgba(231,76,60,.04)';}
function galeriSetValid(el){el.style.borderColor='#27ae60';el.style.background='';}
function galeriClearState(el){el.style.borderColor='';el.style.background='';}
function galeriShowErr(id,msg){const el=document.getElementById(id);if(!el)return;el.querySelector('span').textContent=msg;el.style.display='flex';}
function galeriClearErr(id){const el=document.getElementById(id);if(!el)return;el.style.display='none';}
function galeriUpdateCnt(inp,cid,max){const el=document.getElementById(cid);if(!el)return;const l=inp.value.length;el.textContent=l+' / '+max;el.style.color=l>max*.9?(l>=max?'#c0392b':'#b7770d'):'var(--text-light)';}

function validateGaleriJudul(inp,eid,cid){
    galeriUpdateCnt(inp,cid,200);
    const v=inp.value.trim();
    if(!v){galeriSetInvalid(inp);galeriShowErr(eid,'Judul foto wajib diisi.');return false;}
    if(v.length<3){galeriSetInvalid(inp);galeriShowErr(eid,'Judul minimal 3 karakter.');return false;}
    if(GALERI_FORBIDDEN.test(v)){galeriSetInvalid(inp);galeriShowErr(eid,'Tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) < > dll.');return false;}
    galeriSetValid(inp);galeriClearErr(eid);return true;
}
function validateGaleriDesk(inp,eid,cid){
    galeriUpdateCnt(inp,cid,1000);
    const v=inp.value.trim();
    if(!v){galeriClearState(inp);galeriClearErr(eid);return true;}
    if(v.length<10){galeriSetInvalid(inp);galeriShowErr(eid,'Deskripsi minimal 10 karakter jika diisi (sekarang '+v.length+' karakter).');return false;}
    if(GALERI_FORBIDDEN.test(v)){galeriSetInvalid(inp);galeriShowErr(eid,'Tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) < > dll.');return false;}
    galeriSetValid(inp);galeriClearErr(eid);return true;
}
function validateGaleriForm(){
    let ok=true;
    const jEl=document.getElementById('g_judul');
    if(jEl) ok=validateGaleriJudul(jEl,'g_judul_err','g_judul_cnt')&&ok;
    const dEl=document.getElementById('g_desk');
    if(dEl) ok=validateGaleriDesk(dEl,'g_desk_err','g_desk_cnt')&&ok;
    return ok;
}
// Edit modal judul/desk validasi saat submit
function validateEditGaleri(){
    const jEl=document.getElementById('eJudul');
    const dEl=document.getElementById('eDesk');
    let ok=true;
    if(jEl){
        const v=jEl.value.trim();
        if(!v||v.length<3||GALERI_FORBIDDEN.test(v)){galeriSetInvalid(jEl);ok=false;}
        else galeriSetValid(jEl);
    }
    if(dEl){
        const v=dEl.value.trim();
        if(v&&(v.length<10||GALERI_FORBIDDEN.test(v))){galeriSetInvalid(dEl);ok=false;}
        else galeriClearState(dEl);
    }
    return ok;
}
</script>
</body></html>