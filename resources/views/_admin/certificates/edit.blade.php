@extends('_admin._layout.app')

@section('title', 'Edit Template Sertifikat CEFR ' . $cefr)

@section('content')
    <x-admin.page-header :title="'Edit Template Sertifikat CEFR ' . $cefr" subtitle="Sesuaikan judul, deskripsi pencapaian, dan unggah background kustom untuk kategori ini.">
        <x-admin.button href="{{ route('admin.certificates.index') }}" size="sm" color="white">
            <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </x-admin.button>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm">
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

            <x-admin.card fit="true" class="space-y-6 p-6">
                {{-- CEFR Level Indicator --}}
                <div class="flex items-center gap-3 p-4 rounded-xl bg-gray-50 dark:bg-neutral-800/60 border border-gray-200/80 dark:border-neutral-700">
                    <span class="size-12 rounded-xl flex items-center justify-center font-black text-white text-base shadow-sm" style="background-color: {{ $template->theme_color ?? '#0D3B66' }}">
                        {{ $cefr }}
                    </span>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Kategori Tingkat CEFR: {{ $cefr }}</h4>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">Template ini akan otomatis disematkan pada sertifikat siswa yang menuntaskan seluruh sub-level {{ $cefr }}.</p>
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

                    {{-- Image Preview Area --}}
                    <div id="preview-container" class="{{ $template->background_url ? '' : 'hidden' }} max-w-lg mx-auto my-3">
                        <p class="text-xs font-semibold text-gray-600 dark:text-neutral-300 mb-1.5">Preview Desain Sertifikat:</p>
                        <div class="relative aspect-[1.414/1] rounded-lg overflow-hidden border-2 border-primary shadow-md bg-white">
                            <img id="image-preview" src="{{ $template->background_url ?? '#' }}" alt="Preview Sertifikat" class="w-full h-full object-contain">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="bg-black/60 backdrop-blur-xs px-4 py-1.5 rounded-full text-white text-xs font-serif font-bold tracking-wider border border-white/30">
                                    [ Area Nama Siswa Akan Muncul di Sini ]
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
                <div class="pt-4 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-end gap-3">
                    <x-admin.button href="{{ route('admin.certificates.index') }}" color="white" size="sm">
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
                            previewContainer.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endsection
