@extends('_admin._layout.app')

@section('title', 'Edit Template Sertifikat CEFR ' . $cefr)

@section('content')
    <x-admin.page-header :title="'Edit Template Sertifikat CEFR ' . $cefr" subtitle="Sesuaikan judul, deskripsi pencapaian, dan unggah background kustom untuk kategori ini.">
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
        <form action="{{ route('admin.certificates.update', ['cefr' => $cefr]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-admin.card variant="white" padding="p-6 sm:p-8" class="space-y-6">
                {{-- CEFR Level Indicator --}}
                <div class="flex items-center gap-3 p-4 rounded-xl bg-surface border border-slate-200 border-b-2 border-b-slate-300 dark:bg-neutral-800/60 dark:border-neutral-700">
                    <x-admin.badge :cefr="$cefr" size="lg">
                        {{ $cefr }}
                    </x-admin.badge>
                    <div>
                        <h4 class="font-extrabold text-primary dark:text-white">Kategori Tingkat CEFR: {{ $cefr }}</h4>
                        <p class="text-xs text-slate-500 dark:text-neutral-400">Template ini akan otomatis disematkan pada sertifikat siswa yang menuntaskan seluruh sub-level {{ $cefr }}.</p>
                    </div>
                </div>

                {{-- Judul Sertifikat --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Judul Sertifikat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $template->title) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                {{-- Deskripsi Kompetensi --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Deskripsi Capaian / Kompetensi
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-3.5 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('description', $template->description) }}</textarea>
                    <p class="text-[11px] text-gray-500 dark:text-neutral-400">Teks ini menjelaskan kompetensi yang berhasil dikuasai siswa pada sertifikat.</p>
                </div>

                {{-- Signatory Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Nama Penandatangan
                        </label>
                        <input type="text" name="instructor_name" value="{{ old('instructor_name', $template->instructor_name) }}"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Jabatan Penandatangan
                        </label>
                        <input type="text" name="instructor_title" value="{{ old('instructor_title', $template->instructor_title) }}"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                {{-- Place of Entry & Orientasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Tempat / Lokasi Ujian (Place of Entry)
                        </label>
                        <input type="text" name="place_of_entry" value="{{ old('place_of_entry', $template->place_of_entry ?? 'PBN Kelapa Gading, D.18 Jember East Java') }}"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Orientasi Cetak Sertifikat
                        </label>
                        <select name="orientation"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="portrait" {{ old('orientation', $template->orientation ?? 'portrait') === 'portrait' ? 'selected' : '' }}>A4 Portrait (Resmi Eddy's Local Examination)</option>
                            <option value="landscape" {{ old('orientation', $template->orientation ?? 'portrait') === 'landscape' ? 'selected' : '' }}>A4 Landscape (Mendatar)</option>
                        </select>
                    </div>
                </div>

                {{-- Warna Tema --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Warna Aksen / Tema
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="theme_color" value="{{ old('theme_color', $template->theme_color ?? '#0D3B66') }}"
                               class="size-10 rounded cursor-pointer border border-gray-300 dark:border-neutral-700 p-0.5">
                        <span class="text-xs font-mono text-gray-600 dark:text-neutral-400">{{ $template->theme_color ?? '#0D3B66' }}</span>
                    </div>
                </div>

                {{-- Background / Latar Belakang Sertifikat --}}
                <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Unggah Gambar Desain Sertifikat Baru (JPG, PNG)
                        </label>
                        <span class="text-[11px] font-semibold text-primary dark:text-primary-400 bg-primary/10 px-2 py-0.5 rounded">
                            Rasio A4 Landscape (1.414 : 1)
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Unggah gambar desain sertifikat baru (JPG, PNG, WEBP hingga 8MB). Kosongkan jika tidak ingin mengganti gambar yang sudah ada.
                    </p>

                    {{-- Image Preview Area with Interactive Controls --}}
                    <div id="preview-container" class="{{ $template->background_url ? '' : 'hidden' }} max-w-xl mx-auto my-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs font-semibold text-gray-700 dark:text-neutral-300">Preview Desain Sertifikat & Penempatan Nama:</p>
                            <span class="text-[11px] text-gray-500 dark:text-neutral-400">Geser slider di bawah untuk pas dengan garis</span>
                        </div>
                        <div class="relative aspect-[1.414/1] rounded-xl overflow-hidden border-2 border-primary/50 shadow-md bg-white select-none">
                            <img id="image-preview" src="{{ $template->background_url ?? '#' }}" alt="Preview Sertifikat" class="w-full h-full object-contain"
                                 onerror="this.style.opacity='0'; document.getElementById('no-image-notice')?.classList.remove('hidden');"
                                 onload="this.style.opacity='1'; document.getElementById('no-image-notice')?.classList.add('hidden');">
                            
                            {{-- Notice if image is missing --}}
                            <div id="no-image-notice" class="hidden absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-gray-50/90 dark:bg-neutral-800/90 backdrop-blur-xs">
                                <svg class="size-8 text-amber-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <p class="text-xs font-bold text-gray-800 dark:text-neutral-200 mb-0.5">File Gambar Desain Perlu Diunggah</p>
                                <p class="text-[11px] text-gray-500 dark:text-neutral-400">Pilih file gambar pada kotak upload di bawah untuk menampilkan desain sertifikat.</p>
                            </div>

                            {{-- Dynamic Student Name Overlay --}}
                            <div id="name-overlay-box" class="absolute left-0 right-0 flex items-center justify-center pointer-events-none px-6 transition-all duration-75"
                                 style="top: {{ old('name_y_percent', $template->name_y_percent ?? 48) }}%; transform: translateY(-50%);">
                                <span id="name-overlay-text" class="font-serif font-black text-[#0D3B66] text-center drop-shadow-xs tracking-wide leading-none"
                                      style="font-size: {{ (old('name_font_size', $template->name_font_size ?? 36) * 0.75) }}px;">
                                    Muhammad Aditya Pratama
                                </span>
                            </div>
                        </div>

                        {{-- Interactive Position & Font Size Sliders --}}
                        <div class="mt-3.5 p-3.5 rounded-xl bg-gray-50 dark:bg-neutral-800/60 border border-gray-200 dark:border-neutral-700 space-y-3 text-left">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                                    <svg class="size-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                    Kustomisasi Peletakan Nama Siswa
                                </span>
                                <span class="text-[11px] text-primary font-medium">Live Interaktif</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Vertical Position Slider (Y%) --}}
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center text-xs">
                                        <label for="y-slider" class="font-semibold text-gray-700 dark:text-neutral-300">
                                            Posisi Vertikal (Y):
                                        </label>
                                        <span id="y-value-label" class="font-mono font-bold text-primary">{{ old('name_y_percent', $template->name_y_percent ?? 48) }}%</span>
                                    </div>
                                    <input type="range" id="y-slider" name="name_y_percent" min="15" max="85" value="{{ old('name_y_percent', $template->name_y_percent ?? 48) }}"
                                           class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                                    <div class="flex justify-between text-[10px] text-gray-400">
                                        <span>Atas (15%)</span>
                                        <span>Tengah (50%)</span>
                                        <span>Bawah (85%)</span>
                                    </div>
                                </div>

                                {{-- Font Size Slider (px) --}}
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center text-xs">
                                        <label for="font-size-slider" class="font-semibold text-gray-700 dark:text-neutral-300">
                                            Ukuran Font:
                                        </label>
                                        <span id="font-size-label" class="font-mono font-bold text-primary">{{ old('name_font_size', $template->name_font_size ?? 36) }} px</span>
                                    </div>
                                    <input type="range" id="font-size-slider" name="name_font_size" min="16" max="64" value="{{ old('name_font_size', $template->name_font_size ?? 36) }}"
                                           class="w-full h-2 bg-gray-200 dark:bg-neutral-700 rounded-lg appearance-none cursor-pointer accent-primary">
                                    <div class="flex justify-between text-[10px] text-gray-400">
                                        <span>Kecil (16px)</span>
                                        <span>Sedang (36px)</span>
                                        <span>Besar (64px)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Dropzone Box --}}
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-neutral-700 border-dashed rounded-xl hover:border-primary transition-colors bg-gray-50/50 dark:bg-neutral-800/30">
                        <div class="space-y-2 text-center w-full">
                            <div class="flex flex-col items-center">
                                <svg class="size-10 text-gray-400 dark:text-neutral-500 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-gray-600 dark:text-neutral-400">
                                    <label for="file-upload-edit" class="relative cursor-pointer font-bold text-primary hover:text-primary-600 focus-within:outline-hidden">
                                        <span>Pilih file gambar baru</span>
                                        <input id="file-upload-edit" name="background_file" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="sr-only">
                                    </label>
                                    <p class="pl-1">atau ganti gambar di sini</p>
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-neutral-500 mt-1">PNG, JPG, WEBP hingga 8MB</p>
                            </div>
                        </div>
                    </div>
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
                        Simpan Perubahan Template
                    </x-admin.button>
                </div>
            </x-admin.card>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('file-upload-edit');
            const previewContainer = document.getElementById('preview-container');
            const imagePreview = document.getElementById('image-preview');

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.opacity = '1';
                            document.getElementById('no-image-notice')?.classList.add('hidden');
                            previewContainer.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Sliders for dynamic name placement
            const ySlider = document.getElementById('y-slider');
            const yLabel = document.getElementById('y-value-label');
            const fontSizeSlider = document.getElementById('font-size-slider');
            const fontSizeLabel = document.getElementById('font-size-label');
            const nameOverlayBox = document.getElementById('name-overlay-box');
            const nameOverlayText = document.getElementById('name-overlay-text');

            if (ySlider && nameOverlayBox && yLabel) {
                ySlider.addEventListener('input', function () {
                    const val = this.value;
                    yLabel.textContent = val + '%';
                    nameOverlayBox.style.top = val + '%';
                });
            }

            if (fontSizeSlider && nameOverlayText && fontSizeLabel) {
                fontSizeSlider.addEventListener('input', function () {
                    const val = this.value;
                    fontSizeLabel.textContent = val + ' px';
                    nameOverlayText.style.fontSize = (val * 0.75) + 'px';
                });
            }
        });
    </script>
@endsection
