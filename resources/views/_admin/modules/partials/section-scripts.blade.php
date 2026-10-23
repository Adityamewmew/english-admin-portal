<script>
    let vocabIndex = 0;
    let dialogueIndex = 0;
    let quizIndex = 0;

    function handleSectionTypeChange(type) {
        document.querySelectorAll('.section-type-option').forEach(el => {
            if (el.dataset.type === type) {
                el.classList.add('border-primary', 'bg-surface', 'dark:bg-neutral-800');
                el.classList.remove('border-slate-200', 'dark:border-neutral-700');
                el.querySelector('input').checked = true;
            } else {
                el.classList.remove('border-primary', 'bg-surface', 'dark:bg-neutral-800');
                el.classList.add('border-slate-200', 'dark:border-neutral-700');
            }
        });

        document.querySelectorAll('.section-subform').forEach(el => el.classList.add('hidden'));

        if (type === 'theory') document.getElementById('subformTheory').classList.remove('hidden');
        else if (type === 'vocab') document.getElementById('subformVocab').classList.remove('hidden');
        else if (type === 'dialogue') document.getElementById('subformDialogue').classList.remove('hidden');
        else if (type === 'quiz' || type === 'practice') document.getElementById('subformQuiz').classList.remove('hidden');
    }

    function addTheoryRuleRow(val = '') {
        const container = document.getElementById('theoryRulesContainer');
        const rowId = 'rule-row-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="theory_rules[]" value="${escapeHtml(val)}"
                class="w-full text-xs rounded-xl border-gray-200 bg-white py-2 px-3 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                placeholder="Tuliskan poin aturan atau rumus, misal: 'Gunakan am/is/are dengan subjek...' ">
            <button type="button" onclick="document.getElementById('${rowId}').remove()"
                class="size-8 rounded-xl border border-gray-200 bg-white hover:bg-red-50 text-gray-500 hover:text-red-600 flex items-center justify-center shrink-0 dark:bg-neutral-800 dark:border-neutral-700 cursor-pointer">
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        `;
        container.appendChild(div);
    }

    function addVocabRow(data = {}) {
        const container = document.getElementById('vocabRowsContainer');
        const idx = vocabIndex++;
        const rowId = 'vocab-row-' + idx;
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'p-3.5 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700 space-y-2 text-xs';
        div.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-gray-200/60 dark:border-neutral-700">
                <span class="font-bold text-gray-700 dark:text-neutral-300">Kata #${idx + 1}</span>
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-red-500 hover:text-red-700 font-semibold cursor-pointer">
                    Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <input type="text" name="vocab_words[${idx}][word]" value="${escapeHtml(data.word || '')}"
                    class="rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold"
                    placeholder="Kata (e.g. Greeting)">
                <input type="text" name="vocab_words[${idx}][phonetic]" value="${escapeHtml(data.phonetic || '')}"
                    class="rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-mono"
                    placeholder="Fonetik (e.g. /ˈɡriːtɪŋ/)">
                <input type="text" name="vocab_words[${idx}][partOfSpeech]" value="${escapeHtml(data.partOfSpeech || '')}"
                    class="rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                    placeholder="Kelas (noun, verb, etc.)">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <input type="text" name="vocab_words[${idx}][meaningId]" value="${escapeHtml(data.meaningId || data.meaning || '')}"
                    class="rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                    placeholder="Arti Bahasa Indonesia">
                <input type="text" name="vocab_words[${idx}][exampleSentence]" value="${escapeHtml(data.exampleSentence || data.example || '')}"
                    class="rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white italic"
                    placeholder="Contoh kalimat">
            </div>
        `;
        container.appendChild(div);
    }

    function addDialogueRow(data = {}) {
        const container = document.getElementById('dialogueRowsContainer');
        const idx = dialogueIndex++;
        const rowId = 'dialogue-row-' + idx;
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'p-3 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700 space-y-2 text-xs';
        div.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-gray-200/60 dark:border-neutral-700">
                <span class="font-bold text-gray-700 dark:text-neutral-300">Baris #${idx + 1}</span>
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-red-500 hover:text-red-700 font-semibold cursor-pointer">
                    Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                <div class="sm:col-span-1">
                    <input type="text" name="dialogue_lines[${idx}][speaker]" value="${escapeHtml(data.speaker || 'Speaker')}"
                        class="w-full rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold"
                        placeholder="Pembicara">
                </div>
                <div class="sm:col-span-3">
                    <input type="text" name="dialogue_lines[${idx}][text]" value="${escapeHtml(data.text || '')}"
                        class="w-full rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                        placeholder="Kalimat Bahasa Inggris">
                </div>
            </div>
            <input type="text" name="dialogue_lines[${idx}][translation]" value="${escapeHtml(data.translation || '')}"
                class="w-full rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-gray-500"
                placeholder="Terjemahan Bahasa Indonesia (Opsional)">
        `;
        container.appendChild(div);
    }

    function addQuizQuestionRow(data = {}) {
        const container = document.getElementById('quizQuestionsContainer');
        const idx = quizIndex++;
        const rowId = 'quiz-row-' + idx;
        const options = data.options || ['', '', '', ''];
        const currentAnswer = data.answer || options[0] || '';

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-200 dark:border-neutral-700 space-y-2.5 text-xs';
        div.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-gray-200/60 dark:border-neutral-700">
                <span class="font-bold text-blue-600 dark:text-blue-400">Soal #${idx + 1}</span>
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-red-500 hover:text-red-700 font-semibold cursor-pointer">
                    Hapus Soal
                </button>
            </div>
            <div>
                <label class="block font-semibold text-gray-700 dark:text-neutral-300 mb-1">Pertanyaan Soal</label>
                <input type="text" name="quiz_questions[${idx}][question]" value="${escapeHtml(data.question || '')}"
                    class="w-full rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-medium"
                    placeholder="Misal: What is the correct response to 'How are you?'">
            </div>
            <div class="space-y-1.5">
                <label class="block font-semibold text-gray-700 dark:text-neutral-300">Pilihan Jawaban (Ketik pilihan & tentukan kunci)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    ${[0, 1, 2, 3].map(optIdx => `
                        <input type="text" name="quiz_questions[${idx}][options][]" value="${escapeHtml(options[optIdx] || '')}"
                            class="rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                            placeholder="Pilihan ${String.fromCharCode(65 + optIdx)}">
                    `).join('')}
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-emerald-700 dark:text-emerald-400 mb-1">Kunci Jawaban Tepat</label>
                    <input type="text" name="quiz_questions[${idx}][answer]" value="${escapeHtml(currentAnswer)}"
                        class="w-full rounded-xl border-emerald-300 bg-emerald-50/40 py-1.5 px-2.5 dark:bg-emerald-950/20 dark:border-emerald-700 dark:text-emerald-300 font-bold"
                        placeholder="Ketik teks jawaban yang benar persis">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-neutral-300 mb-1">Pembahasan (Opsional)</label>
                    <input type="text" name="quiz_questions[${idx}][explanation]" value="${escapeHtml(data.explanation || '')}"
                        class="w-full rounded-xl border-gray-200 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-gray-500"
                        placeholder="Alasan mengapa jawaban tersebut tepat">
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    function openModal(modalId) {
        const el = document.getElementById(modalId);
        if (!el) return;
        if (window.HSOverlay && typeof window.HSOverlay.open === 'function') {
            try {
                window.HSOverlay.open(el);
                return;
            } catch (e) {
                console.warn('HSOverlay.open error, using fallback:', e);
            }
        }
        el.classList.remove('hidden');
        el.classList.add('open');
        setTimeout(() => {
            const inner = el.querySelector('.hs-overlay-open\\:opacity-100');
            if (inner) {
                inner.classList.remove('opacity-0');
                inner.classList.add('opacity-100');
            }
        }, 10);
    }

    function openAddSectionModal(subModuleId) {
        const form = document.getElementById('sectionForm');
        form.action = "{{ route('admin.modules.section.create', $module->id) }}";
        document.getElementById('sectionModal-label').innerText = "Tambah Bagian Materi";
        document.getElementById('modalSectionId').value = "";
        document.getElementById('modalSectionTitle').value = "";
        
        if (subModuleId) {
            document.getElementById('modalSubModuleId').value = subModuleId;
        }

        // Reset containers
        document.getElementById('theorySummary').value = "";
        document.getElementById('theoryRulesContainer').innerHTML = "";
        document.getElementById('theoryTrapTitle').value = "";
        document.getElementById('theoryTrapWrong').value = "";
        document.getElementById('theoryTrapCorrect').value = "";
        document.getElementById('theoryTrapExplanation').value = "";

        document.getElementById('vocabRowsContainer').innerHTML = "";
        document.getElementById('dialogueContext').value = "";
        document.getElementById('dialogueRowsContainer').innerHTML = "";
        document.getElementById('quizQuestionsContainer').innerHTML = "";

        // Add 1 default row to each
        addTheoryRuleRow();
        addVocabRow();
        addDialogueRow();
        addQuizQuestionRow();

        handleSectionTypeChange('theory');

        openModal('sectionModal');
    }

    function openEditSectionModalById(id) {
        const tpl = document.getElementById('sec-data-' + id);
        if (!tpl) return;
        try {
            const sec = JSON.parse(tpl.innerHTML);
            openEditSectionModal(sec);
        } catch (e) {
            console.error('Error parsing section data:', e);
        }
    }

    function openEditSectionModal(sec) {
        const form = document.getElementById('sectionForm');
        form.action = "{{ url('admin/modules/detail/' . $module->id . '/section') }}/" + sec.id + "/update";
        document.getElementById('sectionModal-label').innerText = "Edit: " + sec.title;
        document.getElementById('modalSectionId').value = sec.id;
        document.getElementById('modalSectionTitle').value = sec.title;
        
        if (sec.sub_module_id) {
            document.getElementById('modalSubModuleId').value = sec.sub_module_id;
        }

        // Clear subform containers
        document.getElementById('theoryRulesContainer').innerHTML = "";
        document.getElementById('vocabRowsContainer').innerHTML = "";
        document.getElementById('dialogueRowsContainer').innerHTML = "";
        document.getElementById('quizQuestionsContainer').innerHTML = "";

        let c = sec.content;
        if (typeof c === 'string') {
            try { c = JSON.parse(c); } catch (e) { c = {}; }
        }
        c = c || {};

        let type = sec.section_type;
        if (type === 'vocabulary') type = 'vocab';
        if (type === 'practice') type = 'quiz';

        handleSectionTypeChange(type);

        if (type === 'theory') {
            document.getElementById('theorySummary').value = c.summary || '';
            const rules = c.rules || [];
            if (rules.length > 0) {
                rules.forEach(r => addTheoryRuleRow(typeof r === 'object' ? JSON.stringify(r) : r));
            } else {
                addTheoryRuleRow();
            }
            if (c.commonTrap) {
                document.getElementById('theoryTrapTitle').value = c.commonTrap.trapTitle || '';
                document.getElementById('theoryTrapWrong').value = c.commonTrap.wrong || '';
                document.getElementById('theoryTrapCorrect').value = c.commonTrap.correct || '';
                document.getElementById('theoryTrapExplanation').value = c.commonTrap.explanation || '';
            }
        } else if (type === 'vocab') {
            const words = c.items || c.words || [];
            if (words.length > 0) {
                words.forEach(w => addVocabRow(w));
            } else {
                addVocabRow();
            }
        } else if (type === 'dialogue') {
            document.getElementById('dialogueContext').value = c.context || '';
            const lines = c.lines || [];
            if (lines.length > 0) {
                lines.forEach(l => addDialogueRow(l));
            } else {
                addDialogueRow();
            }
        } else if (type === 'quiz') {
            document.getElementById('quizPassingScore').value = c.passingScore || 70;
            const qs = c.questions || [];
            if (qs.length > 0) {
                qs.forEach(q => addQuizQuestionRow(q));
            } else {
                addQuizQuestionRow();
            }
        }

        openModal('sectionModal');
    }

    function confirmDeleteSectionById(id, subModuleId) {
        const card = document.getElementById('section-card-' + id);
        let title = 'Materi';
        if (card) {
            const titleEl = card.querySelector('h3');
            if (titleEl) title = titleEl.innerText.trim();
        }
        confirmDeleteSection(id, title, subModuleId);
    }

    function confirmDeleteSection(id, title, subModuleId) {
        const form = document.getElementById('deleteSectionForm');
        form.action = "{{ url('admin/modules/detail/' . $module->id . '/section') }}/" + id + "/delete";
        document.getElementById('deleteSectionTitle').innerText = title;
        document.getElementById('deleteSectionSubModuleId').value = subModuleId || '';

        openModal('deleteSectionModal');
    }

    function switchSubModuleTab(tabId) {
        document.querySelectorAll('.submodule-tab-btn').forEach(btn => {
            if (btn.dataset.tab === tabId) {
                btn.classList.add('btn-tactile-primary');
                btn.classList.remove('btn-tactile-outline');
            } else {
                btn.classList.remove('btn-tactile-primary');
                btn.classList.add('btn-tactile-outline');
            }
        });

        document.querySelectorAll('.submodule-tab-pane').forEach(pane => {
            if (pane.id === 'pane-' + tabId) {
                pane.classList.remove('hidden');
            } else {
                pane.classList.add('hidden');
            }
        });

        // Update URL query parameter without refresh
        const url = new URL(window.location);
        url.searchParams.set('sub_module', tabId);
        window.history.replaceState({}, '', url);
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function openEditSubModuleModal(subModuleId, title) {
        const form = document.getElementById('editSubModuleForm');
        form.action = "{{ url('admin/modules/detail/' . $module->id . '/submodule') }}/" + subModuleId + "/update";
        document.getElementById('editSubModuleTitleInput').value = title;
        openModal('editSubModuleModal');
    }

    function openAiGenerateModal(subModuleId, title, skillType) {
        document.getElementById('aiGenSubModuleId').value = subModuleId || '';
        const singleLabel = document.getElementById('aiSingleScopeLabel');
        const singleDesc = document.getElementById('aiSingleScopeDesc');
        if (title) {
            singleLabel.innerText = 'Hanya untuk Sub-Modul: ' + title;
            singleDesc.innerText = 'Generate 2-3 materi spesifik untuk skill ' + (skillType || '').toUpperCase();
        } else {
            singleLabel.innerText = 'Hanya untuk Sub-Modul Aktif';
        }
        document.getElementById('aiScopeSingle').checked = true;
        openModal('aiGenerateModal');
    }

    function handleAiGenerateSubmit(form) {
        const btn = document.getElementById('aiGenerateSubmitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 size-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Sedang Menyusun Materi...</span>`;
        }
    }

    // Auto-select tab from URL on page load
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const subModuleParam = urlParams.get('sub_module');
        if (subModuleParam && document.getElementById('pane-' + subModuleParam)) {
            switchSubModuleTab(subModuleParam);
        }
    });
</script>
