@extends('_admin._layout.app')

@section('title', 'Tambah Pengguna Baru')

@php
    use App\Constants\UserConst;
@endphp

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700 flex items-center gap-x-3">
                <a href="{{ route('admin.users.index') }}"
                    class="p-2 inline-flex items-center rounded-xl border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                </a>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tambah Pengguna Baru
                </h2>
            </div>

            <form id="add-form" class="p-6 space-y-4" navigate-form action="{{ route('admin.users.create') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium mb-1 dark:text-neutral-200">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('name') border-rose-500 @enderror"
                        placeholder="Contoh: John Doe" required>
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1 dark:text-neutral-200">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 @error('email') border-rose-500 @enderror"
                        placeholder="Contoh: user@eddys.ai" required>
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="access_type" class="block text-sm font-medium mb-1 dark:text-neutral-200">Peran / Hak Akses <span class="text-rose-500">*</span></label>
                        <select id="access_type" name="access_type"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach (UserConst::getAppAccessTypes() as $val => $lbl)
                                <option value="{{ $val }}" {{ old('access_type', UserConst::STUDENT) == $val ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="current_cefr" class="block text-sm font-medium mb-1 dark:text-neutral-200">Level CEFR Awal</label>
                        <select id="current_cefr" name="current_cefr"
                            class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            @foreach (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'] as $lvl)
                                <option value="{{ $lvl }}" {{ old('current_cefr', 'A1') == $lvl ? 'selected' : '' }}>
                                    Level {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1 dark:text-neutral-200">Password (Opsional)</label>
                    <input type="password" id="password" name="password"
                        class="py-2.5 px-3.5 block w-full border border-gray-200 rounded-lg text-sm dark:bg-neutral-900 dark:border-neutral-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Kosongkan untuk memakai password default (password123)">
                </div>

                <div class="rounded-xl border border-blue-200 bg-blue-50/70 p-3.5 text-xs text-blue-800 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300">
                    ℹ️ Jika password dikosongkan, pengguna akan dibuat dengan password default: <span class="font-mono font-bold">password123</span>
                </div>

                <div class="pt-2 flex items-center justify-end gap-x-2">
                    <a navigate href="{{ route('admin.users.index') }}"
                        class="py-2 px-3 text-sm rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="py-2 px-4 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
