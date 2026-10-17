<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubModuleUsecase extends Usecase
{
    public function getAll(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::SUB_MODULES().' as sm')
                ->leftJoin(DatabaseConst::CURRICULUM_MODULES().' as cm', 'sm.module_id', '=', 'cm.id')
                ->select([
                    'sm.id',
                    'sm.module_id',
                    'cm.title as module_title',
                    'cm.cefr as module_cefr',
                    'sm.skill_type',
                    'sm.title',
                    'sm.order_index',
                    'sm.created_at',
                    DB::raw('(SELECT COUNT(*) FROM '.DatabaseConst::MODULE_SECTIONS().' WHERE sub_module_id = sm.id) as sections_count'),
                ])
                ->whereNull('sm.deleted_at')
                ->when($filterData['module_id'] ?? false, function ($q, $moduleId) {
                    if ($moduleId !== 'all') {
                        return $q->where('sm.module_id', $moduleId);
                    }
                })
                ->when($filterData['skill_type'] ?? false, function ($q, $skill) {
                    if ($skill !== 'all') {
                        return $q->where('sm.skill_type', $skill);
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('sm.title', 'ilike', '%'.$keywords.'%')
                            ->orWhere('sm.id', 'ilike', '%'.$keywords.'%')
                            ->orWhere('cm.title', 'ilike', '%'.$keywords.'%');
                    });
                })
                ->orderBy('sm.module_id', 'asc')
                ->orderBy('sm.order_index', 'asc');

            $data = empty($filterData['no_pagination'])
                ? $query->paginate(20)
                : $query->get();

            if (!empty($filterData) && method_exists($data, 'appends')) {
                $data->appends($filterData);
            }

            return Response::buildSuccess(
                ['list' => $data],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getByID(string $id): array
    {
        try {
            $subModule = DB::table(DatabaseConst::SUB_MODULES().' as sm')
                ->leftJoin(DatabaseConst::CURRICULUM_MODULES().' as cm', 'sm.module_id', '=', 'cm.id')
                ->select([
                    'sm.*',
                    'cm.title as module_title',
                    'cm.cefr as module_cefr',
                ])
                ->where('sm.id', $id)
                ->whereNull('sm.deleted_at')
                ->first();

            if (!$subModule) {
                return Response::buildErrorNotFound('Sub-modul tidak ditemukan');
            }

            $sections = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('sub_module_id', $id)
                ->orderBy('order_index', 'asc')
                ->get()
                ->map(function ($sec) {
                    $sec->content = is_string($sec->content) ? json_decode($sec->content, true) : $sec->content;
                    return $sec;
                });

            return Response::buildSuccess([
                'sub_module' => $subModule,
                'sections' => $sections,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function create(array $data): array
    {
        try {
            $id = !empty($data['id']) ? trim($data['id']) : (string) Str::uuid();

            $insertData = [
                'id' => $id,
                'module_id' => $data['module_id'],
                'skill_type' => $data['skill_type'],
                'title' => $data['title'],
                'order_index' => (int) ($data['order_index'] ?? 1),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::SUB_MODULES())->insert($insertData);

            return Response::buildSuccess(['id' => $id, 'sub_module' => $insertData], ResponseConst::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function update(string $id, array $data): array
    {
        try {
            $existing = DB::table(DatabaseConst::SUB_MODULES())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$existing) {
                return Response::buildErrorNotFound('Sub-modul tidak ditemukan');
            }

            $updateData = [
                'module_id' => $data['module_id'] ?? $existing->module_id,
                'skill_type' => $data['skill_type'] ?? $existing->skill_type,
                'title' => $data['title'] ?? $existing->title,
                'order_index' => isset($data['order_index']) ? (int) $data['order_index'] : $existing->order_index,
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::SUB_MODULES())
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
            $existing = DB::table(DatabaseConst::SUB_MODULES())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$existing) {
                return Response::buildErrorNotFound('Sub-modul tidak ditemukan');
            }

            DB::table(DatabaseConst::SUB_MODULES())
                ->where('id', $id)
                ->update(['deleted_at' => now()]);

            return Response::buildSuccess(['id' => $id]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getModuleOptions(): array
    {
        try {
            $modules = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->whereNull('deleted_at')
                ->select(['id', 'title', 'cefr'])
                ->orderBy('order_index', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            return Response::buildSuccess(['modules' => $modules]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }
}
