@extends('_admin._layout.app')

@section('title', 'Detail Pengguna')

@php
    use App\Constants\UserConst;
@endphp

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between">
                <div class="flex items-center gap-x-3">
                    <a href="{{ route('admin.users.index') }}"
                        class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                    </a>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        Detail Pengguna
                    </h2>
                </div>
                <a navigate href="{{ route('admin.users.update', $data->id) }}"
                    class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 inline-flex items-center gap-x-1.5">
                    @include('_admin._layout.icons.pencil')
                    Edit Pengguna
                </a>
            </div>

            <div class="p-6">
                <div class="flex items-center gap-x-4 mb-6 pb-6 border-b border-gray-100 dark:border-neutral-700/60">
                    <div class="size-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white text-2xl font-bold flex items-center justify-center shadow-md shadow-blue-500/20">
                        {{ strtoupper(substr($data->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $data->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $data->email }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            @if ((int) $data->access_type === UserConst::SUPERADMIN)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                    Administrator
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    Siswa (Student)
                                </span>
                            @endif
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-700">
                                Level {{ $data->current_cefr ?? 'A1' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <span class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">User ID</span>
                        <span class="block text-xs font-mono text-gray-800 dark:text-neutral-200 break-all">{{ $data->id }}</span>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <span class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Tanggal Terdaftar</span>
                        <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-200">
                            {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
