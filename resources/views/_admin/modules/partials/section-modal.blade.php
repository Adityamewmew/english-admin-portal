@props(['module', 'subModules'])

<x-admin.modal id="sectionModal" title="Kelola Bagian Materi" size="sm:max-w-3xl">
    <form id="sectionForm" method="POST" action="" navigate-form>
        @csrf
        <input type="hidden" name="section_id" id="modalSectionId" value="">

        <div class="space-y-5">
            {{-- Base Meta Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1">
                        Sub-Modul (Skill) <span class="text-rose-500">*</span>
                    </label>
                    <select name="sub_module_id" id="modalSubModuleId" class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20" required>
                        @foreach ($subModules as $sm)
                            <option value="{{ $sm->id }}">
                                {{ $sm->title }} ({{ strtoupper($sm->skill_type) }})
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
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all section-type-option border-primary bg-surface dark:bg-neutral-800 card-tactile" data-type="theory">
                        <input type="radio" name="section_type" value="theory" class="sr-only" checked onchange="handleSectionTypeChange('theory')">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="size-2 rounded-full bg-primary"></span>
                            <span class="text-xs font-heading font-extrabold text-primary dark:text-white">Teori & Aturan</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-neutral-400">Ringkasan, poin aturan, common trap</span>
                    </label>

                    <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="vocab">
                        <input type="radio" name="section_type" value="vocab" class="sr-only" onchange="handleSectionTypeChange('vocab')">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="size-2 rounded-full bg-emerald-600"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Kosakata (Vocab)</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-neutral-400">Daftar kata, arti, fonetik, contoh</span>
                    </label>

                    <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="dialogue">
                        <input type="radio" name="section_type" value="dialogue" class="sr-only" onchange="handleSectionTypeChange('dialogue')">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="size-2 rounded-full bg-secondary"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Percakapan / Teks</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-neutral-400">Dialog berpasangan atau teks bacaan</span>
                    </label>

                    <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all section-type-option border-slate-200 hover:border-primary/50 dark:border-neutral-700 card-tactile" data-type="quiz">
                        <input type="radio" name="section_type" value="quiz" class="sr-only" onchange="handleSectionTypeChange('quiz')">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="size-2 rounded-full bg-rose-600"></span>
                            <span class="text-xs font-heading font-extrabold text-slate-800 dark:text-white">Kuis Pilihan Ganda</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-neutral-400">Soal latihan, pilihan A-D, kunci</span>
                    </label>
                </div>
            </div>

            {{-- 1. SUBFORM: THEORY --}}
            <div id="subformTheory" class="section-subform space-y-4">
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
                <div class="p-4 rounded-xl bg-surface border border-secondary/30 dark:bg-neutral-900/40 dark:border-secondary/20 space-y-3">
                    <span class="text-xs font-heading font-extrabold text-primary dark:text-accent block">
                        Peringatan Kesalahan Umum (Common Trap) - Opsional
                    </span>
                    <input type="text" name="theory_trap_title" id="theoryTrapTitle"
                        class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                        placeholder="Judul: misal 'Common Beginner Mistake'">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" name="theory_trap_wrong" id="theoryTrapWrong"
                            class="w-full text-xs rounded-[10px] border-rose-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-rose-700 font-medium"
                            placeholder="Contoh Kalimat Kurang Tepat">
                        <input type="text" name="theory_trap_correct" id="theoryTrapCorrect"
                            class="w-full text-xs rounded-[10px] border-emerald-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-emerald-700 font-medium"
                            placeholder="Contoh Kalimat Disarankan">
                    </div>
                    <textarea name="theory_trap_explanation" id="theoryTrapExplanation" rows="2"
                        class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                        placeholder="Penjelasan mengapa hal tersebut salah dan bagaimana yang benar..."></textarea>
                </div>
            </div>

            {{-- 2. SUBFORM: VOCABULARY --}}
            <div id="subformVocab" class="section-subform space-y-3 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-heading font-extrabold text-primary dark:text-white block">Daftar Kata Kosakata</span>
                        <span class="text-[11px] text-slate-500">Masukkan kata, arti, fonetik, dan contoh kalimat</span>
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
                        Konteks Situasi (Opsional)
                    </label>
                    <input type="text" name="dialogue_context" id="dialogueContext"
                        class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20"
                        placeholder="Contoh: Two university students meeting at the library for the first time">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div>
                        <span class="text-xs font-heading font-extrabold text-primary dark:text-white block">Baris Dialog Percakapan</span>
                        <span class="text-[11px] text-slate-500">Tentukan siapa yang berbicara dan kalimatnya</span>
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

            {{-- 4. SUBFORM: QUIZ --}}
            <div id="subformQuiz" class="section-subform space-y-3 hidden">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-surface dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700">
                    <span class="text-xs font-heading font-extrabold text-primary dark:text-neutral-300">Passing Score (%)</span>
                    <input type="number" name="quiz_passing_score" id="quizPassingScore" value="70" min="0" max="100"
                        class="w-20 text-xs rounded-[8px] border-slate-300 bg-white py-1 px-2 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-center font-bold">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div>
                        <span class="text-xs font-heading font-extrabold text-primary dark:text-white block">Daftar Pertanyaan Kuis</span>
                        <span class="text-[11px] text-slate-500">Pilihan ganda dengan kunci jawaban yang jelas</span>
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
