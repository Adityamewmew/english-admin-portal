@extends('_admin._layout.app')

@section('title', 'Konfirmasi Kelas Siswa')

@section('content')
    <x-admin.page-header :title="'Konfirmasi Kelas Siswa'" subtitle="Verifikasi bukti transfer manual siswa dan setujui (ACC) untuk membuka akses pembelajaran kelas CEFR.">
        <x-admin.button href="{{ route('admin.classes.index') }}" size="sm" color="neutral">
            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Kelola Tarif & Daftar Kelas
        </x-admin.button>
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

    {{-- Tabs Status Filter --}}
    <div class="mb-6 flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-neutral-700">
        <a href="{{ route('admin.classes.confirmations', ['status' => 'verification', 'keywords' => $keywords]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $status === 'verification' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <span class="size-2 rounded-full {{ ($counts['verification'] ?? 0) > 0 ? 'bg-amber-500 animate-pulse' : 'bg-slate-300 dark:bg-neutral-600' }}"></span>
            Perlu Verifikasi / ACC
            <span class="px-2 py-0.5 rounded-full text-xs font-black {{ $status === 'verification' ? 'bg-primary text-white dark:bg-blue-600' : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' }}">
                {{ $counts['verification'] ?? 0 }}
            </span>
        </a>

        <a href="{{ route('admin.classes.confirmations', ['status' => 'pending', 'keywords' => $keywords]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $status === 'pending' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            Menunggu Transfer
            <span class="px-2 py-0.5 rounded-full text-xs font-black {{ $status === 'pending' ? 'bg-primary text-white dark:bg-blue-600' : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' }}">
                {{ $counts['pending'] ?? 0 }}
            </span>
        </a>

        <a href="{{ route('admin.classes.confirmations', ['status' => 'paid', 'keywords' => $keywords]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $status === 'paid' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            Telah Di-ACC / Lunas
            <span class="px-2 py-0.5 rounded-full text-xs font-black {{ $status === 'paid' ? 'bg-primary text-white dark:bg-blue-600' : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' }}">
                {{ $counts['paid'] ?? 0 }}
            </span>
        </a>

        <a href="{{ route('admin.classes.confirmations', ['status' => 'all', 'keywords' => $keywords]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 transition-all {{ $status === 'all' ? 'border-primary text-primary dark:text-white dark:border-blue-400' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            Semua
            <span class="px-2 py-0.5 rounded-full text-xs font-black {{ $status === 'all' ? 'bg-primary text-white dark:bg-blue-600' : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' }}">
                {{ $counts['all'] ?? 0 }}
            </span>
        </a>
    </div>

    {{-- Filter & Search Form --}}
    <div class="mb-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form action="{{ route('admin.classes.confirmations') }}" method="GET" class="flex items-center gap-2 flex-1 max-w-md">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative w-full">
                <input type="text"
                       name="keywords"
                       value="{{ $keywords ?? '' }}"
                       placeholder="Cari nama siswa, email, atau Order ID..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-slate-800 dark:text-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <svg class="size-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-3.5 py-2 text-xs font-bold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors shadow-2xs">
                Cari
            </button>
            @if (!empty($keywords))
                <a href="{{ route('admin.classes.confirmations', ['status' => $status]) }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-neutral-200">
                    Reset
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-500 dark:text-neutral-400 font-medium self-center">
            Total {{ $data->total() ?? count($data) }} transaksi transfer manual
        </div>
    </div>

    {{-- Tabel Konfirmasi --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-neutral-800 bg-slate-50/70 dark:bg-neutral-800/40 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Target Kelas CEFR</th>
                        <th class="px-4 py-3">Order ID & Waktu</th>
                        <th class="px-4 py-3">Nominal Transfer</th>
                        <th class="px-4 py-3 text-center">Bukti Transfer</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-end">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-neutral-800 text-xs">
                    @forelse ($data as $t)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-neutral-800/40 transition-colors">
                            {{-- Siswa --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="size-8 rounded-full bg-primary/10 text-primary dark:bg-blue-900/30 dark:text-blue-300 font-black flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($t->student_name ?? $t->user_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ $t->student_name ?? $t->user_name ?? 'Siswa' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $t->student_email ?? $t->user_email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Target Kelas CEFR --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-black text-[11px] bg-primary/10 text-primary dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $t->cefr ?? $t->cefr_level ?? 'A1' }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300">
                                        {{ strtoupper($t->tier ?? $t->tier_name ?? 'BASIC') }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-neutral-400 mt-0.5">
                                    {{ $t->class_name ?? ('Kelas English ' . ($t->cefr ?? $t->cefr_level ?? '')) }}
                                </div>
                            </td>

                            {{-- Order ID & Waktu --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.payment_transactions.detail', $t->id) }}" class="font-mono font-bold text-primary dark:text-blue-400 hover:underline block">
                                    {{ $t->order_id }}
                                </a>
                                <span class="text-[10px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($t->created_at)->format('d M Y, H:i') }}
                                </span>
                            </td>

                            {{-- Nominal --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="font-extrabold text-slate-900 dark:text-white">
                                    Rp {{ number_format($t->amount ?? 0, 0, ',', '.') }}
                                </p>
                                <span class="text-[10px] font-mono text-slate-500 dark:text-neutral-400">
                                    Bank {{ $t->payment_channel_name ?? $t->payment_channel ?? 'Transfer' }}
                                </span>
                            </td>

                            {{-- Bukti Transfer --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @php
                                    $proof = $t->transfer_proof_url ?? $t->proof_url ?? null;
                                @endphp
                                @if ($proof)
                                    <button type="button"
                                            onclick="openProofModal('{{ $proof }}', '{{ $t->order_id }}', '{{ $t->student_name ?? $t->user_name ?? 'Siswa' }}')"
                                            class="inline-block relative group cursor-pointer"
                                            title="Klik untuk memperbesar bukti transfer">
                                        <img src="{{ $proof }}" alt="Bukti" class="size-10 rounded-lg object-cover border border-slate-200 dark:border-neutral-700 shadow-2xs group-hover:scale-110 transition-transform">
                                        <span class="absolute inset-0 bg-black/20 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[9px] font-bold">
                                            Zoom
                                        </span>
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800">
                                        Belum upload
                                    </span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if ($t->status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        Lunas / Aktif
                                    </span>
                                @elseif ($t->status === 'verification' || $t->status === 'waiting_approval')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 animate-pulse">
                                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                                        Perlu ACC
                                    </span>
                                @elseif ($t->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300">
                                        <span class="size-1.5 rounded-full bg-slate-400"></span>
                                        Pending
                                    </span>
                                @elseif ($t->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        <span class="size-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-400">
                                        {{ $t->status }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi Konfirmasi --}}
                            <td class="px-4 py-3 whitespace-nowrap text-end">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($t->status === 'verification' || $t->status === 'waiting_approval' || $t->status === 'pending')
                                        <form action="{{ route('admin.payment_transactions.approve', $t->id) }}" method="POST"
                                              onsubmit="return confirm('ACC pembayaran dan aktifkan kelas CEFR {{ $t->cefr ?? $t->cefr_level ?? '' }} untuk siswa {{ $t->student_name ?? $t->user_name ?? '' }}?');"
                                              class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-2xs transition-colors cursor-pointer"
                                                    title="ACC Pembayaran (Buka Akses Kelas)">
                                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                ACC Kelas
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.payment_transactions.reject', $t->id) }}" method="POST"
                                              onsubmit="return confirm('Tolak transfer manual untuk transaksi {{ $t->order_id }}?');"
                                              class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                                                    title="Tolak Pembayaran">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.payment_transactions.detail', $t->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 font-bold text-[11px] transition-colors"
                                       title="Lihat Rincian Lengkap">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <x-admin.empty-state message="Belum ada transaksi transfer manual untuk status ini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($data->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-neutral-800">
                {{ $data->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Preview Bukti Transfer --}}
    <div id="proofModal" class="fixed inset-0 z-50 hidden bg-black/75 flex items-center justify-center p-4" onclick="closeProofModal(event)">
        <div class="relative bg-white dark:bg-neutral-900 rounded-2xl max-w-2xl w-full p-4 shadow-2xl overflow-hidden border border-slate-200 dark:border-neutral-700" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-neutral-800">
                <div>
                    <h3 id="modalStudentName" class="text-sm font-bold text-slate-900 dark:text-white">Bukti Transfer</h3>
                    <p id="modalOrderId" class="text-xs font-mono text-slate-400"></p>
                </div>
                <button type="button" onclick="closeProofModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-neutral-200 transition-colors">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-3 flex justify-center items-center bg-slate-50 dark:bg-neutral-950 rounded-xl my-3 max-h-[70vh] overflow-auto">
                <img id="modalProofImg" src="" alt="Bukti Transfer" class="max-h-[65vh] w-auto rounded-lg shadow-xs object-contain">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 dark:border-neutral-800">
                <a id="modalOpenTab" href="#" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 text-xs font-bold text-primary hover:underline">
                    Buka di Tab Baru
                </a>
                <button type="button" onclick="closeProofModal()" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openProofModal(url, orderId, studentName) {
            document.getElementById('modalProofImg').src = url;
            document.getElementById('modalOrderId').textContent = orderId;
            document.getElementById('modalStudentName').textContent = 'Bukti Transfer - ' + studentName;
            document.getElementById('modalOpenTab').href = url;
            document.getElementById('proofModal').classList.remove('hidden');
        }

        function closeProofModal(e) {
            document.getElementById('proofModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProofModal();
            }
        });
    </script>
@endsection
