@extends('_admin._layout.app')

@section('title', 'Ubah Password')

@section('content')
    <x-admin.page-header :title="'Ubah Password Akun'" subtitle="Perbarui kata sandi untuk mengamankan akun administrator Anda" />

    <div class="max-w-xl">
        <x-admin.card variant="white" padding="p-6 sm:p-7">
            <form id="change-password-form" navigate-form
                action="{{ route('admin.profile.do_change_password') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <x-admin.input label="Password Lama" type="password" id="current_password" name="current_password"
                        placeholder="Masukkan password lama anda" required :error="$errors->first('current_password')" />
                </div>

                <div>
                    <x-admin.input label="Password Baru" type="password" id="password" name="password"
                        placeholder="Minimal 8 karakter" required :error="$errors->first('password')" />
                </div>

                <div>
                    <x-admin.input label="Ulangi Password Baru" type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Ulangi password baru anda" required />
                </div>

                <div class="pt-2 flex justify-start">
                    <x-admin.button type="submit" color="primary" size="sm">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Simpan Password Baru
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
