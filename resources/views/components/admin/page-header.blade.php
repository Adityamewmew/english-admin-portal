@props([
    'title' => '',
    'subtitle' => null,
    'backUrl' => null,
])

<div class="py-2 sm:py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-x-3.5">
        @if ($backUrl)
            <x-admin.button href="{{ $backUrl }}" size="sm" color="outline-secondary" class="shrink-0">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </x-admin.button>
        @endif
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-primary dark:text-white tracking-tight">
                {{ $title }}
            </h1>
            @if ($subtitle)
                <p class="text-xs sm:text-sm text-slate-500 dark:text-neutral-400 mt-1 font-normal">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-2 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>
