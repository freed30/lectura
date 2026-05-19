<footer class="{{ $class ?? 'page-footer' }}" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-top:40px;padding:20px 24px;border-radius:20px;background:linear-gradient(145deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));border:1px solid rgba(255,255,255,0.06);color:var(--muted);font-size:0.85rem;backdrop-filter:blur(12px)">
    <div style="display:flex;align-items:center;gap:12px">
        <strong style="color:var(--text);font-family:'Playfair Display',serif;font-size:1.15rem;letter-spacing:0.02em;">{{ $title ?? 'Lectura' }}</strong>
        <span style="display:inline-block;width:1px;height:16px;background:rgba(255,255,255,0.15)"></span>
        <span style="opacity:0.8">© {{ date('Y') }} Tous droits réservés</span>
    </div>
    
    <div style="display:flex;align-items:center;gap:20px">
        <span style="opacity:0.8">{{ $message ?? 'Plateforme de lecture numérique' }}</span>
        <div style="display:flex;align-items:center;gap:8px;padding:5px 12px;border-radius:999px;background:rgba(77,216,154,0.1);border:1px solid rgba(77,216,154,0.2);color:var(--green, #4dd89a);font-size:0.75rem;font-weight:600" title="Tous les services sont opérationnels">
            <span style="width:6px;height:6px;border-radius:50%;background:var(--green, #4dd89a);box-shadow:0 0 8px var(--green, #4dd89a)"></span>
            Système en ligne
        </div>
    </div>
</footer>
