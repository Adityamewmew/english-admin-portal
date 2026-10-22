@props([
    'label' => null,
    'name',
    'id' => null,
    'options' => [],
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'value' => null,
    'size' => 'md',
])
@php
    $selectId = $id ?? $name;

    $sizeClasses = match ($size) {
        'sm' => 'py-2 px-3 text-xs',
        'lg' => 'py-3.5 px-4 text-base',
        default => 'py-2.5 px-3.5 text-sm',
    };

    $baseClasses = "{$sizeClasses} block w-full bg-white border border-slate-300 rounded-[10px] text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 transition-all duration-150";
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed bg-slate-100' : '';
    $errorClass = $error ? 'border-rose-400 focus:border-rose-500' : '';
    $customClass = $attributes->get('class');
    $selectClass = trim(implode(' ', array_filter([$baseClasses, $disabledClass, $errorClass, $customClass])));
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif
    <select id="{{ $selectId }}" name="{{ $name }}" class="{{ $selectClass }}"
        {{ $disabled ? 'disabled' : '' }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if (!empty($options))
            @foreach ($options as $optionValue => $text)
                <option value="{{ $optionValue }}" {{ (old($name) ?? $value) == $optionValue ? 'selected' : '' }}>
                    {{ $text }}</option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    @if ($error)
        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $error }}</p>
    @endif
</div>
