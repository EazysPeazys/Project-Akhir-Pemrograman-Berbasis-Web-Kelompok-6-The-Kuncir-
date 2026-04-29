<?php
require_once 'check_auth.php';
require_once '../koneksi.php';

$admin_name = $_SESSION['username'];

$total_res    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan"))['c'];
$pending_res  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='pending'"))['c'];
$confirmed_res= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='confirmed'"))['c'];
$cancelled_res= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='dibatalkan'"))['c'];
$rejected_res = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='ditolak'"))['c'];
$total_users  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM users WHERE role='user'"))['c'];
$total_ulasan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM ulasan"))['c'];
$avg_rating   = round(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT AVG(rating) avg FROM ulasan"))['avg'], 1);

$act_success = ''; $act_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $rid = (int)$_POST['approve_id'];
        $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_admin'] ?? '');
        mysqli_query($koneksi, "UPDATE kegiatan SET status='confirmed', catatan_admin='$catatan', updated_at=NOW() WHERE id='$rid'");
        $act_success = "Kegiatan #$rid berhasil dikonfirmasi!";
    }
    if (isset($_POST['reject_id'])) {
        $rid = (int)$_POST['reject_id'];
        $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_admin'] ?? '');
        mysqli_query($koneksi, "UPDATE kegiatan SET status='ditolak', catatan_admin='$catatan', updated_at=NOW() WHERE id='$rid'");
        $act_success = "Kegiatan #$rid telah ditolak.";
    }
    if (isset($_POST['delete_res_id'])) {
        $rid = (int)$_POST['delete_res_id'];
        mysqli_query($koneksi, "DELETE FROM kegiatan WHERE id='$rid'");
        $act_success = "Kegiatan #$rid berhasil dihapus.";
    }
}

$pending_list = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap, u.email FROM kegiatan k LEFT JOIN users u ON k.user_id=u.id WHERE k.status='pending' ORDER BY k.created_at ASC LIMIT 5");
$recent_ulasan = mysqli_query($koneksi, "SELECT u.*, COALESCE(us.nama_lengkap, u.nama_tamu,'Pengunjung') as display_name FROM ulasan u LEFT JOIN users us ON u.user_id=us.id ORDER BY u.created_at DESC LIMIT 5");

$chart_data = [];
for ($m = 1; $m <= 12; $m++) {
    $r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE MONTH(tanggal)=$m AND YEAR(tanggal)=YEAR(CURDATE())"));
    $chart_data[] = $r['c'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin – Rumah Adat Budaya Samarinda</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
.btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:8px;font-family:var(--font-ui);font-size:.82rem;font-weight:700;cursor:pointer;border:none;text-decoration:none;transition:all .2s}
.btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 15px rgba(139,46,0,.3)}
.btn-success{background:rgba(39,174,96,.1);color:#1e8449;border:1px solid rgba(39,174,96,.3)}
.btn-success:hover{background:#27ae60;color:#fff}
.btn-danger{background:rgba(231,76,60,.1);color:#c0392b;border:1px solid rgba(231,76,60,.3)}
.btn-danger:hover{background:#e74c3c;color:#fff}
.btn-sm{padding:6px 12px;font-size:.78rem}
.btn-outline{background:transparent;border:2px solid var(--border);color:var(--text-light)}
.btn-outline:hover{border-color:var(--primary);color:var(--primary)}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-med);margin-bottom:6px}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;background:#fdf7ee;border:1.5px solid var(--border);border-radius:8px;font-family:var(--font-ui);font-size:.9rem;color:var(--text-dark);outline:none;transition:all .3s}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--primary);background:#fff;box-shadow:0 0 0 3px rgba(139,46,0,.07)}
.fg textarea{min-height:90px;resize:vertical}
.alert{padding:12px 16px;border-radius:10px;font-size:.88rem;margin-bottom:18px;display:flex;align-items:center;gap:10px;font-weight:600}
.alert-success{background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#1e8449}
.alert-error{background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);color:#c0392b}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:all .3s}
.modal-overlay.show{opacity:1;visibility:visible}
.modal-box{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:500px;box-shadow:0 30px 80px rgba(0,0,0,.3);transform:scale(.95);transition:all .3s}
.modal-overlay.show .modal-box{transform:scale(1)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.modal-header h3{font-family:var(--font-display);font-size:1.05rem;color:var(--text-dark)}
.modal-close{background:none;border:none;font-size:1.1rem;color:var(--text-light);cursor:pointer;padding:4px;border-radius:6px}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
@media(max-width:1024px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.sidebar{transform:translateX(-100%)}.main-wrap{margin-left:0}}
</style>
<style>
.dash-welcome{background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:14px;padding:28px 32px;color:white;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.dash-welcome h2{font-family:var(--font-display);font-size:1.4rem;margin-bottom:6px}
.dash-welcome p{font-size:.9rem;opacity:.85}
.dash-welcome-icon{font-size:3.5rem;opacity:.3}
.chart-card{background:#fff;border-radius:14px;border:1px solid var(--border);box-shadow:var(--shadow-sm);padding:22px;margin-bottom:24px}
.chart-card h3{font-family:var(--font-display);font-size:.95rem;color:var(--text-dark);margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.chart-wrap{height:240px;position:relative}
.pending-badge{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:var(--secondary);color:var(--bg-dark);font-size:.65rem;font-weight:800;margin-left:6px}
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-wrap">
    <div class="topbar">
        <div class="topbar-title"><i class="fas fa-tachometer-alt" style="color:var(--secondary);margin-right:8px"></i>Dashboard</div>
        <div class="topbar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></div>
            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
        </div>
    </div>
    <div class="content">

        <?php if($act_success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?=$act_success?></div><?php endif; ?>
        <?php if($act_error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?=$act_error?></div><?php endif; ?>

        <div class="dash-welcome">
            <div>
                <h2>Selamat Datang, <?= htmlspecialchars($admin_name) ?>! 👋</h2>
                <p>Kelola konten Rumah Adat Budaya Kota Samarinda dari sini.</p>
            </div>
            <i class="fas fa-landmark dash-welcome-icon"></i>
        </div>

        <div class="stat-grid" style="margin-bottom:24px">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#8B2E00,#B84A1A)"><i class="fas fa-calendar-alt"></i></div>
                <div><div class="stat-num"><?=$total_res?></div><div class="stat-lbl">Total Kegiatan</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f39c12,#f1c40f)"><i class="fas fa-hourglass-half"></i></div>
                <div><div class="stat-num"><?=$pending_res?></div><div class="stat-lbl">Pending</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#27ae60,#2ecc71)"><i class="fas fa-check-circle"></i></div>
                <div><div class="stat-num"><?=$confirmed_res?></div><div class="stat-lbl">Dikonfirmasi</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#C9890A,#A06A05)"><i class="fas fa-star"></i></div>
                <div><div class="stat-num"><?=$avg_rating?:'-'?></div><div class="stat-lbl">Rating (<?=$total_ulasan?> ulasan)</div></div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
            <!-- Chart -->
            <div class="chart-card">
                <h3><i class="fas fa-chart-line" style="color:var(--secondary);margin-right:8px"></i>Kegiatan per Bulan <?= date('Y') ?></h3>
                <div class="chart-wrap"><canvas id="lineChart"></canvas></div>
            </div>

            <div class="table-card">
                <div class="tc-header">
                    <h3>Kegiatan Pending <span class="pending-badge"><?=$pending_res?></span></h3>
                    <a href="kegiatan.php?status=pending" class="btn btn-sm btn-outline">Lihat Semua</a>
                </div>
                <div style="overflow-x:auto">
                <table class="tbl">
                    <thead><tr><th>Kegiatan</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php while($r=mysqli_fetch_assoc($pending_list)): ?>
                    <tr>
                        <td><strong><?=htmlspecialchars($r['nama_kegiatan'])?></strong><br><small style="color:var(--text-light)"><?=htmlspecialchars($r['kategori']??'')?></small></td>
                        <td style="white-space:nowrap"><?=date('d M Y',strtotime($r['tanggal']))?></td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="approve_id" value="<?=$r['id']?>">
                                <button class="btn btn-success btn-sm" title="Konfirmasi"><i class="fas fa-check"></i></button>
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="openReject(<?=$r['id']?>)" title="Tolak"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($pending_res==0): ?>
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text-light)"><i class="fas fa-check-circle" style="color:#27ae60;margin-right:6px"></i>Tidak ada kegiatan pending</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="tc-header">
                <h3><i class="fas fa-star" style="color:var(--secondary);margin-right:6px"></i>Ulasan Terbaru</h3>
                <a href="ulasan.php" class="btn btn-sm btn-outline">Kelola Ulasan</a>
            </div>
            <div style="overflow-x:auto">
            <table class="tbl">
                <thead><tr><th>Nama</th><th>Rating</th><th>Komentar</th><th>Tanggal</th></tr></thead>
                <tbody>
                <?php while($u=mysqli_fetch_assoc($recent_ulasan)): ?>
                <tr>
                    <td><strong><?=htmlspecialchars($u['display_name'])?></strong></td>
                    <td><?php for($i=1;$i<=5;$i++) echo $u['rating']>=$i?'<span style="color:#C9890A">★</span>':'<span style="color:#ddd">★</span>'; ?></td>
                    <td style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=htmlspecialchars($u['komentar'])?></td>
                    <td style="white-space:nowrap"><?=date('d M Y',strtotime($u['created_at']))?></td>
                </tr>
                <?php endwhile; ?>
                <?php if($total_ulasan==0): ?>
                <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text-light)">Belum ada ulasan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

    </div>
</div>

<div class="modal-overlay" id="rejectModal">
<div class="modal-box">
    <div class="modal-header">
        <h3><i class="fas fa-times-circle" style="color:#e74c3c;margin-right:8px"></i>Tolak Kegiatan</h3>
        <button class="modal-close" onclick="closeModal('rejectModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
        <input type="hidden" name="reject_id" id="rejectId">
        <div class="fg"><label>Catatan / Alasan Penolakan</label><textarea name="catatan_admin" required placeholder="Alasan penolakan..."></textarea></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeModal('rejectModal')">Batal</button>
            <button type="submit" class="btn btn-danger"><i class="fas fa-times-circle"></i> Tolak</button>
        </div>
    </form>
</div>
</div>

<script>
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Kegiatan',
            data: <?= json_encode($chart_data) ?>,
            borderColor: '#8B2E00',
            backgroundColor: 'rgba(139,46,0,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#C9890A',
            pointRadius: 4,
            tension: 0.4,
            fill: true
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,0.05)'}}, x:{grid:{display:false}} } }
});

function openReject(id) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectModal').classList.add('show');
}
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
document.getElementById('rejectModal').addEventListener('click', function(e){ if(e.target===this) closeModal('rejectModal'); });
</script>
</body>
</html>