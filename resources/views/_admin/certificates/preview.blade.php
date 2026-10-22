@extends('_admin._layout.app')

@section('title', 'Pratinjau Sertifikat CEFR ' . $cefr)

@section('content')
    {{-- Top Action Bar (Hidden when printing) --}}
    <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-neutral-800 border border-gray-200/80 dark:border-neutral-700 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.certificates.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200 hover:bg-gray-50 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
            <div>
                <h1 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    Pratinjau Sertifikat Level {{ $cefr }}
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold text-white shadow-2xs" style="background-color: {{ $template->theme_color ?? '#0D3B66' }}">
                        {{ $cefr }}
                    </span>
                </h1>
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Posisi Nama: Y = {{ $template->name_y_percent ?? 48 }}%, Ukuran Font = {{ $template->name_font_size ?? 36 }}px
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($template->background_url)
                <a href="{{ route('admin.certificates.image', ['cefr' => $cefr]) }}" download="sertifikat-{{ strtolower($cefr) }}.jpg"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-gray-700 dark:text-neutral-200 hover:bg-gray-50 transition-colors">
                    <svg class="size-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Gambar
                </a>
            @endif

            <a href="{{ route('admin.certificates.edit', ['cefr' => $cefr]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 hover:bg-emerald-100 transition-colors">
                @include('_admin._layout.icons.pencil')
                Edit Penempatan
            </a>

            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl bg-[#0D3B66] text-white hover:bg-[#082542] shadow-sm cursor-pointer transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Certificate Visual Preview Stage --}}
    <div class="flex justify-center items-center py-4 sm:py-8 bg-gray-100/60 dark:bg-neutral-900/60 rounded-3xl border border-gray-200/80 dark:border-neutral-800 p-4 sm:p-8">
        <div id="printable-certificate"
             class="relative w-full max-w-[850px] aspect-[1.414/1] bg-[#FCFBF7] text-[#0D3B66] rounded-xl shadow-2xl border-8 border-double border-[#0D3B66]/20 overflow-hidden select-none flex flex-col justify-between"
             style="background-image: {{ $template->background_url ? "url('" . $template->background_url . "')" : 'none' }}; background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            @if ($template->background_url)
                {{-- Custom Uploaded Template Background Mode --}}
                {{-- Name Overlay at Configured Y Position --}}
                <div class="absolute left-0 right-0 flex items-center justify-center pointer-events-none px-8 sm:px-12 z-20"
                     style="top: {{ $template->name_y_percent ?? 48 }}%; transform: translateY(-50%);">
                    <h2 class="font-serif font-black text-[#0D3B66] text-center drop-shadow-xs tracking-wide leading-none"
                        style="font-size: {{ $template->name_font_size ?? 36 }}px;">
                        {{ $studentName }}
                    </h2>
                </div>

                {{-- Credential ID & Date bottom left --}}
                <div class="absolute bottom-4 sm:bottom-6 left-5 sm:left-10 text-left text-[9px] sm:text-[11px] text-slate-700 font-mono z-20 bg-white/80 backdrop-blur-xs px-2.5 py-1 rounded shadow-2xs border border-slate-200/80">
                    <p><span class="font-semibold text-slate-900">ID:</span> {{ $certificateNumber }}</p>
                    <p><span class="font-semibold text-slate-900">Date:</span> {{ $issuedDate }}</p>
                </div>
            @else
                {{-- Default Decorative Fallback Certificate --}}
                <div class="absolute inset-3 border-2 border-[#D4AF37]/50 rounded-lg pointer-events-none"></div>
                <div class="absolute inset-4 border border-[#0D3B66]/20 rounded-md pointer-events-none"></div>

                {{-- Header Section --}}
                <div class="relative text-center z-10 pt-8 sm:pt-12 px-6">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <div class="w-10 h-10 rounded-full bg-[#0D3B66] flex items-center justify-center text-[#F4B41A] shadow-md font-bold">
                            ★
                        </div>
                        <span class="text-sm font-black tracking-[0.25em] text-[#0D3B66] uppercase">
                            EDDY'S AI ENGLISH TUTOR
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-serif font-black tracking-wider text-[#0D3B66] uppercase">
                        {{ $template->title }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 tracking-widest uppercase mt-0.5">
                        International CEFR Standard Assessment
                    </p>
                </div>

                {{-- Recipient Name & Description --}}
                <div class="relative text-center z-10 my-auto py-2 px-6">
                    <p class="text-xs sm:text-sm italic font-serif text-slate-600 mb-1">
                        This is proudly presented to
                    </p>
                    <h2 class="text-2xl sm:text-4xl font-serif font-bold text-[#0D3B66] border-b-2 border-[#D4AF37]/60 inline-block px-8 py-1 mb-3">
                        {{ $studentName }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-700 max-w-xl mx-auto leading-relaxed">
                        {{ $template->description ?? 'For successfully demonstrating communicative proficiency in CEFR ' . $cefr . '.' }}
                    </p>
                    <div class="mt-3 flex items-center justify-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-xs" style="background-color: {{ $template->theme_color ?? '#0D3B66' }}">
                            Level {{ $cefr }} Certified
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Mastery Score: {{ $score }}%
                        </span>
                    </div>
                </div>

                {{-- Signatory & Credentials --}}
                <div class="relative flex items-end justify-between z-10 p-6 sm:p-10 border-t border-slate-200">
                    <div class="text-left text-[10px] sm:text-xs text-slate-500 font-mono">
                        <p><span class="font-semibold text-slate-700">ID:</span> {{ $certificateNumber }}</p>
                        <p><span class="font-semibold text-slate-700">Date:</span> {{ $issuedDate }}</p>
                    </div>
                    <div class="text-right">
                        <div class="inline-block border-b border-slate-400 pb-1 w-40 text-center">
                            <span class="font-serif font-bold text-xs sm:text-sm text-[#0D3B66]">
                                {{ $template->instructor_name ?? 'Mr. Khoirul, M.Pd.' }}
                            </span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-slate-500 font-medium mt-0.5">
                            {{ $template->instructor_title ?? 'Head of English Academic Affairs' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Print CSS Rules --}}
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #printable-certificate,
            #printable-certificate * {
                visibility: visible !important;
            }
            #printable-certificate {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                max-width: none !important;
                border: 8px double #0d3b66 !important;
                box-shadow: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
@endsection
