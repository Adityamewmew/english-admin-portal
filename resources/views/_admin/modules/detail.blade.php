@extends('_admin._layout.app')

@section('title', 'Detail Modul: ' . $module->title)

@php
    $skillIcons = [
        'reading' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'listening' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>',
        'writing' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
        'speaking' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>',
        'assessment' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>',
    ];
    $defaultSkillIcon = '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>';
@endphp

@section('content')
    <div class="space-y-6">
        {{-- Header Card --}}
        <x-admin.card variant="white">
            <div class="pb-4 border-b border-slate-200 dark:border-neutral-700/80 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.modules.index') }}"
                        class="btn-tactile btn-tactile-outline !py-2 !px-2.5 !rounded-xl"
                        title="Kembali ke Daftar Modul">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                    </a>
                    @if(!empty($module->banner_url))
                        <div class="size-14 rounded-xl overflow-hidden border border-slate-200 dark:border-neutral-700 shrink-0 bg-surface shadow-xs">
                            <img src="{{ $module->banner_url }}" alt="Banner" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-primary dark:text-accent bg-surface dark:bg-neutral-800 px-2.5 py-0.5 rounded-lg border border-slate-300 dark:border-neutral-700 shadow-2xs">
                                {{ $module->id }}
                            </span>
                            <x-admin.badge type="cefr" :value="$module->cefr" />
                            @if (!empty($module->is_exam))
                                <x-admin.badge type="status" value="review">Ujian Kelulusan</x-admin.badge>
                            @endif
                        </div>
                        <h1 class="font-heading font-extrabold text-xl text-primary dark:text-white mt-1.5 tracking-tight">
                            {{ $module->title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-admin.button href="{{ route('admin.modules.update', $module->id) }}" variant="secondary" size="sm" as="a">
                        <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                        </svg>
                        Edit Info Modul
                    </x-admin.button>
                </div>
            </div>

            <div class="pt-4">
                <div class="mb-4">
                    <span class="text-xs font-heading font-extrabold text-primary/70 uppercase tracking-wider block mb-1.5">Tujuan Pembelajaran (Objective)</span>
                    <p class="text-sm font-medium text-slate-700 dark:text-neutral-300 bg-surface dark:bg-neutral-900/50 p-4 rounded-xl border border-slate-200 dark:border-neutral-700/80 leading-relaxed shadow-[inset_0_1px_2px_rgba(0,0,0,0.03)]">
                        {{ $module->objective }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                    <div class="p-3.5 bg-surface dark:bg-neutral-900/50 rounded-xl border border-slate-200 dark:border-neutral-700/80 shadow-2xs">
                        <span class="text-[11px] font-bold text-slate-400 block mb-0.5 uppercase tracking-wide">Level Kurikulum</span>
                        <span class="text-sm font-heading font-extrabold text-primary dark:text-neutral-200">{{ $module->level_title ?? $module->level_id }}</span>
                    </div>
                    <div class="p-3.5 bg-surface dark:bg-neutral-900/50 rounded-xl border border-slate-200 dark:border-neutral-700/80 shadow-2xs">
                        <span class="text-[11px] font-bold text-slate-400 block mb-0.5 uppercase tracking-wide">Estimasi Waktu</span>
                        <span class="text-sm font-heading font-extrabold text-primary dark:text-neutral-200">±{{ $module->estimated_minutes }} Menit</span>
                    </div>
                    <div class="p-3.5 bg-surface dark:bg-neutral-900/50 rounded-xl border border-slate-200 dark:border-neutral-700/80 shadow-2xs">
                        <span class="text-[11px] font-bold text-slate-400 block mb-0.5 uppercase tracking-wide">Passing Score</span>
                        <span class="text-sm font-heading font-extrabold text-primary dark:text-neutral-200">{{ $module->passing_score }}%</span>
                    </div>
                    <div class="p-3.5 bg-surface dark:bg-neutral-900/50 rounded-xl border border-slate-200 dark:border-neutral-700/80 shadow-2xs">
                        <span class="text-[11px] font-bold text-slate-400 block mb-0.5 uppercase tracking-wide">Total Bagian Materi</span>
                        <span class="text-sm font-heading font-extrabold text-primary dark:text-neutral-200">{{ count($sections) }} Bagian</span>
                    </div>
                </div>
            </div>
        </x-admin.card>

        {{-- Wizard Stepper Card (Shown when redirected after creating module or ?wizard=1) --}}
        @if(request('wizard') == 1)
            @php
                $wizardActiveSubModuleId = request('sub_module', $subModules->first()->id ?? '');
            @endphp
            <x-admin.card variant="surface" class="border-secondary/30">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-200 dark:border-neutral-700">
                    <div class="flex items-center gap-3">
                        <div class="size-9 rounded-xl bg-secondary text-white flex items-center justify-center shrink-0 border-b-2 border-amber-800 shadow-xs">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                <line x1="4" x2="4" y1="22" y2="15"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-heading font-extrabold text-sm text-primary dark:text-white">
                                Alur Wizard: Pengisian Materi 5 Sub-Modul
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-neutral-400">
                                5 sub-modul skill telah disiapkan otomatis. Silakan lengkapi materi per skill secara berurutan.
                            </p>
                        </div>
                    </div>
                    <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}" variant="outline" size="sm" as="a">
                        Selesai / Buka Mode Standar
                    </x-admin.button>
                </div>

                {{-- Stepper Progress Bar --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    @foreach ($subModules as $stepIdx => $smStep)
                        @php
                            $isCurrent = ($smStep->id === $wizardActiveSubModuleId);
                            $stepSecCount = $sections->where('sub_module_id', $smStep->id)->count();
                            $typeKey = strtolower($smStep->skill_type);
                            $stepIcon = $skillIcons[$typeKey] ?? $defaultSkillIcon;
                        @endphp
                        <button type="button"
                            onclick="switchSubModuleTab('{{ $smStep->id }}')"
                            class="p-2.5 rounded-xl border text-left transition-all cursor-pointer {{ $isCurrent ? 'btn-tactile-primary !shadow-md' : 'btn-tactile-outline' }}">
                            <div class="text-[11px] font-heading font-extrabold flex items-center justify-between">
                                <div class="flex items-center gap-1.5 truncate">
                                    <span class="opacity-90">{!! $stepIcon !!}</span>
                                    <span class="truncate">{{ $stepIdx + 1 }}. {{ ucfirst($typeKey) }}</span>
                                </div>
                                @if ($stepSecCount > 0)
                                    <span class="size-4 rounded-full {{ $isCurrent ? 'bg-accent text-primary' : 'bg-emerald-100 text-emerald-800' }} flex items-center justify-center shrink-0">
                                        <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </span>
                                @endif
                            </div>
                            <div class="text-[10px] mt-1 {{ $isCurrent ? 'text-accent' : 'text-slate-400' }}">
                                {{ $stepSecCount }} Bagian Materi
                            </div>
                        </button>
                    @endforeach
                </div>
            </x-admin.card>
        @endif

        {{-- Sub-Modules Tabs Navigation --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="font-heading font-extrabold text-base text-primary dark:text-white flex items-center gap-2">
                        Struktur Materi Pembelajaran
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">
                        Pilih sub-modul (skill) di bawah untuk mengelola materi secara terfokus
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <x-admin.button type="button"
                        variant="outline"
                        onclick="openAiGenerateModal('', '{{ addslashes($module->title) }}', 'all')"
                        size="sm">
                        <svg class="size-4 text-secondary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                        <span>✨ AI Generate Materi</span>
                    </x-admin.button>
                    <x-admin.button type="button"
                        variant="secondary"
                        data-hs-overlay="#sectionModal"
                        onclick="openAddSectionModal('{{ $subModules->first()->id ?? '' }}')"
                        size="sm">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12h14"/><path d="M12 5v14"/>
                        </svg>
                        Tambah Bagian Manual
                    </x-admin.button>
                </div>
            </div>

            @php
                $activeSubModuleId = request('sub_module', $subModules->first()->id ?? 'all');
            @endphp

            {{-- Skill Tab Buttons --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                @foreach ($subModules as $sm)
                    @php
                        $subSecCount = $sections->where('sub_module_id', $sm->id)->count();
                        $isActive = ($sm->id === $activeSubModuleId);
                        $typeKey = strtolower($sm->skill_type);
                        $tabIcon = $skillIcons[$typeKey] ?? $defaultSkillIcon;
                    @endphp
                    <button type="button"
                        onclick="switchSubModuleTab('{{ $sm->id }}')"
                        data-tab="{{ $sm->id }}"
                        class="submodule-tab-btn shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-heading font-extrabold border transition-all cursor-pointer {{ $isActive ? 'btn-tactile-primary' : 'btn-tactile-outline' }}">
                        <span class="{{ $isActive ? 'text-accent' : 'text-slate-400' }}">{!! $tabIcon !!}</span>
                        <span>{{ $sm->title }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $isActive ? 'bg-white/20 text-accent' : 'bg-slate-200 text-slate-700 dark:bg-neutral-700 dark:text-neutral-300' }}">
                            {{ $subSecCount }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Tab Panes --}}
            <div class="mt-4">
                @foreach ($subModules as $sm)
                    @php
                        $subSections = $sections->where('sub_module_id', $sm->id)->sortBy('order_index');
                        $isActive = ($sm->id === $activeSubModuleId);
                        $typeKey = strtolower($sm->skill_type);
                        $paneIcon = $skillIcons[$typeKey] ?? $defaultSkillIcon;
                    @endphp

                    <div id="pane-{{ $sm->id }}" class="submodule-tab-pane space-y-4 {{ $isActive ? '' : 'hidden' }}">
                        <div class="p-4 rounded-2xl bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex flex-wrap items-center justify-between gap-3 card-tactile">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-surface dark:bg-neutral-700 text-primary dark:text-accent flex items-center justify-center shrink-0 border border-slate-200 dark:border-neutral-600 shadow-2xs">
                                    {!! $paneIcon !!}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-heading font-extrabold text-sm text-primary dark:text-white">
                                            Sub-Modul: {{ $sm->title }}
                                        </h3>
                                        <button type="button"
                                            data-hs-overlay="#editSubModuleModal"
                                            onclick="openEditSubModuleModal('{{ $sm->id }}', '{{ addslashes($sm->title) }}')"
                                            title="Ubah Nama Sub-Modul"
                                            class="p-1 rounded-lg text-slate-400 hover:text-secondary hover:bg-surface dark:hover:bg-neutral-700 cursor-pointer transition-colors">
                                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <span class="text-xs text-slate-500 dark:text-neutral-400">
                                        Skill: <strong class="uppercase text-secondary">{{ $sm->skill_type }}</strong> &bull; Total {{ count($subSections) }} bagian materi
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-admin.button type="button"
                                    size="sm"
                                    variant="outline"
                                    onclick="openAiGenerateModal('{{ $sm->id }}', '{{ addslashes($sm->title) }}', '{{ $sm->skill_type }}')">
                                    <svg class="size-3.5 text-secondary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                    <span>✨ AI Generate</span>
                                </x-admin.button>
                                <x-admin.button type="button"
                                    size="sm"
                                    variant="secondary"
                                    data-hs-overlay="#sectionModal"
                                    onclick="openAddSectionModal('{{ $sm->id }}')">
                                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                    <span>Tambah Manual</span>
                                </x-admin.button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse ($subSections as $sec)
                                @include('_admin.modules.partials.section-card', [
                                    'sec' => $sec,
                                    'index' => $loop->iteration,
                                    'module' => $module,
                                ])
                            @empty
                                <div class="p-8 text-center bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 space-y-3 card-tactile">
                                    <div class="size-12 rounded-2xl bg-surface text-primary dark:text-accent flex items-center justify-center mx-auto border border-slate-200">
                                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                        </svg>
                                    </div>
                                    <div class="font-heading font-extrabold text-sm text-primary dark:text-white">
                                        Belum ada materi pada sub-modul {{ $sm->title }}
                                    </div>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                        Mulai tambahkan teori, kosakata, percakapan, atau kuis pilihan ganda untuk sub-modul ini.
                                    </p>
                                    <div class="flex items-center justify-center gap-2 pt-2">
                                        <x-admin.button type="button"
                                            variant="secondary"
                                            onclick="openAiGenerateModal('{{ $sm->id }}', '{{ addslashes($sm->title) }}', '{{ $sm->skill_type }}')"
                                            size="sm">
                                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                            <span>✨ Buat Materi Otomatis dengan AI</span>
                                        </x-admin.button>
                                        <x-admin.button type="button"
                                            variant="outline"
                                            data-hs-overlay="#sectionModal"
                                            onclick="openAddSectionModal('{{ $sm->id }}')"
                                            size="sm">
                                            + Tambah Manual
                                        </x-admin.button>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        {{-- Wizard Next Step Footer --}}
                        @if(request('wizard') == 1)
                            @php
                                $currentIndex = $subModules->search(fn($item) => $item->id === $sm->id);
                                $nextSubModule = $subModules->get($currentIndex + 1);
                            @endphp
                            <div class="p-4 rounded-2xl bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex flex-wrap items-center justify-between gap-3 card-tactile">
                                <div class="text-xs text-slate-600 dark:text-neutral-400">
                                    @if ($nextSubModule)
                                        Tahap berikutnya: <strong class="font-heading font-bold text-primary dark:text-white">{{ $nextSubModule->title }}</strong>
                                    @else
                                        Semua 5 tahapan sub-modul selesai dikonfigurasi!
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($nextSubModule)
                                        <x-admin.button type="button"
                                            onclick="switchSubModuleTab('{{ $nextSubModule->id }}')"
                                            variant="secondary"
                                            size="sm">
                                            <span>Lanjut ke Sub-Modul {{ $nextSubModule->title }}</span>
                                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </x-admin.button>
                                    @else
                                        <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}"
                                            variant="secondary"
                                            size="sm"
                                            as="a">
                                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span>Selesai Setup Modul</span>
                                        </x-admin.button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('_admin.modules.partials.ai-generate-modal', ['module' => $module, 'subModules' => $subModules])
    @include('_admin.modules.partials.section-modal', ['module' => $module, 'subModules' => $subModules])
    @include('_admin.modules.partials.submodule-modal', ['module' => $module])
    @include('_admin.modules.partials.delete-section-modal', ['module' => $module])

@endsection

@push('scripts')
    @include('_admin.modules.partials.section-scripts', ['module' => $module])
@endpush
