<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Usecase\Admin\CertificateUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CertificateController extends Controller
{
    protected array $page = [
        'route' => 'certificates',
        'title' => 'Manajemen Sertifikat',
    ];

    public function __construct(
        protected CertificateUsecase $usecase
    ) {}

    public function index(Request $request): View|Response
    {
        $activeTab = $request->get('tab', 'templates');
        $cefr = $request->get('cefr', 'all');
        $keywords = $request->get('keywords');

        $templatesData = $this->usecase->getTemplates();
        $templates = $templatesData['data']['templates'] ?? [];

        $issuedData = $this->usecase->getIssuedCertificates([
            'cefr' => $cefr,
            'keywords' => $keywords,
        ]);
        $issuedList = $issuedData['data']['list'] ?? [];
        $totalIssued = $issuedData['data']['total_issued'] ?? 0;

        return view('_admin.certificates.index', [
            'page' => $this->page,
            'activeTab' => $activeTab,
            'templates' => $templates,
            'issuedList' => $issuedList,
            'totalIssued' => $totalIssued,
            'cefr' => $cefr,
            'keywords' => $keywords,
        ]);
    }

    public function edit(string $cefr): View|RedirectResponse
    {
        $res = $this->usecase->getTemplateByCefr($cefr);
        $template = $res['data']['template'] ?? null;

        if (!$template) {
            return redirect()->route('admin.certificates.index')
                ->with('error', 'Template sertifikat tidak ditemukan.');
        }

        return view('_admin.certificates.edit', [
            'page' => $this->page,
            'template' => $template,
            'cefr' => strtoupper($cefr),
        ]);
    }

    public function doUpdate(Request $request, string $cefr): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructor_name' => 'nullable|string|max:150',
            'instructor_title' => 'nullable|string|max:150',
            'theme_color' => 'nullable|string|max:50',
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->only([
            'title',
            'description',
            'instructor_name',
            'instructor_title',
            'theme_color',
        ]);

        if ($request->hasFile('background_file')) {
            $file = $request->file('background_file');
            $filename = 'cert_' . strtolower($cefr) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $targetDir = public_path('uploads/certificates');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $data['background_url'] = '/uploads/certificates/' . $filename;
        }

        $res = $this->usecase->updateTemplate($cefr, $data);

        if (!$res['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $res['message'] ?? 'Gagal memperbarui template.');
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', "Template sertifikat CEFR {$cefr} berhasil diperbarui.");
    }
}
