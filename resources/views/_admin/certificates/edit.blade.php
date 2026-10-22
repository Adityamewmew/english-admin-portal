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
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Unggah Gambar Latar Belakang (Background Template)
                    </label>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Format landscape (rasio A4: 1.414 : 1, resolusi rekomendasi minimal 1754 x 1240 px). Format: JPG, PNG, WEBP (maksimal 5 MB).
                    </p>

                    @if ($template->background_url)
                        <div class="mb-3 p-3 rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/40 max-w-md">
                            <p class="text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">Background Saat Ini:</p>
                            <img src="{{ $template->background_url }}" alt="Background {{ $cefr }}" class="w-full h-auto rounded-lg border border-gray-200 dark:border-neutral-700 shadow-xs">
                        </div>
                    @endif

                    <input type="file" name="background_file" accept="image/png,image/jpeg,image/webp"
                           class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-600 cursor-pointer">
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
@endsection
