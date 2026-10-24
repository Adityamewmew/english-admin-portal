<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateModuleAiMaterialsUsecase extends Usecase
{
    public function execute(string $moduleId, array $options = []): array
    {
        try {
            $module = DB::table(DatabaseConst::CURRICULUM_MODULES())
                ->where('id', $moduleId)
                ->whereNull('deleted_at')
                ->first();

            if (! $module) {
                return Response::buildErrorNotFound('Modul kurikulum tidak ditemukan');
            }

            // 1. Ensure all 5 standard submodules exist (reading, listening, writing, speaking, assessment)
            $subModules = $this->ensureFiveSubModules($module);

            $subModuleId = $options['sub_module_id'] ?? null;
            $skillType = 'all';
            $targetSubModule = null;

            if (! empty($subModuleId)) {
                $targetSubModule = $subModules->firstWhere('id', $subModuleId);
                if ($targetSubModule) {
                    $skillType = strtolower($targetSubModule->skill_type);
                }
            }

            $customInstructions = $options['custom_instructions'] ?? '';
            $documentBase64 = $options['document_base64'] ?? null;
            $documentMimeType = $options['document_mime_type'] ?? null;
            $documentText = $options['document_text'] ?? null;

            // 2. Call Elysia backend endpoint (Default port 3001 as defined in monorepo architecture)
            $elysiaUrl = rtrim(env('ELYSIA_API_URL', 'http://localhost:3001'), '/');
            $payload = [
                'moduleId' => $moduleId,
                'moduleTitle' => $module->title,
                'cefr' => $module->cefr,
                'objective' => $module->objective ?: "Master {$module->title} at {$module->cefr} level",
                'customInstructions' => $customInstructions ?: '',
                'skillType' => $skillType,
                'documentBase64' => $documentBase64,
                'documentMimeType' => $documentMimeType,
                'documentText' => $documentText,
            ];

            $skillsMap = null;
            $materials = null;
            try {
                $httpResponse = Http::timeout(180)->post("{$elysiaUrl}/api/curriculum/modules/ai-generate", $payload);
                if ($httpResponse->successful()) {
                    $json = $httpResponse->json();
                    $skillsMap = $json['data']['skills'] ?? null;
                    $materials = $json['data']['materials'] ?? null;
                } else {
                    Log::error('Elysia AI generator failed: HTTP '.$httpResponse->status().' - '.$httpResponse->body());
                }
            } catch (Exception $e) {
                Log::warning('Elysia AI generator unreachable: '.$e->getMessage().'. Falling back to direct OmniRoute.');
            }

            // Fallback: If Elysia call failed or offline, call OmniRoute API directly
            if (! $skillsMap && ! $materials) {
                $fallbackRes = $this->callOmniRouteDirectly($module, $skillType, $customInstructions, $documentBase64, $documentMimeType, $documentText);
                if (is_array($fallbackRes)) {
                    if (isset($fallbackRes['reading']) || isset($fallbackRes['assessment'])) {
                        $skillsMap = $fallbackRes;
                    } else {
                        $materials = $fallbackRes;
                    }
                }
            }

            if (! $skillsMap && ! $materials) {
                return Response::buildErrorService('Gagal menghasilkan materi dari AI. Silakan coba kembali.');
            }

            $insertedCount = 0;
            $cleanId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moduleId));
            $shouldReplace = ! empty($options['replace']);

            // 3. Process persistence based on skillType and data shape
            if ($skillType !== 'all') {
                $targetSmId = $targetSubModule ? $targetSubModule->id : null;
                $sectionsList = [];

                if ($skillsMap && isset($skillsMap[$skillType])) {
                    $sectionsList = $skillsMap[$skillType];
                } elseif (is_array($materials)) {
                    $sectionsList = isset($materials[0]) ? $materials : ($materials['sections'] ?? ($materials[$skillType] ?? []));
                }

                if ($shouldReplace && $targetSmId) {
                    DB::table(DatabaseConst::MODULE_SECTIONS())
                        ->where('module_id', $moduleId)
                        ->where('sub_module_id', $targetSmId)
                        ->delete();
                }

                $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $targetSmId, $sectionsList);
            } else {
                // ALL SKILLS MODE
                if ($shouldReplace) {
                    DB::table(DatabaseConst::MODULE_SECTIONS())
                        ->where('module_id', $moduleId)
                        ->delete();
                }

                if ($skillsMap && is_array($skillsMap)) {
                    foreach ($skillsMap as $skill => $sectionsList) {
                        if (! is_array($sectionsList)) {
                            continue;
                        }
                        $matchedSm = $subModules->first(function ($sm) use ($skill) {
                            return strtolower($sm->skill_type) === strtolower($skill);
                        });
                        $smId = $matchedSm ? $matchedSm->id : null;
                        $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $smId, $sectionsList);
                    }
                } elseif (is_array($materials)) {
                    // Group sections if flat array
                    foreach ($materials as $item) {
                        if (! is_array($item)) {
                            continue;
                        }
                        $itemSubId = $item['subModuleId'] ?? $item['sub_module_id'] ?? null;
                        $itemSkill = $item['skillType'] ?? $item['skill_type'] ?? null;

                        $matchedSm = null;
                        if ($itemSubId) {
                            $matchedSm = $subModules->firstWhere('id', $itemSubId);
                        }
                        if (! $matchedSm && $itemSkill) {
                            $matchedSm = $subModules->first(function ($sm) use ($itemSkill) {
                                return strtolower($sm->skill_type) === strtolower($itemSkill);
                            });
                        }

                        $smId = $matchedSm ? $matchedSm->id : null;
                        $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $smId, [$item]);
                    }
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

    /**
     * Ensures all 5 standard submodules exist for a module.
     */
    private function ensureFiveSubModules(object $module): Collection
    {
        $existing = DB::table(DatabaseConst::SUB_MODULES())
            ->where('module_id', $module->id)
            ->orderBy('order_index', 'asc')
            ->get();

        $standard = [
            'reading' => ['title' => 'Reading Mission', 'order' => 1],
            'listening' => ['title' => 'Listening Mission', 'order' => 2],
            'writing' => ['title' => 'Writing Mission', 'order' => 3],
            'speaking' => ['title' => 'Speaking Lab', 'order' => 4],
            'assessment' => ['title' => 'Unit Final Mission', 'order' => 5],
        ];

        $cleanId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $module->id));
        $missing = [];

        foreach ($standard as $skill => $meta) {
            $found = $existing->first(function ($sm) use ($skill) {
                return strtolower($sm->skill_type) === $skill;
            });
            if (! $found) {
                $missing[] = [
                    'id' => "sub-{$cleanId}-{$skill}",
                    'module_id' => $module->id,
                    'skill_type' => $skill,
                    'title' => $meta['title'],
                    'order_index' => $meta['order'],
                    'created_at' => now(),
                ];
            }
        }

        if (! empty($missing)) {
            DB::table(DatabaseConst::SUB_MODULES())->insert($missing);
            $existing = DB::table(DatabaseConst::SUB_MODULES())
                ->where('module_id', $module->id)
                ->orderBy('order_index', 'asc')
                ->get();
        }

        return $existing;
    }

    private function persistGeneratedSections(string $moduleId, string $cleanId, ?string $subModuleId, array $sectionsList): int
    {
        $maxOrder = DB::table(DatabaseConst::MODULE_SECTIONS())
            ->where('module_id', $moduleId)
            ->when($subModuleId, fn ($q) => $q->where('sub_module_id', $subModuleId))
            ->max('order_index') ?? 0;

        $count = 0;
        foreach ($sectionsList as $item) {
            if (! is_array($item)) {
                continue;
            }

            $secType = $item['sectionType'] ?? $item['section_type'] ?? null;
            $title = $item['title'] ?? null;
            if (empty($title) || empty($secType)) {
                continue;
            }

            $maxOrder++;
            $secId = $item['id'] ?? ('sec-'.$cleanId.'-'.substr(uniqid(), -5).'-'.mt_rand(10, 99));
            $content = $item['content'] ?? [];

            if ($secType === 'vocab' && is_array($content)) {
                if (isset($content['items']) && ! isset($content['words'])) {
                    $content['words'] = $content['items'];
                } elseif (isset($content['words']) && ! isset($content['items'])) {
                    $content['items'] = $content['words'];
                }
            }

            $orderIdx = $item['orderIndex'] ?? $item['order_index'] ?? $maxOrder;

            DB::table(DatabaseConst::MODULE_SECTIONS())->insert([
                'id' => $secId,
                'module_id' => $moduleId,
                'sub_module_id' => $subModuleId,
                'section_type' => $secType,
                'title' => $title,
                'content' => is_string($content) ? $content : json_encode($content),
                'order_index' => $orderIdx,
                'created_at' => now(),
            ]);
            $count++;
        }

        return $count;
    }

    private function callOmniRouteDirectly($module, string $skillType, string $customInstructions, ?string $documentBase64, ?string $documentMimeType, ?string $documentText): ?array
    {
        try {
            $apiKey = env('OMNIROUTE_API_KEY') ?: env('AI_TTS_API_KEY');
            if (empty($apiKey)) {
                Log::error('OMNIROUTE_API_KEY belum dikonfigurasi di file .env');

                return null;
            }
            $baseUrl = rtrim(env('OMNIROUTE_BASE_URL', env('AI_TTS_BASE_URL', 'https://omni.drimland.my.id/v1')), '/');
            $cefr = strtoupper($module->cefr ?? 'A1');
            $objective = $module->objective ?: "Master {$module->title} at {$cefr} level";

            $systemPrompt = "You are an expert Cambridge & CEFR English curriculum designer for EDDY'S English School. Generate complete learning materials for Indonesian students learning English at CEFR level {$cefr}. Output ONLY raw valid JSON (no markdown fences, no extra text).";

            if ($skillType !== 'all') {
                $userPrompt = "Generate calibrated sections for sub-module skill '{$skillType}' in module '{$module->title}' (CEFR {$cefr}). Objective: {$objective}. {$customInstructions}. Output a JSON array of sections: [{\"title\": \"...\", \"section_type\": \"theory|vocab|dialogue|practice|quiz\", \"content\": {...}}]";
            } else {
                $userPrompt = "Generate 5-skill curriculum package for module '{$module->title}' (CEFR {$cefr}). Objective: {$objective}. {$customInstructions}. Output a JSON object with keys 'reading', 'listening', 'writing', 'speaking', 'assessment', each containing an array of sections with keys 'title', 'section_type' (theory/vocab/dialogue/practice/quiz), and 'content'.";
            }

            if ($documentText) {
                $userPrompt .= "\n\n[DOCUMENT REFERENCE TEXT / TRANSCRIPT]:\n\"\"\"\n".substr($documentText, 0, 45000)."\n\"\"\"\nInstructions: Distill and extract teaching materials based directly on the attached reference text for CEFR {$cefr}.";
            }

            $userContent = $userPrompt;
            if ($documentBase64 && $documentMimeType && str_starts_with($documentMimeType, 'image/')) {
                $docPrompt = $userPrompt."\n\nInstructions: Distill and extract teaching materials based directly on the attached document for CEFR {$cefr}.";
                $userContent = [
                    ['type' => 'text', 'text' => $docPrompt],
                    ['type' => 'image_url', 'image_url' => ['url' => "data:{$documentMimeType};base64,{$documentBase64}"]],
                ];
            }

            $res = Http::timeout(120)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/chat/completions", [
                'model' => 'antigravity/gemini-3.8-flash-tiered',
                'temperature' => 0.4,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userContent],
                ],
            ]);

            if (! $res->successful()) {
                return null;
            }

            $text = $res->json('choices.0.message.content') ?? '';
            $text = trim(preg_replace('/^```(?:json)?/i', '', trim($text)));
            $text = trim(preg_replace('/```$/', '', $text));

            $json = json_decode($text, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                return $json['skills'] ?? ($json['materials'] ?? $json);
            }
        } catch (Exception $e) {
            Log::error('Direct OmniRoute error: '.$e->getMessage());
        }

        return null;
    }
}
