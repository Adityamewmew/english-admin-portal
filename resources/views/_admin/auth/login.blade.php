@extends('_admin._layout.auth')

@section('title', 'Login - EDDY\'S AI')

@section('content')
    <div class="w-full max-w-md p-4">
        <div class="mb-6 flex flex-col items-center justify-center text-center">
            <img src="{{ asset('images/logo-light.png') }}" alt="EDDY'S AI Logo" class="w-auto h-14 mb-2">
            <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">English Learning Platform</span>
        </div>

        <x-admin.card variant="white" padding="p-6 sm:p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-primary dark:text-white tracking-tight">
                    Masuk ke Sistem
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-neutral-400">
                    Gunakan kredensial akun administrator atau pengajar Anda
                </p>
            </div>

            <form id="login-form" action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                @error('login_error')
                    <div class="bg-rose-50 border border-rose-200 border-b-2 border-b-rose-300 text-xs sm:text-sm text-rose-700 rounded-xl p-3.5 dark:bg-rose-950/30 dark:border-rose-800 dark:text-rose-400"
                        role="alert">
                        {{ $message }}
                    </div>
                @enderror

                <div>
                    <x-admin.input label="Akun (Email / NPSN)" name="email" id="email" type="text" placeholder="admin@eddys.ai" required />
                </div>

                <div>
                    <x-admin.input label="Password" name="password" id="password" type="password" placeholder="••••••••" required />
                </div>

                <div class="pt-2">
                    <x-admin.button type="submit" id="login-btn" color="primary" class="w-full py-3 text-base">
                        <span id="btn-text">M A S U K</span>
                        <span id="btn-spinner"
                            class="animate-spin size-4 border-[3px] border-current border-t-transparent text-white rounded-full hidden"
                            role="status" aria-label="loading">
                            <span class="sr-only">Loading...</span>
                        </span>
                        <span id="btn-loading-text" class="hidden">Memproses...</span>
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const btnLoadingText = document.getElementById('btn-loading-text');

            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            btnSpinner.classList.add('inline-block');
            btnLoadingText.classList.remove('hidden');
        });
    </script>
@endsection
