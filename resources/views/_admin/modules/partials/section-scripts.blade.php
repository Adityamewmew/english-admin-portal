<script>
    let vocabIndex = 0;
    let dialogueIndex = 0;
    let quizIndex = 0;
    let cablePairIndex = 0;
    let currentActiveSectionContent = {};
    let currentQuizFormat = 'mcq';

    function syncModalContextFromSelect() {
        const sel = document.getElementById('modalSubModuleId');
        if (!sel) return;
        const opt = sel.options[sel.selectedIndex];
        if (!opt) return;

        const title = opt.dataset.title || opt.text || 'Sub-Modul';
        const skill = (opt.dataset.skill || 'SKILL').toUpperCase();
        const order = opt.dataset.order ? ('#' + opt.dataset.order) : '';

        const titleEl = document.getElementById('contextSubModuleTitle');
        const skillEl = document.getElementById('contextSkillBadge');
        const orderEl = document.getElementById('contextOrderBadge');

        if (titleEl) titleEl.innerText = title;
        if (skillEl) skillEl.innerText = skill;
        if (orderEl) orderEl.innerText = 'Sub-Modul ' + order;
    }

    function switchEditMode(mode) {
        const visualCont = document.getElementById('visualModeContainer');
        const jsonCont = document.getElementById('jsonModeContainer');
        const tabVisual = document.getElementById('tabModeVisual');
        const tabJson = document.getElementById('tabModeJson');
        const btnFormat = document.getElementById('btnFormatJson');
        const modeInput = document.getElementById('modalContentMode');
        const type = document.querySelector('input[name="section_type"]:checked')?.value || 'theory';

        if (mode === 'json') {
            serializeVisualToContent(type);
            visualCont?.classList.add('hidden');
            jsonCont?.classList.remove('hidden');
            btnFormat?.classList.remove('hidden');

            tabVisual?.classList.replace('bg-primary', 'bg-slate-100');
            tabVisual?.classList.replace('text-white', 'text-slate-700');
            tabJson?.classList.replace('bg-slate-100', 'bg-primary');
            tabJson?.classList.replace('text-slate-700', 'text-white');

            if (modeInput) modeInput.value = 'json';
            validateJsonInput();
        } else {
            const ta = document.getElementById('modalContentJson');
            if (ta && ta.value?.trim()) {
                try {
                    populateVisualFromContent(type, JSON.parse(ta.value));
                } catch (e) {
                    console.warn('Cannot parse JSON back to visual:', e);
                }
            }

            visualCont?.classList.remove('hidden');
            jsonCont?.classList.add('hidden');
            btnFormat?.classList.add('hidden');

            tabJson?.classList.replace('bg-primary', 'bg-slate-100');
            tabJson?.classList.replace('text-white', 'text-slate-700');
            tabVisual?.classList.replace('bg-slate-100', 'bg-primary');
            tabVisual?.classList.replace('text-slate-700', 'text-white');

            if (modeInput) modeInput.value = 'visual';
        }
    }

    function switchQuizFormat(fmt) {
        currentQuizFormat = fmt;
        const mcqSec = document.getElementById('quizMcqSection');
        const pracSec = document.getElementById('quizPracticumSection');
        const btnMcq = document.getElementById('btnQuizFormatMcq');
        const btnPrac = document.getElementById('btnQuizFormatPracticum');

        const activeCls = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all bg-white text-primary shadow-xs dark:bg-neutral-700 dark:text-white cursor-pointer text-center';
        const inactiveCls = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-primary dark:text-neutral-400 dark:hover:text-white cursor-pointer text-center';

        if (fmt === 'practicum') {
            mcqSec?.classList.add('hidden');
            pracSec?.classList.remove('hidden');
            if (btnMcq) btnMcq.className = inactiveCls;
            if (btnPrac) btnPrac.className = activeCls;
        } else {
            mcqSec?.classList.remove('hidden');
            pracSec?.classList.add('hidden');
            if (btnPrac) btnPrac.className = inactiveCls;
            if (btnMcq) btnMcq.className = activeCls;
        }
    }

    function formatContentJson() {
        const ta = document.getElementById('modalContentJson');
        if (!ta) return;
        try {
            ta.value = JSON.stringify(JSON.parse(ta.value || '{}'), null, 2);
            validateJsonInput();
        } catch (e) {
            alert('Format JSON belum valid: ' + e.message);
        }
    }

    function validateJsonInput() {
        const ta = document.getElementById('modalContentJson');
        const statusEl = document.getElementById('jsonValidationStatus');
        if (!ta || !statusEl) return true;

        try {
            JSON.parse(ta.value || '{}');
            statusEl.innerText = '✓ Format JSON Valid';
            statusEl.className = 'text-[11px] text-emerald-600 font-semibold';
            return true;
        } catch (e) {
            statusEl.innerText = '✗ Syntax Error: ' + e.message;
            statusEl.className = 'text-[11px] text-rose-500 font-semibold';
            return false;
        }
    }

    function handleSectionTypeChange(type) {
        document.querySelectorAll('.section-type-option').forEach(el => {
            const isMatch = el.dataset.type === type;
            el.classList.toggle('border-primary', isMatch);
            el.classList.toggle('bg-surface', isMatch);
            el.classList.toggle('border-slate-200', !isMatch);
            const radio = el.querySelector('input');
            if (radio && isMatch) radio.checked = true;
        });

        document.querySelectorAll('.section-subform').forEach(el => el.classList.add('hidden'));
        const subformMap = { theory: 'subformTheory', vocab: 'subformVocab', dialogue: 'subformDialogue', practice: 'subformPractice', quiz: 'subformQuiz' };
        if (subformMap[type]) {
            document.getElementById(subformMap[type])?.classList.remove('hidden');
        }
    }

    // --- Dynamic Row Builders ---

    function addTheoryRuleRow(val = '') {
        const container = document.getElementById('theoryRulesContainer');
        if (!container) return;
        const rowId = 'rule-row-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'flex items-center gap-2';
        div.innerHTML = `
            <input type="text" name="theory_rules[]" value="${escapeHtml(val)}" class="w-full text-xs rounded-xl border-slate-300 bg-white py-2 px-3 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white" placeholder="Tuliskan poin aturan, misal: 'Gunakan am/is/are dengan subjek...' ">
            <button type="button" onclick="document.getElementById('${rowId}').remove()" class="size-8 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 text-slate-500 hover:text-rose-600 flex items-center justify-center shrink-0 cursor-pointer">✕</button>
        `;
        container.appendChild(div);
    }

    function addVocabRow(data = {}) {
        const container = document.getElementById('vocabRowsContainer');
        if (!container) return;
        const idx = vocabIndex++;
        const rowId = 'vocab-row-' + idx;
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'p-3 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 space-y-2 text-xs';
        div.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-slate-200/60 dark:border-neutral-700">
                <span class="font-bold text-slate-700 dark:text-neutral-300">Kata #${idx + 1}</span>
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-rose-500 hover:text-rose-700 font-semibold cursor-pointer">Hapus</button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <input type="text" name="vocab_words[${idx}][word]" value="${escapeHtml(data.word || '')}" class="rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold" placeholder="Kata (e.g. Greeting)">
                <input type="text" name="vocab_words[${idx}][phonetic]" value="${escapeHtml(data.phonetic || data.ipa || '')}" class="rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-mono" placeholder="Fonetik (e.g. /ˈɡriːtɪŋ/)">
                <input type="text" name="vocab_words[${idx}][partOfSpeech]" value="${escapeHtml(data.partOfSpeech || '')}" class="rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white" placeholder="Kelas (noun, verb)">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <input type="text" name="vocab_words[${idx}][meaningId]" value="${escapeHtml(data.meaningId || data.meaning || '')}" class="rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white" placeholder="Arti Bahasa Indonesia">
                <input type="text" name="vocab_words[${idx}][exampleSentence]" value="${escapeHtml(data.exampleSentence || data.example || '')}" class="rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white italic" placeholder="Contoh kalimat">
            </div>
        `;
        container.appendChild(div);
    }

    function addDialogueRow(data = {}) {
        const container = document.getElementById('dialogueRowsContainer');
        if (!container) return;
        const idx = dialogueIndex++;
        const rowId = 'dialogue-row-' + idx;
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'p-3 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 space-y-2 text-xs';
        div.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-slate-200/60 dark:border-neutral-700">
                <span class="font-bold text-slate-700 dark:text-neutral-300">Baris #${idx + 1}</span>
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-rose-500 hover:text-rose-700 font-semibold cursor-pointer">Hapus</button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                <input type="text" name="dialogue_lines[${idx}][speaker]" value="${escapeHtml(data.speaker || 'Speaker')}" class="sm:col-span-1 rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold" placeholder="Pembicara">
                <input type="text" name="dialogue_lines[${idx}][text]" value="${escapeHtml(data.text || '')}" class="sm:col-span-3 rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white" placeholder="Kalimat Bahasa Inggris (3-8 kata)">
            </div>
            <input type="text" name="dialogue_lines[${idx}][translation]" value="${escapeHtml(data.translation || '')}" class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-slate-500" placeholder="Terjemahan Bahasa Indonesia (Opsional)">
        `;
        container.appendChild(div);
    }

    function addCablePairRow(data = {}) {
        const container = document.getElementById('cablePairsContainer');
        if (!container) return;
        const idx = cablePairIndex++;
        const rowId = 'cable-pair-' + idx;
        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 text-xs';
        div.innerHTML = `
            <span class="font-mono text-slate-400 font-bold w-5 shrink-0">#${idx + 1}</span>
            <input type="text" name="cable_pairs[${idx}][left]" value="${escapeHtml(data.left || data.word || '')}" class="flex-1 rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold" placeholder="Kata Bahasa Inggris (e.g. collect)">
            <span class="text-amber-600 font-bold shrink-0">⟷</span>
            <input type="text" name="cable_pairs[${idx}][right]" value="${escapeHtml(data.right || data.meaning || data.meaningId || '')}" class="flex-1 rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white" placeholder="Arti Bahasa Indonesia (e.g. mengumpulkan)">
            <button type="button" onclick="document.getElementById('${rowId}').remove()" class="size-7 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center shrink-0 cursor-pointer">✕</button>
        `;
        container.appendChild(div);
    }

    function pullFromVocabSection() {
        const templates = document.querySelectorAll('template[id^="sec-data-"]');
        let pulledPairs = [];

        templates.forEach(tpl => {
            try {
                const sec = JSON.parse(tpl.innerHTML);
                if (sec.section_type === 'vocab' || sec.section_type === 'vocabulary') {
                    let content = typeof sec.content === 'string' ? JSON.parse(sec.content) : sec.content;
                    const items = (content?.items || content?.words) || [];
                    items.forEach(it => {
                        const en = it.word || '';
                        const id = it.meaning || it.meaningId || '';
                        if (en && id && pulledPairs.length < 5) pulledPairs.push({ left: en, right: id });
                    });
                }
            } catch (e) {}
        });

        if (pulledPairs.length === 0) {
            alert('Tidak ditemukan bagian Kosakata pada modul ini. Silakan tambahkan secara manual.');
            return;
        }

        const container = document.getElementById('cablePairsContainer');
        if (container) container.innerHTML = '';
        cablePairIndex = 0;
        pulledPairs.forEach(p => addCablePairRow(p));
    }

    function addQuizQuestionRow(data = {}) {
        const container = document.getElementById('quizQuestionsContainer');
        if (!container) return;
        const idx = quizIndex++;
        const rowId = 'quiz-row-' + idx;
        const options = data.options || ['', '', '', ''];
        const currentAnswer = data.answer || options[0] || '';

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-900/50 border border-slate-200 dark:border-neutral-700 space-y-2 text-xs';
        div.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-slate-200/60 dark:border-neutral-700">
                <span class="font-bold text-primary dark:text-accent">Pilihan Ganda #${idx + 1}</span>
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="text-rose-500 hover:text-rose-700 font-semibold cursor-pointer">Hapus Soal</button>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Pertanyaan</label>
                <input type="text" name="quiz_questions[${idx}][question]" value="${escapeHtml(data.question || '')}" class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-medium" placeholder="Misal: What is the correct response to 'How are you?'">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                ${[0, 1, 2, 3].map(optIdx => `
                    <input type="text" name="quiz_questions[${idx}][options][]" value="${escapeHtml(options[optIdx] || '')}" class="rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white" placeholder="Pilihan ${String.fromCharCode(65 + optIdx)}">
                `).join('')}
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-emerald-700 dark:text-emerald-400 mb-1">Kunci Jawaban Tepat</label>
                    <input type="text" name="quiz_questions[${idx}][answer]" value="${escapeHtml(currentAnswer)}" class="w-full rounded-xl border-emerald-300 bg-emerald-50/40 py-1.5 px-2.5 dark:bg-emerald-950/20 dark:border-emerald-700 dark:text-emerald-300 font-bold" placeholder="Ketik teks jawaban yang benar">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Pembahasan (Opsional)</label>
                    <input type="text" name="quiz_questions[${idx}][explanation]" value="${escapeHtml(data.explanation || '')}" class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-slate-500" placeholder="Alasan mengapa jawaban tepat">
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    // --- Two-Way Serialization ---

    function serializeVisualToContent(type) {
        let content = Object.assign({}, currentActiveSectionContent || {});

        if (type === 'theory') {
            content.summary = document.getElementById('theorySummary')?.value || '';
            content.rules = Array.from(document.querySelectorAll('input[name="theory_rules[]"]')).map(i => i.value.trim()).filter(Boolean);
            const trapTitle = document.getElementById('theoryTrapTitle')?.value || '';
            const trapWrong = document.getElementById('theoryTrapWrong')?.value || '';
            if (trapTitle || trapWrong) {
                content.commonTrap = {
                    trapTitle: trapTitle,
                    wrong: trapWrong,
                    correct: document.getElementById('theoryTrapCorrect')?.value || '',
                    explanation: document.getElementById('theoryTrapExplanation')?.value || ''
                };
            }
        } else if (type === 'vocab') {
            const items = Array.from(document.querySelectorAll('[id^="vocab-row-"]')).map(row => ({
                word: row.querySelector('input[name*="[word]"]')?.value.trim() || '',
                phonetic: row.querySelector('input[name*="[phonetic]"]')?.value.trim() || '',
                ipa: row.querySelector('input[name*="[phonetic]"]')?.value.trim() || '',
                partOfSpeech: row.querySelector('input[name*="[partOfSpeech]"]')?.value.trim() || '',
                meaning: row.querySelector('input[name*="[meaningId]"]')?.value.trim() || '',
                meaningId: row.querySelector('input[name*="[meaningId]"]')?.value.trim() || '',
                exampleSentence: row.querySelector('input[name*="[exampleSentence]"]')?.value.trim() || ''
            })).filter(it => it.word !== '');
            content.items = items;
            content.words = items;
        } else if (type === 'dialogue') {
            content.context = document.getElementById('dialogueContext')?.value || '';
            content.lines = Array.from(document.querySelectorAll('[id^="dialogue-row-"]')).map(row => ({
                speaker: row.querySelector('input[name*="[speaker]"]')?.value.trim() || 'Speaker',
                text: row.querySelector('input[name*="[text]"]')?.value.trim() || '',
                translation: row.querySelector('input[name*="[translation]"]')?.value.trim() || ''
            })).filter(l => l.text !== '');
        } else if (type === 'practice') {
            const pairs = Array.from(document.querySelectorAll('[id^="cable-pair-"]')).map(row => ({
                left: row.querySelector('input[name*="[left]"]')?.value.trim() || '',
                right: row.querySelector('input[name*="[right]"]')?.value.trim() || ''
            })).filter(p => p.left && p.right);
            content.drills = [{
                id: 'drill-cable-1',
                title: 'Interactive Cable Connection (Vocab Match)',
                instruction: document.getElementById('practiceInstruction')?.value || 'Connect each vocabulary term on the left with its accurate meaning on the right.',
                pairs: pairs
            }];
        } else if (type === 'quiz') {
            content.passingScore = parseInt(document.getElementById('quizPassingScore')?.value || '70', 10);
            if (currentQuizFormat === 'practicum') {
                content.practicumQuestions = serializePracticumQuestions();
                delete content.questions;
            } else {
                content.questions = Array.from(document.querySelectorAll('[id^="quiz-row-"]')).map(row => {
                    const qText = row.querySelector('input[name*="[question]"]')?.value.trim();
                    const opts = Array.from(row.querySelectorAll('input[name*="[options]"]')).map(i => i.value.trim()).filter(Boolean);
                    return qText ? {
                        question: qText,
                        options: opts,
                        answer: row.querySelector('input[name*="[answer]"]')?.value.trim() || opts[0] || '',
                        explanation: row.querySelector('input[name*="[explanation]"]')?.value.trim() || ''
                    } : null;
                }).filter(Boolean);
                delete content.practicumQuestions;
            }
        }

        currentActiveSectionContent = content;
        const ta = document.getElementById('modalContentJson');
        if (ta) ta.value = JSON.stringify(content, null, 2);
        return content;
    }

    function populateVisualFromContent(type, c) {
        c = c || {};
        if (type === 'theory') {
            document.getElementById('theorySummary').value = c.summary || '';
            const container = document.getElementById('theoryRulesContainer');
            if (container) container.innerHTML = '';
            (c.rules?.length ? c.rules : ['']).forEach(r => addTheoryRuleRow(typeof r === 'object' ? JSON.stringify(r) : r));
            if (c.commonTrap) {
                document.getElementById('theoryTrapTitle').value = c.commonTrap.trapTitle || '';
                document.getElementById('theoryTrapWrong').value = c.commonTrap.wrong || '';
                document.getElementById('theoryTrapCorrect').value = c.commonTrap.correct || '';
                document.getElementById('theoryTrapExplanation').value = c.commonTrap.explanation || '';
            }
        } else if (type === 'vocab') {
            const container = document.getElementById('vocabRowsContainer');
            if (container) container.innerHTML = '';
            vocabIndex = 0;
            const words = c.items || c.words || [];
            (words.length ? words : [{}]).forEach(w => addVocabRow(w));
        } else if (type === 'dialogue') {
            document.getElementById('dialogueContext').value = c.context || '';
            const container = document.getElementById('dialogueRowsContainer');
            if (container) container.innerHTML = '';
            dialogueIndex = 0;
            const lines = c.lines || [];
            (lines.length ? lines : [{}]).forEach(l => addDialogueRow(l));
        } else if (type === 'practice') {
            const container = document.getElementById('cablePairsContainer');
            if (container) container.innerHTML = '';
            cablePairIndex = 0;
            let pairs = c.drills?.[0]?.pairs || c.pairs || [];
            if (c.drills?.[0]?.instruction) {
                const instrInput = document.getElementById('practiceInstruction');
                if (instrInput) instrInput.value = c.drills[0].instruction;
            }
            (pairs.length ? pairs : [{}]).forEach(p => addCablePairRow(p));
        } else if (type === 'quiz') {
            document.getElementById('quizPassingScore').value = c.passingScore || 70;
            const mcqContainer = document.getElementById('quizQuestionsContainer');
            if (mcqContainer) mcqContainer.innerHTML = '';
            quizIndex = 0;

            const isPracticum = (c.practicumQuestions && c.practicumQuestions.length > 0) ||
                                (c.questions && c.questions.some(q => q.type === 'dictation' || q.type === 'arrange'));

            if (isPracticum) {
                switchQuizFormat('practicum');
                populatePracticumQuestions(c.practicumQuestions || c.questions || []);
            } else {
                switchQuizFormat('mcq');
                const list = c.questions || [];
                (list.length ? list : [{}]).forEach(q => addQuizQuestionRow(q));
            }
        }
    }

    function openAddSectionModal(subModuleId) {
        const form = document.getElementById('sectionForm');
        if (!form) return;
        form.action = "{{ route('admin.modules.section.create', $module->id) }}";
        const label = document.getElementById('sectionModal-label');
        if (label) label.innerText = "Tambah Bagian Materi";
        document.getElementById('modalSectionId').value = "";
        document.getElementById('modalSectionTitle').value = "";

        const targetSubModuleId = subModuleId || window.currentActiveSubModuleId;
        if (targetSubModuleId) {
            const sel = document.getElementById('modalSubModuleId');
            if (sel) sel.value = targetSubModuleId;
        }

        currentActiveSectionContent = {};
        document.getElementById('modalContentJson').value = "{}";

        ['theorySummary', 'theoryTrapTitle', 'theoryTrapWrong', 'theoryTrapCorrect', 'theoryTrapExplanation', 'dialogueContext'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        ['theoryRulesContainer', 'vocabRowsContainer', 'dialogueRowsContainer', 'cablePairsContainer', 'quizQuestionsContainer', 'practicumQuestionsContainer'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '';
        });

        addTheoryRuleRow();
        addVocabRow();
        addDialogueRow();
        addCablePairRow();
        addQuizQuestionRow();
        addPracticumQuestionRow();

        syncModalContextFromSelect();
        handleSectionTypeChange('theory');
        switchQuizFormat('mcq');
        switchEditMode('visual');

        openModal('sectionModal');
    }

    function openEditSectionModalById(id) {
        const tpl = document.getElementById('sec-data-' + id);
        if (!tpl) return;
        try {
            openEditSectionModal(JSON.parse(tpl.innerHTML));
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

        syncModalContextFromSelect();

        let c = sec.content;
        if (typeof c === 'string') {
            try { c = JSON.parse(c); } catch (e) { c = {}; }
        }
        c = c || {};
        currentActiveSectionContent = c;

        document.getElementById('modalContentJson').value = JSON.stringify(c, null, 2);

        let type = sec.section_type;
        if (type === 'vocabulary') type = 'vocab';

        handleSectionTypeChange(type);
        populateVisualFromContent(type, c);

        switchEditMode('visual');
        openModal('sectionModal');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('sectionForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                const modeInput = document.getElementById('modalContentMode');
                const type = document.querySelector('input[name="section_type"]:checked')?.value || 'theory';

                if (modeInput && modeInput.value === 'json') {
                    if (!validateJsonInput()) {
                        e.preventDefault();
                        alert('Silakan perbaiki format JSON yang keliru sebelum menyimpan.');
                        return false;
                    }
                } else {
                    serializeVisualToContent(type);
                }
            });
        }
    });
</script>
