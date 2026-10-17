<?php

use App\Constants\UserConst;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('guest is redirected to login from admin routes', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    $this->get(route('admin.modules.index'))->assertRedirect(route('login'));
    $this->get(route('admin.questions.index'))->assertRedirect(route('login'));
});

test('student cannot access admin-only routes', function () {
    $student = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::STUDENT,
        'role' => 'student',
    ]);

    $this->actingAs($student)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin can access curriculum modules and detail', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    DB::table('curriculum_modules')->insert([
        'id' => 'TEST-M01',
        'level_id' => 'A1.1',
        'title' => 'Test Greetings',
        'cefr' => 'A1',
        'group' => 'Daily Life',
        'objective' => 'Learn how to greet people',
        'complexity' => 'easy',
        'estimated_minutes' => 10,
        'is_exam' => false,
        'passing_score' => 70,
        'order_index' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.modules.index'))
        ->assertSuccessful()
        ->assertSee('Test Greetings');

    $this->actingAs($admin)
        ->get(route('admin.modules.detail', 'TEST-M01'))
        ->assertSuccessful()
        ->assertSee('Test Greetings')
        ->assertSee('Learn how to greet people');
});

test('admin can access question bank and detail', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    DB::table('item_bank')->insert([
        'id' => 'Q-TEST-001',
        'skill' => 'grammar',
        'cefr' => 'A1',
        'type' => 'MCQ',
        'question' => 'She ___ a student.',
        'options' => json_encode(['is', 'are', 'am', 'be']),
        'answer' => 'is',
        'explanation' => 'Third person singular takes is.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questions.index'))
        ->assertSuccessful()
        ->assertSee('She ___ a student.');

    $this->actingAs($admin)
        ->get(route('admin.questions.detail', 'Q-TEST-001'))
        ->assertSuccessful()
        ->assertSee('She ___ a student.')
        ->assertSee('Third person singular takes is.');
});
