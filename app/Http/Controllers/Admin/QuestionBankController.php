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
}
