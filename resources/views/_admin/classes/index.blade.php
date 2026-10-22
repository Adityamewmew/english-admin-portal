@extends('_admin._layout.app')

@section('title', 'Manajemen Kelas & Tarif')

@section('content')
    <x-admin.page-header :title="'Manajemen Kelas & Tarif'" subtitle="Kelola harga akses kelas level CEFR (A1-C2) dan rekap akses siswa setelah placement test.">
        <x-admin.button href="{{ route('admin.classes.add') }}" size="sm" color="primary">
            @include('_admin._layout.icons.add')
            Tambah Paket Kelas
        </x-admin.button>
    </x-admin.page-header>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="mb-6 flex items-center gap-2 border-b border-gray-200 dark:border-neutral-700">
        <a href="{{ route('admin.classes.index', ['tab' => 'classes']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'classes' ? 'border-primary text-primary dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Daftar & Tarif Kelas CEFR
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold">
                {{ count($classes) }}
            </span>
        </a>
        <a href="{{ route('admin.classes.index', ['tab' => 'access']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors {{ $activeTab === 'access' ? 'border-primary text-primary dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Rekap Akses Siswa
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-bold">
                {{ $totalAccesses }}
            </span>
        </a>
    </div>

    @if ($activeTab === 'classes')
        {{-- TAB 1: MASTER KELAS & TARIF CRUD TABLE --}}
        <x-admin.table.wrapper>
            <x-admin.table>
                <x-admin.table.thead>
                    <tr>
                        <x-admin.table.th>Level CEFR</x-admin.table.th>
                        <x-admin.table.th>Nama Kelas & Ringkasan</x-admin.table.th>
                        <x-admin.table.th>Tarif Tier (Basic / Pro / Ultra)</x-admin.table.th>
                        <x-admin.table.th class="text-center">Fasilitas</x-admin.table.th>
                        <x-admin.table.th class="text-center">Status</x-admin.table.th>
                        <x-admin.table.th class="text-center">Siswa Aktif</x-admin.table.th>
                        <x-admin.table.th align="end">Aksi</x-admin.table.th>
                    </tr>
                </x-admin.table.thead>
                <x-admin.table.tbody>
                    @forelse ($classes as $c)
                        @php
                            $colors = [
                                'A1' => '#0D3B66',
                                'A2' => '#107C41',
                                'B1' => '#0078D4',
                                'B2' => '#D83B01',
                                'C1' => '#744DA9',
                                'C2' => '#C239B3',
                            ];
                            $cefrColor = $colors[$c->cefr] ?? '#0D3B66';
                            $tp = is_array($c->tier_plans) ? $c->tier_plans : [];
                            $bPrice = $tp['basic']['price'] ?? (int) ($c->price * 0.65);
                            $pPrice = $tp['pro']['price'] ?? $c->price;
                            $uPrice = $tp['ultra']['price'] ?? (int) ($c->price * 1.65);
                        @endphp
                        <x-admin.table.tr>
                            {{-- CEFR Badge --}}
                            <x-admin.table.td>
                                <span class="inline-flex items-center justify-center size-9 rounded-xl font-black text-white text-xs shadow-xs" style="background-color: {{ $cefrColor }}">
                                    {{ $c->cefr }}
                                </span>
                            </x-admin.table.td>

                            {{-- Name & Description --}}
                            <x-admin.table.td>
                                <div class="max-w-xs sm:max-w-sm">
                                    <p class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">
                                        {{ $c->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2 mt-0.5">
                                        {{ $c->description ?? '-' }}
                                    </p>
                                </div>
                            </x-admin.table.td>

                            {{-- 3 Tier Pricing (Basic / Pro / Ultra) --}}
                            <x-admin.table.td>
                                <div class="space-y-1 font-mono text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 font-bold">Basic</span>
                                        <span class="font-bold text-gray-800 dark:text-neutral-200">Rp {{ number_format($bPrice, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-gray-400 font-sans">(20c/5m)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-bold">Pro</span>
                                        <span class="font-bold text-indigo-900 dark:text-indigo-200">Rp {{ number_format($pPrice, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-gray-400 font-sans">(100c/30m)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 font-bold">Ultra</span>
                                        <span class="font-bold text-amber-900 dark:text-amber-200">Rp {{ number_format($uPrice, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-gray-400 font-sans">(∞/120m)</span>
                                    </div>
                                </div>
                            </x-admin.table.td>

                            {{-- Features Count --}}
                            <x-admin.table.td class="text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-300">
                                    {{ is_array($c->features) ? count($c->features) : 0 }} Poin
                                </span>
                            </x-admin.table.td>

                            {{-- Active Status --}}
                            <x-admin.table.td class="text-center">
                                @if ($c->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-neutral-400">
                                        Nonaktif
                                    </span>
                                @endif
                            </x-admin.table.td>

                            {{-- Active Students Count --}}
                            <x-admin.table.td class="text-center font-bold text-gray-800 dark:text-neutral-200">
                                {{ $c->student_count }}
                            </x-admin.table.td>

                            {{-- Actions --}}
                            <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end gap-x-2">
                                <a href="{{ route('admin.classes.edit', ['cefr' => $c->cefr]) }}"
                                   class="inline-flex items-center gap-1 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400" title="Edit Tarif">
                                    @include('_admin._layout.icons.pencil')
                                    Edit
                                </a>

                                <form action="{{ route('admin.classes.delete', ['cefr' => $c->cefr]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket kelas CEFR {{ $c->cefr }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 py-1.5 px-2.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 cursor-pointer" title="Hapus">
                                        @include('_admin._layout.icons.trash')
                                        Hapus
                                    </button>
                                </form>
                            </x-admin.table.td>
                        </x-admin.table.tr>
                    @empty
                        <x-admin.table.tr>
                            <x-admin.table.td colspan="7" class="text-center py-10">
                                <x-admin.empty-state message="Belum ada data paket kelas. Klik 'Tambah Paket Kelas' di atas untuk mulai membuat kelas." />
                            </x-admin.table.td>
                        </x-admin.table.tr>
                    @endforelse
                </x-admin.table.tbody>
            </x-admin.table>
        </x-admin.table.wrapper>
    @else
        {{-- TAB 2: REKAP AKSES SISWA & HIBAH MANUAL --}}
        <div class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
                {{-- Filter & Search Form --}}
                <form action="{{ route('admin.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="tab" value="access">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500 dark:text-neutral-400">Level CEFR:</span>
                        @foreach (['all', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $level)
                            <a href="{{ route('admin.classes.index', ['tab' => 'access', 'cefr' => $level, 'keywords' => $keywords]) }}"
                               class="px-3 py-1 text-xs rounded-full font-medium transition-colors {{ $cefr === $level ? 'bg-primary text-white font-bold' : 'bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 hover:bg-gray-200' }}">
                                {{ $level === 'all' ? 'Semua Level' : $level }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari nama / email siswa..." size="sm" />
                        <x-admin.button type="submit" size="sm" color="primary">
                            Cari
                        </x-admin.button>
                    </div>
                </form>

                {{-- Button Open Grant Access Modal --}}
                <button type="button" data-hs-overlay="#grant-access-modal"
                        class="inline-flex items-center gap-1.5 py-2 px-3 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs cursor-pointer transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Beri Akses Manual Siswa
                </button>
            </div>

            {{-- Table Rekap Akses --}}
            <x-admin.table.wrapper>
                <x-admin.table>
                    <x-admin.table.thead>
                        <tr>
                            <x-admin.table.th>Nama Siswa</x-admin.table.th>
                            <x-admin.table.th>Email Siswa</x-admin.table.th>
                            <x-admin.table.th class="text-center">Kelas Dibeli</x-admin.table.th>
                            <x-admin.table.th class="text-center">Tier Paket</x-admin.table.th>
                            <x-admin.table.th class="text-center">Asal Akses</x-admin.table.th>
                            <x-admin.table.th class="text-center">Tanggal Mulai</x-admin.table.th>
                            <x-admin.table.th class="text-center">Status</x-admin.table.th>
                            <x-admin.table.th align="end">Aksi</x-admin.table.th>
                        </tr>
                    </x-admin.table.thead>
                    <x-admin.table.tbody>
                        @forelse ($accessList as $item)
                            <x-admin.table.tr>
                                <x-admin.table.td class="font-semibold text-gray-900 dark:text-white">
                                    {{ $item->user_name ?? 'Student' }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-gray-500 dark:text-neutral-400 font-mono text-xs">
                                    {{ $item->user_email ?? '-' }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded font-black text-[11px] bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                        CEFR {{ $item->cefr }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    @php $t = strtolower($item->tier ?? 'basic'); @endphp
                                    @if ($t === 'ultra')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200">
                                            ★ Ultra
                                        </span>
                                    @elseif ($t === 'pro')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200">
                                            Pro
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300 border border-slate-200">
                                            Basic
                                        </span>
                                    @endif
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    <span class="text-xs font-medium text-gray-600 dark:text-neutral-300 capitalize">
                                        {{ $item->granted_by ?? 'system' }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center text-xs text-gray-500 dark:text-neutral-400">
                                    {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i') }}
                                </x-admin.table.td>
                                <x-admin.table.td class="text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        Active
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td innerClass="px-6 py-2 flex items-center justify-end">
                                    <form action="{{ route('admin.classes.revoke_access', ['id' => $item->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mencabut akses kelas {{ $item->cefr }} untuk siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 py-1 px-2 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-900/20 dark:text-rose-400 cursor-pointer" title="Cabut Akses">
                                            @include('_admin._layout.icons.trash')
                                            Cabut
                                        </button>
                                    </form>
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @empty
                            <x-admin.table.tr>
                                <x-admin.table.td colspan="8" class="text-center py-10">
                                    <x-admin.empty-state message="Belum ada data akses kelas siswa." />
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @endforelse
                    </x-admin.table.tbody>
                </x-admin.table>
            </x-admin.table.wrapper>

            @if (method_exists($accessList, 'links'))
                <div class="mt-4">
                    {{ $accessList->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Beri Akses Manual --}}
        <x-admin.modal id="grant-access-modal" title="Beri Akses Kelas Manual ke Siswa" size="sm:max-w-md" :formAction="route('admin.classes.grant_access')" formMethod="POST">
            <div class="space-y-4 text-left">
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Gunakan form ini untuk membuka akses kelas secara langsung ke siswa (misal: pembayaran transfer manual, beasiswa, atau kebutuhan khusus).
                </p>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300">
                        Pilih Siswa <span class="text-rose-500">*</span>
                    </label>
                    <select name="user_id" required class="w-full text-xs rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-2.5 text-gray-800 dark:text-neutral-200 focus:ring-2 focus:ring-primary/20">
                        <option value="">-- Pilih Akun Siswa --</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->name }} ({{ $s->email }}) - Level: {{ $s->current_cefr ?? 'A1' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300">
                        Pilih Level Kelas yang Dibuka <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $level)
                            <label class="flex items-center gap-2 p-2 rounded-xl border border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer">
                                <input type="radio" name="cefr" value="{{ $level }}" required class="text-primary focus:ring-primary">
                                <span class="text-xs font-bold text-gray-800 dark:text-neutral-200">{{ $level }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-neutral-300">
                        Pilih Tier Paket AI <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col p-2.5 rounded-xl border border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer text-center">
                            <input type="radio" name="tier" value="basic" checked class="text-primary focus:ring-primary mx-auto mb-1">
                            <span class="text-xs font-bold text-gray-800 dark:text-neutral-200">Basic</span>
                            <span class="text-[10px] text-gray-400">20c / 5m</span>
                        </label>
                        <label class="flex flex-col p-2.5 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50/30 dark:bg-indigo-950/20 hover:bg-indigo-50 cursor-pointer text-center">
                            <input type="radio" name="tier" value="pro" class="text-indigo-600 focus:ring-indigo-600 mx-auto mb-1">
                            <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200">Pro</span>
                            <span class="text-[10px] text-indigo-600">100c / 30m</span>
                        </label>
                        <label class="flex flex-col p-2.5 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50/30 dark:bg-amber-950/20 hover:bg-amber-50 cursor-pointer text-center">
                            <input type="radio" name="tier" value="ultra" class="text-amber-600 focus:ring-amber-600 mx-auto mb-1">
                            <span class="text-xs font-bold text-amber-900 dark:text-amber-200">Ultra</span>
                            <span class="text-[10px] text-amber-600">∞ / 120m</span>
                        </label>
                    </div>
                </div>
            </div>
        </x-admin.modal>
    @endif
@endsection
