<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CertificateUsecase extends Usecase
{
    /**
     * Get all master certificate templates (A1 - C2)
     */
    public function getTemplates(): array
    {
        try {
            $templates = DB::table(DatabaseConst::CERTIFICATE_TEMPLATES())
                ->orderBy('cefr', 'asc')
                ->get();

            // Count how many certificates have been issued per CEFR
            $counts = DB::table(DatabaseConst::USER_CERTIFICATES())
                ->select('cefr_level', DB::raw('count(*) as count'))
                ->groupBy('cefr_level')
                ->pluck('count', 'cefr_level')
                ->toArray();

            $list = $templates->map(function ($t) use ($counts) {
                $t->issued_count = $counts[$t->cefr] ?? 0;
                return $t;
            });

            return Response::buildSuccess([
                'templates' => $list,
            ]);
        } catch (Exception $e) {
            Log::error('CertificateUsecase::getTemplates Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Get single template by CEFR
     */
    public function getTemplateByCefr(string $cefr): array
    {
        try {
            $template = DB::table(DatabaseConst::CERTIFICATE_TEMPLATES())
                ->where('cefr', strtoupper($cefr))
                ->first();

            if (!$template) {
                return Response::buildErrorNotFound('Template sertifikat tidak ditemukan');
            }

            return Response::buildSuccess([
                'template' => $template,
            ]);
        } catch (Exception $e) {
            Log::error('CertificateUsecase::getTemplateByCefr Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Create new certificate template
     */
    public function createTemplate(array $data): array
    {
        try {
            $cefr = strtoupper(trim($data['cefr'] ?? ''));
            if (empty($cefr)) {
                return Response::buildErrorValidation(['cefr' => ['Kategori level CEFR wajib dipilih.']]);
            }

            $exists = DB::table(DatabaseConst::CERTIFICATE_TEMPLATES())
                ->where('cefr', $cefr)
                ->exists();

            if ($exists) {
                return Response::buildErrorConflict("Template sertifikat untuk CEFR {$cefr} sudah ada.");
            }

            $insertData = [
                'cefr' => $cefr,
                'title' => $data['title'] ?? "Certificate of CEFR {$cefr} English Proficiency",
                'description' => $data['description'] ?? null,
                'instructor_name' => $data['instructor_name'] ?? 'KHOIRUL ANWAR, M.Pd',
                'instructor_title' => $data['instructor_title'] ?? 'Director of Studies',
                'theme_color' => $data['theme_color'] ?? '#0D3B66',
                'name_x_percent' => (int) ($data['name_x_percent'] ?? 50),
                'name_y_percent' => (int) ($data['name_y_percent'] ?? 34),
                'name_font_size' => (int) ($data['name_font_size'] ?? 26),
                'scores_x_percent' => (int) ($data['scores_x_percent'] ?? 50),
                'scores_y_percent' => (int) ($data['scores_y_percent'] ?? 52),
                'scores_font_size' => (int) ($data['scores_font_size'] ?? 10),
                'date_x_percent' => (int) ($data['date_x_percent'] ?? 18),
                'date_y_percent' => (int) ($data['date_y_percent'] ?? 80),
                'date_font_size' => (int) ($data['date_font_size'] ?? 8),
                'place_x_percent' => (int) ($data['place_x_percent'] ?? 50),
                'place_y_percent' => (int) ($data['place_y_percent'] ?? 80),
                'place_font_size' => (int) ($data['place_font_size'] ?? 8),
                'ref_no_x_percent' => (int) ($data['ref_no_x_percent'] ?? 82),
                'ref_no_y_percent' => (int) ($data['ref_no_y_percent'] ?? 80),
                'ref_no_font_size' => (int) ($data['ref_no_font_size'] ?? 8),
                'orientation' => $data['orientation'] ?? 'portrait',
                'place_of_entry' => $data['place_of_entry'] ?? 'PBN Kelapa Gading, D.18 Jember East Java',
                'background_url' => $data['background_url'] ?? null,
                'signature_url' => $data['signature_url'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::CERTIFICATE_TEMPLATES())->insert($insertData);

            return Response::buildSuccessCreated(null, "Template sertifikat CEFR {$cefr} berhasil dibuat.");
        } catch (Exception $e) {
            Log::error('CertificateUsecase::createTemplate Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Delete certificate template
     */
    public function deleteTemplate(string $cefr): array
    {
        try {
            DB::table(DatabaseConst::CERTIFICATE_TEMPLATES())
                ->where('cefr', strtoupper($cefr))
                ->delete();

            return Response::buildSuccess(null, ResponseConst::HTTP_SUCCESS, "Template sertifikat CEFR {$cefr} berhasil dihapus.");
        } catch (Exception $e) {
            Log::error('CertificateUsecase::deleteTemplate Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Get single template by CEFR
     */
    public function updateTemplate(string $cefr, array $data): array
    {
        try {
            $updateData = [
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? null,
                'instructor_name' => $data['instructor_name'] ?? null,
                'instructor_title' => $data['instructor_title'] ?? null,
                'theme_color' => $data['theme_color'] ?? '#0D3B66',
                'updated_at' => now(),
            ];

            foreach ([
                'name_x_percent', 'name_y_percent', 'name_font_size',
                'scores_x_percent', 'scores_y_percent', 'scores_font_size',
                'date_x_percent', 'date_y_percent', 'date_font_size',
                'place_x_percent', 'place_y_percent', 'place_font_size',
                'ref_no_x_percent', 'ref_no_y_percent', 'ref_no_font_size',
            ] as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = (int) $data[$field];
                }
            }

            if (isset($data['orientation'])) {
                $updateData['orientation'] = $data['orientation'];
            }

            if (isset($data['place_of_entry'])) {
                $updateData['place_of_entry'] = $data['place_of_entry'];
            }

            if (!empty($data['background_url'])) {
                $updateData['background_url'] = $data['background_url'];
            }

            if (!empty($data['signature_url'])) {
                $updateData['signature_url'] = $data['signature_url'];
            }

            DB::table(DatabaseConst::CERTIFICATE_TEMPLATES())
                ->where('cefr', strtoupper($cefr))
                ->update($updateData);

            return Response::buildSuccess(null, ResponseConst::HTTP_SUCCESS, 'Template sertifikat berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('CertificateUsecase::updateTemplate Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Get issued certificates with search, filter, and pagination
     */
    public function getIssuedCertificates(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::USER_CERTIFICATES() . ' as uc')
                ->leftJoin(DatabaseConst::USER() . ' as u', 'uc.user_id', '=', 'u.id')
                ->select([
                    'uc.id',
                    'uc.certificate_number',
                    'uc.user_id',
                    'uc.user_name as cert_user_name',
                    'u.name as current_user_name',
                    'u.email as user_email',
                    'uc.cefr_level',
                    'uc.score',
                    'uc.scores_breakdown',
                    'uc.completed_at',
                    'uc.issued_at',
                ])
                ->when($filterData['cefr'] ?? false, function ($q, $cefr) {
                    if ($cefr !== 'all') {
                        return $q->where('uc.cefr_level', strtoupper($cefr));
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('uc.certificate_number', 'ilike', '%' . $keywords . '%')
                            ->orWhere('uc.user_name', 'ilike', '%' . $keywords . '%')
                            ->orWhere('u.name', 'ilike', '%' . $keywords . '%')
                            ->orWhere('u.email', 'ilike', '%' . $keywords . '%');
                    });
                })
                ->orderBy('uc.issued_at', 'desc');

            $data = $query->paginate(15);

            if (!empty($filterData) && method_exists($data, 'appends')) {
                $data->appends($filterData);
            }

            $totalIssued = DB::table(DatabaseConst::USER_CERTIFICATES())->count();

            return Response::buildSuccess([
                'list' => $data,
                'total_issued' => $totalIssued,
            ]);
        } catch (Exception $e) {
            Log::error('CertificateUsecase::getIssuedCertificates Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }
}
