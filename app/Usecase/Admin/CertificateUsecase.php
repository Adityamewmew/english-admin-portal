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
     * Update certificate template
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
