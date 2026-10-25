@extends('_admin._layout.app')

@section('title', 'Tambah Modul Kurikulum')

@section('content')
    <div class="max-w-4xl space-y-6">
        <x-admin.page-header title="Tambah Modul Kurikulum" subtitle="Susun unit pembelajaran baru berdasarkan standar level CEFR" :backUrl="route('admin.modules.index')" />

        <x-admin.card variant="white">
            <form class="space-y-6" navigate-form action="{{ route('admin.modules.create') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 1. IDENTITAS & LEVEL --}}
                <div>
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        1. Identitas & Level Kurikulum
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-admin.input
                            label="Kode / ID Modul (Otomatis jika kosong)"
                            name="id"
                            value="{{ old('id') }}"
                            placeholder="Contoh: MOD-A1.1-01"
                            error="{{ $errors->first('id') }}"
                            class="font-mono"
                        />

                        <x-admin.select
                            label="Level CEFR"
                            name="cefr"
                            required
                            :value="old('cefr', 'A1')"
                            :options="['A1' => 'Level A1 (Beginner)', 'A2' => 'Level A2 (Elementary)', 'B1' => 'Level B1 (Intermediate)', 'B2' => 'Level B2 (Upper Intermediate)', 'C1' => 'Level C1 (Advanced)', 'C2' => 'Level C2 (Mastery)']"
                        />
                    </div>
                </div>

                {{-- 2. INFORMASI DASAR --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        2. Informasi Modul
                    </h3>
                    <div class="space-y-4">
                        <x-admin.input
                            label="Judul Modul"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Contoh: Introducing Yourself & Daily Greetings"
                            required
                            error="{{ $errors->first('title') }}"
                        />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $levelOptions = [];
                                foreach ($levels as $lvl) {
                                    $levelOptions[$lvl->id] = $lvl->id . ' - ' . $lvl->title;
                                }
                                if (empty($levelOptions)) {
                                    foreach (['A1.1', 'A1.2', 'A2.1', 'A2.2', 'B1.1', 'B1.2', 'B2.1', 'B2.2', 'C1.1', 'C1.2', 'C2.1', 'C2.2'] as $l) {
                                        $levelOptions[$l] = $l;
                                    }
                                }
                            @endphp

                            <x-admin.select
                                label="Sub-Level Kurikulum"
                                name="level_id"
                                required
                                :value="old('level_id')"
                                :options="$levelOptions"
                            />

                            <x-admin.input
                                label="Kategori / Topik Pembelajaran"
                                name="group"
                                value="{{ old('group', 'Daily Communication') }}"
                                placeholder="Contoh: Daily Life, Travel, Career"
                                required
                            />
                        </div>

                        <div>
                            <label for="objective" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                                Tujuan Pembelajaran (Objective)
                            </label>
                            <textarea id="objective" name="objective" rows="3"
                                class="py-2.5 px-3.5 block w-full bg-white border border-slate-300 rounded-[10px] text-slate-800 placeholder-slate-400 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 text-sm leading-relaxed"
                                placeholder="Jelaskan kompetensi dan kemampuan bahasa Inggris yang akan dikuasai siswa pada unit ini...">{{ old('objective') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 3. DOKUMEN BAHAN AJAR (OPSIONAL) --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <div class="flex items-center justify-between mb-1.5">
                        <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider">
                            3. Dokumen Bahan Ajar / Referensi Kurikulum (Opsional)
                        </h3>
                        <span class="text-[11px] font-bold text-secondary bg-secondary/10 px-2 py-0.5 rounded-md">
                            AI Scanner Support
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-neutral-400 mb-3 leading-relaxed">
                        Unggah modul, silabus, atau buku materi dalam format <strong>PDF, Word (DOCX/DOC), atau Foto/Scan (JPG/PNG)</strong>. AI akan otomatis memindai isi dokumen ini dan menyusun materi kurikulum saat Anda memilih mode generate di halaman detail.
                    </p>
                    <div>
                        <input type="file" id="document" name="document" accept=".pdf,.docx,.doc,.jpg,.jpeg,.png"
                            class="py-2.5 px-3 block w-full border border-slate-300 rounded-[10px] text-sm file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary/90 dark:file:bg-neutral-800 dark:file:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 cursor-pointer shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)]">
                        <p class="text-[11px] text-slate-400 mt-1.5">Maksimal ukuran file: 20MB. Dokumen hanya diproses di memori tanpa disimpan permanen ke database.</p>
                        @error('document')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 4. BANNER MODUL --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        4. Banner Gambar Modul
                    </h3>
                    <div>
                        <div class="flex items-center gap-4">
                            <input type="file" id="banner" name="banner" accept="image/png,image/jpeg,image/jpg"
                                onchange="previewBanner(this)"
                                class="py-2 px-3 block w-full border border-slate-300 rounded-[10px] text-sm file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-surface file:text-primary hover:file:bg-slate-200 dark:file:bg-neutral-800 dark:file:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 cursor-pointer shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)]">
                            <div id="banner-preview-wrap" class="hidden shrink-0 size-16 rounded-xl overflow-hidden border border-slate-200 dark:border-neutral-700 bg-surface shadow-sm">
                                <img id="banner-preview" src="#" alt="Preview Banner" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Format file: JPG, JPEG, PNG (Maksimal 5MB). Jika dikosongkan, ilustrasi tematik akan otomatis digunakan.</p>
                        @error('banner')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 5. PARAMETER KELULUSAN & EVALUASI --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        5. Parameter Pembelajaran
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <x-admin.select
                            label="Tingkat Kesulitan"
                            name="complexity"
                            required
                            :value="old('complexity', 'medium')"
                            :options="['easy' => 'Mudah (Easy)', 'medium' => 'Menengah (Medium)', 'hard' => 'Sulit (Hard)']"
                        />

                        <x-admin.input
                            label="Estimasi Waktu (Menit)"
                            name="estimated_minutes"
                            type="number"
                            value="{{ old('estimated_minutes', 15) }}"
                            min="1"
                            required
                        />

                        <x-admin.input
                            label="Nilai Kelulusan (Passing Score %)"
                            name="passing_score"
                            type="number"
                            value="{{ old('passing_score', 70) }}"
                            min="0"
                            max="100"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-4">
                        <x-admin.input
                            label="Urutan Modul"
                            name="order_index"
                            type="number"
                            value="{{ old('order_index', 1) }}"
                            min="1"
                        />

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:pt-6">
                            <div class="flex items-center gap-x-2.5">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="shrink-0 border-slate-300 rounded text-emerald-600 focus:ring-emerald-500 size-4 cursor-pointer">
                                <label for="is_active" class="text-xs font-semibold text-slate-700 dark:text-neutral-200 cursor-pointer">
                                    Modul Aktif (Dapat Diakses Siswa)
                                </label>
                            </div>

                            <div class="flex items-center gap-x-2.5">
                                <input type="checkbox" id="is_exam" name="is_exam" value="1" {{ old('is_exam') ? 'checked' : '' }}
                                    class="shrink-0 border-slate-300 rounded text-secondary focus:ring-secondary size-4 cursor-pointer">
                                <label for="is_exam" class="text-xs font-semibold text-slate-700 dark:text-neutral-200 cursor-pointer">
                                    Ujian Akhir (Checkpoint)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80 flex items-center justify-end gap-x-3">
                    <x-admin.button href="{{ route('admin.modules.index') }}" color="outline-secondary" size="sm">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" id="btnSubmitModule" color="secondary" size="sm">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan & Masuk ke Penyusunan Materi
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>

    {{-- SCANNING LOADING OVERLAY --}}
    <div id="scanLoadingOverlay" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-slate-950/80 backdrop-blur-md p-6 text-center text-white transition-all">
        <div class="relative flex items-center justify-center size-24 mb-6">
            <div class="absolute inset-0 rounded-full bg-amber-500/20 animate-ping"></div>
            <div class="relative size-20 rounded-2xl bg-secondary border border-amber-300/40 flex items-center justify-center shadow-2xl">
                <svg class="size-10 text-white animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-heading font-extrabold tracking-tight mb-2 text-white">
            Sedang Memindai Dokumen Bahan Ajar...
        </h3>
        <p class="text-sm text-slate-300 max-w-md leading-relaxed">
            AI sedang mengekstrak teks & menganalisis materi kurikulum Anda. Setelah selesai, Anda akan diarahkan ke halaman detail untuk memilih mode generate.
        </p>
        <div class="mt-6 flex items-center gap-2.5 text-xs font-semibold text-amber-300 bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/15 shadow-sm">
            <svg class="animate-spin size-4 text-amber-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Memproses teks & menyiapkan 5 sub-modul...</span>
        </div>
    </div>

    <script>
        function previewBanner(input) {
            const previewWrap = document.getElementById('banner-preview-wrap');
            const preview = document.getElementById('banner-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewWrap.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('admin.modules.create') }}"]');
            if (form) {
                form.addEventListener('submit', function() {
                    if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                        return;
                    }
                    const docInput = document.getElementById('document');
                    const overlay = document.getElementById('scanLoadingOverlay');
                    if (docInput && docInput.files && docInput.files.length > 0 && overlay) {
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    }
                });
            }

            if (window.jQuery) {
                $(document).ajaxError(function() {
                    const overlay = document.getElementById('scanLoadingOverlay');
                    if (overlay) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                    }
                });
            }
        });
    </script>
@endsection
