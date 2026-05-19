<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lectura | Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
    @include('partials.admin-styles')
</head>
<body>
<div class="app">

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura">
        <div>
            <strong>Lectura</strong>
            <em>Administrateur</em>
        </div>
    </div>

    <div class="admin-badge">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        {{ $admin->name }}
    </div>

    <div class="nav-label">Navigation</div>
    <a class="nav-item active" href="{{ route('admin.dashboard', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Vue globale
    </a>
    <a class="nav-item" href="{{ route('admin.books.create', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Ajouter un livre
        <span class="nav-count">+</span>
    </a>
    <a class="nav-item" href="{{ route('reader.index', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Bibliothèque
    </a>
    <a class="nav-item" href="{{ route('profile.edit', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Mon profil
    </a>

    <div class="nav-label">Métriques</div>
    <div class="nav-item" style="cursor:default;flex-direction:column;align-items:flex-start;gap:2px;padding:10px 11px">
        <span style="color:var(--muted);font-size:.72rem">Utilisateurs actifs</span>
        <span style="font-size:1rem;font-weight:700;color:var(--text)">{{ $stats['connected_users'] }} / {{ $stats['total_users'] }}</span>
    </div>
    <div class="nav-item" style="cursor:default;flex-direction:column;align-items:flex-start;gap:2px;padding:10px 11px">
        <span style="color:var(--muted);font-size:.72rem">Livres publiés</span>
        <span style="font-size:1rem;font-weight:700;color:var(--text)">{{ $stats['published_books'] }} / {{ $stats['total_books'] }}</span>
    </div>
    <div class="nav-item" style="cursor:default;flex-direction:column;align-items:flex-start;gap:2px;padding:10px 11px">
        <span style="color:var(--muted);font-size:.72rem">Lectures actives</span>
        <span style="font-size:1rem;font-weight:700;color:var(--text)">{{ $stats['active_readings'] }}</span>
    </div>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout', [], false) }}">
            @csrf
            <button class="nav-item" style="width:100%;color:var(--muted)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Déconnexion
            </button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">

    {{-- TOPBAR --}}
    <div class="topbar" data-reveal>
        <div style="display:flex;align-items:center;gap:12px">
            <button onclick="document.getElementById('sidebar').classList.toggle('open')" id="menu-btn" style="display:none;background:rgba(255,255,255,.07);border:1px solid var(--border);border-radius:10px;padding:8px;color:var(--text)">☰</button>
            <span class="topbar-title">Tableau de bord</span>
            <div style="display:flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;background:rgba(95,206,160,.1);border:1px solid rgba(95,206,160,.2);font-size:0.7rem;color:var(--green);font-weight:600;text-transform:uppercase;letter-spacing:0.05em">
                <span class="online-dot"></span> Temps réel
            </div>
        </div>
        <div class="topbar-right">
            <a class="btn btn-primary" href="{{ route('admin.books.create', [], false) }}">+ Nouveau livre</a>
            <a class="btn btn-ghost" href="{{ route('reader.index', [], false) }}">Bibliothèque</a>
        </div>
    </div>

    @include('partials.flash-messages')

    @if(!$insights['session_tracking_enabled'])
    <div class="notice" data-reveal>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div><strong>Suivi temps réel désactivé.</strong> Activez <code>SESSION_DRIVER=database</code> pour voir les connexions en direct (actuel : <code>{{ $insights['session_driver'] }}</code>).</div>
    </div>
    @endif

    {{-- STATS --}}
    <div class="stats-row" data-reveal>
        <div class="stat-card blue">
            <div class="stat-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-val">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Comptes totaux</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-val" id="active-users-stat">{{ $stats['connected_users'] }}</div>
            <div class="stat-label">Utilisateurs actifs</div>
        </div>
        <div class="stat-card accent">
            <div class="stat-icon accent">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div class="stat-val">{{ $stats['published_books'] }}</div>
            <div class="stat-label">Livres publiés</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="stat-val">{{ $stats['active_readings'] }}</div>
            <div class="stat-label">Lectures actives</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div class="stat-val">{{ $stats['favorites'] }}</div>
            <div class="stat-label">Favoris</div>
        </div>
        <div class="stat-card accent">
            <div class="stat-icon accent">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="stat-val">{{ $stats['reviews'] }}</div>
            <div class="stat-label">Avis lecteurs</div>
        </div>
        <div class="stat-card red">
            <div class="stat-icon red">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div class="stat-val">{{ $stats['total_books'] - $stats['published_books'] }}</div>
            <div class="stat-label">Brouillons</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div class="stat-val">{{ $stats['total_admins'] }}</div>
            <div class="stat-label">Administrateurs</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon green">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="4" height="7"/><rect x="10" y="3" width="4" height="12"/><rect x="17" y="3" width="4" height="5"/></svg>
            </div>
            <div class="stat-val">{{ $stats['wishlists'] }}</div>
            <div class="stat-label">Listes de lecture</div>
        </div>
    </div>

    {{-- ANALYTICS --}}
    <div class="analytics-grid" data-reveal>
        {{-- Métriques opérationnelles --}}
        <div class="card">
            <div class="card-head">
                <h2>Vue opérationnelle</h2>
                <p>Indicateurs de santé de la plateforme</p>
            </div>
            <div class="card-body">
                <div class="metric-list">
                    <div class="metric-item">
                        <div class="metric-row"><span>Taux d'utilisateurs actifs</span><strong>{{ $insights['connected_rate'] }}%</strong></div>
                        <div class="meter"><div class="meter-fill" style="width:{{ max(4,min(100,$insights['connected_rate'])) }}%"></div></div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-row"><span>Couverture catalogue publié</span><strong>{{ $insights['published_rate'] }}%</strong></div>
                        <div class="meter"><div class="meter-fill" style="width:{{ max(4,min(100,$insights['published_rate'])) }}%"></div></div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-row"><span>Progression moyenne</span><strong>{{ number_format((float)$insights['average_reading_progress'],1) }}%</strong></div>
                        <div class="meter"><div class="meter-fill" style="width:{{ max(4,min(100,(int)round($insights['average_reading_progress']))) }}%"></div></div>
                    </div>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:14px">
                    <span class="badge badge-accent">{{ $insights['pending_books'] }} brouillon(s)</span>
                    <span class="badge badge-green">{{ $insights['recent_uploads'] }} ajout(s) / 30j</span>
                    <span class="badge badge-default">{{ $insights['completed_books'] }} termités</span>
                    <span class="badge badge-blue">{{ number_format((float)$insights['average_review_rating'],1) }}/5 note moy.</span>
                </div>
            </div>
        </div>

        {{-- Top lecteurs --}}
        <div class="card">
            <div class="card-head">
                <h2>Lecteurs engagés</h2>
                <p>Classement par engagement</p>
            </div>
            <div class="card-body">
                @forelse($topReaders as $tr)
                <div class="list-item">
                    <div class="item-row">
                        <div class="avatar" style="background:rgba(244,164,74,.12);color:var(--accent)">{{ strtoupper(substr($tr->name,0,2)) }}</div>
                        <div style="flex:1;min-width:0">
                            <div class="item-name">{{ $tr->name }}</div>
                            <div class="item-sub">{{ $tr->email }}</div>
                        </div>
                        <span class="badge badge-green">{{ $tr->engagement_score }}</span>
                    </div>
                    <div class="item-meta">
                        <span class="badge badge-default">{{ $tr->reading_history_count }} ouv.</span>
                        <span class="badge badge-default">{{ $tr->completed_books_count }} fini(s)</span>
                        <span class="badge badge-default">{{ $tr->favorites_count }} ♥</span>
                    </div>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.85rem;padding:8px 0">Aucun signal fort pour le moment.</p>
                @endforelse
            </div>
        </div>

        {{-- Formats catalogue --}}
        <div class="card">
            <div class="card-head">
                <h2>Catalogue & formats</h2>
                <p>Répartition par format de fichier</p>
            </div>
            <div class="card-body">
                @forelse($insights['formats'] as $format => $count)
                <div class="metric-item" style="margin-bottom:10px">
                    <div class="metric-row"><span>{{ strtoupper((string)$format) }}</span><strong>{{ $count }} titre(s)</strong></div>
                    <div class="meter"><div class="meter-fill" style="width:{{ $stats['published_books'] > 0 ? max(4,min(100,round($count/$stats['published_books']*100))) : 4 }}%"></div></div>
                </div>
                @empty
                <p style="color:var(--muted);font-size:.85rem">Aucun format détecté.</p>
                @endforelse
                <div style="margin-top:14px;font-size:.8rem;color:var(--muted)">{{ $insights['interaction_volume'] }} interactions totales</div>
            </div>
        </div>
    </div>

    {{-- LAYOUT PRINCIPAL --}}
    <div class="layout" data-reveal>

        {{-- Comptes utilisateurs --}}
        <div class="card">
            <div class="card-head" style="padding-bottom:14px">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
                    <div>
                        <h2>Comptes utilisateurs</h2>
                        <p>Gérez les rôles et sessions depuis cette vue</p>
                    </div>
                    <span class="badge badge-blue">{{ $users->count() }} comptes</span>
                </div>
                <div class="list-search" style="margin-top:12px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input id="user-search" placeholder="Filtrer par nom ou email…" oninput="filterUsers(this.value)">
                </div>
            </div>
            <div class="card-body" id="user-list">
                @foreach($users as $user)
                <div class="list-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                    <div class="item-row">
                        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0">
                            <div class="avatar" style="background:rgba({{ $user->role==='admin' ? '244,164,74' : '126,184,245' }},.12);color:var(--{{ $user->role==='admin' ? 'accent' : 'blue' }})">
                                {{ strtoupper(substr($user->name,0,2)) }}
                            </div>
                            <div style="min-width:0">
                                <div class="item-name" style="display:flex;align-items:center;gap:6px">
                                    {{ $user->name }}
                                    @if($user->is_connected)<span class="online-dot" title="Connecté"></span>@endif
                                </div>
                                <div class="item-sub">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div style="display:flex;gap:5px;flex-wrap:wrap;justify-content:flex-end">
                            <span class="badge {{ $user->role==='admin' ? 'badge-accent' : 'badge-blue' }}">{{ $user->role === 'admin' ? 'Administrateur' : 'Lecteur' }}</span>
                            <span class="badge {{ $user->is_connected ? 'badge-green' : 'badge-default' }}">{{ $user->is_connected ? 'en ligne' : 'hors ligne' }}</span>
                        </div>
                    </div>
                    <div class="item-meta">
                        <span class="badge badge-default">inscrit {{ $user->created_at?->diffForHumans() ?? 'récemment' }}</span>
                        <span class="badge badge-default">vu {{ $user->last_seen_human }}</span>
                        <span class="badge badge-default">{{ $user->reading_history_count }} ouv.</span>
                        <span class="badge badge-default">{{ $user->completed_books_count }} terminé(s)</span>
                        <span class="badge badge-default">{{ $user->favorites_count }} ♥</span>
                        <span class="badge badge-default">{{ $user->wishlists_count }} liste</span>
                        @if($user->ip_address)<span class="badge badge-default">{{ $user->ip_address }}</span>@endif
                    </div>
                    <div class="item-actions">
                        <form method="POST" action="{{ route('admin.users.role', $user) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="role" value="{{ $user->role==='admin' ? 'reader' : 'admin' }}">
                            <button class="btn btn-ghost btn-sm">{{ $user->role==='admin' ? 'Retirer admin' : 'Rendre admin' }}</button>
                        </form>
                        @if($user->is_connected)
                        <form method="POST" action="{{ route('admin.users.disconnect', $user) }}">
                            @csrf
                            <button class="btn btn-ghost btn-sm">Déconnecter</button>
                        </form>
                        @endif
                        @if($user->id !== $admin->id)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Supprimer ce compte ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="stack">

            {{-- Lecteurs connectés --}}
            <div class="card">
                <div class="card-head">
                    <h2>Lecteurs connectés</h2>
                    <p>Sessions actives récentes</p>
                </div>
                <div class="card-body" id="connected-readers">
                    @forelse($connectedReaders as $cr)
                    <div class="list-item">
                        <div class="item-row">
                            <div class="avatar" style="background:rgba(95,206,160,.12);color:var(--green)">{{ strtoupper(substr($cr->name,0,2)) }}</div>
                            <div style="flex:1;min-width:0">
                                <div class="item-name" style="display:flex;align-items:center;gap:6px">
                                    <span class="online-dot"></span>{{ $cr->name }}
                                </div>
                                <div class="item-sub">{{ $cr->email }}</div>
                            </div>
                        </div>
                        <div class="item-meta">
                            <span class="badge badge-green">actif {{ $cr->last_seen_human }}</span>
                            <span class="badge badge-default">{{ $cr->sessions_count }} session(s)</span>
                            @if($cr->ip_address)<span class="badge badge-default">{{ $cr->ip_address }}</span>@endif
                        </div>
                    </div>
                    @empty
                    <p style="color:var(--muted);font-size:.85rem;padding:8px 0">Aucun lecteur actif en ce moment.</p>
                    @endforelse
                </div>
            </div>

            {{-- Activité récente (timeline) --}}
            <div class="card">
                <div class="card-head">
                    <h2>Activité récente</h2>
                    <p>Dernières ouvertures de livres</p>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($recentHistory as $entry)
                        <div class="tl-item">
                            <div class="tl-dot">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            </div>
                            <div class="tl-content">
                                <div class="tl-name">{{ $entry->user?->name ?? 'Lecteur' }}</div>
                                <div class="tl-detail">{{ $entry->book?->title ?? 'Livre indisponible' }} — {{ number_format((float)$entry->last_progress_percent,0) }}% lu</div>
                                <div class="tl-time">{{ $entry->last_opened_at?->diffForHumans() ?? 'récemment' }}</div>
                            </div>
                        </div>
                        @empty
                        <p style="color:var(--muted);font-size:.85rem">Aucune activité récente.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Livres récents --}}
            <div class="card">
                <div class="card-head">
                    <h2>Livres récents</h2>
                    <p>Derniers ajouts au catalogue</p>
                </div>
                <div class="card-body">
                    @forelse($recentBooks as $book)
                    <div class="list-item">
                        <div class="item-name">{{ $book->title }}</div>
                        <div class="item-sub">{{ $book->author?->name ?? 'Auteur inconnu' }}</div>
                        <div class="item-meta">
                            <span class="badge badge-default">{{ strtoupper($book->file_format) }}</span>
                            <span class="badge {{ $book->is_published ? 'badge-green' : 'badge-accent' }}">{{ $book->is_published ? 'publié' : 'brouillon' }}</span>
                        </div>
                        <div class="item-actions">
                            <a class="btn btn-ghost btn-sm" href="{{ route('reader.show', $book, false) }}">Ouvrir</a>
                            <a class="btn btn-ghost btn-sm" href="{{ route('admin.books.edit', $book, false) }}">Modifier</a>
                        </div>
                    </div>
                    @empty
                    <p style="color:var(--muted);font-size:.85rem">Aucun livre récent.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    @include('partials.app-footer')
</main>
</div>

<script>
// Sidebar mobile
const menuBtn = document.getElementById('menu-btn');
if(menuBtn){
    menuBtn.style.display = window.innerWidth <= 680 ? 'flex' : 'none';
    window.addEventListener('resize', () => { menuBtn.style.display = window.innerWidth <= 680 ? 'flex' : 'none'; });
}
document.addEventListener('click', e => {
    const sb = document.getElementById('sidebar');
    if(sb && sb.classList.contains('open') && !sb.contains(e.target) && !menuBtn?.contains(e.target)) sb.classList.remove('open');
});
// Reveal animations
const reveals = document.querySelectorAll('[data-reveal]');
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('visible'); obs.unobserve(e.target); }});
}, { threshold: 0.06 });
reveals.forEach((el, i) => { el.style.transitionDelay = Math.min(i*40,180)+'ms'; obs.observe(el); });
// Filter users
function filterUsers(q){
    q = q.toLowerCase();
    document.querySelectorAll('#user-list .list-item').forEach(item => {
        const match = (item.dataset.name||'').includes(q) || (item.dataset.email||'').includes(q);
        item.style.display = match ? '' : 'none';
    });
}

// Actualisation en temps réel silencieuse
setInterval(() => {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newUserList = doc.getElementById('user-list');
            const newConnectedReaders = doc.getElementById('connected-readers');
            const newActiveStat = doc.getElementById('active-users-stat');
            
            if (newUserList && newConnectedReaders && newActiveStat) {
                document.getElementById('user-list').innerHTML = newUserList.innerHTML;
                document.getElementById('connected-readers').innerHTML = newConnectedReaders.innerHTML;
                document.getElementById('active-users-stat').innerHTML = newActiveStat.innerHTML;
                
                // Réappliquer le filtre si existant
                const q = document.getElementById('user-search')?.value;
                if (q) filterUsers(q);
            }
        });
}, 10000);
</script>
</body>
</html>
