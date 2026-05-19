@php
    $status = 404;
    $title = 'Contenu introuvable';
    $message = 'La page, le livre ou la ressource demandee est introuvable pour le moment.';
    $detail = 'Verifiez le lien utilise ou revenez a la bibliotheque pour continuer.';
    $primaryLabel = auth()->check() ? 'Ouvrir la bibliotheque' : 'Aller a la connexion';
    $primaryUrl = auth()->check() ? route('reader.index') : route('login');
@endphp

@include('errors.layout')
