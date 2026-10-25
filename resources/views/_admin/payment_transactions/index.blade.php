@extends('_admin._layout.app')

@section('title', 'Transaksi Masuk & Verifikasi')

@section('content')
    <x-admin.page-header :title="'Transaksi Masuk & Verifikasi'" subtitle="Pantau seluruh transaksi pembayaran siswa (SingaPay Gateway & Transfer Manual) dan verifikasi bukti transfer manual.">
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

    <div class="space-y-4">
        {{-- Filter & Pencarian Bar --}}
        <div class="p-4 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <form action="{{ route('admin.payment_transactions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Search Keywords --}}
                <div class="relative">
                    <input type="text" name="keywords" value="{{ $keywords ?? '' }}"
                           placeholder="Cari Order ID, nama siswa, email..."
                           class="w-full px-3.5 py-2 pl-9 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                    <svg class="size-4 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Filter Status --}}
                <div>
                    <select name="status" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                        <option value="">Semua Status</option>
                        <option value="verification" {{ in_array($status ?? '', ['verification', 'waiting_approval']) ? 'selected' : '' }}>Menunggu Verifikasi (Manual)</option>
                        <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran (Pending)</option>
                        <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>Berhasil (Paid)</option>
                        <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        <option value="expired" {{ ($status ?? '') === 'expired' ? 'selected' : '' }}>Kadaluarsa (Expired)</option>
                    </select>
                </div>

                {{-- Filter Category --}}
                <div>
                    <select name="category" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                        <option value="">Semua Kategori</option>
                        <option value="manual_transfer" {{ ($category ?? '') === 'manual_transfer' ? 'selected' : '' }}>Transfer Manual</option>
                        <option value="va" {{ ($category ?? '') === 'va' ? 'selected' : '' }}>Virtual Account (SingaPay)</option>
                        <option value="qris" {{ ($category ?? '') === 'qris' ? 'selected' : '' }}>QRIS (SingaPay)</option>
                        <option value="ewallet" {{ ($category ?? '') === 'ewallet' ? 'selected' : '' }}>E-Wallet (SingaPay)</option>
                    </select>
                </div>

                {{-- Tombol Aksi Filter --}}
                <div class="flex items-center gap-2">
                    <x-admin.button type="submit" size="sm" color="primary" class="w-full">
                        Filter
                    </x-admin.button>
                    @if (!empty($keywords) || !empty($status) || !empty($category))
                        <a href="{{ route('admin.payment_transactions.index') }}"
                           class="px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 hover:bg-slate-100 shrink-0 font-medium transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Transaksi --}}
        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-neutral-800">
                <thead class="bg-surface dark:bg-neutral-800/60 text-start">
                    <tr>
                        <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Order ID & Tanggal</th>
                        <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Siswa</th>
                        <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Paket Kelas</th>
                        <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Metode</th>
                        <th class="px-4 py-3 text-start text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Total Nominal</th>
                        <th class="px-4 py-3 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Bukti Transfer</th>
                        <th class="px-4 py-3 text-center text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-end text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-neutral-800 text-xs">
                    @forelse ($data as $t)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-neutral-800/40 transition-colors">
                            {{-- Order ID & Waktu --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.payment_transactions.detail', $t->id) }}" class="font-mono font-bold text-primary dark:text-blue-400 hover:underline block">
                                    {{ $t->order_id }}
                                </a>
                                <span class="text-[10px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($t->created_at)->format('d M Y, H:i') }}
                                </span>
                            </td>

                            {{-- Siswa --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $t->student_name ?? $t->user_name ?? 'Siswa' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $t->student_email ?? $t->user_email ?? '-' }}</p>
                            </td>

                            {{-- Paket Kelas --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md font-black text-[11px] bg-slate-100 dark:bg-neutral-800 text-slate-800 dark:text-neutral-200">
                                    {{ $t->cefr ?? $t->cefr_level ?? 'A1' }}
                                </span>
                                <span class="text-[11px] font-medium text-slate-600 dark:text-neutral-300 ml-1">
                                    {{ $t->class_name ?? strtoupper($t->tier ?? $t->tier_name ?? 'BASIC') }}
                                </span>
                            </td>

                            {{-- Metode / Channel --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-bold text-slate-800 dark:text-neutral-200">
                                    {{ $t->payment_channel_name ?? $t->payment_channel }}
                                </div>
                                <span class="text-[10px] uppercase font-mono tracking-wider px-1.5 py-0.5 rounded bg-slate-100 dark:bg-neutral-800 text-slate-500">
                                    {{ str_replace('_', ' ', $t->payment_category ?? '') }}
                                </span>
                            </td>

                            {{-- Nominal --}}
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format($t->amount ?? 0, 0, ',', '.') }}
                            </td>

                            {{-- Bukti Transfer --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @php
                                    $proof = $t->transfer_proof_url ?? $t->proof_url ?? null;
                                @endphp
                                @if ($proof)
                                    <a href="{{ $proof }}" target="_blank" rel="noopener noreferrer" class="inline-block relative group" title="Lihat Bukti Transfer">
                                        <img src="{{ $proof }}" alt="Bukti" class="size-9 rounded-lg object-cover border border-slate-200 dark:border-neutral-700 shadow-2xs group-hover:scale-110 transition-transform">
                                    </a>
                                @elseif (($t->payment_category ?? '') === 'manual_transfer')
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Belum upload</span>
                                @else
                                    <span class="text-[10px] text-slate-400 font-mono">Gateway Otomatis</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if ($t->status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        Berhasil
                                    </span>
                                @elseif ($t->status === 'verification' || $t->status === 'waiting_approval')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 animate-pulse">
                                        <span class="size-1.5 rounded-full bg-blue-500"></span>
                                        Perlu ACC
                                    </span>
                                @elseif ($t->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @elseif ($t->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        <span class="size-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-400">
                                        <span class="size-1.5 rounded-full bg-slate-400"></span>
                                        {{ $t->status }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap text-end">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.payment_transactions.detail', $t->id) }}"
                                       class="px-2.5 py-1 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 font-bold text-[11px] transition-colors"
                                       title="Lihat Rincian">
                                        Detail
                                    </a>

                                    @if ($t->status === 'verification' || $t->status === 'waiting_approval' || $t->status === 'pending')
                                        <form action="{{ route('admin.payment_transactions.approve', $t->id) }}" method="POST"
                                              onsubmit="return confirm('ACC pembayaran transaksi {{ $t->order_id }}? Akses kelas siswa akan langsung otomatis diaktifkan.');"
                                              class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-2xs transition-colors cursor-pointer"
                                                    title="ACC Pembayaran (Buka Akses Kelas)">
                                                ACC
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.payment_transactions.reject', $t->id) }}" method="POST"
                                              onsubmit="return confirm('Tolak pembayaran transaksi {{ $t->order_id }}?');"
                                              class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                    title="Tolak Pembayaran">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto text-center space-y-2">
                                    <svg class="size-12 text-slate-300 dark:text-neutral-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <p class="font-bold text-slate-700 dark:text-neutral-300">Belum ada transaksi masuk</p>
                                    <p class="text-xs text-slate-400">Transaksi pembayaran dari siswa akan tampil di tabel ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if (!empty($data) && method_exists($data, 'hasPages') && $data->hasPages())
            <div class="pt-2">
                {{ $data->links() }}
            </div>
        @endif
    </div>
@endsection
