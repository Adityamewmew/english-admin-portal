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
            'place_of_entry' => 'nullable|string|max:255',
            'orientation' => 'nullable|string|in:portrait,landscape,square',
            'theme_color' => 'nullable|string|max:50',
            'name_y_percent' => 'nullable|integer|between:10,90',
            'name_font_size' => 'nullable|integer|between:14,72',
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
            'instructor_name' => $request->input('instructor_name') ?: 'KHOIRUL ANWAR, M.Pd',
            'instructor_title' => $request->input('instructor_title') ?: 'Director of Studies',
            'place_of_entry' => $request->input('place_of_entry') ?: 'PBN Kelapa Gading, D.18 Jember East Java',
            'orientation' => $request->input('orientation') ?: 'portrait',
            'theme_color' => $request->input('theme_color') ?: '#0D3B66',
            'name_y_percent' => (int) ($request->input('name_y_percent') ?: 48),
            'name_font_size' => (int) ($request->input('name_font_size') ?: 36),
        ];

        if ($request->hasFile('background_file')) {
            $data['background_url'] = $this->processCertificateImage($request->file('background_file'), $cefr);
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
            'place_of_entry' => 'nullable|string|max:255',
            'orientation' => 'nullable|string|in:portrait,landscape,square',
            'theme_color' => 'nullable|string|max:50',
            'name_y_percent' => 'nullable|integer|between:10,90',
            'name_font_size' => 'nullable|integer|between:14,72',
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
        ]);

        $data = $request->only([
            'title',
            'description',
            'instructor_name',
            'instructor_title',
            'place_of_entry',
            'orientation',
            'theme_color',
            'name_y_percent',
            'name_font_size',
        ]);

        if ($request->has('name_y_percent')) {
            $data['name_y_percent'] = (int) $request->input('name_y_percent');
        }
        if ($request->has('name_font_size')) {
            $data['name_font_size'] = (int) $request->input('name_font_size');
        }

        if ($request->hasFile('background_file')) {
            $data['background_url'] = $this->processCertificateImage($request->file('background_file'), $cefr);
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

    /**
     * Convert and optimize uploaded certificate image into persistent base64 data URL
     * and also save to public/uploads/certificates as fallback.
     */
    protected function processCertificateImage($file, string $cefr): string
    {
        $imageContent = file_get_contents($file->getRealPath());
        $mime = $file->getMimeType() ?: 'image/jpeg';

        if (extension_loaded('gd')) {
            $sourceImage = @imagecreatefromstring($imageContent);
            if ($sourceImage) {
                $origW = imagesx($sourceImage);
                $origH = imagesy($sourceImage);
                $maxDim = 1600;
                if ($origW > $maxDim || $origH > $maxDim) {
                    $scale = min($maxDim / $origW, $maxDim / $origH);
                    $newW = (int) round($origW * $scale);
                    $newH = (int) round($origH * $scale);
                    $resized = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                    imagedestroy($sourceImage);
                    $sourceImage = $resized;
                }
                ob_start();
                imagejpeg($sourceImage, null, 85);
                $imageContent = ob_get_clean();
                $mime = 'image/jpeg';
                imagedestroy($sourceImage);
            }
        }

        // Also save to disk if directory writable
        try {
            $targetDir = public_path('uploads/certificates');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $filename = 'cert_' . strtolower($cefr) . '_' . time() . '.jpg';
            @file_put_contents($targetDir . '/' . $filename, $imageContent);
        } catch (\Throwable $e) {
            // Ignore disk write errors, base64 is primary
        }

        $base64 = base64_encode($imageContent);
        return "data:{$mime};base64,{$base64}";
    }

    public function preview(Request $request, string $cefr): View|RedirectResponse
    {
        $res = $this->usecase->getTemplateByCefr($cefr);
        $template = $res['data']['template'] ?? null;

        if (!$template) {
            return redirect()->route('admin.certificates.index')
                ->with('error', 'Template sertifikat tidak ditemukan.');
        }

        $studentName = $request->query('student', 'Muhammad Aditya Pratama');
        $certificateNumber = $request->query('number', 'EDDY-' . strtoupper($cefr) . '-SAMPLE-' . date('Ym') . '-001');
        $issuedDate = $request->query('date', \Carbon\Carbon::now()->translatedFormat('d F Y'));
        $score = (int) $request->query('score', 95);

        return view('_admin.certificates.preview', [
            'page' => $this->page,
            'template' => $template,
            'cefr' => strtoupper($cefr),
            'studentName' => $studentName,
            'certificateNumber' => $certificateNumber,
            'issuedDate' => $issuedDate,
            'score' => $score,
        ]);
    }

    public function image(string $cefr): Response|RedirectResponse
    {
        $res = $this->usecase->getTemplateByCefr($cefr);
        $template = $res['data']['template'] ?? null;

        if (!$template || empty($template->background_url)) {
            abort(404, 'Gambar sertifikat tidak ditemukan.');
        }

        $bg = $template->background_url;

        if (str_starts_with($bg, 'data:')) {
            $parts = explode(',', $bg, 2);
            if (count($parts) === 2) {
                $header = $parts[0];
                $data = base64_decode($parts[1]);
                $mime = 'image/jpeg';
                if (preg_match('/data:([^;]+)/', $header, $matches)) {
                    $mime = $matches[1];
                }
                return response($data, 200, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="certificate-' . strtolower($cefr) . '.jpg"',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        if (str_starts_with($bg, 'http://') || str_starts_with($bg, 'https://')) {
            return redirect($bg);
        }

        $filePath = public_path(ltrim($bg, '/'));
        if (file_exists($filePath)) {
            return response()->file($filePath);
        }

        abort(404, 'File gambar tidak ditemukan.');
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
