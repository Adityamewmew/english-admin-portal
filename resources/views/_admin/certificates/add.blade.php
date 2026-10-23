@extends('_admin._layout.app')

@section('title', 'Tambah Template Sertifikat')

@section('content')
    <x-admin.page-header :title="'Tambah Template Sertifikat'" subtitle="Unggah gambar desain sertifikat kosongan (JPG/PNG) untuk kategori level CEFR. Nama siswa dan nilai kelulusan akan otomatis dicetak pada sertifikat ini.">
        <x-admin.button href="{{ route('admin.certificates.index') }}" size="sm" color="outline-secondary">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-admin.button>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 border-b-2 border-b-rose-300 text-rose-800 dark:text-rose-200 text-sm shadow-2xs">
            <p class="font-bold mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-4xl">
        <form action="{{ route('admin.certificates.create') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-admin.card variant="white" padding="p-6 sm:p-8" class="space-y-6">
                {{-- 1. CEFR Selection --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        1. Pilih Kategori Level CEFR <span class="text-rose-500">*</span>
                    </label>
                    <select id="cefr-select" name="cefr" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium">
                        <option value="">-- Pilih Level CEFR --</option>
                        <option value="A1" {{ old('cefr') === 'A1' ? 'selected' : '' }}>Level A1 - Elementary / Starter Foundations</option>
                        <option value="A2" {{ old('cefr') === 'A2' ? 'selected' : '' }}>Level A2 - Pre-Intermediate Communication</option>
                        <option value="B1" {{ old('cefr') === 'B1' ? 'selected' : '' }}>Level B1 - Intermediate Practical English</option>
                        <option value="B2" {{ old('cefr') === 'B2' ? 'selected' : '' }}>Level B2 - Upper-Intermediate Fluency</option>
                        <option value="C1" {{ old('cefr') === 'C1' ? 'selected' : '' }}>Level C1 - Advanced Professional English</option>
                        <option value="C2" {{ old('cefr') === 'C2' ? 'selected' : '' }}>Level C2 - Mastery / Bilingual Proficiency</option>
                    </select>
                    <p class="text-[11px] text-gray-500 dark:text-neutral-400">Pilih level yang akan menggunakan gambar sertifikat ini.</p>
                </div>

                {{-- 2. Pilihan Format & Ukuran Sertifikat (Filter Orientasi) --}}
                <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            2. Pilih Format & Ukuran Kertas <span class="text-rose-500">*</span>
                        </label>
                        <span id="aspect-badge" class="text-[11px] font-semibold text-primary dark:text-primary-400 bg-primary/10 px-2.5 py-0.5 rounded-full">
                            A4 Portrait (1 : 1.414) · Resmi Eddy's
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        {{-- Portrait Option (Default) --}}
                        <label class="orientation-card relative flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all border-primary bg-primary/5 text-primary" data-orientation="portrait">
                            <input type="radio" name="orientation" value="portrait" class="sr-only" checked>
                            <div class="w-6 h-8 border-2 border-current rounded-xs mb-1.5 flex items-center justify-center">
                                <span class="text-[8px] font-bold">A4</span>
                            </div>
                            <span class="text-xs font-extrabold">Portrait</span>
                            <span class="text-[10px] text-slate-500 dark:text-neutral-400">1 : 1.414 (Tegak Resmi)</span>
                        </label>

                        {{-- Landscape Option --}}
                        <label class="orientation-card relative flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all border-gray-200 dark:border-neutral-700 hover:border-gray-300 text-gray-700 dark:text-neutral-300" data-orientation="landscape">
                            <input type="radio" name="orientation" value="landscape" class="sr-only">
                            <div class="w-8 h-6 border-2 border-current rounded-xs mb-1.5 flex items-center justify-center">
                                <span class="text-[8px] font-bold">A4</span>
                            </div>
                            <span class="text-xs font-extrabold">Landscape</span>
                            <span class="text-[10px] text-slate-500 dark:text-neutral-400">1.414 : 1 (Mendatar)</span>
                        </label>

                        {{-- Square Option --}}
                        <label class="orientation-card relative flex flex-col items-center justify-center p-3 rounded-xl border-2 cursor-pointer transition-all border-gray-200 dark:border-neutral-700 hover:border-gray-300 text-gray-700 dark:text-neutral-300" data-orientation="square">
                            <input type="radio" name="orientation" value="square" class="sr-only">
                            <div class="w-7 h-7 border-2 border-current rounded-xs mb-1.5 flex items-center justify-center">
                                <span class="text-[8px] font-bold">1:1</span>
                            </div>
                            <span class="text-xs font-extrabold">Square</span>
                            <span class="text-[10px] text-slate-500 dark:text-neutral-400">1 : 1 (Persegi)</span>
                        </label>
                    </div>
                </div>

                {{-- 3. File Upload Box with Live Interactive Preview --}}
                <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            3. Unggah Gambar Desain Sertifikat (JPG, PNG) <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Unggah file gambar template sertifikat (kosongkan bagian nama dan nilai). Sistem otomatis menimpa teks nama, 6 rincian nilai, no. referensi, dan tanggal di atas gambar ini.
                    </p>

                    {{-- Upload Dropzone Box --}}
                    <div class="mt-2 flex justify-center px-4 sm:px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-neutral-700 border-dashed rounded-xl hover:border-primary transition-colors bg-gray-50/50 dark:bg-neutral-800/30">
                        <div class="space-y-2 text-center w-full">
                            {{-- Interactive Preview Stage --}}
                            <div id="preview-container" class="hidden mx-auto mb-4 transition-all duration-300 max-w-sm sm:max-w-md">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-neutral-300">Preview Live Penempatan Sertifikat:</p>
                                    <label class="flex items-center gap-1.5 text-[11px] font-medium text-primary cursor-pointer select-none">
                                        <input type="checkbox" id="toggle-credential-overlays" checked class="rounded border-gray-300 text-primary focus:ring-primary size-3.5">
                                        <span>Tampilkan Nilai & No Ref</span>
                                    </label>
                                </div>

                                {{-- Certificate Stage Container (Aspect ratio dynamically matches orientation) --}}
                                <div id="preview-stage"
                                     class="relative aspect-[1/1.414] rounded-xl overflow-hidden border-2 border-primary/50 shadow-md bg-white select-none transition-all duration-300">
                                    <img id="image-preview" src="#" alt="Preview Sertifikat" class="w-full h-full object-contain pointer-events-none">

                                    {{-- 1. Dynamic Student Name Overlay --}}
                                    <div id="name-overlay-box" class="preview-element absolute left-0 right-0 flex items-center justify-center px-4 transition-all duration-75 z-20 cursor-pointer"
                                         data-target="name"
                                         style="top: {{ old('name_y_percent', 34) }}%; transform: translateY(-50%);">
                                        <span id="name-overlay-text" class="font-serif font-black text-[#0D3B66] text-center drop-shadow-xs tracking-wide leading-none select-none px-2 py-0.5 rounded transition-all"
                                              style="font-size: {{ (old('name_font_size', 26) * 0.75) }}px;">
                                            Muhammad Aditya Pratama
                                        </span>
                                    </div>

                                    {{-- 2. Credential & Scores Overlay (Breakdown 6 Nilai + Pass Standard) --}}
                                    <div id="scores-overlay-box" class="preview-element absolute left-0 right-0 flex flex-col items-center justify-center px-2 transition-all duration-75 z-10 cursor-pointer credential-overlay"
                                         data-target="scores"
                                         style="top: {{ old('scores_y_percent', 52) }}%; transform: translateY(-50%);">
                                        <div id="scores-overlay-inner" class="space-y-1 text-center select-none p-1 rounded transition-all">
                                            <div id="scores-table-box"
                                                 class="max-w-[220px] sm:max-w-[260px] mx-auto text-left font-mono font-bold space-y-0.5 text-slate-900 bg-white/85 backdrop-blur-2xs p-2 rounded border border-slate-200 shadow-2xs"
                                                 style="font-size: {{ (old('scores_font_size', 10) * 0.9) }}px;">
                                                <div class="flex justify-between"><span>STRUCTURE &amp; VOCABULARY</span><span>: 85</span></div>
                                                <div class="flex justify-between"><span>READING COMPREHENSION</span><span>: 86</span></div>
                                                <div class="flex justify-between"><span>LISTENING COMPREHENSION</span><span>: 87</span></div>
                                                <div class="flex justify-between"><span>DICTATION</span><span>: 91</span></div>
                                                <div class="flex justify-between"><span>CONVERSATION</span><span>: 90</span></div>
                                                <div class="flex justify-between"><span>COMPOSITION</span><span>: 86</span></div>
                                            </div>
                                            <div class="inline-block border border-slate-700/60 rounded-xl px-2 py-0.5 bg-[#FCFBF7]/90 text-[7px] sm:text-[8px] shadow-2xs font-mono">
                                                <p class="font-bold">89-100 = EXCELLENT · 76-88 = GOOD · 60-75 = FAIR</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 3. Date Overlay --}}
                                    <div id="date-overlay-box" class="preview-element absolute left-3 sm:left-5 text-left transition-all duration-75 z-10 cursor-pointer select-none credential-overlay"
                                         data-target="date"
                                         style="top: {{ old('date_y_percent', 80) }}%; transform: translateY(-50%);">
                                        <div id="date-overlay-inner" class="bg-white/75 backdrop-blur-2xs p-1 rounded border border-slate-200/60 shadow-2xs transition-all"
                                             style="font-size: {{ old('date_font_size', 8) }}px;">
                                            <p class="font-bold text-slate-800 leading-none mb-0.5">Date of Examination</p>
                                            <p class="text-slate-600 font-mono leading-none">24 Oktober 2026</p>
                                        </div>
                                    </div>

                                    {{-- 4. Place of Entry Overlay --}}
                                    <div id="place-overlay-box" class="preview-element absolute left-1/2 -translate-x-1/2 text-center transition-all duration-75 z-10 cursor-pointer select-none max-w-[130px] sm:max-w-[170px] credential-overlay"
                                         data-target="place"
                                         style="top: {{ old('place_y_percent', 80) }}%; transform: translate(-50%, -50%);">
                                        <div id="place-overlay-inner" class="bg-white/75 backdrop-blur-2xs p-1 rounded border border-slate-200/60 shadow-2xs transition-all"
                                             style="font-size: {{ old('place_font_size', 8) }}px;">
                                            <p class="font-bold text-slate-800 leading-none mb-0.5">Place of Entry</p>
                                            <p id="preview-place-text" class="text-slate-600 leading-tight">PBN Kelapa Gading, D.18</p>
                                        </div>
                                    </div>

                                    {{-- 5. Reference No. Overlay --}}
                                    <div id="ref-overlay-box" class="preview-element absolute right-3 sm:right-5 text-right transition-all duration-75 z-10 cursor-pointer select-none credential-overlay"
                                         data-target="ref_no"
                                         style="top: {{ old('ref_no_y_percent', 80) }}%; transform: translateY(-50%);">
                                        <div id="ref-overlay-inner" class="bg-white/75 backdrop-blur-2xs p-1 rounded border border-slate-200/60 shadow-2xs transition-all"
                                             style="font-size: {{ old('ref_no_font_size', 8) }}px;">
                                            <p class="font-bold text-slate-800 leading-none mb-0.5">Reference No.</p>
                                            <p class="font-bold text-slate-900 font-mono leading-none">EE-RC-A-C1-X/26-001</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Multi-Element Sliders Controller --}}
                                <div class="mt-3 p-3.5 rounded-xl bg-gray-50 dark:bg-neutral-800/60 border border-gray-200 dark:border-neutral-700 space-y-3 text-left">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                                        <span class="text-xs font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                                            <svg class="size-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                            Kustomisasi Posisi &amp; Ukuran Teks
                                        </span>
                                        <span class="text-[11px] text-primary font-medium">Live Interaktif</span>
                                    </div>

                                    {{-- Segmented Pill Selector --}}
                                    <div class="flex flex-wrap items-center gap-1 p-1 rounded-lg bg-gray-200/70 dark:bg-neutral-900 border border-gray-300/60 dark:border-neutral-700">
                                        <button type="button" class="target-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-neutral-800 text-primary shadow-xs" data-target="name">
                                            👤 Nama Siswa
                                        </button>
                                        <button type="button" class="target-btn px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white" data-target="scores">
                                            📊 Blok Nilai
                                        </button>
                                        <button type="button" class="target-btn px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white" data-target="date">
                                            📅 Tanggal Ujian
                                        </button>
                                        <button type="button" class="target-btn px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white" data-target="place">
                                            📍 Place of Entry
                                        </button>
                                        <button type="button" class="target-btn px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white" data-target="ref_no">
                                            🔢 No. Registrasi
                                        </button>
                                    </div>

                                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-neutral-400 px-0.5">
                                        <span>Mengatur: <strong id="active-target-title" class="text-primary font-bold">Nama Siswa</strong></span>
                                        <span class="text-[10px] text-gray-400">Klik elemen di preview untuk memilih</span>
                                    </div>

                                    {{-- Dynamic Sliders --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 border-t border-gray-200/60 dark:border-neutral-700/60">
                                        {{-- Vertical Position Slider (Y%) --}}
                                        <div class="space-y-1">
                                            <div class="flex justify-between items-center text-xs">
                                                <label for="y-slider" class="font-semibold text-gray-700 dark:text-neutral-300">
                                                    Posisi Vertikal (Y):
                                                </label>
                                                <span id="y-value-label" class="font-mono font-bold text-primary">{{ old('name_y_percent', 34) }}%</span>
                                            </div>
                                            <input type="range" id="y-slider" min="10" max="90" value="{{ old('name_y_percent', 34) }}"
                                                   class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                                            <div class="flex justify-between text-[10px] text-gray-400">
                                                <span>Atas (10%)</span>
                                                <span>Tengah (50%)</span>
                                                <span>Bawah (90%)</span>
                                            </div>
                                        </div>

                                        {{-- Font Size Slider (px) --}}
                                        <div class="space-y-1">
                                            <div class="flex justify-between items-center text-xs">
                                                <label for="font-size-slider" class="font-semibold text-gray-700 dark:text-neutral-300">
                                                    Ukuran Font:
                                                </label>
                                                <span id="font-size-label" class="font-mono font-bold text-primary">{{ old('name_font_size', 26) }} px</span>
                                            </div>
                                            <input type="range" id="font-size-slider" min="14" max="54" value="{{ old('name_font_size', 26) }}"
                                                   class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                                            <div class="flex justify-between text-[10px] text-gray-400">
                                                <span>Kecil</span>
                                                <span>Standar</span>
                                                <span>Besar</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden Inputs for Form Submission --}}
                                    <input type="hidden" name="name_y_percent" id="hidden-name-y" value="{{ old('name_y_percent', 34) }}">
                                    <input type="hidden" name="name_font_size" id="hidden-name-size" value="{{ old('name_font_size', 26) }}">

                                    <input type="hidden" name="scores_y_percent" id="hidden-scores-y" value="{{ old('scores_y_percent', 52) }}">
                                    <input type="hidden" name="scores_font_size" id="hidden-scores-size" value="{{ old('scores_font_size', 10) }}">

                                    <input type="hidden" name="date_y_percent" id="hidden-date-y" value="{{ old('date_y_percent', 80) }}">
                                    <input type="hidden" name="date_font_size" id="hidden-date-size" value="{{ old('date_font_size', 8) }}">

                                    <input type="hidden" name="place_y_percent" id="hidden-place-y" value="{{ old('place_y_percent', 80) }}">
                                    <input type="hidden" name="place_font_size" id="hidden-place-size" value="{{ old('place_font_size', 8) }}">

                                    <input type="hidden" name="ref_no_y_percent" id="hidden-ref_no-y" value="{{ old('ref_no_y_percent', 80) }}">
                                    <input type="hidden" name="ref_no_font_size" id="hidden-ref_no-size" value="{{ old('ref_no_font_size', 8) }}">
                                </div>
                            </div>

                            <div id="upload-placeholder" class="flex flex-col items-center">
                                <svg class="size-12 text-gray-400 dark:text-neutral-500 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-gray-600 dark:text-neutral-400">
                                    <label for="file-upload" class="relative cursor-pointer font-bold text-primary hover:text-primary-600 focus-within:outline-hidden">
                                        <span>Pilih file gambar</span>
                                        <input id="file-upload" name="background_file" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" required class="sr-only">
                                    </label>
                                    <p class="pl-1">atau seret gambar ke sini</p>
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-neutral-500 mt-1">PNG, JPG, WEBP hingga 8MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Detail Teks & Pejabat (Bisa Dilipat/Collapsible) --}}
                <div class="pt-4 border-t border-gray-100 dark:border-neutral-800">
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer list-none text-xs font-bold text-gray-600 dark:text-neutral-300 hover:text-primary">
                            <span>Informasi Teks & Pejabat Sertifikat (Otomatis Terisi, Klik untuk Ubah)</span>
                            <span class="transition group-open:rotate-180">
                                <svg fill="none" height="18" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="18"><path d="M6 9l6 6 6-6"></path></svg>
                            </span>
                        </summary>

                        <div class="mt-4 space-y-4 pt-2">
                            {{-- Judul Sertifikat --}}
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                    Judul Sertifikat
                                </label>
                                <input type="text" id="cert-title" name="title" value="{{ old('title') }}" placeholder="Otomatis berdasarkan level CEFR"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                            </div>

                            {{-- Deskripsi Kompetensi --}}
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                    Deskripsi Capaian / Kompetensi
                                </label>
                                <textarea id="cert-description" name="description" rows="2" placeholder="Otomatis berdasarkan level CEFR"
                                          class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">{{ old('description') }}</textarea>
                            </div>

                            {{-- Signatory Fields & Place of Entry --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                        Nama Penandatangan
                                    </label>
                                    <input type="text" name="instructor_name" value="{{ old('instructor_name', 'KHOIRUL ANWAR, M.Pd') }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                        Jabatan Penandatangan
                                    </label>
                                    <input type="text" name="instructor_title" value="{{ old('instructor_title', 'Director of Studies') }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                        Tempat Ujian (Place of Entry)
                                    </label>
                                    <input type="text" name="place_of_entry" value="{{ old('place_of_entry', 'PBN Kelapa Gading, D.18 Jember East Java') }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </details>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-800 flex items-center justify-end gap-3">
                    <x-admin.button href="{{ route('admin.certificates.index') }}" color="outline-secondary" size="sm">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary" size="sm">
                        <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Template Sertifikat
                    </x-admin.button>
                </div>
            </x-admin.card>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('file-upload');
            const previewContainer = document.getElementById('preview-container');
            const previewStage = document.getElementById('preview-stage');
            const imagePreview = document.getElementById('image-preview');
            const aspectBadge = document.getElementById('aspect-badge');
            const cefrSelect = document.getElementById('cefr-select');
            const certTitle = document.getElementById('cert-title');
            const certDescription = document.getElementById('cert-description');
            const toggleOverlays = document.getElementById('toggle-credential-overlays');
            const credentialsOverlay = document.getElementById('credentials-overlay');

            // Dynamic Aspect Ratio Switcher
            const orientationCards = document.querySelectorAll('.orientation-card');
            orientationCards.forEach(card => {
                card.addEventListener('click', function () {
                    orientationCards.forEach(c => {
                        c.classList.remove('border-primary', 'bg-primary/5', 'text-primary');
                        c.classList.add('border-gray-200', 'text-gray-700');
                        c.querySelector('input').checked = false;
                    });
                    this.classList.add('border-primary', 'bg-primary/5', 'text-primary');
                    this.classList.remove('border-gray-200', 'text-gray-700');
                    const radio = this.querySelector('input');
                    radio.checked = true;

                    const aspect = this.dataset.orientation;
                    applyOrientation(aspect);
                });
            });

            function applyOrientation(aspect) {
                previewStage.className = 'relative rounded-xl overflow-hidden border-2 border-primary/50 shadow-md bg-white select-none transition-all duration-300';
                previewContainer.className = 'mx-auto mb-4 transition-all duration-300';

                if (aspect === 'portrait') {
                    previewStage.classList.add('aspect-[1/1.414]');
                    previewContainer.classList.add('max-w-sm', 'sm:max-w-md');
                    aspectBadge.textContent = "A4 Portrait (1 : 1.414) · Resmi Eddy's";
                } else if (aspect === 'landscape') {
                    previewStage.classList.add('aspect-[1.414/1]');
                    previewContainer.classList.add('max-w-xl');
                    aspectBadge.textContent = "A4 Landscape (1.414 : 1) · Mendatar";
                } else if (aspect === 'square') {
                    previewStage.classList.add('aspect-square');
                    previewContainer.classList.add('max-w-md');
                    aspectBadge.textContent = "Square (1 : 1) · Persegi";
                }
            }

            // Toggle Credentials Overlay Visibility
            if (toggleOverlays) {
                toggleOverlays.addEventListener('change', function () {
                    const credOverlays = document.querySelectorAll('.credential-overlay');
                    credOverlays.forEach(el => {
                        el.style.opacity = toggleOverlays.checked ? '1' : '0';
                        el.style.pointerEvents = toggleOverlays.checked ? 'auto' : 'none';
                    });
                });
            }

            // CEFR Selection Change -> Auto Fill Title & Description
            const cefrTemplates = {
                'A1': { title: 'Certificate of CEFR A1 Elementary English Proficiency', description: 'Has demonstrated comprehensive competence in understanding and using familiar everyday expressions, basic phrases, and fundamental communication structures for real-world interactions.' },
                'A2': { title: 'Certificate of CEFR A2 Pre-Intermediate English Proficiency', description: 'Has demonstrated proven ability to communicate in routine tasks, exchange direct information on familiar topics, and describe aspects of background, immediate environment, and personal matters.' },
                'B1': { title: 'Certificate of CEFR B1 Intermediate English Proficiency', description: 'Has demonstrated solid competence to handle most communicative situations, produce connected text on familiar topics, and describe experiences, events, dreams, hopes, and ambitions with reasoned explanations.' },
                'B2': { title: 'Certificate of CEFR B2 Upper-Intermediate English Proficiency', description: 'Has achieved fluency and spontaneity in interacting with native speakers, understanding complex technical texts, and producing clear, detailed arguments on a wide range of contemporary subjects.' },
                'C1': { title: 'Certificate of CEFR C1 Advanced English Proficiency', description: 'Has attained advanced mastery in comprehending demanding, longer academic and professional discourses, expressing ideas fluently without obvious searching for expressions, and using language flexibly for social and professional purposes.' },
                'C2': { title: 'Certificate of CEFR C2 Mastery English Proficiency', description: 'Has attained bilingual-level precision and effortless fluency, understanding virtually everything heard or read, summarizing information from different spoken and written sources, and reconstructing arguments coherently in spontaneous presentation.' }
            };

            cefrSelect.addEventListener('change', function () {
                const val = this.value;
                if (val && cefrTemplates[val]) {
                    if (!certTitle.value || Object.values(cefrTemplates).some(t => t.title === certTitle.value)) {
                        certTitle.value = cefrTemplates[val].title;
                    }
                    if (!certDescription.value || Object.values(cefrTemplates).some(t => t.description === certDescription.value)) {
                        certDescription.value = cefrTemplates[val].description;
                    }
                }
            });

            // Instant Image Preview
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Multi-Element Customizer
            const targets = {
                name: {
                    title: 'Nama Siswa',
                    yInput: document.getElementById('hidden-name-y'),
                    sizeInput: document.getElementById('hidden-name-size'),
                    box: document.getElementById('name-overlay-box'),
                    textElem: document.getElementById('name-overlay-text'),
                    minY: 10, maxY: 90, minSize: 14, maxSize: 54, sizeScale: 0.75,
                },
                scores: {
                    title: 'Blok Nilai (6 Mata Uji & Grade)',
                    yInput: document.getElementById('hidden-scores-y'),
                    sizeInput: document.getElementById('hidden-scores-size'),
                    box: document.getElementById('scores-overlay-box'),
                    textElem: document.getElementById('scores-table-box'),
                    minY: 15, maxY: 85, minSize: 6, maxSize: 24, sizeScale: 0.9,
                },
                date: {
                    title: 'Tanggal Ujian (Date of Examination)',
                    yInput: document.getElementById('hidden-date-y'),
                    sizeInput: document.getElementById('hidden-date-size'),
                    box: document.getElementById('date-overlay-box'),
                    textElem: document.getElementById('date-overlay-inner'),
                    minY: 35, maxY: 95, minSize: 6, maxSize: 20, sizeScale: 1.0,
                },
                place: {
                    title: 'Tempat Ujian (Place of Entry)',
                    yInput: document.getElementById('hidden-place-y'),
                    sizeInput: document.getElementById('hidden-place-size'),
                    box: document.getElementById('place-overlay-box'),
                    textElem: document.getElementById('place-overlay-inner'),
                    minY: 35, maxY: 95, minSize: 6, maxSize: 20, sizeScale: 1.0,
                    isCentered: true,
                },
                ref_no: {
                    title: 'Nomor Registrasi (Reference No.)',
                    yInput: document.getElementById('hidden-ref_no-y'),
                    sizeInput: document.getElementById('hidden-ref_no-size'),
                    box: document.getElementById('ref-overlay-box'),
                    textElem: document.getElementById('ref-overlay-inner'),
                    minY: 35, maxY: 95, minSize: 6, maxSize: 20, sizeScale: 1.0,
                }
            };

            let currentTargetKey = 'name';
            const targetButtons = document.querySelectorAll('.target-btn');
            const previewElements = document.querySelectorAll('.preview-element');
            const activeTitle = document.getElementById('active-target-title');
            const ySlider = document.getElementById('y-slider');
            const yLabel = document.getElementById('y-value-label');
            const fontSizeSlider = document.getElementById('font-size-slider');
            const fontSizeLabel = document.getElementById('font-size-label');

            function selectTarget(key) {
                if (!targets[key]) return;
                currentTargetKey = key;
                const conf = targets[key];

                targetButtons.forEach(btn => {
                    if (btn.dataset.target === key) {
                        btn.className = 'target-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-neutral-800 text-primary shadow-xs';
                    } else {
                        btn.className = 'target-btn px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white';
                    }
                });

                previewElements.forEach(el => {
                    if (el.dataset.target === key) {
                        el.classList.add('ring-2', 'ring-primary', 'ring-offset-1', 'rounded-xs');
                    } else {
                        el.classList.remove('ring-2', 'ring-primary', 'ring-offset-1', 'rounded-xs');
                    }
                });

                if (activeTitle) activeTitle.textContent = conf.title;

                ySlider.min = conf.minY;
                ySlider.max = conf.maxY;
                ySlider.value = conf.yInput.value;
                yLabel.textContent = conf.yInput.value + '%';

                fontSizeSlider.min = conf.minSize;
                fontSizeSlider.max = conf.maxSize;
                fontSizeSlider.value = conf.sizeInput.value;
                fontSizeLabel.textContent = conf.sizeInput.value + ' px';
            }

            targetButtons.forEach(btn => {
                btn.addEventListener('click', () => selectTarget(btn.dataset.target));
            });

            previewElements.forEach(el => {
                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectTarget(el.dataset.target);
                });
            });

            if (ySlider) {
                ySlider.addEventListener('input', function () {
                    const conf = targets[currentTargetKey];
                    if (!conf) return;
                    const val = this.value;
                    yLabel.textContent = val + '%';
                    conf.yInput.value = val;
                    if (conf.isCentered) {
                        conf.box.style.top = val + '%';
                        conf.box.style.transform = 'translate(-50%, -50%)';
                    } else {
                        conf.box.style.top = val + '%';
                        conf.box.style.transform = 'translateY(-50%)';
                    }
                });
            }

            if (fontSizeSlider) {
                fontSizeSlider.addEventListener('input', function () {
                    const conf = targets[currentTargetKey];
                    if (!conf) return;
                    const val = this.value;
                    fontSizeLabel.textContent = val + ' px';
                    conf.sizeInput.value = val;
                    if (conf.textElem) {
                        conf.textElem.style.fontSize = (val * conf.sizeScale) + 'px';
                    }
                });
            }

            // Sync Place of Entry text input with preview live
            const placeInput = document.querySelector('input[name="place_of_entry"]');
            const previewPlaceText = document.getElementById('preview-place-text');
            if (placeInput && previewPlaceText) {
                placeInput.addEventListener('input', function () {
                    previewPlaceText.textContent = this.value || 'PBN Kelapa Gading, D.18';
                });
            }

            // Initial selection
            selectTarget('name');
        });
    </script>
@endsection
