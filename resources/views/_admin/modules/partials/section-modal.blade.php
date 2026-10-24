@props(['module', 'subModules'])

<x-admin.modal id="sectionModal" title="Kelola Bagian Materi" size="sm:max-w-4xl">
    <form id="sectionForm" method="POST" action="" navigate-form>
        @csrf
        <input type="hidden" name="section_id" id="modalSectionId" value="">
        <input type="hidden" name="content_mode" id="modalContentMode" value="visual">

        <div class="space-y-4">
            {{-- Context Awareness Banner: Shows exactly where this section belongs --}}
            <div id="modalContextBanner" class="p-3.5 rounded-xl bg-primary/5 dark:bg-primary/10 border border-primary/20 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <span class="size-6 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-[11px]">
                        📍
                    </span>
                    <div>
                        <span class="text-slate-500 dark:text-neutral-400 block text-[11px]">Sub-Modul yang Sedang Diedit:</span>
                        <span id="contextSubModuleTitle" class="font-heading font-extrabold text-primary dark:text-white text-xs">
                            Pilih Sub-Modul
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span id="contextSkillBadge" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-primary text-white tracking-wide uppercase">
                        ALL
                    </span>
                    <span id="contextOrderBadge" class="text-[11px] text-slate-500 dark:text-neutral-400 font-mono">
                        Bagian #1
                    </span>
                </div>
            </div>

            {{-- Base Meta Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">
                        Sub-Modul (Skill) <span class="text-rose-500">*</span>
                    </label>
                    <select name="sub_module_id" id="modalSubModuleId" class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20" onchange="syncModalContextFromSelect()" required>
                        @foreach ($subModules as $sm)
                            <option value="{{ $sm->id }}" data-skill="{{ strtolower($sm->skill_type) }}" data-title="{{ $sm->title }}" data-order="{{ $sm->order_index }}">
                                #{{ $sm->order_index }} {{ $sm->title }} ({{ strtoupper($sm->skill_type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">
                        Judul Bagian Materi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="modalSectionTitle"
                        class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                        placeholder="Contoh: Vocabulary in Context atau Sentence Building" required>
                </div>
            </div>

            {{-- Section Type Selector --}}
            <div>
                <label class="block text-xs font-heading font-extrabold text-primary/80 dark:text-neutral-300 mb-2 uppercase tracking-wide">
                    Tipe Materi <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    <label class="relative flex flex-col p-2.5 rounded-xl border cursor-pointer transition-all section-type-option border-primary bg-surface dark:bg-neutral-800 card-tactile" data-type="theory">
                        <input type="radio" name="section_type" value="theory" class="sr-only" checked onchange="handleSectionTypeChange('theory')">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="size-2 rounded-full bg-primary"></span>
                            <span class="text-xs font-heading font-extrabold text-primary dark:text-white">Teori</span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">Ringkasan, rumus, tips</span>
                    </label>

                    <label class="relative flex flex-col p-2.5 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="vocab">
                        <input type="radio" name="section_type" value="vocab" class="sr-only" onchange="handleSectionTypeChange('vocab')">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="size-2 rounded-full bg-emerald-600"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Kosakata</span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">Daftar kata, arti, IPA</span>
                    </label>

                    <label class="relative flex flex-col p-2.5 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="dialogue">
                        <input type="radio" name="section_type" value="dialogue" class="sr-only" onchange="handleSectionTypeChange('dialogue')">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="size-2 rounded-full bg-secondary"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Dialog / Teks</span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">Percakapan / passage</span>
                    </label>

                    <label class="relative flex flex-col p-2.5 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="practice">
                        <input type="radio" name="section_type" value="practice" class="sr-only" onchange="handleSectionTypeChange('practice')">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="size-2 rounded-full bg-amber-600"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Praktik Lab</span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">Kabel, builder, drill</span>
                    </label>

                    <label class="relative flex flex-col p-2.5 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="quiz">
                        <input type="radio" name="section_type" value="quiz" class="sr-only" onchange="handleSectionTypeChange('quiz')">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="size-2 rounded-full bg-rose-600"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Kuis / Evaluasi</span>
                        </div>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">Soal tes & ujian</span>
                    </label>
                </div>
            </div>

            {{-- Mode Switcher: Form Cepat vs Mode JSON Terstruktur --}}
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-neutral-700 pb-2 pt-1">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="switchEditMode('visual')" id="tabModeVisual"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-primary text-white cursor-pointer shadow-xs">
                        📝 Form Visual Cepat
                    </button>
                    <button type="button" onclick="switchEditMode('json')" id="tabModeJson"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-neutral-800 dark:text-neutral-300 cursor-pointer">
                        ⚡ Mode JSON Terstruktur (Aman untuk 55 Soal / Drills)
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnFormatJson" onclick="formatContentJson()" class="text-[11px] text-secondary font-bold hover:underline hidden cursor-pointer">
                        Format Rapih JSON
                    </button>
                </div>
            </div>

            {{-- VISUAL MODE CONTAINER --}}
            <div id="visualModeContainer" class="space-y-4">
                {{-- 1. SUBFORM: THEORY --}}
                <div id="subformTheory" class="section-subform space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">
                            Ringkasan Materi (Summary)
                        </label>
                        <textarea name="theory_summary" id="theorySummary" rows="3"
                            class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 leading-relaxed"
                            placeholder="Tuliskan intisari materi atau panduan yang perlu dipelajari siswa..."></textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-heading font-extrabold text-primary/80 dark:text-neutral-300">
                                Poin Aturan / Tips Tata Bahasa (Rules)
                            </label>
                            <button type="button" onclick="addTheoryRuleRow()"
                                class="text-xs text-secondary font-bold hover:underline flex items-center gap-1 cursor-pointer">
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                + Tambah Poin Aturan
                            </button>
                        </div>
                        <div id="theoryRulesContainer" class="space-y-2">
                            {{-- Injected dynamically --}}
                        </div>
                    </div>

                    {{-- Common Trap Box --}}
                    <div class="p-3.5 rounded-xl bg-surface border border-secondary/30 dark:bg-neutral-900/40 dark:border-secondary/20 space-y-2.5">
                        <span class="text-xs font-heading font-extrabold text-primary dark:text-accent block">
                            Peringatan Kesalahan Umum (Common Trap) - Opsional
                        </span>
                        <input type="text" name="theory_trap_title" id="theoryTrapTitle"
                            class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-1.5 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                            placeholder="Judul: misal 'Common Learner Pitfall'">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="theory_trap_wrong" id="theoryTrapWrong"
                                class="w-full text-xs rounded-[10px] border-rose-300 bg-white py-1.5 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-rose-700 font-medium"
                                placeholder="Contoh Kalimat Kurang Tepat">
                            <input type="text" name="theory_trap_correct" id="theoryTrapCorrect"
                                class="w-full text-xs rounded-[10px] border-emerald-300 bg-white py-1.5 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-emerald-700 font-medium"
                                placeholder="Contoh Kalimat Disarankan">
                        </div>
                        <textarea name="theory_trap_explanation" id="theoryTrapExplanation" rows="2"
                            class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-1.5 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                            placeholder="Penjelasan mengapa hal tersebut salah dan bagaimana yang benar..."></textarea>
                    </div>
                </div>

                {{-- 2. SUBFORM: VOCABULARY --}}
                <div id="subformVocab" class="section-subform space-y-3 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-heading font-extrabold text-primary dark:text-white block">Daftar Kata Kosakata</span>
                            <span class="text-[11px] text-slate-500">Masukkan kata, arti (ID), fonetik IPA, dan contoh kalimat</span>
                        </div>
                        <button type="button" onclick="addVocabRow()"
                            class="btn-tactile btn-tactile-outline !py-1.5 !px-3 !rounded-xl !text-xs !font-bold flex items-center gap-1 cursor-pointer">
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            + Tambah Kata
                        </button>
                    </div>
                    <div id="vocabRowsContainer" class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        {{-- Injected dynamically --}}
                    </div>
                </div>

                {{-- 3. SUBFORM: DIALOGUE --}}
                <div id="subformDialogue" class="section-subform space-y-3 hidden">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">
                            Konteks Situasi (Context)
                        </label>
                        <input type="text" name="dialogue_context" id="dialogueContext"
                            class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                            placeholder="Contoh: Two friends meeting at a local cafe to discuss weekend plans">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <span class="text-xs font-heading font-extrabold text-primary dark:text-white block">Baris Percakapan / Kalimat</span>
                            <span class="text-[11px] text-slate-500">Tentukan pembicara, kalimat bahasa Inggris (3-8 kata), dan arti Indonesia</span>
                        </div>
                        <button type="button" onclick="addDialogueRow()"
                            class="btn-tactile btn-tactile-outline !py-1.5 !px-3 !rounded-xl !text-xs !font-bold flex items-center gap-1 cursor-pointer">
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            + Tambah Baris Dialog
                        </button>
                    </div>
                    <div id="dialogueRowsContainer" class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        {{-- Injected dynamically --}}
                    </div>
                </div>

                {{-- 4. SUBFORM: PRACTICE LAB --}}
                <div id="subformPractice" class="section-subform space-y-3 hidden">
                    <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200 dark:bg-amber-950/20 dark:border-amber-800 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-amber-600"></span>
                            <span class="text-xs font-heading font-extrabold text-amber-900 dark:text-amber-300">Bagian Latihan Interaktif (Practice Lab)</span>
                        </div>
                        <p class="text-xs text-amber-800/90 dark:text-amber-300/80 leading-relaxed">
                            Bagian ini memuat format latihan khusus seperti <strong>Kabel Sambung Kosakata</strong> (Reading), <strong>Sentence Builder & Mechanics Challenges</strong> (Writing), atau <strong>Syllable Stress & Roleplay Drill</strong> (Speaking).
                        </p>
                        <div class="pt-1">
                            <button type="button" onclick="switchEditMode('json')" class="btn-tactile btn-tactile-secondary !py-1.5 !px-3 !rounded-xl !text-xs !font-bold flex items-center gap-1.5 cursor-pointer">
                                ⚡ Buka di Mode JSON Terstruktur untuk Edit Latihan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 5. SUBFORM: QUIZ --}}
                <div id="subformQuiz" class="section-subform space-y-3 hidden">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700">
                        <div>
                            <span class="text-xs font-heading font-extrabold text-primary dark:text-neutral-300 block">Passing Score (%)</span>
                            <span class="text-[11px] text-slate-500">Batas nilai kelulusan kuis atau ujian</span>
                        </div>
                        <input type="number" name="quiz_passing_score" id="quizPassingScore" value="70" min="0" max="100"
                            class="w-20 text-xs rounded-[8px] border-slate-300 bg-white py-1 px-2 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-center font-bold">
                    </div>

                    {{-- Notice for Practicum & 55-Item Assessment --}}
                    <div id="quizComplexNotice" class="p-3 rounded-xl bg-blue-50/70 border border-blue-200 dark:bg-blue-950/20 dark:border-blue-800 text-xs text-blue-900 dark:text-blue-300 flex items-center justify-between gap-2 hidden">
                        <div>
                            <span class="font-bold block">✨ Format Soal Khusus Terdeteksi</span>
                            <span id="quizComplexDesc" class="text-[11px] text-blue-700 dark:text-blue-400">Bagian ini memuat soal audio dictation, arrange, atau 55 butir evaluasi.</span>
                        </div>
                        <button type="button" onclick="switchEditMode('json')" class="shrink-0 px-2.5 py-1 rounded-lg bg-blue-600 text-white font-bold text-[11px] hover:bg-blue-700 cursor-pointer">
                            Edit di Mode JSON
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <span class="text-xs font-heading font-extrabold text-primary dark:text-white block">Daftar Pertanyaan Pilihan Ganda</span>
                            <span class="text-[11px] text-slate-500">Soal multiple choice standar (A-D)</span>
                        </div>
                        <button type="button" onclick="addQuizQuestionRow()"
                            class="btn-tactile btn-tactile-outline !py-1.5 !px-3 !rounded-xl !text-xs !font-bold flex items-center gap-1 cursor-pointer">
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            + Tambah Soal
                        </button>
                    </div>
                    <div id="quizQuestionsContainer" class="space-y-4 max-h-96 overflow-y-auto pr-1">
                        {{-- Injected dynamically --}}
                    </div>
                </div>
            </div>

            {{-- JSON MODE CONTAINER (Full fidelity, safe for 55 questions, dictation, etc.) --}}
            <div id="jsonModeContainer" class="space-y-2 hidden">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700 dark:text-neutral-300">Editor JSON Konten Section:</span>
                    <span id="jsonValidationStatus" class="text-[11px] text-emerald-600 font-semibold">✓ Format JSON Valid</span>
                </div>
                <textarea name="content_json" id="modalContentJson" rows="14"
                    class="w-full text-xs font-mono rounded-[10px] border-slate-300 bg-slate-900 text-emerald-400 p-3 shadow-inner focus:border-primary focus:ring-2 focus:ring-primary/20 leading-relaxed"
                    placeholder="{ ... }" oninput="validateJsonInput()"></textarea>
            </div>

            {{-- Modal Footer --}}
            <div class="flex justify-end items-center gap-2 pt-3 border-t border-slate-200 dark:border-neutral-700">
                <x-admin.button type="button"
                    size="sm"
                    variant="outline"
                    data-hs-overlay="#sectionModal">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit" id="modalSubmitBtn" variant="secondary" size="sm">
                    Simpan Materi
                </x-admin.button>
            </div>
        </div>
    </form>
</x-admin.modal>
