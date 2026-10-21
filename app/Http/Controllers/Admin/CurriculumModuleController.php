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
        $stats = $this->usecase->getStats()['data'] ?? [
            'total' => 0,
            'learning_count' => 0,
            'exam_count' => 0,
        ];

        return view('_admin.modules.index', [
            'data' => $data,
            'levels' => $levels,
            'stats' => $stats,
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
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->all();

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/modules');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['banner_url'] = '/uploads/modules/' . $filename;
        }

        $res = $this->usecase->create($data);

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.modules.index')->with('success', 'Modul kurikulum berhasil ditambahkan');
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
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data = $request->all();

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/modules');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $data['banner_url'] = '/uploads/modules/' . $filename;
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
}
