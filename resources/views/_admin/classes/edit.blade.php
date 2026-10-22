@extends('_admin._layout.app')

@section('title', 'Edit Paket Kelas CEFR ' . $cefr)

@section('content')
    <x-admin.page-header :title="'Edit Paket Kelas CEFR ' . $cefr" subtitle="Kelola tarif, batas kuota AI harian, dan fasilitas materi untuk tier Basic, Pro, dan Ultra.">
        <x-admin.button href="{{ route('admin.classes.index') }}" size="sm" color="secondary">
            Kembali ke Daftar
        </x-admin.button>
    </x-admin.page-header>

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-center gap-2">
            <svg class="size-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $tierPlans = is_array($class->tier_plans) ? $class->tier_plans : [];
        $basic = $tierPlans['basic'] ?? [
            'name' => "Paket Basic ({$cefr})",
            'price' => (int) ($class->price * 0.65),
            'originalPrice' => (int) ($class->original_price * 0.65),
            'dailyChatLimit' => 20,
            'dailyVoiceMinutes' => 5,
            'features' => ["Akses penuh seluruh modul kurikulum CEFR {$cefr}", "20 pesan Chat AI per hari", "5 menit Voice AI per hari"],
        ];
        $pro = $tierPlans['pro'] ?? [
            'name' => "Paket Pro ({$cefr})",
            'price' => (int) $class->price,
            'originalPrice' => (int) $class->original_price,
            'dailyChatLimit' => 100,
            'dailyVoiceMinutes' => 30,
            'features' => ["Akses penuh seluruh modul kurikulum CEFR {$cefr}", "100 pesan Chat AI per hari", "30 menit Voice AI per hari", "Feedback evaluasi AI mendalam"],
        ];
        $ultra = $tierPlans['ultra'] ?? [
            'name' => "Paket Ultra ({$cefr})",
            'price' => (int) ($class->price * 1.65),
            'originalPrice' => (int) ($class->original_price * 1.65),
            'dailyChatLimit' => -1,
            'dailyVoiceMinutes' => 120,
            'features' => ["Akses penuh seluruh modul kurikulum CEFR {$cefr}", "Unlimited Chat AI tanpa batas", "120 menit Voice AI per hari", "Prioritas respon tutor ultra-cepat"],
        ];
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <form action="{{ route('admin.classes.update', ['cefr' => $cefr]) }}" method="POST" class="space-y-6" id="classForm">
            @csrf

            {{-- Card 1: Informasi Dasar Kelas --}}
            <x-admin.card>
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-neutral-800">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center size-11 rounded-xl font-black text-white text-base bg-primary shadow-xs">
                                {{ $cefr }}
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Informasi Dasar Kelas</h3>
                                <p class="text-xs text-gray-500 dark:text-neutral-400">Level CEFR {{ $cefr }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-neutral-600 peer-checked:bg-emerald-600"></div>
                            <span class="ml-2 text-xs font-semibold text-gray-700 dark:text-neutral-300">Status Aktif</span>
                        </label>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Nama Kelas Utama <span class="text-rose-500">*</span>
                        </label>
                        <x-admin.input name="name" :value="old('name', $class->name)" placeholder="Contoh: Kelas Bahasa Inggris Dasar (CEFR A1)" required />
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">
                            Deskripsi Kelas
                        </label>
                        <textarea name="description" rows="2" class="w-full text-xs rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3 text-gray-800 dark:text-neutral-200 focus:ring-2 focus:ring-primary/20"
                                  placeholder="Ringkasan materi dan target pembelajaran...">{{ old('description', $class->description) }}</textarea>
                    </div>
                </div>
            </x-admin.card>

            {{-- Card 2: Pengaturan 3 Tier AI (Basic, Pro, Ultra) --}}
            <x-admin.card>
                <div class="space-y-5">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pengaturan Paket & Batas Kuota AI Harian</h3>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">Tentukan tarif dan kuota AI yang didapatkan siswa pada masing-masing tier.</p>
                    </div>

                    {{-- Tab Switcher Pills --}}
                    <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-gray-100 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700" id="tierTabButtons">
                        <button type="button" onclick="switchTier('basic')" id="tabBtn-basic"
                                class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all text-gray-600 dark:text-neutral-300 hover:text-gray-900 flex items-center justify-center gap-1.5">
                            <span class="size-2 rounded-full bg-slate-400"></span>
                            <span>1. Paket Basic</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-md bg-slate-200 dark:bg-neutral-700 text-slate-700 dark:text-neutral-300">Hemat</span>
                        </button>
                        <button type="button" onclick="switchTier('pro')" id="tabBtn-pro"
                                class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all bg-white dark:bg-neutral-700 text-primary dark:text-white shadow-xs flex items-center justify-center gap-1.5">
                            <span class="size-2 rounded-full bg-indigo-500"></span>
                            <span>2. Paket Pro</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-md bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-black">Populer</span>
                        </button>
                        <button type="button" onclick="switchTier('ultra')" id="tabBtn-ultra"
                                class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all text-gray-600 dark:text-neutral-300 hover:text-gray-900 flex items-center justify-center gap-1.5">
                            <span class="size-2 rounded-full bg-amber-500"></span>
                            <span>3. Paket Ultra</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded-md bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 font-black">Maksimal</span>
                        </button>
                    </div>

                    {{-- ==================== PANEL BASIC ==================== --}}
                    <div id="tierPanel-basic" class="space-y-4 hidden">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-200 dark:border-neutral-700 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 dark:text-neutral-200">Konfigurasi Tier: Basic</span>
                            <span class="text-[11px] text-slate-500">Cocok untuk siswa dengan kebutuhan latihan esensial</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Tarif Basic (Rp) <span class="text-rose-500">*</span></label>
                                <x-admin.input type="number" name="tier_plans[basic][price]" :value="old('tier_plans.basic.price', $basic['price'] ?? 0)" min="0" required />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Harga Coret Basic (Rp)</label>
                                <x-admin.input type="number" name="tier_plans[basic][original_price]" :value="old('tier_plans.basic.original_price', $basic['originalPrice'] ?? '')" min="0" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Limit Chat AI Harian (pesan)</label>
                                <x-admin.input type="number" name="tier_plans[basic][daily_chat_limit]" :value="old('tier_plans.basic.daily_chat_limit', $basic['dailyChatLimit'] ?? 20)" min="0" required />
                                <span class="text-[11px] text-gray-400">Jumlah turn pesan interaksi AI study companion per hari.</span>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Limit Voice Call Harian (menit)</label>
                                <x-admin.input type="number" name="tier_plans[basic][daily_voice_minutes]" :value="old('tier_plans.basic.daily_voice_minutes', $basic['dailyVoiceMinutes'] ?? 5)" min="0" required />
                                <span class="text-[11px] text-gray-400">Total menit berbicara AI Voice Tutor per hari.</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Benefit Khusus Basic (1 baris = 1 benefit)</label>
                            @php $bfText = is_array($basic['features'] ?? null) ? implode("\n", $basic['features']) : ''; @endphp
                            <textarea name="tier_plans[basic][features]" rows="3" class="w-full text-xs font-mono rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3 text-gray-800 dark:text-neutral-200">{{ old('tier_plans.basic.features', $bfText) }}</textarea>
                        </div>
                    </div>

                    {{-- ==================== PANEL PRO (DEFAULT) ==================== --}}
                    <div id="tierPanel-pro" class="space-y-4">
                        <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200">Konfigurasi Tier: Pro (Rekomendasi Utama)</span>
                            <span class="text-[11px] text-indigo-700 dark:text-indigo-400">Kuota intensif harian untuk akselerasi kefasihan</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Tarif Pro (Rp) <span class="text-rose-500">*</span></label>
                                <x-admin.input type="number" name="tier_plans[pro][price]" :value="old('tier_plans.pro.price', $pro['price'] ?? $class->price)" min="0" required />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Harga Coret Pro (Rp)</label>
                                <x-admin.input type="number" name="tier_plans[pro][original_price]" :value="old('tier_plans.pro.original_price', $pro['originalPrice'] ?? $class->original_price)" min="0" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Limit Chat AI Harian (pesan)</label>
                                <x-admin.input type="number" name="tier_plans[pro][daily_chat_limit]" :value="old('tier_plans.pro.daily_chat_limit', $pro['dailyChatLimit'] ?? 100)" min="0" required />
                                <span class="text-[11px] text-gray-400">Jumlah turn pesan interaksi AI study companion per hari.</span>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Limit Voice Call Harian (menit)</label>
                                <x-admin.input type="number" name="tier_plans[pro][daily_voice_minutes]" :value="old('tier_plans.pro.daily_voice_minutes', $pro['dailyVoiceMinutes'] ?? 30)" min="0" required />
                                <span class="text-[11px] text-gray-400">Total menit berbicara AI Voice Tutor per hari.</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Benefit Khusus Pro (1 baris = 1 benefit)</label>
                            @php $pfText = is_array($pro['features'] ?? null) ? implode("\n", $pro['features']) : ''; @endphp
                            <textarea name="tier_plans[pro][features]" rows="3" class="w-full text-xs font-mono rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3 text-gray-800 dark:text-neutral-200">{{ old('tier_plans.pro.features', $pfText) }}</textarea>
                        </div>
                    </div>

                    {{-- ==================== PANEL ULTRA ==================== --}}
                    <div id="tierPanel-ultra" class="space-y-4 hidden">
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-900 dark:text-amber-200">Konfigurasi Tier: Ultra (Maksimal)</span>
                            <span class="text-[11px] text-amber-700 dark:text-amber-400">Tanpa batas chat dan kuota voice terbesar</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Tarif Ultra (Rp) <span class="text-rose-500">*</span></label>
                                <x-admin.input type="number" name="tier_plans[ultra][price]" :value="old('tier_plans.ultra.price', $ultra['price'] ?? 0)" min="0" required />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Harga Coret Ultra (Rp)</label>
                                <x-admin.input type="number" name="tier_plans[ultra][original_price]" :value="old('tier_plans.ultra.original_price', $ultra['originalPrice'] ?? '')" min="0" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Limit Chat AI Harian (pesan)</label>
                                <x-admin.input type="number" name="tier_plans[ultra][daily_chat_limit]" :value="old('tier_plans.ultra.daily_chat_limit', $ultra['dailyChatLimit'] ?? -1)" required />
                                <span class="text-[11px] text-gray-400">Isi <strong>-1</strong> untuk kuota chat harian <em>Unlimited</em>.</span>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Limit Voice Call Harian (menit)</label>
                                <x-admin.input type="number" name="tier_plans[ultra][daily_voice_minutes]" :value="old('tier_plans.ultra.daily_voice_minutes', $ultra['dailyVoiceMinutes'] ?? 120)" min="0" required />
                                <span class="text-[11px] text-gray-400">Total menit berbicara AI Voice Tutor per hari.</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-neutral-200">Benefit Khusus Ultra (1 baris = 1 benefit)</label>
                            @php $ufText = is_array($ultra['features'] ?? null) ? implode("\n", $ultra['features']) : ''; @endphp
                            <textarea name="tier_plans[ultra][features]" rows="3" class="w-full text-xs font-mono rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-3 text-gray-800 dark:text-neutral-200">{{ old('tier_plans.ultra.features', $ufText) }}</textarea>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            {{-- Form Submit Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <x-admin.button href="{{ route('admin.classes.index') }}" color="secondary" size="sm">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit" color="primary" size="sm">
                    Simpan Perubahan
                </x-admin.button>
            </div>
        </form>
    </div>

    <script>
        function switchTier(tier) {
            const tiers = ['basic', 'pro', 'ultra'];
            tiers.forEach(t => {
                const panel = document.getElementById('tierPanel-' + t);
                const btn = document.getElementById('tabBtn-' + t);
                if (t === tier) {
                    panel.classList.remove('hidden');
                    btn.classList.add('bg-white', 'dark:bg-neutral-700', 'text-primary', 'dark:text-white', 'shadow-xs');
                    btn.classList.remove('text-gray-600', 'dark:text-neutral-300');
                } else {
                    panel.classList.add('hidden');
                    btn.classList.remove('bg-white', 'dark:bg-neutral-700', 'text-primary', 'dark:text-white', 'shadow-xs');
                    btn.classList.add('text-gray-600', 'dark:text-neutral-300');
                }
            });
        }
    </script>
@endsection
