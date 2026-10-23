@props(['module', 'subModules'])

<x-admin.modal id="aiGenerateModal" title="✨ AI Generate Materi Pembelajaran" size="sm:max-w-lg">
    <form id="aiGenerateForm" method="POST" action="{{ route('admin.modules.ai_generate', $module->id) }}" onsubmit="handleAiGenerateSubmit(this)">
        @csrf
        <input type="hidden" name="sub_module_id" id="aiGenSubModuleId" value="">

        <div class="space-y-4">
            <div class="p-3.5 rounded-xl border border-secondary/20 bg-amber-500/5 dark:bg-amber-500/10 flex items-start gap-3">
                <div class="size-8 rounded-lg bg-secondary text-white flex items-center justify-center shrink-0 shadow-xs font-bold text-sm">
                    ✨
                </div>
                <div class="text-xs text-slate-600 dark:text-neutral-300 leading-relaxed">
                    AI Curriculum Generator akan menyusun struktur materi siap pakai (Teori, Kosakata, Percakapan & Kuis) berstandar <strong>CEFR {{ $module->cefr }}</strong> untuk modul <strong>{{ $module->title }}</strong>.
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                    Cakupan Pembuatan Materi <span class="text-rose-500">*</span>
                </label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:border-secondary cursor-pointer transition-colors">
                        <input type="radio" name="scope" value="single" id="aiScopeSingle" checked class="text-secondary focus:ring-secondary size-4">
                        <div class="text-xs">
                            <span class="font-bold text-primary dark:text-white block" id="aiSingleScopeLabel">Hanya untuk Sub-Modul yang Dipilih</span>
                            <span class="text-slate-400 text-[11px]" id="aiSingleScopeDesc">Menambahkan 2-3 materi spesifik pada skill ini</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:border-secondary cursor-pointer transition-colors">
                        <input type="radio" name="scope" value="all" class="text-secondary focus:ring-secondary size-4">
                        <div class="text-xs">
                            <span class="font-bold text-primary dark:text-white block">Seluruh 5 Sub-Modul Sekaligus</span>
                            <span class="text-slate-400 text-[11px]">Mengisi Reading, Listening, Writing, Speaking, dan Assessment</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label for="aiCustomInstructions" class="block text-xs font-semibold text-slate-700 dark:text-neutral-300 mb-1.5">
                    Instruksi Tambahan / Topik Khusus (Opsional)
                </label>
                <textarea id="aiCustomInstructions" name="custom_instructions" rows="2"
                    class="w-full text-xs rounded-[10px] border-slate-300 bg-white py-2 px-3 shadow-[inset_0_1px_2px_rgba(0,0,0,0.04)] dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:border-secondary focus:ring-1 focus:ring-secondary"
                    placeholder="Contoh: Fokus pada kosakata perhotelan, atau buat pertanyaan kuis dengan tingkat kesulitan bertahap..."></textarea>
            </div>

            <div class="flex justify-end items-center gap-2 pt-3 border-t border-slate-200 dark:border-neutral-700">
                <x-admin.button type="button" size="sm" variant="outline" data-hs-overlay="#aiGenerateModal">
                    Batal
                </x-admin.button>
                <x-admin.button type="submit" size="sm" variant="secondary" id="aiGenerateSubmitBtn">
                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    <span>Mulai Generate Materi</span>
                </x-admin.button>
            </div>
        </div>
    </form>
</x-admin.modal>
