<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lectura | {{ $status }}</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #050b14;
            --panel: rgba(10, 20, 32, 0.78);
            --line: rgba(169, 214, 255, 0.16);
            --text: #eef6ff;
            --muted: #9db4cd;
            --accent: #ffb45d;
            font-family: "Trebuchet MS", sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(132, 216, 255, 0.18), transparent 22%),
                radial-gradient(circle at top right, rgba(255, 180, 93, 0.14), transparent 18%),
                linear-gradient(180deg, #040912, #09111c 42%, #050b14);
            padding: 20px;
        }

        .card {
            width: min(720px, 100%);
            padding: 34px;
            border-radius: 34px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(13, 24, 38, 0.82), rgba(8, 15, 24, 0.66));
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.38);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.05);
            color: #cbe2fb;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }

        h1 {
            margin: 18px 0 12px;
            font-family: Georgia, serif;
            font-size: clamp(2.8rem, 8vw, 4.4rem);
            line-height: 0.92;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
            font-size: 1rem;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            text-decoration: none;
        }

        .button-accent {
            background: linear-gradient(90deg, var(--accent), #ffd7a2);
            border-color: transparent;
            color: #1c1208;
            font-weight: 800;
        }

        .detail {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            color: var(--muted);
        }

        @media (max-width: 540px) {
            .card {
                padding: 24px;
                border-radius: 26px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <section class="card">
        <span class="badge">Erreur {{ $status }}</span>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>

        @if (!empty($detail))
            <div class="detail">{{ $detail }}</div>
        @endif

        <div class="actions">
            <a class="button button-accent" href="{{ $primaryUrl }}">{{ $primaryLabel }}</a>
            <a class="button" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
                {{ auth()->check() ? 'Aller au dashboard' : 'Se connecter' }}
            </a>
        </div>
    </section>
</body>
</html>
