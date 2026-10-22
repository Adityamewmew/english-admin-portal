@props([
    'id',
    'title' => '',
    'icon' => null,
    'size' => 'sm:max-w-xl',
    'formAction' => null,
    'formMethod' => 'POST',
    'formClass' => '',
    'formId' => null,
    'footer' => null,
])

<div id="{{ $id }}"
    class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none"
    role="dialog" tabindex="-1" aria-labelledby="{{ $id }}-label">
    <div
        class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-300 mt-0 opacity-0 ease-out transition-all {{ $size }} sm:w-full m-3 sm:mx-auto">
        <div
            class="card-tactile bg-white rounded-2xl overflow-hidden pointer-events-auto dark:bg-neutral-900 dark:border-neutral-700">

            {{-- Header --}}
            <div
                class="flex justify-between items-center py-4 px-6 border-b border-slate-200/80 bg-surface/50 dark:bg-neutral-800 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    @if ($icon)
                        <div class="p-2 bg-white rounded-xl border border-slate-200 text-primary shadow-2xs dark:bg-neutral-700 dark:border-neutral-600">
                            {!! $icon !!}
                        </div>
                    @endif
                    <h2 id="{{ $id }}-label" class="font-extrabold text-lg text-primary dark:text-white">
                        {{ $title }}
                    </h2>
                </div>
                <button type="button"
                    class="size-8 inline-flex justify-center items-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-800 hover:bg-slate-50 disabled:opacity-50 transition-colors cursor-pointer dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400"
                    aria-label="Close" data-hs-overlay="#{{ $id }}">
                    <span class="sr-only">Close</span>
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            @if ($formAction)
                <form id="{{ $formId }}" action="{{ $formAction }}"
                    method="{{ strtoupper($formMethod) === 'GET' ? 'GET' : 'POST' }}"
                    {{ $attributes->merge(['class' => $formClass]) }} navigate-form>
                    @unblaze
                        @csrf
                    @endunblaze
                    @if (!in_array(strtoupper($formMethod), ['GET', 'POST']))
                        @method($formMethod)
                    @endif
            @endif

            {{-- Body --}}
            <div class="p-6 overflow-y-auto">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if ($footer)
                <div
                    class="flex justify-end items-center gap-x-2 py-3.5 px-6 border-t border-slate-200/80 bg-slate-50/50 dark:bg-neutral-800 dark:border-neutral-700">
                    {{ $footer }}
                </div>
            @elseif($formAction)
                <div
                    class="flex justify-end items-center gap-x-2 py-3.5 px-6 border-t border-slate-200/80 bg-slate-50/50 dark:bg-neutral-800 dark:border-neutral-700">
                    <x-admin.button type="button" color="outline-secondary" size="sm" data-hs-overlay="#{{ $id }}">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary" size="sm">
                        Simpan
                    </x-admin.button>
                </div>
            @endif

            @if ($formAction)
                </form>
            @endif
        </div>
    </div>
</div>
