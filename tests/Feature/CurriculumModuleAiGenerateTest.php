<?php

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use App\Models\User;
use App\Usecase\Admin\GenerateModuleAiMaterialsUsecase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

test('GenerateModuleAiMaterialsUsecase ensures 5 standard submodules exist', function () {
    $testModuleId = 'TEST-A1-M99';

    // Cleanup before test
    DB::table(DatabaseConst::MODULE_SECTIONS())->where('module_id', $testModuleId)->delete();
    DB::table(DatabaseConst::SUB_MODULES())->where('module_id', $testModuleId)->delete();
    DB::table(DatabaseConst::CURRICULUM_MODULES())->where('id', $testModuleId)->delete();

    // Insert dummy module without submodules
    DB::table(DatabaseConst::CURRICULUM_MODULES())->insert([
        'id' => $testModuleId,
        'level_id' => 'A1.1',
        'title' => 'Test Unit 99',
        'cefr' => 'A1',
        'group' => 'A1 Starter',
        'objective' => 'Test objective',
        'complexity' => 'easy',
        'estimated_minutes' => 25,
        'is_exam' => false,
        'passing_score' => 70,
        'order_index' => 99,
        'created_at' => now(),
    ]);

    // Mock Elysia response with 5-skill structure
    Http::fake([
        '*/api/curriculum/modules/ai-generate' => Http::response([
            'success' => true,
            'data' => [
                'moduleTitle' => 'Test Unit 99',
                'cefr' => 'A1',
                'skills' => [
                    'reading' => [
                        ['title' => 'Reading Theory', 'sectionType' => 'theory', 'content' => ['summary' => 'Reading summary']],
                    ],
                    'assessment' => [
                        ['title' => 'Exam Guidelines', 'sectionType' => 'theory', 'content' => ['totalQuestions' => 55]],
                        ['title' => 'Final 55 Quiz', 'sectionType' => 'quiz', 'content' => ['totalQuestions' => 55, 'questions' => array_fill(0, 55, ['q' => 'Test'])]],
                    ],
                ],
            ],
        ], 200),
    ]);

    $usecase = app(GenerateModuleAiMaterialsUsecase::class);
    $result = $usecase->execute($testModuleId, ['sub_module_id' => null]);

    expect($result['success'])->toBeTrue();

    // Verify all 5 submodules exist in database
    $submodules = DB::table(DatabaseConst::SUB_MODULES())
        ->where('module_id', $testModuleId)
        ->orderBy('order_index', 'asc')
        ->get();

    expect($submodules)->toHaveCount(5);
    $skills = $submodules->pluck('skill_type')->map(fn ($s) => strtolower($s))->toArray();
    expect($skills)->toEqual(['reading', 'listening', 'writing', 'speaking', 'assessment']);

    // Verify sections were inserted and linked to correct submodules
    $assessmentSub = $submodules->firstWhere('skill_type', 'assessment');
    expect($assessmentSub)->not->toBeNull();

    $assessmentSections = DB::table(DatabaseConst::MODULE_SECTIONS())
        ->where('module_id', $testModuleId)
        ->where('sub_module_id', $assessmentSub->id)
        ->get();

    expect($assessmentSections)->toHaveCount(2);

    // Clean up
    DB::table(DatabaseConst::MODULE_SECTIONS())->where('module_id', $testModuleId)->delete();
    DB::table(DatabaseConst::SUB_MODULES())->where('module_id', $testModuleId)->delete();
    DB::table(DatabaseConst::CURRICULUM_MODULES())->where('id', $testModuleId)->delete();
});

test('CurriculumModuleController non-destructive update preserves custom JSON content', function () {
    $user = User::factory()->make(['id' => 1, 'access_type' => UserConst::SUPERADMIN]);

    $testModuleId = 'TEST-MOD-JSON';
    $testSecId = 'sec-test-json-01';

    DB::table(DatabaseConst::MODULE_SECTIONS())->where('id', $testSecId)->delete();
    DB::table(DatabaseConst::CURRICULUM_MODULES())->where('id', $testModuleId)->delete();

    DB::table(DatabaseConst::CURRICULUM_MODULES())->insert([
        'id' => $testModuleId,
        'level_id' => 'A1.1',
        'title' => 'Test Module for JSON',
        'cefr' => 'A1',
        'group' => 'A1 Starter',
        'objective' => 'Test objective',
        'complexity' => 'easy',
        'order_index' => 1,
        'created_at' => now(),
    ]);

    $initialContent = [
        'totalQuestions' => 55,
        'parts' => ['reading' => 15, 'listening' => 15, 'writing' => 10, 'speaking' => 15],
        'questions' => array_fill(0, 55, ['id' => 1, 'question' => 'Q']),
    ];

    DB::table(DatabaseConst::MODULE_SECTIONS())->insert([
        'id' => $testSecId,
        'module_id' => $testModuleId,
        'section_type' => 'quiz',
        'title' => 'Assessment Section',
        'content' => json_encode($initialContent),
        'order_index' => 1,
        'created_at' => now(),
    ]);

    // Update with JSON mode
    $updatedContent = $initialContent;
    $updatedContent['passingScore'] = 75;

    $this->actingAs($user)
        ->post(route('admin.modules.section.update', ['id' => $testModuleId, 'sectionId' => $testSecId]), [
            'title' => 'Assessment Section Updated',
            'section_type' => 'quiz',
            'content_mode' => 'json',
            'content_json' => json_encode($updatedContent),
        ])
        ->assertRedirect();

    $saved = DB::table(DatabaseConst::MODULE_SECTIONS())->where('id', $testSecId)->first();
    expect($saved)->not->toBeNull();
    expect($saved->title)->toBe('Assessment Section Updated');

    $savedJson = json_decode($saved->content, true);
    expect($savedJson['totalQuestions'])->toBe(55);
    expect($savedJson['passingScore'])->toBe(75);
    expect($savedJson['parts']['reading'])->toBe(15);

    // Clean up
    DB::table(DatabaseConst::MODULE_SECTIONS())->where('id', $testSecId)->delete();
    DB::table(DatabaseConst::CURRICULUM_MODULES())->where('id', $testModuleId)->delete();
});
