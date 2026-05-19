@php
    $status = 419;
    $title = 'Session expiree';
    $message = 'Votre session utilisateur a expire. Reconnectez-vous pour reprendre votre lecture.';
    $detail = 'Cela peut arriver apres une longue inactivite ou un rafraichissement tardif du formulaire.';
    $primaryLabel = 'Revenir a la connexion';
    $primaryUrl = route('login');
@endphp

@include('errors.layout')
