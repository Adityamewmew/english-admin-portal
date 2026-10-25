@extends('_admin._layout.app')

@section('title', 'Menu Saldo & Penarikan')

@section('content')
    <x-admin.page-header :title="'Saldo & Penarikan Dana'" subtitle="Kelola saldo penghasilan, rekening bank tujuan, dan pengajuan penarikan dana ke rekening pribadi.">
        <div class="flex items-center gap-2">
            @if(count($bankAccounts) > 0 && $summary->balance >= 12500)
                <button type="button" onclick="document.getElementById('modal-withdraw').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Tarik Saldo
                </button>
            @endif
            <button type="button" onclick="document.getElementById('modal-add-bank').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl bg-primary hover:bg-primary-dark text-white shadow-sm transition">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Rekening
            </button>
        </div>
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

    {{-- 4 Kartu Ringkasan Saldo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-5 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-neutral-400 text-xs font-medium mb-2">
                <span>Saldo Tersedia</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-neutral-100">
                Rp {{ number_format($summary->balance, 0, ',', '.') }}
            </div>
            <p class="text-3xs text-slate-400 mt-1">Dapat ditarik ke rekening pribadi kapan saja</p>
        </div>

        <div class="p-5 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-neutral-400 text-xs font-medium mb-2">
                <span>Total Saldo Masuk</span>
                <span class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-neutral-100">
                Rp {{ number_format($summary->total_income, 0, ',', '.') }}
            </div>
            <p class="text-3xs text-slate-400 mt-1">Akumulasi seluruh pembayaran siswa yang masuk</p>
        </div>

        <div class="p-5 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-neutral-400 text-xs font-medium mb-2">
                <span>Total Telah Ditarik</span>
                <span class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-900 dark:text-neutral-100">
                Rp {{ number_format($summary->total_withdraw, 0, ',', '.') }}
            </div>
            <p class="text-3xs text-slate-400 mt-1">Penarikan yang telah berhasil masuk ke rekening</p>
        </div>

        <div class="p-5 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-neutral-400 text-xs font-medium mb-2">
                <span>Sedang Diproses</span>
                <span class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                Rp {{ number_format($summary->pending_withdraw, 0, ',', '.') }}
            </div>
            <p class="text-3xs text-slate-400 mt-1">Dalam antrean verifikasi & transfer SingaPay</p>
        </div>
    </div>

    {{-- Grid: Rekening Bank & Mutasi Transaksi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Rekening Bank Tersimpan --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                    <h3 class="font-bold text-slate-900 dark:text-neutral-100 text-sm">Rekening Bank Tujuan</h3>
                    <button type="button" onclick="document.getElementById('modal-add-bank').classList.remove('hidden')"
                            class="text-xs text-primary font-semibold hover:underline">+ Tambah</button>
                </div>

                @forelse($bankAccounts as $bank)
                    <div class="p-3.5 mb-3 rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-800/40 relative group">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="inline-block px-2 py-0.5 text-3xs font-bold rounded-md bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 uppercase mb-1">
                                    {{ $bank->bank_name }}
                                </span>
                                <div class="text-sm font-bold text-slate-800 dark:text-neutral-200 tracking-wider">
                                    {{ $bank->account_number }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-neutral-400 font-medium">
                                    a.n. {{ $bank->account_name }}
                                </div>
                            </div>
                            <form action="{{ route('admin.wallet.delete_bank', $bank->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus Rekening">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 dark:text-neutral-500">
                        <svg class="size-10 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <p class="text-xs">Belum ada rekening bank yang tersimpan.</p>
                        <button type="button" onclick="document.getElementById('modal-add-bank').classList.remove('hidden')"
                                class="mt-2 text-xs font-semibold text-primary hover:underline">Tambah Sekarang</button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom Kanan: Riwayat Transaksi Saldo --}}
        <div class="lg:col-span-2">
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                    <h3 class="font-bold text-slate-900 dark:text-neutral-100 text-sm">Riwayat Mutasi Saldo</h3>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.wallet.index') }}" class="px-2.5 py-1 text-3xs font-semibold rounded-lg {{ empty($currentType) ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400' }}">Semua</a>
                        <a href="{{ route('admin.wallet.index', ['type' => 'deposit']) }}" class="px-2.5 py-1 text-3xs font-semibold rounded-lg {{ $currentType === 'deposit' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400' }}">Masuk</a>
                        <a href="{{ route('admin.wallet.index', ['type' => 'withdraw']) }}" class="px-2.5 py-1 text-3xs font-semibold rounded-lg {{ $currentType === 'withdraw' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400' }}">Keluar</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-neutral-300">
                        <thead class="text-3xs uppercase bg-slate-50 dark:bg-neutral-800/60 text-slate-400">
                            <tr>
                                <th class="p-3 rounded-l-lg">Waktu</th>
                                <th class="p-3">Tipe</th>
                                <th class="p-3">Keterangan</th>
                                <th class="p-3 text-right rounded-r-lg">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                            @forelse($transactions as $t)
                                @php
                                    $meta = is_string($t->meta) ? json_decode($t->meta, true) : (array) $t->meta;
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/30 transition">
                                    <td class="p-3 font-mono text-3xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') }}
                                    </td>
                                    <td class="p-3">
                                        @if($t->type === 'deposit')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-3xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                                Masuk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-3xs font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">
                                                <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                                Tarik Dana
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div class="font-medium text-slate-800 dark:text-neutral-200">
                                            {{ $meta['notes'] ?? ($t->type === 'deposit' ? 'Pembayaran Siswa Masuk' : 'Penarikan Saldo ke Rekening') }}
                                        </div>
                                        @if(!empty($meta['reference_no']))
                                            <div class="text-3xs font-mono text-slate-400">Ref: {{ $meta['reference_no'] }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right font-bold {{ $t->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $t->type === 'deposit' ? '+' : '-' }} Rp {{ number_format((float)$t->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400">
                                        Belum ada riwayat transaksi saldo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-neutral-800">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Tambah Rekening Bank --}}
    <div id="modal-add-bank" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-700 w-full max-w-md rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-neutral-100">Tambah Rekening Bank</h3>
                <button type="button" onclick="document.getElementById('modal-add-bank').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.wallet.add_bank') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">Pilih Bank</label>
                    <select name="bank_code" id="bank-select" required onchange="document.getElementById('bank_name').value = this.options[this.selectedIndex].text"
                            class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200">
                        <option value="">-- Pilih Bank Tujuan --</option>
                        <option value="014">Bank Central Asia (BCA)</option>
                        <option value="002">Bank Rakyat Indonesia (BRI)</option>
                        <option value="008">Bank Mandiri</option>
                        <option value="009">Bank Negara Indonesia (BNI)</option>
                        <option value="451">Bank Syariah Indonesia (BSI)</option>
                        <option value="013">Bank Permata</option>
                        <option value="022">CIMB Niaga</option>
                        <option value="011">Bank Danamon</option>
                    </select>
                    <input type="hidden" name="bank_name" id="bank_name">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">Nomor Rekening</label>
                    <input type="text" name="account_number" required placeholder="Contoh: 1234567890"
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" name="account_name" required placeholder="Sesuai nama yang tertera di buku tabungan"
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 uppercase">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-neutral-800">
                    <button type="button" onclick="document.getElementById('modal-add-bank').classList.add('hidden')"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-primary text-white hover:bg-primary-dark transition">Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tarik Saldo --}}
    @if(count($bankAccounts) > 0)
    <div id="modal-withdraw" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-700 w-full max-w-md rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-neutral-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-neutral-100">Tarik Saldo ke Rekening</h3>
                <button type="button" onclick="document.getElementById('modal-withdraw').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form action="{{ route('admin.wallet.request_withdraw') }}" method="POST" class="space-y-4" oninput="updateWithdrawCalc()">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">Rekening Tujuan</label>
                    <select name="bank_account_id" required
                            class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200">
                        @foreach($bankAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_number }} (a.n. {{ $acc->account_name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-neutral-300">Nominal Penarikan (Rp)</label>
                        <span class="text-3xs text-slate-400">Tersedia: Rp {{ number_format($summary->balance, 0, ',', '.') }}</span>
                    </div>
                    <input type="number" name="amount" id="wd-amount" min="10000" max="{{ $summary->balance > 2500 ? $summary->balance - 2500 : 0 }}" required placeholder="Min. 10.000"
                           class="w-full px-3.5 py-2 text-sm font-bold rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200">
                </div>

                {{-- Kalkulasi Fee --}}
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200 dark:border-neutral-700 text-xs space-y-1.5">
                    <div class="flex justify-between text-slate-500 dark:text-neutral-400">
                        <span>Nominal Diterima Rekening:</span>
                        <span class="font-semibold text-slate-800 dark:text-neutral-200" id="calc-net">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-neutral-400">
                        <span>Biaya Admin (Transfer):</span>
                        <span class="font-semibold text-slate-800 dark:text-neutral-200">Rp 2.500</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 dark:text-neutral-100 pt-1 border-t border-slate-200 dark:border-neutral-700">
                        <span>Total Saldo Terpotong:</span>
                        <span class="text-rose-600 dark:text-rose-400" id="calc-total">Rp 2.500</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">Catatan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Contoh: Honor mengajar bulan Oktober"
                           class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-neutral-800">
                    <button type="button" onclick="document.getElementById('modal-withdraw').classList.add('hidden')"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateWithdrawCalc() {
            const input = document.getElementById('wd-amount');
            const net = parseFloat(input.value) || 0;
            const fee = 2500;
            const total = net > 0 ? net + fee : fee;
            document.getElementById('calc-net').innerText = 'Rp ' + net.toLocaleString('id-ID');
            document.getElementById('calc-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>
    @endif
@endsection
