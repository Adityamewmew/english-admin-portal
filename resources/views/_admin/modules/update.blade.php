@extends('_admin._layout.app')

@section('title', 'Edit Modul - ' . $module->title)

@section('content')
    <div class="max-w-4xl space-y-6">
        <x-admin.page-header :title="'Edit Modul: ' . $module->title" :subtitle="'ID Modul: ' . $module->id . ' • Level ' . $module->cefr" :backUrl="route('admin.modules.detail', $module->id)" />

        <x-admin.card fit="true">
            <form class="space-y-6 p-2 sm:p-4" navigate-form action="{{ route('admin.modules.doUpdate', $module->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 1. IDENTITAS & LEVEL --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">1. Identitas & Level Kurikulum</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Kode / ID Modul
                            </label>
                            <input type="text" value="{{ $module->id }}" disabled
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm font-mono bg-gray-100 dark:bg-neutral-900/50 dark:border-neutral-700 dark:text-neutral-400 cursor-not-allowed">
                        </div>

                        <div>
                            <label for="cefr" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Level CEFR <span class="text-rose-500">*</span>
                            </label>
                            <select id="cefr" name="cefr"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 font-semibold" required>
                                @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $c)
                                    <option value="{{ $c }}" {{ old('cefr', $module->cefr) == $c ? 'selected' : '' }}>Level {{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. INFORMASI MODUL --}}
                <div class="pt-2 border-t border-gray-100 dark:border-neutral-700/80">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">2. Informasi Modul</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Judul Modul <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $module->title) }}"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('title') border-rose-500 @enderror" required>
                            @error('title')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="level_id" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                    Sub-Level Kurikulum <span class="text-rose-500">*</span>
                                </label>
                                <select id="level_id" name="level_id"
                                    class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                                    @forelse ($levels as $lvl)
                                        <option value="{{ $lvl->id }}" {{ old('level_id', $module->level_id) == $lvl->id ? 'selected' : '' }}>
                                            {{ $lvl->id }} - {{ $lvl->title }}
                                        </option>
                                    @empty
                                        @foreach (['A1.1', 'A1.2', 'A2.1', 'A2.2', 'B1.1', 'B1.2', 'B2.1', 'B2.2', 'C1.1', 'C1.2', 'C2.1', 'C2.2'] as $l)
                                            <option value="{{ $l }}" {{ old('level_id', $module->level_id) == $l ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    @endforelse
                                </select>
                            </div>

                            <div>
                                <label for="group" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                    Kategori / Topik Pembelajaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="group" name="group" value="{{ old('group', $module->group) }}"
                                    class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>

                        <div>
                            <label for="objective" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Tujuan Pembelajaran (Objective)
                            </label>
                            <textarea id="objective" name="objective" rows="3"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 leading-relaxed">{{ old('objective', $module->objective) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 3. BANNER MODUL --}}
                <div class="pt-2 border-t border-gray-100 dark:border-neutral-700/80">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">3. Banner Gambar Modul</h3>
                    <div>
                        <div class="flex items-center gap-4">
                            <input type="file" id="banner" name="banner" accept="image/png,image/jpeg,image/jpg"
                                onchange="previewBanner(this)"
                                class="py-2 px-3 block w-full border border-gray-200 rounded-xl text-sm file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950 dark:file:text-blue-300 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 cursor-pointer">
                            <div id="banner-preview-wrap" class="{{ empty($module->banner_url) ? 'hidden' : '' }} shrink-0 size-16 rounded-xl overflow-hidden border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900 shadow-2xs">
                                <img id="banner-preview" src="{{ $module->banner_url ?? '#' }}" alt="Banner" class="w-full h-full object-cover">
                            </div>
                        </div>
                        @if(!empty($module->banner_url))
                            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1.5">Banner aktif saat ini tersimpan di: <span class="font-mono text-gray-700 dark:text-neutral-300">{{ $module->banner_url }}</span> (Unggah file baru untuk menggantinya)</p>
                        @endif
                        @error('banner')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 4. PARAMETER PEMBELAJARAN --}}
                <div class="pt-2 border-t border-gray-100 dark:border-neutral-700/80">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">4. Parameter Pembelajaran</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="complexity" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Tingkat Kesulitan <span class="text-rose-500">*</span>
                            </label>
                            <select id="complexity" name="complexity"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="easy" {{ old('complexity', $module->complexity) == 'easy' ? 'selected' : '' }}>Mudah (Easy)</option>
                                <option value="medium" {{ old('complexity', $module->complexity) == 'medium' ? 'selected' : '' }}>Menengah (Medium)</option>
                                <option value="hard" {{ old('complexity', $module->complexity) == 'hard' ? 'selected' : '' }}>Sulit (Hard)</option>
                            </select>
                        </div>

                        <div>
                            <label for="estimated_minutes" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Estimasi Waktu (Menit)
                            </label>
                            <input type="number" id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes', $module->estimated_minutes) }}" min="1"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label for="passing_score" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Nilai Kelulusan (Passing Score %)
                            </label>
                            <input type="number" id="passing_score" name="passing_score" value="{{ old('passing_score', $module->passing_score) }}" min="0" max="100"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-3">
                        <div>
                            <label for="order_index" class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                Urutan Modul
                            </label>
                            <input type="number" id="order_index" name="order_index" value="{{ old('order_index', $module->order_index) }}" min="1"
                                class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-xl text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex items-center gap-x-3 sm:pt-5">
                            <input type="checkbox" id="is_exam" name="is_exam" value="1" {{ old('is_exam', $module->is_exam) ? 'checked' : '' }}
                                class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 size-4">
                            <label for="is_exam" class="text-sm font-medium text-gray-700 dark:text-neutral-200 cursor-pointer">
                                Tetapkan sebagai Modul Ujian Akhir (Checkpoint)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="pt-4 border-t border-gray-100 dark:border-neutral-700/80 flex justify-end gap-x-2">
                    <x-admin.button href="{{ route('admin.modules.detail', $module->id) }}" color="secondary">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary" class="font-bold">
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
