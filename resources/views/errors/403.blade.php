@php
    $status = 403;
    $title = 'Acces refuse';
    $message = 'Cette zone du site n est pas accessible avec votre niveau d autorisation.';
    $detail = $exception->getMessage() ?: 'Connectez-vous avec un compte autorise pour continuer.';
    $primaryLabel = auth()->check() ? 'Retour a la bibliotheque' : 'Aller a la connexion';
    $primaryUrl = auth()->check() ? route('reader.index') : route('login');
@endphp

@include('errors.layout')
