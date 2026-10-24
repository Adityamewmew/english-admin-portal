<?php

namespace App\Http\Controllers\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\CurriculumModuleUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CurriculumModuleController extends Controller
{
    protected array $page = [
        'route' => 'modules',
        'title' => 'Modul Kurikulum',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected CurriculumModuleUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $cefr = $request->get('cefr', 'all');
        $status = $request->get('status', 'all');
        $keywords = $request->get('keywords');

        $data = $this->usecase->getAll([
            'cefr' => $cefr,
            'status' => $status,
            'keywords' => $keywords,
        ]);
        $data = $data['data']['list'] ?? [];

        $levels = $this->usecase->getLevels()['data']['levels'] ?? [];
        $stats = $this->usecase->getStats()['data'] ?? [
            'total' => 0,
            'learning_count' => 0,
            'exam_count' => 0,
            'active_count' => 0,
            'inactive_count' => 0,
        ];

        return view('_admin.modules.index', [
            'data' => $data,
            'levels' => $levels,
            'stats' => $stats,
            'page' => $this->page,
            'cefr' => $cefr,
            'status' => $status,
            'keywords' => $keywords,
        ]);
    }

    public function detail(string $id): View|RedirectResponse|Response
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data'])) {
            return redirect()
                ->intended($this->baseRedirect)
                ->with('error', ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $cachedDocName = Cache::get('module_doc_name_'.$id);
        $hasCachedDoc = ! empty(Cache::get('module_doc_text_'.$id)) || ! empty(Cache::get('module_doc_base64_'.$id));

        return view('_admin.modules.detail', [
            'module' => (object) $data['data']['module'],
            'sections' => $data['data']['sections'],
            'subModules' => $data['data']['sub_modules'],
            'hasCachedDoc' => $hasCachedDoc,
            'cachedDocName' => $cachedDocName,
            'page' => $this->page,
        ]);
    }

    public function add(): View
    {
        $levels = $this->usecase->getLevels()['data']['levels'] ?? [];

        return view('_admin.modules.add', [
            'levels' => $levels,
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'cefr' => 'required|in:A1,A2,B1,B2,C1,C2',
            'level_id' => 'required|string|max:20',
            'group' => 'required|string|max:100',
            'objective' => 'nullable|string',
            'complexity' => 'required|in:easy,medium,hard',
            'estimated_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'order_index' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:20480',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $ext = 'jpg';
            }
            $filename = 'banner_'.time().'_'.uniqid().'.'.$ext;
            $destinationPath = public_path('uploads/modules');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['banner_url'] = '/uploads/modules/'.$filename;
        }

        $res = $this->usecase->create($data);

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $id = $res['data']['id'];
        $firstSubModuleId = $res['data']['first_sub_module_id'] ?? null;

        // Extract and cache document text / payload if uploaded
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $extracted = $this->extractUploadedDocument($file);
            if (! empty($extracted['document_text'])) {
                Cache::put('module_doc_text_'.$id, $extracted['document_text'], now()->addHours(24));
            }
            if (! empty($extracted['document_base64'])) {
                Cache::put('module_doc_base64_'.$id, $extracted['document_base64'], now()->addHours(24));
                Cache::put('module_doc_mime_'.$id, $extracted['document_mime_type'] ?? 'application/pdf', now()->addHours(24));
            }
            Cache::put('module_doc_name_'.$id, $file->getClientOriginalName(), now()->addHours(24));
        }

        $redirectUrl = route('admin.modules.detail', $id).'?wizard=1&step=reading'.($firstSubModuleId ? ('&sub_module='.$firstSubModuleId) : '');

        $successMsg = $request->hasFile('document')
            ? 'Modul berhasil dibuat! Dokumen bahan ajar telah dipindai. Silakan pilih opsi generate materi di bawah.'
            : 'Modul berhasil dibuat! 5 Sub-Modul telah disiapkan. Silakan pilih opsi generate materi di bawah.';

        return redirect()->to($redirectUrl)->with('success', $successMsg);
    }

    public function doUpdateSubModule(string $id, string $subModuleId, Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
        ]);

        $res = $this->usecase->updateSubModule($id, $subModuleId, [
            'title' => $request->input('title'),
        ]);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $query = '?sub_module='.$subModuleId.($request->has('wizard') ? '&wizard='.$request->input('wizard') : '').($request->has('step') ? '&step='.$request->input('step') : '');

        return redirect()->to(route('admin.modules.detail', $id).$query)->with('success', 'Nama sub-modul berhasil diperbarui');
    }

    public function update(string $id): View|RedirectResponse
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data']['module'])) {
            return redirect()->route('admin.modules.index')->with('error', 'Modul tidak ditemukan');
        }

        $levels = $this->usecase->getLevels()['data']['levels'] ?? [];

        return view('_admin.modules.update', [
            'module' => (object) $data['data']['module'],
            'levels' => $levels,
            'page' => $this->page,
        ]);
    }

    public function doUpdate(string $id, Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'cefr' => 'required|in:A1,A2,B1,B2,C1,C2',
            'level_id' => 'required|string|max:20',
            'group' => 'required|string|max:100',
            'objective' => 'nullable|string',
            'complexity' => 'required|in:easy,medium,hard',
            'estimated_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'order_index' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $ext = strtolower($file->guessExtension() ?: 'jpg');
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $ext = 'jpg';
            }
            $filename = 'banner_'.time().'_'.uniqid().'.'.$ext;
            $destinationPath = public_path('uploads/modules');
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['banner_url'] = '/uploads/modules/'.$filename;
        }

        $res = $this->usecase->update($id, $data);

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.modules.detail', $id)->with('success', 'Modul kurikulum berhasil diperbarui');
    }

    public function delete(string $id): RedirectResponse
    {
        $res = $this->usecase->delete($id);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.modules.index')->with('success', 'Modul kurikulum berhasil dihapus');
    }

    public function toggleStatus(string $id): RedirectResponse
    {
        $res = $this->usecase->toggleStatus($id);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $statusText = $res['data']['is_active'] ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Modul {$id} berhasil {$statusText}.");
    }

    public function doCreateSection(string $id, Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'section_type' => 'required|in:theory,vocab,dialogue,practice,quiz',
            'sub_module_id' => 'nullable|string|max:36',
            'order_index' => 'nullable|integer|min:1',
        ]);

        $content = $this->extractSectionContent($request, $request->input('section_type'));

        $res = $this->usecase->createSection($id, [
            'title' => $request->input('title'),
            'section_type' => $request->input('section_type'),
            'sub_module_id' => $request->input('sub_module_id'),
            'order_index' => $request->input('order_index'),
            'content' => $content,
        ]);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $activeTab = $request->input('sub_module_id') ? ('?sub_module='.$request->input('sub_module_id')) : '';

        return redirect()->to(route('admin.modules.detail', $id).$activeTab)->with('success', 'Bagian materi berhasil ditambahkan');
    }

    public function doUpdateSection(string $id, string $sectionId, Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'section_type' => 'required|in:theory,vocab,dialogue,practice,quiz',
            'sub_module_id' => 'nullable|string|max:36',
            'order_index' => 'nullable|integer|min:1',
        ]);

        $existingSec = DB::table(DatabaseConst::MODULE_SECTIONS())->where('id', $sectionId)->where('module_id', $id)->first();
        $existingContent = ! empty($existingSec?->content) ? (is_array($existingSec->content) ? $existingSec->content : (json_decode($existingSec->content, true) ?: [])) : [];

        $content = $this->extractSectionContent($request, $request->input('section_type'), $existingContent);

        $res = $this->usecase->updateSection($id, $sectionId, [
            'title' => $request->input('title'),
            'section_type' => $request->input('section_type'),
            'sub_module_id' => $request->input('sub_module_id'),
            'order_index' => $request->input('order_index'),
            'content' => $content,
        ]);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $activeTab = $request->input('sub_module_id') ? ('?sub_module='.$request->input('sub_module_id')) : '';

        return redirect()->to(route('admin.modules.detail', $id).$activeTab)->with('success', 'Bagian materi berhasil diperbarui');
    }

    public function doDeleteSection(string $id, string $sectionId, Request $request): RedirectResponse
    {
        $res = $this->usecase->deleteSection($id, $sectionId);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        $activeTab = $request->input('sub_module_id') ? ('?sub_module='.$request->input('sub_module_id')) : '';

        return redirect()->to(route('admin.modules.detail', $id).$activeTab)->with('success', 'Bagian materi berhasil dihapus');
    }

    public function doGenerateAiSection(string $id, Request $request): RedirectResponse
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);

        $request->validate([
            'sub_module_id' => 'nullable|string',
            'scope' => 'required|in:single,all',
            'custom_instructions' => 'nullable|string|max:1000',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,doc|max:20480',
            'is_progressive' => 'nullable|boolean',
        ]);

        $subModuleId = ($request->input('scope') === 'all') ? null : $request->input('sub_module_id');

        $options = [
            'sub_module_id' => $subModuleId,
            'custom_instructions' => $request->input('custom_instructions'),
            'replace' => $request->boolean('replace'),
        ];

        // 1. If new document uploaded in request
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $extracted = $this->extractUploadedDocument($file);
            $options = array_merge($options, $extracted);

            if (! empty($extracted['document_text'])) {
                Cache::put('module_doc_text_'.$id, $extracted['document_text'], now()->addHours(24));
            }
            if (! empty($extracted['document_base64'])) {
                Cache::put('module_doc_base64_'.$id, $extracted['document_base64'], now()->addHours(24));
                Cache::put('module_doc_mime_'.$id, $extracted['document_mime_type'] ?? 'application/pdf', now()->addHours(24));
            }
            Cache::put('module_doc_name_'.$id, $file->getClientOriginalName(), now()->addHours(24));
        } else {
            // 2. Fall back to cached document from add page or previous scan
            $cachedText = Cache::get('module_doc_text_'.$id);
            $cachedBase64 = Cache::get('module_doc_base64_'.$id);
            $cachedMime = Cache::get('module_doc_mime_'.$id);

            if (! empty($cachedText)) {
                $options['document_text'] = $cachedText;
            }
            if (! empty($cachedBase64)) {
                $options['document_base64'] = $cachedBase64;
                $options['document_mime_type'] = $cachedMime;
            }
        }

        $res = $this->usecase->generateMaterialsWithAI($id, $options);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? 'Gagal membuat materi dengan AI');
        }

        $count = $res['data']['inserted_count'] ?? 0;
        $activeTab = $subModuleId ? ('?sub_module='.$subModuleId) : '';
        $wizardParam = ($request->boolean('is_progressive') || $request->has('wizard'))
            ? ($activeTab ? '&wizard=1' : '?wizard=1')
            : '';

        $skillLabel = $subModuleId ? 'sub-modul' : 'seluruh sub-modul';

        return redirect()->to(route('admin.modules.detail', $id).$activeTab.$wizardParam)
            ->with('success', "Berhasil! {$count} bagian materi berhasil disusun otomatis oleh AI untuk {$skillLabel}.");
    }

    private function extractUploadedDocument(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType() ?: 'application/octet-stream';
        $path = $file->getRealPath();

        // 1. Word Document (.docx / .doc)
        if (in_array($ext, ['docx', 'doc'])) {
            $extractedText = '';
            if ($ext === 'docx' && class_exists('ZipArchive')) {
                $zip = new \ZipArchive;
                if ($zip->open($path) === true) {
                    $xml = $zip->getFromName('word/document.xml');
                    if ($xml !== false) {
                        $extractedText = trim(strip_tags(str_replace(['<w:p>', '</w:p>'], ["\n", "\n"], $xml)));
                    }
                    $zip->close();
                }
            }
            if (empty($extractedText)) {
                $raw = @file_get_contents($path);
                if ($raw) {
                    $extractedText = preg_replace('/[^\x20-\x7E\r\n\t]/', ' ', $raw);
                    $extractedText = trim(preg_replace('/\s+/', ' ', $extractedText));
                }
            }

            return ['document_text' => $extractedText];
        }

        // 2. Images & PDF (Multimodal Base64 & Text Extraction)
        $contents = @file_get_contents($path);
        if ($contents) {
            $base64 = base64_encode($contents);
            $extractedText = '';
            if ($ext === 'pdf') {
                $mime = 'application/pdf';
                // Extract plain text streams from PDF
                preg_match_all('/\((.*?)\)\s*Tj/s', $contents, $matches);
                if (! empty($matches[1])) {
                    $extractedText = implode(' ', $matches[1]);
                }
                if (empty($extractedText)) {
                    preg_match_all('/[a-zA-Z0-9.,;:!?\'"\-\s]{4,}/', $contents, $words);
                    if (! empty($words[0])) {
                        $extractedText = implode(' ', array_slice($words[0], 0, 3000));
                    }
                }
            } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                $mime = 'image/jpeg';
            } elseif ($ext === 'png') {
                $mime = 'image/png';
            }
            $result = [
                'document_base64' => $base64,
                'document_mime_type' => $mime,
            ];
            if (! empty($extractedText)) {
                $result['document_text'] = trim($extractedText);
            }

            return $result;
        }

        return [];
    }

    private function extractSectionContent(Request $request, string $type, array $existingContent = []): array
    {
        // 1. Structured JSON: Decoded directly from JSON mode or serialized visual form
        if ($request->filled('content_json')) {
            $decoded = json_decode($request->input('content_json'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && ! empty($decoded)) {
                return $decoded;
            }
        }

        switch ($type) {
            case 'theory':
                $rules = array_values(array_filter($request->input('theory_rules', [])));
                $content = $existingContent;
                $content['summary'] = $request->input('theory_summary', $content['summary'] ?? '');
                if (! empty($rules)) {
                    $content['rules'] = $rules;
                }
                if ($request->filled('theory_trap_title') || $request->filled('theory_trap_wrong')) {
                    $content['commonTrap'] = [
                        'trapTitle' => $request->input('theory_trap_title', ''),
                        'wrong' => $request->input('theory_trap_wrong', ''),
                        'correct' => $request->input('theory_trap_correct', ''),
                        'explanation' => $request->input('theory_trap_explanation', ''),
                    ];
                }

                return $content;

            case 'vocab':
                $items = [];
                foreach ($request->input('vocab_words', []) as $w) {
                    if (! empty($w['word'])) {
                        $items[] = [
                            'word' => trim($w['word']),
                            'phonetic' => trim($w['phonetic'] ?? ($w['ipa'] ?? '')),
                            'ipa' => trim($w['ipa'] ?? ($w['phonetic'] ?? '')),
                            'partOfSpeech' => trim($w['partOfSpeech'] ?? ''),
                            'meaning' => trim($w['meaningId'] ?? ($w['meaning'] ?? '')),
                            'meaningId' => trim($w['meaningId'] ?? ($w['meaning'] ?? '')),
                            'exampleSentence' => trim($w['exampleSentence'] ?? ''),
                        ];
                    }
                }

                return ['items' => $items, 'words' => $items];

            case 'dialogue':
                $lines = [];
                foreach ($request->input('dialogue_lines', []) as $l) {
                    if (! empty($l['text'])) {
                        $lines[] = ['speaker' => trim($l['speaker'] ?? 'Speaker'), 'text' => trim($l['text']), 'translation' => trim($l['translation'] ?? '')];
                    }
                }
                $content = $existingContent;
                $content['context'] = $request->input('dialogue_context', $content['context'] ?? '');
                if (! empty($lines)) {
                    $content['lines'] = $lines;
                }

                return $content;

            case 'practice':
                if ($request->has('cable_pairs')) {
                    $pairs = [];
                    foreach ($request->input('cable_pairs', []) as $p) {
                        if (! empty($p['left']) && ! empty($p['right'])) {
                            $pairs[] = ['left' => trim($p['left']), 'right' => trim($p['right'])];
                        }
                    }
                    $content = $existingContent;
                    $content['drills'] = [[
                        'id' => 'drill-cable-1',
                        'title' => 'Interactive Cable Connection (Vocab Match)',
                        'instruction' => $request->input('practice_instruction', 'Connect each vocabulary term on the left with its accurate meaning on the right.'),
                        'pairs' => $pairs,
                    ]];

                    return $content;
                }

                return $existingContent;

            case 'quiz':
                $content = $existingContent;
                if ($request->filled('quiz_passing_score')) {
                    $content['passingScore'] = (int) $request->input('quiz_passing_score');
                }
                if ($request->has('practicum_questions')) {
                    $content['practicumQuestions'] = array_values($request->input('practicum_questions', []));
                    unset($content['questions']);

                    return $content;
                }
                $questions = [];
                foreach ($request->input('quiz_questions', []) as $q) {
                    if (! empty($q['question'])) {
                        $options = array_values(array_filter($q['options'] ?? []));
                        $questions[] = [
                            'question' => trim($q['question']),
                            'options' => $options,
                            'answer' => trim($q['answer'] ?? ($options[0] ?? '')),
                            'explanation' => trim($q['explanation'] ?? ''),
                        ];
                    }
                }
                if (! empty($questions)) {
                    $content['questions'] = $questions;
                    unset($content['practicumQuestions']);
                }

                return $content;

            default:
                return $existingContent;
        }
    }
}
