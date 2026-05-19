@php
    $flashMessages = collect([
        [
            'key' => 'status',
            'label' => 'Succes',
            'border' => 'rgba(127, 224, 188, 0.26)',
            'background' => 'rgba(127, 224, 188, 0.12)',
            'color' => '#7fe0bc',
        ],
        [
            'key' => 'notification_status',
            'label' => 'Notification',
            'border' => 'rgba(132, 216, 255, 0.24)',
            'background' => 'rgba(132, 216, 255, 0.1)',
            'color' => '#9edfff',
        ],
        [
            'key' => 'success',
            'label' => 'Succes',
            'border' => 'rgba(127, 224, 188, 0.26)',
            'background' => 'rgba(127, 224, 188, 0.12)',
            'color' => '#7fe0bc',
        ],
        [
            'key' => 'info',
            'label' => 'Information',
            'border' => 'rgba(132, 216, 255, 0.24)',
            'background' => 'rgba(132, 216, 255, 0.1)',
            'color' => '#9edfff',
        ],
        [
            'key' => 'warning',
            'label' => 'Attention',
            'border' => 'rgba(255, 180, 93, 0.28)',
            'background' => 'rgba(255, 180, 93, 0.12)',
            'color' => '#ffd6a0',
        ],
        [
            'key' => 'error',
            'label' => 'Erreur',
            'border' => 'rgba(255, 159, 143, 0.28)',
            'background' => 'rgba(255, 159, 143, 0.12)',
            'color' => '#ffb7aa',
        ],
    ])->filter(fn ($item) => session()->has($item['key']));
@endphp

@if ($flashMessages->isNotEmpty())
    <div style="display:grid;gap:12px;margin:18px 0;">
        @foreach ($flashMessages as $message)
            <div style="padding:14px 16px;border-radius:18px;border:1px solid {{ $message['border'] }};background:{{ $message['background'] }};color:{{ $message['color'] }};">
                <strong style="display:block;margin-bottom:6px;">{{ $message['label'] }}</strong>
                <span>{{ session($message['key']) }}</span>
            </div>
        @endforeach
    </div>
@endif
