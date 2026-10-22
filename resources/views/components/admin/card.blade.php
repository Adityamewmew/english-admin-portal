@props([
    'variant' => 'white', // 'white', 'surface', 'achievement'
    'fit' => false,
    'padding' => 'p-5 sm:p-6',
])

@php
    $heightClass = $fit ? 'h-auto' : 'h-full';

    $variantClasses = match ($variant) {
        'surface' => 'card-tactile-surface text-primary',
        'achievement' => 'card-tactile-achievement text-primary',
        default => 'card-tactile bg-white text-slate-800 dark:bg-neutral-800 dark:border-neutral-700',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl {$padding} {$heightClass} {$variantClasses} transition-all duration-150"]) }}>
    {{ $slot }}
</div>
