@extends('_admin._layout.app')

@section('title', 'Edit Pengguna')

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
                        Edit Data Pengguna
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">Perbarui profil, hak akses peran, atau password akun</p>
                </div>
            </div>

            <form id="edit-form" class="p-6 space-y-4" navigate-form action="{{ route('admin.users.doUpdate', $userId) }}" method="POST">
                @csrf

                <x-admin.input
                    label="Nama Lengkap"
                    name="name"
                    value="{{ old('name', $data->name) }}"
                    required
                    error="{{ $errors->first('name') }}"
                />

                <x-admin.input
                    label="Alamat Email"
                    name="email"
                    type="email"
                    value="{{ old('email', $data->email) }}"
                    required
                    error="{{ $errors->first('email') }}"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-admin.select
                        label="Peran / Hak Akses"
                        name="access_type"
                        required
                        :value="old('access_type', $data->access_type)"
                        :options="UserConst::getAppAccessTypes()"
                    />

                    <x-admin.select
                        label="Level CEFR"
                        name="current_cefr"
                        :value="old('current_cefr', $data->current_cefr ?? 'A1')"
                        :options="['A1' => 'Level A1 (Beginner)', 'A2' => 'Level A2 (Elementary)', 'B1' => 'Level B1 (Intermediate)', 'B2' => 'Level B2 (Upper Intermediate)', 'C1' => 'Level C1 (Advanced)', 'C2' => 'Level C2 (Mastery)']"
                    />
                </div>

                <x-admin.input
                    label="Ubah Password (Opsional)"
                    name="password"
                    type="password"
                    placeholder="Biarkan kosong jika tidak ingin mengubah password"
                    error="{{ $errors->first('password') }}"
                />

                <div class="pt-4 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-end gap-x-3">
                    <x-admin.button href="{{ route('admin.users.index') }}" variant="outline" as="a" size="sm">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" variant="secondary" size="sm">
                        Perbarui Data
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
