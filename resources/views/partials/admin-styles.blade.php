<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
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
    --green:#4dd89a;
    --purple:#a78bff;
    --red:#ff7070;
    --blue:#7eb8f5;
    --shadow:0 32px 80px rgba(0,0,0,.55);
    font-family:'Inter',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}a{color:inherit;text-decoration:none}button{font:inherit;cursor:pointer;border:none;background:none}
body{min-height:100vh;color:var(--text);background:radial-gradient(ellipse at 10% -5%,rgba(120,190,255,.12),transparent 45%),radial-gradient(ellipse at 90% 5%,rgba(167,139,255,.1),transparent 40%),radial-gradient(ellipse at 50% 100%,rgba(244,164,74,.06),transparent 50%),linear-gradient(180deg,#030710 0%,#060d1a 60%,#040a14 100%);overflow-x:hidden}
.app{display:flex;min-height:100vh}
.sidebar{position:fixed;top:0;left:0;width:256px;height:100vh;overflow-y:auto;padding:20px 14px;display:flex;flex-direction:column;gap:4px;background:linear-gradient(180deg,rgba(6,14,28,.97),rgba(3,8,18,.99));border-right:1px solid var(--border);z-index:100;scrollbar-width:none}
.sidebar::-webkit-scrollbar{display:none}
.main{margin-left:256px;flex:1;padding:28px 32px 60px;max-width:calc(100vw - 256px)}
.brand{display:flex;align-items:center;gap:10px;padding:6px 8px 18px;border-bottom:1px solid var(--border);margin-bottom:8px}
.brand img{width:36px;height:36px;border-radius:11px;object-fit:cover}
.brand strong{font-size:.92rem;font-family:'Playfair Display',serif}
.brand em{display:block;color:var(--accent);font-size:.68rem;font-style:normal;letter-spacing:.06em;text-transform:uppercase;margin-top:1px}
.admin-badge{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:12px;background:rgba(244,164,74,.08);border:1px solid rgba(244,164,74,.18);margin:4px 0 10px;font-size:.8rem;color:#ffd38b}
.nav-label{font-size:.66rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:10px 8px 4px}
.nav-item{display:flex;align-items:center;gap:9px;padding:9px 11px;border-radius:11px;color:var(--muted);font-size:.855rem;transition:all .18s;width:100%;text-align:left}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,.07);color:var(--text)}
.nav-item svg{width:15px;height:15px;flex-shrink:0}
.nav-item .nav-count{margin-left:auto;font-size:.7rem;padding:2px 7px;border-radius:999px;background:rgba(244,164,74,.15);color:var(--accent)}
.sidebar-bottom{margin-top:auto;padding-top:14px;border-top:1px solid var(--border)}
/* Topbar */
.topbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:24px;padding:14px 20px;border-radius:20px;background:var(--surface);border:1px solid var(--border);backdrop-filter:blur(24px)}
.topbar-title{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700}
.topbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
/* Buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:0 16px;height:38px;border-radius:999px;font-size:.845rem;font-weight:500;transition:all .18s;border:1px solid transparent;cursor:pointer}
.btn-primary{background:linear-gradient(90deg,var(--accent),#ffd38b);color:#1a0d00;font-weight:700}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(244,164,74,.3)}
.btn-ghost{background:rgba(255,255,255,.06);border-color:var(--border);color:var(--text)}
.btn-ghost:hover{background:rgba(255,255,255,.1)}
.btn-danger{background:rgba(255,138,128,.1);border-color:rgba(255,138,128,.25);color:var(--red)}
.btn-sm{height:32px;padding:0 12px;font-size:.78rem}
/* Stats grid */
.stats-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:22px}
.stat-card{padding:18px 16px;border-radius:18px;background:var(--surface);border:1px solid var(--border);backdrop-filter:blur(20px);position:relative;overflow:hidden;transition:transform .2s,border-color .2s}
.stat-card:hover{transform:translateY(-3px);border-color:rgba(244,164,74,.22)}
.stat-card::before{content:"";position:absolute;inset:0;opacity:.06;pointer-events:none}
.stat-card.blue::before{background:radial-gradient(circle at top right,var(--blue),transparent)}
.stat-card.green::before{background:radial-gradient(circle at top right,var(--green),transparent)}
.stat-card.accent::before{background:radial-gradient(circle at top right,var(--accent),transparent)}
.stat-card.red::before{background:radial-gradient(circle at top right,var(--red),transparent)}
.stat-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px}
.stat-icon.blue{background:rgba(126,184,245,.12);color:var(--blue)}
.stat-icon.green{background:rgba(95,206,160,.12);color:var(--green)}
.stat-icon.accent{background:rgba(244,164,74,.12);color:var(--accent)}
.stat-icon.red{background:rgba(255,138,128,.12);color:var(--red)}
.stat-val{font-size:1.7rem;font-weight:700;font-family:'Playfair Display',serif;line-height:1}
.stat-label{color:var(--muted);font-size:.77rem;margin-top:5px}
/* Cards */
.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;backdrop-filter:blur(20px)}
.card-head{padding:20px 22px 0}
.card-head h2{font-family:'Playfair Display',serif;font-size:1.25rem;margin-bottom:4px}
.card-head p{color:var(--muted);font-size:.8rem;line-height:1.5}
.card-body{padding:16px 22px 20px}
/* Metric bars */
.metric-list{display:grid;gap:14px}
.metric-item{}
.metric-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;font-size:.82rem}
.metric-row span{color:var(--muted)}
.meter{height:7px;border-radius:999px;background:rgba(255,255,255,.07);overflow:hidden}
.meter-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--accent),#ffd38b)}
/* Layout */
.layout{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(320px,.7fr);gap:20px}
.stack{display:grid;gap:16px;align-content:start}
/* List items */
.list-item{padding:14px 16px;border-radius:14px;border:1px solid var(--border);background:rgba(255,255,255,.03);margin-bottom:10px;transition:border-color .2s}
.list-item:hover{border-color:rgba(244,164,74,.2)}
.list-item:last-child{margin-bottom:0}
.item-row{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}
.item-name{font-weight:600;font-size:.9rem}
.item-sub{color:var(--muted);font-size:.77rem;margin-top:2px}
.item-meta{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
.item-actions{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
.item-actions form{margin:0;display:flex}
/* Badges */
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;font-size:.7rem;font-weight:500;border:1px solid transparent}
.badge-default{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.08);color:var(--muted)}
.badge-green{background:rgba(95,206,160,.1);border-color:rgba(95,206,160,.2);color:var(--green)}
.badge-accent{background:rgba(244,164,74,.1);border-color:rgba(244,164,74,.2);color:#ffd38b}
.badge-red{background:rgba(255,138,128,.1);border-color:rgba(255,138,128,.2);color:var(--red)}
.badge-blue{background:rgba(126,184,245,.1);border-color:rgba(126,184,245,.18);color:var(--blue)}
/* Avatar initials */
.avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem;flex-shrink:0;text-transform:uppercase}
/* Online dot */
.online-dot{width:8px;height:8px;border-radius:50%;background:var(--green);box-shadow:0 0 0 2px rgba(95,206,160,.3);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 2px rgba(95,206,160,.3)}50%{box-shadow:0 0 0 5px rgba(95,206,160,.1)}}
/* Notice */
.notice{display:flex;gap:8px;align-items:flex-start;padding:10px 14px;border-radius:12px;border:1px solid rgba(244,164,74,.2);background:rgba(244,164,74,.08);color:#ffd6a0;font-size:.8rem;line-height:1.5}
/* Section head */
.section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin:24px 0 16px}
.section-head h2{font-family:'Playfair Display',serif;font-size:1.4rem}
.section-head p{color:var(--muted);font-size:.8rem;margin-top:3px}
/* Search filter */
.list-search{display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:12px;background:rgba(255,255,255,.05);border:1px solid var(--border);margin-bottom:14px}
.list-search input{flex:1;background:none;border:none;color:var(--text);font:inherit;font-size:.85rem;outline:none}
.list-search input::placeholder{color:var(--muted)}
/* Analytics grid */
.analytics-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;margin-bottom:22px}
/* Timeline */
.timeline{display:grid;gap:0}
.tl-item{display:flex;gap:12px;padding-bottom:14px;position:relative}
.tl-item::before{content:"";position:absolute;left:17px;top:32px;bottom:0;width:1px;background:var(--border)}
.tl-item:last-child::before{display:none}
.tl-dot{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.05);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tl-content{flex:1}
.tl-name{font-size:.87rem;font-weight:600}
.tl-detail{font-size:.77rem;color:var(--muted);margin-top:2px}
.tl-time{font-size:.7rem;color:var(--muted);margin-top:4px}
/* Reveal */
[data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .5s ease,transform .5s ease}
[data-reveal].visible{opacity:1;transform:translateY(0)}
::-webkit-scrollbar{width:5px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:999px}
@media(max-width:1100px){.layout{grid-template-columns:1fr}}
@media(max-width:900px){.sidebar{width:220px}.main{margin-left:220px;padding:20px;max-width:calc(100vw - 220px)}}
@media(max-width:680px){.sidebar{position:fixed;left:-260px;width:256px;transition:left .3s;z-index:200}.sidebar.open{left:0}.main{margin-left:0;max-width:100vw;padding:16px}.stats-row{grid-template-columns:repeat(2,1fr)}}
</style>
