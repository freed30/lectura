@php
    $initialProgress = [
        'current_page' => (int) ($progress?->current_page ?? 0),
        'current_location' => $progress?->current_location,
        'total_pages' => (int) ($progress?->total_pages ?? ($book->page_count ?? 0)),
        'progress_percent' => (float) ($progress?->progress_percent ?? 0),
        'is_finished' => (bool) ($progress?->is_finished ?? false),
    ];
    $averageRating = (float) $book->average_rating;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lectura | {{ $book->title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>
    <style>
        :root{color-scheme:dark;--bg:#0e0b09;--panel:rgba(27,21,18,.92);--soft:rgba(43,34,28,.92);--line:rgba(255,239,218,.09);--text:#f7efdf;--muted:#bba58b;--accent:#ebb264;--ok:#92d4b0;--danger:#ff9d8a;font-family:"Segoe UI",sans-serif}
        :root[data-theme="light"]{color-scheme:light;--bg:#f4ecdf;--panel:rgba(255,250,243,.94);--soft:rgba(248,240,230,.96);--line:rgba(56,40,20,.09);--text:#281e16;--muted:#77614d;--accent:#b66d25;--ok:#2c7f54;--danger:#b54c30}
        :root[data-theme="sepia"]{color-scheme:light;--bg:#e6d5b7;--panel:rgba(247,238,223,.94);--soft:rgba(240,229,210,.96);--line:rgba(78,54,28,.12);--text:#3a2818;--muted:#7d6142;--accent:#9d5f24;--ok:#356d4d;--danger:#a74a31}
        *{box-sizing:border-box}html,body{margin:0;min-height:100vh;background:radial-gradient(circle at top left,rgba(235,178,100,.14),transparent 28%),linear-gradient(160deg,var(--bg),#15110f 58%,var(--bg));color:var(--text)}body{overflow-x:hidden;overflow-y:auto}
        button,a{font:inherit;color:inherit}button{cursor:pointer;border:0}
        .shell{display:grid;grid-template-rows:72px 1fr;min-height:100vh}.topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:0 20px;border-bottom:1px solid var(--line);background:rgba(16,13,11,.56);backdrop-filter:blur(18px)}
        .brand{display:flex;align-items:center;gap:14px;min-width:0}.mark{display:grid;place-items:center;width:40px;height:40px;border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.04)}.mark img{width:100%;height:100%;object-fit:cover}.copy strong,.copy span{display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.copy span{color:var(--muted);font-size:.92rem}
        .actions,.toolbar-left,.toolbar-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.actions form{margin:0}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 16px;border-radius:999px;border:1px solid var(--line);background:rgba(255,255,255,.05)}.btn-accent{background:linear-gradient(90deg,var(--accent),#ffd590);color:#281910;font-weight:700;border-color:transparent}
        .layout{display:grid;grid-template-columns:280px 1fr;min-height:0;align-items:start}.layout.immersive{grid-template-columns:1fr}.layout.immersive .sidebar{display:none}
        .sidebar{overflow:auto;padding:22px;border-right:1px solid var(--line);background:rgba(0,0,0,.08)}.cover{display:grid;align-items:end;min-height:280px;padding:20px;border-radius:26px;border:1px solid var(--line);background:linear-gradient(140deg,rgba(255,224,176,.18),transparent 42%),linear-gradient(160deg,#423023,#1b1511 68%);box-shadow:0 30px 80px rgba(0,0,0,.28)}.cover strong{max-width:86%;font-size:2rem;line-height:.95}
        .sidebar h1{margin:20px 0 8px;font-size:1.65rem}.sidebar p{color:var(--muted);line-height:1.7}.meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:16px 0}.meta-card,.hint,.review-panel,.review-item,.review-login,.review-status,.review-errors{padding:14px;border-radius:18px;border:1px solid var(--line);background:var(--soft)}.meta-card strong{display:block;font-size:1.08rem}.meta-card span{color:var(--muted);font-size:.9rem}.hint strong,.review-panel strong,.review-item strong,.review-login strong,.review-status strong,.review-errors strong{display:block;margin-bottom:6px}.hint-actions{display:grid;gap:10px;margin-top:12px}.hint-actions form{margin:0}
        .review-stack{display:grid;gap:12px;margin-top:16px}.review-form{display:grid;gap:12px;margin-top:12px}.review-form label{display:grid;gap:8px;color:var(--muted);font-size:.92rem}.review-form select,.review-form textarea{width:100%;padding:12px 14px;border-radius:14px;border:1px solid var(--line);background:rgba(255,255,255,.04);color:var(--text)}.review-form textarea{min-height:110px;resize:vertical}.review-meta{display:flex;align-items:center;justify-content:space-between;gap:10px;color:var(--muted);font-size:.88rem}.stars{color:var(--accent);letter-spacing:.08em}.review-status{color:var(--ok);background:rgba(146,212,176,.12);border-color:rgba(146,212,176,.24)}.review-errors{color:var(--danger);background:rgba(255,157,138,.1);border-color:rgba(255,157,138,.24)}.review-errors ul{margin:0;padding-left:18px}
        .main{display:grid;grid-template-rows:auto 1fr;gap:14px;min-height:0;padding:18px}.toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border-radius:24px;border:1px solid var(--line);background:var(--panel);box-shadow:0 20px 52px rgba(0,0,0,.2)}.chip{min-width:116px;padding:11px 14px;border-radius:999px;background:rgba(235,178,100,.14);color:var(--accent);font-weight:700;text-align:center}.status{display:flex;align-items:center;gap:12px;color:var(--muted)}.sync{color:var(--ok);font-size:.92rem}
        .frame{position:relative;overflow:hidden;min-height:0;border-radius:30px;border:1px solid var(--line);background:linear-gradient(180deg,rgba(19,15,13,.96),rgba(12,10,9,.98));box-shadow:0 36px 100px rgba(0,0,0,.36)}:root[data-theme="light"] .frame{background:linear-gradient(180deg,rgba(255,251,245,.97),rgba(247,239,226,.96))}:root[data-theme="sepia"] .frame{background:linear-gradient(180deg,rgba(245,235,219,.97),rgba(232,216,190,.96))}
        .frame.is-flipping::after{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(90deg,rgba(255,233,204,.24),transparent 46%,rgba(0,0,0,.1));transform-origin:left center;animation:flip .52s cubic-bezier(.2,.7,.2,1)}.frame[data-direction="prev"].is-flipping::after{transform-origin:right center}@keyframes flip{0%{opacity:0;transform:perspective(1400px) rotateY(0)}25%{opacity:.92}100%{opacity:0;transform:perspective(1400px) rotateY(-14deg)}}
        .loader,.error{position:absolute;inset:0;display:grid;place-items:center;padding:30px;text-align:center;z-index:4}.loader[hidden],.error[hidden],.pdf[hidden],.epub[hidden]{display:none}.error{color:var(--danger);background:rgba(0,0,0,.18)}
        .pdf,.epub{width:100%;height:100%}.pdf{display:grid;place-items:center;overflow:auto;padding:26px}.pdf canvas{max-width:100%;border-radius:18px;box-shadow:0 24px 68px rgba(0,0,0,.35)}.epub{padding:18px}.epub>div{height:100%}.badge{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;border:1px solid var(--line);background:rgba(255,255,255,.05);color:var(--muted);font-size:.86rem}
        @media (max-width:1080px){.layout{grid-template-columns:1fr}.sidebar{border-right:0;border-bottom:1px solid var(--line)}}
        @media (max-width:720px){body{overflow:auto}.shell{grid-template-rows:auto 1fr}.topbar{padding:12px;align-items:flex-start;flex-direction:column}.main{padding:12px}.toolbar{align-items:flex-start;flex-direction:column}.toolbar-left,.toolbar-right,.actions,.actions form{width:100%}.btn,.chip{width:100%}.pdf{padding:12px}.meta{grid-template-columns:1fr}.review-meta{align-items:flex-start;flex-direction:column}}

        :root{--bg:#050b14;--panel:rgba(10,20,32,.78);--soft:rgba(17,28,42,.66);--line:rgba(169,214,255,.16);--text:#eef6ff;--muted:#9db4cd;--accent:#ffb45d;--ok:#7fe0bc;--danger:#ff9f8f;font-family:"Trebuchet MS",sans-serif}
        html,body{background:radial-gradient(circle at top left,rgba(132,216,255,.18),transparent 22%),radial-gradient(circle at top right,rgba(255,180,93,.14),transparent 18%),linear-gradient(180deg,var(--bg),#09111c 42%,var(--bg))}
        .topbar,.toolbar,.frame,.meta-card,.hint,.review-panel,.review-item,.review-login,.review-status,.review-errors{backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px)}
        .topbar{margin:16px;border:1px solid var(--line);border-radius:999px;background:linear-gradient(180deg,rgba(13,24,38,.82),rgba(8,15,24,.68));box-shadow:0 24px 72px rgba(0,0,0,.28)}
        .mark{width:46px;height:46px;border-radius:18px}
        .copy strong{font-family:Georgia,serif;font-size:1.15rem}
        .actions .btn,.actions .btn-accent,.toolbar .btn,.toolbar .chip{transition:transform .18s ease,border-color .18s ease,background .18s ease}
        .actions .btn:hover,.actions .btn-accent:hover,.toolbar .btn:hover{transform:translateY(-2px)}
        .sidebar{padding:18px;border-right:0}
        .cover{min-height:320px;border-radius:30px;background:linear-gradient(180deg,rgba(255,255,255,.02),rgba(5,10,16,.78)),linear-gradient(145deg,rgba(132,216,255,.22),transparent 38%),linear-gradient(135deg,#254462,#101927 60%,#08111b);box-shadow:0 34px 92px rgba(0,0,0,.3)}
        .cover strong{font-family:Georgia,serif;font-size:2.4rem}
        .meta-card,.hint,.review-panel,.review-item,.review-login,.review-status,.review-errors{border-radius:22px;background:rgba(255,255,255,.04)}
        .toolbar{border-radius:28px;background:linear-gradient(180deg,rgba(13,24,38,.8),rgba(8,15,24,.66));box-shadow:0 24px 72px rgba(0,0,0,.24)}
        .chip{background:rgba(255,180,93,.12);color:var(--accent)}
        .frame{border-radius:36px;background:linear-gradient(180deg,rgba(12,22,34,.94),rgba(8,15,24,.98));box-shadow:0 38px 110px rgba(0,0,0,.38)}
        .frame::before{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(180deg,rgba(255,255,255,.04),transparent 18%),radial-gradient(circle at top,rgba(132,216,255,.08),transparent 44%)}
        .pdf canvas{border-radius:22px;box-shadow:0 30px 90px rgba(0,0,0,.34)}
        .stars{display:flex;gap:4px;letter-spacing:0}
        .stars span{color:rgba(255,255,255,.18);font-size:1rem}
        .stars span.filled{color:var(--accent)}
        .copy small{display:block;color:var(--muted);font-size:.78rem;letter-spacing:.08em;text-transform:uppercase}
        .pdf-native{width:100%;height:100%;border:0;border-radius:28px;background:rgba(255,255,255,.98)}
        .reader-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px 18px;border-radius:22px;border:1px solid var(--line);background:rgba(255,255,255,.04);color:var(--muted)}
        .reader-footer strong{color:var(--text);font-size:.98rem}
        @media (max-width:720px){.topbar{margin:12px;border-radius:28px}.cover{min-height:260px}}
        @media (max-width:520px){.sidebar,.main{padding:12px}.frame{border-radius:24px}.cover{min-height:220px}.review-login div{width:100%}}
        @media (max-width:720px){.reader-footer{align-items:flex-start;flex-direction:column}}

        .shell{grid-template-rows:auto auto 1fr}
        .layout{padding:0 18px 18px;gap:18px}
        .sidebar{position:sticky;top:20px;max-height:calc(100vh - 40px);overflow-y:auto;display:grid;align-content:start;gap:16px;padding:22px;border-radius:34px;border:1px solid var(--line);background:linear-gradient(180deg,rgba(13,24,38,.84),rgba(8,15,24,.74));box-shadow:0 28px 80px rgba(0,0,0,.24)}
        .sidebar h1{margin:6px 0 6px;font-family:Georgia,serif;font-size:2rem;line-height:.98}
        .sidebar p{margin:0}
        .sidebar-header{display:grid;gap:10px}
        .sidebar-badge{display:inline-flex;align-items:center;width:fit-content;padding:8px 14px;border-radius:999px;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.05);color:#d6e8fd;font-size:.8rem;letter-spacing:.08em;text-transform:uppercase}
        .author-line{display:flex;flex-wrap:wrap;gap:10px;color:#d6e8fd;font-size:.92rem}
        .sidebar-lead{color:var(--muted);line-height:1.75}
        .cover{position:relative;overflow:hidden}
        .cover::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 20%,rgba(6,11,18,.82) 100%)}
        .cover strong{position:relative;z-index:1}
        .main{gap:16px}
        .reader-overview,.reading-band,.frame-shell{border:1px solid var(--line);background:linear-gradient(180deg,rgba(13,24,38,.82),rgba(8,15,24,.7));box-shadow:0 26px 80px rgba(0,0,0,.22);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px)}
        .reader-overview{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(260px,.8fr);gap:18px;padding:22px 24px;border-radius:30px}
        .overview-copy{display:grid;gap:14px}
        .overview-kicker,.frame-label,.status-pill{display:inline-flex;align-items:center;width:fit-content;padding:8px 14px;border-radius:999px;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.05);color:#d4e7ff;font-size:.8rem;letter-spacing:.08em;text-transform:uppercase}
        .overview-title{margin:0;font-family:Georgia,serif;font-size:clamp(1.8rem,3vw,3rem);line-height:1}
        .overview-copy p,.overview-side p{margin:0;color:var(--muted);line-height:1.75}
        .overview-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
        .overview-metric{padding:14px;border-radius:22px;border:1px solid var(--line);background:rgba(255,255,255,.04)}
        .overview-metric strong,.overview-metric span{display:block}
        .overview-metric strong{font-size:1.14rem}
        .overview-metric span{margin-top:6px;color:var(--muted);font-size:.88rem}
        .overview-side{display:grid;align-content:space-between;gap:14px}
        .reading-band{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,.8fr);gap:16px;padding:18px 20px;border-radius:28px}
        .reading-band-copy{display:grid;gap:8px}
        .reading-band-copy strong{font-size:1.08rem}
        .reading-band-copy span{color:var(--muted);line-height:1.65}
        .reading-band-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;color:var(--muted);font-size:.92rem;margin-top:10px}
        .reading-progress-track{height:10px;border-radius:999px;background:rgba(255,255,255,.08);overflow:hidden}
        .reading-progress-fill{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--accent),#ffd590);box-shadow:0 0 22px rgba(255,180,93,.35)}
        .status-pill{color:#9ce6c6}
        .frame-shell{padding:16px;border-radius:34px}
        .frame-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:4px 4px 16px}
        .frame-copy{display:grid;gap:8px}
        .frame-copy strong{font-size:1.06rem}
        .frame-caption{color:var(--muted);font-size:.92rem;text-align:right}
        .frame{min-height:640px}
        .toolbar{background:rgba(255,255,255,.03);box-shadow:none}
        .review-item p{line-height:1.68;color:var(--text)}
        .review-panel p,.review-login p{line-height:1.7}
        .stars{display:flex;gap:4px}
        .stars span{font-size:1rem;color:rgba(255,255,255,.18)}
        .stars span.filled{color:var(--accent)}
        @media (max-width:1080px){.layout{grid-template-columns:1fr;padding:0 16px 16px}.sidebar{border-right:1px solid var(--line)}.reader-overview,.reading-band{grid-template-columns:1fr}}
        @media (max-width:720px){.reader-overview{padding:18px}.overview-metrics{grid-template-columns:1fr}.reading-band-meta,.frame-head{align-items:flex-start;flex-direction:column}.frame-caption{text-align:left}.frame-shell{padding:12px}}
        @media (max-width:520px){.layout{padding:0 12px 12px}.sidebar{padding:16px}.sidebar h1{font-size:1.7rem}.frame{min-height:500px}}
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <div class="mark">
                    <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura">
                </div>
                <div class="copy">
                    <strong>{{ $book->title }}</strong>
                    <span>{{ $book->author?->name ?? 'Auteur inconnu' }} | {{ strtoupper($book->file_format) }}</span>
                </div>
            </div>
            <div class="actions">
                @auth
                    <a class="btn" href="{{ route('dashboard', [], false) }}">Tableau de bord</a>
                    @if (auth()->user()->isAdmin())
                        <a class="btn" href="{{ route('admin.dashboard', [], false) }}">Espace Administrateur</a>
                    @endif
                    <form method="POST" action="{{ route('logout', [], false) }}">
                        @csrf
                        <button class="btn" type="submit">Déconnexion</button>
                    </form>
                @else
                    <span class="badge">Mode démo actif</span>
                    <a class="btn" href="{{ route('login', [], false) }}">Connexion</a>
                    <a class="btn" href="{{ route('register', [], false) }}">Inscription</a>
                @endauth
                <a class="btn" href="{{ route('reader.index', [], false) }}">Retour bibliothèque</a>
                <button class="btn" id="theme-toggle" type="button">Mode sombre</button>
                <button class="btn btn-accent" id="immersive-toggle" type="button">Mode immersif</button>
            </div>
        </header>

        <div style="padding: 0 18px;">
            @include('partials.flash-messages')
        </div>

        <div class="layout" id="layout">
            <aside class="sidebar">
                <div class="sidebar-header">
                    <span class="sidebar-badge">Lecture premium</span>
                    <div class="author-line">
                        <span>{{ $book->author?->name ?? 'Auteur inconnu' }}</span>
                        <span>{{ strtoupper($book->file_format) }}</span>
                    </div>
                </div>
                <div class="cover" style="background-image:linear-gradient(180deg,rgba(255,255,255,.02),rgba(5,10,16,.78)),url('{{ $book->display_cover_url }}');background-size:cover;background-position:center;"><strong>{{ $book->title }}</strong></div>
                <h1>{{ $book->title }}</h1>
                <p class="sidebar-lead">{{ $book->description ?: 'Lecture fluide avec reprise automatique de la progression.' }}</p>
                <div class="meta">
                    <div class="meta-card"><strong>{{ strtoupper($book->file_format) }}</strong><span>format</span></div>
                    <div class="meta-card"><strong>{{ $book->page_count ?: '--' }}</strong><span>pages connues</span></div>
                    <div class="meta-card"><strong>{{ $book->language }}</strong><span>langue</span></div>
                    <div class="meta-card"><strong>{{ number_format($initialProgress['progress_percent'], 0) }}%</strong><span>progression</span></div>
                    <div class="meta-card"><strong>{{ $averageRating > 0 ? number_format($averageRating, 1) . '/5' : '0/5' }}</strong><span>moyenne</span></div>
                    <div class="meta-card"><strong>{{ $book->visible_reviews_count }}</strong><span>avis lecteurs</span></div>
                </div>
                <div class="hint">
                    <strong>Mode lecture immersive</strong>
                    Affichage du fichier dans le navigateur, navigation rapide et sauvegarde progression automatique.

                    <div class="hint-actions">
                        <form method="POST" action="{{ route('reader.favorites.toggle', $book) }}">
                            @csrf
                            <button class="btn" type="submit">{{ $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</button>
                        </form>

                        <form method="POST" action="{{ route('reader.wishlist.toggle', $book) }}">
                            @csrf
                            <button class="btn" type="submit">{{ $isInWishlist ? 'Retirer de ma liste' : 'Ajouter a lire plus tard' }}</button>
                        </form>
                    </div>
                </div>

                <div class="review-stack">
                    @if (session('review_status'))
                        <div class="review-status">
                            <strong>Avis enregistre</strong>
                            {{ session('review_status') }}
                        </div>
                    @endif

                    @if ($errors->has('rating') || $errors->has('review_text'))
                        <div class="review-errors">
                            <strong>Verification</strong>
                            <ul>
                                @foreach ($errors->only(['rating', 'review_text']) as $messages)
                                    @foreach ($messages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="review-panel">
                        <strong>Donner votre avis</strong>
                        <p style="margin: 0; color: var(--muted);">
                            Notez ce livre et ajoutez un commentaire. La moyenne du livre est recalculee apres chaque avis.
                        </p>

                        @auth
                            <form class="review-form" method="POST" action="{{ route('reader.reviews.store', $book, false) }}">
                                @csrf
                                <label for="rating">
                                    Note
                                    <select id="rating" name="rating" required>
                                        @for ($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" @selected((int) old('rating', $userReview?->rating ?? 5) === $i)>{{ $i }}/5</option>
                                        @endfor
                                    </select>
                                </label>

                                <label for="review_text">
                                    Commentaire
                                    <textarea id="review_text" name="review_text" placeholder="Partagez votre ressenti sur ce livre...">{{ old('review_text', $userReview?->review_text) }}</textarea>
                                </label>

                                <button class="btn btn-accent" type="submit">
                                    {{ $userReview ? 'Mettre a jour mon avis' : 'Publier mon avis' }}
                                </button>
                            </form>
                        @else
                            <div class="review-login" style="margin-top: 12px;">
                                <strong>Connexion requise</strong>
                                <p style="margin: 0 0 10px; color: var(--muted);">Connectez-vous pour noter le livre et commenter.</p>
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <a class="btn" href="{{ route('login', [], false) }}">Connexion</a>
                                    <a class="btn" href="{{ route('register', [], false) }}">Inscription</a>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <div class="review-panel">
                        <strong>Avis des lecteurs</strong>
                        <p style="margin: 0; color: var(--muted);">
                            Les derniers commentaires visibles sur ce livre.
                        </p>
                    </div>

                    @forelse ($book->reviews as $review)
                        <article class="review-item">
                            <div class="review-meta">
                                <span>{{ $review->user?->name ?? 'Lecteur' }}</span>
                                <span>{{ $review->created_at?->diffForHumans() ?? 'recent' }}</span>
                            </div>
                            <div class="stars" aria-label="{{ (int) $review->rating }} sur 5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= (int) $review->rating ? 'filled' : '' }}">&#9733;</span>
                                @endfor
                            </div>
                            <p style="margin: 10px 0 0;">{{ $review->review_text ?: 'Aucun commentaire laisse.' }}</p>
                        </article>
                    @empty
                        <div class="review-item">
                            <strong>Pas encore d'avis</strong>
                            <p style="margin: 0;">Soyez le premier lecteur a noter ce livre.</p>
                        </div>
                    @endforelse
                </div>
            </aside>

            <main class="main" style="display:flex; flex-direction:column; gap:16px;">
                <section class="toolbar" style="flex-shrink:0;">
                    <div class="toolbar-left">
                        <button class="btn" id="prev-page" type="button">Page précédente</button>
                        <button class="btn" id="next-page" type="button">Page suivante</button>
                        <span class="chip" id="page-indicator">Chargement...</span>
                        
                        <div style="display:flex;align-items:center;gap:4px;margin-left:8px;border-left:1px solid var(--line);padding-left:12px">
                            <button class="btn" id="zoom-out" type="button" title="Réduire" style="min-height:34px;padding:0 12px;font-size:1.2rem;font-weight:700">-</button>
                            <span id="zoom-indicator" style="font-size:0.85rem;color:var(--muted);min-width:44px;text-align:center">100%</span>
                            <button class="btn" id="zoom-in" type="button" title="Agrandir" style="min-height:34px;padding:0 12px;font-size:1.2rem;font-weight:700">+</button>
                        </div>
                    </div>
                    <div class="toolbar-right">
                        <div class="status">
                            <span id="progress-indicator">0%</span>
                            <span class="sync" id="sync-status">En attente...</span>
                        </div>
                    </div>
                </section>

                <section class="frame-shell" style="flex:1; display:flex; flex-direction:column; padding:0; border:none; background:transparent; box-shadow:none;">
                    <div class="reading-progress-track" style="margin-bottom:12px; border-radius:999px; height:8px; background:rgba(255,255,255,0.05); overflow:hidden;">
                        <span class="reading-progress-fill" id="reading-progress-fill" style="display:block; height:100%; background:linear-gradient(90deg, var(--accent), #ffd590); transition:width 0.3s; width: {{ max(4, min(100, (float) $initialProgress['progress_percent'])) }}%"></span>
                    </div>

                    <section class="frame" id="frame" data-direction="next" style="flex:1; display:flex; flex-direction:column;">
                        <div class="loader" id="loader">Chargement du livre...</div>
                        <div class="error" id="error" hidden></div>
                        <div class="pdf" id="pdf-stage" hidden style="flex:1; overflow:hidden;"><canvas id="pdf-canvas" style="max-height:100%; object-fit:contain;"></canvas></div>
                        <iframe class="pdf-native" id="pdf-native" title="Lecteur PDF integre" hidden style="flex:1;"></iframe>
                        <div class="epub" id="epub-stage" hidden style="flex:1;"></div>
                    </section>
                </section>

                @include('partials.app-footer', ['class' => 'reader-footer'])
            </main>
        </div>
    </div>

    <script>
        const readerConfig = {
            format: @json($book->file_format),
            fileUrl: @json($readerFileUrl),
            progressUrl: @json(route('reader.progress.store', $book, false)),
            csrfToken: @json(csrf_token()),
            initialProgress: @json($initialProgress),
            preferredTheme: @json($preferences->theme ?? 'dark'),
            fontSize: @json($preferences->font_size ?? 'medium'),
            lineSpacing: @json($preferences->line_spacing ?? 'comfortable'),
            pageFlipEnabled: @json((bool) ($preferences->page_flip_enabled ?? true)),
            immersiveModeDefault: @json((bool) ($preferences->immersive_mode_default ?? false)),
        };

        const root = document.documentElement;
        const layout = document.getElementById('layout');
        const frame = document.getElementById('frame');
        const loader = document.getElementById('loader');
        const errorBox = document.getElementById('error');
        const pdfStage = document.getElementById('pdf-stage');
        const pdfCanvas = document.getElementById('pdf-canvas');
        const pdfNative = document.getElementById('pdf-native');
        const epubStage = document.getElementById('epub-stage');
        const prevButton = document.getElementById('prev-page');
        const nextButton = document.getElementById('next-page');
        const pageIndicator = document.getElementById('page-indicator');
        const zoomInBtn = document.getElementById('zoom-in');
        const zoomOutBtn = document.getElementById('zoom-out');
        const zoomIndicator = document.getElementById('zoom-indicator');
        const progressIndicator = document.getElementById('progress-indicator');
        const syncStatus = document.getElementById('sync-status');
        const pageMiniIndicator = document.getElementById('page-mini-indicator');
        const readingProgressFill = document.getElementById('reading-progress-fill');
        const readingProgressCopy = document.getElementById('reading-progress-copy');
        const readerStatusPill = document.getElementById('reader-status-pill');
        const themeToggle = document.getElementById('theme-toggle');
        const immersiveToggle = document.getElementById('immersive-toggle');
        const themeOrder = ['dark', 'sepia', 'light'];
        const themeLabels = {
            dark: 'Mode sombre',
            sepia: 'Mode sépia',
            light: 'Mode clair',
        };

        const state = {
            mode: readerConfig.format,
            pdfDocument: null,
            epubBook: null,
            rendition: null,
            currentPage: Number(readerConfig.initialProgress.current_page || 0),
            totalPages: Number(readerConfig.initialProgress.total_pages || 0),
            currentLocation: readerConfig.initialProgress.current_location || null,
            progressPercent: Number(readerConfig.initialProgress.progress_percent || 0),
            zoomLevel: 1.0,
            saveTimer: null,
            lastSavedSignature: null,
            pendingPayload: null,
            pendingDirection: 'next',
            busy: false,
            pdfLoadTimer: null,
        };

        initReader();

        async function initReader() {
            applyTheme(readerConfig.preferredTheme || localStorage.getItem('lectura-theme') || 'dark');
            bindEvents();
            if (readerConfig.immersiveModeDefault) {
                layout.classList.add('immersive');
            }
            syncImmersiveLabel();
            try {
                if (state.mode === 'pdf') {
                    await initPdfReader();
                } else {
                    await initEpubReader();
                }
            } catch (error) {
                showError(formatReaderError(error));
            }
        }

        function bindEvents() {
            prevButton.addEventListener('click', () => navigate('prev'));
            nextButton.addEventListener('click', () => navigate('next'));
            if(zoomInBtn) zoomInBtn.addEventListener('click', () => changeZoom(0.15));
            if(zoomOutBtn) zoomOutBtn.addEventListener('click', () => changeZoom(-0.15));
            themeToggle.addEventListener('click', toggleTheme);
            immersiveToggle.addEventListener('click', toggleImmersive);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowLeft') navigate('prev');
                if (event.key === 'ArrowRight' || event.key === ' ') { event.preventDefault(); navigate('next'); }
            });
            window.addEventListener('resize', debounce(() => {
                if (state.mode === 'pdf' && state.currentPage) {
                    if (state.pdfDocument) renderPdfPage(state.currentPage, false);
                    else renderNativePdfPage(state.currentPage, false);
                }
            }, 180));
            window.addEventListener('beforeunload', () => {
                // Avant de quitter la page, on envoie la derniere page connue au backend.
                const payload = state.pendingPayload || buildProgressPayload();
                if (!payload) return;
                fetch(readerConfig.progressUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': readerConfig.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                    keepalive: true,
                });
            });
        }

        function changeZoom(delta) {
            state.zoomLevel = Math.max(0.4, Math.min(4.0, state.zoomLevel + delta));
            if (zoomIndicator) zoomIndicator.textContent = `${Math.round(state.zoomLevel * 100)}%`;
            if (state.mode === 'pdf' && state.currentPage) {
                if (state.pdfDocument) renderPdfPage(state.currentPage, false);
            } else if (state.mode !== 'pdf' && state.rendition) {
                const percent = Math.round(state.zoomLevel * 100);
                state.rendition.themes.fontSize(`${percent}%`);
            }
        }

        async function initPdfReader() {
            if (!window.pdfjsLib) {
                initNativePdfReader();
                return;
            }

            loader.hidden = false;
            pdfNative.hidden = true;
            pdfStage.hidden = true;

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            const loadingTask = pdfjsLib.getDocument({ url: readerConfig.fileUrl, withCredentials: true });

            try {
                const pdfDocument = await Promise.race([
                    loadingTask.promise,
                    new Promise((_, reject) => {
                        state.pdfLoadTimer = window.setTimeout(() => reject(new Error('PDF_TIMEOUT')), 12000);
                    }),
                ]);
                clearTimeout(state.pdfLoadTimer);
                state.pdfLoadTimer = null;
                
                state.pdfDocument = pdfDocument;
                state.totalPages = pdfDocument.numPages;
                state.currentPage = clamp(state.currentPage || 1, 1, state.totalPages);
                
                loader.hidden = true;
                pdfStage.hidden = false;
                
                await renderPdfPage(state.currentPage, false);
                
                syncStatus.textContent = 'PDF optimisé prêt';
                readerStatusPill && (readerStatusPill.textContent = 'PDF optimisé');
            } catch (error) {
                clearTimeout(state.pdfLoadTimer);
                state.pdfLoadTimer = null;
                try { await loadingTask.destroy(); } catch (destroyError) {}
                initNativePdfReader();
            }
        }

        function initNativePdfReader() {
            pdfStage.hidden = true;
            pdfNative.hidden = false;
            state.pdfDocument = null;
            state.totalPages = Math.max(Number(state.totalPages || 0), Number(readerConfig.initialProgress.total_pages || 0));
            state.currentPage = Math.max(1, Number(state.currentPage || 1));
            renderNativePdfPage(state.currentPage, false);
            loader.hidden = true;
            syncStatus.textContent = 'Lecteur PDF integre';
            readerStatusPill.textContent = 'PDF integre';
        }

        function renderNativePdfPage(pageNumber, animate = true) {
            const safePage = Math.max(1, Number(pageNumber || 1));

            if (animate) {
                playFlip(state.pendingDirection);
            }

            pdfNative.src = `${readerConfig.fileUrl}#page=${safePage}&zoom=page-width`;
            state.currentPage = safePage;

            if (state.totalPages > 0) {
                state.progressPercent = (safePage / state.totalPages) * 100;
            }

            refreshIndicators();
            queueSave(buildProgressPayload());
        }

        async function renderPdfPage(pageNumber, animate = true) {
            if (!state.pdfDocument || state.busy) return;
            state.busy = true;
            try {
                const page = await state.pdfDocument.getPage(pageNumber);
                const baseViewport = page.getViewport({ scale: 1 });
                // Calculate scale to fit both width and height gracefully
                const containerWidth = pdfStage.clientWidth - 40;
                const containerHeight = pdfStage.clientHeight - 40;
                const scaleWidth = containerWidth / baseViewport.width;
                const scaleHeight = containerHeight / baseViewport.height;
                // Use the minimum scale to ensure the whole page is visible, but cap at 2.5x
                const baseScale = Math.min(Math.max(Math.min(scaleWidth, scaleHeight), 0.5), 2.5);
                const scale = baseScale * state.zoomLevel;
                const viewport = page.getViewport({ scale });
                const ratio = window.devicePixelRatio || 1;
                const context = pdfCanvas.getContext('2d', { alpha: false });
                if (animate) playFlip(state.pendingDirection);
                pdfCanvas.width = Math.floor(viewport.width * ratio);
                pdfCanvas.height = Math.floor(viewport.height * ratio);
                pdfCanvas.style.width = `${viewport.width}px`;
                pdfCanvas.style.height = `${viewport.height}px`;
                context.setTransform(ratio, 0, 0, ratio, 0, 0);
                // affichage du fichier page par page.
                await page.render({ canvasContext: context, viewport }).promise;
                state.currentPage = pageNumber;
                state.progressPercent = (pageNumber / state.totalPages) * 100;
                refreshIndicators();
                // sauvegarde progression apres le rendu.
                queueSave(buildProgressPayload());
            } finally {
                state.busy = false;
            }
        }

        async function initEpubReader() {
            if (!window.ePub) throw new Error('La bibliotheque EPUB est indisponible.');
            epubStage.hidden = false;
            // affichage du fichier EPUB dans le navigateur.
            state.epubBook = window.ePub(readerConfig.fileUrl);
            state.rendition = state.epubBook.renderTo('epub-stage', { width: '100%', height: '100%', flow: 'paginated', spread: 'always', manager: 'default' });
            syncEpubTheme();
            await state.epubBook.ready;
            try { await state.epubBook.locations.generate(1200); } catch (error) {}
            state.rendition.on('relocated', onEpubRelocated);
            await state.rendition.display(state.currentLocation || undefined);
            loader.hidden = true;
        }

        function onEpubRelocated(location) {
            const displayed = location?.start?.displayed || {};
            const cfi = location?.start?.cfi || null;
            let percent = typeof location?.start?.percentage === 'number' ? location.start.percentage * 100 : null;
            if (percent === null && state.epubBook?.locations?.length() && cfi) percent = state.epubBook.locations.percentageFromCfi(cfi) * 100;
            state.currentLocation = cfi;
            state.currentPage = Number(displayed.page || state.currentPage || 1);
            state.totalPages = Number(displayed.total || state.totalPages || 0);
            state.progressPercent = clamp(percent ?? state.progressPercent, 0, 100);
            refreshIndicators();
            playFlip(state.pendingDirection);
            // sauvegarde progression apres changement de position EPUB.
            queueSave(buildProgressPayload());
        }

        function navigate(direction) {
            state.pendingDirection = direction;
            if (state.mode === 'pdf') {
                // navigation entre les pages du PDF.
                const nextPage = direction === 'next' ? state.currentPage + 1 : state.currentPage - 1;
                const maxPage = state.totalPages > 0 ? state.totalPages : Number.MAX_SAFE_INTEGER;
                if (nextPage >= 1 && nextPage <= maxPage) {
                    if (state.pdfDocument) renderPdfPage(nextPage, true);
                    else renderNativePdfPage(nextPage, true);
                }
                return;
            }
            if (!state.rendition) return;
            // navigation du livre EPUB.
            direction === 'next' ? state.rendition.next() : state.rendition.prev();
        }

        function buildProgressPayload() {
            // Ce payload contient la position courante qui sera enregistree dans reading_progress.
            return {
                current_page: Number(state.currentPage || 0),
                current_location: state.currentLocation,
                total_pages: Number(state.totalPages || 0),
                progress_percent: Number(clamp(state.progressPercent || 0, 0, 100).toFixed(2)),
                is_finished: Number(state.progressPercent || 0) >= 99.5,
            };
        }

        function queueSave(payload) {
            state.pendingPayload = payload;
            if(syncStatus) syncStatus.textContent = 'Sauvegarde...';
            if(readerStatusPill) readerStatusPill.textContent = 'Synchronisation';
            // On attend un court delai pour regrouper les changements de page proches.
            clearTimeout(state.saveTimer);
            state.saveTimer = window.setTimeout(() => saveProgress(payload), 700);
        }

        async function saveProgress(payload) {
            const signature = JSON.stringify(payload);
            if (signature === state.lastSavedSignature) {
                if(syncStatus) syncStatus.textContent = 'Progression a jour';
                if(readerStatusPill) readerStatusPill.textContent = 'Progression a jour';
                return;
            }
            try {
                // Laravel met a jour ou cree la ligne reading_progress du lecteur pour ce livre.
                const response = await fetch(readerConfig.progressUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': readerConfig.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                if (response.status === 401) {
                    if(syncStatus) syncStatus.textContent = 'Connexion requise';
                    if(readerStatusPill) readerStatusPill.textContent = 'Connexion requise';
                    return;
                }
                if (!response.ok) throw new Error('Erreur de sauvegarde');
                state.lastSavedSignature = signature;
                if(syncStatus) syncStatus.textContent = 'Progression sauvegardee';
                if(readerStatusPill) readerStatusPill.textContent = 'Progression a jour';
            } catch (error) {
                if(syncStatus) syncStatus.textContent = 'Sauvegarde en attente';
                if(readerStatusPill) readerStatusPill.textContent = 'Sauvegarde en attente';
            }
        }

        function refreshIndicators() {
            if(pageIndicator) pageIndicator.textContent = state.totalPages ? `Page ${Math.max(1, state.currentPage)} / ${state.totalPages}` : `Position ${Math.max(1, state.currentPage)}`;
            if(progressIndicator) progressIndicator.textContent = `${Math.round(state.progressPercent || 0)}%`;
            if(pageMiniIndicator) pageMiniIndicator.textContent = state.totalPages ? `Page ${Math.max(1, state.currentPage)} sur ${state.totalPages}` : `Position ${Math.max(1, state.currentPage)}`;
            if(readingProgressCopy) readingProgressCopy.textContent = `${Math.round(state.progressPercent || 0)}% du livre parcouru`;
            if(readingProgressFill) readingProgressFill.style.width = `${Math.max(4, Math.min(100, Math.round(state.progressPercent || 0)))}%`;
        }

        function toggleTheme() {
            const currentTheme = root.dataset.theme || 'dark';
            const currentIndex = Math.max(themeOrder.indexOf(currentTheme), 0);
            const nextTheme = themeOrder[(currentIndex + 1) % themeOrder.length];
            applyTheme(nextTheme);
        }

        function applyTheme(theme) {
            root.dataset.theme = theme;
            localStorage.setItem('lectura-theme', theme);
            themeToggle.textContent = themeLabels[theme] || 'Mode sombre';
            syncEpubTheme();
        }

        function syncEpubTheme() {
            if (!state.rendition) return;
            const light = root.dataset.theme === 'light';
            const sepia = root.dataset.theme === 'sepia';
            const fontSize = readerConfig.fontSize === 'large'
                ? '115%'
                : (readerConfig.fontSize === 'small' ? '92%' : '100%');
            const lineHeight = readerConfig.lineSpacing === 'wide'
                ? '2'
                : (readerConfig.lineSpacing === 'compact' ? '1.55' : '1.8');
            state.rendition.themes.default({
                body: {
                    'background-color': sepia ? '#f3e6d2 !important' : (light ? '#fffaf3 !important' : '#15110e !important'),
                    color: sepia ? '#3c2a1a !important' : (light ? '#261b13 !important' : '#f7efdf !important'),
                    'font-family': 'Georgia, serif !important',
                    'font-size': `${fontSize} !important`,
                    'line-height': `${lineHeight} !important`,
                    padding: '0 18px !important',
                }
            });
        }

        async function toggleImmersive() {
            layout.classList.toggle('immersive');
            syncImmersiveLabel();
            if (!document.fullscreenElement) {
                try { await document.documentElement.requestFullscreen(); } catch (error) {}
            } else {
                await document.exitFullscreen();
            }
            if (state.mode === 'pdf' && state.currentPage) {
                setTimeout(() => {
                    if (state.pdfDocument) renderPdfPage(state.currentPage, false);
                    else renderNativePdfPage(state.currentPage, false);
                }, 150);
            }
        }

        function syncImmersiveLabel() {
            immersiveToggle.textContent = layout.classList.contains('immersive')
                ? 'Quitter immersif'
                : 'Mode immersif';
        }

        function playFlip(direction) {
            if (!readerConfig.pageFlipEnabled) return;
            frame.dataset.direction = direction;
            frame.classList.remove('is-flipping');
            requestAnimationFrame(() => frame.classList.add('is-flipping'));
            setTimeout(() => frame.classList.remove('is-flipping'), 560);
        }

        function showError(message) {
            loader.hidden = true;
            errorBox.hidden = false;
            errorBox.textContent = message;
            if(pageIndicator) pageIndicator.textContent = 'Lecture indisponible';
            if(syncStatus) syncStatus.textContent = 'Verification requise';
            if(pageMiniIndicator) pageMiniIndicator.textContent = 'Lecture indisponible';
            if(readerStatusPill) readerStatusPill.textContent = 'Verification requise';
        }

        function formatReaderError(error) {
            const message = error instanceof Error ? error.message : 'Impossible de charger le livre.';

            if (message.includes('PDF_TIMEOUT')) {
                return 'Le rendu avance du PDF a pris trop de temps. Le lecteur integre affiche le livre a la place.';
            }

            if (message.includes('Unexpected server response')) {
                return 'Le fichier du livre ne repond pas correctement. Rechargez la page ou reconnectez-vous puis reessayez.';
            }

            if (message.includes('Missing PDF')) {
                return 'Le PDF de ce livre est introuvable. L administrateur doit remettre le fichier dans la bibliotheque.';
            }

            return message;
        }

        function clamp(value, min, max) { return Math.min(Math.max(Number(value), min), max); }
        function debounce(callback, delay) { let t = null; return (...args) => { clearTimeout(t); t = setTimeout(() => callback(...args), delay); }; }
    </script>
</body>
</html>
