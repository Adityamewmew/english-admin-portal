<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\CurriculumModuleUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $keywords = $request->get('keywords');

        $data = $this->usecase->getAll([
            'cefr' => $cefr,
            'keywords' => $keywords,
        ]);
        $data = $data['data']['list'] ?? [];

        $levels = $this->usecase->getLevels()['data']['levels'] ?? [];

        return view('_admin.modules.index', [
            'data' => $data,
            'levels' => $levels,
            'page' => $this->page,
            'cefr' => $cefr,
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

        return view('_admin.modules.detail', [
            'module' => (object) $data['data']['module'],
            'sections' => $data['data']['sections'],
            'subModules' => $data['data']['sub_modules'],
            'page' => $this->page,
        ]);
    }
}
