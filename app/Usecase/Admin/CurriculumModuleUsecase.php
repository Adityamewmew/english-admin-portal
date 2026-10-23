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
                $smId = 'sub-' . $cleanId . '-' . $s['skill'];
                if (!$firstSubModuleId) {
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

            if (!$updated) {
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

    public function createSection(string $moduleId, array $data): array
    {
        try {
            $maxOrder = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('module_id', $moduleId)
                ->max('order_index') ?? 0;

            $id = 'sec-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moduleId)) . '-' . substr(uniqid(), -6);

            $insertData = [
                'id' => $id,
                'module_id' => $moduleId,
                'sub_module_id' => !empty($data['sub_module_id']) ? $data['sub_module_id'] : null,
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

            if (!$existing) {
                return Response::buildErrorNotFound('Bagian materi tidak ditemukan');
            }

            $updateData = [
                'title' => $data['title'] ?? $existing->title,
                'section_type' => $data['section_type'] ?? $existing->section_type,
                'order_index' => isset($data['order_index']) ? (int) $data['order_index'] : $existing->order_index,
            ];

            if (array_key_exists('sub_module_id', $data)) {
                $updateData['sub_module_id'] = !empty($data['sub_module_id']) ? $data['sub_module_id'] : null;
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

            if (!$deleted) {
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
        try {
            $module = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $moduleId)
                ->whereNull('deleted_at')
                ->first();

            if (!$module) {
                return Response::buildErrorNotFound('Modul kurikulum tidak ditemukan');
            }

            $subModules = DB::table(DatabaseConst::SUB_MODULES())
                ->where('module_id', $moduleId)
                ->orderBy('order_index', 'asc')
                ->get();

            $subModuleId = $options['sub_module_id'] ?? null;
            $skillType = 'all';
            $targetSubModule = null;

            if (!empty($subModuleId)) {
                $targetSubModule = $subModules->firstWhere('id', $subModuleId);
                if ($targetSubModule) {
                    $skillType = strtolower($targetSubModule->skill_type);
                }
            }

            $customInstructions = $options['custom_instructions'] ?? '';

            // 1. Call Elysia backend endpoint
            $elysiaUrl = rtrim(env('ELYSIA_API_URL', 'http://localhost:3003'), '/');
            $payload = [
                'moduleTitle' => $module->title,
                'cefr' => $module->cefr,
                'objective' => $module->objective,
                'customInstructions' => $customInstructions,
                'skillType' => $skillType,
            ];

            $materials = null;
            try {
                $httpResponse = \Illuminate\Support\Facades\Http::timeout(90)->post("{$elysiaUrl}/api/curriculum/modules/ai-generate", $payload);
                if ($httpResponse->successful()) {
                    $json = $httpResponse->json();
                    $materials = $json['data']['materials'] ?? null;
                }
            } catch (Exception $e) {
                Log::warning('Elysia AI generator unreachable: ' . $e->getMessage() . '. Falling back to direct OmniRoute.');
            }

            // Fallback: If Elysia call failed or offline, call OmniRoute API directly
            if (!$materials) {
                $materials = $this->callOmniRouteDirectly($module, $skillType, $customInstructions);
            }

            if (!$materials) {
                return Response::buildErrorService('Gagal menghasilkan materi dari AI. Silakan coba kembali.');
            }

            $insertedCount = 0;
            $cleanId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moduleId));

            if ($skillType !== 'all' && is_array($materials)) {
                $sectionsList = isset($materials[0]) ? $materials : ($materials[$skillType] ?? []);
                $targetSmId = $targetSubModule ? $targetSubModule->id : null;
                $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $targetSmId, $sectionsList);
            } elseif (is_array($materials)) {
                foreach ($materials as $skill => $sectionsList) {
                    if (!is_array($sectionsList)) continue;
                    $matchedSm = $subModules->first(function ($sm) use ($skill) {
                        return strtolower($sm->skill_type) === strtolower($skill);
                    });
                    $smId = $matchedSm ? $matchedSm->id : null;
                    $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $smId, $sectionsList);
                }
            }

            return Response::buildSuccess([
                'module_id' => $moduleId,
                'inserted_count' => $insertedCount,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    private function persistGeneratedSections(string $moduleId, string $cleanId, ?string $subModuleId, array $sectionsList): int
    {
        $maxOrder = DB::table(DatabaseConst::MODULE_SECTIONS())
            ->where('module_id', $moduleId)
            ->when($subModuleId, fn($q) => $q->where('sub_module_id', $subModuleId))
            ->max('order_index') ?? 0;

        $count = 0;
        foreach ($sectionsList as $item) {
            if (empty($item['title']) || empty($item['section_type'])) continue;

            $maxOrder++;
            $secId = 'sec-' . $cleanId . '-' . substr(uniqid(), -5) . '-' . mt_rand(10, 99);
            $content = $item['content'] ?? [];

            if ($item['section_type'] === 'vocab' && is_array($content)) {
                if (isset($content['items']) && !isset($content['words'])) {
                    $content['words'] = $content['items'];
                } elseif (isset($content['words']) && !isset($content['items'])) {
                    $content['items'] = $content['words'];
                }
            }

            DB::table(DatabaseConst::MODULE_SECTIONS())->insert([
                'id' => $secId,
                'module_id' => $moduleId,
                'sub_module_id' => $subModuleId,
                'section_type' => $item['section_type'],
                'title' => $item['title'],
                'content' => is_string($content) ? $content : json_encode($content),
                'order_index' => $maxOrder,
                'created_at' => now(),
            ]);
            $count++;
        }
        return $count;
    }

    private function callOmniRouteDirectly($module, string $skillType, string $customInstructions): ?array
    {
        try {
            $apiKey = env('AI_TTS_API_KEY', 'sk-8a1c8c009c8af9a0-15746f-4c9a4a96');
            $baseUrl = rtrim(env('AI_TTS_BASE_URL', 'https://omni.drimland.my.id/v1'), '/');
            $cefr = strtoupper($module->cefr ?? 'A1');

            $systemPrompt = "You are an expert Cambridge & CEFR English curriculum designer for EDDY'S English School. Generate complete learning materials for Indonesian students learning English at CEFR level {$cefr}. Output ONLY raw valid JSON (no markdown fences, no extra text).";

            if ($skillType !== 'all') {
                $userPrompt = "Generate 2 to 3 practical sections for sub-module skill '{$skillType}' in module '{$module->title}' (CEFR {$cefr}). Objective: {$module->objective}. {$customInstructions}. Output a JSON array of sections: [{\"title\": \"...\", \"section_type\": \"theory|vocab|dialogue|quiz\", \"content\": {...}}]";
            } else {
                $userPrompt = "Generate 5-skill curriculum package for module '{$module->title}' (CEFR {$cefr}). Objective: {$module->objective}. {$customInstructions}. Output a JSON object with keys 'reading', 'listening', 'writing', 'speaking', 'assessment', each containing an array of sections with keys 'title', 'section_type' (theory/vocab/dialogue/quiz), and 'content'.";
            }

            $res = \Illuminate\Support\Facades\Http::timeout(120)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/chat/completions", [
                'model' => 'antigravity/gemini-3.8-flash-tiered',
                'temperature' => 0.4,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            if (!$res->successful()) return null;

            $text = $res->json('choices.0.message.content') ?? '';
            $text = trim(preg_replace('/^```(?:json)?/i', '', trim($text)));
            $text = trim(preg_replace('/```$/', '', $text));

            $json = json_decode($text, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json['materials'] ?? $json;
            }
        } catch (Exception $e) {
            Log::error('Direct OmniRoute error: ' . $e->getMessage());
        }
        return null;
    }
}


