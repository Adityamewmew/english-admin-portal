@extends('_admin._layout.app')

@section('title', 'Edit Modul - ' . $module->title)

@section('content')
    <div class="max-w-4xl space-y-6">
        <x-admin.page-header :title="'Edit Modul: ' . $module->title" :subtitle="'ID Modul: ' . $module->id . ' • Level ' . $module->cefr" :backUrl="route('admin.modules.detail', $module->id)" />

        <x-admin.card variant="white">
            <form class="space-y-6" navigate-form action="{{ route('admin.modules.doUpdate', $module->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 1. IDENTITAS & LEVEL --}}
                <div>
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        1. Identitas & Level Kurikulum
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-admin.input
                            label="Kode / ID Modul"
                            name="id_disabled"
                            value="{{ $module->id }}"
                            disabled
                            class="font-mono"
                        />

                        <x-admin.select
                            label="Level CEFR"
                            name="cefr"
                            required
                            :value="old('cefr', $module->cefr)"
                            :options="['A1' => 'Level A1 (Beginner)', 'A2' => 'Level A2 (Elementary)', 'B1' => 'Level B1 (Intermediate)', 'B2' => 'Level B2 (Upper Intermediate)', 'C1' => 'Level C1 (Advanced)', 'C2' => 'Level C2 (Mastery)']"
                        />
                    </div>
                </div>

                {{-- 2. INFORMASI MODUL --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        2. Informasi Modul
                    </h3>
                    <div class="space-y-4">
                        <x-admin.input
                            label="Judul Modul"
                            name="title"
                            value="{{ old('title', $module->title) }}"
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
                                :value="old('level_id', $module->level_id)"
                                :options="$levelOptions"
                            />

                            <x-admin.input
                                label="Kategori / Topik Pembelajaran"
                                name="group"
                                value="{{ old('group', $module->group) }}"
                                required
                            />
                        </div>

                        <div>
                            <label for="objective" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                                Tujuan Pembelajaran (Objective)
                            </label>
                            <textarea id="objective" name="objective" rows="3"
                                class="py-2.5 px-3.5 block w-full bg-white border border-slate-300 rounded-[10px] text-slate-800 placeholder-slate-400 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 text-sm leading-relaxed">{{ old('objective', $module->objective) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 3. BANNER MODUL --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        3. Banner Gambar Modul
                    </h3>
                    <div>
                        <div class="flex items-center gap-4">
                            <input type="file" id="banner" name="banner" accept="image/png,image/jpeg,image/jpg"
                                onchange="previewBanner(this)"
                                class="py-2 px-3 block w-full border border-slate-300 rounded-[10px] text-sm file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-surface file:text-primary hover:file:bg-slate-200 dark:file:bg-neutral-800 dark:file:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 cursor-pointer shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)]">
                            <div id="banner-preview-wrap" class="{{ empty($module->banner_url) ? 'hidden' : '' }} shrink-0 size-16 rounded-xl overflow-hidden border border-slate-200 dark:border-neutral-700 bg-surface shadow-sm">
                                <img id="banner-preview" src="{{ $module->banner_url ?? '#' }}" alt="Preview Banner" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Format file: JPG, JPEG, PNG (Maksimal 5MB). Biarkan kosong jika tidak ingin mengubah banner.</p>
                        @error('banner')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 4. PARAMETER KELULUSAN & EVALUASI --}}
                <div class="pt-4 border-t border-slate-200 dark:border-neutral-700/80">
                    <h3 class="font-heading font-extrabold text-xs text-primary/80 uppercase tracking-wider mb-3">
                        4. Parameter Pembelajaran
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <x-admin.select
                            label="Tingkat Kesulitan"
                            name="complexity"
                            required
                            :value="old('complexity', $module->complexity)"
                            :options="['easy' => 'Mudah (Easy)', 'medium' => 'Menengah (Medium)', 'hard' => 'Sulit (Hard)']"
                        />

                        <x-admin.input
                            label="Estimasi Waktu (Menit)"
                            name="estimated_minutes"
                            type="number"
                            value="{{ old('estimated_minutes', $module->estimated_minutes) }}"
                            min="1"
                            required
                        />

                        <x-admin.input
                            label="Nilai Kelulusan (Passing Score %)"
                            name="passing_score"
                            type="number"
                            value="{{ old('passing_score', $module->passing_score) }}"
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
                            value="{{ old('order_index', $module->order_index) }}"
                            min="1"
                        />

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:pt-6">
                            <div class="flex items-center gap-x-2.5">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $module->is_active ?? true) ? 'checked' : '' }}
                                    class="shrink-0 border-slate-300 rounded text-emerald-600 focus:ring-emerald-500 size-4 cursor-pointer">
                                <label for="is_active" class="text-xs font-semibold text-slate-700 dark:text-neutral-200 cursor-pointer">
                                    Modul Aktif (Dapat Diakses Siswa)
                                </label>
                            </div>

                            <div class="flex items-center gap-x-2.5">
                                <input type="checkbox" id="is_exam" name="is_exam" value="1" {{ old('is_exam', $module->is_exam) ? 'checked' : '' }}
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
                    <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}" color="outline-secondary" size="sm">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="secondary" size="sm">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan Perubahan
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
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
    </script>
@endsection
