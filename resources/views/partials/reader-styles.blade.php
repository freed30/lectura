<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;0,800;1,700&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#05080f;
    --surface:rgba(12,20,38,0.75);
    --surface-2:rgba(18,28,52,0.6);
    --border:rgba(100,170,255,0.1);
    --border-hover:rgba(244,164,74,0.35);
    --text:#eef3ff;
    --text-2:#b8cde8;
    --muted:#607a99;
    --accent:#f4a44a;
    --accent-2:#ffd38b;
    --accent-glow:rgba(244,164,74,0.25);
    --green:#4dd89a;
    --purple:#a78bff;
    --red:#ff7070;
    --shadow:0 32px 80px rgba(0,0,0,.55);
    --radius:20px;
    --radius-sm:12px;
    font-family:'Inter',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
a{color:inherit;text-decoration:none}
button{font:inherit;cursor:pointer;border:none;background:none}
img{max-width:100%}

body{
    min-height:100vh;
    color:var(--text);
    background:
        radial-gradient(ellipse at 10% -5%,rgba(120,190,255,.12),transparent 45%),
        radial-gradient(ellipse at 90% 5%,rgba(167,139,255,.1),transparent 40%),
        radial-gradient(ellipse at 50% 100%,rgba(244,164,74,.06),transparent 50%),
        linear-gradient(180deg,#030710 0%,#060d1a 60%,#040a14 100%);
    overflow-x:hidden;
}

/* ─── LAYOUT ─── */
.app{display:flex;min-height:100vh}
.sidebar{
    position:fixed;top:0;left:0;width:268px;height:100vh;
    overflow-y:auto;padding:20px 14px;
    display:flex;flex-direction:column;gap:4px;
    background:linear-gradient(180deg,rgba(6,12,26,.97) 0%,rgba(4,9,20,.99) 100%);
    border-right:1px solid var(--border);
    z-index:100;scrollbar-width:none;
}
.sidebar::-webkit-scrollbar{display:none}
.main{margin-left:268px;flex:1;padding:30px 36px 80px;max-width:calc(100vw - 268px)}

/* ─── BRAND ─── */
.brand{display:flex;align-items:center;gap:10px;padding:6px 8px 18px;border-bottom:1px solid var(--border);margin-bottom:6px}
.brand img{width:40px;height:40px;border-radius:14px;object-fit:cover;box-shadow:0 0 20px rgba(244,164,74,.3)}
.brand strong{font-size:.95rem;font-family:'Playfair Display',serif;background:linear-gradient(90deg,var(--text),var(--accent-2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.brand span{display:block;color:var(--muted);font-size:.7rem;margin-top:1px;-webkit-text-fill-color:var(--muted)}

/* ─── READER CARD ─── */
.reader-card{
    padding:14px 16px;border-radius:16px;
    background:linear-gradient(135deg,rgba(244,164,74,.08),rgba(167,139,255,.06));
    border:1px solid rgba(244,164,74,.18);
    margin:4px 0 10px;
    position:relative;overflow:hidden;
}
.reader-card::before{
    content:'';position:absolute;inset:-1px;border-radius:inherit;
    background:linear-gradient(135deg,rgba(244,164,74,.15),transparent 60%);
    pointer-events:none;
}
.reader-card .rc-avatar{
    width:38px;height:38px;border-radius:50%;
    background:linear-gradient(135deg,var(--accent),var(--purple));
    display:flex;align-items:center;justify-content:center;
    font-weight:700;font-size:1rem;color:#1a0d00;
    margin-bottom:10px;flex-shrink:0;
}
.reader-card .rc-info{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.reader-card .rname{font-weight:700;font-size:.9rem}
.reader-card .rrole{color:var(--accent);font-size:.7rem;font-weight:500;letter-spacing:.04em}
.reader-card .rc-stats{display:flex;gap:12px}
.reader-card .rcs{text-align:center}
.reader-card .rcs strong{display:block;font-size:.95rem;font-weight:700;color:var(--accent-2)}
.reader-card .rcs span{font-size:.62rem;color:var(--muted)}

/* ─── NAV ─── */
.nav-section{font-size:.65rem;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);padding:12px 10px 4px;font-weight:600}
.nav-item{
    display:flex;align-items:center;gap:9px;
    padding:9px 12px;border-radius:12px;
    color:var(--muted);font-size:.845rem;
    transition:all .2s ease;position:relative;
}
.nav-item:hover{background:rgba(255,255,255,.05);color:var(--text);padding-left:16px}
.nav-item.active{
    background:linear-gradient(90deg,rgba(244,164,74,.1),rgba(244,164,74,.04));
    color:var(--accent-2);border-left:2px solid var(--accent);padding-left:10px;
}
.nav-item svg{width:15px;height:15px;flex-shrink:0}

/* ─── SIDEBAR STATS ─── */
.sidebar-stat{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin:8px 0}
.ss-item{
    padding:10px 8px;border-radius:12px;
    background:rgba(255,255,255,.03);border:1px solid var(--border);
    text-align:center;
}
.ss-item strong{display:block;font-size:1.05rem;font-weight:800;color:var(--text)}
.ss-item span{color:var(--muted);font-size:.65rem;margin-top:1px;display:block}

/* ─── GENRE PROFILE ─── */
.genre-profile{margin:6px 0}
.gp-item{margin-bottom:9px}
.gp-label{display:flex;justify-content:space-between;font-size:.73rem;margin-bottom:4px}
.gp-label span:first-child{color:var(--text-2)}
.gp-label span:last-child{color:var(--accent);font-weight:600}
.gp-bar{height:5px;border-radius:999px;background:rgba(255,255,255,.06);overflow:hidden}
.gp-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--accent),var(--accent-2));transition:width 1.2s cubic-bezier(.34,1.56,.64,1)}

.sidebar-nav-bottom{margin-top:auto;padding-top:14px;border-top:1px solid var(--border)}

/* ─── TOPBAR ─── */
.topbar{
    display:flex;align-items:center;justify-content:space-between;gap:16px;
    margin-bottom:32px;padding:14px 22px;
    border-radius:var(--radius);
    background:var(--surface);
    border:1px solid var(--border);
    backdrop-filter:blur(32px);
    -webkit-backdrop-filter:blur(32px);
    position:sticky;top:20px;z-index:50;
}
.topbar h1{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;white-space:nowrap}
.topbar-right{display:flex;align-items:center;gap:10px}

/* ─── BUTTONS ─── */
.btn{
    display:inline-flex;align-items:center;justify-content:center;gap:6px;
    padding:0 20px;height:42px;border-radius:999px;
    font-size:.875rem;font-weight:600;
    transition:all .2s ease;border:1px solid transparent;
    white-space:nowrap;
}
.btn-primary{
    background:linear-gradient(90deg,var(--accent),var(--accent-2));
    color:#1a0d00;font-weight:700;
    box-shadow:0 4px 16px rgba(244,164,74,.2);
}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(244,164,74,.4)}
.btn-ghost{background:rgba(255,255,255,.06);border-color:var(--border);color:var(--text)}
.btn-ghost:hover{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.15);transform:translateY(-1px)}
.btn-sm{height:34px;padding:0 14px;font-size:.8rem}
.btn-icon{width:36px;height:36px;padding:0;border-radius:50%}

/* ─── SEARCH ─── */
.search-box{
    display:flex;align-items:center;gap:10px;
    padding:10px 16px;border-radius:14px;
    background:rgba(255,255,255,.05);
    border:1px solid var(--border);
    transition:border-color .2s,background .2s;
    min-width:240px;
}
.search-box:focus-within{border-color:rgba(244,164,74,.3);background:rgba(255,255,255,.07)}
.search-box input{flex:1;background:none;border:none;color:var(--text);font:inherit;font-size:.875rem;outline:none}
.search-box input::placeholder{color:var(--muted)}

/* ─── GENRE PILLS ─── */
.genre-pills{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:24px}
.genre-pill{
    padding:6px 16px;border-radius:999px;
    background:rgba(255,255,255,.04);
    border:1px solid var(--border);
    color:var(--muted);font-size:.8rem;cursor:pointer;
    transition:all .18s ease;
}
.genre-pill:hover{background:rgba(244,164,74,.08);border-color:rgba(244,164,74,.22);color:var(--accent-2)}
.genre-pill.active{background:rgba(244,164,74,.12);border-color:rgba(244,164,74,.35);color:var(--accent-2);font-weight:600}

/* ─── BADGES ─── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:.7rem;font-weight:600;border:1px solid transparent}
.badge-default{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.08);color:var(--muted)}
.badge-accent{background:rgba(244,164,74,.12);border-color:rgba(244,164,74,.22);color:var(--accent-2)}
.badge-green{background:rgba(77,216,154,.1);border-color:rgba(77,216,154,.2);color:var(--green)}
.badge-purple{background:rgba(167,139,255,.1);border-color:rgba(167,139,255,.2);color:var(--purple)}
.badge-ai{background:linear-gradient(90deg,rgba(167,139,255,.12),rgba(244,164,74,.08));border-color:rgba(167,139,255,.22);color:var(--purple)}

/* ─── CARDS ─── */
.card{
    background:linear-gradient(160deg,rgba(13,24,46,.88),rgba(6,12,28,.94));
    border:1px solid var(--border);border-radius:var(--radius);
    overflow:hidden;
    transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease;
    display:flex;flex-direction:column;
}
.card:hover{
    transform:translateY(-6px);
    border-color:rgba(244,164,74,.22);
    box-shadow:0 32px 72px rgba(0,0,0,.5),0 0 0 1px rgba(244,164,74,.06);
}
.card-cover{
    position:relative;min-height:210px;
    background:linear-gradient(145deg,#142040,#080f24);
    background-size:cover;background-position:center;
    display:flex;align-items:flex-end;padding:14px;
    overflow:hidden;
}
.card-cover::after{
    content:'';position:absolute;inset:0;
    background:linear-gradient(180deg,transparent 30%,rgba(4,9,22,.92));
    pointer-events:none;
}
.card-cover-title{
    position:relative;z-index:1;
    font-family:'Playfair Display',serif;
    font-size:1.05rem;line-height:1.2;
    font-weight:700;
    text-shadow:0 2px 12px rgba(0,0,0,.7);
}
.card-ribbon{
    position:absolute;top:12px;right:12px;z-index:2;
    padding:4px 10px;border-radius:999px;
    font-size:.68rem;font-weight:700;
    backdrop-filter:blur(8px);
}
.card-body{padding:14px 16px;flex:1;display:flex;flex-direction:column;gap:6px}
.card-meta{display:flex;flex-wrap:wrap;gap:5px}
.card-title{font-size:.95rem;font-weight:700;line-height:1.3;color:var(--text)}
.card-author{font-size:.78rem;color:var(--muted)}
.card-desc{font-size:.78rem;color:var(--text-2);line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1}

.progress-wrap{height:4px;border-radius:999px;background:rgba(255,255,255,.07);overflow:hidden;margin-top:auto}
.progress-bar{
    height:100%;border-radius:999px;
    background:linear-gradient(90deg,var(--accent),var(--accent-2));
    transition:width 1.2s cubic-bezier(.34,1.56,.64,1);
    position:relative;
}
.progress-bar::after{
    content:'';position:absolute;right:0;top:50%;transform:translateY(-50%);
    width:7px;height:7px;border-radius:50%;
    background:var(--accent-2);
    box-shadow:0 0 8px var(--accent);
}
.progress-label{font-size:.73rem;color:var(--green);font-weight:600}

.card-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;padding-top:10px;border-top:1px solid var(--border);margin-top:auto}
.card-actions{display:flex;gap:5px;flex-wrap:wrap}
.card-actions form{margin:0}

/* ─── GRIDS ─── */
.grid-4{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:20px}
.grid-3{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:20px}

/* ─── SECTION HEAD ─── */
.section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin:40px 0 20px}
.section-head h2{font-family:'Playfair Display',serif;font-size:1.65rem;font-weight:800;line-height:1}
.section-head p{color:var(--muted);font-size:.83rem;margin-top:5px}
.section-note{padding:5px 14px;border-radius:999px;background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--muted);font-size:.78rem;white-space:nowrap}

/* ─── HERO ─── */
.hero-banner{
    position:relative;overflow:hidden;padding:40px;
    border-radius:28px;
    background:linear-gradient(145deg,rgba(10,20,42,.95),rgba(5,10,24,.98));
    border:1px solid var(--border);margin-bottom:32px;
}
.hero-banner::before{
    content:'';position:absolute;inset:-1px;border-radius:inherit;
    background:linear-gradient(135deg,rgba(244,164,74,.06) 0%,transparent 40%,rgba(120,190,255,.04) 100%);
    pointer-events:none;
}
.hero-banner::after{
    content:'';position:absolute;bottom:-80px;left:-80px;
    width:350px;height:350px;border-radius:50%;
    background:radial-gradient(circle,rgba(167,139,255,.06),transparent 65%);
    pointer-events:none;
}
.hero-glow{position:absolute;width:420px;height:420px;top:-180px;right:-120px;border-radius:50%;background:radial-gradient(circle,rgba(244,164,74,.14),transparent 60%);pointer-events:none}
.hero-kicker{
    display:inline-flex;align-items:center;gap:7px;
    padding:6px 16px;border-radius:999px;
    background:rgba(255,255,255,.05);border:1px solid var(--border);
    color:var(--text-2);font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;font-weight:600;
    margin-bottom:16px;
}
.hero-kicker-dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 2s ease infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.85)}}
.hero-title{
    font-family:'Playfair Display',serif;
    font-size:clamp(2.2rem,4.5vw,3.6rem);
    line-height:.92;letter-spacing:-.03em;margin-bottom:14px;
    background:linear-gradient(135deg,var(--text) 0%,var(--accent-2) 80%);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.hero-sub{color:var(--text-2);font-size:.95rem;line-height:1.7;max-width:580px;margin-bottom:24px}
.hero-actions{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:28px}
.hero-stats{display:flex;gap:32px;padding-top:22px;border-top:1px solid var(--border)}
.hstat strong{display:block;font-size:1.5rem;font-weight:800;background:linear-gradient(90deg,var(--text),var(--accent-2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hstat span{color:var(--muted);font-size:.75rem;display:block;margin-top:2px}

/* ─── AI REASON ─── */
.ai-reason{
    display:flex;align-items:flex-start;gap:7px;
    font-size:.74rem;color:var(--purple);
    padding:7px 10px;border-radius:10px;
    background:rgba(167,139,255,.07);border:1px solid rgba(167,139,255,.14);
    margin:4px 0 6px;line-height:1.5;
}

/* ─── EMPTY STATE ─── */
.empty-state{
    padding:60px 40px;text-align:center;
    border:1px dashed rgba(244,164,74,.18);
    border-radius:var(--radius);color:var(--muted);
}
.empty-state p{margin-top:12px;line-height:1.7;font-size:.9rem}

/* ─── NOTIF ─── */
.notif-dot{
    display:inline-flex;align-items:center;justify-content:center;
    width:22px;height:22px;border-radius:50%;
    background:linear-gradient(135deg,var(--accent),#ff6b35);
    color:#fff;font-size:.65rem;font-weight:800;
    animation:bounce .8s ease infinite alternate;
}
@keyframes bounce{from{transform:scale(1)}to{transform:scale(1.18)}}

/* ─── SEARCH HISTORY ─── */
.search-history{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:20px}
.sh-tag{
    display:inline-flex;align-items:center;gap:5px;
    padding:5px 12px;border-radius:999px;
    background:rgba(255,255,255,.04);border:1px solid var(--border);
    color:var(--muted);font-size:.75rem;cursor:pointer;
    transition:all .15s;
}
.sh-tag:hover{background:rgba(244,164,74,.08);border-color:rgba(244,164,74,.2);color:var(--accent-2)}

/* ─── FLASH ─── */
.flash{padding:13px 18px;border-radius:14px;margin-bottom:18px;font-size:.875rem;border:1px solid transparent}
.flash-success{background:rgba(77,216,154,.08);border-color:rgba(77,216,154,.2);color:var(--green)}
.flash-error{background:rgba(255,112,112,.08);border-color:rgba(255,112,112,.2);color:var(--red)}

/* ─── PAGINATION ─── */
.pagination-wrapper { margin-top: 32px; display: flex; justify-content: center; width: 100%; }
.pagination-wrapper nav { width: 100%; display: flex; justify-content: center; flex-direction: column; align-items: center; gap: 12px; }
.pagination-wrapper ul.pagination { display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; justify-content: center; }
.pagination-wrapper .page-item .page-link {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 40px; height: 40px; padding: 0 12px; border-radius: 12px;
    background: rgba(255,255,255,.05); border: 1px solid var(--border);
    color: var(--muted); font-size: .95rem; font-weight: 600;
    transition: all .2s ease;
}
.pagination-wrapper .page-item .page-link:hover {
    background: rgba(244,164,74,.1); border-color: rgba(244,164,74,.3); color: var(--accent-2);
}
.pagination-wrapper .page-item.active .page-link,
.pagination-wrapper .page-item.active span.page-link {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    border-color: transparent; color: #1a0d00;
}
.pagination-wrapper .page-item.disabled .page-link,
.pagination-wrapper .page-item.disabled span.page-link {
    opacity: 0.4; pointer-events: none;
}
.pagination-wrapper p.small { color: var(--muted); font-size: 0.85rem; }

/* ─── SCROLL REVEAL ─── */
[data-reveal]{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease}
[data-reveal].visible{opacity:1;transform:translateY(0)}

/* ─── SCROLLBAR ─── */
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:999px}

/* ─── RESPONSIVE ─── */
@media(max-width:1024px){
    .sidebar{width:230px}.main{margin-left:230px;padding:22px 24px 60px;max-width:calc(100vw - 230px)}
}
@media(max-width:720px){
    .sidebar{position:fixed;left:-270px;width:268px;transition:left .3s cubic-bezier(.4,0,.2,1);z-index:200}
    .sidebar.open{left:0}
    .main{margin-left:0;max-width:100vw;padding:16px 16px 60px}
    .hero-banner{padding:24px}
    .hero-stats{flex-wrap:wrap;gap:16px}
    .topbar{position:relative;top:0;margin-bottom:20px}
    .topbar h1{font-size:1.2rem}
    .grid-4,.grid-3{grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px}
    .section-head{margin:28px 0 14px}
    .section-head h2{font-size:1.3rem}
}

/* ─── STATS STRIP ─── */
.stats-strip{
    display:flex;align-items:center;flex-wrap:wrap;gap:8px 20px;
    padding:14px 20px;border-radius:16px;
    background:var(--surface);border:1px solid var(--border);
    backdrop-filter:blur(20px);margin-bottom:24px;
}
.sstrip-item{display:flex;align-items:center;gap:8px}
.sstrip-item svg{flex-shrink:0;opacity:.8}
.sstrip-item strong{display:block;font-size:1rem;font-weight:800;line-height:1}
.sstrip-item span{display:block;font-size:.67rem;color:var(--muted);margin-top:1px}
.sstrip-sort{display:flex;align-items:center;flex-wrap:wrap;gap:5px}
.sort-btn{
    padding:5px 12px;border-radius:999px;font-size:.75rem;font-weight:500;
    background:rgba(255,255,255,.04);border:1px solid var(--border);
    color:var(--muted);cursor:pointer;transition:all .18s;
}
.sort-btn:hover{background:rgba(255,255,255,.08);color:var(--text)}
.sort-btn.active{background:rgba(244,164,74,.12);border-color:rgba(244,164,74,.3);color:var(--accent-2);font-weight:700}
</style>
