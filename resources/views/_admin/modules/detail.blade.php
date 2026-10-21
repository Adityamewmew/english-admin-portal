@extends('_admin._layout.app')

@section('title', 'Detail Modul: ' . $module->title)

@php
    $cefrColors = [
        'A1' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/50',
        'A2' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/40 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/50',
        'B1' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/50',
        'B2' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/50',
        'C1' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/50',
        'C2' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/50',
    ];
    $cefrClass = $cefrColors[$module->cefr] ?? 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700';

    $skillIcons = [
        'reading' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'listening' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>',
        'writing' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
        'speaking' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>',
        'assessment' => '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>',
    ];
    $defaultSkillIcon = '<svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>';
@endphp

@section('content')
    <div class="space-y-6">
        {{-- Header Card --}}
        <x-admin.card fit="true">
            <div class="p-2 sm:p-3">
                <div class="pb-4 border-b border-gray-100 dark:border-neutral-700/80 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <x-admin.button href="{{ route('admin.modules.index') }}" size="sm" color="outline-secondary" class="p-2!">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m12 19-7-7 7-7" />
                                <path d="M19 12H5" />
                            </svg>
                        </x-admin.button>
                        @if(!empty($module->banner_url))
                            <div class="size-14 rounded-xl overflow-hidden border border-gray-200 dark:border-neutral-700 shrink-0 bg-gray-50 dark:bg-neutral-800">
                                <img src="{{ $module->banner_url }}" alt="Banner" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded border border-blue-100 dark:border-blue-900/50">
                                    {{ $module->id }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs font-mono font-bold {{ $cefrClass }}">
                                    CEFR {{ $module->cefr }}
                                </span>
                                @if (!empty($module->is_exam))
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/50">
                                        Ujian Kelulusan
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ $module->title }}
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-admin.button href="{{ route('admin.modules.update', $module->id) }}" size="sm" color="outline-secondary">
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                            </svg>
                            Edit Info Modul
                        </x-admin.button>
                    </div>
                </div>

                <div class="pt-4">
                    <div class="mb-4">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-1">Tujuan Pembelajaran (Objective)</span>
                        <p class="text-sm text-gray-700 dark:text-neutral-300 bg-gray-50/80 dark:bg-neutral-900/50 p-3.5 rounded-xl border border-gray-100 dark:border-neutral-700/80 leading-relaxed">
                            {{ $module->objective }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                        <div class="p-3 bg-gray-50/80 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700/80">
                            <span class="text-xs text-gray-400 block mb-0.5">Level Kurikulum</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $module->level_title ?? $module->level_id }}</span>
                        </div>
                        <div class="p-3 bg-gray-50/80 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700/80">
                            <span class="text-xs text-gray-400 block mb-0.5">Estimasi Waktu</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">±{{ $module->estimated_minutes }} Menit</span>
                        </div>
                        <div class="p-3 bg-gray-50/80 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700/80">
                            <span class="text-xs text-gray-400 block mb-0.5">Passing Score</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ $module->passing_score }}%</span>
                        </div>
                        <div class="p-3 bg-gray-50/80 dark:bg-neutral-900/50 rounded-xl border border-gray-100 dark:border-neutral-700/80">
                            <span class="text-xs text-gray-400 block mb-0.5">Total Bagian Materi</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">{{ count($sections) }} Bagian</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-admin.card>

        {{-- Wizard Stepper Card (Shown when redirected after creating module or ?wizard=1) --}}
        @if(request('wizard') == 1)
            @php
                $wizardActiveSubModuleId = request('sub_module', $subModules->first()->id ?? '');
            @endphp
            <x-admin.card fit="true" class="border-blue-200 dark:border-blue-900/60 bg-blue-50/30 dark:bg-blue-950/20">
                <div class="p-2 sm:p-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-blue-100 dark:border-blue-900/50">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-xl bg-blue-600/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200/60 dark:border-blue-800/50">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                    <line x1="4" x2="4" y1="22" y2="15"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-blue-950 dark:text-blue-100">
                                    Alur Wizard: Pengisian Materi 5 Sub-Modul
                                </h2>
                                <p class="text-xs text-blue-800/80 dark:text-blue-300">
                                    Modul baru berhasil dibuat. 5 sub-modul skill telah disiapkan otomatis. Silakan lengkapi materi per skill secara berurutan.
                                </p>
                            </div>
                        </div>
                        <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}" size="sm" color="outline-secondary" class="self-start sm:self-auto text-xs shrink-0">
                            Selesai / Buka Mode Standar
                        </x-admin.button>
                    </div>

                    {{-- Stepper Progress Bar --}}
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        @foreach ($subModules as $stepIdx => $smStep)
                            @php
                                $isCurrent = ($smStep->id === $wizardActiveSubModuleId);
                                $stepSecCount = $sections->where('sub_module_id', $smStep->id)->count();
                                $typeKey = strtolower($smStep->skill_type);
                                $stepIcon = $skillIcons[$typeKey] ?? $defaultSkillIcon;
                            @endphp
                            <button type="button"
                                onclick="switchSubModuleTab('{{ $smStep->id }}')"
                                class="p-2.5 rounded-xl border text-left transition-all cursor-pointer {{ $isCurrent ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white dark:bg-neutral-800 border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-neutral-300 hover:border-blue-300' }}">
                                <div class="text-[11px] font-bold flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="opacity-80">{!! $stepIcon !!}</span>
                                        <span class="truncate">{{ $stepIdx + 1 }}. {{ ucfirst($typeKey) }}</span>
                                    </div>
                                    @if ($stepSecCount > 0)
                                        <span class="size-4 rounded-full {{ $isCurrent ? 'bg-white text-blue-600' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' }} flex items-center justify-center shrink-0">
                                            <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] mt-1 {{ $isCurrent ? 'text-white/80' : 'text-gray-400' }}">
                                    {{ $stepSecCount }} Bagian Materi
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </x-admin.card>
        @endif

        {{-- Sub-Modules Tabs Navigation --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        Struktur Materi Pembelajaran
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Pilih sub-modul (skill) di bawah untuk mengelola materi secara terfokus
                    </p>
                </div>
                <x-admin.button type="button"
                    data-hs-overlay="#sectionModal"
                    onclick="openAddSectionModal('{{ $subModules->first()->id ?? '' }}')"
                    class="py-2 px-4 rounded-xl text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14"/><path d="M12 5v14"/>
                    </svg>
                    Tambah Bagian Materi
                </x-admin.button>
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
                        class="submodule-tab-btn shrink-0 inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-semibold border transition-all cursor-pointer {{ $isActive ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:border-neutral-700 hover:border-gray-300 dark:hover:border-neutral-600' }}">
                        <span class="{{ $isActive ? 'text-white' : 'text-gray-500 dark:text-neutral-400' }}">{!! $tabIcon !!}</span>
                        <span>{{ $sm->title }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 dark:bg-neutral-700 dark:text-neutral-300' }}">
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
                        <div class="p-4 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/40">
                                    {!! $paneIcon !!}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                            Sub-Modul: {{ $sm->title }}
                                        </h3>
                                        <button type="button"
                                            data-hs-overlay="#editSubModuleModal"
                                            onclick="openEditSubModuleModal('{{ $sm->id }}', '{{ addslashes($sm->title) }}')"
                                            title="Ubah Nama Sub-Modul"
                                            class="p-1 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-gray-100 dark:hover:bg-neutral-700 dark:hover:text-blue-400 cursor-pointer transition-colors">
                                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-neutral-400">
                                        Skill: <strong class="uppercase text-blue-600 dark:text-blue-400">{{ $sm->skill_type }}</strong> &bull; Total {{ count($subSections) }} bagian materi
                                    </span>
                                </div>
                            </div>
                            <x-admin.button type="button"
                                size="sm"
                                color="primary"
                                data-hs-overlay="#sectionModal"
                                onclick="openAddSectionModal('{{ $sm->id }}')"
                                class="text-xs font-bold">
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                Tambah Materi di Sub-Modul Ini
                            </x-admin.button>
                        </div>

                        <div class="space-y-4">
                            @forelse ($subSections as $sec)
                                @include('_admin.modules.partials.section-card', [
                                    'sec' => $sec,
                                    'index' => $loop->iteration,
                                    'module' => $module,
                                ])
                            @empty
                                <div class="p-8 text-center bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 space-y-3 shadow-xs">
                                    <div class="size-12 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400 flex items-center justify-center mx-auto">
                                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white">
                                        Belum ada materi pada sub-modul {{ $sm->title }}
                                    </div>
                                    <p class="text-xs text-gray-500 max-w-sm mx-auto">
                                        Mulai tambahkan teori, kosakata, percakapan, atau kuis pilihan ganda untuk sub-modul ini.
                                    </p>
                                    <x-admin.button type="button"
                                        data-hs-overlay="#sectionModal"
                                        onclick="openAddSectionModal('{{ $sm->id }}')"
                                        class="py-2 px-4 rounded-xl text-xs font-bold">
                                        + Tambah Bagian Pertama
                                    </x-admin.button>
                                </div>
                            @endforelse
                        </div>

                        {{-- Wizard Next Step Footer --}}
                        @if(request('wizard') == 1)
                            @php
                                $currentIndex = $subModules->search(fn($item) => $item->id === $sm->id);
                                $nextSubModule = $subModules->get($currentIndex + 1);
                            @endphp
                            <div class="p-4 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                                <div class="text-xs text-gray-600 dark:text-neutral-400">
                                    @if ($nextSubModule)
                                        Tahap berikutnya: <strong class="text-gray-900 dark:text-white">{{ $nextSubModule->title }}</strong>
                                    @else
                                        Semua 5 tahapan sub-modul selesai dikonfigurasi!
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($nextSubModule)
                                        <x-admin.button type="button"
                                            onclick="switchSubModuleTab('{{ $nextSubModule->id }}')"
                                            color="primary"
                                            size="sm">
                                            <span>Lanjut ke Sub-Modul {{ $nextSubModule->title }}</span>
                                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </x-admin.button>
                                    @else
                                        <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}"
                                            color="success"
                                            size="sm">
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
    @include('_admin.modules.partials.section-modal', ['module' => $module, 'subModules' => $subModules])
    @include('_admin.modules.partials.submodule-modal', ['module' => $module])
    @include('_admin.modules.partials.delete-section-modal', ['module' => $module])

@endsection

@push('scripts')
    @include('_admin.modules.partials.section-scripts', ['module' => $module])
@endpush
