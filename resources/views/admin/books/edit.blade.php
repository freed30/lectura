<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lectura | Modifier {{ $book->title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
    <style>
        :root {
            color-scheme: dark;
            --bg: #0f0c0a;
            --panel: rgba(31, 24, 19, 0.92);
            --soft: rgba(42, 34, 28, 0.86);
            --line: rgba(255, 233, 204, 0.1);
            --text: #f8efe2;
            --muted: #c8b39b;
            --accent: #edb364;
            --ok: #93d4b1;
            --danger: #ff9a86;
            font-family: "Segoe UI", sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(237, 179, 100, 0.16), transparent 30%),
                linear-gradient(150deg, #0b0908, #19120f 55%, #0f0c0a);
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
            background: linear-gradient(145deg, rgba(48, 36, 28, 0.95), rgba(24, 20, 17, 0.95));
        }

        .hero h1 {
            margin: 10px 0 8px;
            font-size: clamp(2rem, 4vw, 3.2rem);
            line-height: 0.96;
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
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 221, 173, 0.08);
            color: #ffd89c;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.82rem;
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
        }

        .button-accent {
            background: linear-gradient(90deg, var(--accent), #ffd38b);
            color: #27180e;
            border-color: transparent;
            font-weight: 700;
        }

        .danger-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid rgba(255, 154, 134, 0.28);
            background: rgba(255, 154, 134, 0.12);
            color: var(--danger);
        }
        .hero-signature{display:inline-flex;align-items:center;margin-top:16px;padding:8px 14px;border-radius:999px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);color:#ffd89c;font-size:.82rem;letter-spacing:.08em;text-transform:uppercase}
        .page-footer{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:24px;padding:18px 20px;border-radius:24px;border:1px solid var(--line);background:rgba(255,255,255,.04);color:var(--muted)}
        .page-footer strong{color:var(--text);font-size:1rem}

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 20px;
            margin-top: 24px;
        }

        .panel {
            padding: 22px;
            border-radius: 26px;
            border: 1px solid var(--line);
            background: var(--panel);
            box-shadow: 0 24px 72px rgba(0, 0, 0, 0.24);
        }

        .panel h2 {
            margin: 0 0 6px;
            font-size: 1.35rem;
        }

        .panel p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.65;
        }

        .status {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(147, 212, 177, 0.12);
            border: 1px solid rgba(147, 212, 177, 0.24);
            color: var(--ok);
        }

        .errors {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 154, 134, 0.1);
            border: 1px solid rgba(255, 154, 134, 0.24);
            color: var(--danger);
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
            font-size: 0.94rem;
        }

        input,
        textarea {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--soft);
            color: var(--text);
        }

        textarea {
            min-height: 130px;
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
        }

        .hint,
        .current-file,
        .item {
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.03);
            color: var(--muted);
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .item strong {
            display: block;
            color: var(--text);
            margin-bottom: 6px;
        }

        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            font-size: 0.82rem;
        }

        code,
        .tag,
        .current-file,
        .item {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        @media (max-width: 920px) {
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .shell { width: min(100% - 20px, 1180px); }
            .hero { padding: 20px; align-items: start; flex-direction: column; }
            .form-grid { grid-template-columns: 1fr; }
            .hero .actions,
            .hero .actions form,
            .hero .button,
            .hero .danger-button { width: 100%; }
        }

        @media (max-width: 520px) {
            .panel { padding: 18px; border-radius: 22px; }
            .hero { border-radius: 24px; }
            .actions,
            .actions form,
            .button,
            .button-accent,
            .danger-button { width: 100%; }
            .page-footer { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <div>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;">
                    <img src="{{ asset('images/branding/lectura-logo-3d.png') }}" alt="Lectura" style="width:60px;height:60px;border-radius:18px;object-fit:cover;border:1px solid rgba(255,255,255,.08);box-shadow:0 18px 40px rgba(0,0,0,.24);">
                    <span class="badge">admin edition</span>
                </div>
                <h1>Modifier {{ $book->title }}</h1>
                <p>
                    Mettez a jour les informations du livre, remplacez le fichier si besoin,
                    puis supprimez proprement l’ancien fichier apres remplacement.
                </p>
                <div class="hero-signature">Developed by Dongmo Joan</div>
            </div>
            <div class="actions">
                <a class="button" href="{{ route('admin.dashboard') }}">Admin dashboard</a>
                <a class="button" href="{{ route('reader.show', $book) }}">Ouvrir le lecteur</a>
                <a class="button" href="{{ route('admin.books.create') }}">Retour upload</a>
            </div>
        </section>

        @include('partials.flash-messages')

        <div class="grid">
            <section class="panel">
                <h2>Edition du livre</h2>
                <p>Le remplacement du fichier est optionnel. Si vous laissez ce champ vide, le fichier actuel est conserve.</p>

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

                <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="field field-full">
                            <label for="title">Titre du livre</label>
                            <input id="title" name="title" type="text" value="{{ old('title', $book->title) }}" required>
                        </div>

                        <div class="field">
                            <label for="author_name">Auteur</label>
                            <input id="author_name" name="author_name" type="text" value="{{ old('author_name', $book->author?->name) }}" required>
                        </div>

                        <div class="field">
                            <label for="language">Langue</label>
                            <input id="language" name="language" type="text" value="{{ old('language', $book->language) }}" required>
                        </div>

                        <div class="field field-full">
                            <label for="genres">Genres</label>
                            <input id="genres" name="genres" type="text" value="{{ old('genres', implode(', ', $book->genres ?? [])) }}" placeholder="fantasy, aventure, science-fiction">
                        </div>

                        <div class="field">
                            <label for="isbn">ISBN</label>
                            <input id="isbn" name="isbn" type="text" value="{{ old('isbn', $book->isbn) }}">
                        </div>

                        <div class="field">
                            <label for="page_count">Nombre de pages</label>
                            <input id="page_count" name="page_count" type="number" min="1" value="{{ old('page_count', $book->page_count) }}">
                        </div>

                        <div class="field">
                            <label for="published_at">Date de publication</label>
                            <input id="published_at" name="published_at" type="date" value="{{ old('published_at', optional($book->published_at)->format('Y-m-d')) }}">
                        </div>

                        <div class="field">
                            <label for="price">Prix</label>
                            <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $book->price) }}">
                        </div>

                        <div class="field field-full">
                            <label for="description">Description</label>
                            <textarea id="description" name="description">{{ old('description', $book->description) }}</textarea>
                        </div>

                        <div class="field field-full">
                            <label for="cover_file">Nouvelle image de couverture</label>
                            <input id="cover_file" name="cover_file" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        </div>

                        <div class="field field-full">
                            <label for="book_file">Nouveau fichier du livre (PDF ou EPUB)</label>
                            <input id="book_file" name="book_file" type="file" accept=".pdf,.epub,application/pdf,application/epub+zip">
                        </div>

                        <div class="field field-full">
                            <label class="checkbox" for="is_published">
                                <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', $book->is_published) ? 'checked' : '' }}>
                                Laisser ce livre visible dans la bibliotheque
                            </label>
                        </div>

                        <div class="field field-full">
                            <button class="button button-accent" type="submit">Enregistrer les modifications</button>
                        </div>
                    </div>
                </form>
            </section>

            <aside class="panel">
                <h2>Fichier actuel</h2>
                <div class="current-file" style="margin-bottom: 14px;">
                    <strong style="display:block;color: var(--text);margin-bottom:10px;">Couverture actuelle</strong>
                    <img
                        src="{{ $book->display_cover_url }}"
                        alt="Couverture de {{ $book->title }}"
                        style="display:block;width:100%;max-width:260px;border-radius:18px;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.04);object-fit:cover;"
                    >
                </div>
                <div class="current-file">
                    Format: <strong style="color: var(--text);">{{ strtoupper($book->file_format) }}</strong><br>
                    Chemin: <code>{{ $book->fichier_path }}</code>
                </div>

                <div class="hint" style="margin-top: 16px;">
                    La suppression du livre retire aussi le fichier stocke dans
                    <code>storage/app/public/books</code> si ce fichier existe encore sur le disque.
                </div>

                <div class="actions" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre et son fichier ?');">
                        @csrf
                        @method('DELETE')
                        <button class="danger-button" type="submit">Supprimer le livre</button>
                    </form>
                </div>

                <h2 style="margin-top: 22px;">Derniers livres ajoutes</h2>
                <div class="list">
                    @foreach ($books as $item)
                        <article class="item">
                            <strong>{{ $item->title }}</strong>
                            <div>{{ $item->author?->name ?? 'Auteur inconnu' }}</div>
                            <div class="tag-row">
                                <span class="tag">{{ strtoupper($item->file_format) }}</span>
                                <span class="tag">{{ $item->language }}</span>
                                <span class="tag">{{ $item->is_published ? 'publie' : 'brouillon' }}</span>
                                @foreach (($item->genres ?? []) as $genre)
                                    <span class="tag">{{ $genre }}</span>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </aside>
        </div>

        @include('partials.app-footer')
    </div>
</body>
</html>
