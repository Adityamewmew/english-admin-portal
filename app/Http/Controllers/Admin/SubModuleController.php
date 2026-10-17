<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\SubModuleUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubModuleController extends Controller
{
    protected array $page = [
        'route' => 'sub_modules',
        'title' => 'Sub Modules',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected SubModuleUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $moduleId = $request->get('module_id', 'all');
        $skillType = $request->get('skill_type', 'all');
        $keywords = $request->get('keywords');

        $data = $this->usecase->getAll([
            'module_id' => $moduleId,
            'skill_type' => $skillType,
            'keywords' => $keywords,
        ]);
        $data = $data['data']['list'] ?? [];

        $modules = $this->usecase->getModuleOptions()['data']['modules'] ?? [];

        return view('_admin.sub_modules.index', [
            'data' => $data,
            'modules' => $modules,
            'page' => $this->page,
            'moduleId' => $moduleId,
            'skillType' => $skillType,
            'keywords' => $keywords,
        ]);
    }

    public function detail(string $id): View|RedirectResponse|Response
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data']['sub_module'])) {
            return redirect()
                ->intended($this->baseRedirect)
                ->with('error', ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return view('_admin.sub_modules.detail', [
            'subModule' => (object) $data['data']['sub_module'],
            'sections' => $data['data']['sections'],
            'page' => $this->page,
        ]);
    }

    public function add(Request $request): View
    {
        $modules = $this->usecase->getModuleOptions()['data']['modules'] ?? [];
        $selectedModule = $request->get('module_id');

        return view('_admin.sub_modules.add', [
            'modules' => $modules,
            'selectedModule' => $selectedModule,
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $request->validate([
            'module_id' => 'required|string|max:20',
            'skill_type' => 'required|string|max:20',
            'title' => 'required|string|max:150',
            'order_index' => 'nullable|integer|min:1',
        ]);

        $res = $this->usecase->create($request->all());

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.sub_modules.index')->with('success', 'Sub-modul berhasil ditambahkan');
    }

    public function update(string $id): View|RedirectResponse
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data']['sub_module'])) {
            return redirect()->route('admin.sub_modules.index')->with('error', 'Sub-modul tidak ditemukan');
        }

        $modules = $this->usecase->getModuleOptions()['data']['modules'] ?? [];

        return view('_admin.sub_modules.update', [
            'subModule' => (object) $data['data']['sub_module'],
            'modules' => $modules,
            'page' => $this->page,
        ]);
    }

    public function doUpdate(string $id, Request $request): RedirectResponse
    {
        $request->validate([
            'module_id' => 'required|string|max:20',
            'skill_type' => 'required|string|max:20',
            'title' => 'required|string|max:150',
            'order_index' => 'nullable|integer|min:1',
        ]);

        $res = $this->usecase->update($id, $request->all());

        if (empty($res['data'])) {
            return back()->withInput()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.sub_modules.detail', $id)->with('success', 'Sub-modul berhasil diperbarui');
    }

    public function delete(string $id): RedirectResponse
    {
        $res = $this->usecase->delete($id);

        if (empty($res['data'])) {
            return back()->with('error', $res['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return redirect()->route('admin.sub_modules.index')->with('success', 'Sub-modul berhasil dihapus');
    }
}
