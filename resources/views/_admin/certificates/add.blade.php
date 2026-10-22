@extends('_admin._layout.app')

@section('title', 'Tambah Template Sertifikat')

@section('content')
    <x-admin.page-header :title="'Tambah Template Sertifikat'" subtitle="Unggah gambar desain sertifikat kosongan (JPG/PNG) untuk kategori level CEFR. Nama siswa akan otomatis dicetak pada sertifikat ini.">
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

                {{-- 2. File Upload Box with Instant Preview --}}
                <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            2. Unggah Gambar Desain Sertifikat (JPG, PNG) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-semibold text-primary dark:text-primary-400 bg-primary/10 px-2 py-0.5 rounded">
                            Rasio A4 Landscape (1.414 : 1)
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Unggah file gambar sertifikat (kosongkan bagian nama siswa). Format yang didukung: <strong>JPG, PNG, WEBP</strong> (maksimal 8 MB). Sistem akan otomatis mencetak nama siswa yang lulus di atas gambar ini.
                    </p>

                    {{-- Upload Dropzone Box --}}
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-neutral-700 border-dashed rounded-xl hover:border-primary transition-colors bg-gray-50/50 dark:bg-neutral-800/30">
                        <div class="space-y-2 text-center w-full">
                            {{-- Image Preview Area --}}
                            <div id="preview-container" class="hidden max-w-lg mx-auto mb-3">
                                <p class="text-xs font-semibold text-gray-600 dark:text-neutral-300 mb-1.5">Preview Desain Sertifikat:</p>
                                <div class="relative aspect-[1.414/1] rounded-lg overflow-hidden border-2 border-primary shadow-md bg-white">
                                    <img id="image-preview" src="#" alt="Preview Sertifikat" class="w-full h-full object-contain">
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="bg-black/60 backdrop-blur-xs px-4 py-1.5 rounded-full text-white text-xs font-serif font-bold tracking-wider border border-white/30">
                                            [ Area Nama Siswa Akan Muncul di Sini ]
                                        </div>
                                    </div>
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

                {{-- 3. Detail Opsional (Bisa Dilipat/Collapsible) --}}
                <div class="pt-4 border-t border-gray-100 dark:border-neutral-800">
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer list-none text-xs font-bold text-gray-600 dark:text-neutral-300 hover:text-primary">
                            <span>Informasi Teks Sertifikat (Otomatis Terisi, Klik untuk Ubah)</span>
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

                            {{-- Signatory Fields --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                        Nama Penandatangan
                                    </label>
                                    <input type="text" name="instructor_name" value="{{ old('instructor_name', 'Mr. Khoirul, M.Pd.') }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                        Jabatan Penandatangan
                                    </label>
                                    <input type="text" name="instructor_title" value="{{ old('instructor_title', 'Head of English Academic Affairs') }}"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs text-gray-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </details>
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
            const imagePreview = document.getElementById('image-preview');
            const cefrSelect = document.getElementById('cefr-select');
            const certTitle = document.getElementById('cert-title');
            const certDescription = document.getElementById('cert-description');

            const cefrTemplates = {
                'A1': {
                    title: 'Certificate of CEFR A1 Elementary English Proficiency',
                    description: 'Has demonstrated comprehensive competence in understanding and using familiar everyday expressions, basic phrases, and fundamental communication structures for real-world interactions.'
                },
                'A2': {
                    title: 'Certificate of CEFR A2 Pre-Intermediate English Proficiency',
                    description: 'Has demonstrated proven ability to communicate in routine tasks, exchange direct information on familiar topics, and describe aspects of background, immediate environment, and personal matters.'
                },
                'B1': {
                    title: 'Certificate of CEFR B1 Intermediate English Proficiency',
                    description: 'Has demonstrated solid competence to handle most communicative situations, produce connected text on familiar topics, and describe experiences, events, dreams, hopes, and ambitions with reasoned explanations.'
                },
                'B2': {
                    title: 'Certificate of CEFR B2 Upper-Intermediate English Proficiency',
                    description: 'Has achieved fluency and spontaneity in interacting with native speakers, understanding complex technical texts, and producing clear, detailed arguments on a wide range of contemporary subjects.'
                },
                'C1': {
                    title: 'Certificate of CEFR C1 Advanced English Proficiency',
                    description: 'Has attained advanced mastery in comprehending demanding, longer academic and professional discourses, expressing ideas fluently without obvious searching for expressions, and using language flexibly for social and professional purposes.'
                },
                'C2': {
                    title: 'Certificate of CEFR C2 Mastery English Proficiency',
                    description: 'Has attained bilingual-level precision and effortless fluency, understanding virtually everything heard or read, summarizing information from different spoken and written sources, and reconstructing arguments coherently in spontaneous presentation.'
                }
            };

            // CEFR Selection Change -> Auto Fill Title & Description
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
        });
    </script>
@endsection
