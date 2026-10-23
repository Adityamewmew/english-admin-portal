@extends('_admin._layout.app')

@section('title', 'Pratinjau Sertifikat CEFR ' . $cefr)

@php
    $orientation = strtolower($template->orientation ?? 'portrait');
    $isSquare = $orientation === 'square';
    $isPortrait = !$isSquare && $orientation !== 'landscape';
    $tier = ($cefr === 'C1' || $cefr === 'C2') ? 'Advanced' : (($cefr === 'B1' || $cefr === 'B2') ? 'Intermediate' : 'Elementary');
    $qualification = $score >= 89 ? 'EXCELLENT' : ($score >= 76 ? 'GOOD' : 'FAIR');
    $hasBackground = !empty($template->background_url);
@endphp

@section('content')
    {{-- Top Action Bar (Hidden when printing) --}}
    <x-admin.card variant="white" padding="p-4 sm:p-5" class="no-print mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-admin.button href="{{ route('admin.certificates.index') }}" size="sm" color="outline-secondary">
                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </x-admin.button>
                <div>
                    <h1 class="text-base font-extrabold text-primary dark:text-white flex items-center gap-2">
                        Pratinjau Sertifikat Resmi Level {{ $cefr }} ({{ $tier }})
                        <x-admin.badge :cefr="$cefr" size="sm">
                            {{ $cefr }}
                        </x-admin.badge>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 font-normal">
                            {{ $isSquare ? 'Square 1:1' : ($isPortrait ? 'A4 Portrait' : 'A4 Landscape') }}
                        </span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-neutral-400">
                        Format Resmi Eddy's Local Examination in English · No Ref: {{ $certificateNumber }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <x-admin.button type="button" id="btn-download-image" onclick="downloadCertificateImage()" size="sm" color="outline-secondary">
                    <svg class="size-4 text-slate-500 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span id="btn-download-label">Unduh Gambar</span>
                </x-admin.button>

                <x-admin.button href="{{ route('admin.certificates.edit', ['cefr' => $cefr]) }}" size="sm" color="outline-secondary">
                    @include('_admin._layout.icons.pencil')
                    Edit Pengaturan
                </x-admin.button>

                <x-admin.button type="button" onclick="window.print()" size="sm" color="primary">
                    <svg class="size-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak / Simpan PDF
                </x-admin.button>
            </div>
        </div>
    </x-admin.card>

    {{-- Certificate Visual Preview Stage --}}
    <div class="flex justify-center items-center py-4 sm:py-8 bg-gray-100/60 dark:bg-neutral-900/60 rounded-3xl border border-gray-200/80 dark:border-neutral-800 p-3 sm:p-8">
        <div id="printable-certificate"
             class="relative w-full {{ $isSquare ? 'max-w-[620px] aspect-square' : ($isPortrait ? 'max-w-[620px] aspect-[1/1.414]' : 'max-w-[850px] aspect-[1.414/1]') }} bg-[#FCFBF7] text-[#0D3B66] rounded-xl shadow-2xl overflow-hidden select-none"
             style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">

            @if ($hasBackground)
                {{-- MODE A: OFFICIAL TEMPLATE BACKGROUND WITH DYNAMIC X/Y OVERLAYS --}}
                <img id="cert-bg-img" src="{{ route('admin.certificates.image', ['cefr' => $cefr]) }}" alt="Background Sertifikat"
                     class="absolute inset-0 w-full h-full object-contain pointer-events-none select-none z-0">

                <div class="absolute inset-0 w-full h-full z-10 pointer-events-none select-none">
                    {{-- 1. Student Name Overlay --}}
                    <div class="absolute z-20 text-center px-1 whitespace-nowrap"
                         style="left: {{ $template->name_x_percent ?? 50 }}%; top: {{ $template->name_y_percent ?? 34 }}%; transform: translate(-50%, -50%);">
                        <span class="font-serif font-black text-[#0D3B66] text-center drop-shadow-xs tracking-wide leading-none whitespace-nowrap select-none inline-block"
                              style="font-size: {{ ($template->name_font_size ?? 26) * 0.85 }}px;">
                            {{ $studentName }}
                        </span>
                    </div>

                    {{-- 2. Scores Overlay (Breakdown 6 Nilai + Pass Standard) --}}
                    <div class="absolute z-10 text-center px-1"
                         style="left: {{ $template->scores_x_percent ?? 50 }}%; top: {{ $template->scores_y_percent ?? 52 }}%; transform: translate(-50%, -50%);">
                        <div class="space-y-1 text-center select-none">
                            <div class="w-[220px] sm:w-[260px] text-left font-mono font-bold space-y-0.5 text-slate-900 bg-white/95 p-2 rounded border border-slate-200 shadow-2xs"
                                 style="font-size: {{ ($template->scores_font_size ?? 10) * 0.95 }}px;">
                                <div class="flex justify-between"><span>STRUCTURE &amp; VOCABULARY</span><span>: {{ $score > 88 ? $score - 1 : $score + 1 }}</span></div>
                                <div class="flex justify-between"><span>READING COMPREHENSION</span><span>: {{ $score }}</span></div>
                                <div class="flex justify-between"><span>LISTENING COMPREHENSION</span><span>: {{ min(100, $score + 1) }}</span></div>
                                <div class="flex justify-between"><span>DICTATION</span><span>: {{ min(100, $score >= 88 ? $score + 2 : $score - 1) }}</span></div>
                                <div class="flex justify-between"><span>CONVERSATION</span><span>: {{ $score >= 85 ? $score + 1 : $score }}</span></div>
                                <div class="flex justify-between"><span>COMPOSITION</span><span>: {{ max(60, $score - 1) }}</span></div>
                            </div>
                            <div class="inline-block border border-slate-700/60 rounded-xl px-2.5 py-0.5 bg-[#FCFBF7]/95 text-[7px] sm:text-[8px] shadow-2xs font-mono text-slate-800">
                                <p class="font-bold">89-100 = EXCELLENT · 76-88 = GOOD · 60-75 = FAIR</p>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Date of Examination Overlay (Value Only) --}}
                    <div class="absolute z-10 text-center whitespace-nowrap"
                         style="left: {{ $template->date_x_percent ?? 18 }}%; top: {{ $template->date_y_percent ?? 80 }}%; transform: translate(-50%, -50%);">
                        <span class="font-mono font-bold text-slate-800 drop-shadow-2xs leading-none inline-block"
                              style="font-size: {{ $template->date_font_size ?? 8 }}px;">
                            {{ $issuedDate }}
                        </span>
                    </div>

                    {{-- 4. Place of Entry Overlay (Value Only) --}}
                    <div class="absolute z-10 text-center max-w-[140px] sm:max-w-[180px]"
                         style="left: {{ $template->place_x_percent ?? 50 }}%; top: {{ $template->place_y_percent ?? 80 }}%; transform: translate(-50%, -50%);">
                        <span class="font-bold text-slate-800 leading-tight drop-shadow-2xs block"
                              style="font-size: {{ $template->place_font_size ?? 8 }}px;">
                            {{ $template->place_of_entry ?? 'PBN Kelapa Gading, D.18 Jember East Java' }}
                        </span>
                    </div>

                    {{-- 5. Reference No. Overlay (Value Only) --}}
                    <div class="absolute z-10 text-center whitespace-nowrap"
                         style="left: {{ $template->ref_no_x_percent ?? 82 }}%; top: {{ $template->ref_no_y_percent ?? 80 }}%; transform: translate(-50%, -50%);">
                        <span class="font-mono font-bold text-slate-900 drop-shadow-2xs leading-none inline-block"
                              style="font-size: {{ $template->ref_no_font_size ?? 8 }}px;">
                            {{ $certificateNumber }}
                        </span>
                    </div>
                </div>
            @elseif ($isPortrait)
                {{-- MODE B: GENERATED PORTRAIT FALLBACK --}}
                <div class="absolute top-0 left-0 w-24 sm:w-32 h-2.5 sm:h-3.5 bg-gradient-to-r from-[#F4B41A] to-[#D4AF37] z-10"></div>
                <div class="relative z-10 flex flex-col justify-between h-full p-4 sm:p-6 text-center leading-normal">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-200/80 pb-3 pt-2">
                        <div class="text-left flex items-start gap-2">
                            <div class="w-8 h-8 rounded-full border border-sky-600/40 bg-sky-50 flex items-center justify-center font-bold text-sky-800 text-[10px] shrink-0">BAN</div>
                            <div class="text-[7.5px] sm:text-[9px] text-slate-600 font-sans leading-tight">
                                <p class="font-semibold text-slate-800">Terakreditasi BAN PAUD dan PNF/AKR/2019</p>
                                <p>NPSN : K5655732</p>
                                <p>Nomor Ijin : 421.8/12.KP/PAUD/35.09.325/2024</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <div class="w-6 h-6 rounded bg-[#0D3B66] text-[#F4B41A] flex items-center justify-center font-black text-[10px]">E</div>
                                <span class="font-serif font-black tracking-wider text-sm sm:text-base text-[#0D3B66]">EDDY'S</span>
                            </div>
                            <p class="text-[7.5px] sm:text-[9px] text-rose-600 font-bold uppercase tracking-wider">Great Communicators</p>
                            <p class="text-[7px] text-slate-400 font-mono">SINCE 1990</p>
                        </div>
                    </div>

                    <div class="space-y-1 my-auto pt-1">
                        <h1 class="text-xl sm:text-2xl font-serif font-black tracking-wider text-[#0D3B66] uppercase">TO WHOM IT MAY CONCERN</h1>
                        <p class="text-xs sm:text-sm font-extrabold tracking-widest text-[#D4AF37] uppercase">EDDY'S LOCAL EXAMINATION IN ENGLISH</p>
                    </div>

                    <div class="space-y-1 max-w-md mx-auto my-auto px-2">
                        <p class="text-[11px] sm:text-xs text-slate-600 font-serif">This is to certify the candidate named above</p>
                        <h2 class="text-lg sm:text-2xl font-serif font-black text-[#0D3B66] tracking-wide py-0.5 border-b border-[#D4AF37]/50 inline-block px-4">
                            {{ $studentName }}
                        </h2>
                        <p class="text-[11px] sm:text-xs text-slate-700 font-serif pt-1">
                            examined for the <strong class="font-extrabold text-[#0D3B66]">{{ $tier }}</strong> level in English ({{ $cefr }}) and qualified for the award of:
                        </p>
                    </div>

                    <div class="my-auto py-1">
                        <div class="max-w-[280px] sm:max-w-[320px] mx-auto text-left font-mono font-bold text-[10px] sm:text-xs space-y-1 text-slate-800 bg-white/95 p-2.5 sm:p-3 rounded-lg border border-slate-200/90 shadow-2xs">
                            <div class="flex justify-between"><span>STRUCTURE &amp; VOCABULARY</span><span>: {{ $score > 88 ? $score - 1 : $score + 1 }}</span></div>
                            <div class="flex justify-between"><span>READING COMPREHENSION</span><span>: {{ $score }}</span></div>
                            <div class="flex justify-between"><span>LISTENING COMPREHENSION</span><span>: {{ min(100, $score + 1) }}</span></div>
                            <div class="flex justify-between"><span>DICTATION</span><span>: {{ min(100, $score >= 88 ? $score + 2 : $score - 1) }}</span></div>
                            <div class="flex justify-between"><span>CONVERSATION</span><span>: {{ $score >= 85 ? $score + 1 : $score }}</span></div>
                            <div class="flex justify-between"><span>COMPOSITION</span><span>: {{ max(60, $score - 1) }}</span></div>
                        </div>
                    </div>

                    <div class="my-auto">
                        <div class="inline-block border border-slate-700/60 rounded-2xl px-4 py-1.5 bg-[#FCFBF7] text-slate-800 shadow-2xs">
                            <p class="font-bold text-[8.5px] sm:text-[9.5px] tracking-wider uppercase border-b border-slate-300 pb-0.5">THE PASS STANDART IS INDICATED THUS</p>
                            <div class="font-mono text-[8px] sm:text-[9px] text-slate-700 space-y-0.5 pt-0.5">
                                <p>89–100 = EXCELLENT</p>
                                <p>76–88 = GOOD</p>
                                <p>60–75 = FAIR</p>
                            </div>
                            <div class="mt-1 pt-1 border-t border-slate-200">
                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">STATUS: {{ $qualification }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200/90 space-y-2">
                        <div class="grid grid-cols-3 gap-2 text-left text-[8px] sm:text-[9.5px] font-sans">
                            <div>
                                <p class="font-bold text-slate-900">Date of Examination</p>
                                <p class="text-slate-600 font-mono text-[8px] sm:text-[9px] mt-0.5">{{ $issuedDate }}</p>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-slate-900">Place of Entry</p>
                                <p class="text-slate-600 text-[7.5px] sm:text-[8.5px] mt-0.5 leading-tight">{{ $template->place_of_entry ?? 'PBN Kelapa Gading, D.18 Jember East Java' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-900">Reference No.</p>
                                <p class="text-slate-900 font-mono font-bold text-[8px] sm:text-[9px] mt-0.5">{{ $certificateNumber }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center pt-1">
                            <p class="text-[9px] font-bold text-slate-800">Director of Studies</p>
                            <div class="relative my-1 w-32 h-9 flex items-center justify-center">
                                @if (!empty($template->signature_url))
                                    <img src="{{ $template->signature_url }}" alt="Signature" class="h-full object-contain">
                                @else
                                    <div class="w-24 border-b border-slate-400 mt-4"></div>
                                @endif
                            </div>
                            <p class="font-serif font-black text-xs sm:text-sm text-[#0D3B66]">{{ $template->instructor_name ?? 'KHOIRUL ANWAR, M.Pd' }}</p>
                        </div>
                    </div>

                    <div class="bg-[#0D3B66] text-white -mx-4 -mb-4 sm:-mx-6 sm:-mb-6 px-3 py-1.5 flex flex-wrap items-center justify-between text-[7px] sm:text-[8px] tracking-wide">
                        <span>Eddy's English Jember</span>
                        <span>+62 813-3671-2345</span>
                        <span>Perum Puri Bunga Nirwana D18 Kelapa Gading</span>
                    </div>
                </div>
            @else
                {{-- MODE C: GENERATED LANDSCAPE FALLBACK --}}
                <div class="relative z-10 flex flex-col justify-between h-full p-8 sm:p-12 text-center">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <span class="text-xs font-black tracking-[0.25em] text-[#0D3B66] uppercase">EDDY'S GREAT COMMUNICATORS · CEFR {{ $cefr }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-serif font-black tracking-wider text-[#0D3B66] uppercase">{{ $template->title }}</h1>
                    <div class="my-auto py-2">
                        <p class="text-xs italic font-serif text-slate-600 mb-1">This is proudly presented to</p>
                        <h2 class="text-2xl sm:text-4xl font-serif font-bold text-[#0D3B66] border-b-2 border-[#D4AF37]/60 inline-block px-8 py-1 mb-2">{{ $studentName }}</h2>
                        <p class="text-xs text-slate-700 max-w-xl mx-auto">{{ $template->description ?? 'For successfully demonstrating proficiency in CEFR ' . $cefr . '.' }}</p>
                    </div>
                    <div class="flex items-end justify-between pt-4 border-t border-slate-200 text-xs">
                        <div class="text-left font-mono text-[10px]">
                            <p>ID: {{ $certificateNumber }}</p>
                            <p>Date: {{ $issuedDate }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-serif font-bold text-xs text-[#0D3B66]">{{ $template->instructor_name ?? 'KHOIRUL ANWAR, M.Pd' }}</p>
                            <p class="text-[10px] text-slate-500">{{ $template->instructor_title ?? 'Director of Studies' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Script Unduh Gambar: Dual-Engine (Native Canvas 2D + html2canvas Fallback) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadCertificateImage() {
            const btn = document.getElementById('btn-download-image');
            const label = document.getElementById('btn-download-label');
            const originalText = label ? label.textContent : 'Unduh Gambar';
            if (label) label.textContent = 'Memproses...';
            btn.disabled = true;

            const filename = 'sertifikat-{{ strtolower($cefr) }}-{{ \Illuminate\Support\Str::slug($studentName) }}.png';

            // Engine 1: Native Canvas 2D (Instant, 100% crisp, zero CORS issues with same-origin image)
            try {
                const bgImg = document.getElementById('cert-bg-img');
                if (bgImg && (bgImg.complete || bgImg.naturalWidth > 0)) {
                    const canvas = document.createElement('canvas');
                    const W = bgImg.naturalWidth || 1600;
                    const H = bgImg.naturalHeight || Math.round(W * 1.414);
                    canvas.width = W;
                    canvas.height = H;
                    const ctx = canvas.getContext('2d');

                    // 1. Draw Background Template
                    ctx.drawImage(bgImg, 0, 0, W, H);

                    // 2. Draw Student Name
                    const nameX = {{ $template->name_x_percent ?? 50 }} / 100 * W;
                    const nameY = {{ $template->name_y_percent ?? 34 }} / 100 * H;
                    const nameSize = Math.round({{ $template->name_font_size ?? 26 }} * (W / 620) * 0.95);
                    ctx.font = `bold ${nameSize}px "Times New Roman", Georgia, serif`;
                    ctx.fillStyle = '#0D3B66';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(@json($studentName), nameX, nameY);

                    // 3. Draw Scores Box
                    const scX = {{ $template->scores_x_percent ?? 50 }} / 100 * W;
                    const scY = {{ $template->scores_y_percent ?? 52 }} / 100 * H;
                    const boxW = Math.round(250 * (W / 620));
                    const boxH = Math.round(145 * (W / 620));
                    const boxL = scX - (boxW / 2);
                    const boxT = scY - (boxH / 2);

                    ctx.fillStyle = 'rgba(255, 255, 255, 0.94)';
                    ctx.strokeStyle = '#cbd5e1';
                    ctx.lineWidth = Math.max(1, Math.round(1 * (W / 620)));
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(boxL, boxT, boxW, boxH, Math.round(6 * (W / 620)));
                    } else {
                        ctx.rect(boxL, boxT, boxW, boxH);
                    }
                    ctx.fill();
                    ctx.stroke();

                    // Scores items
                    const scFont = Math.round({{ $template->scores_font_size ?? 10 }} * (W / 620) * 1.05);
                    ctx.font = `bold ${scFont}px ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace`;
                    ctx.fillStyle = '#0f172a';
                    const padX = Math.round(10 * (W / 620));
                    const lineH = Math.round(18 * (W / 620));
                    const padY = Math.round(8 * (W / 620));

                    const scoresList = [
                        ['STRUCTURE & VOCABULARY', ': {{ $score > 88 ? $score - 1 : $score + 1 }}'],
                        ['READING COMPREHENSION', ': {{ $score }}'],
                        ['LISTENING COMPREHENSION', ': {{ min(100, $score + 1) }}'],
                        ['DICTATION', ': {{ min(100, $score >= 88 ? $score + 2 : $score - 1) }}'],
                        ['CONVERSATION', ': {{ $score >= 85 ? $score + 1 : $score }}'],
                        ['COMPOSITION', ': {{ max(60, $score - 1) }}']
                    ];

                    scoresList.forEach((item, idx) => {
                        const rowY = boxT + padY + (idx * lineH) + (scFont * 0.8);
                        ctx.textAlign = 'left';
                        ctx.fillText(item[0], boxL + padX, rowY);
                        ctx.textAlign = 'right';
                        ctx.fillText(item[1], boxL + boxW - padX, rowY);
                    });

                    // Pass Standard Scale pill
                    const pillW = Math.round(230 * (W / 620));
                    const pillH = Math.round(18 * (W / 620));
                    const pillL = scX - (pillW / 2);
                    const pillT = boxT + boxH + Math.round(4 * (W / 620));
                    ctx.fillStyle = 'rgba(252, 251, 247, 0.95)';
                    ctx.strokeStyle = '#475569';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(pillL, pillT, pillW, pillH, Math.round(8 * (W / 620)));
                    } else {
                        ctx.rect(pillL, pillT, pillW, pillH);
                    }
                    ctx.fill();
                    ctx.stroke();

                    ctx.font = `bold ${Math.round(7.5 * (W / 620))}px ui-monospace, monospace`;
                    ctx.fillStyle = '#1e293b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('89-100 = EXCELLENT · 76-88 = GOOD · 60-75 = FAIR', scX, pillT + (pillH / 2));

                    // 4. Date
                    const dtX = {{ $template->date_x_percent ?? 18 }} / 100 * W;
                    const dtY = {{ $template->date_y_percent ?? 80 }} / 100 * H;
                    const dtFont = Math.round({{ $template->date_font_size ?? 8 }} * (W / 620) * 1.25);
                    ctx.font = `bold ${dtFont}px ui-monospace, monospace`;
                    ctx.fillStyle = '#1e293b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(@json($issuedDate), dtX, dtY);

                    // 5. Place of Entry
                    const plX = {{ $template->place_x_percent ?? 50 }} / 100 * W;
                    const plY = {{ $template->place_y_percent ?? 80 }} / 100 * H;
                    const plFont = Math.round({{ $template->place_font_size ?? 8 }} * (W / 620) * 1.2);
                    ctx.font = `bold ${plFont}px sans-serif`;
                    ctx.fillStyle = '#1e293b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(@json($template->place_of_entry ?? 'PBN Kelapa Gading, D.18 Jember East Java'), plX, plY);

                    // 6. Reference No.
                    const rfX = {{ $template->ref_no_x_percent ?? 82 }} / 100 * W;
                    const rfY = {{ $template->ref_no_y_percent ?? 80 }} / 100 * H;
                    const rfFont = Math.round({{ $template->ref_no_font_size ?? 8 }} * (W / 620) * 1.3);
                    ctx.font = `bold ${rfFont}px ui-monospace, monospace`;
                    ctx.fillStyle = '#0f172a';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(@json($certificateNumber), rfX, rfY);

                    // Download generated canvas
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = canvas.toDataURL('image/png', 0.95);
                    link.click();

                    if (label) label.textContent = originalText;
                    btn.disabled = false;
                    return;
                }
            } catch (e) {
                console.warn('Native canvas error, falling back to html2canvas:', e);
            }

            // Engine 2: html2canvas fallback
            const certElement = document.getElementById('printable-certificate');
            if (!certElement) {
                if (label) label.textContent = originalText;
                btn.disabled = false;
                return;
            }

            if (typeof html2canvas === 'undefined') {
                window.print();
                if (label) label.textContent = originalText;
                btn.disabled = false;
                return;
            }

            html2canvas(certElement, {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#FCFBF7'
            }).then(function(canvas) {
                const link = document.createElement('a');
                link.download = filename;
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(function(err) {
                console.error('Download error:', err);
                window.print();
            }).finally(function() {
                if (label) label.textContent = originalText;
                btn.disabled = false;
            });
        }
    </script>

    {{-- Print CSS Rules --}}
    <style>
        #printable-certificate {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
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
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: {{ $isSquare ? '210mm 210mm' : ($isPortrait ? 'A4 portrait' : 'A4 landscape') }};
                margin: 0;
            }
        }
    </style>
@endsection
