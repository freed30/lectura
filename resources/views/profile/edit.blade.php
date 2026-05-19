<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Lectura | Profil & Préférences</title>
    @include('partials.reader-styles')
    <style>
        .profile-grid { display: grid; grid-template-columns: 1fr minmax(320px, 380px); gap: 32px; }
        @media (max-width: 1024px) { .profile-grid { grid-template-columns: 1fr; } }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: .85rem; color: var(--text-2); font-weight: 600; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 14px 18px; border-radius: 14px; background: rgba(255,255,255,.03); border: 1px solid var(--border); color: var(--text); font: inherit; outline: none; transition: all .2s; }
        .form-control:focus { border-color: rgba(244,164,74,.5); background: rgba(255,255,255,.06); box-shadow: 0 0 0 3px rgba(244,164,74,.1); }
        
        .list-item { padding: 18px; border-radius: 16px; background: rgba(255,255,255,.02); border: 1px solid var(--border); margin-bottom: 12px; transition: transform .2s; }
        .list-item:hover { background: rgba(255,255,255,.04); border-color: rgba(244,164,74,.2); transform: translateX(4px); }
        
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 999px; }
    </style>
</head>
<body>
<div class="app">

    {{-- SIDEBAR ─── Identique au tableau de bord lecteur --}}
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura">
            <div><strong>Lectura</strong><span>Profil & Préférences</span></div>
        </div>

        <div class="reader-card">
            <div class="rc-info">
                <div class="rc-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                <div>
                    <div class="rname">{{ $user->name }}</div>
                    <div class="rrole">✦ {{ $user->isAdmin() ? 'Administrateur' : 'Lecteur actif' }}</div>
                </div>
            </div>
        </div>

        <div class="nav-section">Navigation</div>
        <a class="nav-item" href="{{ route('reader.index', [], false) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Bibliothèque
        </a>
        <a class="nav-item" href="{{ route('reader.index', ['filter' => 'favorites'], false) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
            Mes favoris
        </a>
        <a class="nav-item" href="{{ route('reader.index', ['filter' => 'wishlist'], false) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            Ma liste d'envie
        </a>
        <a class="nav-item active" href="{{ route('profile.edit', [], false) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            Mon profil
        </a>
        @if($user->isAdmin())
        <a class="nav-item" href="{{ route('admin.dashboard', [], false) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Administration
        </a>
        @endif

        <div class="sidebar-stat" style="margin-top:16px">
            <div class="ss-item"><strong>{{ $completedBooksCount }}</strong><span>Livres terminés</span></div>
            <div class="ss-item"><strong>{{ $historyCount }}</strong><span>Sessions actives</span></div>
        </div>

        <div class="sidebar-nav-bottom">
            <form method="POST" action="{{ route('logout', [], false) }}">
                @csrf
                <button class="nav-item" style="width:100%;text-align:left;color:var(--muted)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        {{-- TOPBAR --}}
        <div class="topbar" data-reveal>
            <div style="display:flex;align-items:center;gap:12px">
                <button id="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')"
                    style="display:none;background:rgba(255,255,255,.07);border:1px solid var(--border);border-radius:12px;padding:9px;color:var(--text)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <h1>Profil & Préférences</h1>
            </div>
            <div class="topbar-right">
                <a href="{{ route('reader.index', [], false) }}" class="btn btn-primary btn-sm">Ouvrir la bibliothèque →</a>
            </div>
        </div>

        @include('partials.flash-messages')
        
        @if ($errors->any())
            <div class="flash flash-error" data-reveal>
                <ul style="margin-left:16px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-grid">
            {{-- COLONNE GAUCHE : FORMULAIRE DE PROFIL --}}
            <div class="space-y-6">
                <div class="card" data-reveal>
                    <div class="card-body" style="padding: 32px">
                        <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin-bottom:6px">Paramètres du compte</h2>
                        <p style="color:var(--muted);font-size:.85rem;margin-bottom:24px">Mettez à jour vos informations personnelles et vos préférences de lecture.</p>
                        
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                                <div class="form-group">
                                    <label>Nom d'utilisateur</label>
                                    <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Adresse Email</label>
                                    <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Biographie (optionnelle)</label>
                                <textarea class="form-control" name="bio" rows="3" placeholder="Parlez-nous de vos genres littéraires préférés...">{{ old('bio', $user->bio) }}</textarea>
                            </div>
                            
                            <hr style="border:0;border-top:1px solid var(--border);margin:32px 0">
                            
                            <h3 style="color:var(--accent);font-size:.9rem;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;font-weight:700">Préférences de lecteur (PDF/EPUB)</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                                <div class="form-group">
                                    <label>Thème par défaut</label>
                                    <select class="form-control" name="theme">
                                        <option value="dark" @selected(old('theme', $preferences->theme) === 'dark')>Mode Sombre (Recommandé)</option>
                                        <option value="light" @selected(old('theme', $preferences->theme) === 'light')>Mode Clair</option>
                                        <option value="sepia" @selected(old('theme', $preferences->theme) === 'sepia')>Mode Sépia</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Taille de police (EPUB)</label>
                                    <select class="form-control" name="font_size">
                                        <option value="small" @selected(old('font_size', $preferences->font_size) === 'small')>Petite</option>
                                        <option value="medium" @selected(old('font_size', $preferences->font_size) === 'medium')>Moyenne</option>
                                        <option value="large" @selected(old('font_size', $preferences->font_size) === 'large')>Grande</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Interligne (EPUB)</label>
                                    <select class="form-control" name="line_spacing">
                                        <option value="compact" @selected(old('line_spacing', $preferences->line_spacing) === 'compact')>Compact</option>
                                        <option value="comfortable" @selected(old('line_spacing', $preferences->line_spacing) === 'comfortable')>Confortable</option>
                                        <option value="wide" @selected(old('line_spacing', $preferences->line_spacing) === 'wide')>Large</option>
                                    </select>
                                </div>
                            </div>
                            
                            <hr style="border:0;border-top:1px solid var(--border);margin:32px 0">
                            
                            <h3 style="color:var(--accent);font-size:.9rem;margin-bottom:16px;text-transform:uppercase;letter-spacing:1px;font-weight:700">Sécurité du compte</h3>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                                <div class="form-group">
                                    <label>Nouveau mot de passe</label>
                                    <input class="form-control" type="password" name="password" placeholder="Laissez vide pour conserver l'actuel">
                                </div>
                                <div class="form-group">
                                    <label>Confirmer le mot de passe</label>
                                    <input class="form-control" type="password" name="password_confirmation" placeholder="Laissez vide pour conserver l'actuel">
                                </div>
                            </div>

                            <div style="display:flex;justify-content:flex-end;margin-top:16px">
                                <button type="submit" class="btn btn-primary" style="height:48px;padding:0 32px;font-size:1rem">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE : NOTIFICATIONS & HISTORIQUE --}}
            <div>
                <div class="section-head" style="margin-top:0" data-reveal>
                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%">
                        <h2 style="font-size:1.3rem">Notifications</h2>
                        <span class="badge badge-accent">{{ $unreadNotificationsCount }} non lue(s)</span>
                    </div>
                </div>
                
                @if($unreadNotificationsCount > 0)
                <form method="POST" action="{{ route('notifications.read-all', [], false) }}">
                    @csrf
                    <button class="btn btn-ghost btn-sm" style="margin-bottom:16px;width:100%">Tout marquer comme lu ✓</button>
                </form>
                @endif
                
                <div class="custom-scroll" style="max-height:360px;overflow-y:auto;padding-right:8px;margin-bottom:32px" data-reveal>
                    @forelse($notifications as $notif)
                    <div class="list-item" style="{{ !$notif->read_at ? 'border-color:rgba(244,164,74,.4);background:rgba(244,164,74,.05)' : '' }}">
                        <strong style="display:block;font-size:.9rem;color:var(--text)">{{ $notif->data['title'] ?? 'Alerte Système' }}</strong>
                        <p style="font-size:.8rem;color:var(--text-2);margin-top:4px">{{ $notif->data['message'] ?? '' }}</p>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
                            <span style="font-size:.7rem;color:var(--muted)">{{ $notif->created_at->diffForHumans() }}</span>
                            @if(!$notif->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notif->id, false) }}" style="margin:0">
                                    @csrf
                                    <button type="submit" style="color:var(--accent);font-size:.75rem;font-weight:600">Marquer lu</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="empty-state" style="padding:30px 20px">
                        <p style="margin:0">Aucune notification pour le moment.</p>
                    </div>
                    @endforelse
                </div>

                <div class="section-head" data-reveal>
                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%">
                        <h2 style="font-size:1.3rem">Historique Récent</h2>
                        <span class="badge badge-default">{{ $historyCount }} sessions</span>
                    </div>
                </div>
                <div class="custom-scroll" style="max-height:400px;overflow-y:auto;padding-right:8px" data-reveal>
                    @forelse($history->take(10) as $entry)
                    <a href="{{ $entry->book ? route('reader.show', $entry->book, false) : '#' }}" class="list-item" style="display:block;text-decoration:none">
                        <strong style="display:block;font-size:.9rem;color:var(--text)">{{ $entry->book?->title ?? 'Livre supprimé' }}</strong>
                        <span style="font-size:.75rem;color:var(--muted);display:block;margin-top:2px">{{ $entry->book?->author?->name ?? 'Auteur Inconnu' }}</span>
                        <div style="margin-top:10px;display:flex;gap:6px">
                            <span class="badge badge-accent">{{ (int)$entry->last_progress_percent }}% lu</span>
                            <span class="badge badge-default">{{ $entry->last_opened_at?->diffForHumans() }}</span>
                            @if($entry->completed_at)
                                <span class="badge badge-green">Terminé</span>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="empty-state" style="padding:30px 20px">
                        <p style="margin:0">Votre historique apparaîtra ici après vos premières lectures.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');
    function checkMenu(){if(menuBtn) menuBtn.style.display = window.innerWidth <= 720 ? 'flex' : 'none';}
    checkMenu();
    window.addEventListener('resize', checkMenu);
    
    const revealObs = new IntersectionObserver(entries => {
        entries.forEach((e, i) => {
            if(e.isIntersecting){ setTimeout(() => e.target.classList.add('visible'), i * 40); revealObs.unobserve(e.target); }
        });
    }, { threshold: 0.07 });
    document.querySelectorAll('[data-reveal]').forEach(el => revealObs.observe(el));
</script>
</body>
</html>
