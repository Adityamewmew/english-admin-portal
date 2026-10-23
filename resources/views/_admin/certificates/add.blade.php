@extends('_admin._layout.app')

@section('title', 'Tambah Template Sertifikat')

@section('content')
    <x-admin.page-header :title="'Tambah Template Sertifikat'" subtitle="Unggah gambar desain sertifikat kosongan (JPG/PNG) untuk kategori level CEFR. Atur posisi teks secara visual pada preview di sebelah kanan.">
        <x-admin.button href="{{ route('admin.certificates.index') }}" size="sm" color="outline-secondary">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-admin.button>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 border-b-2 border-b-rose-300 text-rose-800 dark:text-rose-200 text-sm shadow-2xs">
            <p class="font-bold mb-1">Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.certificates.create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- 2-Column Layout: Left = Settings (5 cols), Right = Live Preview Card (7 cols, sticky) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Left Column: Form Settings --}}
            <div class="lg:col-span-5 space-y-5">
                <x-admin.card variant="white" padding="p-6" class="space-y-4 shadow-xs">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-neutral-800 pb-3">
                        <svg class="size-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Konfigurasi Template Sertifikat
                    </h3>

                    {{-- 1. CEFR Selection --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            1. Level CEFR <span class="text-rose-500">*</span>
                        </label>
                        <select id="cefr-select" name="cefr" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium">
                            <option value="">-- Pilih Level CEFR --</option>
                            <option value="A1" {{ old('cefr') === 'A1' ? 'selected' : '' }}>Level A1 - Elementary / Starter Foundations</option>
                            <option value="A2" {{ old('cefr') === 'A2' ? 'selected' : '' }}>Level A2 - Pre-Intermediate Communication</option>
                            <option value="B1" {{ old('cefr') === 'B1' ? 'selected' : '' }}>Level B1 - Intermediate Practical English</option>
                            <option value="B2" {{ old('cefr') === 'B2' ? 'selected' : '' }}>Level B2 - Upper-Intermediate Fluency</option>
                            <option value="C1" {{ old('cefr') === 'C1' ? 'selected' : '' }}>Level C1 - Advanced Professional English</option>
                            <option value="C2" {{ old('cefr') === 'C2' ? 'selected' : '' }}>Level C2 - Mastery / Bilingual Proficiency</option>
                        </select>
                    </div>

                    {{-- 2. Format & Ukuran Kertas --}}
                    <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-800">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            2. Format Ukuran Kertas <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="orientation-card flex flex-col items-center justify-center p-2 rounded-xl border-2 cursor-pointer transition-all border-primary bg-primary/5 text-primary text-center" data-orientation="portrait">
                                <input type="radio" name="orientation" value="portrait" class="sr-only" checked>
                                <div class="w-4 h-6 border-2 border-current rounded-xs mb-1 flex items-center justify-center text-[7px] font-bold">A4</div>
                                <span class="text-[11px] font-extrabold">Portrait</span>
                                <span class="text-[9px] text-slate-500">1:1.414</span>
                            </label>
                            <label class="orientation-card flex flex-col items-center justify-center p-2 rounded-xl border-2 cursor-pointer transition-all border-gray-200 dark:border-neutral-700 hover:border-gray-300 text-gray-700 dark:text-neutral-300 text-center" data-orientation="landscape">
                                <input type="radio" name="orientation" value="landscape" class="sr-only">
                                <div class="w-6 h-4 border-2 border-current rounded-xs mb-1 flex items-center justify-center text-[7px] font-bold">A4</div>
                                <span class="text-[11px] font-extrabold">Landscape</span>
                                <span class="text-[9px] text-slate-500">1.414:1</span>
                            </label>
                            <label class="orientation-card flex flex-col items-center justify-center p-2 rounded-xl border-2 cursor-pointer transition-all border-gray-200 dark:border-neutral-700 hover:border-gray-300 text-gray-700 dark:text-neutral-300 text-center" data-orientation="square">
                                <input type="radio" name="orientation" value="square" class="sr-only">
                                <div class="w-5 h-5 border-2 border-current rounded-xs mb-1 flex items-center justify-center text-[7px] font-bold">1:1</div>
                                <span class="text-[11px] font-extrabold">Square</span>
                                <span class="text-[9px] text-slate-500">1:1</span>
                            </label>
                        </div>
                    </div>

                    {{-- 3. File Upload Box --}}
                    <div class="space-y-1.5 pt-2 border-t border-gray-100 dark:border-neutral-800">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            3. Background Desain <span class="text-rose-500">*</span>
                        </label>
                        <div class="p-3.5 border-2 border-dashed border-gray-300 dark:border-neutral-700 rounded-xl hover:border-primary transition-colors bg-gray-50/50 dark:bg-neutral-800/30 text-center">
                            <label for="file-upload" class="cursor-pointer text-xs font-bold text-primary hover:underline block">
                                <span>Pilih file gambar</span>
                                <input id="file-upload" name="background_file" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" required class="sr-only">
                            </label>
                            <p class="text-[10px] text-gray-400 mt-0.5">PNG, JPG, WEBP hingga 8MB</p>
                            <p id="file-name-label" class="text-[11px] font-semibold text-emerald-600 mt-1 hidden"></p>
                        </div>
                    </div>

                    {{-- 4. Detail Teks & Pejabat --}}
                    <div class="pt-2 border-t border-gray-100 dark:border-neutral-800 space-y-3">
                        <div class="space-y-1">
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-neutral-300">Judul Sertifikat</label>
                            <input type="text" id="cert-title" name="title" value="{{ old('title') }}" placeholder="Otomatis berdasarkan CEFR"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-neutral-300">Deskripsi Capaian</label>
                            <textarea id="cert-description" name="description" rows="2" placeholder="Otomatis berdasarkan CEFR"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">{{ old('description') }}</textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-semibold text-gray-700 dark:text-neutral-300">Tempat Ujian (Place of Entry)</label>
                            <input type="text" name="place_of_entry" id="input-place-of-entry" value="{{ old('place_of_entry', 'PBN Kelapa Gading, D.18 Jember East Java') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-gray-700 dark:text-neutral-300">Penandatangan</label>
                                <input type="text" name="instructor_name" value="{{ old('instructor_name', 'KHOIRUL ANWAR, M.Pd') }}"
                                       class="w-full px-3 py-1.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-gray-700 dark:text-neutral-300">Jabatan</label>
                                <input type="text" name="instructor_title" value="{{ old('instructor_title', 'Director of Studies') }}"
                                       class="w-full px-3 py-1.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    {{-- Form Action Buttons --}}
                    <div class="pt-3 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-end gap-2">
                        <x-admin.button href="{{ route('admin.certificates.index') }}" color="outline-secondary" size="sm">
                            Batal
                        </x-admin.button>
                        <x-admin.button type="submit" color="primary" size="sm">
                            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Template
                        </x-admin.button>
                    </div>
                </x-admin.card>
            </div>

            {{-- Right Column: Dedicated Live Preview Card (7 Cols, Sticky) --}}
            <div class="lg:col-span-7 sticky top-4 space-y-3">
                <x-admin.card variant="white" padding="p-4 sm:p-5" class="shadow-sm border border-slate-200 dark:border-neutral-700 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-neutral-800">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="flex size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Preview &amp; Posisi Teks
                            </h3>
                            <p class="text-[11px] text-gray-500 dark:text-neutral-400">Atur posisi X, Y, dan ukuran font tiap elemen di atas desain sertifikat.</p>
                        </div>
                        <span id="aspect-badge" class="text-[10px] font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-full shrink-0">
                            Portrait (1:1.414)
                        </span>
                    </div>

                    {{-- Preview Container & Stage --}}
                    <div id="preview-stage-wrap" class="relative mx-auto w-full transition-all duration-300 max-w-sm sm:max-w-md">
                        <div id="preview-stage"
                             class="relative aspect-[1/1.414] rounded-xl overflow-hidden border-2 border-primary/40 shadow-sm bg-slate-50 dark:bg-neutral-900 select-none transition-all duration-300">
                            <img id="image-preview" src="#" alt="Preview Background" class="w-full h-full object-contain pointer-events-none opacity-0 transition-opacity duration-200">

                            <div id="no-image-notice" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                                <svg class="size-10 text-gray-300 dark:text-neutral-600 mb-2" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-xs font-bold text-gray-600 dark:text-neutral-400">Pilih file background di sebelah kiri</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Preview penempatan teks akan tampil otomatis</p>
                            </div>

                            {{-- 1. Student Name Overlay --}}
                            <div id="name-overlay-box" class="preview-element absolute z-20 cursor-pointer text-center px-1 py-0.5 transition-all"
                                 data-target="name"
                                 style="left: {{ old('name_x_percent', 50) }}%; top: {{ old('name_y_percent', 34) }}%; transform: translate(-50%, -50%);">
                                <span id="name-overlay-text" class="font-serif font-black text-[#0D3B66] text-center drop-shadow-xs tracking-wide leading-none whitespace-nowrap select-none"
                                      style="font-size: {{ (old('name_font_size', 26) * 0.75) }}px;">
                                    Muhammad Aditya Pratama
                                </span>
                            </div>

                            {{-- 2. Scores Overlay (Breakdown 6 Nilai) --}}
                            <div id="scores-overlay-box" class="preview-element absolute z-10 cursor-pointer text-center px-1 transition-all"
                                 data-target="scores"
                                 style="left: {{ old('scores_x_percent', 50) }}%; top: {{ old('scores_y_percent', 52) }}%; transform: translate(-50%, -50%);">
                                <div id="scores-overlay-inner" class="space-y-1 text-center select-none">
                                    <div id="scores-table-box"
                                         class="w-[210px] sm:w-[240px] text-left font-mono font-bold space-y-0.5 text-slate-900 bg-white/85 backdrop-blur-2xs p-2 rounded border border-slate-200 shadow-2xs"
                                         style="font-size: {{ (old('scores_font_size', 10) * 0.9) }}px;">
                                        <div class="flex justify-between"><span>STRUCTURE &amp; VOCABULARY</span><span>: 85</span></div>
                                        <div class="flex justify-between"><span>READING COMPREHENSION</span><span>: 86</span></div>
                                        <div class="flex justify-between"><span>LISTENING COMPREHENSION</span><span>: 87</span></div>
                                        <div class="flex justify-between"><span>DICTATION</span><span>: 91</span></div>
                                        <div class="flex justify-between"><span>CONVERSATION</span><span>: 90</span></div>
                                        <div class="flex justify-between"><span>COMPOSITION</span><span>: 86</span></div>
                                    </div>
                                    <div class="inline-block border border-slate-700/60 rounded-xl px-2 py-0.5 bg-[#FCFBF7]/90 text-[7px] shadow-2xs font-mono">
                                        <p class="font-bold">89-100 = EXCELLENT · 76-88 = GOOD · 60-75 = FAIR</p>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Date Overlay --}}
                            <div id="date-overlay-box" class="preview-element absolute z-10 cursor-pointer select-none text-center whitespace-nowrap transition-all"
                                 data-target="date"
                                 style="left: {{ old('date_x_percent', 18) }}%; top: {{ old('date_y_percent', 80) }}%; transform: translate(-50%, -50%);">
                                <span id="date-overlay-text" class="font-mono font-bold text-slate-800 drop-shadow-2xs leading-none"
                                      style="font-size: {{ old('date_font_size', 8) }}px;">
                                    24 Oktober 2026
                                </span>
                            </div>

                            {{-- 4. Place of Entry Overlay --}}
                            <div id="place-overlay-box" class="preview-element absolute z-10 cursor-pointer select-none text-center transition-all max-w-[130px] sm:max-w-[170px]"
                                 data-target="place"
                                 style="left: {{ old('place_x_percent', 50) }}%; top: {{ old('place_y_percent', 80) }}%; transform: translate(-50%, -50%);">
                                <span id="preview-place-text" class="font-bold text-slate-800 leading-tight drop-shadow-2xs block"
                                      style="font-size: {{ old('place_font_size', 8) }}px;">
                                    {{ old('place_of_entry', 'PBN Kelapa Gading, D.18 Jember East Java') }}
                                </span>
                            </div>

                            {{-- 5. Reference No. Overlay --}}
                            <div id="ref-overlay-box" class="preview-element absolute z-10 cursor-pointer select-none text-center whitespace-nowrap transition-all"
                                 data-target="ref_no"
                                 style="left: {{ old('ref_no_x_percent', 82) }}%; top: {{ old('ref_no_y_percent', 80) }}%; transform: translate(-50%, -50%);">
                                <span id="ref-overlay-text" class="font-mono font-bold text-slate-900 drop-shadow-2xs leading-none"
                                      style="font-size: {{ old('ref_no_font_size', 8) }}px;">
                                    EE-RC-A-{{ old('cefr', 'A1') }}-X/{{ date('y') }}-001
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Multi-Element Sliders Controller --}}
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-neutral-800/60 border border-gray-200 dark:border-neutral-700 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800 dark:text-white">Pilih Target Elemen:</span>
                            <span class="text-[11px] text-primary font-medium" id="active-target-title">Nama Siswa</span>
                        </div>

                        {{-- Segmented Pill Buttons --}}
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-1 p-1 rounded-lg bg-gray-200/70 dark:bg-neutral-900 border border-gray-300/60 dark:border-neutral-700">
                            <button type="button" class="target-btn px-2 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-neutral-800 text-primary shadow-xs" data-target="name">👤 Nama</button>
                            <button type="button" class="target-btn px-2 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900" data-target="scores">📊 Nilai</button>
                            <button type="button" class="target-btn px-2 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900" data-target="date">📅 Tanggal</button>
                            <button type="button" class="target-btn px-2 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900" data-target="place">📍 Place</button>
                            <button type="button" class="target-btn px-2 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900" data-target="ref_no">🔢 Ref No</button>
                        </div>

                        {{-- 3 Sliders: X, Y, Size --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1 border-t border-gray-200/60 dark:border-neutral-700/60">
                            <div class="space-y-1">
                                <div class="flex justify-between items-center text-xs">
                                    <label class="font-semibold text-gray-700 dark:text-neutral-300">Posisi X (Horiz):</label>
                                    <span id="x-value-label" class="font-mono font-bold text-primary">50%</span>
                                </div>
                                <input type="range" id="x-slider" min="0" max="100" value="50" class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center text-xs">
                                    <label class="font-semibold text-gray-700 dark:text-neutral-300">Posisi Y (Vert):</label>
                                    <span id="y-value-label" class="font-mono font-bold text-primary">34%</span>
                                </div>
                                <input type="range" id="y-slider" min="5" max="95" value="34" class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between items-center text-xs">
                                    <label class="font-semibold text-gray-700 dark:text-neutral-300">Ukuran Font:</label>
                                    <span id="font-size-label" class="font-mono font-bold text-primary">26 px</span>
                                </div>
                                <input type="range" id="font-size-slider" min="14" max="54" value="26" class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                            </div>
                        </div>

                        {{-- Hidden inputs --}}
                        <input type="hidden" name="name_x_percent" id="hidden-name-x" value="{{ old('name_x_percent', 50) }}">
                        <input type="hidden" name="name_y_percent" id="hidden-name-y" value="{{ old('name_y_percent', 34) }}">
                        <input type="hidden" name="name_font_size" id="hidden-name-size" value="{{ old('name_font_size', 26) }}">

                        <input type="hidden" name="scores_x_percent" id="hidden-scores-x" value="{{ old('scores_x_percent', 50) }}">
                        <input type="hidden" name="scores_y_percent" id="hidden-scores-y" value="{{ old('scores_y_percent', 52) }}">
                        <input type="hidden" name="scores_font_size" id="hidden-scores-size" value="{{ old('scores_font_size', 10) }}">

                        <input type="hidden" name="date_x_percent" id="hidden-date-x" value="{{ old('date_x_percent', 18) }}">
                        <input type="hidden" name="date_y_percent" id="hidden-date-y" value="{{ old('date_y_percent', 80) }}">
                        <input type="hidden" name="date_font_size" id="hidden-date-size" value="{{ old('date_font_size', 8) }}">

                        <input type="hidden" name="place_x_percent" id="hidden-place-x" value="{{ old('place_x_percent', 50) }}">
                        <input type="hidden" name="place_y_percent" id="hidden-place-y" value="{{ old('place_y_percent', 80) }}">
                        <input type="hidden" name="place_font_size" id="hidden-place-size" value="{{ old('place_font_size', 8) }}">

                        <input type="hidden" name="ref_no_x_percent" id="hidden-ref_no-x" value="{{ old('ref_no_x_percent', 82) }}">
                        <input type="hidden" name="ref_no_y_percent" id="hidden-ref_no-y" value="{{ old('ref_no_y_percent', 80) }}">
                        <input type="hidden" name="ref_no_font_size" id="hidden-ref_no-size" value="{{ old('ref_no_font_size', 8) }}">
                    </div>
                </x-admin.card>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('file-upload');
            const previewStage = document.getElementById('preview-stage');
            const previewStageWrap = document.getElementById('preview-stage-wrap');
            const imagePreview = document.getElementById('image-preview');
            const noImageNotice = document.getElementById('no-image-notice');
            const fileNameLabel = document.getElementById('file-name-label');
            const aspectBadge = document.getElementById('aspect-badge');
            const cefrSelect = document.getElementById('cefr-select');
            const certTitle = document.getElementById('cert-title');
            const certDescription = document.getElementById('cert-description');

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
                    this.querySelector('input').checked = true;
                    applyOrientation(this.dataset.orientation);
                });
            });

            function applyOrientation(aspect) {
                previewStage.className = 'relative rounded-xl overflow-hidden border-2 border-primary/40 shadow-sm bg-slate-50 dark:bg-neutral-900 select-none transition-all duration-300';
                previewStageWrap.className = 'relative mx-auto w-full transition-all duration-300';
                if (aspect === 'portrait') {
                    previewStage.classList.add('aspect-[1/1.414]');
                    previewStageWrap.classList.add('max-w-sm', 'sm:max-w-md');
                    aspectBadge.textContent = "Portrait (1:1.414)";
                } else if (aspect === 'landscape') {
                    previewStage.classList.add('aspect-[1.414/1]');
                    previewStageWrap.classList.add('max-w-xl');
                    aspectBadge.textContent = "Landscape (1.414:1)";
                } else if (aspect === 'square') {
                    previewStage.classList.add('aspect-square');
                    previewStageWrap.classList.add('max-w-md');
                    aspectBadge.textContent = "Square (1:1)";
                }
            }

            const cefrTemplates = {
                'A1': { title: 'Certificate of CEFR A1 Elementary English Proficiency', description: 'Has demonstrated comprehensive competence in understanding and using familiar everyday expressions, basic phrases, and fundamental communication structures for real-world interactions.' },
                'A2': { title: 'Certificate of CEFR A2 Pre-Intermediate English Proficiency', description: 'Has demonstrated proven ability to communicate in routine tasks, exchange direct information on familiar topics, and describe aspects of background, immediate environment, and personal matters.' },
                'B1': { title: 'Certificate of CEFR B1 Intermediate English Proficiency', description: 'Has demonstrated solid competence to handle most communicative situations, produce connected text on familiar topics, and describe experiences, events, dreams, hopes, and ambitions with reasoned explanations.' },
                'B2': { title: 'Certificate of CEFR B2 Upper-Intermediate English Proficiency', description: 'Has achieved fluency and spontaneity in interacting with native speakers, understanding complex technical texts, and producing clear, detailed arguments on a wide range of contemporary subjects.' },
                'C1': { title: 'Certificate of CEFR C1 Advanced English Proficiency', description: 'Has attained advanced mastery in comprehending demanding, longer academic and professional discourses, expressing ideas fluently without obvious searching for expressions, and using language flexibly for social and professional purposes.' },
                'C2': { title: 'Certificate of CEFR C2 Mastery English Proficiency', description: 'Has attained bilingual-level precision and effortless fluency, understanding virtually everything heard or read, summarizing information from different spoken and written sources, and reconstructing arguments coherently in spontaneous presentation.' }
            };

            const refOverlayText = document.getElementById('ref-overlay-text');

            cefrSelect.addEventListener('change', function () {
                const val = this.value;
                if (val) {
                    if (refOverlayText) {
                        refOverlayText.textContent = `EE-RC-A-${val}-X/{{ date('y') }}-001`;
                    }
                    if (cefrTemplates[val]) {
                        if (!certTitle.value || Object.values(cefrTemplates).some(t => t.title === certTitle.value)) {
                            certTitle.value = cefrTemplates[val].title;
                        }
                        if (!certDescription.value || Object.values(cefrTemplates).some(t => t.description === certDescription.value)) {
                            certDescription.value = cefrTemplates[val].description;
                        }
                    }
                }
            });

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    if (fileNameLabel) {
                        fileNameLabel.textContent = '✓ ' + file.name;
                        fileNameLabel.classList.remove('hidden');
                    }
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('opacity-0');
                        if (noImageNotice) noImageNotice.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            const targets = {
                name: {
                    title: 'Nama Siswa',
                    xInput: document.getElementById('hidden-name-x'),
                    yInput: document.getElementById('hidden-name-y'),
                    sizeInput: document.getElementById('hidden-name-size'),
                    box: document.getElementById('name-overlay-box'),
                    textElem: document.getElementById('name-overlay-text'),
                    minX: 5, maxX: 95, minY: 10, maxY: 90, minSize: 14, maxSize: 54, sizeScale: 0.75,
                },
                scores: {
                    title: 'Blok Nilai (6 Mata Uji & Grade)',
                    xInput: document.getElementById('hidden-scores-x'),
                    yInput: document.getElementById('hidden-scores-y'),
                    sizeInput: document.getElementById('hidden-scores-size'),
                    box: document.getElementById('scores-overlay-box'),
                    textElem: document.getElementById('scores-table-box'),
                    minX: 5, maxX: 95, minY: 15, maxY: 85, minSize: 6, maxSize: 24, sizeScale: 0.9,
                },
                date: {
                    title: 'Tanggal Ujian',
                    xInput: document.getElementById('hidden-date-x'),
                    yInput: document.getElementById('hidden-date-y'),
                    sizeInput: document.getElementById('hidden-date-size'),
                    box: document.getElementById('date-overlay-box'),
                    textElem: document.getElementById('date-overlay-text'),
                    minX: 2, maxX: 98, minY: 20, maxY: 95, minSize: 6, maxSize: 22, sizeScale: 1.0,
                },
                place: {
                    title: 'Tempat Ujian (Place of Entry)',
                    xInput: document.getElementById('hidden-place-x'),
                    yInput: document.getElementById('hidden-place-y'),
                    sizeInput: document.getElementById('hidden-place-size'),
                    box: document.getElementById('place-overlay-box'),
                    textElem: document.getElementById('preview-place-text'),
                    minX: 2, maxX: 98, minY: 20, maxY: 95, minSize: 6, maxSize: 22, sizeScale: 1.0,
                },
                ref_no: {
                    title: 'Nomor Registrasi (Reference No.)',
                    xInput: document.getElementById('hidden-ref_no-x'),
                    yInput: document.getElementById('hidden-ref_no-y'),
                    sizeInput: document.getElementById('hidden-ref_no-size'),
                    box: document.getElementById('ref-overlay-box'),
                    textElem: document.getElementById('ref-overlay-text'),
                    minX: 2, maxX: 98, minY: 20, maxY: 95, minSize: 6, maxSize: 22, sizeScale: 1.0,
                }
            };

            let currentTargetKey = 'name';
            const targetButtons = document.querySelectorAll('.target-btn');
            const previewElements = document.querySelectorAll('.preview-element');
            const activeTitle = document.getElementById('active-target-title');
            const xSlider = document.getElementById('x-slider');
            const xLabel = document.getElementById('x-value-label');
            const ySlider = document.getElementById('y-slider');
            const yLabel = document.getElementById('y-value-label');
            const fontSizeSlider = document.getElementById('font-size-slider');
            const fontSizeLabel = document.getElementById('font-size-label');

            function selectTarget(key) {
                if (!targets[key]) return;
                currentTargetKey = key;
                const conf = targets[key];

                targetButtons.forEach(btn => {
                    btn.className = (btn.dataset.target === key)
                        ? 'target-btn px-2 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-neutral-800 text-primary shadow-xs'
                        : 'target-btn px-2 py-1 rounded-md text-[11px] font-semibold transition-all text-gray-600 dark:text-neutral-400 hover:text-gray-900';
                });

                previewElements.forEach(el => {
                    el.classList.toggle('ring-2', el.dataset.target === key);
                    el.classList.toggle('ring-primary', el.dataset.target === key);
                });

                if (activeTitle) activeTitle.textContent = conf.title;

                xSlider.min = conf.minX;
                xSlider.max = conf.maxX;
                xSlider.value = conf.xInput.value;
                xLabel.textContent = conf.xInput.value + '%';

                ySlider.min = conf.minY;
                ySlider.max = conf.maxY;
                ySlider.value = conf.yInput.value;
                yLabel.textContent = conf.yInput.value + '%';

                fontSizeSlider.min = conf.minSize;
                fontSizeSlider.max = conf.maxSize;
                fontSizeSlider.value = conf.sizeInput.value;
                fontSizeLabel.textContent = conf.sizeInput.value + ' px';
            }

            targetButtons.forEach(btn => btn.addEventListener('click', () => selectTarget(btn.dataset.target)));
            previewElements.forEach(el => el.addEventListener('click', (e) => { e.stopPropagation(); selectTarget(el.dataset.target); }));

            if (xSlider) {
                xSlider.addEventListener('input', function () {
                    const conf = targets[currentTargetKey];
                    if (!conf) return;
                    xLabel.textContent = this.value + '%';
                    conf.xInput.value = this.value;
                    conf.box.style.left = this.value + '%';
                });
            }

            if (ySlider) {
                ySlider.addEventListener('input', function () {
                    const conf = targets[currentTargetKey];
                    if (!conf) return;
                    yLabel.textContent = this.value + '%';
                    conf.yInput.value = this.value;
                    conf.box.style.top = this.value + '%';
                });
            }

            if (fontSizeSlider) {
                fontSizeSlider.addEventListener('input', function () {
                    const conf = targets[currentTargetKey];
                    if (!conf) return;
                    fontSizeLabel.textContent = this.value + ' px';
                    conf.sizeInput.value = this.value;
                    if (conf.textElem) conf.textElem.style.fontSize = (this.value * conf.sizeScale) + 'px';
                });
            }

            const placeInput = document.getElementById('input-place-of-entry');
            const previewPlaceText = document.getElementById('preview-place-text');
            if (placeInput && previewPlaceText) {
                placeInput.addEventListener('input', function () {
                    previewPlaceText.textContent = this.value || 'PBN Kelapa Gading, D.18 Jember East Java';
                });
            }

            selectTarget('name');
        });
    </script>
@endsection
