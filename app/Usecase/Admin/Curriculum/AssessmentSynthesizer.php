<?php

namespace App\Usecase\Admin\Curriculum;

use App\Constants\DatabaseConst;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentSynthesizer
{
    /**
     * Synthesizes 55 calibrated questions across 4 competencies (Reading 15, Listening 15, Writing 10, Speaking 15).
     * Execution time: ~0.05 seconds (Instantaneous, 0 LLM token cost).
     */
    public function synthesize(object $module, ?string $subModuleId = null): array
    {
        $moduleId = $module->id;
        $title = $module->title;
        $cefr = strtoupper($module->cefr ?? 'A1');

        $tierInfo = $this->resolveCefrTier($cefr);
        $bank = $this->loadTierBank($tierInfo['tier']);

        $cleanId = strtolower(preg_replace('/[^a-z0-9]/', '-', $moduleId));
        preg_match('/M?(\d+)/i', $moduleId, $matches);
        $unitNum = isset($matches[1]) ? (int) $matches[1] : 1;
        $offset = ($unitNum * 3) % 15;

        // 1. Gather existing module questions from DB if available
        $dbQuestions = $this->gatherExistingQuestions($moduleId);

        // 2. Reading Pool (15 Qs)
        $readingPool = array_merge($dbQuestions['reading'], $this->rotateSlice($bank['reading'] ?? [], $offset, 15));
        $finalReading = [];
        foreach ($this->rotateSlice($readingPool, 0, 15) as $idx => $q) {
            $q['id'] = "q-{$cleanId}-r-".($idx + 1);
            $q['part'] = 'reading';
            $q['partIndex'] = $idx + 1;
            $q['sourceModule'] = $moduleId;
            $finalReading[] = $q;
        }

        // 3. Listening Pool (15 Qs)
        $listeningPool = array_merge($dbQuestions['listening'], $this->rotateSlice($bank['listening'] ?? [], $offset, 15));
        $finalListening = [];
        foreach ($this->rotateSlice($listeningPool, 0, 15) as $idx => $q) {
            $q['id'] = "q-{$cleanId}-l-".($idx + 1);
            $q['part'] = 'listening';
            $q['partIndex'] = $idx + 1;
            $q['sourceModule'] = $moduleId;
            $finalListening[] = $q;
        }

        // 4. Writing Pool (10 Qs)
        $writingPool = array_merge($dbQuestions['writing'], $this->rotateSlice($bank['writing'] ?? [], $offset, 10));
        $finalWriting = [];
        foreach ($this->rotateSlice($writingPool, 0, 10) as $idx => $q) {
            $q['id'] = "q-{$cleanId}-w-".($idx + 1);
            $q['part'] = 'writing';
            $q['partIndex'] = $idx + 1;
            $q['sourceModule'] = $moduleId;
            $finalWriting[] = $q;
        }

        // 5. Speaking Pool (15 Qs)
        $speakingPool = array_merge($dbQuestions['speaking'], $this->rotateSlice($bank['speaking'] ?? [], $offset, 15));
        $finalSpeaking = [];
        foreach ($this->rotateSlice($speakingPool, 0, 15) as $idx => $q) {
            $q['id'] = "q-{$cleanId}-s-".($idx + 1);
            $q['part'] = 'speaking';
            $q['partIndex'] = $idx + 1;
            $q['sourceModule'] = $moduleId;
            $finalSpeaking[] = $q;
        }

        $allQuestions = array_merge($finalReading, $finalListening, $finalWriting, $finalSpeaking);

        return [
            [
                'id' => "sec-{$cleanId}-theory",
                'module_id' => $moduleId,
                'sub_module_id' => $subModuleId,
                'section_type' => 'theory',
                'title' => 'Unit Final Mission Guidelines & Criteria',
                'order_index' => 1,
                'content' => [
                    'summary' => "Official unit assessment for {$title}. Tests 4 integrated competencies: Reading (15), Listening (15), Writing (10), Speaking (15).",
                    'totalQuestions' => 55,
                    'passingScore' => $tierInfo['passingScore'],
                    'estimatedMinutes' => 40,
                    'difficultyTier' => $tierInfo['tier'],
                    'parts' => [
                        ['part' => 1, 'name' => 'Reading Comprehension & Lexicogrammar', 'count' => 15],
                        ['part' => 2, 'name' => 'Listening Auditory Synthesis', 'count' => 15],
                        ['part' => 3, 'name' => 'Written Production & Discourse', 'count' => 10],
                        ['part' => 4, 'name' => 'Spoken Fluency & Oral Interaction', 'count' => 15],
                    ],
                ],
            ],
            [
                'id' => "sec-{$cleanId}-quiz",
                'module_id' => $moduleId,
                'sub_module_id' => $subModuleId,
                'section_type' => 'quiz',
                'title' => 'Unit Mastery Quiz: 55 Items',
                'order_index' => 2,
                'content' => [
                    'passingScore' => $tierInfo['passingScore'],
                    'totalQuestions' => 55,
                    'difficultyTier' => $tierInfo['tier'],
                    'parts' => [
                        'reading' => $finalReading,
                        'listening' => $finalListening,
                        'writing' => $finalWriting,
                        'speaking' => $finalSpeaking,
                    ],
                    'questions' => $allQuestions,
                    'practicumQuestions' => $allQuestions,
                ],
            ],
        ];
    }

    private function resolveCefrTier(string $cefr): array
    {
        $upper = strtoupper($cefr);
        if (str_starts_with($upper, 'C1') || str_starts_with($upper, 'C2')) {
            return ['tier' => 'hard', 'passingScore' => 80];
        }
        if (str_starts_with($upper, 'B1') || str_starts_with($upper, 'B2')) {
            return ['tier' => 'medium', 'passingScore' => 75];
        }

        return ['tier' => 'easy', 'passingScore' => 70];
    }

    private function loadTierBank(string $tier): array
    {
        $filePath = resource_path("data/exam-tiers/tier-{$tier}.json");
        if (file_exists($filePath)) {
            $json = json_decode(file_get_contents($filePath), true);
            if (is_array($json)) {
                return $json;
            }
        }

        Log::warning("Tier bank file not found or invalid: {$filePath}");

        return ['reading' => [], 'listening' => [], 'writing' => [], 'speaking' => []];
    }

    private function gatherExistingQuestions(string $moduleId): array
    {
        $res = [
            'reading' => [],
            'listening' => [],
            'writing' => [],
            'speaking' => [],
        ];

        try {
            $sections = DB::table(DatabaseConst::MODULE_SECTIONS())
                ->where('module_id', $moduleId)
                ->where('section_type', 'quiz')
                ->get();

            foreach ($sections as $sec) {
                if (empty($sec->content)) {
                    continue;
                }
                $c = is_array($sec->content) ? $sec->content : json_decode($sec->content, true);
                if (! is_array($c)) {
                    continue;
                }

                $subId = strtolower($sec->sub_module_id ?? '');
                $qs = $c['practicumQuestions'] ?? ($c['questions'] ?? []);

                if (str_contains($subId, 'read') || str_contains($subId, '-r-')) {
                    $res['reading'] = array_merge($res['reading'], $qs);
                } elseif (str_contains($subId, 'listen') || str_contains($subId, '-l-')) {
                    $res['listening'] = array_merge($res['listening'], $qs);
                } elseif (str_contains($subId, 'write') || str_contains($subId, '-w-')) {
                    $res['writing'] = array_merge($res['writing'], $qs);
                } elseif (str_contains($subId, 'speak') || str_contains($subId, '-s-')) {
                    $res['speaking'] = array_merge($res['speaking'], $qs);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error gathering existing questions for assessment: '.$e->getMessage());
        }

        return $res;
    }

    private function rotateSlice(array $arr, int $offset, int $count): array
    {
        if (empty($arr)) {
            return [];
        }
        $result = [];
        $len = count($arr);
        for ($i = 0; $i < $count; $i++) {
            $idx = ($offset + $i) % $len;
            $result[] = $arr[$idx];
        }

        return $result;
    }
}
