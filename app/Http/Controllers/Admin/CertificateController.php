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

    public function add(): View
    {
        return view('_admin.certificates.add', [
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $request->validate([
            'cefr' => 'required|string|in:A1,A2,B1,B2,C1,C2',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'instructor_name' => 'nullable|string|max:150',
            'instructor_title' => 'nullable|string|max:150',
            'theme_color' => 'nullable|string|max:50',
            'background_file' => 'required|image|mimes:jpeg,png,jpg,webp|max:8192',
        ], [
            'background_file.required' => 'File gambar desain sertifikat (JPG, PNG, WEBP) wajib diunggah.',
            'cefr.required' => 'Pilih kategori level CEFR.',
        ]);

        $cefr = strtoupper($request->input('cefr'));
        $data = [
            'cefr' => $cefr,
            'title' => $request->input('title') ?: "Certificate of CEFR {$cefr} English Proficiency",
            'description' => $request->input('description') ?: "Has demonstrated comprehensive competence and communicative proficiency in CEFR {$cefr}.",
            'instructor_name' => $request->input('instructor_name') ?: 'Mr. Khoirul, M.Pd.',
            'instructor_title' => $request->input('instructor_title') ?: 'Head of English Academic Affairs',
            'theme_color' => $request->input('theme_color') ?: '#0D3B66',
        ];

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

        $res = $this->usecase->createTemplate($data);

        if (!$res['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $res['message'] ?? 'Gagal membuat template sertifikat.');
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', "Template sertifikat CEFR {$cefr} berhasil ditambahkan.");
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
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
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

    public function delete(string $cefr): RedirectResponse
    {
        $res = $this->usecase->deleteTemplate($cefr);

        if (!$res['success']) {
            return redirect()->back()
                ->with('error', $res['message'] ?? 'Gagal menghapus template.');
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', "Template sertifikat CEFR {$cefr} berhasil dihapus.");
    }
}
