@extends('_admin._layout.app')

@section('title', 'Kelola Penarikan Dana')

@section('content')
    <x-admin.page-header :title="'Kelola Penarikan Dana'" subtitle="Daftar pengajuan penarikan dana (withdraw) pengguna ke rekening bank via SingaPay.">
        <a href="{{ route('admin.wallet.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Dompet Saya
        </a>
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
        {{-- Filter Bar --}}
        <div class="p-4 rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs">
            <form action="{{ route('admin.wallet.withdrawals') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <input type="text" name="keywords" value="{{ $keywords ?? '' }}"
                           placeholder="Cari Ref No, nama pengguna, atau nomor rekening..."
                           class="w-full px-3.5 py-2 pl-9 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                    <svg class="size-4 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200 focus:outline-hidden focus:ring-2 focus:ring-primary/20">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ ($currentStatus ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan (Pending)</option>
                        <option value="processing" {{ ($currentStatus ?? '') === 'processing' ? 'selected' : '' }}>Sedang Ditransfer (Processing)</option>
                        <option value="success" {{ ($currentStatus ?? '') === 'success' ? 'selected' : '' }}>Berhasil (Success)</option>
                        <option value="rejected" {{ ($currentStatus ?? '') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Tabel Antrean Penarikan --}}
        <div class="rounded-2xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-neutral-300">
                    <thead class="text-3xs uppercase bg-slate-50 dark:bg-neutral-800/60 text-slate-400 border-b border-slate-100 dark:border-neutral-800">
                        <tr>
                            <th class="p-3.5">Ref No & Waktu</th>
                            <th class="p-3.5">Pengguna</th>
                            <th class="p-3.5">Rekening Bank Tujuan</th>
                            <th class="p-3.5 text-right">Nominal Bersih</th>
                            <th class="p-3.5 text-right">Biaya Admin</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                        @forelse($withdrawals as $w)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/30 transition">
                                <td class="p-3.5">
                                    <div class="font-mono font-bold text-slate-800 dark:text-neutral-200">{{ $w->reference_no }}</div>
                                    <div class="text-3xs text-slate-400">{{ \Carbon\Carbon::parse($w->created_at)->format('d M Y H:i') }}</div>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-medium text-slate-900 dark:text-neutral-100">{{ $w->user_name }}</div>
                                    <div class="text-3xs text-slate-400">{{ $w->user_email }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-block px-1.5 py-0.5 text-3xs font-bold rounded bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 uppercase mb-0.5">
                                        {{ $w->bank_name }}
                                    </span>
                                    <div class="font-mono text-xs font-semibold text-slate-800 dark:text-neutral-200">{{ $w->account_number }}</div>
                                    <div class="text-3xs text-slate-500">a.n. {{ $w->account_name }}</div>
                                </td>
                                <td class="p-3.5 text-right font-bold text-slate-900 dark:text-neutral-100">
                                    Rp {{ number_format((float)$w->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-right text-slate-500">
                                    Rp {{ number_format((float)$w->admin_fee, 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($w->status === 'pending')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-3xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300">
                                            Menunggu Approval
                                        </span>
                                    @elseif($w->status === 'processing')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-3xs font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300">
                                            Transfer SingaPay
                                        </span>
                                    @elseif($w->status === 'success')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-3xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                                            Selesai (Berhasil)
                                        </span>
                                    @elseif($w->status === 'rejected')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-3xs font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($w->status === 'pending')
                                        <div class="flex items-center justify-center gap-1.5">
                                            <form action="{{ route('admin.wallet.approve_withdrawal', $w->id) }}" method="POST" onsubmit="return confirm('Setujui dan proses transfer dana via SingaPay?')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-3xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                                    Approve
                                                </button>
                                            </form>

                                            <button type="button" onclick="openRejectModal('{{ $w->id }}', '{{ $w->reference_no }}')"
                                                    class="px-2.5 py-1 rounded-lg text-3xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition">
                                                Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-3xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    Tidak ada data pengajuan penarikan dana.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($withdrawals, 'hasPages') && $withdrawals->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-neutral-800">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Tolak Penarikan --}}
    <div id="modal-reject" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-700 w-full max-w-md rounded-2xl p-6 shadow-2xl">
            <h3 class="font-bold text-base text-slate-900 dark:text-neutral-100 mb-2">Tolak Penarikan Dana</h3>
            <p class="text-xs text-slate-500 mb-4">Saldo pengguna akan otomatis dikembalikan ke dompet setelah penarikan ditolak.</p>

            <form id="form-reject" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">Alasan Penolakan</label>
                    <textarea name="reason" required rows="3" placeholder="Contoh: Nomor rekening tidak cocok dengan nama pemilik..."
                              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-surface dark:bg-neutral-800 text-slate-800 dark:text-neutral-200"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-reject').classList.add('hidden')"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 text-white hover:bg-rose-700 transition">Tolak & Kembalikan Saldo</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id, ref) {
            const form = document.getElementById('form-reject');
            form.action = `/admin/wallet/withdrawals/${id}/reject`;
            document.getElementById('modal-reject').classList.remove('hidden');
        }
    </script>
@endsection
