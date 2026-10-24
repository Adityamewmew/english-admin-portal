<script>
    let practicumIndex = 0;

    function playTtsPreview(text) {
        if (!window.speechSynthesis) {
            alert('Browser tidak mendukung Web Speech API audio preview.');
            return;
        }
        if (!text || text.trim() === '') {
            alert('Silakan isi teks audio terlebih dahulu untuk mendengarkan pelafalan.');
            return;
        }
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text.trim());
        u.lang = 'en-US';
        u.rate = 0.9;
        window.speechSynthesis.speak(u);
    }

    function addPracticumQuestionRow(data = {}) {
        const container = document.getElementById('practicumQuestionsContainer');
        if (!container) return;
        const idx = practicumIndex++;
        const rowId = 'prac-row-' + idx;
        const type = data.type || (data.category === 'roleplay' ? 'speaking_roleplay' : (data.category ? 'speaking_drill' : (data.targetAnswers ? 'writing' : 'dictation')));
        const summary = data.audioText || data.promptSentence || data.targetSentence || data.promptText || data.audioPrompt || data.instruction || `Soal Practicum #${idx + 1}`;
        const wordsStr = Array.isArray(data.words) ? data.words.join(', ') : (data.words || '');
        const targetAnswersStr = Array.isArray(data.targetAnswers) ? data.targetAnswers.join(' | ') : (data.targetAnswers || '');

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'rounded-xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900/60 overflow-hidden shadow-2xs transition-all';
        div.innerHTML = `
            {{-- Collapsible Card Header --}}
            <div class="p-3 bg-slate-50 dark:bg-neutral-800/80 flex items-center justify-between gap-2 cursor-pointer select-none" onclick="toggleAccordionItem('${idx}')">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="font-mono text-xs font-bold text-slate-400">#${idx + 1}</span>
                    <span id="badge-prac-${idx}" class="px-2 py-0.5 rounded-md text-[10px] font-extrabold tracking-wide uppercase bg-primary text-white">
                        ${type.replace('_', ' ')}
                    </span>
                    <span id="title-prac-${idx}" class="text-xs font-medium text-slate-700 dark:text-neutral-300 truncate">
                        ${escapeHtml(summary)}
                    </span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="event.stopPropagation(); document.getElementById('${rowId}').remove()"
                        class="text-xs text-rose-500 hover:text-rose-700 font-bold px-2 py-1 rounded-md hover:bg-rose-50 cursor-pointer">
                        Hapus
                    </button>
                    <svg id="chevron-prac-${idx}" class="size-4 text-slate-400 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>

            {{-- Collapsible Card Body --}}
            <div id="body-prac-${idx}" class="p-3.5 space-y-3 border-t border-slate-100 dark:border-neutral-800 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Kategori / Tipe Practicum</label>
                        <select name="practicum_questions[${idx}][type]" onchange="handlePracticumTypeChange('${idx}', this.value)"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold">
                            <option value="dictation" ${type === 'dictation' ? 'selected' : ''}>Dictation (Listening)</option>
                            <option value="arrange" ${type === 'arrange' ? 'selected' : ''}>Sentence Arrange (Listening)</option>
                            <option value="writing" ${type === 'writing' ? 'selected' : ''}>Sentence Transformation (Writing)</option>
                            <option value="speaking_drill" ${type === 'speaking_drill' ? 'selected' : ''}>Oral Pronunciation Drill (Speaking)</option>
                            <option value="speaking_roleplay" ${type === 'speaking_roleplay' ? 'selected' : ''}>Voice Call with Mr. Khoirul (Speaking)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Instruksi Soal</label>
                        <input type="text" name="practicum_questions[${idx}][instruction]" value="${escapeHtml(data.instruction || '')}"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                            placeholder="Contoh: Listen to the audio clip and type the missing word:">
                    </div>
                </div>

                {{-- Fields: Dictation --}}
                <div id="fields-dictation-${idx}" class="space-y-2 ${type === 'dictation' ? '' : 'hidden'}">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-semibold text-slate-700 dark:text-neutral-300">Teks Audio Pembicara (audioText)</label>
                            <button type="button" onclick="playTtsPreview(document.getElementById('audioText-${idx}').value)"
                                class="text-[11px] text-primary hover:underline font-bold flex items-center gap-1 cursor-pointer">
                                🔊 Tes Dengarkan Audio
                            </button>
                        </div>
                        <input type="text" id="audioText-${idx}" name="practicum_questions[${idx}][audioText]" value="${escapeHtml(data.audioText || '')}"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-medium"
                            placeholder="Contoh: It is a new shirt.">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Kalimat Soal Berserta Blank (promptSentence)</label>
                            <input type="text" name="practicum_questions[${idx}][promptSentence]" value="${escapeHtml(data.promptSentence || '')}"
                                class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                                placeholder="Contoh: It is a new _____.">
                        </div>
                        <div>
                            <label class="block font-semibold text-emerald-700 dark:text-emerald-400 mb-1">Kata Kunci Jawaban (targetWord)</label>
                            <input type="text" name="practicum_questions[${idx}][targetWord]" value="${escapeHtml(data.targetWord || '')}"
                                class="w-full rounded-xl border-emerald-300 bg-emerald-50/40 py-1.5 px-2.5 dark:bg-emerald-950/20 dark:border-emerald-700 dark:text-emerald-300 font-bold"
                                placeholder="Contoh: shirt">
                        </div>
                    </div>
                </div>

                {{-- Fields: Sentence Arrange --}}
                <div id="fields-arrange-${idx}" class="space-y-2 ${type === 'arrange' ? '' : 'hidden'}">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-semibold text-slate-700 dark:text-neutral-300">Kalimat Target Lengkap (targetSentence)</label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="playTtsPreview(document.getElementById('targetSentence-${idx}').value)"
                                    class="text-[11px] text-primary hover:underline font-bold flex items-center gap-1 cursor-pointer">
                                    🔊 Tes Audio
                                </button>
                                <button type="button" onclick="autoShuffleSentence('${idx}')"
                                    class="text-[11px] text-amber-700 dark:text-amber-300 hover:underline font-bold cursor-pointer">
                                    ⚡ Acak Kata Otomatis
                                </button>
                            </div>
                        </div>
                        <input type="text" id="targetSentence-${idx}" name="practicum_questions[${idx}][targetSentence]" value="${escapeHtml(data.targetSentence || data.audioText || '')}"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-medium"
                            placeholder="Contoh: She is from Spain.">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Daftar Kata Acak (pisahkan dengan koma)</label>
                        <input type="text" id="words-${idx}" name="practicum_questions[${idx}][words]" value="${escapeHtml(wordsStr)}"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-mono"
                            placeholder="Contoh: from, is, Spain., She">
                    </div>
                </div>

                {{-- Fields: Writing Transformation --}}
                <div id="fields-writing-${idx}" class="space-y-2 ${type === 'writing' ? '' : 'hidden'}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Kalimat Awal (promptText)</label>
                            <input type="text" name="practicum_questions[${idx}][promptText]" value="${escapeHtml(data.promptText || '')}"
                                class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-medium"
                                placeholder="Contoh: Liam is from Melbourne.">
                        </div>
                        <div>
                            <label class="block font-semibold text-emerald-700 dark:text-emerald-400 mb-1">Kunci Jawaban Tulis Lengkap (targetAnswers)</label>
                            <input type="text" name="practicum_questions[${idx}][targetAnswers]" value="${escapeHtml(targetAnswersStr)}"
                                class="w-full rounded-xl border-emerald-300 bg-emerald-50/40 py-1.5 px-2.5 dark:bg-emerald-950/20 dark:border-emerald-700 dark:text-emerald-300 font-bold"
                                placeholder="Contoh: Liam is not from Melbourne. | Liam isn't from Melbourne.">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Petunjuk / Hint Aturan</label>
                        <input type="text" name="practicum_questions[${idx}][hint]" value="${escapeHtml(data.hint || '')}"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                            placeholder="Contoh: Use 'is not' or the contraction 'isn't'.">
                    </div>
                </div>

                {{-- Fields: Speaking Drill / Roleplay --}}
                <div id="fields-speaking-${idx}" class="space-y-2 ${type.startsWith('speaking') ? '' : 'hidden'}">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-semibold text-slate-700 dark:text-neutral-300">Audio Prompt Tutor (audioPrompt)</label>
                            <button type="button" onclick="playTtsPreview(document.getElementById('spkAudio-${idx}').value)"
                                class="text-[11px] text-primary hover:underline font-bold flex items-center gap-1 cursor-pointer">
                                🔊 Tes Suara Tutor
                            </button>
                        </div>
                        <input type="text" id="spkAudio-${idx}" name="practicum_questions[${idx}][audioPrompt]" value="${escapeHtml(data.audioPrompt || '')}"
                            class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white"
                            placeholder="Contoh: Repeat: Canada. Canadian. atau Hello, welcome to Eddy's English!">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Target Ucapan / Skenario</label>
                            <input type="text" name="practicum_questions[${idx}][targetPhrase]" value="${escapeHtml(data.targetPhrase || data.scenarioContext || '')}"
                                class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-bold"
                                placeholder="Contoh: Canada. Canadian. atau Meeting at the cafe">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Fonetik IPA / Catatan Roleplay</label>
                            <input type="text" name="practicum_questions[${idx}][phonetic]" value="${escapeHtml(data.phonetic || data.characterRole || '')}"
                                class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white font-mono"
                                placeholder="Contoh: /ˈkænədə/ /kəˈneɪdiən/ atau Friendly Tutor">
                        </div>
                    </div>
                </div>

                {{-- Explanation --}}
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-neutral-300 mb-1">Pembahasan & Catatan Penilaian (explanation)</label>
                    <input type="text" name="practicum_questions[${idx}][explanation]" value="${escapeHtml(data.explanation || '')}"
                        class="w-full rounded-xl border-slate-300 bg-white py-1.5 px-2.5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white text-slate-500"
                        placeholder="Contoh: Subject + Verb + Preposition + Country.">
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    function toggleAccordionItem(idx) {
        const body = document.getElementById('body-prac-' + idx);
        const chevron = document.getElementById('chevron-prac-' + idx);
        if (!body) return;
        body.classList.toggle('hidden');
        if (chevron) chevron.classList.toggle('rotate-180');
    }

    function toggleAllPracticumAccordions() {
        const bodies = document.querySelectorAll('[id^="body-prac-"]');
        const chevrons = document.querySelectorAll('[id^="chevron-prac-"]');
        const anyOpen = Array.from(bodies).some(b => !b.classList.contains('hidden'));
        bodies.forEach(b => {
            if (anyOpen) b.classList.add('hidden');
            else b.classList.remove('hidden');
        });
        chevrons.forEach(c => {
            if (anyOpen) c.classList.remove('rotate-180');
            else c.classList.add('rotate-180');
        });
    }

    function handlePracticumTypeChange(idx, type) {
        const badge = document.getElementById('badge-prac-' + idx);
        if (badge) badge.innerText = type.replace('_', ' ');

        const dict = document.getElementById('fields-dictation-' + idx);
        const arr = document.getElementById('fields-arrange-' + idx);
        const wrt = document.getElementById('fields-writing-' + idx);
        const spk = document.getElementById('fields-speaking-' + idx);

        if (dict) dict.classList.toggle('hidden', type !== 'dictation');
        if (arr) arr.classList.toggle('hidden', type !== 'arrange');
        if (wrt) wrt.classList.toggle('hidden', type !== 'writing');
        if (spk) spk.classList.toggle('hidden', !type.startsWith('speaking'));
    }

    function serializePracticumQuestions() {
        const pracQs = [];
        document.querySelectorAll('[id^="prac-row-"]').forEach(row => {
            const typeSel = row.querySelector('select[name*="[type]"]')?.value || 'dictation';
            const instr = row.querySelector('input[name*="[instruction]"]')?.value.trim() || '';
            const expl = row.querySelector('input[name*="[explanation]"]')?.value.trim() || '';

            if (typeSel === 'dictation') {
                pracQs.push({
                    type: 'dictation',
                    instruction: instr,
                    audioText: row.querySelector('input[name*="[audioText]"]')?.value.trim() || '',
                    promptSentence: row.querySelector('input[name*="[promptSentence]"]')?.value.trim() || '',
                    targetWord: row.querySelector('input[name*="[targetWord]"]')?.value.trim() || '',
                    explanation: expl
                });
            } else if (typeSel === 'arrange') {
                const wordsRaw = row.querySelector('input[name*="[words]"]')?.value.trim() || '';
                const wordsArr = wordsRaw ? wordsRaw.split(',').map(s => s.trim()).filter(Boolean) : [];
                pracQs.push({
                    type: 'arrange',
                    instruction: instr,
                    targetSentence: row.querySelector('input[name*="[targetSentence]"]')?.value.trim() || '',
                    audioText: row.querySelector('input[name*="[targetSentence]"]')?.value.trim() || '',
                    words: wordsArr,
                    explanation: expl
                });
            } else if (typeSel === 'writing') {
                const ansRaw = row.querySelector('input[name*="[targetAnswers]"]')?.value.trim() || '';
                const ansArr = ansRaw ? ansRaw.split('|').map(s => s.trim()).filter(Boolean) : [];
                pracQs.push({
                    typeLabel: 'Sentence Transformation',
                    instruction: instr,
                    promptText: row.querySelector('input[name*="[promptText]"]')?.value.trim() || '',
                    targetAnswers: ansArr,
                    hint: row.querySelector('input[name*="[hint]"]')?.value.trim() || '',
                    explanation: expl
                });
            } else if (typeSel === 'speaking_roleplay') {
                pracQs.push({
                    category: 'roleplay',
                    typeLabel: 'Interactive Voice Call with Mr. Khoirul',
                    instruction: instr,
                    audioPrompt: row.querySelector('input[name*="[audioPrompt]"]')?.value.trim() || '',
                    scenarioContext: row.querySelector('input[name*="[targetPhrase]"]')?.value.trim() || '',
                    characterRole: row.querySelector('input[name*="[phonetic]"]')?.value.trim() || 'Friendly English Tutor',
                    minTurns: 3,
                    explanation: expl
                });
            } else {
                pracQs.push({
                    category: 'linguistic_drill',
                    typeLabel: 'Oral Drill & Pronunciation',
                    instruction: instr,
                    audioPrompt: row.querySelector('input[name*="[audioPrompt]"]')?.value.trim() || '',
                    targetPhrase: row.querySelector('input[name*="[targetPhrase]"]')?.value.trim() || '',
                    phonetic: row.querySelector('input[name*="[phonetic]"]')?.value.trim() || '',
                    explanation: expl
                });
            }
        });
        return pracQs;
    }

    function populatePracticumQuestions(list) {
        const pracContainer = document.getElementById('practicumQuestionsContainer');
        if (pracContainer) pracContainer.innerHTML = '';
        practicumIndex = 0;
        (list || []).forEach(q => addPracticumQuestionRow(q));
    }
</script>
