<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lectura | Connexion</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/lectura-logo-3d.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#ffb45d',
                        accent2: '#6dd3ff',
                    },
                    boxShadow: {
                        glow: '0 28px 80px rgba(0, 0, 0, 0.38)',
                    },
                    fontFamily: {
                        display: ['Georgia', 'serif'],
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 text-slate-100">
    <div class="pointer-events-none fixed inset-0">
        <div class="absolute left-0 top-0 h-72 w-72 rounded-full bg-cyan-400/15 blur-3xl"></div>
        <div class="absolute right-0 top-0 h-80 w-80 rounded-full bg-amber-400/15 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-fuchsia-500/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-5 sm:px-6 lg:px-8">
        <header class="rounded-[1.9rem] border border-white/10 bg-white/5 shadow-glow backdrop-blur-2xl">
            <div class="flex flex-col gap-5 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-4">
                    <img
                        src="{{ asset('images/branding/lectura-logo-3d.png') }}"
                        alt="Lectura"
                        class="h-16 w-16 rounded-[1.3rem] border border-white/10 object-cover shadow-2xl"
                    >
                    <div>
                        <strong class="block font-display text-2xl text-white">Lectura</strong>
                        <span class="block text-sm uppercase tracking-[0.24em] text-slate-400">Secure reading access</span>
                    </div>
                </div>

                <nav class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('register', [], false) }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-400/10 px-4 py-2.5 text-sm font-semibold text-cyan-50 transition hover:-translate-y-0.5 hover:bg-cyan-400/15"
                    >
                        Creer un compte
                    </a>
                    <a
                        href="/"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-100 transition hover:-translate-y-0.5 hover:bg-white/10"
                    >
                        Accueil
                    </a>
                </nav>
            </div>
        </header>

        <main class="flex-1 py-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_460px]">
                <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-glow backdrop-blur-2xl">
                    <div class="flex h-full flex-col justify-between p-6 sm:p-8">
                        <div>
                            <span class="inline-flex rounded-full border border-cyan-300/20 bg-cyan-400/10 px-4 py-1.5 text-xs font-medium uppercase tracking-[0.28em] text-cyan-100">
                                Connexion Lectura
                            </span>
                            <h1 class="mt-6 max-w-3xl font-display text-4xl leading-tight text-white sm:text-5xl">
                                Reprenez vos livres, votre progression et votre univers de lecture.
                            </h1>
                            <p class="mt-5 max-w-2xl text-base leading-8 text-slate-300">
                                Votre espace Lectura centralise l'historique de lecture, les suggestions personnalisees,
                                les favoris et la reprise automatique des livres PDF et EPUB.
                            </p>
                        </div>

                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <article class="rounded-[1.6rem] border border-white/10 bg-slate-950/45 p-5">
                                <strong class="block text-2xl text-white">PDF</strong>
                                <p class="mt-2 text-sm leading-7 text-slate-300">Lecture directe dans le navigateur avec reprise automatique.</p>
                            </article>
                            <article class="rounded-[1.6rem] border border-white/10 bg-slate-950/45 p-5">
                                <strong class="block text-2xl text-white">EPUB</strong>
                                <p class="mt-2 text-sm leading-7 text-slate-300">Experience immersive adaptee aux preferences du lecteur.</p>
                            </article>
                            <article class="rounded-[1.6rem] border border-white/10 bg-slate-950/45 p-5">
                                <strong class="block text-2xl text-white">Smart</strong>
                                <p class="mt-2 text-sm leading-7 text-slate-300">Suggestions basees sur vos recherches, vos genres et vos lectures.</p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-glow backdrop-blur-2xl sm:p-8">
                    <div class="mb-6">
                        <p class="text-sm uppercase tracking-[0.26em] text-slate-400">Acces securise</p>
                        <h2 class="mt-3 font-display text-3xl text-white">Bienvenue sur votre compte</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Connectez-vous pour retrouver votre session utilisateur, vos preferences et votre historique de lecture.
                        </p>
                    </div>

                    @include('partials.flash-messages')

                    @if ($errors->any())
                        <div class="mb-5 rounded-[1.5rem] border border-rose-300/20 bg-rose-400/10 px-4 py-4 text-sm text-rose-100">
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store', [], false) }}" class="grid gap-5">
                        @csrf

                        <label class="grid gap-2 text-sm text-slate-300" for="email">
                            <span>Email</span>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300/40"
                            >
                        </label>

                        <label class="grid gap-2 text-sm text-slate-300" for="password">
                            <span>Mot de passe</span>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none transition focus:border-cyan-300/40"
                            >
                        </label>

                        <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-4 text-sm text-slate-200" for="remember">
                            <input id="remember" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-white/20 bg-slate-900 text-cyan-400">
                            <span>Se souvenir de moi sur cet appareil</span>
                        </label>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-amber-400 to-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5"
                        >
                            Se connecter
                        </button>
                    </form>

                    <div class="mt-6 flex flex-col gap-3 text-sm text-slate-300 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('register', [], false) }}" class="transition hover:text-white">Je n'ai pas encore de compte</a>
                        <a href="/" class="transition hover:text-white">Retour a l'accueil</a>
                    </div>
                </section>
            </div>
        </main>

        @include('partials.app-footer', [
            'class' => 'flex flex-col items-start justify-between gap-4 rounded-[1.75rem] border border-white/10 bg-white/5 px-6 py-5 text-sm text-slate-300 shadow-glow backdrop-blur-xl sm:flex-row sm:items-center',
            'title' => 'Lectura',
            'message' => 'Connexion securisee, lecture immersive et identite visuelle developpees par Dongmo Joan.',
        ])
    </div>
</body>
</html>
