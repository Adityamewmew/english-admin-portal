@props([
    'variant' => 'neutral', // 'achievement', 'primary', 'secondary', 'basic', 'pro', 'ultra', 'success', 'danger', 'warning', 'info', 'neutral'
    'size' => 'md', // 'sm', 'md', 'lg'
    'cefr' => null, // 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'
    'icon' => null,
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'py-0.5 px-2 text-[10px]',
        'lg' => 'py-1.5 px-3.5 text-xs font-bold',
        default => 'py-1 px-2.5 text-xs font-semibold',
    };

    if ($cefr) {
        $cefrColors = [
            'A1' => 'bg-[#0D3B66] text-white border-b-2 border-[#072440]',
            'A2' => 'bg-[#107C41] text-white border-b-2 border-[#0A522A]',
            'B1' => 'bg-[#0078D4] text-white border-b-2 border-[#005A9E]',
            'B2' => 'bg-[#D83B01] text-white border-b-2 border-[#9E2B00]',
            'C1' => 'bg-[#744DA9] text-white border-b-2 border-[#4E3272]',
            'C2' => 'bg-[#C239B3] text-white border-b-2 border-[#8C2781]',
        ];
        $variantClasses = $cefrColors[strtoupper($cefr)] ?? 'bg-primary text-white border-b-2 border-primary-dark';
    } else {
        $variantClasses = match ($variant) {
            'achievement' => 'bg-accent text-primary border border-amber-300 border-b-2 border-b-accent-dark font-bold shadow-xs',
            'primary' => 'bg-primary text-white border-b-2 border-primary-dark shadow-xs',
            'secondary' => 'bg-secondary text-white border-b-2 border-secondary-dark shadow-xs',
            'basic' => 'bg-slate-100 text-slate-700 border border-slate-200 border-b-2 border-b-slate-300',
            'pro' => 'bg-indigo-50 text-indigo-900 border border-indigo-200 border-b-2 border-b-indigo-300 font-bold',
            'ultra' => 'bg-amber-50 text-amber-900 border border-amber-200 border-b-2 border-b-amber-400 font-extrabold',
            'success' => 'bg-emerald-50 text-emerald-800 border border-emerald-200 border-b-2 border-b-emerald-300',
            'danger' => 'bg-rose-50 text-rose-800 border border-rose-200 border-b-2 border-b-rose-300',
            'warning' => 'bg-amber-50 text-amber-800 border border-amber-200 border-b-2 border-b-amber-300',
            'info' => 'bg-sky-50 text-sky-800 border border-sky-200 border-b-2 border-b-sky-300',
            default => 'bg-slate-100 text-slate-700 border border-slate-200 border-b-2 border-b-slate-300',
        };
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-2xl {$sizeClasses} {$variantClasses} tracking-tight select-none"]) }}>
    @if ($icon)
        <span class="shrink-0">{!! $icon !!}</span>
    @endif
    {{ $slot }}
</span>
