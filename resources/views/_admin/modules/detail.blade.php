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
    $firstSubModule = $subModules->first();
    $activeSubModuleId = request('sub_module', $firstSubModule->id ?? 'all');
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
                            @if(!empty($module->is_active))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-500 text-white shadow-2xs">
                                    <span class="size-1.5 rounded-full bg-white"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-600 text-white shadow-2xs">
                                    <span class="size-1.5 rounded-full bg-slate-300"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        <h1 class="font-heading font-extrabold text-xl text-primary dark:text-white mt-1.5 tracking-tight">
                            {{ $module->title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.modules.toggleStatus', $module->id) }}" method="POST">
                        @csrf
                        <x-admin.button type="submit"
                            size="sm"
                            :color="!empty($module->is_active) ? 'outline-secondary' : 'primary'">
                            @if(!empty($module->is_active))
                                <svg class="size-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <span>Nonaktifkan</span>
                            @else
                                <svg class="size-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Aktifkan Modul</span>
                            @endif
                        </x-admin.button>
                    </form>

                    <x-admin.button href="{{ route('admin.modules.update', $module->id) }}" color="secondary" size="sm">
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
                    <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}" color="outline-secondary" size="sm">
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
                            data-step-id="{{ $smStep->id }}"
                            class="submodule-wizard-step p-2.5 rounded-xl border text-left transition-all cursor-pointer {{ $isCurrent ? 'btn-tactile-primary !shadow-md' : 'btn-tactile-outline' }}">
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
                            <div class="step-count text-[10px] mt-1 {{ $isCurrent ? 'text-accent' : 'text-slate-400' }}">
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
                <div class="flex flex-wrap items-center gap-2">
                    <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun 5 Sub-Modul Sekaligus...', 'AI sedang menyusun materi Reading, Listening, Writing, Speaking, dan Assessment lengkap...')">
                        @csrf
                        <input type="hidden" name="scope" value="all">
                        <x-admin.button type="submit" size="sm" color="outline-secondary">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Generate Semua Sub-Modul
                        </x-admin.button>
                    </form>

                    <x-admin.button type="button"
                        color="secondary"
                        data-hs-overlay="#sectionModal"
                        onclick="openAddSectionModal(window.currentActiveSubModuleId || '{{ $subModules->first()->id ?? '' }}')"
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
                        <span class="tab-icon {{ $isActive ? 'text-accent' : 'text-slate-400' }}">{!! $tabIcon !!}</span>
                        <span>{{ $sm->title }}</span>
                        <span class="tab-count px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $isActive ? 'bg-white/20 text-accent' : 'bg-slate-200 text-slate-700 dark:bg-neutral-700 dark:text-neutral-300' }}">
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
                        $currentIndex = $subModules->search(fn($item) => $item->id === $sm->id);
                        $nextSubModule = $subModules->get($currentIndex + 1);
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
                                             {{ $sm->title }}
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
                                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun Sub-Modul {{ addslashes($sm->title) }}...', 'Sedang menyusun materi untuk sub-modul ini...')">
                                    @csrf
                                    <input type="hidden" name="scope" value="single">
                                    <input type="hidden" name="sub_module_id" value="{{ $sm->id }}">
                                    <x-admin.button type="submit" size="sm" color="outline-secondary">
                                        <svg class="size-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <span>Generate Materi</span>
                                    </x-admin.button>
                                </form>
                                <x-admin.button type="button"
                                    size="sm"
                                    color="secondary"
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
                                <x-admin.card variant="white" padding="p-8">
                                    <x-admin.empty-state
                                        title="Belum ada materi pada sub-modul {{ $sm->title }}"
                                        message="Mulai tambahkan teori, kosakata, percakapan, atau kuis pilihan ganda untuk sub-modul ini.">
                                        <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                                            <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun Sub-Modul {{ addslashes($sm->title) }}...', 'Sedang menyusun materi untuk sub-modul ini...')">
                                                @csrf
                                                <input type="hidden" name="scope" value="single">
                                                <input type="hidden" name="sub_module_id" value="{{ $sm->id }}">
                                                <input type="hidden" name="is_progressive" value="1">
                                                <x-admin.button type="submit" size="sm" color="secondary">
                                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                    </svg>
                                                    <span>Buat Materi Otomatis</span>
                                                </x-admin.button>
                                            </form>
                                            <x-admin.button type="button"
                                                color="outline-secondary"
                                                data-hs-overlay="#sectionModal"
                                                onclick="openAddSectionModal('{{ $sm->id }}')"
                                                size="sm">
                                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                                <span>Tambah Manual</span>
                                            </x-admin.button>
                                        </div>
                                    </x-admin.empty-state>
                                </x-admin.card>
                            @endforelse
                        </div>

                        {{-- Progressive Review & Confirmation Bar (Ya / Tidak) --}}
                        @if ($subSections->count() > 0)
                            <div class="p-4 rounded-2xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 card-tactile shadow-xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-extrabold text-xs text-emerald-900 dark:text-emerald-200">
                                                Materi Sub-Modul {{ $sm->title }} ({{ strtoupper($sm->skill_type) }}) Siap Ditinjau
                                            </h4>
                                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">
                                                Apakah materi di atas sudah sesuai?
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($nextSubModule)
                                            @php
                                                $nextSecCount = $sections->where('sub_module_id', $nextSubModule->id)->count();
                                             @endphp
                                            @if ($nextSecCount > 0)
                                                <x-admin.button type="button"
                                                    color="secondary"
                                                    size="sm"
                                                    onclick="switchSubModuleTab('{{ $nextSubModule->id }}')">
                                                    <span>Sudah Sesuai &bull; Lanjut Tinjau {{ $nextSubModule->title }}</span>
                                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                                    </svg>
                                                </x-admin.button>
                                            @else
                                                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun Sub-Modul {{ addslashes($nextSubModule->title) }}...', 'Sedang menyusun materi untuk tahap berikutnya...')">
                                                    @csrf
                                                    <input type="hidden" name="scope" value="single">
                                                    <input type="hidden" name="sub_module_id" value="{{ $nextSubModule->id }}">
                                                    <input type="hidden" name="is_progressive" value="1">
                                                    <x-admin.button type="submit" color="secondary" size="sm">
                                                        <span>Sudah Sesuai &bull; Buat Materi {{ $nextSubModule->title }}</span>
                                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                                        </svg>
                                                    </x-admin.button>
                                                </form>
                                            @endif
                                        @else
                                            <x-admin.button href="{{ route('admin.modules.index') }}" color="primary" size="sm">
                                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                                <span>Selesai &bull; Semua Sub-Modul Lengkap</span>
                                            </x-admin.button>
                                        @endif

                                        <x-admin.button type="button"
                                            color="outline-secondary"
                                            size="sm"
                                            onclick="toggleRegenerateBox('{{ $sm->id }}')">
                                            <svg class="size-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            <span>Generate Ulang</span>
                                        </x-admin.button>
                                    </div>
                                </div>

                                <div id="regen-box-{{ $sm->id }}" class="hidden mt-3 pt-3 border-t border-emerald-200 dark:border-emerald-800/50">
                                    <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" class="space-y-2.5" onsubmit="showAiOverlay('Memperbarui Sub-Modul {{ addslashes($sm->title) }}...', 'Sedang menyusun ulang materi dengan catatan Anda...')">
                                        @csrf
                                        <input type="hidden" name="scope" value="single">
                                        <input type="hidden" name="sub_module_id" value="{{ $sm->id }}">
                                        <input type="hidden" name="is_progressive" value="1">
                                        <input type="hidden" name="replace" value="1">
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-neutral-300">
                                            Catatan Perbaikan / Arahan Revisi (Opsional):
                                        </label>
                                        <textarea name="custom_instructions" rows="2" class="w-full text-xs rounded-xl border border-slate-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-2.5 text-slate-800 dark:text-neutral-200 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)]" placeholder="Misal: Tambahkan kosakata tentang daily habits, soal kuis dibuat lebih aplikatif..."></textarea>
                                        <div class="flex items-center gap-2">
                                            <x-admin.button type="submit" color="secondary" size="sm">
                                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                                <span>Proses Generate Ulang</span>
                                            </x-admin.button>
                                            <x-admin.button type="button" color="outline-secondary" size="sm" onclick="toggleRegenerateBox('{{ $sm->id }}')">
                                                Batal
                                            </x-admin.button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- DEDICATED MODAL NOTIFIKASI GENERATE AI --}}
    <div id="aiGeneratingOverlay" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 transition-all">
        <div class="relative w-full max-w-lg bg-white dark:bg-neutral-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-neutral-700 shadow-2xl text-center overflow-hidden card-tactile">
            {{-- Top Accent Stripe --}}
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-amber-500 via-secondary to-amber-600"></div>

            {{-- Animated AI Emblem --}}
            <div class="relative mx-auto flex items-center justify-center size-20 mb-5">
                <div class="absolute inset-0 rounded-2xl bg-secondary/20 animate-ping"></div>
                <div class="relative size-16 rounded-2xl bg-secondary border border-amber-300/40 flex items-center justify-center shadow-lg text-white">
                    <svg class="size-8 text-white animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
            </div>

            {{-- Title & Subtitle --}}
            <h3 id="aiOverlayTitle" class="text-lg sm:text-xl font-heading font-extrabold text-primary dark:text-white tracking-tight mb-2">
                Sedang Menyusun Materi Kurikulum dengan AI...
            </h3>
            <p id="aiOverlaySubtitle" class="text-xs sm:text-sm text-slate-500 dark:text-neutral-400 max-w-sm mx-auto leading-relaxed mb-6">
                AI sedang menganalisis kurikulum CEFR dan menyusun kosakata, teori, percakapan, dan kuis.
            </p>

            {{-- Live Progress Step Card --}}
            <div class="p-4 rounded-2xl bg-surface dark:bg-neutral-800/80 border border-slate-200 dark:border-neutral-700 text-left space-y-2.5 mb-5 shadow-2xs">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                    <span>Status Aktivitas AI</span>
                    <span class="inline-flex items-center gap-1.5 text-secondary">
                        <span class="size-2 rounded-full bg-secondary animate-pulse"></span>
                        Sedang Berjalan
                    </span>
                </div>
                <div class="flex items-center gap-2.5 text-xs font-semibold text-primary dark:text-white">
                    <svg class="animate-spin size-4 text-secondary shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="aiOverlayStatusText" class="truncate">Menganalisis kurikulum CEFR & konteks modul...</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-amber-400 to-secondary rounded-full animate-pulse w-3/4"></div>
                </div>
            </div>

            {{-- Warning / Notice Footer --}}
            <div class="inline-flex items-center gap-2 text-[11px] text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 px-3.5 py-2 rounded-xl border border-amber-200 dark:border-amber-800/60 font-medium">
                <svg class="size-4 shrink-0 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Estimasi 15–30 detik. Harap jangan menutup atau memuat ulang halaman.</span>
            </div>
        </div>
    </div>

    {{-- Modals (Pure CRUD only, zero AI modal bloat) --}}
    @include('_admin.modules.partials.section-modal', ['module' => $module, 'subModules' => $subModules])
    @include('_admin.modules.partials.submodule-modal', ['module' => $module])
    @include('_admin.modules.partials.delete-section-modal', ['module' => $module])
@endsection

@push('scripts')
    @include('_admin.modules.partials.page-scripts', ['module' => $module, 'activeSubModuleId' => $activeSubModuleId, 'subModules' => $subModules])
    @include('_admin.modules.partials.practicum-scripts')
    @include('_admin.modules.partials.section-scripts', ['module' => $module, 'activeSubModuleId' => $activeSubModuleId, 'subModules' => $subModules])
@endpush
