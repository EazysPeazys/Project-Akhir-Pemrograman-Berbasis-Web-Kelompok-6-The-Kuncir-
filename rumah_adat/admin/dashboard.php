<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../koneksi.php';

$admin_name = $_SESSION['username'];
$page = $_GET['page'] ?? 'dashboard';

$total_res    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan"))['c'];
$pending_res  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='pending'"))['c'];
$confirmed_res= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='confirmed'"))['c'];
$cancelled_res= mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='dibatalkan'"))['c'];
$rejected_res = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kegiatan WHERE status='ditolak'"))['c'];
$total_users  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM users WHERE role='user'"))['c'];

$act_success = ''; $act_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['approve_id'])) {
        $rid = (int)$_POST['approve_id'];
        $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_admin'] ?? '');
        if (mysqli_query($koneksi, "UPDATE kegiatan SET status='confirmed', catatan_admin='$catatan', updated_at=NOW() WHERE id='$rid'")) {
            $act_success = "Reservasi #$rid berhasil dikonfirmasi!";
        } else { $act_error = "Gagal mengkonfirmasi."; }
    }

    if (isset($_POST['reject_id'])) {
        $rid = (int)$_POST['reject_id'];
        $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_admin'] ?? '');
        if (mysqli_query($koneksi, "UPDATE kegiatan SET status='ditolak', catatan_admin='$catatan', updated_at=NOW() WHERE id='$rid'")) {
            $act_success = "Reservasi #$rid telah ditolak.";
        } else { $act_error = "Gagal menolak."; }
    }

    if (isset($_POST['pending_id'])) {
        $rid = (int)$_POST['pending_id'];
        if (mysqli_query($koneksi, "UPDATE kegiatan SET status='pending', catatan_admin='', updated_at=NOW() WHERE id='$rid'")) {
            $act_success = "Reservasi #$rid dikembalikan ke Pending.";
        } else { $act_error = "Gagal mengubah status."; }
    }

    if (isset($_POST['delete_res_id'])) {
        $rid = (int)$_POST['delete_res_id'];
        if (mysqli_query($koneksi, "DELETE FROM kegiatan WHERE id='$rid'")) {
            $act_success = "Reservasi #$rid berhasil dihapus permanen.";
        } else { $act_error = "Gagal menghapus."; }
    }
}

$pending_list = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap, u.email, u.no_hp as user_hp FROM kegiatan k LEFT JOIN users u ON k.user_id=u.id WHERE k.status='pending' ORDER BY k.created_at ASC LIMIT 5");

$all_res = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap, u.email FROM kegiatan k LEFT JOIN users u ON k.user_id=u.id ORDER BY k.created_at DESC");

$all_users = mysqli_query($koneksi, "SELECT u.*, COUNT(k.id) as total_res FROM users u LEFT JOIN kegiatan k ON u.id=k.user_id WHERE u.role='user' GROUP BY u.id ORDER BY u.created_at DESC");

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
<title>Admin Dashboard – Rumah Adat Budaya Kota Samarinda</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
:root {
    --primary:       #8B2E00;
    --primary-dark:  #6B1E00;
    --primary-light: #B84A1A;
    --secondary:     #C9890A;
    --secondary-dark:#A06A05;
    --bg-dark:       #1C0A00;
    --bg-med:        #2D1500;
    --bg-light:      #FDF8F0;
    --bg-card:       #FFF9F0;
    --text-dark:     #1C0A00;
    --text-med:      #4A2800;
    --text-light:    #7A5C3A;
    --text-white:    #FDF8F0;
    --text-gold:     #C9890A;
    --border:        #E8D4B0;
    --border-dark:   rgba(201,137,10,0.2);
    --shadow-sm:     0 2px 10px rgba(139,46,0,0.08);
    --shadow-md:     0 8px 30px rgba(139,46,0,0.15);
    --font-display:  'Cinzel', serif;
    --font-ui:       'Nunito', sans-serif;
    --sidebar-w:     270px;
    --topbar-h:      70px;
    --radius-sm:     6px;
    --radius-md:     12px;
    --radius-lg:     18px;
    --radius-xl:     28px;
    --transition:    0.3s cubic-bezier(0.4,0,0.2,1);
}

* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:var(--font-ui); background:#F5EFE6; color:var(--text-dark); display:flex; min-height:100vh; overflow-x:hidden; }

.sidebar { width:var(--sidebar-w); min-height:100vh; background:var(--bg-dark); position:fixed; top:0; left:0; z-index:200; display:flex; flex-direction:column; border-right:1px solid var(--border-dark); transition:transform var(--transition); }
.sidebar-brand { padding:22px 18px 18px; border-bottom:1px solid var(--border-dark); }
.brand-logo { display:flex; align-items:center; gap:12px; text-decoration:none; }
.brand-icon { width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); display:flex; align-items:center; justify-content:center; color:white; font-size:1.2rem; box-shadow:0 4px 15px rgba(139,46,0,0.4); flex-shrink:0; }
.brand-text { display:flex; flex-direction:column; }
.brand-name { font-family:var(--font-display); font-size:0.82rem; font-weight:700; color:var(--text-white); letter-spacing:0.5px; line-height:1.2; }
.brand-sub  { font-size:0.65rem; color:var(--text-gold); font-style:italic; }
.admin-badge { background:linear-gradient(135deg,var(--secondary),var(--secondary-dark)); color:var(--bg-dark); font-size:0.6rem; font-weight:800; padding:2px 8px; border-radius:50px; letter-spacing:1px; text-transform:uppercase; margin-top:4px; display:inline-block; }

.sidebar-user { padding:14px 18px; display:flex; align-items:center; gap:12px; background:rgba(201,137,10,0.08); border-bottom:1px solid var(--border-dark); }
.user-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,var(--secondary),var(--secondary-dark)); display:flex; align-items:center; justify-content:center; color:var(--bg-dark); font-size:1rem; font-weight:700; font-family:var(--font-display); flex-shrink:0; }
.user-name { font-size:0.85rem; font-weight:700; color:var(--text-white); }
.user-role-badge { font-size:0.65rem; color:var(--secondary); }

.sidebar-nav { flex:1; padding:14px 10px; overflow-y:auto; }
.nav-section-label { font-size:0.62rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,0.22); padding:10px 8px 5px; }
.sidebar-link { display:flex; align-items:center; gap:11px; padding:11px 14px; border-radius:var(--radius-md); color:rgba(255,255,255,0.65) !important; text-decoration:none !important; font-size:0.86rem; font-weight:600; transition:var(--transition); margin-bottom:2px; position:relative; }
.sidebar-link:link, .sidebar-link:visited { color:rgba(255,255,255,0.65) !important; text-decoration:none !important; }
.sidebar-link i { width:18px; text-align:center; font-size:0.9rem; flex-shrink:0; color:rgba(201,137,10,0.7); transition:color 0.25s; }
.sidebar-link:hover { background:rgba(201,137,10,0.12) !important; color:#C9890A !important; }
.sidebar-link:hover i { color:#C9890A !important; }
.sidebar-link.active { background:linear-gradient(135deg,var(--primary),var(--primary-light)) !important; color:white !important; box-shadow:0 4px 14px rgba(139,46,0,0.3); }
.sidebar-link.active i { color:white !important; }
.sidebar-link .badge-count { margin-left:auto; background:var(--secondary); color:var(--bg-dark); font-size:0.62rem; font-weight:800; padding:2px 7px; border-radius:50px; }
.nav-section-label { font-size:0.62rem !important; font-weight:700 !important; letter-spacing:2px !important; text-transform:uppercase !important; color:rgba(255,255,255,0.3) !important; padding:10px 8px 5px !important; }
.sidebar-footer { padding:12px 10px; border-top:1px solid var(--border-dark); }
.sidebar-footer a { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:var(--radius-md); color:rgba(255,255,255,0.35) !important; font-size:0.83rem; font-weight:600; text-decoration:none !important; transition:var(--transition); }
.sidebar-footer a:link, .sidebar-footer a:visited { color:rgba(255,255,255,0.35) !important; }
.sidebar-footer a:hover { background:rgba(231,76,60,0.1) !important; color:#e74c3c !important; }

.topbar { position:fixed; top:0; left:var(--sidebar-w); right:0; height:var(--topbar-h); background:rgba(253,248,240,0.97); backdrop-filter:blur(20px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; z-index:100; box-shadow:var(--shadow-sm); }
.topbar-left { display:flex; align-items:center; gap:14px; }
.menu-toggle { display:none; background:none; border:none; font-size:1.2rem; color:var(--text-med); cursor:pointer; padding:8px; border-radius:var(--radius-sm); }
.page-title { font-family:var(--font-display); font-size:0.95rem; font-weight:700; color:var(--text-dark); }
.page-breadcrumb { font-size:0.75rem; color:var(--text-light); margin-top:1px; }
.topbar-right { display:flex; align-items:center; gap:10px; }
.topbar-btn { position:relative; width:40px; height:40px; border-radius:50%; background:var(--bg-card); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--text-med); cursor:pointer; transition:var(--transition); font-size:0.9rem; text-decoration:none; }
.topbar-btn:hover { background:var(--primary); color:white; border-color:var(--primary); }
.notif-dot { position:absolute; top:7px; right:7px; width:8px; height:8px; border-radius:50%; background:var(--secondary); border:2px solid var(--bg-light); }
.topbar-profile { display:flex; align-items:center; gap:10px; padding:6px 14px 6px 6px; border-radius:var(--radius-xl); background:var(--bg-card); border:1px solid var(--border); cursor:pointer; }
.topbar-avatar { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,var(--secondary),var(--secondary-dark)); display:flex; align-items:center; justify-content:center; color:var(--bg-dark); font-size:0.8rem; font-weight:700; font-family:var(--font-display); }

.main-content { margin-left:var(--sidebar-w); margin-top:var(--topbar-h); flex:1; padding:28px; min-height:calc(100vh - var(--topbar-h)); }
.dash-section { display:none; }
.dash-section.active { display:block; }

.stats-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:14px; margin-bottom:24px; }
.stat-card { background:white; border-radius:var(--radius-lg); padding:20px 16px; border:1px solid var(--border); box-shadow:var(--shadow-sm); position:relative; overflow:hidden; transition:var(--transition); cursor:pointer; }
.stat-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.stat-card.c1::before { background:linear-gradient(90deg,var(--primary),var(--primary-light)); }
.stat-card.c2::before { background:linear-gradient(90deg,#f39c12,#f1c40f); }
.stat-card.c3::before { background:linear-gradient(90deg,#27ae60,#2ecc71); }
.stat-card.c4::before { background:linear-gradient(90deg,#c0392b,#e74c3c); }
.stat-card.c5::before { background:linear-gradient(90deg,#8e44ad,#9b59b6); }
.stat-card.c6::before { background:linear-gradient(90deg,#2980b9,#3498db); }
.stat-icon { width:44px; height:44px; border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; font-size:1.1rem; margin-bottom:12px; }
.stat-card.c1 .stat-icon { background:rgba(139,46,0,0.08); color:var(--primary); }
.stat-card.c2 .stat-icon { background:rgba(243,156,18,0.1); color:#f39c12; }
.stat-card.c3 .stat-icon { background:rgba(39,174,96,0.1); color:#27ae60; }
.stat-card.c4 .stat-icon { background:rgba(231,76,60,0.1); color:#e74c3c; }
.stat-card.c5 .stat-icon { background:rgba(142,68,173,0.1); color:#8e44ad; }
.stat-card.c6 .stat-icon { background:rgba(41,128,185,0.1); color:#2980b9; }
.stat-num   { font-family:var(--font-display); font-size:1.8rem; font-weight:700; color:var(--text-dark); line-height:1; margin-bottom:4px; }
.stat-label { font-size:0.72rem; color:var(--text-light); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }

.dash-card { background:white; border-radius:var(--radius-lg); border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; }
.card-header { padding:16px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:var(--font-display); font-size:0.9rem; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px; }
.card-title i { color:var(--primary); }
.card-action { font-size:0.78rem; color:var(--primary); font-weight:700; text-decoration:none; display:flex; align-items:center; gap:4px; cursor:pointer; }
.card-body { padding:20px 22px; }

.pending-item { padding:16px 0; border-bottom:1px solid var(--border); display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
.pending-item:last-child { border-bottom:none; }
.pending-icon { width:46px; height:46px; border-radius:var(--radius-md); background:rgba(243,156,18,0.1); border:1px solid rgba(243,156,18,0.3); display:flex; align-items:center; justify-content:center; color:#f39c12; font-size:1.1rem; flex-shrink:0; }
.pending-info { flex:1; min-width:0; }
.pending-title { font-weight:700; font-size:0.88rem; color:var(--text-dark); margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pending-meta  { font-size:0.73rem; color:var(--text-light); line-height:1.5; }
.pending-meta b { color:var(--text-med); }
.pending-actions { display:flex; flex-direction:row; gap:6px; flex-shrink:0; align-items:center; flex-wrap:wrap; }

.btn { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:var(--radius-xl); font-family:var(--font-ui); font-size:0.86rem; font-weight:700; cursor:pointer; border:none; transition:var(--transition); text-decoration:none; white-space:nowrap; }
.btn-primary { background:linear-gradient(135deg,var(--primary),var(--primary-light)); color:white; box-shadow:0 3px 12px rgba(139,46,0,0.22); }
.btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(139,46,0,0.32); color:white; }
.btn-success { background:linear-gradient(135deg,#1e8449,#27ae60); color:white; }
.btn-success:hover { transform:translateY(-2px); color:white; }
.btn-danger  { background:linear-gradient(135deg,#c0392b,#e74c3c); color:white; }
.btn-danger:hover  { transform:translateY(-2px); color:white; }
.btn-warning { background:linear-gradient(135deg,#d4870a,#f39c12); color:white; }
.btn-warning:hover { transform:translateY(-2px); color:white; }
.btn-gold    { background:linear-gradient(135deg,var(--secondary),var(--secondary-dark)); color:var(--bg-dark); }
.btn-gold:hover { transform:translateY(-2px); color:var(--bg-dark); }
.btn-outline { background:transparent; border:2px solid var(--border); color:var(--text-med); }
.btn-outline:hover { border-color:var(--primary); color:var(--primary); }
.btn-sm { padding:7px 14px; font-size:0.78rem; }
.btn-xs { padding:5px 10px; font-size:0.72rem; border-radius:var(--radius-xl); }

.badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:50px; font-size:0.7rem; font-weight:700; letter-spacing:0.3px; white-space:nowrap; }
.badge-pending   { background:rgba(243,156,18,0.12); color:#d4870a; border:1px solid rgba(243,156,18,0.3); }
.badge-confirmed { background:rgba(39,174,96,0.12);  color:#1e8449; border:1px solid rgba(39,174,96,0.3); }
.badge-cancelled { background:rgba(231,76,60,0.12);  color:#c0392b; border:1px solid rgba(231,76,60,0.3); }
.badge-rejected  { background:rgba(142,68,173,0.12); color:#7d3c98; border:1px solid rgba(142,68,173,0.3); }

.data-table { width:100%; border-collapse:collapse; }
.data-table th { padding:11px 14px; text-align:left; font-size:0.68rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--text-light); background:var(--bg-light); border-bottom:1px solid var(--border); }
.data-table td { padding:13px 14px; font-size:0.85rem; color:var(--text-med); border-bottom:1px solid var(--border); vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tr:hover td { background:rgba(139,46,0,0.02); }

.alert { padding:12px 16px; border-radius:var(--radius-md); font-size:0.86rem; margin-bottom:16px; display:flex; align-items:center; gap:10px; }
.alert-success { background:rgba(39,174,96,0.1); border:1px solid rgba(39,174,96,0.3); color:#1e8449; }
.alert-error   { background:rgba(231,76,60,0.1); border:1px solid rgba(231,76,60,0.3); color:#c0392b; }
.alert-warning { background:rgba(243,156,18,0.1); border:1px solid rgba(243,156,18,0.3); color:#d4870a; }

.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
.grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:20px; }

.page-header-dash { margin-bottom:22px; display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; }
.page-header-dash h1 { font-family:var(--font-display); font-size:1.4rem; color:var(--text-dark); }
.page-header-dash p  { color:var(--text-light); font-size:0.88rem; margin-top:3px; }

.form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:0.74rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-med); margin-bottom:6px; }
.form-control { width:100%; padding:11px 14px; background:var(--bg-light); border:1.5px solid var(--border); border-radius:var(--radius-md); font-family:var(--font-ui); font-size:0.88rem; color:var(--text-dark); outline:none; transition:var(--transition); }
.form-control:focus { border-color:var(--primary); background:white; box-shadow:0 0 0 3px rgba(139,46,0,0.07); }
textarea.form-control { resize:vertical; min-height:80px; }
select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%238B2E00' d='M6 8L0 0h12z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:34px; }

.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.65); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px; opacity:0; visibility:hidden; transition:var(--transition); }
.modal-overlay.active { opacity:1; visibility:visible; }
.modal-box { background:white; border-radius:var(--radius-xl); padding:32px; max-width:480px; width:100%; box-shadow:0 24px 80px rgba(0,0,0,0.2); transform:scale(0.9); transition:var(--transition); }
.modal-overlay.active .modal-box { transform:scale(1); }
.modal-title { font-family:var(--font-display); font-size:1.2rem; color:var(--text-dark); margin-bottom:6px; }
.modal-subtitle { color:var(--text-light); font-size:0.88rem; margin-bottom:20px; }

.sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:199; }

.chart-container { position:relative; height:220px; }

.activity-item { display:flex; gap:12px; padding:11px 0; border-bottom:1px solid var(--border); align-items:flex-start; }
.activity-item:last-child { border-bottom:none; }
.act-dot { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.85rem; flex-shrink:0; }
.act-dot.green  { background:rgba(39,174,96,0.1);  color:#27ae60; }
.act-dot.orange { background:rgba(243,156,18,0.1); color:#f39c12; }
.act-dot.red    { background:rgba(231,76,60,0.1);  color:#e74c3c; }
.act-dot.purple { background:rgba(142,68,173,0.1); color:#8e44ad; }
.act-text { font-size:0.83rem; color:var(--text-med); flex:1; }
.act-time { font-size:0.7rem; color:var(--text-light); white-space:nowrap; }

.status-chip { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:50px; font-size:0.75rem; font-weight:700; }

.welcome-banner { background:linear-gradient(135deg,var(--bg-dark),var(--bg-med),#3D2000); border-radius:var(--radius-lg); padding:28px 32px; margin-bottom:22px; position:relative; overflow:hidden; border:1px solid var(--border-dark); }
.welcome-banner::before { content:''; position:absolute; inset:0; background-image:repeating-linear-gradient(45deg,rgba(201,137,10,0.04) 0,rgba(201,137,10,0.04) 1px,transparent 1px,transparent 28px); }
.welcome-content { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap; }
.welcome-tag { font-size:0.68rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--text-gold); margin-bottom:6px; }
.welcome-title { font-family:var(--font-display); font-size:1.4rem; color:white; font-weight:700; margin-bottom:6px; }
.welcome-title em { color:var(--text-gold); font-style:italic; }
.welcome-desc { color:rgba(255,255,255,0.55); font-size:0.88rem; }
.welcome-stat-grid { display:flex; gap:28px; }
.wstat { text-align:center; }
.wstat-num { font-family:var(--font-display); font-size:1.6rem; color:var(--secondary); }
.wstat-lbl { font-size:0.65rem; color:rgba(255,255,255,0.4); text-transform:uppercase; letter-spacing:1px; }

@media (max-width:1280px) { .stats-grid { grid-template-columns:repeat(3,1fr); } }
@media (max-width:1024px) { .grid-2 { grid-template-columns:1fr; } .stats-grid { grid-template-columns:repeat(3,1fr); } }
@media (max-width:768px) { .sidebar { transform:translateX(-100%); } .sidebar.open { transform:translateX(0); } .sidebar-overlay.show { display:block; } .topbar { left:0; } .main-content { margin-left:0; padding:18px 14px; } .menu-toggle { display:flex; } .stats-grid { grid-template-columns:repeat(2,1fr); } .form-row { grid-template-columns:1fr; } }
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-brand" style="display:flex;align-items:center;gap:12px;padding:20px 18px;border-bottom:1px solid rgba(201,137,10,0.15);">
        <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#8B2E00,#B84A1A);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;">
            <i class="fas fa-torii-gate"></i>
        </div>
        <div style="display:flex;flex-direction:column;line-height:1.3;">
            <span style="font-family:'Cinzel',serif;font-size:.85rem;font-weight:700;color:white;letter-spacing:.5px;">Rumah Adat</span>
            <span style="font-size:.65rem;color:#C9890A;font-style:italic;letter-spacing:1px;">Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <!-- MENU UTAMA -->
        <div class="nav-section-label">Menu Utama</div>
        <a href="#" class="sidebar-link active" data-section="overview" onclick="showSection('overview',this)">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <!-- KELOLA KONTEN -->
        <div class="nav-section-label" style="margin-top:8px;">Kelola Konten</div>
        <a href="#" class="sidebar-link" data-section="pending" onclick="showSection('pending',this)">
            <i class="fas fa-calendar-alt"></i> Kegiatan / Acara
            <?php if ($pending_res > 0): ?><span class="badge-count"><?= $pending_res ?></span><?php endif; ?>
        </a>
        <a href="#" class="sidebar-link" data-section="semua-reservasi" onclick="showSection('semua-reservasi',this)">
            <i class="fas fa-images"></i> Galeri Foto
        </a>
        <a href="#" class="sidebar-link" data-section="pengguna" onclick="showSection('pengguna',this)">
            <i class="fas fa-star"></i> Ulasan
        </a>

        <!-- AKUN -->
        <div class="nav-section-label" style="margin-top:8px;">Akun</div>
        <a href="ganti_password.php" class="sidebar-link">
            <i class="fas fa-key"></i> Ganti Password
        </a>
        <a href="../index.php" class="sidebar-link" target="_blank">
            <i class="fas fa-globe"></i> Lihat Website
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>

<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <div>
            <div class="page-title" id="topbarTitle">Overview Dashboard</div>
            <div class="page-breadcrumb">Admin Dashboard Panel · Rumah Adat Budaya Kota Samarinda</div>
        </div>
    </div>
    <div class="topbar-right">
        <a href="#" class="topbar-btn" title="Reservasi Pending" onclick="showSection('pending',null)">
            <i class="fas fa-bell"></i>
            <?php if ($pending_res > 0): ?><span class="notif-dot"></span><?php endif; ?>
        </a>
        <a href="../index.php" class="topbar-btn" title="Lihat Website" target="_blank">
            <i class="fas fa-external-link-alt"></i>
        </a>
        <div class="topbar-profile-wrap" id="topbarProfileWrap">
            <div class="topbar-profile" onclick="toggleTopbarDropdown()">
                <div class="topbar-avatar"><?= strtoupper(substr($admin_name, 0, 1)) ?></div>
                <span style="font-size:0.85rem;font-weight:700;color:var(--text-dark);"><?= htmlspecialchars($admin_name) ?></span>
                <span style="font-size:0.62rem;color:var(--secondary);font-weight:800;padding:2px 7px;background:rgba(201,137,10,0.1);border-radius:50px;border:1px solid rgba(201,137,10,0.25);">ADMIN</span>
                <i class="fas fa-chevron-down" id="topbarChevron" style="font-size:0.7rem;color:var(--text-light);transition:transform 0.3s;margin-left:2px;"></i>
            </div>
            <div class="topbar-dropdown" id="topbarDropdown">
                <div class="topbar-dropdown-header">
                    <div class="td-avatar"><?= strtoupper(substr($admin_name, 0, 1)) ?></div>
                    <div>
                        <div class="td-name"><?= htmlspecialchars($admin_name) ?></div>
                        <div class="td-role">Administrator</div>
                    </div>
                </div>
                <div class="topbar-dropdown-body">
                    <a href="../index.php" target="_blank" class="td-item">
                        <i class="fas fa-globe"></i> Lihat Website
                    </a>
                    <div class="td-divider"></div>
                    <a href="../logout.php" class="td-item td-logout">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.topbar-profile-wrap { position: relative; }
.topbar-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 220px;
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    box-shadow: 0 10px 40px rgba(139,46,0,0.15);
    z-index: 999;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.25s ease;
}
.topbar-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.topbar-dropdown-header {
    padding: 16px;
    background: linear-gradient(135deg, var(--bg-dark), var(--bg-med));
    display: flex;
    align-items: center;
    gap: 12px;
}
.td-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--secondary), var(--secondary-dark));
    display: flex; align-items: center; justify-content: center;
    color: var(--bg-dark); font-weight: 700; font-family: var(--font-display);
    font-size: 1rem; flex-shrink: 0;
}
.td-name  { font-size: 0.88rem; font-weight: 700; color: white; }
.td-role  { font-size: 0.68rem; color: var(--secondary); font-weight: 700; margin-top: 2px; letter-spacing: 1px; text-transform: uppercase; }
.td-email { font-size: 0.72rem; color: rgba(255,255,255,0.5); margin-top: 2px; }
.topbar-dropdown-body { padding: 8px; }
.td-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: var(--radius-md);
    font-family: var(--font-ui);
    font-size: 0.86rem;
    font-weight: 600;
    color: var(--text-med);
    text-decoration: none;
    transition: var(--transition);
    cursor: pointer;
}
.td-item:hover { background: var(--bg-light); color: var(--primary); }
.td-item i { width: 18px; text-align: center; color: var(--text-light); }
.td-item:hover i { color: var(--primary); }
.td-divider { height: 1px; background: var(--border); margin: 6px 0; }
.td-logout:hover { background: rgba(231,76,60,0.08) !important; color: #e74c3c !important; }
.td-logout:hover i { color: #e74c3c !important; }
</style>

<main class="main-content">

<?php if ($act_success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $act_success ?></div><?php endif; ?>
<?php if ($act_error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $act_error ?></div><?php endif; ?>

<div class="dash-section active" id="section-overview">

    <div class="welcome-banner">
        <div class="welcome-content">
            <div>
                <div class="welcome-tag"><i class="fas fa-shield-alt"></i> Administrator Panel</div>
                <h1 class="welcome-title">Dashboard Admin, <em><?= htmlspecialchars($admin_name) ?></em></h1>
                <p class="welcome-desc">Kelola semua reservasi, pengguna, dan kegiatan Rumah Adat Budaya Kota Samarinda.</p>
            </div>
            <div class="welcome-stat-grid">
                <div class="wstat"><div class="wstat-num"><?= $pending_res ?></div><div class="wstat-lbl">Pending</div></div>
                <div class="wstat"><div class="wstat-num"><?= $total_users ?></div><div class="wstat-lbl">Pengguna</div></div>
                <div class="wstat"><div class="wstat-num"><?= $total_res ?></div><div class="wstat-lbl">Total Res.</div></div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card c1" onclick="showSection('semua-reservasi',null)"><div class="stat-icon"><i class="fas fa-calendar-alt"></i></div><div class="stat-num"><?= $total_res ?></div><div class="stat-label">Total Reservasi</div></div>
        <div class="stat-card c2" onclick="showSection('pending',null)"><div class="stat-icon"><i class="fas fa-hourglass-half"></i></div><div class="stat-num"><?= $pending_res ?></div><div class="stat-label">Pending</div></div>
        <div class="stat-card c3" onclick="showSection('semua-reservasi',null)"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-num"><?= $confirmed_res ?></div><div class="stat-label">Dikonfirmasi</div></div>
        <div class="stat-card c4" onclick="showSection('semua-reservasi',null)"><div class="stat-icon"><i class="fas fa-times-circle"></i></div><div class="stat-num"><?= $cancelled_res ?></div><div class="stat-label">Dibatalkan</div></div>
        <div class="stat-card c5" onclick="showSection('semua-reservasi',null)"><div class="stat-icon"><i class="fas fa-ban"></i></div><div class="stat-num"><?= $rejected_res ?></div><div class="stat-label">Ditolak</div></div>
        <div class="stat-card c6" onclick="showSection('pengguna',null)"><div class="stat-icon"><i class="fas fa-users"></i></div><div class="stat-num"><?= $total_users ?></div><div class="stat-label">Pengguna</div></div>
    </div>

    <div class="grid-2">
        <div class="dash-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-chart-line"></i> Grafik Reservasi <?= date('Y') ?></div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="reservasiChart"></canvas>
                </div>
            </div>
        </div>

        <div class="dash-card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-clock"></i> Antrian Pending <span class="badge badge-pending" style="margin-left:6px;"><?= $pending_res ?></span></div>
                <a href="#" class="card-action" onclick="showSection('pending',null)">Kelola <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body" style="padding:0 20px;">
                <?php
                mysqli_data_seek($pending_list, 0);
                if (mysqli_num_rows($pending_list) > 0):
                    while ($p = mysqli_fetch_assoc($pending_list)):
                ?>
                <div class="pending-item">
                    <div class="pending-icon"><i class="fas fa-calendar-plus"></i></div>
                    <div class="pending-info">
                        <div class="pending-title"><?= htmlspecialchars($p['nama_kegiatan']) ?></div>
                        <div class="pending-meta">
                            <b><?= htmlspecialchars($p['nama_lengkap'] ?? $p['nama_penyelenggara']) ?></b> · <?= date('d M Y', strtotime($p['tanggal'])) ?><br>
                            <i class="fas fa-map-marker-alt"></i> <?= $p['lokasi'] ?> &nbsp;·&nbsp; <i class="fas fa-clock"></i> <?= $p['jam_mulai'] ?>–<?= $p['jam_selesai'] ?>
                        </div>
                    </div>
                    <div class="pending-actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="approve_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="catatan_admin" value="Reservasi dikonfirmasi oleh admin.">
                            <button type="submit" class="btn btn-xs btn-success" title="Konfirmasi">
                                <i class="fas fa-check-circle"></i> Konfirmasi
                            </button>
                        </form>
                        <button class="btn btn-xs btn-danger" onclick="openRejectModal(<?= $p['id'] ?>)" title="Tolak">
                            <i class="fas fa-times-circle"></i> Tolak
                        </button>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align:center;padding:32px 0;color:var(--text-light);">
                    <i class="fas fa-check-double" style="font-size:2rem;margin-bottom:10px;display:block;color:#27ae60;opacity:0.5;"></i>
                    <p style="font-size:0.88rem;">Tidak ada reservasi pending. Semua beres! 🎉</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dash-card">
        <div class="card-header"><div class="card-title"><i class="fas fa-history"></i> Aktivitas Terbaru</div></div>
        <div class="card-body" style="padding:0 22px;">
            <?php
            $act_log = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap FROM kegiatan k LEFT JOIN users u ON k.user_id=u.id ORDER BY k.updated_at DESC, k.created_at DESC LIMIT 8");
            while ($al = mysqli_fetch_assoc($act_log)):
                $dot_class = $al['status'] === 'confirmed' ? 'green' : ($al['status'] === 'dibatalkan' || $al['status'] === 'ditolak' ? 'red' : 'orange');
                $ico = $al['status'] === 'confirmed' ? 'fa-check' : ($al['status'] === 'dibatalkan' || $al['status'] === 'ditolak' ? 'fa-times' : 'fa-hourglass-half');
            ?>
            <div class="activity-item">
                <div class="act-dot <?= $dot_class ?>"><i class="fas <?= $ico ?>"></i></div>
                <div class="act-text">
                    <b><?= htmlspecialchars($al['nama_kegiatan']) ?></b> oleh <?= htmlspecialchars($al['nama_lengkap'] ?? $al['nama_penyelenggara']) ?>
                    — <span class="badge badge-<?= $al['status'] === 'confirmed' ? 'confirmed' : ($al['status'] === 'dibatalkan' ? 'cancelled' : ($al['status'] === 'ditolak' ? 'rejected' : 'pending')) ?>"><?= ucfirst($al['status']) ?></span>
                </div>
                <div class="act-time"><?= date('d M, H:i', strtotime($al['updated_at'] ?: $al['created_at'])) ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<div class="dash-section" id="section-pending">
    <div class="page-header-dash">
        <div><h1>Reservasi Pending</h1><p>Tinjau dan ambil tindakan untuk reservasi yang menunggu persetujuan</p></div>
    </div>

    <?php
    $all_pending = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap, u.email, u.no_hp as uhp FROM kegiatan k LEFT JOIN users u ON k.user_id=u.id WHERE k.status='pending' ORDER BY k.created_at ASC");
    if (mysqli_num_rows($all_pending) === 0):
    ?>
    <div class="dash-card">
        <div style="text-align:center;padding:60px 20px;color:var(--text-light);">
            <i class="fas fa-check-double" style="font-size:3rem;margin-bottom:16px;display:block;color:#27ae60;opacity:0.4;"></i>
            <p style="font-size:1.1rem;font-weight:700;">Tidak ada reservasi pending!</p>
            <p style="font-size:0.9rem;margin-top:6px;">Semua reservasi telah ditangani.</p>
        </div>
    </div>
    <?php else: ?>
    <?php while ($p = mysqli_fetch_assoc($all_pending)): ?>
    <div class="dash-card" style="margin-bottom:16px;">
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr auto;gap:20px;align-items:start;">
                <div>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
                        <span class="badge badge-pending"><i class="fas fa-hourglass-half"></i> Pending</span>
                        <span style="font-size:0.72rem;color:var(--text-light);">Diajukan: <?= date('d M Y, H:i', strtotime($p['created_at'])) ?></span>
                        <span style="font-size:0.72rem;color:var(--text-light);">#<?= $p['id'] ?></span>
                    </div>
                    <h3 style="font-family:var(--font-display);font-size:1.1rem;color:var(--text-dark);margin-bottom:10px;"><?= htmlspecialchars($p['nama_kegiatan']) ?></h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px 20px;margin-bottom:12px;">
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Penyelenggara</div><div style="font-size:0.88rem;color:var(--text-dark);font-weight:700;"><?= htmlspecialchars($p['nama_lengkap'] ?? $p['nama_penyelenggara']) ?></div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">No. HP</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= htmlspecialchars($p['no_hp']) ?></div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Tanggal</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= date('d F Y', strtotime($p['tanggal'])) ?></div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Waktu</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= $p['jam_mulai'] ?> – <?= $p['jam_selesai'] ?> WITA</div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Lokasi</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= htmlspecialchars($p['lokasi']) ?></div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Kategori</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= htmlspecialchars($p['kategori']) ?></div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Peserta</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= $p['jumlah_peserta'] ?> orang</div></div>
                        <div><div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;">Email</div><div style="font-size:0.88rem;color:var(--text-dark);"><?= htmlspecialchars($p['email'] ?? '-') ?></div></div>
                    </div>
                    <?php if (!empty($p['deskripsi'])): ?>
                    <div style="background:var(--bg-light);border-radius:var(--radius-md);padding:12px 16px;border:1px solid var(--border);">
                        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-light);font-weight:700;margin-bottom:4px;">Deskripsi</div>
                        <p style="font-size:0.85rem;color:var(--text-med);"><?= htmlspecialchars($p['deskripsi']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;min-width:140px;">
                    <form method="POST">
                        <input type="hidden" name="approve_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="catatan_admin" value="Reservasi Anda telah dikonfirmasi. Selamat!">
                        <button type="submit" class="btn btn-success" style="width:100%;">
                            <i class="fas fa-check-circle"></i> Konfirmasi
                        </button>
                    </form>
                    <button class="btn btn-danger" style="width:100%;" onclick="openRejectModal(<?= $p['id'] ?>)">
                        <i class="fas fa-times-circle"></i> Tolak
                    </button>
                    <?php if (!empty($p['no_hp'])): ?>
                    <a href="https://wa.me/62<?= ltrim($p['no_hp'],'0') ?>?text=Halo+<?= urlencode($p['nama_penyelenggara']) ?>%2C+terkait+reservasi+<?= urlencode($p['nama_kegiatan']) ?>" target="_blank" class="btn btn-gold" style="width:100%;">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    <?php endif; ?>
</div>

<div class="dash-section" id="section-semua-reservasi">
    <div class="page-header-dash">
        <div><h1>Semua Reservasi</h1><p>Kelola seluruh data reservasi dari semua pengguna</p></div>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;align-items:center;">
        <button class="btn btn-primary btn-sm all-filter active" data-filter="all" onclick="filterAll(this,'all')">Semua</button>
        <button class="btn btn-sm all-filter" style="background:rgba(243,156,18,0.1);color:#d4870a;border:1px solid rgba(243,156,18,0.3);" data-filter="pending" onclick="filterAll(this,'pending')">Pending</button>
        <button class="btn btn-sm all-filter" style="background:rgba(39,174,96,0.1);color:#1e8449;border:1px solid rgba(39,174,96,0.3);" data-filter="confirmed" onclick="filterAll(this,'confirmed')">Dikonfirmasi</button>
        <button class="btn btn-sm all-filter" style="background:rgba(142,68,173,0.1);color:#7d3c98;border:1px solid rgba(142,68,173,0.3);" data-filter="ditolak" onclick="filterAll(this,'ditolak')">Ditolak</button>
        <button class="btn btn-sm all-filter" style="background:rgba(231,76,60,0.1);color:#c0392b;border:1px solid rgba(231,76,60,0.3);" data-filter="dibatalkan" onclick="filterAll(this,'dibatalkan')">Dibatalkan</button>
    </div>

    <div class="dash-card">
        <div style="overflow-x:auto;">
            <table class="data-table" id="allResTable">
                <thead>
                    <tr><th>#</th><th>Kegiatan</th><th>Penyelenggara</th><th>Tanggal & Waktu</th><th>Lokasi</th><th>Peserta</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    mysqli_data_seek($all_res, 0);
                    while ($r = mysqli_fetch_assoc($all_res)):
                    ?>
                    <tr data-status="<?= $r['status'] ?>">
                        <td style="color:var(--text-light);font-weight:600;"><?= $no++ ?></td>
                        <td>
                            <div style="font-weight:700;color:var(--text-dark);max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($r['nama_kegiatan']) ?></div>
                            <div style="font-size:0.7rem;color:var(--text-light);"><?= $r['kategori'] ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($r['nama_lengkap'] ?? $r['nama_penyelenggara']) ?></div>
                            <div style="font-size:0.7rem;color:var(--text-light);"><?= htmlspecialchars($r['no_hp'] ?? '') ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;"><?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                            <div style="font-size:0.7rem;color:var(--text-light);"><?= $r['jam_mulai'] ?> – <?= $r['jam_selesai'] ?></div>
                        </td>
                        <td><?= htmlspecialchars($r['lokasi']) ?></td>
                        <td><?= $r['jumlah_peserta'] ?> org</td>
                        <td>
                            <span class="badge badge-<?= $r['status'] === 'confirmed' ? 'confirmed' : ($r['status'] === 'dibatalkan' ? 'cancelled' : ($r['status'] === 'ditolak' ? 'rejected' : 'pending')) ?>">
                                <?= $r['status'] === 'confirmed' ? '✓ Dikonfirmasi' : ($r['status'] === 'dibatalkan' ? '✗ Dibatalkan' : ($r['status'] === 'ditolak' ? '✗ Ditolak' : '⏳ Pending')) ?>
                            </span>
                            <?php if (!empty($r['catatan_admin'])): ?>
                            <div style="font-size:0.68rem;color:var(--text-light);margin-top:3px;font-style:italic;" title="<?= htmlspecialchars($r['catatan_admin']) ?>">💬 <?= substr($r['catatan_admin'],0,30) ?>...</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                <?php if ($r['status'] === 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="approve_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="catatan_admin" value="Dikonfirmasi oleh admin.">
                                    <button type="submit" class="btn btn-xs btn-success" title="Konfirmasi"><i class="fas fa-check"></i></button>
                                </form>
                                <button class="btn btn-xs btn-danger" onclick="openRejectModal(<?= $r['id'] ?>)" title="Tolak"><i class="fas fa-times"></i></button>
                                <?php endif; ?>
                                <?php if ($r['status'] === 'confirmed' || $r['status'] === 'ditolak'): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Kembalikan ke pending?')">
                                    <input type="hidden" name="pending_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-xs btn-warning" title="Set Pending"><i class="fas fa-undo"></i></button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus permanen reservasi ini?')">
                                    <input type="hidden" name="delete_res_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-xs" style="background:rgba(231,76,60,0.1);color:#c0392b;border:1px solid rgba(231,76,60,0.3);" title="Hapus Permanen"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dash-section" id="section-pengguna">
    <div class="page-header-dash">
        <div><h1>Data Pengguna</h1><p>Daftar semua pengguna terdaftar</p></div>
    </div>
    <div class="dash-card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Nama</th><th>Username</th><th>Email</th><th>No. HP</th><th>Total Reservasi</th><th>Bergabung</th></tr>
                </thead>
                <tbody>
                    <?php $no=1; mysqli_data_seek($all_users,0); while ($u = mysqli_fetch_assoc($all_users)): ?>
                    <tr>
                        <td style="color:var(--text-light);font-weight:600;"><?= $no++ ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-family:var(--font-display);font-size:0.85rem;flex-shrink:0;"><?= strtoupper(substr($u['username'],0,1)) ?></div>
                                <div style="font-weight:700;color:var(--text-dark);"><?= htmlspecialchars($u['nama_lengkap'] ?? $u['username']) ?></div>
                            </div>
                        </td>
                        <td style="font-family:monospace;font-size:0.85rem;">@<?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['no_hp'] ?? '-') ?></td>
                        <td>
                            <span style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:var(--primary);"><?= $u['total_res'] ?></span>
                            <span style="font-size:0.75rem;color:var(--text-light);"> reservasi</span>
                        </td>
                        <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dash-section" id="section-laporan">
    <div class="page-header-dash"><div><h1>Laporan & Statistik</h1><p>Ringkasan data dan analitik reservasi</p></div></div>
    <div class="grid-2">
        <div class="dash-card">
            <div class="card-header"><div class="card-title"><i class="fas fa-chart-bar"></i> Reservasi per Bulan (<?= date('Y') ?>)</div></div>
            <div class="card-body"><div class="chart-container" style="height:260px;"><canvas id="barChart"></canvas></div></div>
        </div>
        <div class="dash-card">
            <div class="card-header"><div class="card-title"><i class="fas fa-chart-pie"></i> Distribusi Status</div></div>
            <div class="card-body">
                <div class="chart-container" style="height:260px;"><canvas id="pieChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="dash-card">
        <div class="card-header"><div class="card-title"><i class="fas fa-table"></i> Ringkasan Per Lokasi</div></div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead><tr><th>Lokasi</th><th>Total Reservasi</th><th>Dikonfirmasi</th><th>Pending</th><th>Ditolak/Dibatalkan</th></tr></thead>
                <tbody>
                    <?php
                    $lokasi_list = ['Aula Utama','Pendopo','Ruang Serbaguna','Panggung Terbuka','Ruang Pertemuan'];
                    foreach ($lokasi_list as $lok):
                        $t = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) c FROM kegiatan WHERE lokasi='$lok'"))['c'];
                        $c = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) c FROM kegiatan WHERE lokasi='$lok' AND status='confirmed'"))['c'];
                        $pe= mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) c FROM kegiatan WHERE lokasi='$lok' AND status='pending'"))['c'];
                        $x = $t - $c - $pe;
                    ?>
                    <tr>
                        <td style="font-weight:700;"><?= $lok ?></td>
                        <td><b style="color:var(--primary);font-family:var(--font-display);"><?= $t ?></b></td>
                        <td><span class="badge badge-confirmed"><?= $c ?></span></td>
                        <td><span class="badge badge-pending"><?= $pe ?></span></td>
                        <td><span class="badge badge-cancelled"><?= $x ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</main>

<div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:48px;height:48px;border-radius:50%;background:rgba(231,76,60,0.1);display:flex;align-items:center;justify-content:center;color:#e74c3c;font-size:1.3rem;"><i class="fas fa-times-circle"></i></div>
            <div>
                <div class="modal-title">Tolak Reservasi</div>
                <div class="modal-subtitle">Berikan alasan penolakan kepada pemohon</div>
            </div>
        </div>
        <form method="POST">
            <input type="hidden" name="reject_id" id="rejectId">
            <div class="form-group">
                <label>Catatan / Alasan Penolakan</label>
                <textarea name="catatan_admin" class="form-control" placeholder="Contoh: Jadwal sudah terisi untuk tanggal tersebut. Silakan ajukan ulang dengan tanggal/lokasi lain." required></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                <button type="button" class="btn btn-outline" onclick="closeModal()"><i class="fas fa-arrow-left"></i> Batal</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-times-circle"></i> Ya, Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
const chartData = <?= json_encode($chart_data) ?>;
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

new Chart(document.getElementById('reservasiChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Reservasi',
            data: chartData,
            borderColor: '#8B2E00',
            backgroundColor: 'rgba(139,46,0,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#C9890A',
            pointRadius: 4,
            tension: 0.4,
            fill: true
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,grid:{color:'rgba(0,0,0,0.05)'}}, x:{grid:{display:false}} } }
});

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Jumlah Reservasi',
            data: chartData,
            backgroundColor: 'rgba(139,46,0,0.75)',
            borderColor: '#8B2E00',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,grid:{color:'rgba(0,0,0,0.05)'}}, x:{grid:{display:false}} } }
});

new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Dikonfirmasi','Pending','Ditolak','Dibatalkan'],
        datasets: [{
            data: [<?= $confirmed_res ?>, <?= $pending_res ?>, <?= $rejected_res ?>, <?= $cancelled_res ?>],
            backgroundColor: ['#27ae60','#f39c12','#8e44ad','#e74c3c'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{ padding:16, font:{ size:12, family:'Nunito', weight:'700' } } } } }
});

function toggleTopbarDropdown() {
    const dd = document.getElementById('topbarDropdown');
    const ch = document.getElementById('topbarChevron');
    dd.classList.toggle('show');
    ch.style.transform = dd.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0)';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#topbarProfileWrap')) {
        const dd = document.getElementById('topbarDropdown');
        const ch = document.getElementById('topbarChevron');
        if (dd) { dd.classList.remove('show'); ch.style.transform = 'rotate(0)'; }
    }
});

function showSection(id, el) {
    document.querySelectorAll('.dash-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
    const sec = document.getElementById('section-' + id);
    if (sec) sec.classList.add('active');
    if (el) el.classList.add('active');
    else document.querySelectorAll('.sidebar-link').forEach(l => { if (l.dataset.section === id) l.classList.add('active'); });
    const titles = {
        overview:'Overview Dashboard',
        pending:'Reservasi Pending',
        'semua-reservasi':'Semua Reservasi',
        pengguna:'Data Pengguna',
        laporan:'Laporan & Statistik'
    };
    document.getElementById('topbarTitle').textContent = titles[id] || 'Dashboard';
    return false;
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}

function openRejectModal(id) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectModal').classList.add('active');
}

function closeModal() {
    document.getElementById('rejectModal').classList.remove('active');
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function filterAll(btn, status) {
    document.querySelectorAll('.all-filter').forEach(b => { b.classList.remove('btn-primary'); });
    btn.classList.add('btn-primary');
    document.querySelectorAll('#allResTable tbody tr').forEach(tr => {
        tr.style.display = (status === 'all' || tr.dataset.status === status) ? '' : 'none';
    });
}
</script>
</body>
</html>