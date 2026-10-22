@extends('_admin._layout.app')

@section('title', 'Tambah Paket Kelas Baru')

@section('content')
    <x-admin.page-header :title="'Tambah Paket Kelas Baru'" subtitle="Buat paket kelas baru untuk level CEFR dengan tarif akses dan fasilitas materi.">
        <x-admin.button href="{{ route('admin.classes.index') }}" size="sm" color="outline-secondary">
            Kembali ke Daftar
        </x-admin.button>
    </x-admin.page-header>

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 border-b-2 border-b-rose-300 text-rose-800 dark:text-rose-200 text-sm flex items-center gap-2 shadow-2xs">
            <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <x-admin.card variant="white" padding="p-6 sm:p-7">
            <form action="{{ route('admin.classes.create') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Level CEFR Selector --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                        Kategori Level CEFR <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                        @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $c)
                            <label class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 dark:border-neutral-700 hover:bg-surface cursor-pointer transition-all has-checked:border-primary has-checked:border-b-3 has-checked:border-b-primary-dark has-checked:bg-surface has-checked:shadow-2xs">
                                <input type="radio" name="cefr" value="{{ $c }}" {{ old('cefr') === $c ? 'checked' : '' }} required class="sr-only">
                                <span class="text-sm font-black text-primary dark:text-white">{{ $c }}</span>
                                <span class="text-[10px] text-slate-400">Level</span>
                            </label>
                        @endforeach
                    </div>
                    @error('cefr')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Paket Kelas --}}
                <div class="space-y-1.5">
                    <x-admin.input label="Nama Paket Kelas" name="name" :value="old('name')" placeholder="Contoh: Kelas Bahasa Inggris Dasar (CEFR A1)" required />
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tarif & Harga Coret --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <x-admin.input label="Tarif Akses (Rp)" type="number" name="price" :value="old('price', 149000)" placeholder="149000" min="0" required />
                        <span class="text-[11px] text-slate-400">Harga yang harus dibayar siswa untuk membuka level ini.</span>
                        @error('price')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <x-admin.input label="Harga Coret / Asli (Rp)" type="number" name="original_price" :value="old('original_price')" placeholder="249000" min="0" />
                        <span class="text-[11px] text-slate-400">Ditampilkan sebagai harga sebelum diskon.</span>
                        @error('original_price')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi Kelas --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                        Deskripsi Paket Kelas
                    </label>
                    <textarea name="description" rows="3" class="w-full text-xs rounded-[10px] border border-slate-300 bg-white p-3 text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 transition-all"
                              placeholder="Tuliskan ringkasan materi dan target pencapaian kelas...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Poin Fasilitas / Benefit --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                            Poin Fasilitas & Benefit Materi
                        </label>
                        <span class="text-[11px] text-slate-400">1 baris = 1 poin fasilitas</span>
                    </div>
                    <textarea name="features" rows="4" class="w-full text-xs font-mono rounded-[10px] border border-slate-300 bg-white p-3 text-slate-800 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200 transition-all"
                              placeholder="Akses penuh 24/7 seluruh modul pembelajaran&#10;Latihan speaking interaktif bersama AI&#10;Kuis harian & evaluasi pemahaman real-time&#10;Sertifikat resmi kompetensi setelah lulus">{{ old('features') }}</textarea>
                    <span class="text-[11px] text-slate-400">Poin benefit yang didapat siswa setelah membeli paket kelas ini.</span>
                    @error('features')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status Aktif Switch --}}
                <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 bg-surface dark:bg-neutral-800/60 dark:border-neutral-700">
                    <div>
                        <p class="text-xs font-bold text-primary dark:text-white">Status Kelas Aktif</p>
                        <p class="text-[11px] text-slate-500 dark:text-neutral-400">Jika dinonaktifkan, siswa tidak dapat membeli akses kelas level ini.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-neutral-800">
                    <x-admin.button href="{{ route('admin.classes.index') }}" color="outline-secondary" size="sm">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary" size="sm">
                        Simpan & Buat Kelas
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
