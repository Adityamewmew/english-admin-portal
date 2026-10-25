@extends('_admin._layout.app')

@section('title', 'Tambah Rekening Transfer Bank')

@section('content')
    <x-admin.page-header :title="'Tambah Rekening Bank Baru'" subtitle="Tambahkan rekening bank untuk metode pembayaran transfer manual siswa.">
        <x-admin.button href="{{ route('admin.payment.index', ['tab' => 'banks']) }}" size="sm" color="outline-secondary">
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
            <form action="{{ route('admin.payment.create') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Nama Bank & Preset Pilihan Cepat --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                        Nama Bank <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="bank_name_input" name="bank_name" value="{{ old('bank_name') }}"
                           placeholder="Contoh: BCA, Mandiri, BRI, BNI, BSI, CIMB Niaga..." required
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20">

                    {{-- Quick Fill Chips --}}
                    <div class="flex items-center gap-1.5 flex-wrap pt-1">
                        <span class="text-[10px] text-slate-400 font-medium">Pilihan cepat:</span>
                        @php
                            $presetBanks = [
                                'BCA (Bank Central Asia)',
                                'Bank Mandiri',
                                'BRI (Bank Rakyat Indonesia)',
                                'BNI (Bank Negara Indonesia)',
                                'BSI (Bank Syariah Indonesia)',
                            ];
                        @endphp
                        @foreach ($presetBanks as $name)
                            <button type="button"
                                    onclick="document.getElementById('bank_name_input').value = '{{ $name }}';"
                                    class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 hover:bg-primary/10 hover:text-primary transition-colors cursor-pointer border border-slate-200 dark:border-neutral-700">
                                {{ explode(' ', $name)[0] }}
                            </button>
                        @endforeach
                    </div>
                    @error('bank_name')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor Rekening --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                        Nomor Rekening Bank <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}"
                           placeholder="Contoh: 8920192831" required
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20 tracking-wider">
                    <span class="text-[11px] text-slate-400">Pastikan nomor rekening tepat dan tanpa spasi.</span>
                    @error('account_number')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Atas Nama Rekening --}}
                <div class="space-y-1.5">
                    <x-admin.input label="Atas Nama Rekening" name="account_name" :value="old('account_name')"
                                   placeholder="Contoh: KHOIRUL ANWAR atau PT EDDYS ENGLISH ACADEMY" required />
                    <span class="text-[11px] text-slate-400">Nama pemilik rekening yang tertera di mutasi bank.</span>
                    @error('account_name')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Logo Bank --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                        Upload Logo Bank (Opsional)
                    </label>
                    <input type="file" name="bank_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:cursor-pointer border border-slate-200 dark:border-neutral-700 rounded-xl bg-surface dark:bg-neutral-800 p-1">
                    <span class="text-[11px] text-slate-400">Pilih file logo bank (PNG, JPG, SVG, WebP transparan).</span>
                    @error('bank_logo')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instruksi / Catatan Transfer --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                        Instruksi / Catatan Pembayaran (Opsional)
                    </label>
                    <textarea name="instructions" rows="2"
                              placeholder="Contoh: Transfer tepat sesuai nominal tarif kelas. Simpan bukti transfer dan konfirmasi ke WhatsApp Admin."
                              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20 leading-relaxed">{{ old('instructions', 'Transfer tepat sesuai nominal tarif kelas. Simpan bukti transfer dan konfirmasi ke WhatsApp Admin.') }}</textarea>
                    @error('instructions')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Urutan & Status Aktif --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-neutral-800">
                    <div class="space-y-1.5">
                        <x-admin.input label="Urutan Tampilan" type="number" name="sort_order"
                                       :value="old('sort_order', 1)" min="1" required />
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                            Status Rekening
                        </label>
                        <select name="is_active" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif (Ditampilkan ke Siswa)</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif (Disembunyikan)</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-end gap-3">
                    <x-admin.button href="{{ route('admin.payment.index', ['tab' => 'banks']) }}" size="sm" color="outline-secondary">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" size="sm" color="primary">
                        Simpan Rekening Bank
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
