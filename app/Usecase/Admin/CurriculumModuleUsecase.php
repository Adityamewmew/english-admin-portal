<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurriculumModuleUsecase extends Usecase
{
    public function getAll(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::CURRICULUM_MODULES().' as cm')
                ->leftJoin(DatabaseConst::CURRICULUM_LEVELS().' as cl', 'cm.level_id', '=', 'cl.id')
                ->select([
                    'cm.id',
                    'cm.level_id',
                    'cl.title as level_title',
                    'cm.title',
                    'cm.cefr',
                    'cm.group',
                    'cm.objective',
                    'cm.complexity',
                    'cm.estimated_minutes',
                    'cm.is_exam',
                    'cm.passing_score',
                    'cm.order_index',
                    DB::raw('(SELECT COUNT(*) FROM '.DatabaseConst::MODULE_SECTIONS().' WHERE module_id = cm.id) as sections_count'),
                ])
                ->whereNull('cm.deleted_at')
                ->when($filterData['cefr'] ?? false, function ($q, $cefr) {
                    if ($cefr !== 'all') {
                        return $q->where('cm.cefr', $cefr);
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('cm.title', 'ilike', '%'.$keywords.'%')
                            ->orWhere('cm.objective', 'ilike', '%'.$keywords.'%')
                            ->orWhere('cm.group', 'ilike', '%'.$keywords.'%')
                            ->orWhere('cm.id', 'ilike', '%'.$keywords.'%');
                    });
                })
                ->orderBy('cm.order_index', 'asc')
                ->orderBy('cm.id', 'asc');

            $data = empty($filterData['no_pagination'])
                ? $query->paginate(20)
                : $query->get();

            if (! empty($filterData) && method_exists($data, 'appends')) {
                $data->appends($filterData);
            }

            return Response::buildSuccess(
                ['list' => $data],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getByID(string $id): array
    {
        try {
            $module = DB::table(DatabaseConst::CURRICULUM_MODULES().' as cm')
                ->leftJoin(DatabaseConst::CURRICULUM_LEVELS().' as cl', 'cm.level_id', '=', 'cl.id')
                ->select([
                    'cm.*',
                    'cl.title as level_title',
                    'cl.description as level_description',
                ])
                ->where('cm.id', $id)
                ->whereNull('cm.deleted_at')
                ->first();

            if (! $module) {
                return Response::buildErrorNotFound('Modul kurikulum tidak ditemukan');
            }

            $sections = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('module_id', $id)
                ->orderBy('order_index', 'asc')
                ->get()
                ->map(function ($sec) {
                    $sec->content = is_string($sec->content) ? json_decode($sec->content, true) : $sec->content;

                    return $sec;
                });

            $subModules = DB::table(DatabaseConst::SUB_MODULES())
                ->where('module_id', $id)
                ->orderBy('order_index', 'asc')
                ->get();

            return Response::buildSuccess([
                'module' => $module,
                'sections' => $sections,
                'sub_modules' => $subModules,
            ]);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getLevels(): array
    {
        try {
            $levels = DB::table(DatabaseConst::CURRICULUM_LEVELS())
                ->orderBy('order_index', 'asc')
                ->get();

            return Response::buildSuccess(['levels' => $levels]);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
