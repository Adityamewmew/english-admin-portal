@extends('_admin._layout.app')

@section('title', 'Tambah Pengguna Baru')

@php
    use App\Constants\UserConst;
@endphp

@section('content')
    <div class="max-w-2xl">
        <x-admin.card variant="white" class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-neutral-700/80 flex items-center gap-x-3 bg-surface/50 dark:bg-neutral-800/50">
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
                        Tambah Pengguna Baru
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">Buat akun siswa atau administrator platform EDDY'S AI</p>
                </div>
            </div>

            <form id="add-form" class="p-6 space-y-4" navigate-form action="{{ route('admin.users.create') }}" method="POST">
                @csrf

                <x-admin.input
                    label="Nama Lengkap"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Contoh: John Doe"
                    required
                    error="{{ $errors->first('name') }}"
                />

                <x-admin.input
                    label="Alamat Email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="Contoh: user@eddys.ai"
                    required
                    error="{{ $errors->first('email') }}"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-admin.select
                        label="Peran / Hak Akses"
                        name="access_type"
                        required
                        :value="old('access_type', UserConst::STUDENT)"
                        :options="UserConst::getAppAccessTypes()"
                    />

                    <x-admin.select
                        label="Level CEFR Awal"
                        name="current_cefr"
                        :value="old('current_cefr', 'A1')"
                        :options="['A1' => 'Level A1 (Beginner)', 'A2' => 'Level A2 (Elementary)', 'B1' => 'Level B1 (Intermediate)', 'B2' => 'Level B2 (Upper Intermediate)', 'C1' => 'Level C1 (Advanced)', 'C2' => 'Level C2 (Mastery)']"
                    />
                </div>

                <x-admin.input
                    label="Password (Opsional)"
                    name="password"
                    type="password"
                    placeholder="Kosongkan untuk memakai password default"
                    error="{{ $errors->first('password') }}"
                />

                <div class="rounded-xl border border-secondary/30 bg-surface/70 dark:bg-neutral-900/60 p-3.5 text-xs text-primary dark:text-white flex items-start gap-2.5">
                    <span class="text-base leading-none">💡</span>
                    <span>Jika password dikosongkan, pengguna akan dibuat dengan password default: <code class="px-1.5 py-0.5 rounded bg-white dark:bg-neutral-800 border border-slate-300 font-mono font-bold text-secondary">password123</code></span>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-end gap-x-3">
                    <x-admin.button href="{{ route('admin.users.index') }}" variant="outline" as="a" size="sm">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" variant="secondary" size="sm">
                        Simpan Pengguna
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
