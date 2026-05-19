<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lectura | Upload admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --bg: #05080f;
            --panel: rgba(12, 20, 38, 0.75);
            --soft: rgba(18, 28, 52, 0.6);
            --line: rgba(100, 170, 255, 0.1);
            --text: #eef3ff;
            --muted: #607a99;
            --accent: #f4a44a;
            --ok: #4dd89a;
            --danger: #ff7070;
            font-family: 'Inter', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            background:
                radial-gradient(ellipse at 10% -5%, rgba(120, 190, 255, .12), transparent 45%),
                radial-gradient(ellipse at 90% 5%, rgba(167, 139, 255, .1), transparent 40%),
                radial-gradient(ellipse at 50% 100%, rgba(244, 164, 74, .06), transparent 50%),
                linear-gradient(180deg, #030710 0%, #060d1a 60%, #040a14 100%);
            overflow-x: hidden;
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1180px, calc(100% - 28px));
            margin: 0 auto;
            padding: 24px 0 40px;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 24px 28px;
            border-radius: 28px;
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(20px);
        }

        .hero h1 {
            margin: 10px 0 8px;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-family: 'Playfair Display', serif;
            line-height: 1.1;
        }

        .hero p {
            max-width: 700px;
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(126, 184, 245, 0.1);
            border: 1px solid rgba(126, 184, 245, 0.18);
            color: var(--blue, #7eb8f5);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.05);
            font-weight: 500;
            transition: all 0.2s;
        }
        .button:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .button-accent {
            background: linear-gradient(90deg, var(--accent), #ffd38b);
            color: #1a0d00;
            border-color: transparent;
            font-weight: 700;
        }
        .button-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(244, 164, 74, 0.3);
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(320px, 0.7fr);
            gap: 20px;
            margin-top: 24px;
        }

        .panel {
            padding: 24px;
            border-radius: 26px;
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(20px);
            box-shadow: 0 24px 72px rgba(0, 0, 0, 0.24);
        }

        .panel h2 {
            margin: 0 0 16px;
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
        }

        .status {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(77, 216, 154, 0.1);
            border: 1px solid rgba(77, 216, 154, 0.2);
            color: var(--ok);
            font-size: 0.9rem;
        }

        .errors {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(255, 112, 112, 0.1);
            border: 1px solid rgba(255, 112, 112, 0.2);
            color: var(--danger);
            font-size: 0.9rem;
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        label {
            color: var(--muted);
            font-size: 0.85rem;
            font-weight: 500;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--soft);
            color: var(--text);
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        
        input:focus, textarea:focus {
            border-color: rgba(244, 164, 74, 0.5);
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            padding: 0;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-top: 12px;
            cursor: pointer;
        }

        .hint {
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.85rem;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .item {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.02);
            transition: border-color 0.2s;
        }
        .item:hover {
            border-color: rgba(244, 164, 74, 0.2);
        }

        .item strong {
            display: block;
            margin-bottom: 2px;
            font-size: 0.95rem;
        }

        .item-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
        }

        .item p {
            margin: 0;
            color: var(--muted);
            font-size: 0.8rem;
        }

        .item-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            font-size: 0.75rem;
            border: 1px solid transparent;
        }

        .item-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 12px;
        }

        .link-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            font-size: 0.78rem;
            transition: background 0.2s;
        }
        .link-button:hover { background: rgba(255, 255, 255, 0.1); }

        code, .tag, .item p {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .danger-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 112, 112, 0.2);
            background: rgba(255, 112, 112, 0.1);
            color: var(--danger);
            font-size: 0.78rem;
            transition: all 0.2s;
        }
        .danger-button:hover {
            background: rgba(255, 112, 112, 0.2);
        }

        .page-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 24px;
            padding: 16px 20px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted);
            font-size: 0.85rem;
        }
        .page-footer strong { color: var(--text); font-size: 0.95rem; }

        @media (max-width: 920px) {
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 20px, 1180px); }
            .hero { padding: 20px; align-items: start; flex-direction: column; }
            .form-grid { grid-template-columns: 1fr; }
            .hero > div:last-child,
            .hero > div:last-child .button,
            .item-actions,
            .item-actions form,
            .item-actions .link-button,
            .item-actions .danger-button { width: 100%; }
            .item-head { flex-direction: column; }
            .page-footer { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <div>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;">
                    <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura" style="width:50px;height:50px;border-radius:14px;object-fit:cover;border:1px solid rgba(255,255,255,.08);box-shadow:0 12px 30px rgba(0,0,0,.2);">
                    <span class="badge">Espace Admin</span>
                </div>
                <h1>Ajouter un ouvrage</h1>
                <p>Complétez les informations ci-dessous pour importer un nouveau fichier dans la bibliothèque.</p>
            </div>
            <div style="display:flex;gap:10px;">
                <a class="button" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="button" href="{{ route('reader.index') }}">Bibliothèque</a>
            </div>
        </section>

        @include('partials.flash-messages')

        <div class="grid">
            <section class="panel">
                <h2>Informations du livre</h2>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="field field-full">
                            <label for="title">Titre du livre</label>
                            <input id="title" name="title" type="text" value="{{ old('title') }}" required>
                        </div>

                        <div class="field">
                            <label for="author_name">Auteur</label>
                            <input id="author_name" name="author_name" type="text" value="{{ old('author_name') }}" required>
                        </div>

                        <div class="field">
                            <label for="language">Langue</label>
                            <input id="language" name="language" type="text" value="{{ old('language', 'fr') }}" required>
                        </div>

                        <div class="field field-full">
                            <label for="genres">Genres</label>
                            <input id="genres" name="genres" type="text" value="{{ old('genres') }}" placeholder="fantasy, aventure, science-fiction">
                        </div>

                        <div class="field">
                            <label for="isbn">ISBN</label>
                            <input id="isbn" name="isbn" type="text" value="{{ old('isbn') }}">
                        </div>

                        <div class="field">
                            <label for="page_count">Nombre de pages</label>
                            <input id="page_count" name="page_count" type="number" min="1" value="{{ old('page_count') }}">
                        </div>

                        <div class="field">
                            <label for="published_at">Date de publication</label>
                            <input id="published_at" name="published_at" type="date" value="{{ old('published_at') }}">
                        </div>

                        <div class="field">
                            <label for="price">Prix</label>
                            <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', 0) }}">
                        </div>

                        <div class="field field-full">
                            <label for="description">Description</label>
                            <textarea id="description" name="description">{{ old('description') }}</textarea>
                        </div>

                        <div class="field field-full">
                            <label for="cover_file">Image de couverture</label>
                            <input id="cover_file" name="cover_file" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        </div>

                        <div class="field field-full">
                            <label for="book_file">Fichier du livre (PDF ou EPUB)</label>
                            <input id="book_file" name="book_file" type="file" accept=".pdf,.epub,application/pdf,application/epub+zip" required>
                        </div>

                        <div class="field field-full">
                            <label class="checkbox" for="is_published">
                                <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', '1') ? 'checked' : '' }}>
                                Rendre visible immédiatement
                            </label>
                        </div>

                        <div class="field field-full" style="margin-top: 8px;">
                            <button class="button button-accent" type="submit">Uploader le livre</button>
                        </div>
                    </div>
                </form>
            </section>

            <aside class="panel">
                <h2>Derniers ajouts</h2>
                <div class="list">
                    @forelse ($books as $book)
                        <article class="item">
                            <div class="item-head">
                                <div>
                                    <strong>{{ $book->title }}</strong>
                                    <p>{{ $book->author?->name ?? 'Auteur inconnu' }}</p>
                                </div>
                                <span class="tag" style="background: {{ $book->is_published ? 'rgba(77,216,154,.1)' : 'rgba(244,164,74,.1)' }}; color: {{ $book->is_published ? 'var(--ok)' : 'var(--accent)' }}">{{ $book->is_published ? 'Publié' : 'Brouillon' }}</span>
                            </div>
                            <div class="item-meta">
                                <span class="tag">{{ strtoupper($book->file_format) }}</span>
                                <span class="tag">{{ strtoupper($book->language) }}</span>
                                @foreach (array_slice($book->genres ?? [], 0, 2) as $genre)
                                    <span class="tag">{{ $genre }}</span>
                                @endforeach
                            </div>
                            <div class="item-actions">
                                <a class="link-button" href="{{ route('reader.show', $book) }}">Lire</a>
                                <a class="link-button" href="{{ route('admin.books.edit', $book) }}">Modifier</a>
                                <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre et son fichier ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger-button" type="submit">Supprimer</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="hint">Aucun livre n'a encore été ajouté à la bibliothèque.</div>
                    @endforelse
                </div>
            </aside>
        </div>

        @include('partials.app-footer')
    </div>
</body>
</html>
