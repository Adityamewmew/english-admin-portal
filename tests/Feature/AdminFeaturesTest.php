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

test('admin can access sub modules and detail', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    DB::table('curriculum_modules')->insert([
        'id' => 'MOD-TEST-SM',
        'level_id' => 'A1.1',
        'title' => 'Sub Module Parent',
        'cefr' => 'A1',
        'group' => 'Daily Life',
        'objective' => 'Parent module objective',
        'complexity' => 'easy',
        'estimated_minutes' => 10,
        'is_exam' => false,
        'passing_score' => 70,
        'order_index' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $subModuleId = (string) Str::uuid();
    DB::table('sub_modules')->insert([
        'id' => $subModuleId,
        'module_id' => 'MOD-TEST-SM',
        'skill_type' => 'speaking',
        'title' => 'Speaking Practice Section',
        'order_index' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.sub_modules.index'))
        ->assertSuccessful()
        ->assertSee('Speaking Practice Section');

    $this->actingAs($admin)
        ->get(route('admin.sub_modules.detail', $subModuleId))
        ->assertSuccessful()
        ->assertSee('Speaking Practice Section');
});

test('admin can create, update, and soft delete curriculum module', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.modules.create'), [
        'id' => 'MOD-CRUD-01',
        'level_id' => 'B1.1',
        'title' => 'New CRUD Module',
        'cefr' => 'B1',
        'group' => 'Business English',
        'objective' => 'Learn business vocab',
        'complexity' => 'medium',
        'estimated_minutes' => 20,
        'passing_score' => 75,
        'order_index' => 2,
    ]);
    $response->assertRedirect(route('admin.modules.index'));
    expect(DB::table('curriculum_modules')->where('id', 'MOD-CRUD-01')->exists())->toBeTrue();

    $this->actingAs($admin)->post(route('admin.modules.doUpdate', 'MOD-CRUD-01'), [
        'level_id' => 'B1.1',
        'title' => 'Updated CRUD Module',
        'cefr' => 'B1',
        'group' => 'Business English',
        'objective' => 'Updated objective',
        'complexity' => 'hard',
        'estimated_minutes' => 25,
        'passing_score' => 80,
        'order_index' => 2,
    ])->assertRedirect(route('admin.modules.detail', 'MOD-CRUD-01'));

    expect(DB::table('curriculum_modules')->where('id', 'MOD-CRUD-01')->value('title'))->toBe('Updated CRUD Module');

    $this->actingAs($admin)->delete(route('admin.modules.delete', 'MOD-CRUD-01'))
        ->assertRedirect(route('admin.modules.index'));

    expect(DB::table('curriculum_modules')->where('id', 'MOD-CRUD-01')->whereNotNull('deleted_at')->exists())->toBeTrue();
});

test('admin can create, update, and soft delete sub module', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    DB::table('curriculum_modules')->insert([
        'id' => 'MOD-PARENT-02',
        'level_id' => 'A2.1',
        'title' => 'Parent for Submodule CRUD',
        'cefr' => 'A2',
        'group' => 'Travel',
        'objective' => 'Travel vocab',
        'complexity' => 'easy',
        'estimated_minutes' => 15,
        'is_exam' => false,
        'passing_score' => 70,
        'order_index' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($admin)->post(route('admin.sub_modules.create'), [
        'module_id' => 'MOD-PARENT-02',
        'skill_type' => 'listening',
        'title' => 'Airport Announcement',
        'order_index' => 1,
    ])->assertRedirect(route('admin.sub_modules.index'));

    $created = DB::table('sub_modules')->where('title', 'Airport Announcement')->first();
    expect($created)->not->toBeNull();

    $this->actingAs($admin)->post(route('admin.sub_modules.doUpdate', $created->id), [
        'module_id' => 'MOD-PARENT-02',
        'skill_type' => 'listening',
        'title' => 'Airport Announcement Updated',
        'order_index' => 2,
    ])->assertRedirect(route('admin.sub_modules.detail', $created->id));

    expect(DB::table('sub_modules')->where('id', $created->id)->value('title'))->toBe('Airport Announcement Updated');

    $this->actingAs($admin)->delete(route('admin.sub_modules.delete', $created->id))
        ->assertRedirect(route('admin.sub_modules.index'));

    expect(DB::table('sub_modules')->where('id', $created->id)->whereNotNull('deleted_at')->exists())->toBeTrue();
});

test('admin can create, update, and soft delete question item', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    $this->actingAs($admin)->post(route('admin.questions.create'), [
        'id' => 'Q-CRUD-001',
        'cefr' => 'B1',
        'skill' => 'reading',
        'type' => 'MCQ',
        'question' => 'What is the main topic?',
        'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
        'answer' => 'Option A',
        'explanation' => 'First sentence says so.',
    ])->assertRedirect(route('admin.questions.index'));

    expect(DB::table('item_bank')->where('id', 'Q-CRUD-001')->exists())->toBeTrue();

    $this->actingAs($admin)->post(route('admin.questions.doUpdate', 'Q-CRUD-001'), [
        'cefr' => 'B1',
        'skill' => 'reading',
        'type' => 'MCQ',
        'question' => 'What is the updated main topic?',
        'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
        'answer' => 'Option B',
        'explanation' => 'Updated reasoning.',
    ])->assertRedirect(route('admin.questions.detail', 'Q-CRUD-001'));

    expect(DB::table('item_bank')->where('id', 'Q-CRUD-001')->value('answer'))->toBe('Option B');

    $this->actingAs($admin)->delete(route('admin.questions.delete', 'Q-CRUD-001'))
        ->assertRedirect(route('admin.questions.index'));

    expect(DB::table('item_bank')->where('id', 'Q-CRUD-001')->whereNotNull('deleted_at')->exists())->toBeTrue();
});
