@extends('_admin._layout.app')

@section('title', 'Detail Transaksi #' . $data->order_id)

@section('content')
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payment_transactions.index') }}"
               class="p-2 rounded-xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 hover:bg-slate-50 transition-colors shadow-2xs">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="font-heading font-black text-xl text-slate-900 dark:text-white flex items-center gap-2">
                    Transaksi #{{ $data->order_id }}
                </h1>
                <p class="text-xs text-slate-500">Dibuat pada {{ \Carbon\Carbon::parse($data->created_at)->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        {{-- Status Badge & Action Buttons --}}
        <div class="flex items-center gap-2">
            @if ($data->status === 'paid')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Pembayaran Berhasil
                </span>
            @elseif ($data->status === 'verification')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 animate-pulse">
                    <span class="size-2 rounded-full bg-blue-500"></span>
                    Menunggu Verifikasi Admin
                </span>
            @elseif ($data->status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    <span class="size-2 rounded-full bg-amber-500"></span>
                    Menunggu Pembayaran
                </span>
            @elseif ($data->status === 'rejected')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                    <span class="size-2 rounded-full bg-rose-500"></span>
                    Pembayaran Ditolak
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-400">
                    <span class="size-2 rounded-full bg-slate-400"></span>
                    {{ $data->status }}
                </span>
            @endif
        </div>
    </div>

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

    {{-- Action Bar ACC / Reject if pending / verification --}}
    @if ($data->status === 'verification' || $data->status === 'pending')
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 border border-blue-200 dark:border-blue-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">Verifikasi Pembayaran Siswa</h4>
                    <p class="text-xs text-slate-500">Jika bukti transfer valid, klik <strong>ACC Pembayaran</strong> untuk mengaktifkan akses kelas siswa secara instan.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                {{-- Form ACC --}}
                <form action="{{ route('admin.payment_transactions.approve', $data->id) }}" method="POST"
                      onsubmit="return confirm('ACC pembayaran transaksi ini? Akses kelas siswa akan langsung aktif.');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        ACC Pembayaran (Buka Akses)
                    </button>
                </form>

                {{-- Form Reject --}}
                <form action="{{ route('admin.payment_transactions.reject', $data->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menolak transaksi ini?');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900 font-bold text-xs transition-colors cursor-pointer">
                        Tolak
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- KOLOM KIRI: BUKTI TRANSFER --}}
        <div class="lg:col-span-5 space-y-6">
            <x-admin.card variant="white" padding="p-5 sm:p-6">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                    <h3 class="font-heading font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="size-4 text-primary dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Bukti Transfer Manual
                    </h3>
                    @php
                        $proof = $data->transfer_proof_url ?? $data->proof_url ?? null;
                    @endphp
                    @if ($proof)
                        <a href="{{ $proof }}" target="_blank" rel="noopener noreferrer"
                           class="text-xs font-bold text-primary dark:text-blue-400 hover:underline">
                            Buka Gambar Penuh
                        </a>
                    @endif
                </div>

                @if ($proof)
                    <div class="space-y-3">
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-neutral-700 bg-slate-50 dark:bg-neutral-800 flex items-center justify-center p-2">
                            <img src="{{ $proof }}" alt="Bukti Transfer" class="max-h-96 w-auto object-contain rounded-lg shadow-2xs">
                        </div>
                        <div class="p-3 rounded-xl bg-surface dark:bg-neutral-800 text-[11px] text-slate-500 space-y-1">
                            <p><span class="font-bold text-slate-700 dark:text-neutral-300">File:</span> Bukti transfer diunggah oleh siswa.</p>
                            @if ($data->metadata && isset($data->metadata['proof_uploaded_at']))
                                <p><span class="font-bold text-slate-700 dark:text-neutral-300">Waktu Upload:</span> {{ $data->metadata['proof_uploaded_at'] }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-12 text-center space-y-2">
                        <div class="size-12 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-400 mx-auto flex items-center justify-center">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="font-bold text-xs text-slate-700 dark:text-neutral-300">
                            @if ($data->payment_category === 'manual_transfer')
                                Siswa belum mengunggah bukti transfer
                            @else
                                Pembayaran Otomatis SingaPay Gateway
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-400 max-w-xs mx-auto">
                            @if ($data->payment_category === 'manual_transfer')
                                Status akan otomatis berubah menjadi "Menunggu Verifikasi" segera setelah siswa upload foto bukti.
                            @else
                                Sistem SingaPay memvalidasi pembayaran ini secara otomatis lewat callback webhook.
                            @endif
                        </p>
                    </div>
                @endif
            </x-admin.card>
        </div>

        {{-- KOLOM KANAN: RINCIAN ORDER & DATA SISWA --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Rincian Transaksi --}}
            <x-admin.card variant="white" padding="p-5 sm:p-6">
                <h3 class="font-heading font-black text-sm text-slate-900 dark:text-white pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                    Rincian Pembayaran
                </h3>

                <dl class="divide-y divide-slate-100 dark:divide-neutral-800 text-xs">
                    <div class="py-2.5 flex justify-between items-center">
                        <dt class="text-slate-500">Order ID</dt>
                        <dd class="font-mono font-bold text-primary dark:text-blue-400">{{ $data->order_id }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between items-center">
                        <dt class="text-slate-500">Total Tagihan</dt>
                        <dd class="text-base font-black text-slate-900 dark:text-white">
                            Rp {{ number_format($data->amount, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="py-2.5 flex justify-between items-center">
                        <dt class="text-slate-500">Kategori & Metode</dt>
                        <dd class="font-bold text-slate-800 dark:text-neutral-200 text-end">
                            {{ $data->payment_channel_name ?? $data->payment_channel }}
                            <span class="block text-[10px] text-slate-400 uppercase font-mono">{{ str_replace('_', ' ', $data->payment_category) }}</span>
                        </dd>
                    </div>
                    @if ($data->va_number)
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-500">Nomor Virtual Account</dt>
                            <dd class="font-mono font-black text-primary dark:text-blue-400 bg-primary/5 px-2 py-1 rounded">
                                {{ $data->va_number }}
                            </dd>
                        </div>
                    @endif
                    @if ($data->gateway_trx_id)
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-500">ID Transaksi Gateway</dt>
                            <dd class="font-mono text-slate-700 dark:text-neutral-300">{{ $data->gateway_trx_id }}</dd>
                        </div>
                    @endif
                    <div class="py-2.5 flex justify-between items-center">
                        <dt class="text-slate-500">Batas Waktu Expired</dt>
                        <dd class="font-mono text-slate-700 dark:text-neutral-300">
                            {{ $data->expired_at ? \Carbon\Carbon::parse($data->expired_at)->format('d M Y, H:i') . ' WIB' : '-' }}
                        </dd>
                    </div>
                    @if ($data->paid_at)
                        <div class="py-2.5 flex justify-between items-center">
                            <dt class="text-slate-500">Waktu Sukses Bayar</dt>
                            <dd class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ \Carbon\Carbon::parse($data->paid_at)->format('d M Y, H:i') }} WIB
                            </dd>
                        </div>
                    @endif
                </dl>
            </x-admin.card>

            {{-- Siswa & Kelas Terkait --}}
            <x-admin.card variant="white" padding="p-5 sm:p-6">
                <h3 class="font-heading font-black text-sm text-slate-900 dark:text-white pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                    Siswa & Hak Akses Kelas
                </h3>

                <div class="space-y-4 text-xs">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-surface dark:bg-neutral-800">
                        <div class="size-10 rounded-full bg-primary/10 text-primary dark:text-blue-400 font-black flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($data->student_name ?? $data->user_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $data->student_name ?? $data->user_name ?? 'Siswa' }}</p>
                            <p class="text-slate-400 text-xs">{{ $data->student_email ?? $data->user_email ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-neutral-700 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Level CEFR:</span>
                            <span class="px-2 py-0.5 rounded-md bg-primary text-white font-black text-xs">
                                {{ $data->cefr ?? $data->cefr_level ?? 'A1' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Paket Tier:</span>
                            <span class="font-bold text-slate-800 dark:text-white">
                                {{ $data->class_name ?? strtoupper($data->tier ?? $data->tier_name ?? 'BASIC') }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
@endsection
