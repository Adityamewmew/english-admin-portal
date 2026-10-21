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
                    'cm.banner_url',
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

    public function getStats(): array
    {
        try {
            $total = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->whereNull('deleted_at')
                ->count();

            $examCount = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->whereNull('deleted_at')
                ->where('is_exam', true)
                ->count();

            $learningCount = $total - $examCount;

            return Response::buildSuccess([
                'total' => $total,
                'learning_count' => $learningCount,
                'exam_count' => $examCount,
            ]);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildSuccess([
                'total' => 0,
                'learning_count' => 0,
                'exam_count' => 0,
            ]);
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

    public function create(array $data): array
    {
        try {
            $id = !empty($data['id']) ? trim($data['id']) : 'MOD-' . ($data['cefr'] ?? 'A1') . '-' . strtoupper(substr(uniqid(), -5));

            $existing = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->first();

            if ($existing) {
                return Response::buildErrorValidation(['id' => 'Module ID already exists']);
            }

            $insertData = [
                'id' => $id,
                'level_id' => $data['level_id'] ?? ($data['cefr'] . '.1'),
                'title' => $data['title'],
                'cefr' => $data['cefr'],
                'group' => $data['group'] ?? 'General English',
                'objective' => $data['objective'] ?? '',
                'complexity' => $data['complexity'] ?? 'medium',
                'estimated_minutes' => (int) ($data['estimated_minutes'] ?? 15),
                'is_exam' => !empty($data['is_exam']),
                'passing_score' => (int) ($data['passing_score'] ?? 70),
                'order_index' => (int) ($data['order_index'] ?? 1),
                'banner_url' => $data['banner_url'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::CURRICULUM_MODULES())->insert($insertData);

            return Response::buildSuccess(['id' => $id, 'module' => $insertData], ResponseConst::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function update(string $id, array $data): array
    {
        try {
            $existing = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$existing) {
                return Response::buildErrorNotFound('Modul kurikulum tidak ditemukan');
            }

            $updateData = [
                'level_id' => $data['level_id'] ?? $existing->level_id,
                'title' => $data['title'] ?? $existing->title,
                'cefr' => $data['cefr'] ?? $existing->cefr,
                'group' => $data['group'] ?? $existing->group,
                'objective' => $data['objective'] ?? $existing->objective,
                'complexity' => $data['complexity'] ?? $existing->complexity,
                'estimated_minutes' => isset($data['estimated_minutes']) ? (int) $data['estimated_minutes'] : $existing->estimated_minutes,
                'is_exam' => isset($data['is_exam']) ? (bool) $data['is_exam'] : $existing->is_exam,
                'passing_score' => isset($data['passing_score']) ? (int) $data['passing_score'] : $existing->passing_score,
                'order_index' => isset($data['order_index']) ? (int) $data['order_index'] : $existing->order_index,
                'updated_at' => now(),
            ];

            if (array_key_exists('banner_url', $data)) {
                $updateData['banner_url'] = $data['banner_url'];
            }

            DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->update($updateData);

            return Response::buildSuccess(['id' => $id]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function delete(string $id): array
    {
        try {
            $existing = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$existing) {
                return Response::buildErrorNotFound('Modul kurikulum tidak ditemukan');
            }

            DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->update(['deleted_at' => now()]);

            return Response::buildSuccess(['id' => $id]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
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
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }
}
