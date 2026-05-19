@php
    $status = 500;
    $title = 'Erreur interne';
    $message = 'Une erreur inattendue est survenue sur le site. Vous pouvez reessayer dans un instant.';
    $detail = 'Si le probleme continue, reconnectez-vous puis reprenez la navigation depuis le dashboard ou la bibliotheque.';
    $primaryLabel = auth()->check() ? 'Retour a la bibliotheque' : 'Aller a la connexion';
    $primaryUrl = auth()->check() ? route('reader.index') : route('login');
@endphp

@include('errors.layout')
