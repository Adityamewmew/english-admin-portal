<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Http\Presenter\Response;
use App\Usecase\Admin\Curriculum\AssessmentSynthesizer;
use App\Usecase\Admin\Curriculum\SkillPromptBuilder;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateModuleAiMaterialsUsecase extends Usecase
{
    public function __construct(
        protected SkillPromptBuilder $promptBuilder = new SkillPromptBuilder,
        protected AssessmentSynthesizer $assessmentSynthesizer = new AssessmentSynthesizer
    ) {}

    public function execute(string $moduleId, array $options = []): array
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);

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
            $shouldReplace = ! empty($options['replace']);
            $cleanId = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $moduleId));

            $insertedCount = 0;

            // 2. SINGLE SKILL MODE
            if ($skillType !== 'all') {
                $targetSmId = $targetSubModule ? $targetSubModule->id : null;

                if ($skillType === 'assessment') {
                    $sectionsList = $this->assessmentSynthesizer->synthesize($module, $targetSmId);
                } else {
                    $sectionsList = $this->generateSkillWithAi(
                        $skillType,
                        $module,
                        $customInstructions,
                        $documentText,
                        $documentBase64,
                        $documentMimeType
                    );
                }

                if ($shouldReplace && $targetSmId) {
                    DB::table(DatabaseConst::MODULE_SECTIONS())
                        ->where('module_id', $moduleId)
                        ->where('sub_module_id', $targetSmId)
                        ->delete();
                }

                $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $targetSmId, $sectionsList);
            } else {
                // 3. ALL SKILLS MODE (Reading -> Listening -> Writing -> Speaking -> Assessment)
                if ($shouldReplace) {
                    DB::table(DatabaseConst::MODULE_SECTIONS())
                        ->where('module_id', $moduleId)
                        ->delete();
                }

                $skills = ['reading', 'listening', 'writing', 'speaking', 'assessment'];
                foreach ($skills as $skill) {
                    $matchedSm = $subModules->first(function ($sm) use ($skill) {
                        return strtolower($sm->skill_type) === $skill;
                    });
                    $smId = $matchedSm ? $matchedSm->id : null;

                    if ($skill === 'assessment') {
                        $sectionsList = $this->assessmentSynthesizer->synthesize($module, $smId);
                    } else {
                        $sectionsList = $this->generateSkillWithAi(
                            $skill,
                            $module,
                            $customInstructions,
                            $documentText,
                            $documentBase64,
                            $documentMimeType
                        );
                    }

                    $insertedCount += $this->persistGeneratedSections($moduleId, $cleanId, $smId, $sectionsList);
                }
            }

            return Response::buildSuccess([
                'module_id' => $moduleId,
                'inserted_count' => $insertedCount,
            ]);
        } catch (Exception $e) {
            Log::error('GenerateModuleAiMaterialsUsecase error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    private function generateSkillWithAi(
        string $skill,
        object $module,
        string $customInstructions,
        ?string $documentText,
        ?string $documentBase64,
        ?string $documentMimeType
    ): array {
        $apiKey = config('services.omniroute.key', env('OMNIROUTE_API_KEY')) ?: env('AI_TTS_API_KEY');
        if (empty($apiKey)) {
            throw new Exception('OMNIROUTE_API_KEY belum dikonfigurasi di file .env');
        }

        $baseUrl = rtrim(config('services.omniroute.url', env('OMNIROUTE_BASE_URL', 'https://omni.drimland.my.id/v1')), '/');
        $prompts = $this->promptBuilder->build($skill, $module, $customInstructions, $documentText);

        $userContent = $prompts['user'];
        if ($documentBase64 && $documentMimeType && str_starts_with($documentMimeType, 'image/')) {
            $userContent = [
                ['type' => 'text', 'text' => $prompts['user']."\n\nDistill visual teaching materials from attached image."],
                ['type' => 'image_url', 'image_url' => ['url' => "data:{$documentMimeType};base64,{$documentBase64}"]],
            ];
        }

        $res = Http::timeout(120)->withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post("{$baseUrl}/chat/completions", [
            'model' => 'antigravity/gemini-3.8-flash-tiered',
            'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => $prompts['system']],
                ['role' => 'user', 'content' => $userContent],
            ],
        ]);

        if (! $res->successful()) {
            throw new Exception("OmniRoute failed for {$skill}: HTTP {$res->status()} - {$res->body()}");
        }

        $text = $res->json('choices.0.message.content') ?? '';
        $text = trim(preg_replace('/^```(?:json)?/i', '', trim($text)));
        $text = trim(preg_replace('/```$/', '', $text));

        $json = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($json)) {
            throw new Exception("Invalid JSON returned for {$skill}: ".substr($text, 0, 200));
        }

        return $json['sections'] ?? ($json['skills'][$skill] ?? $json);
    }

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
                $rawList = $content['items'] ?? ($content['words'] ?? ($content['vocabulary_list'] ?? []));
                $content['items'] = $rawList;
                $content['words'] = $rawList;
            }

            if ($secType === 'practice' && is_array($content)) {
                $hasDrills = ! empty($content['drills']) && is_array($content['drills']) && ! empty($content['drills'][0]['pairs']);
                if (! $hasDrills) {
                    $vocabItems = [];
                    foreach ($sectionsList as $secCandidate) {
                        $cType = $secCandidate['sectionType'] ?? $secCandidate['section_type'] ?? null;
                        if ($cType === 'vocab') {
                            $cContent = $secCandidate['content'] ?? [];
                            $vocabItems = $cContent['items'] ?? ($cContent['words'] ?? ($cContent['vocabulary_list'] ?? []));
                            if (! empty($vocabItems)) {
                                break;
                            }
                        }
                    }

                    if (empty($vocabItems) && $subModuleId) {
                        $dbVocab = DB::table(DatabaseConst::MODULE_SECTIONS())
                            ->where('module_id', $moduleId)
                            ->where('sub_module_id', $subModuleId)
                            ->where('section_type', 'vocab')
                            ->first();
                        if ($dbVocab && ! empty($dbVocab->content)) {
                            $decodedVocab = json_decode($dbVocab->content, true);
                            $vocabItems = $decodedVocab['items'] ?? ($decodedVocab['words'] ?? ($decodedVocab['vocabulary_list'] ?? []));
                        }
                    }

                    if (! empty($vocabItems) && is_array($vocabItems)) {
                        $pairs = [];
                        foreach (array_slice($vocabItems, 0, 5) as $vItem) {
                            $left = $vItem['word'] ?? ($vItem['item'] ?? ($vItem['term'] ?? ''));
                            $right = $vItem['meaning'] ?? ($vItem['meaning_id'] ?? ($vItem['meaningId'] ?? ($vItem['definition'] ?? ($vItem['translation'] ?? ''))));
                            if (! empty($left)) {
                                $pairs[] = [
                                    'left' => $left,
                                    'right' => $right,
                                ];
                            }
                        }
                        if (! empty($pairs)) {
                            $content['drills'] = [
                                [
                                    'id' => 'drill-cable-1',
                                    'title' => 'Interactive Cable Connection (Vocab Match)',
                                    'instruction' => 'Connect each vocabulary term on the left with its accurate meaning on the right.',
                                    'pairs' => $pairs,
                                ],
                            ];
                        }
                    }
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
}
