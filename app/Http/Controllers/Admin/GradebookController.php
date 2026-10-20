<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\GradebookUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GradebookController extends Controller
{
    protected array $page = [
        'route' => 'gradebook',
        'title' => 'Rekap Nilai Siswa',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected GradebookUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $response = $this->usecase->getAll([
            'keywords' => $request->get('keywords'),
            'level' => $request->get('level'),
        ]);

        $data = $response['data']['list'] ?? [];
        $summary = $response['data']['summary'] ?? [];

        return view('_admin.gradebook.index', [
            'data' => $data,
            'summary' => $summary,
            'page' => $this->page,
            'keywords' => $request->get('keywords'),
            'level' => $request->get('level'),
        ]);
    }

    public function detail(string $id): View|RedirectResponse|Response
    {
        $response = $this->usecase->getStudentScoreDetail($id);

        if (empty($response['data'])) {
            return redirect()
                ->intended($this->baseRedirect)
                ->with('error', $response['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return view('_admin.gradebook.detail', [
            'student' => $response['data']['student'] ?? null,
            'modules' => $response['data']['modules'] ?? [],
            'page' => $this->page,
        ]);
    }
}
