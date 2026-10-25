@extends('_admin._layout.app')

@section('title', 'Pengaturan Pembayaran')

@section('content')
    <x-admin.page-header :title="'Metode Pembayaran'" subtitle="Kelola rekening transfer manual dan kontak WhatsApp Admin untuk konfirmasi pembayaran siswa.">
        @if ($activeTab === 'banks')
            <x-admin.button href="{{ route('admin.payment.add') }}" size="sm" color="primary">
                @include('_admin._layout.icons.add')
                Tambah Rekening Bank
            </x-admin.button>
        @endif
    </x-admin.page-header>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 border-b-2 border-b-emerald-300 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 border-b-2 border-b-rose-300 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="mb-6 flex items-center gap-2 border-b border-slate-200 dark:border-neutral-700">
        <a href="{{ route('admin.payment.index', ['tab' => 'banks']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $activeTab === 'banks' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Transfer Bank Manual
            <x-admin.badge variant="{{ $activeTab === 'banks' ? 'primary' : 'neutral' }}" size="sm">
                {{ count($methods) }}
            </x-admin.badge>
        </a>
        <a href="{{ route('admin.payment.index', ['tab' => 'whatsapp']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $activeTab === 'whatsapp' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm0 17.58c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.03 8.03 0 01-1.24-4.22c0-4.48 3.64-8.12 8.12-8.12 2.17 0 4.21.85 5.74 2.38 1.53 1.54 2.38 3.58 2.38 5.75 0 4.49-3.64 8.12-8.12 8.12zm4.45-6.09c-.24-.12-1.45-.71-1.67-.79-.22-.09-.39-.12-.55.12-.17.24-.63.79-.78.95-.14.17-.29.19-.53.07-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.24-.02-.37.1-.49.11-.11.25-.29.37-.43.12-.14.17-.24.25-.4.08-.17.04-.31-.02-.43-.06-.12-.55-1.33-.76-1.82-.2-.48-.41-.41-.56-.42h-.48c-.16 0-.43.06-.66.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.13.17 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.52.59.19 1.13.16 1.56.1.48-.07 1.45-.59 1.66-1.17.2-.57.2-1.07.14-1.17-.05-.12-.21-.19-.46-.31z"/>
            </svg>
            Hubungi Admin (WhatsApp)
            @if(($settings->whatsapp_is_active ?? '1') == '1')
                <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
            @endif
        </a>
        <a href="{{ route('admin.payment.index', ['tab' => 'gateway']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $activeTab === 'gateway' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            SingaPay & Batas Expired
            @if(($settings->singapay_is_sandbox ?? '1') == '1')
                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Sandbox</span>
            @endif
        </a>
    </div>

    @if ($activeTab === 'banks')
        {{-- TAB 1: DAFTAR REKENING BANK MANUAL --}}
        <div class="space-y-4">
            {{-- Filter & Pencarian --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form action="{{ route('admin.payment.index') }}" method="GET" class="w-full sm:w-80">
                    <input type="hidden" name="tab" value="banks">
                    <div class="relative">
                        <input type="text" name="keywords" value="{{ $keywords ?? '' }}"
                               placeholder="Cari bank, nomor rekening, atas nama..."
                               class="w-full px-3.5 py-2 pl-9 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                        <svg class="size-4 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                <div class="text-xs text-slate-500 font-medium">
                    Total: <span class="font-bold text-slate-800 dark:text-neutral-200">{{ count($methods) }} Rekening</span>
                </div>
            </div>

            {{-- Tabel Rekening Bank --}}
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-neutral-800">
                    <thead class="bg-surface dark:bg-neutral-800/60 text-start">
                        <tr>
                            <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Bank</th>
                            <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Nomor Rekening</th>
                            <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Atas Nama</th>
                            <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Instruksi</th>
                            <th class="px-4 py-3 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-3 text-end text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800 text-xs">
                        @forelse ($methods as $m)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-neutral-800/40 transition-colors">
                                {{-- Kolom Bank & Logo --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center p-1.5 shrink-0 overflow-hidden">
                                            @if ($m->bank_logo)
                                                <img src="{{ $m->bank_logo }}" alt="{{ $m->bank_name }}" class="w-full h-full object-contain">
                                            @else
                                                <svg class="size-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white">{{ $m->bank_name }}</p>
                                            <span class="text-[10px] text-slate-400 font-mono">Urutan #{{ $m->sort_order }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom No Rek --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono font-bold text-sm text-primary dark:text-blue-400 tracking-wider bg-primary/5 dark:bg-blue-400/10 px-2 py-1 rounded-md border border-primary/20">
                                        {{ $m->account_number }}
                                    </span>
                                </td>

                                {{-- Kolom Atas Nama --}}
                                <td class="px-4 py-3 whitespace-nowrap font-bold text-slate-800 dark:text-neutral-200 uppercase">
                                    {{ $m->account_name }}
                                </td>

                                {{-- Kolom Instruksi --}}
                                <td class="px-4 py-3 max-w-xs text-slate-500 dark:text-neutral-400 truncate">
                                    {{ $m->instructions ?: 'Transfer sesuai nominal' }}
                                </td>

                                {{-- Kolom Status Aktif --}}
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <form action="{{ route('admin.payment.toggleStatus', $m->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="cursor-pointer group" title="Klik untuk ubah status">
                                            @if ($m->is_active)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 group-hover:bg-emerald-200 transition-colors">
                                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-400 group-hover:bg-slate-200 transition-colors">
                                                    <span class="size-1.5 rounded-full bg-slate-400"></span>
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="px-4 py-3 whitespace-nowrap text-end">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.payment.edit', $m->id) }}"
                                           class="p-1.5 rounded-lg text-slate-600 hover:text-primary hover:bg-slate-100 dark:hover:bg-neutral-800 dark:text-neutral-400 transition-colors"
                                           title="Edit Rekening">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('admin.payment.delete', $m->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening {{ $m->bank_name }} ini?');"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                    title="Hapus Rekening">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                    <div class="max-w-sm mx-auto text-center space-y-2">
                                        <svg class="size-10 text-slate-300 dark:text-neutral-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <p class="font-bold text-slate-700 dark:text-neutral-300">Belum ada rekening transfer bank</p>
                                        <p class="text-xs text-slate-400">Tambahkan rekening bank untuk pembayaran manual siswa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif ($activeTab === 'whatsapp')
        {{-- TAB 2: PENGATURAN HUBUNGI ADMIN (WHATSAPP) --}}
        <div class="max-w-2xl mx-auto space-y-6">
            <x-admin.card variant="white" padding="p-6 sm:p-7">
                <form action="{{ route('admin.payment.updateSettings') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="setting_type" value="whatsapp">

                    <div class="border-b border-slate-100 dark:border-neutral-800 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm0 17.58c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.03 8.03 0 01-1.24-4.22c0-4.48 3.64-8.12 8.12-8.12 2.17 0 4.21.85 5.74 2.38 1.53 1.54 2.38 3.58 2.38 5.75 0 4.49-3.64 8.12-8.12 8.12z"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Kontak WhatsApp Admin</h3>
                                <p class="text-xs text-slate-500">Nomor ini digunakan siswa untuk konfirmasi transfer manual dan konsultasi pembayaran.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Status Aktif Channel WhatsApp --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-surface dark:bg-neutral-800/50 border border-slate-200 dark:border-neutral-700">
                        <div>
                            <span class="block text-xs font-bold text-slate-800 dark:text-white">Aktifkan Tombol "Hubungi Admin"</span>
                            <span class="text-[11px] text-slate-400">Jika dinonaktifkan, siswa tidak akan melihat tombol WhatsApp di modal pembayaran.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="whatsapp_is_active" value="1"
                                   {{ old('whatsapp_is_active', $settings->whatsapp_is_active ?? '1') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                            Nomor WhatsApp Admin <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="whatsapp_number"
                                   value="{{ old('whatsapp_number', $settings->whatsapp_number ?? '6281234567890') }}"
                                   placeholder="Contoh: 6281234567890 atau 081234567890" required
                                   class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                        </div>
                        <span class="text-[11px] text-slate-400">Gunakan format internasional dengan kode negara (contoh: 6281234567890).</span>
                        @error('whatsapp_number')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Display Kontak --}}
                    <div class="space-y-1.5">
                        <x-admin.input label="Nama Display Admin" name="whatsapp_name"
                                       :value="old('whatsapp_name', $settings->whatsapp_name ?? 'Admin Support EDDY\'S AI')"
                                       placeholder="Contoh: Admin Support EDDY'S AI" required />
                        @error('whatsapp_name')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Template Pesan Default --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                            Template Pesan Otomatis WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="whatsapp_message" rows="3" required
                                  placeholder="Tulis pesan awal yang otomatis muncul di chat WhatsApp siswa..."
                                  class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20 leading-relaxed">{{ old('whatsapp_message', $settings->whatsapp_message ?? 'Halo Admin EDDY\'S AI, saya ingin konfirmasi pembayaran transfer manual untuk aktivasi kelas.') }}</textarea>
                        <span class="text-[11px] text-slate-400">Pesan ini otomatis terisi di kolom ketik WhatsApp siswa saat tombol diklik.</span>
                        @error('whatsapp_message')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Simulasi Link Test --}}
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $settings->whatsapp_number ?? '6281234567890');
                        if (str_starts_with($cleanPhone, '08')) {
                            $cleanPhone = '628' . substr($cleanPhone, 2);
                        }
                        $testUrl = 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($settings->whatsapp_message ?? 'Halo Admin');
                    @endphp
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-between gap-3">
                        <div class="space-y-0.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">Live Preview Link</span>
                            <p class="text-xs font-mono text-emerald-700 dark:text-emerald-400 truncate max-w-sm">{{ $testUrl }}</p>
                        </div>
                        <a href="{{ $testUrl }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shrink-0 transition-colors shadow-2xs">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Tes Buka WA
                        </a>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-neutral-800 flex justify-end">
                        <x-admin.button type="submit" size="sm" color="primary">
                            Simpan Pengaturan WhatsApp
                        </x-admin.button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    @elseif ($activeTab === 'gateway')
        {{-- TAB 3: PENGATURAN GATEWAY SINGAPAY & EXPIRED TIMER --}}
        <div class="max-w-2xl mx-auto space-y-6">
            <x-admin.card variant="white" padding="p-6 sm:p-7">
                <form action="{{ route('admin.payment.updateSettings') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="setting_type" value="gateway">

                    <div class="border-b border-slate-100 dark:border-neutral-800 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-xl bg-blue-50 text-blue-600 border border-blue-200">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Pengaturan SingaPay Payment Gateway</h3>
                                <p class="text-xs text-slate-500">Konfigurasi lingkungan dan durasi masa berlaku link pembayaran siswa.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Security & Env Info Card --}}
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200 dark:border-neutral-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-bold text-slate-800 dark:text-neutral-200">Kredensial API Dikelola via Environment (.env)</span>
                            </div>
                            <span class="text-[10px] uppercase font-mono font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Aman
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Demi keamanan, seluruh API credentials SingaPay (Client ID, Client Secret, API Key, HMAC Validation Key) dimuat langsung dari environment variable server (<code class="font-mono text-primary dark:text-blue-400 font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-neutral-700">.env</code>) dan tidak disimpan di database.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono pt-1">
                            <div class="p-2.5 rounded-lg bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700">
                                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold">Client ID SingaPay</span>
                                <span class="font-bold text-slate-700 dark:text-neutral-300">
                                    {{ env('SINGAPAY_CLIENT_ID') ? '••••••••' . substr(env('SINGAPAY_CLIENT_ID'), -6) : 'Terkonfigurasi di .env' }}
                                </span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700">
                                <span class="text-slate-400 block text-[10px] uppercase font-sans font-bold">Target Environment</span>
                                <span class="font-bold text-slate-700 dark:text-neutral-300 uppercase">
                                    {{ env('SINGAPAY_ENVIRONMENT', 'sandbox') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Mode Sandbox --}}
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-800/40">
                        <div>
                            <span class="block text-xs font-bold text-amber-900 dark:text-amber-200">Gunakan SingaPay Sandbox</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-400">Aktifkan untuk lingkungan pengujian (https://sandbox-openapi.singapay.id).</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="singapay_is_sandbox" value="1"
                                   {{ old('singapay_is_sandbox', $settings->singapay_is_sandbox ?? '1') == '1' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    {{-- Batas Waktu Kadaluarsa (Jam) --}}
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-neutral-200">
                            Durasi Kadaluarsa Link Pembayaran (Jam) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="payment_expired_hours" min="1" max="168"
                                   value="{{ old('payment_expired_hours', $settings->payment_expired_hours ?? '24') }}"
                                   placeholder="24" required
                                   class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                        </div>
                        <span class="text-[11px] text-slate-400">Waktu hitung mundur (countdown) pada halaman konfirmasi sebelum link expired (default: 24 jam).</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-neutral-800 flex justify-end">
                        <x-admin.button type="submit" size="sm" color="primary">
                            Simpan Pengaturan Gateway
                        </x-admin.button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    @endif
@endsection
