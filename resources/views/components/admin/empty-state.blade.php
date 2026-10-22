@props([
    'title' => 'Belum ada data',
    'message' => 'Data tidak ditemukan atau belum ditambahkan.',
    'action' => null,
])

<div class="flex flex-col items-center justify-center py-10 px-4 text-center">
    <div class="size-16 rounded-2xl bg-surface border border-slate-200 border-b-3 border-b-slate-300 flex items-center justify-center text-primary/60 mb-3.5 shadow-2xs">
        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
    </div>
    <h4 class="font-extrabold text-base text-primary dark:text-white mb-1">
        {{ $title }}
    </h4>
    <p class="text-xs sm:text-sm text-slate-500 dark:text-neutral-400 max-w-sm mb-4">
        {{ $message }}
    </p>
    @if ($action)
        <div>{!! $action !!}</div>
    @endif
    @if ($slot->isNotEmpty())
        <div>{{ $slot }}</div>
    @endif
</div>
