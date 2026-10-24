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
                    'cm.is_active',
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
                ->when(isset($filterData['status']) && $filterData['status'] !== 'all', function ($q) use ($filterData) {
                    $isActive = in_array($filterData['status'], ['active', '1', 1, true], true);

                    return $q->where('cm.is_active', $isActive);
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

            $activeCount = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->count();

            $learningCount = $total - $examCount;

            return Response::buildSuccess([
                'total' => $total,
                'learning_count' => $learningCount,
                'exam_count' => $examCount,
                'active_count' => $activeCount,
                'inactive_count' => $total - $activeCount,
            ]);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildSuccess([
                'total' => 0,
                'learning_count' => 0,
                'exam_count' => 0,
                'active_count' => 0,
                'inactive_count' => 0,
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
            $id = ! empty($data['id']) ? trim($data['id']) : 'MOD-'.($data['cefr'] ?? 'A1').'-'.strtoupper(substr(uniqid(), -5));

            $existing = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->first();

            if ($existing) {
                return Response::buildErrorValidation(['id' => 'Module ID already exists']);
            }

            $levelId = $data['level_id'] ?? (($data['cefr'] ?? 'A1').'.1');
            $maxOrder = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('level_id', $levelId)
                ->whereNull('deleted_at')
                ->max('order_index') ?? 0;
            $orderIndex = ! empty($data['order_index']) ? (int) $data['order_index'] : ((int) $maxOrder + 1);

            $insertData = [
                'id' => $id,
                'level_id' => $levelId,
                'title' => $data['title'],
                'cefr' => $data['cefr'],
                'group' => $data['group'] ?? 'General English',
                'objective' => $data['objective'] ?? '',
                'complexity' => $data['complexity'] ?? 'medium',
                'estimated_minutes' => (int) ($data['estimated_minutes'] ?? 15),
                'is_exam' => ! empty($data['is_exam']),
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
                'passing_score' => (int) ($data['passing_score'] ?? 70),
                'order_index' => $orderIndex,
                'banner_url' => $data['banner_url'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::CURRICULUM_MODULES())->insert($insertData);

            // Auto-provision 5 standard sub-modules (Reading -> Listening -> Writing -> Speaking -> Assessment)
            $cleanId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $id));
            $skills = [
                ['skill' => 'reading', 'title' => 'Reading & Vocabulary', 'order' => 1],
                ['skill' => 'listening', 'title' => 'Listening & Spoken Dialogue', 'order' => 2],
                ['skill' => 'writing', 'title' => 'Sentence Building & Writing', 'order' => 3],
                ['skill' => 'speaking', 'title' => 'Speaking & Pronunciation', 'order' => 4],
                ['skill' => 'assessment', 'title' => 'Unit Final Mission & Assessment', 'order' => 5],
            ];

            $subModuleRows = [];
            $firstSubModuleId = null;
            foreach ($skills as $s) {
                $smId = 'sub-'.$cleanId.'-'.$s['skill'];
                if (! $firstSubModuleId) {
                    $firstSubModuleId = $smId;
                }
                $subModuleRows[] = [
                    'id' => $smId,
                    'module_id' => $id,
                    'skill_type' => $s['skill'],
                    'title' => $s['title'],
                    'order_index' => $s['order'],
                    'created_at' => now(),
                ];
            }

            DB::table(DatabaseConst::SUB_MODULES())->insert($subModuleRows);

            return Response::buildSuccess([
                'id' => $id,
                'module' => $insertData,
                'first_sub_module_id' => $firstSubModuleId,
            ], ResponseConst::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function updateSubModule(string $moduleId, string $subModuleId, array $data): array
    {
        try {
            $updated = DB::table(DatabaseConst::SUB_MODULES())
                ->where('id', $subModuleId)
                ->where('module_id', $moduleId)
                ->update([
                    'title' => $data['title'],
                ]);

            if (! $updated) {
                return Response::buildErrorNotFound('Sub-modul tidak ditemukan');
            }

            return Response::buildSuccess(['id' => $subModuleId]);
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

            if (! $existing) {
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

            if (array_key_exists('is_active', $data)) {
                $updateData['is_active'] = (bool) $data['is_active'];
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

            if (! $existing) {
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

    public function toggleStatus(string $id): array
    {
        try {
            $existing = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (! $existing) {
                return Response::buildErrorNotFound('Modul kurikulum tidak ditemukan');
            }

            $newStatus = ! ((bool) ($existing->is_active ?? true));

            DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $id)
                ->update([
                    'is_active' => $newStatus,
                    'updated_at' => now(),
                ]);

            return Response::buildSuccess([
                'id' => $id,
                'is_active' => $newStatus,
            ], ResponseConst::HTTP_SUCCESS);
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

    public function createSection(string $moduleId, array $data): array
    {
        try {
            $maxOrder = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('module_id', $moduleId)
                ->max('order_index') ?? 0;

            $id = 'sec-'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moduleId)).'-'.substr(uniqid(), -6);

            $insertData = [
                'id' => $id,
                'module_id' => $moduleId,
                'sub_module_id' => ! empty($data['sub_module_id']) ? $data['sub_module_id'] : null,
                'section_type' => $data['section_type'],
                'title' => $data['title'],
                'content' => is_array($data['content']) ? json_encode($data['content']) : $data['content'],
                'order_index' => isset($data['order_index']) ? (int) $data['order_index'] : ($maxOrder + 1),
                'created_at' => now(),
            ];

            DB::table(DatabaseConst::MODULE_SECTIONS())->insert($insertData);

            return Response::buildSuccess(['id' => $id], ResponseConst::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function updateSection(string $moduleId, string $sectionId, array $data): array
    {
        try {
            $existing = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('id', $sectionId)
                ->where('module_id', $moduleId)
                ->first();

            if (! $existing) {
                return Response::buildErrorNotFound('Bagian materi tidak ditemukan');
            }

            $updateData = [
                'title' => $data['title'] ?? $existing->title,
                'section_type' => $data['section_type'] ?? $existing->section_type,
                'order_index' => isset($data['order_index']) ? (int) $data['order_index'] : $existing->order_index,
            ];

            if (array_key_exists('sub_module_id', $data)) {
                $updateData['sub_module_id'] = ! empty($data['sub_module_id']) ? $data['sub_module_id'] : null;
            }

            if (isset($data['content'])) {
                $updateData['content'] = is_array($data['content']) ? json_encode($data['content']) : $data['content'];
            }

            DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('id', $sectionId)
                ->update($updateData);

            return Response::buildSuccess(['id' => $sectionId]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function deleteSection(string $moduleId, string $sectionId): array
    {
        try {
            $deleted = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('id', $sectionId)
                ->where('module_id', $moduleId)
                ->delete();

            if (! $deleted) {
                return Response::buildErrorNotFound('Bagian materi tidak ditemukan');
            }

            return Response::buildSuccess(['id' => $sectionId]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function generateMaterialsWithAI(string $moduleId, array $options = []): array
    {
        return app(GenerateModuleAiMaterialsUsecase::class)->execute($moduleId, $options);
    }
}
