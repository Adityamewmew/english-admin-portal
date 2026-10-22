@props([
    'label' => null,
    'name',
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autocomplete' => null,
    'error' => null,
    'size' => 'md',
])

@php
    $inputId = $id ?? $name;
    $inputValue = $value ?? old($name);

    $sizeClasses = match ($size) {
        'sm' => 'py-2 px-3 text-xs',
        'lg' => 'py-3.5 px-4 text-base',
        default => 'py-2.5 px-3.5 text-sm',
    };

    $baseInputClasses = "{$sizeClasses} block w-full bg-white border border-slate-300 rounded-[10px] text-slate-800 placeholder-slate-400 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 transition-all duration-150";
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-neutral-800' : '';
    $readonlyClass = $readonly ? 'bg-slate-50 dark:bg-neutral-800/50' : '';
    $errorClass = $error ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20' : '';
    
    $inputClasses = implode(
        ' ',
        array_filter([$baseInputClasses, $disabledClass, $readonlyClass, $errorClass, $attributes->get('class')]),
    );

    $inputAttributes = $attributes->merge([
        'id' => $inputId,
        'type' => $type,
        'name' => $name,
        'value' => $inputValue,
        'placeholder' => $placeholder,
        'required' => $required ? true : null,
        'disabled' => $disabled ? true : null,
        'readonly' => $readonly ? true : null,
        'autocomplete' => $autocomplete,
        'class' => $inputClasses,
    ]);
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <input {{ $inputAttributes }}>

    @if ($error)
        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $error }}</p>
    @endif
</div>
