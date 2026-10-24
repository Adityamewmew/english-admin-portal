<script>
    window.currentActiveSubModuleId = "{{ $activeSubModuleId ?? ($subModules->first()->id ?? '') }}";
    let aiStepInterval = null;

    // AI Generating Overlay / Modal Notification
    window.showAiOverlay = function(title, subtitle) {
        const overlay = document.getElementById('aiGeneratingOverlay');
        if (!overlay) return true;

        if (title) {
            const titleEl = document.getElementById('aiOverlayTitle');
            if (titleEl) titleEl.innerText = title;
        }
        if (subtitle) {
            const subEl = document.getElementById('aiOverlaySubtitle');
            if (subEl) subEl.innerText = subtitle;
        }

        overlay.classList.remove('hidden');
        overlay.classList.add('flex');

        // Dynamic status ticker
        const statusTextEl = document.getElementById('aiOverlayStatusText');
        const stepItems = [
            'Menganalisis kurikulum CEFR & konteks modul...',
            'Menyusun materi teori, tata bahasa, dan contoh...',
            'Menghasilkan daftar kosakata, fonetik, dan arti...',
            'Membuat dialog percakapan dan konteks audio...',
            'Menyusun butir soal kuis pilihan ganda & pembahasan...',
            'Memvalidasi format JSON & menyimpan ke database...'
        ];
        let stepIdx = 0;
        if (statusTextEl) {
            statusTextEl.innerText = stepItems[0];
            if (aiStepInterval) clearInterval(aiStepInterval);
            aiStepInterval = setInterval(() => {
                stepIdx = (stepIdx + 1) % stepItems.length;
                statusTextEl.innerText = stepItems[stepIdx];
            }, 3500);
        }

        // Prevent double submit
        setTimeout(() => {
            document.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.setAttribute('disabled', 'disabled');
            });
        }, 50);

        return true;
    };

    // Tab Switching for Sub-Modules
    window.switchSubModuleTab = function(id) {
        if (!id) return;
        window.currentActiveSubModuleId = id;

        // Switch Panes
        document.querySelectorAll('.submodule-tab-pane').forEach(el => el.classList.add('hidden'));
        const targetPane = document.getElementById('pane-' + id);
        if (targetPane) {
            targetPane.classList.remove('hidden');
        }

        // Switch Tab Pills
        document.querySelectorAll('.submodule-tab-btn').forEach(btn => {
            const isActive = (btn.dataset.tab === id);
            if (isActive) {
                btn.classList.remove('btn-tactile-outline');
                btn.classList.add('btn-tactile-primary');
                const icon = btn.querySelector('.tab-icon');
                if (icon) {
                    icon.classList.remove('text-slate-400');
                    icon.classList.add('text-accent');
                }
                const count = btn.querySelector('.tab-count');
                if (count) {
                    count.className = 'tab-count px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-accent';
                }
            } else {
                btn.classList.remove('btn-tactile-primary');
                btn.classList.add('btn-tactile-outline');
                const icon = btn.querySelector('.tab-icon');
                if (icon) {
                    icon.classList.remove('text-accent');
                    icon.classList.add('text-slate-400');
                }
                const count = btn.querySelector('.tab-count');
                if (count) {
                    count.className = 'tab-count px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-200 text-slate-700 dark:bg-neutral-700 dark:text-neutral-300';
                }
            }
        });

        // Switch Wizard Stepper if present
        document.querySelectorAll('.submodule-wizard-step').forEach(btn => {
            const isCurrent = (btn.dataset.stepId === id);
            if (isCurrent) {
                btn.classList.remove('btn-tactile-outline');
                btn.classList.add('btn-tactile-primary', '!shadow-md');
                const count = btn.querySelector('.step-count');
                if (count) {
                    count.classList.remove('text-slate-400');
                    count.classList.add('text-accent');
                }
            } else {
                btn.classList.remove('btn-tactile-primary', '!shadow-md');
                btn.classList.add('btn-tactile-outline');
                const count = btn.querySelector('.step-count');
                if (count) {
                    count.classList.remove('text-accent');
                    count.classList.add('text-slate-400');
                }
            }
        });

        // Update URL query parameter silently
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('sub_module', id);
            window.history.replaceState({}, '', url.toString());
        } catch (e) {}
    };

    // Sub-Module Title Edit Modal
    window.openEditSubModuleModal = function(id, title) {
        const form = document.getElementById('editSubModuleForm');
        if (form) {
            form.action = "{{ url('admin/modules/detail/' . $module->id . '/submodule') }}/" + id + "/update";
        }
        const input = document.getElementById('editSubModuleTitleInput');
        if (input) {
            input.value = title || '';
        }
        openModal('editSubModuleModal');
    };

    // Toggle Regenerate Form Box
    window.toggleRegenerateBox = function(id) {
        const box = document.getElementById('regen-box-' + id);
        if (box) {
            box.classList.toggle('hidden');
        }
    };

    window.confirmDeleteSectionById = function(id, subModuleId) {
        const card = document.getElementById('section-card-' + id);
        let title = 'Materi';
        if (card) {
            const titleEl = card.querySelector('h3');
            if (titleEl) title = titleEl.innerText.trim();
        }
        confirmDeleteSection(id, title, subModuleId);
    };

    window.confirmDeleteSection = function(id, title, subModuleId) {
        const form = document.getElementById('deleteSectionForm');
        form.action = "{{ url('admin/modules/detail/' . $module->id . '/section') }}/" + id + "/delete";
        document.getElementById('deleteSectionTitle').innerText = title;
        document.getElementById('deleteSectionSubModuleId').value = subModuleId || '';

        openModal('deleteSectionModal');
    };

    window.escapeHtml = function(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    window.openModal = function(modalId) {
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
    };

    // Reset overlay & buttons if user returns to page via back button
    window.addEventListener('pageshow', () => {
        const overlay = document.getElementById('aiGeneratingOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
        if (aiStepInterval) {
            clearInterval(aiStepInterval);
            aiStepInterval = null;
        }
        document.querySelectorAll('button[type="submit"][disabled]').forEach(b => {
            b.removeAttribute('disabled');
        });
    });
</script>
