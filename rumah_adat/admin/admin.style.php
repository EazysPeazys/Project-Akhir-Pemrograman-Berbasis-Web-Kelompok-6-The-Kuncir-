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
.sidebar-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;font-size:.82rem;font-weight:600;color:rgba(255,255,255,.65)!important;transition:all .25s;margin-bottom:2px;cursor:pointer;text-decoration:none!important;}
.sidebar-link:link,.sidebar-link:visited{color:rgba(255,255,255,.65)!important;text-decoration:none!important;}
.sidebar-link i{width:18px;text-align:center;font-size:.9rem;color:rgba(201,137,10,.7)!important;transition:color .25s;}
.sidebar-link:hover{background:rgba(201,137,10,.1)!important;color:white!important;}
.sidebar-link:hover i{color:#C9890A!important;}
.sidebar-link.active{background:linear-gradient(135deg,#8B2E00,#B84A1A)!important;color:white!important;box-shadow:0 4px 15px rgba(139,46,0,.3);}
.sidebar-link.active i{color:#fff!important;}
.sidebar-link.danger{color:rgba(231,76,60,.75)!important;}
.sidebar-link.danger:hover{background:rgba(231,76,60,.12)!important;color:#e74c3c!important;}
.sidebar-spacer{flex:1;}
.sidebar-bottom{padding:12px 8px 16px;border-top:1px solid rgba(201,137,10,.1);}
.sb-w{--sb-w:260px;}
</style>