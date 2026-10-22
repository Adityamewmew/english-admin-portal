@props([
    'size' => 'md',
    'color' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'class' => '',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'py-1.5 px-3 text-xs rounded-md',
        'md' => 'py-2.5 px-4 text-sm rounded-lg',
        'lg' => 'py-3 px-5 text-base rounded-lg',
        'xl' => 'py-3.5 px-6 text-lg rounded-xl',
        default => 'py-2.5 px-4 text-sm rounded-lg',
    };

    $colorClasses = match ($color) {
        'primary' => 'btn-tactile-primary',
        'secondary' => 'btn-tactile-secondary',
        'accent' => 'btn-tactile-accent',
        'danger' => 'btn-tactile-danger',
        'success' => 'btn-tactile-success',
        'warning' => 'btn-tactile-accent',
        'outline-primary' => 'btn-tactile-outline text-primary border-primary/40',
        'outline-secondary', 'outline' => 'btn-tactile-outline',
        'outline-danger' => 'btn-tactile-outline text-danger border-danger/30 hover:bg-rose-50',
        'outline-success' => 'btn-tactile-outline text-success border-success/30 hover:bg-emerald-50',
        default => 'btn-tactile-primary',
    };

    $baseClasses = 'btn-tactile inline-flex items-center justify-center gap-x-2 font-medium focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none';
    $classes = implode(' ', array_filter([$class, $baseClasses, $sizeClasses, $colorClasses]));

    $attributes = $attributes->merge([
        'type' => $href ? null : $type,
        'href' => $href,
        'disabled' => $disabled ? true : null,
        'class' => $classes . ' cursor-pointer',
    ]);
@endphp

@if ($href)
    <a navigate {{ $attributes }}>
        @if ($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            {!! $icon !!}
        @endif
    </a>
@else
    <button {{ $attributes }}>
        @if ($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'right')
            {!! $icon !!}
        @endif
    </button>
@endif
