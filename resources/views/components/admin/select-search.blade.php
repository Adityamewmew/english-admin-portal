@props([
    'label',
    'name',
    'id' => null,
    'options' => [],
    'readonlyValue' => null,
    'readonlyText' => null,
    'readonlySubtext' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'value' => null,
    'searchPlaceholder' => 'Cari...',
    'size' => 'md',
    'class' => '',
])
@php
    $selectId = $id ?? $name;
    $isReadonly = !empty($readonlyValue);

    $sizeClasses = match ($size) {
        'sm' => 'py-1.5 px-3 pe-9 text-xs',
        'lg' => 'py-3.5 px-4 pe-9 text-base',
        default => 'py-2.5 px-3.5 pe-9 text-sm',
    };

    $hsSelectConfig =
        '{
        "hasSearch": true,
        "searchPlaceholder": "' .
        $searchPlaceholder .
        '",
        "searchClasses": "block w-full text-sm bg-surface border-slate-300 rounded-[8px] text-slate-800 placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:placeholder-neutral-500 py-2 px-3",
        "searchWrapperClasses": "bg-white dark:bg-neutral-800 p-2 sticky top-0 border-b border-slate-200 dark:border-neutral-700",
        "placeholder": "' .
        ($placeholder ?: 'Pilih...') .
        '",
        "toggleTag": "<button type=\"button\" aria-expanded=\"false\"><span class=\"me-2\" data-icon></span><span class=\"text-slate-800 dark:text-neutral-200 font-medium\" data-title></span></button>",
        "toggleClasses": "hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 relative ' .
        $sizeClasses .
        ' flex items-center text-nowrap w-full cursor-pointer bg-white dark:bg-neutral-900 border border-slate-300 dark:border-neutral-700 text-slate-800 dark:text-neutral-200 rounded-[10px] text-start hover:bg-slate-50 focus:outline-hidden focus:border-primary focus:ring-2 focus:ring-primary/20 dark:hover:bg-neutral-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] transition-all",
        "dropdownClasses": "mt-2 max-h-72 pb-1 px-1 space-y-0.5 z-20 w-full bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl shadow-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:size-2 [&::-webkit-scrollbar-thumb]:bg-slate-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500",
        "optionClasses": "hs-selected:bg-surface hs-selected:text-primary hs-selected:font-bold py-2 px-3.5 w-full text-sm text-slate-700 dark:text-neutral-200 cursor-pointer hover:bg-slate-100 dark:hover:bg-neutral-700 rounded-lg focus:outline-hidden focus:bg-slate-100 transition-colors",
        "optionTemplate": "<div><div class=\"flex items-center\"><div class=\"me-2\" data-icon></div><div class=\"text-slate-800 dark:text-neutral-200\" data-title></div></div></div>",
        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-slate-400 opacity-70\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
    }';
@endphp
<div class="space-y-1.5 {{ $class }}">
    @if ($label)
        <label for="{{ $selectId }}" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    @if ($isReadonly)
        <input type="hidden" name="{{ $name }}" value="{{ $readonlyValue }}">
        <div
            class="py-2 px-3 block w-full bg-gray-50 border-gray-200 rounded-lg text-sm dark:bg-neutral-900/50 dark:border-neutral-700 dark:text-neutral-400 font-bold">
            {{ $readonlyText }}
            @if ($readonlySubtext)
                <span class="ms-1 text-xs font-normal text-gray-500 uppercase">{{ $readonlySubtext }}</span>
            @endif
        </div>
    @else
        <select id="{{ $selectId }}" name="{{ $name }}" data-hs-select="{{ $hsSelectConfig }}"
            class="hidden">
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $optValue => $text)
                <option value="{{ $optValue }}" {{ (old($name) ?? $value) == $optValue ? 'selected' : '' }}>
                    {{ $text }}</option>
            @endforeach
        </select>
    @endif

    @if ($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @endif
</div>
