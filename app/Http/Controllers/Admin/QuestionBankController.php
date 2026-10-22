<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\QuestionBankUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuestionBankController extends Controller
{
    protected array $page = [
        'route' => 'questions',
        'title' => 'Bank Soal (Item Bank)',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected QuestionBankUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $cefr = $request->get('cefr', 'all');
        $skill = $request->get('skill', 'all');
        $keywords = $request->get('keywords');

        $data = $this->usecase->getAll([
            'cefr' => $cefr,
            'skill' => $skill,
            'keywords' => $keywords,
        ]);
        $data = $data['data']['list'] ?? [];

        return view('_admin.questions.index', [
            'data' => $data,
            'page' => $this->page,
            'cefr' => $cefr,
            'skill' => $skill,
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

        return view('_admin.questions.detail', [
            'item' => (object) $data['data']['item'],
            'page' => $this->page,
        ]);
    }

    public function add(): View
    {
        return view('_admin.questions.add', [
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $request->validate([
            'skill' => 'required|string|in:mcq,listening,speaking,writing,grammar,vocabulary,reading',
            'cefr' => 'required|in:A1,A2,B1,B2,C1,C2',
            'type' => 'required|string|in:MCQ,PROMPT,open_ended,speaking',
            'question' => 'required|string',
            'answer' => 'required|string',
            'options' => 'nullable|array',
            'audio_script' => 'nullable|string',
            'audio_url' => 'nullable|string',
            'explanation' => 'nullable|string',
        ]);

        $res = $this->usecase->create($request->all());

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Soal placement test berhasil ditambahkan');
    }

    public function update(string $id): View|RedirectResponse
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data']['item'])) {
            return redirect()->route('admin.questions.index')->with('error', 'Soal tidak ditemukan');
        }

        return view('_admin.questions.update', [
            'item' => (object) $data['data']['item'],
            'page' => $this->page,
        ]);
    }

    public function doUpdate(string $id, Request $request): RedirectResponse
    {
        $request->validate([
            'skill' => 'required|string|in:mcq,listening,speaking,writing,grammar,vocabulary,reading',
            'cefr' => 'required|in:A1,A2,B1,B2,C1,C2',
            'type' => 'required|string|in:MCQ,PROMPT,open_ended,speaking',
            'question' => 'required|string',
            'answer' => 'required|string',
            'options' => 'nullable|array',
            'audio_script' => 'nullable|string',
            'audio_url' => 'nullable|string',
            'explanation' => 'nullable|string',
        ]);

        $res = $this->usecase->update($id, $request->all());

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.questions.detail', $id)->with('success', 'Soal placement test berhasil diperbarui');
    }

    public function delete(string $id): RedirectResponse
    {
        $res = $this->usecase->delete($id);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Soal placement test berhasil dihapus');
    }
}
