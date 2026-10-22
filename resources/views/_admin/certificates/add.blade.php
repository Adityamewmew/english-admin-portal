@extends('_admin._layout.app')

@section('title', 'Tambah Template Sertifikat')

@section('content')
    <x-admin.page-header :title="'Tambah Template Sertifikat'" subtitle="Buat template sertifikat kelulusan baru untuk kategori level CEFR.">
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
        <form action="{{ route('admin.certificates.create') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-admin.card fit="true" class="space-y-6 p-6">
                {{-- CEFR Selection --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Pilih Kategori Level CEFR <span class="text-rose-500">*</span>
                    </label>
                    <select name="cefr" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">-- Pilih Level CEFR --</option>
                        <option value="A1" {{ old('cefr') === 'A1' ? 'selected' : '' }}>A1 - Elementary / Starter Foundations</option>
                        <option value="A2" {{ old('cefr') === 'A2' ? 'selected' : '' }}>A2 - Pre-Intermediate Communication</option>
                        <option value="B1" {{ old('cefr') === 'B1' ? 'selected' : '' }}>B1 - Intermediate Practical English</option>
                        <option value="B2" {{ old('cefr') === 'B2' ? 'selected' : '' }}>B2 - Upper-Intermediate Fluency</option>
                        <option value="C1" {{ old('cefr') === 'C1' ? 'selected' : '' }}>C1 - Advanced Professional English</option>
                        <option value="C2" {{ old('cefr') === 'C2' ? 'selected' : '' }}>C2 - Mastery / Bilingual Proficiency</option>
                    </select>
                    <p class="text-[11px] text-gray-500 dark:text-neutral-400">Setiap level CEFR memiliki satu master template sertifikat.</p>
                </div>

                {{-- Judul Sertifikat --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Judul Sertifikat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', 'Certificate of CEFR Proficiency') }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                {{-- Deskripsi Kompetensi --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Deskripsi Capaian / Kompetensi
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-3.5 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('description') }}</textarea>
                    <p class="text-[11px] text-gray-500 dark:text-neutral-400">Contoh: Has demonstrated comprehensive competence in understanding and using everyday expressions and fundamental communication structures.</p>
                </div>

                {{-- Signatory Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Nama Penandatangan
                        </label>
                        <input type="text" name="instructor_name" value="{{ old('instructor_name', 'Mr. Khoirul, M.Pd.') }}"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Jabatan Penandatangan
                        </label>
                        <input type="text" name="instructor_title" value="{{ old('instructor_title', 'Head of English Academic Affairs') }}"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                {{-- Warna Tema --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Warna Aksen / Tema
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="theme_color" value="{{ old('theme_color', '#0D3B66') }}"
                               class="size-10 rounded cursor-pointer border border-gray-300 dark:border-neutral-700 p-0.5">
                        <span class="text-xs font-mono text-gray-600 dark:text-neutral-400">Warna lencana level pada sertifikat</span>
                    </div>
                </div>

                {{-- Background Upload --}}
                <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-800">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                        Unggah Background Sertifikat (Opsional)
                    </label>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Format landscape (rasio A4: 1.414 : 1, resolusi minimal 1754 x 1240 px). Format: JPG, PNG, WEBP (maksimal 5 MB). Jika dikosongkan, sertifikat akan menggunakan background template resmi EDDY'S.
                    </p>
                    <input type="file" name="background_file" accept="image/png,image/jpeg,image/webp"
                           class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-600 cursor-pointer">
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-end gap-3">
                    <x-admin.button href="{{ route('admin.certificates.index') }}" color="white" size="sm">
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
@endsection
