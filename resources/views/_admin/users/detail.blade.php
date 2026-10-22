@extends('_admin._layout.app')

@section('title', 'Detail Pengguna')

@php
    use App\Constants\UserConst;
@endphp

@section('content')
    <div class="max-w-2xl">
        <x-admin.card variant="white" class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-neutral-700/80 flex items-center justify-between bg-surface/50 dark:bg-neutral-800/50">
                <div class="flex items-center gap-x-3">
                    <a href="{{ route('admin.users.index') }}"
                        class="btn-tactile btn-tactile-outline !py-2 !px-2.5 !rounded-xl"
                        title="Kembali ke Daftar Pengguna">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-heading font-extrabold text-lg text-primary dark:text-white tracking-tight">
                            Detail Pengguna
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-neutral-400">Informasi profil dan aktivitas pengguna</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ((int) $data->access_type === UserConst::STUDENT)
                        <x-admin.button href="{{ route('admin.gradebook.detail', $data->id) }}" variant="accent" as="a" size="sm">
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2m-6 9 2 2 4-4" />
                            </svg>
                            Rekap Nilai
                        </x-admin.button>
                    @endif
                    <x-admin.button href="{{ route('admin.users.update', $data->id) }}" variant="secondary" as="a" size="sm">
                        @include('_admin._layout.icons.pencil')
                        Edit Pengguna
                    </x-admin.button>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-center gap-x-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-700/60">
                    <div class="size-16 rounded-2xl bg-primary text-accent border-b-4 border-slate-900 font-heading font-extrabold text-2xl flex items-center justify-center shadow-sm">
                        {{ strtoupper(substr($data->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-xl text-primary dark:text-white">{{ $data->name }}</h3>
                        <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">{{ $data->email }}</p>
                        <div class="mt-2.5 flex items-center gap-2">
                            @if ((int) $data->access_type === UserConst::SUPERADMIN)
                                <x-admin.badge type="role" value="admin" />
                            @else
                                <x-admin.badge type="role" value="student" />
                            @endif
                            <x-admin.badge type="cefr" :value="$data->current_cefr ?? 'A1'" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-surface border border-slate-200 dark:bg-neutral-900/50 dark:border-neutral-700">
                        <span class="block text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wider mb-1">User ID</span>
                        <span class="block text-xs font-mono font-bold text-primary dark:text-neutral-200 break-all select-all">{{ $data->id }}</span>
                    </div>

                    <div class="p-4 rounded-xl bg-surface border border-slate-200 dark:bg-neutral-900/50 dark:border-neutral-700">
                        <span class="block text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wider mb-1">Tanggal Terdaftar</span>
                        <span class="block text-sm font-bold text-primary dark:text-neutral-200">
                            {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </x-admin.card>
    </div>
@endsection
