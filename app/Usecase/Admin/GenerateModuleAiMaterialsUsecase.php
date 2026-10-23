<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
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

            $subModules = DB::table(DatabaseConst::SUB_MODULES())
                ->where('module_id', $moduleId)
                ->orderBy('order_index', 'asc')
                ->get();

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

            // 1. Call Elysia backend endpoint
            $elysiaUrl = rtrim(env('ELYSIA_API_URL', 'http://localhost:3003'), '/');
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

            $materials = null;
            try {
                $httpResponse = Http::timeout(180)->post("{$elysiaUrl}/api/curriculum/modules/ai-generate", $payload);
                if ($httpResponse->successful()) {
                    $json = $httpResponse->json();
                    $materials = $json['data']['materials'] ?? null;
                } else {
                    Log::error('Elysia AI generator failed: HTTP '.$httpResponse->status().' - '.$httpResponse->body());
                }
            } catch (Exception $e) {
                Log::warning('Elysia AI generator unreachable: '.$e->getMessage().'. Falling back to direct OmniRoute.');
            }

            // Fallback: If Elysia call failed or offline, call OmniRoute API directly
            if (! $materials) {
                $materials = $this->callOmniRouteDirectly($module, $skillType, $customInstructions, $documentBase64, $documentMimeType, $documentText);
            }

            if (! $materials) {
                return Response::buildErrorService('Gagal menghasilkan materi dari AI. Silakan coba kembali.');
            }

            $insertedCount = 0;
            $cleanId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moduleId));
            $shouldReplace = ! empty($options['replace']);

            if ($skillType !== 'all' && is_array($materials)) {
                $sectionsList = isset($materials[0]) ? $materials : ($materials['sections'] ?? ($materials[$skillType] ?? []));
                $targetSmId = $targetSubModule ? $targetSubModule->id : null;
                if ($shouldReplace && $targetSmId) {
                    DB::table(DatabaseConst::MODULE_SECTIONS())
                        ->where('module_id', $moduleId)
                        ->where('sub_module_id', $targetSmId)
                        ->delete();
                }
                $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $targetSmId, $sectionsList);
            } elseif (is_array($materials)) {
                if ($shouldReplace) {
                    DB::table(DatabaseConst::MODULE_SECTIONS())
                        ->where('module_id', $moduleId)
                        ->delete();
                }
                foreach ($materials as $skill => $sectionsList) {
                    if (! is_array($sectionsList)) {
                        continue;
                    }
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
            ->when($subModuleId, fn ($q) => $q->where('sub_module_id', $subModuleId))
            ->max('order_index') ?? 0;

        $count = 0;
        foreach ($sectionsList as $item) {
            $secType = $item['sectionType'] ?? $item['section_type'] ?? null;
            if (empty($item['title']) || empty($secType)) {
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
                'title' => $item['title'],
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
                Log::error('OMNIROUTE_API_KEY atau AI_TTS_API_KEY belum dikonfigurasi di file .env');

                return null;
            }
            $baseUrl = rtrim(env('OMNIROUTE_BASE_URL', env('AI_TTS_BASE_URL', 'https://omni.drimland.my.id/v1')), '/');
            $cefr = strtoupper($module->cefr ?? 'A1');
            $objective = $module->objective ?: "Master {$module->title} at {$cefr} level";

            $systemPrompt = "You are an expert Cambridge & CEFR English curriculum designer for EDDY'S English School. Generate complete learning materials for Indonesian students learning English at CEFR level {$cefr}. Output ONLY raw valid JSON (no markdown fences, no extra text).";

            if ($skillType !== 'all') {
                $userPrompt = "Generate 2 to 3 practical sections for sub-module skill '{$skillType}' in module '{$module->title}' (CEFR {$cefr}). Objective: {$objective}. {$customInstructions}. Output a JSON array of sections: [{\"title\": \"...\", \"section_type\": \"theory|vocab|dialogue|quiz\", \"content\": {...}}]";
            } else {
                $userPrompt = "Generate 5-skill curriculum package for module '{$module->title}' (CEFR {$cefr}). Objective: {$objective}. {$customInstructions}. Output a JSON object with keys 'reading', 'listening', 'writing', 'speaking', 'assessment', each containing an array of sections with keys 'title', 'section_type' (theory/vocab/dialogue/quiz), and 'content'.";
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
                return $json['materials'] ?? $json;
            }
        } catch (Exception $e) {
            Log::error('Direct OmniRoute error: '.$e->getMessage());
        }

        return null;
    }
}
