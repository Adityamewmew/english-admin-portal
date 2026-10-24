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

        {{-- Master Action Hub: 3 Pilihan Generate (Semua, Progresif, Manual) --}}
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 card-tactile shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-neutral-700/80">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/></svg>
                            Opsi Penyusunan Materi Modul
                        </span>
                        @if ($hasCachedDoc)
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-800 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                                <svg class="size-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Dokumen Bahan Ajar Siap: {{ $cachedDocName ?: 'Terdeteksi' }}
                            </span>
                        @else
                            <button type="button" onclick="document.getElementById('hubDocInputWrap').classList.toggle('hidden')" class="text-[11px] font-semibold text-secondary hover:underline cursor-pointer">
                                + Unggah Dokumen Bahan Ajar (Opsional)
                            </button>
                        @endif
                    </div>
                    <h3 class="font-heading font-extrabold text-base text-primary dark:text-white pt-1">
                        Pilih Metode Pembuatan Materi 5 Sub-Modul
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">
                        Pilih untuk menghasilkan seluruh materi secara instan, menyusun progresif tahap demi tahap dengan review, atau menginput manual.
                    </p>
                </div>
            </div>

            {{-- Optional Document Upload Dropdown in Hub if none exists --}}
            <div id="hubDocInputWrap" class="hidden pt-3 pb-1 border-b border-slate-100 dark:border-neutral-700">
                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-3">
                    @csrf
                    <input type="hidden" name="scope" value="all">
                    <input type="file" name="document" accept=".pdf,.docx,.doc,.jpg,.jpeg,.png" class="py-1.5 px-3 block w-full text-xs border border-slate-300 rounded-lg file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-bold file:bg-primary file:text-white">
                    <button type="submit" onclick="showAiOverlay('Memindai & Menyusun Modul...', 'AI sedang memproses dokumen baru Anda...')" class="btn-tactile btn-tactile-secondary shrink-0 !py-1.5 !px-3 text-xs font-bold cursor-pointer">
                        Unggah & Generate Semua
                    </button>
                </form>
            </div>

            {{-- The 3 prominent choices --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 pt-4">
                {{-- 1. Generate Semua --}}
                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun 5 Sub-Modul Sekaligus...', 'AI sedang menyusun materi Reading, Listening, Writing, Speaking, dan Assessment lengkap...')">
                    @csrf
                    <input type="hidden" name="scope" value="all">
                    <button type="submit" class="w-full h-full text-left p-4 rounded-xl border border-secondary/40 bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent hover:border-secondary transition-all cursor-pointer group shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="size-9 rounded-xl bg-secondary text-white flex items-center justify-center font-bold shadow-xs">
                                ✨
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-secondary bg-secondary/15 px-2 py-0.5 rounded-md">Instan (1 Klik)</span>
                        </div>
                        <div class="font-heading font-extrabold text-sm text-primary dark:text-white group-hover:text-secondary transition-colors">
                            Generate Modul Semua
                        </div>
                        <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 leading-relaxed">
                            Otomatis menyusun materi lengkap untuk ke-5 sub-modul (Reading, Listening, Writing, Speaking, Test) sekaligus.
                        </p>
                    </button>
                </form>

                {{-- 2. Generate Progresif --}}
                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Memulai Alur Progresif...', 'AI sedang menyusun materi sub-modul Reading terlebih dahulu...')">
                    @csrf
                    <input type="hidden" name="scope" value="single">
                    <input type="hidden" name="sub_module_id" value="{{ $subModules->first()->id ?? '' }}">
                    <input type="hidden" name="is_progressive" value="1">
                    <button type="submit" class="w-full h-full text-left p-4 rounded-xl border border-primary/30 bg-primary/5 hover:border-primary transition-all cursor-pointer group shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="size-9 rounded-xl bg-primary text-accent flex items-center justify-center font-bold shadow-xs">
                                🚀
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary dark:text-accent bg-primary/15 px-2 py-0.5 rounded-md">Rekomendasi</span>
                        </div>
                        <div class="font-heading font-extrabold text-sm text-primary dark:text-white group-hover:text-primary transition-colors">
                            Generate Sub Module (Progresif)
                        </div>
                        <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 leading-relaxed">
                            Mulai dari Reading &rarr; Konfirmasi Ya/Tidak &rarr; Listening sampai Test. Anda dapat meninjau hasil sebelum lanjut.
                        </p>
                    </button>
                </form>

                {{-- 3. Manual --}}
                <button type="button" data-hs-overlay="#sectionModal" onclick="openAddSectionModal(window.currentActiveSubModuleId || '{{ $activeSubModuleId }}')" class="w-full h-full text-left p-4 rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 hover:border-slate-400 transition-all cursor-pointer group shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="size-9 rounded-xl bg-slate-200 dark:bg-neutral-700 text-slate-700 dark:text-white flex items-center justify-center font-bold shadow-xs">
                            ✏️
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-200/60 dark:bg-neutral-700 px-2 py-0.5 rounded-md">Bebas AI</span>
                    </div>
                    <div class="font-heading font-extrabold text-sm text-primary dark:text-white group-hover:text-primary transition-colors">
                        Susun Materi Manual
                    </div>
                    <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 leading-relaxed">
                        Input manual kosakata, teks penjelasan tata bahasa, baris dialog percakapan, dan soal kuis pilihan ganda.
                    </p>
                </button>
            </div>
        </div>

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
                        variant="secondary"
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
                                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun Sub-Modul {{ addslashes($sm->title) }}...', 'AI sedang menyusun materi untuk sub-modul ini...')">
                                    @csrf
                                    <input type="hidden" name="scope" value="single">
                                    <input type="hidden" name="sub_module_id" value="{{ $sm->id }}">
                                    <button type="submit" class="btn-tactile btn-tactile-outline !py-1.5 !px-3 text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer">
                                        <svg class="size-3.5 text-secondary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                        <span>✨ AI Generate Sub-Modul Ini</span>
                                    </button>
                                </form>
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
                                        <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun Sub-Modul {{ addslashes($sm->title) }}...', 'AI sedang menyusun materi untuk sub-modul ini...')">
                                            @csrf
                                            <input type="hidden" name="scope" value="single">
                                            <input type="hidden" name="sub_module_id" value="{{ $sm->id }}">
                                            <input type="hidden" name="is_progressive" value="1">
                                            <button type="submit" class="btn-tactile btn-tactile-secondary !py-2 !px-4 text-xs font-bold inline-flex items-center gap-2 cursor-pointer shadow-xs">
                                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                                <span>✨ Buat Materi Otomatis dengan AI</span>
                                            </button>
                                        </form>
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
                                                <button type="button" onclick="switchSubModuleTab('{{ $nextSubModule->id }}')" class="btn-tactile btn-tactile-secondary !py-2 !px-3.5 text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                                                    <span>✅ Ya, Sudah Sesuai &bull; Lanjut Tinjau {{ $nextSubModule->title }}</span>
                                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" onsubmit="showAiOverlay('Menyusun Sub-Modul {{ addslashes($nextSubModule->title) }}...', 'AI sedang menyusun materi untuk tahap berikutnya...')">
                                                    @csrf
                                                    <input type="hidden" name="scope" value="single">
                                                    <input type="hidden" name="sub_module_id" value="{{ $nextSubModule->id }}">
                                                    <input type="hidden" name="is_progressive" value="1">
                                                    <button type="submit" class="btn-tactile btn-tactile-secondary !py-2 !px-3.5 text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                                                        <span>✅ Ya, Sudah Sesuai &bull; Buat Materi {{ $nextSubModule->title }}</span>
                                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('admin.modules.index') }}" class="btn-tactile btn-tactile-primary !py-2 !px-3.5 text-xs font-bold inline-flex items-center gap-1.5 shadow-xs">
                                                <span>🎉 Ya, Selesai! Semua Tahapan Berhasil</span>
                                            </a>
                                        @endif

                                        <button type="button" onclick="toggleRegenerateBox('{{ $sm->id }}')" class="btn-tactile btn-tactile-outline !py-2 !px-3 text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="size-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                                            <span>🔄 Tidak, Generate Ulang</span>
                                        </button>
                                    </div>
                                </div>

                                <div id="regen-box-{{ $sm->id }}" class="hidden mt-3 pt-3 border-t border-emerald-200 dark:border-emerald-800/50">
                                    <form action="{{ route('admin.modules.ai_generate', $module->id) }}" method="POST" class="space-y-2.5" onsubmit="showAiOverlay('Memperbarui Sub-Modul {{ addslashes($sm->title) }}...', 'AI sedang menyusun ulang materi dengan catatan Anda...')">
                                        @csrf
                                        <input type="hidden" name="scope" value="single">
                                        <input type="hidden" name="sub_module_id" value="{{ $sm->id }}">
                                        <input type="hidden" name="is_progressive" value="1">
                                        <input type="hidden" name="replace" value="1">
                                        <label class="block text-[11px] font-bold text-slate-700 dark:text-neutral-300">
                                            Catatan Perbaikan / Arahan Revisi (Opsional):
                                        </label>
                                        <textarea name="custom_instructions" rows="2" class="w-full text-xs rounded-xl border border-slate-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-2.5 text-slate-800 dark:text-neutral-200" placeholder="Misal: Tambahkan kosakata tentang daily habits, soal kuis dibuat lebih aplikatif..."></textarea>
                                        <div class="flex items-center gap-2">
                                            <button type="submit" class="btn-tactile btn-tactile-secondary !py-1.5 !px-3 text-xs font-bold cursor-pointer">
                                                ⚡ Proses Generate Ulang
                                            </button>
                                            <button type="button" onclick="toggleRegenerateBox('{{ $sm->id }}')" class="btn-tactile btn-tactile-outline !py-1.5 !px-3 text-xs cursor-pointer">
                                                Batal
                                            </button>
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
                    <svg class="size-8 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
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
    @include('_admin.modules.partials.section-scripts', ['module' => $module, 'activeSubModuleId' => $activeSubModuleId, 'subModules' => $subModules])
@endpush
