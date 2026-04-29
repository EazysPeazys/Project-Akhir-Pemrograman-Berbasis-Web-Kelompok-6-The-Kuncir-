<?php
require_once 'check_auth.php';
require_once '../koneksi.php';
$msg=''; $msg_type='';

$check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM ulasan LIKE 'status'");
if (mysqli_num_rows($check_col) === 0) {
    mysqli_query($koneksi, "ALTER TABLE ulasan ADD COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending' AFTER komentar");
    mysqli_query($koneksi, "ALTER TABLE ulasan ADD COLUMN alasan_tolak TEXT NULL AFTER status");
    mysqli_query($koneksi, "UPDATE ulasan SET status='approved'");
}
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($koneksi,"UPDATE ulasan SET status='approved', updated_at=NOW() WHERE id='$id'");
    $msg="Ulasan berhasil disetujui dan ditampilkan di website."; $msg_type="success";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['act_reject'])) {
    $id     = (int)$_POST['reject_id'];
    $alasan = mysqli_real_escape_string($koneksi, $_POST['alasan_tolak'] ?? '');
    mysqli_query($koneksi,"UPDATE ulasan SET status='rejected', alasan_tolak='$alasan', updated_at=NOW() WHERE id='$id'");
    $msg="Ulasan berhasil ditolak."; $msg_type="warning";
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['act_edit'])) {
    $id   = (int)$_POST['edit_id'];
    $nama = trim(mysqli_real_escape_string($koneksi,$_POST['nama']));
    $rat  = (int)$_POST['rating'];
    $kom  = trim(mysqli_real_escape_string($koneksi,$_POST['komentar']));
    $st   = mysqli_real_escape_string($koneksi,$_POST['status'] ?? 'pending');
    $ulasan_edit_errors = [];
    if (empty($nama)) $ulasan_edit_errors[] = "Nama pengulas wajib diisi.";
    elseif (strlen($nama)<3) $ulasan_edit_errors[] = "Nama minimal 3 karakter.";
    elseif (preg_match('/[\{\}\[\]#@\$%\^&\*\(\)<>\/\\|~`"=\+;!\?]/', $nama)) $ulasan_edit_errors[] = "Nama mengandung simbol yang tidak diizinkan.";
    if ($rat<1||$rat>5) $ulasan_edit_errors[] = "Rating harus antara 1–5.";
    if (empty($kom)) $ulasan_edit_errors[] = "Komentar wajib diisi.";
    elseif (strlen($kom)<10) $ulasan_edit_errors[] = "Komentar minimal 10 karakter.";
    elseif (preg_match('/[\{\}\[\]#@\$%\^&\*\(\)<>\/\\|~`"=\+;!\?]/', $kom)) $ulasan_edit_errors[] = "Komentar mengandung simbol yang tidak diizinkan.";
    if ($ulasan_edit_errors) {
        $msg = "<strong>Perbaiki:</strong> ".implode("; ",$ulasan_edit_errors); $msg_type="error";
    } else {
        mysqli_query($koneksi,"UPDATE ulasan SET nama_tamu='$nama',rating='$rat',komentar='$kom',status='$st',updated_at=NOW() WHERE id='$id'");
        $msg="Ulasan berhasil diperbarui!"; $msg_type="success";
    }
}
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    mysqli_query($koneksi,"DELETE FROM ulasan WHERE id='".(int)$_GET['hapus']."'");
    $msg="Ulasan berhasil dihapus."; $msg_type="warning";
}
if (isset($_GET['hapus_semua']) && $_GET['hapus_semua']==='1') {
    mysqli_query($koneksi,"DELETE FROM ulasan");
    $msg="Semua ulasan berhasil dihapus."; $msg_type="warning";
}

$filter_rat    = isset($_GET['rating']) && is_numeric($_GET['rating']) ? (int)$_GET['rating'] : 0;
$filter_status = isset($_GET['status_filter']) ? mysqli_real_escape_string($koneksi,$_GET['status_filter']) : '';
$where_parts   = [];
if ($filter_rat)    $where_parts[] = "u.rating='$filter_rat'";
if ($filter_status) $where_parts[] = "u.status='$filter_status'";
$where = count($where_parts) ? "WHERE ".implode(" AND ",$where_parts) : '';
$data  = mysqli_query($koneksi,"SELECT u.*,COALESCE(us.nama_lengkap,u.nama_tamu,'Pengunjung') as display_name FROM ulasan u LEFT JOIN users us ON u.user_id=us.id AND u.user_id IS NOT NULL $where ORDER BY FIELD(u.status,'pending','approved','rejected'), u.created_at DESC");
$total = mysqli_num_rows($data);
$pending_count = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) c FROM ulasan WHERE status='pending'"))['c'];
$avg   = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT AVG(rating) avg FROM ulasan WHERE status='approved'"))['avg'];
$avg   = round($avg,1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ulasan – Admin</title>
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
        <div class="topbar-title"><i class="fas fa-star" style="color:var(--secondary);margin-right:8px"></i>Kelola Ulasan</div>
        <div class="topbar-user"><div class="avatar"><?=strtoupper(substr($_SESSION['username'],0,1))?></div><span><?=htmlspecialchars($_SESSION['username'])?></span></div>
    </div>
    <div class="content">
        <?php if($msg): ?><div class="alert alert-<?=$msg_type?>"><i class="fas fa-check-circle"></i> <?=$msg?></div><?php endif; ?>

        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#C9890A,#A06A05)"><i class="fas fa-comments"></i></div>
                <div><div class="stat-num"><?=$total?></div><div class="stat-lbl">Total Ulasan</div></div>
            </div>
            <div class="stat-card" style="border:2px solid <?=$pending_count>0?'rgba(243,156,18,.5)':'var(--border)'?>">
                <div class="stat-icon" style="background:linear-gradient(135deg,#e67e22,#f39c12)"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-num" style="color:<?=$pending_count>0?'#b7770d':'var(--text-dark)'?>"><?=$pending_count?></div>
                    <div class="stat-lbl">Menunggu Review</div>
                    <?php if ($pending_count > 0): ?>
                    <div style="font-size:.7rem;color:#b7770d;font-weight:700;margin-top:4px;animation:pulse 1.5s infinite">⚠ Perlu tindakan!</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f39c12,#f1c40f)"><i class="fas fa-star"></i></div>
                <div><div class="stat-num"><?=$avg?:'-'?></div><div class="stat-lbl">Rating Rata-rata</div></div>
            </div>
            <div class="stat-card" style="align-items:center;justify-content:center">
                <a href="?hapus_semua=1" class="btn btn-danger" onclick="return confirm('Hapus SEMUA ulasan? Tidak bisa dikembalikan!')">
                    <i class="fas fa-trash-alt"></i> Hapus Semua
                </a>
            </div>
        </div>
        <style>
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        .badge-pending-mod{background:rgba(243,156,18,.15);color:#b7770d;border:1px solid rgba(243,156,18,.4);padding:3px 10px;border-radius:12px;font-size:.7rem;font-weight:700;}
        .badge-approved-mod{background:rgba(39,174,96,.12);color:#1e8449;border:1px solid rgba(39,174,96,.3);padding:3px 10px;border-radius:12px;font-size:.7rem;font-weight:700;}
        .badge-rejected-mod{background:rgba(231,76,60,.12);color:#c0392b;border:1px solid rgba(231,76,60,.3);padding:3px 10px;border-radius:12px;font-size:.7rem;font-weight:700;}
        </style>

        <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
            <span style="font-size:.78rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:1px">Status:</span>
            <a href="ulasan.php" class="btn btn-sm <?=$filter_status===''?'btn-primary':'btn-outline'?>">Semua</a>
            <a href="?status_filter=pending" class="btn btn-sm <?=$filter_status==='pending'?'btn-primary':'btn-outline'?>" style="<?=$filter_status==='pending'?'':'color:#b7770d;border-color:rgba(243,156,18,.4)'?>">⏳ Pending <?php if($pending_count>0 && $filter_status!=='pending'): ?><span style="background:#e67e22;color:white;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;margin-left:4px"><?=$pending_count?></span><?php endif; ?></a>
            <a href="?status_filter=approved" class="btn btn-sm <?=$filter_status==='approved'?'btn-primary':'btn-outline'?>" style="<?=$filter_status==='approved'?'':'color:#1e8449;border-color:rgba(39,174,96,.3)'?>">✅ Disetujui</a>
            <a href="?status_filter=rejected" class="btn btn-sm <?=$filter_status==='rejected'?'btn-primary':'btn-outline'?>" style="<?=$filter_status==='rejected'?'':'color:#c0392b;border-color:rgba(231,76,60,.3)'?>">❌ Ditolak</a>
            <span style="font-size:.78rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:1px;margin-left:8px">Rating:</span>
            <a href="ulasan.php<?=$filter_status?'?status_filter='.$filter_status:''?>" class="btn btn-sm <?=$filter_rat===0?'btn-primary':'btn-outline'?>">Semua</a>
            <?php for($i=5;$i>=1;$i--): ?>
            <a href="?rating=<?=$i?><?=$filter_status?'&status_filter='.$filter_status:''?>" class="btn btn-sm <?=$filter_rat===$i?'btn-primary':'btn-outline'?>"><?=$i?> ⭐</a>
            <?php endfor; ?>
        </div>

        <div class="table-card">
            <div class="tc-header"><h3>Daftar Ulasan</h3><span style="font-size:.82rem;color:var(--text-light)"><?=$total?> ulasan</span></div>
            <div style="overflow-x:auto">
            <table class="tbl">
                <thead><tr><th>#</th><th>Nama</th><th>Status</th><th>Rating</th><th>Komentar</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php
                mysqli_data_seek($data,0);
                $no=1; while($r=mysqli_fetch_assoc($data)): ?>
                <tr style="<?=$r['status']==='pending'?'background:rgba(243,156,18,.04)':''?>">
                    <td><?=$no++?></td>
                    <td>
                        <strong><?=htmlspecialchars($r['display_name'])?></strong>
                        <?php if($r['status']==='pending'): ?><br><span style="font-size:.7rem;color:#b7770d;"><i class="fas fa-clock"></i> Baru masuk</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if($r['status']==='pending'): ?>
                        <span class="badge-pending-mod"><i class="fas fa-clock"></i> Pending</span>
                        <?php elseif($r['status']==='approved'): ?>
                        <span class="badge-approved-mod"><i class="fas fa-check"></i> Disetujui</span>
                        <?php else: ?>
                        <span class="badge-rejected-mod"><i class="fas fa-times"></i> Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td><?php for($i=1;$i<=5;$i++) echo $r['rating']>=$i?'<span style="color:#C9890A">★</span>':'<span style="color:#ddd">★</span>'; ?> <strong><?=$r['rating']?>/5</strong></td>
                    <td style="max-width:260px"><p style="overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical"><?=htmlspecialchars($r['komentar'])?></p></td>
                    <td style="white-space:nowrap"><?=date('d M Y',strtotime($r['created_at']))?></td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <?php if($r['status']==='pending' || $r['status']==='rejected'): ?>
                            <a href="?approve=<?=$r['id']?><?=$filter_status?'&status_filter='.$filter_status:''?>" class="btn btn-success btn-sm" title="Setujui" onclick="return confirm('Setujui ulasan ini? Akan ditampilkan di website.')"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <?php if($r['status']==='pending' || $r['status']==='approved'): ?>
                            <button class="btn btn-sm" style="background:rgba(231,76,60,.1);color:#c0392b;border:1px solid rgba(231,76,60,.3);" title="Tolak" onclick="openRejectModal(<?=$r['id']?>)"><i class="fas fa-ban"></i></button>
                            <?php endif; ?>
                            <button class="btn btn-warning btn-sm" title="Edit" onclick="openEditUlasan(<?=htmlspecialchars(json_encode($r))?>)"><i class="fas fa-edit"></i></button>
                            <a href="?hapus=<?=$r['id']?><?=$filter_status?'&status_filter='.$filter_status:''?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Hapus ulasan ini?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($total===0): ?>
                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-light)">Belum ada ulasan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editModal">
<div class="modal-box">
    <div class="modal-header">
        <h3><i class="fas fa-edit" style="color:var(--secondary);margin-right:8px"></i>Edit Ulasan</h3>
        <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
        <input type="hidden" name="edit_id" id="eId">
        <div class="fg">
            <label>Nama Pengulas *</label>
            <input type="text" name="nama" id="eNama" required maxlength="100"
                    oninput="validateUlasanNama(this,'eu_nama_err')"
                    onblur="validateUlasanNama(this,'eu_nama_err')">
            <div style="font-size:.73rem;color:#c0392b;margin-top:4px;display:none;align-items:center;gap:5px;font-weight:600" id="eu_nama_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
        </div>
        <div class="fg">
            <label>Rating</label>
            <div id="starWrap" style="display:flex;gap:8px;font-size:1.8rem;margin-bottom:4px">
                <?php for($i=1;$i<=5;$i++): ?>
                <span class="estar" data-v="<?=$i?>" onclick="setEStar(<?=$i?>)" style="cursor:pointer;color:#ddd;transition:all .2s">★</span>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating" id="eRat" value="0">
        </div>
        <div class="fg">
            <label>Komentar * <span style="color:#7A5C3A;font-weight:400;font-size:.68rem">(min. 10 karakter)</span></label>
            <textarea name="komentar" id="eKom" required maxlength="1000"
                        oninput="validateUlasanKom(this,'eu_kom_err','eu_kom_cnt')"
                        onblur="validateUlasanKom(this,'eu_kom_err','eu_kom_cnt')"></textarea>
            <div style="font-size:.7rem;color:#7A5C3A;text-align:right;margin-top:2px" id="eu_kom_cnt">0 / 1000</div>
            <div style="font-size:.73rem;color:#c0392b;margin-top:4px;display:none;align-items:center;gap:5px;font-weight:600" id="eu_kom_err"><i class="fas fa-exclamation-circle"></i><span></span></div>
        </div>
        <div class="fg">
            <label>Status</label>
            <select name="status" id="eStatus" class="fg" style="width:100%;padding:11px 14px;background:#fdf7ee;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-ui);font-size:.9rem;color:var(--text-dark);outline:none;">
                <option value="pending">⏳ Pending (belum ditampilkan)</option>
                <option value="approved">✅ Disetujui (tampil di website)</option>
                <option value="rejected">❌ Ditolak</option>
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Batal</button>
            <button type="submit" name="act_edit" class="btn btn-primary" onclick="return validateEditUlasan()"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
</div>

<script>
function setEStar(v){
    document.getElementById('eRat').value=v;
    document.querySelectorAll('.estar').forEach(function(s,i){
        s.style.color=i<v?'#C9890A':'#ddd';
    });
}
function openEditUlasan(r){
    document.getElementById('eId').value=r.id;
    document.getElementById('eNama').value=r.nama_tamu||r.display_name||'';
    document.getElementById('eKom').value=r.komentar;
    document.getElementById('eStatus').value=r.status||'pending';
    setEStar(r.rating);
    document.getElementById('editModal').classList.add('show');
}
function closeModal(id){document.getElementById(id).classList.remove('show');}
document.getElementById('editModal').addEventListener('click',function(e){if(e.target===this)closeModal('editModal');});

function openRejectModal(id) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectModal').classList.add('show');
}
document.getElementById('rejectModal').addEventListener('click',function(e){if(e.target===this)closeModal('rejectModal');});

const UL_FORBIDDEN = /[\{\}\[\]#@\$%\^&\*\(\)<>\/\\|~`"=\+;!\?]/;
function ulErrShow(id,msg){const el=document.getElementById(id);if(!el)return;el.querySelector('span').textContent=msg;el.style.display='flex';}
function ulErrClear(id){const el=document.getElementById(id);if(!el)return;el.style.display='none';}
function validateUlasanNama(inp,eid){
    const v=inp.value.trim();
    if(!v){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Nama wajib diisi.');return false;}
    if(v.length<3){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Nama minimal 3 karakter.');return false;}
    if(/\d/.test(v)){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Nama tidak boleh mengandung angka.');return false;}
    if(UL_FORBIDDEN.test(v)){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) dll.');return false;}
    inp.style.borderColor='#27ae60';ulErrClear(eid);return true;
}
function validateUlasanKom(inp,eid,cid){
    const el=document.getElementById(cid);if(el){const l=inp.value.length;el.textContent=l+' / 1000';el.style.color=l>900?(l>=1000?'#c0392b':'#b7770d'):'#7A5C3A';}
    const v=inp.value.trim();
    if(!v){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Komentar wajib diisi.');return false;}
    if(v.length<10){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Komentar minimal 10 karakter (sekarang '+v.length+').');return false;}
    if(UL_FORBIDDEN.test(v)){inp.style.borderColor='#e74c3c';ulErrShow(eid,'Tidak boleh menggunakan simbol seperti { } # @ $ % & * ( ) dll.');return false;}
    inp.style.borderColor='#27ae60';ulErrClear(eid);return true;
}
function validateEditUlasan(){
    let ok=true;
    const nEl=document.getElementById('eNama');if(nEl)ok=validateUlasanNama(nEl,'eu_nama_err')&&ok;
    const kEl=document.getElementById('eKom');if(kEl)ok=validateUlasanKom(kEl,'eu_kom_err','eu_kom_cnt')&&ok;
    return ok;
}
</script>

<div class="modal-overlay" id="rejectModal">
<div class="modal-box">
    <div class="modal-header">
        <h3><i class="fas fa-ban" style="color:#e74c3c;margin-right:8px"></i>Tolak Ulasan</h3>
        <button class="modal-close" onclick="closeModal('rejectModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
        <input type="hidden" name="reject_id" id="rejectId">
        <div class="fg">
            <label>Alasan Penolakan (opsional)</label>
            <textarea name="alasan_tolak" placeholder="Masukkan alasan penolakan (tidak wajib)..." style="min-height:80px"></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('rejectModal')">Batal</button>
            <button type="submit" name="act_reject" class="btn btn-danger"><i class="fas fa-ban"></i> Tolak Ulasan</button>
        </div>
    </form>
</div>
</div>

</body></html>