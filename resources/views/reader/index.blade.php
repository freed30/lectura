<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Lectura | Bibliothèque immersive</title>
<meta name="description" content="Votre bibliothèque numérique immersive — progression automatique, recommandations IA, lecture PDF & EPUB.">
<link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
@include('partials.reader-styles')
</head>
<body>
<div class="app">

{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="brand">
        <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura">
        <div>
            <strong>Lectura</strong>
            <span>Bibliothèque immersive</span>
        </div>
    </div>

    {{-- Reader card --}}
    @auth
    <div class="reader-card">
        <div class="rc-info">
            <div class="rc-avatar">{{ mb_strtoupper(mb_substr($readerName, 0, 1)) }}</div>
            <div>
                <div class="rname">{{ $readerName }}</div>
                <div class="rrole">✦ Lecteur actif</div>
            </div>
        </div>
        <div class="rc-stats">
            <div class="rcs">
                <strong>{{ $continueReading->count() }}</strong>
                <span>En cours</span>
            </div>
            <div class="rcs">
                <strong>{{ $recommendations->count() }}</strong>
                <span>Suggestions</span>
            </div>
            <div class="rcs">
                <strong>{{ $recentFavorites->count() }}</strong>
                <span>Favoris</span>
            </div>
        </div>
    </div>
    @endauth

    {{-- Navigation --}}
    <div class="nav-section">Navigation</div>
    <a class="nav-item {{ $filterType === '' ? 'active' : '' }}" href="{{ route('reader.index', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        Bibliothèque
    </a>
    @auth
    <a class="nav-item {{ $filterType === 'favorites' ? 'active' : '' }}" href="{{ route('reader.index', ['filter' => 'favorites'], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
        Mes favoris
    </a>
    <a class="nav-item {{ $filterType === 'wishlist' ? 'active' : '' }}" href="{{ route('reader.index', ['filter' => 'wishlist'], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        Ma liste d'envie
    </a>
    <a class="nav-item" href="{{ route('dashboard', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Mon espace
    </a>
    <a class="nav-item" href="{{ route('profile.edit', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Mon profil
    </a>
    @if(auth()->user()->isAdmin())
    <a class="nav-item" href="{{ route('admin.dashboard', [], false) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Administration
    </a>
    @endif
    @endauth

    {{-- Lectures en cours --}}
    @if($continueReading->isNotEmpty())
    <div class="nav-section">En cours ({{ $continueReading->count() }})</div>
    @foreach($continueReading->take(4) as $cr)
    @php $crp = (float)($cr->readingProgress->first()?->progress_percent ?? 0); @endphp
    <a class="nav-item" href="{{ route('reader.show', $cr, false) }}" title="{{ $cr->title }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;font-size:.8rem">{{ \Illuminate\Support\Str::limit($cr->title, 20) }}</span>
        <span style="font-size:.65rem;color:var(--accent);font-weight:700;flex-shrink:0">{{ (int)$crp }}%</span>
    </a>
    @endforeach
    @endif

    {{-- Profil de genres --}}
    @if(!empty($genreProfile))
    <div class="nav-section">Vos genres</div>
    <div class="genre-profile">
        @foreach($genreProfile as $gp)
        <div class="gp-item">
            <div class="gp-label">
                <span>{{ $gp['genre'] }}</span>
                <span>{{ $gp['percent'] }}%</span>
            </div>
            <div class="gp-bar"><div class="gp-fill" style="width:{{ $gp['percent'] }}%"></div></div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Favoris récents --}}
    @if($recentFavorites->isNotEmpty())
    <div class="nav-section">Favoris récents</div>
    @foreach($recentFavorites as $fav)
    <a class="nav-item" href="{{ route('reader.show', $fav->book, false) }}" title="{{ $fav->book->title }}">
        <svg viewBox="0 0 24 24" fill="currentColor" style="color:var(--accent)"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z" stroke="none"/></svg>
        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;font-size:.8rem">{{ \Illuminate\Support\Str::limit($fav->book->title, 20) }}</span>
    </a>
    @endforeach
    @endif

    {{-- Stats globales --}}
    <div class="sidebar-stat" style="margin-top:12px">
        <div class="ss-item"><strong>{{ $totalPublishedBooks }}</strong><span>Livres</span></div>
        <div class="ss-item"><strong>{{ $totalPdfBooks }}</strong><span>PDF</span></div>
        <div class="ss-item"><strong>{{ $totalEpubBooks }}</strong><span>EPUB</span></div>
        <div class="ss-item"><strong>{{ count($searchHistory) }}</strong><span>Recherches</span></div>
    </div>

    {{-- Déconnexion --}}
    <div class="sidebar-nav-bottom">
        @auth
        <form method="POST" action="{{ route('logout', [], false) }}">
            @csrf
            <button class="nav-item" style="width:100%;text-align:left;color:var(--muted)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Déconnexion
            </button>
        </form>
        @else
        <a class="nav-item" href="{{ route('login', [], false) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Connexion
        </a>
        @endauth
    </div>
</aside>

{{-- ═══════════════ MAIN ═══════════════ --}}
<main class="main">

    {{-- TOPBAR --}}
    <div class="topbar" data-reveal>
        <div style="display:flex;align-items:center;gap:12px">
            <button id="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')"
                style="display:none;background:rgba(255,255,255,.07);border:1px solid var(--border);border-radius:12px;padding:9px;color:var(--text);line-height:1">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1>Bibliothèque</h1>
        </div>
        <div class="topbar-right">
            <form method="GET" action="{{ route('reader.index', [], false) }}" id="filter-form" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <input type="hidden" name="sort" id="sort-input" value="{{ $sortBy }}">
                @if($filterType !== '')
                <input type="hidden" name="filter" value="{{ $filterType }}">
                @endif
                <div class="search-box" style="margin:0">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--muted);flex-shrink:0"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input name="search" value="{{ $search }}" placeholder="Titre, auteur, genre…" autocomplete="off" id="topbar-search">
                </div>
                {{-- Format filter --}}
                <select name="format" id="format-filter" onchange="document.getElementById('filter-form').submit()"
                    style="background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:12px;color:var(--text);font:inherit;font-size:.82rem;padding:8px 12px;outline:none;cursor:pointer">
                    <option value="" {{ $filterFormat==='' ? 'selected' : '' }}>Tous formats</option>
                    <option value="pdf" {{ $filterFormat==='pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="epub" {{ $filterFormat==='epub' ? 'selected' : '' }}>EPUB</option>
                </select>
                <button class="btn btn-primary btn-sm" type="submit">Rechercher</button>
                @if($hasActiveFilters)
                <a class="btn btn-ghost btn-sm" href="{{ route('reader.index', [], false) }}">✕ Effacer</a>
                @endif
            </form>
            @auth
            @if($unreadNotificationsCount > 0)
            <span class="notif-dot" title="{{ $unreadNotificationsCount }} notification(s)">{{ $unreadNotificationsCount }}</span>
            @endif
            @endauth
        </div>
    </div>

    @include('partials.flash-messages')

    {{-- ─── STATS STRIP ─── --}}
    @auth
    <div class="stats-strip" data-reveal>
        <div class="sstrip-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <div><strong>{{ $continueReading->count() }}</strong><span>En cours</span></div>
        </div>
        <div class="sstrip-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--green)"><polyline points="20 6 9 17 4 12"/></svg>
            <div><strong style="color:var(--green)">{{ $finishedBooks }}</strong><span>Terminés</span></div>
        </div>
        <div class="sstrip-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--accent)"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <div><strong style="color:var(--accent)">{{ count($favoriteBookIds) }}</strong><span>Favoris</span></div>
        </div>
        <div class="sstrip-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--purple)"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            <div><strong style="color:var(--purple)">{{ count($wishlistBookIds) }}</strong><span>Liste d'envie</span></div>
        </div>
        <div class="sstrip-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text-2)"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <div><strong>{{ number_format($totalPagesRead) }}</strong><span>Pages lues</span></div>
        </div>
        {{-- Tri --}}
        <div class="sstrip-sort" style="margin-left:auto">
            <span style="font-size:.75rem;color:var(--muted);margin-right:8px">Trier par :</span>
            @foreach(['title'=>'A–Z','recent'=>'Récents','rating'=>'Note','popular'=>'Populaires'] as $key=>$label)
            <button onclick="setSortAndSubmit('{{ $key }}')" class="sort-btn {{ $sortBy===$key ? 'active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
    </div>
    @endauth

    @if($filterType === '' && $search === '')
    {{-- ─── HERO BANNER ─── --}}
    <div class="hero-banner" data-reveal>
        <div class="hero-glow"></div>
        <div class="hero-kicker">
            <span class="hero-kicker-dot"></span>
            Lectura · Système de lecture immersive
        </div>
        <h1 class="hero-title">
            @auth Bonjour, {{ explode(' ', $readerName)[0] }}. @else Bienvenue sur Lectura. @endauth
        </h1>
        <p class="hero-sub">
            @if($continueReading->isNotEmpty())
                Vous avez <strong style="-webkit-text-fill-color:var(--accent-2)">{{ $continueReading->count() }} lecture{{ $continueReading->count() > 1 ? 's' : '' }} en cours</strong>. Reprenez là où vous vous êtes arrêté — votre progression est sauvegardée automatiquement.
            @else
                Bibliothèque immersive avec progression automatique, recommandations IA personnalisées et lecture fluide PDF &amp; EPUB.
            @endif
        </p>
        <div class="hero-actions">
            @if($featuredBook)
            <a class="btn btn-primary" href="{{ route('reader.show', $featuredBook, false) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Lire maintenant
            </a>
            @endif
            @auth
            <a class="btn btn-ghost" href="{{ route('profile.edit', [], false) }}">Mon profil</a>
            @else
            <a class="btn btn-primary" href="{{ route('register', [], false) }}">S'inscrire gratuitement</a>
            <a class="btn btn-ghost" href="{{ route('login', [], false) }}">Connexion</a>
            @endauth
        </div>
        <div class="hero-stats">
            <div class="hstat"><strong>{{ $totalPublishedBooks }}</strong><span>Livres disponibles</span></div>
            <div class="hstat"><strong>{{ $totalPdfBooks }}</strong><span>Fichiers PDF</span></div>
            <div class="hstat"><strong>{{ $totalEpubBooks }}</strong><span>Fichiers EPUB</span></div>
            @auth
            <div class="hstat"><strong>{{ $recommendations->count() }}</strong><span>Suggestions IA</span></div>
            @endauth
        </div>
    </div>

    {{-- ─── HISTORIQUE RECHERCHES ─── --}}
    @if(!empty($searchHistory) && !$hasActiveFilters)
    <div class="search-history" data-reveal>
        <span style="font-size:.75rem;color:var(--muted);align-self:center">🕐 Récentes :</span>
        @foreach(array_slice($searchHistory, 0, 6) as $sh)
        <a class="sh-tag" href="{{ route('reader.index', ['search' => $sh], false) }}">{{ $sh }}</a>
        @endforeach
    </div>
    @endif

    {{-- ─── GENRE PILLS ─── --}}
    @php
        $allGenres = $books->flatMap(fn($b) => $b->genres ?? [])->filter()->map(fn($g) => trim($g))->countBy()->sortDesc()->take(10)->keys();
    @endphp
    @if($allGenres->isNotEmpty())
    <div class="genre-pills" data-reveal>
        <a class="genre-pill {{ $search === '' ? 'active' : '' }}" href="{{ route('reader.index', [], false) }}">Tous</a>
        @foreach($allGenres as $gname)
        <a class="genre-pill {{ $search === $gname ? 'active' : '' }}" href="{{ route('reader.index', ['search' => $gname], false) }}">{{ $gname }}</a>
        @endforeach
    </div>
    @endif

    {{-- ─── REPRENDRE LA LECTURE ─── --}}
    @if($continueReading->isNotEmpty())
    <div class="section-head" data-reveal>
        <div>
            <h2>Reprendre la lecture</h2>
            <p>Continuez là où vous vous êtes arrêté</p>
        </div>
        <span class="section-note">{{ $continueReading->count() }} en cours</span>
    </div>
    <div class="grid-4" style="margin-bottom:16px" data-reveal>
        @foreach($continueReading as $book)
        @php $prog = (float)($book->readingProgress->first()?->progress_percent ?? 0); @endphp
        <article class="card" id="book-card-{{ $book->id }}">
            <div class="card-cover" style="background-image:url('{{ $book->display_cover_url }}')">
                <span style="background:rgba(244,164,74,.85);color:#1a0d00;top:12px;left:12px;padding:3px 10px;border-radius:999px;font-size:.68rem;font-weight:700;position:absolute">{{ (int)$prog }}% lu</span>
                <span class="card-cover-title">{{ $book->title }}</span>
            </div>
            <div class="card-body">
                <div class="card-meta">
                    <span class="badge badge-default">{{ strtoupper($book->file_format) }}</span>
                    @if($book->author)<span class="badge badge-default">{{ $book->author->name }}</span>@endif
                </div>
                <div class="card-title">{{ $book->title }}</div>
                <div class="progress-wrap">
                    <div class="progress-bar" style="width:{{ max(3,min(100,$prog)) }}%"></div>
                </div>
                <div class="card-footer">
                    <span class="progress-label">{{ (int)$prog }}% terminé</span>
                    <div style="display:flex;gap:6px">
                        <a class="btn btn-primary btn-sm" href="{{ route('reader.show', $book, false) }}">Reprendre →</a>
                        @auth<button class="btn btn-ghost btn-sm" onclick="markFinished({{ $book->id }}, this)" title="Marquer comme terminé">✓ Terminé</button>@endauth
                    </div>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @endif

    {{-- ─── SUGGESTIONS IA ─── --}}
    @if($recommendations->isNotEmpty())
    <div class="section-head" data-reveal>
        <div>
            <h2>Suggestions personnalisées</h2>
            @if(!empty($recommendationInsights))
            <p>Basé sur {{ $recommendationInsights['reading_signals'] }} lecture(s) · {{ $recommendationInsights['favorites_count'] }} favori(s) · signal <strong style="color:var(--green)">{{ $recommendationInsights['signals_strength'] }}</strong></p>
            @else
            <p>Recommandations basées sur vos habitudes de lecture</p>
            @endif
        </div>
        <span class="section-note badge-ai" style="background:rgba(167,139,255,.08);border-color:rgba(167,139,255,.2);color:var(--purple);padding:5px 14px;border-radius:999px;font-size:.78px;border:1px solid">🤖 IA personnalisée</span>
    </div>
    <div class="grid-3" style="margin-bottom:16px">
        @foreach($recommendations as $rec)
        @php $rb = $rec->book; @endphp
        @if($rb)
        <article class="card" data-reveal>
            <div class="card-cover" style="background-image:url('{{ $rb->display_cover_url }}')">
                <span class="card-ribbon" style="background:rgba(167,139,255,.7);color:#fff;padding:4px 10px;border-radius:999px;font-size:.65rem;font-weight:700;position:absolute;top:12px;right:12px">✦ IA</span>
                <span class="card-cover-title">{{ $rb->title }}</span>
            </div>
            <div class="card-body">
                <div class="card-meta">
                    <span class="badge badge-purple">Suggestion IA</span>
                    <span class="badge badge-default">{{ strtoupper($rb->file_format) }}</span>
                    @foreach(array_slice($rb->genres ?? [], 0, 2) as $g)<span class="badge badge-default">{{ $g }}</span>@endforeach
                </div>
                <div class="card-title">{{ $rb->title }}</div>
                <div class="card-author">{{ $rb->author?->name ?? 'Auteur inconnu' }}</div>
                <div class="ai-reason">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    {{ $rec->reason }}
                </div>
                <div class="card-footer">
                    <span style="font-size:.72rem;color:var(--muted)">Score {{ number_format((float)$rec->score, 0) }}</span>
                    <a class="btn btn-primary btn-sm" href="{{ route('reader.show', $rb, false) }}">Découvrir →</a>
                </div>
            </div>
        </article>
        @endif
        @endforeach
    </div>
    @endif

    @endif

    {{-- ─── TOUS LES LIVRES ─── --}}
    <div class="section-head" data-reveal>
        <div>
            @php
                $sectionTitle = 'Tous les livres';
                if ($search !== '') $sectionTitle = 'Résultats pour «&nbsp;'.$search.'&nbsp;»';
                elseif ($filterType === 'favorites') $sectionTitle = 'Mes favoris';
                elseif ($filterType === 'wishlist') $sectionTitle = 'Ma liste d\'envie';
            @endphp
            <h2>{!! $sectionTitle !!}</h2>
            <p>{{ $hasActiveFilters ? $books->total().' livre(s) trouvé(s)' : 'Chaque lecture reprend depuis votre dernière progression' }}</p>
        </div>
        <span class="section-note">{{ $books->total() }} titre(s)</span>
    </div>

    @if($books->isEmpty())
    <div class="empty-state" data-reveal>
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin:0 auto;display:block;color:var(--muted)"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <p>{{ $hasActiveFilters ? 'Aucun livre ne correspond à « '.$search.' ». Essayez un autre terme.' : 'Aucun livre disponible pour le moment.' }}</p>
        @if($hasActiveFilters)
        <a class="btn btn-ghost" href="{{ route('reader.index', [], false) }}" style="margin-top:18px;display:inline-flex">Effacer les filtres</a>
        @endif
    </div>
    @else
    <div class="grid-4">
        @foreach($books as $book)
        @php
            $p = $book->readingProgress->first();
            $pp = (float)($p?->progress_percent ?? 0);
            $isFav = in_array($book->id, $favoriteBookIds ?? [], true);
            $isWish = in_array($book->id, $wishlistBookIds ?? [], true);
        @endphp
        <article class="card" data-reveal id="book-card-{{ $book->id }}">
            <div class="card-cover" style="background-image:url('{{ $book->display_cover_url }}')">
                @if((float)$book->average_rating > 0)
                <span style="background:rgba(77,216,154,.8);color:#003320;top:12px;left:12px;padding:3px 9px;border-radius:999px;font-size:.67rem;font-weight:700;position:absolute">★ {{ number_format((float)$book->average_rating,1) }}</span>
                @endif
                <span class="card-cover-title">{{ $book->title }}</span>
            </div>
            <div class="card-body">
                <div class="card-meta">
                    <span class="badge badge-default">{{ strtoupper($book->file_format) }}</span>
                    @if($pp > 0)<span class="badge badge-accent">{{ (int)$pp }}%</span>@endif
                    @foreach(array_slice($book->genres ?? [], 0, 1) as $g)<span class="badge badge-default">{{ $g }}</span>@endforeach
                </div>
                <div class="card-title">{{ $book->title }}</div>
                <div class="card-author">{{ $book->author?->name ?? 'Auteur inconnu' }}</div>
                <div class="card-desc">{{ $book->description ? \Illuminate\Support\Str::limit($book->description, 110) : 'Disponible en lecture immersive dans votre navigateur.' }}</div>
                @if($pp > 0)
                <div class="progress-wrap">
                    <div class="progress-bar" style="width:{{ max(3,min(100,$pp)) }}%"></div>
                </div>
                @endif
                <div class="card-footer">
                    <span class="progress-label">{{ $p ? (int)$pp . '% lu' : 'Nouveau' }}</span>
                    <div class="card-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('reader.show', $book, false) }}">{{ $p ? 'Reprendre' : 'Lire' }}</a>
                        @auth
                        <button class="btn btn-ghost btn-sm btn-icon fav-btn" type="button"
                            data-book-id="{{ $book->id }}"
                            data-url="{{ route('reader.favorites.toggle', $book) }}"
                            data-active="{{ $isFav ? '1' : '0' }}"
                            title="{{ $isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                            style="{{ $isFav ? 'color:var(--accent)' : '' }}">
                            {{ $isFav ? '♥' : '♡' }}
                        </button>
                        <button class="btn btn-ghost btn-sm btn-icon wish-btn" type="button"
                            data-book-id="{{ $book->id }}"
                            data-url="{{ route('reader.wishlist.toggle', $book) }}"
                            data-active="{{ $isWish ? '1' : '0' }}"
                            title="{{ $isWish ? 'Retirer de la liste' : 'Lire plus tard' }}"
                            style="{{ $isWish ? 'color:var(--purple)' : '' }}">
                            {{ $isWish ? '🔖' : '📌' }}
                        </button>
                        @endauth
                    </div>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @endif

    @if($books->hasPages())
    <div class="pagination-wrapper" data-reveal>
        {{ $books->links('pagination::bootstrap-5') }}
    </div>
    @endif

    @include('partials.app-footer')
</main>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Mobile menu ──
const menuBtn = document.getElementById('menu-btn');
const sidebar = document.getElementById('sidebar');
function checkMenu(){if(menuBtn) menuBtn.style.display = window.innerWidth <= 720 ? 'flex' : 'none';}
checkMenu();
window.addEventListener('resize', checkMenu);
document.addEventListener('click', e => {
    if(sidebar && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !menuBtn?.contains(e.target))
        sidebar.classList.remove('open');
});

// ── Scroll reveal ──
const revealObs = new IntersectionObserver(entries => {
    entries.forEach((e, i) => {
        if(e.isIntersecting){ setTimeout(() => e.target.classList.add('visible'), i * 40); revealObs.unobserve(e.target); }
    });
}, { threshold: 0.07 });
document.querySelectorAll('[data-reveal]').forEach(el => revealObs.observe(el));

// ── Progress bars animate on load ──
window.addEventListener('load', () => {
    document.querySelectorAll('.gp-fill').forEach(el => { const w = el.style.width; el.style.width='0'; setTimeout(() => el.style.width=w, 300); });
});

// ── Sort helper ──
function setSortAndSubmit(key) {
    document.getElementById('sort-input').value = key;
    document.getElementById('filter-form').submit();
}

// ── AJAX toggle favorites ──
document.querySelectorAll('.fav-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        btn.disabled = true;
        try {
            const res = await fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
            const data = await res.json();
            btn.dataset.active = data.active ? '1' : '0';
            btn.textContent = data.active ? '♥' : '♡';
            btn.style.color = data.active ? 'var(--accent)' : '';
            btn.title = data.active ? 'Retirer des favoris' : 'Ajouter aux favoris';
            showToast(data.message, data.active ? 'accent' : 'default');
        } catch(e) { showToast('Erreur lors de la mise à jour.', 'error'); }
        btn.disabled = false;
    });
});

// ── AJAX toggle wishlist ──
document.querySelectorAll('.wish-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        btn.disabled = true;
        try {
            const res = await fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
            const data = await res.json();
            btn.dataset.active = data.active ? '1' : '0';
            btn.textContent = data.active ? '🔖' : '📌';
            btn.style.color = data.active ? 'var(--purple)' : '';
            btn.title = data.active ? 'Retirer de la liste' : 'Lire plus tard';
            showToast(data.message, 'purple');
        } catch(e) { showToast('Erreur lors de la mise à jour.', 'error'); }
        btn.disabled = false;
    });
});

// ── Mark as finished ──
async function markFinished(bookId, btn) {
    btn.disabled = true; btn.textContent = '…';
    try {
        const url = `/lecteur/livres/${bookId}/termine`;
        const res = await fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
        const data = await res.json();
        if(data.marked){
            btn.textContent = '✓ Lu !';
            btn.style.color = 'var(--green)';
            const card = document.getElementById('book-card-'+bookId);
            if(card){ const bar = card.querySelector('.progress-bar'); if(bar) bar.style.width='100%'; }
            showToast(data.message, 'green');
        }
    } catch(e) { btn.textContent = '✓ Terminé'; btn.disabled = false; }
}

// ── Toast notifications ──
function showToast(msg, type='default') {
    const colors = { accent:'var(--accent)', green:'var(--green)', purple:'var(--purple)', error:'var(--red)', default:'var(--text-2)' };
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:14px;background:rgba(10,18,36,.97);border:1px solid ${colors[type]||colors.default};color:${colors[type]||colors.default};font-size:.85rem;font-weight:500;box-shadow:0 8px 32px rgba(0,0,0,.5);transform:translateY(20px);opacity:0;transition:all .3s ease;max-width:320px`;
    t.textContent = msg;
    document.body.appendChild(t);
    requestAnimationFrame(() => { t.style.transform='translateY(0)'; t.style.opacity='1'; });
    setTimeout(() => { t.style.transform='translateY(20px)'; t.style.opacity='0'; setTimeout(()=>t.remove(),350); }, 3200);
}
</script>
</body>
</html>
