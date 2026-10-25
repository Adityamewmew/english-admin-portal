<?php

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function () {
    if (! Schema::hasTable(DatabaseConst::PAYMENT_METHODS())) {
        Schema::create(DatabaseConst::PAYMENT_METHODS(), function ($table) {
            $table->increments('id');
            $table->string('bank_name', 100);
            $table->string('account_name', 150);
            $table->string('account_number', 50);
            $table->text('bank_logo')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('sort_order')->default(1);
            $table->tinyInteger('is_active')->default(1);
            $table->string('created_by', 36)->nullable();
            $table->string('updated_by', 36)->nullable();
            $table->string('deleted_by', 36)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    if (! Schema::hasTable(DatabaseConst::PAYMENT_SETTINGS())) {
        Schema::create(DatabaseConst::PAYMENT_SETTINGS(), function ($table) {
            $table->increments('id');
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }
});

test('guest is redirected to login from payment routes', function () {
    $this->get(route('admin.payment.index'))->assertRedirect(route('login'));
    $this->get(route('admin.payment.add'))->assertRedirect(route('login'));
});

test('admin can view payment index with banks and whatsapp tabs', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.payment.index', ['tab' => 'banks']))
        ->assertOk()
        ->assertSee('Transfer Bank');

    $this->actingAs($admin)
        ->get(route('admin.payment.index', ['tab' => 'whatsapp']))
        ->assertOk()
        ->assertSee('WhatsApp Admin');
});

test('admin can create, update, and soft delete payment method', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    // Create
    $response = $this->actingAs($admin)->post(route('admin.payment.create'), [
        'bank_name' => 'Bank Jago Test',
        'account_name' => 'PT Test Jago',
        'account_number' => '1029384756',
        'bank_logo' => 'https://example.com/jago.png',
        'instructions' => 'Transfer nominal tepat',
        'is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.payment.index', ['tab' => 'banks']));

    $created = DB::table(DatabaseConst::PAYMENT_METHODS())
        ->where('account_number', '1029384756')
        ->first();

    expect($created)->not->toBeNull();
    expect($created->bank_name)->toBe('Bank Jago Test');

    // Toggle status via POST
    $this->actingAs($admin)->post(route('admin.payment.toggleStatus', ['id' => $created->id]))
        ->assertRedirect(route('admin.payment.index', ['tab' => 'banks']));

    $toggled = DB::table(DatabaseConst::PAYMENT_METHODS())->where('id', $created->id)->first();
    expect((int) $toggled->is_active)->toBe(0);

    // Delete via DELETE
    $this->actingAs($admin)->delete(route('admin.payment.delete', ['id' => $created->id]))
        ->assertRedirect(route('admin.payment.index', ['tab' => 'banks']));

    $deleted = DB::table(DatabaseConst::PAYMENT_METHODS())->where('id', $created->id)->first();
    expect($deleted->deleted_at)->not->toBeNull();

    // Clean up test record permanently
    DB::table(DatabaseConst::PAYMENT_METHODS())->where('id', $created->id)->delete();
});

test('admin can update whatsapp payment settings', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.payment.updateSettings'), [
        'whatsapp_number' => '628999888777',
        'whatsapp_name' => 'CS Testing',
        'whatsapp_message' => 'Halo CS konfirmasi',
        'whatsapp_is_active' => '1',
    ]);

    $response->assertRedirect(route('admin.payment.index', ['tab' => 'whatsapp']));

    $savedNumber = DB::table(DatabaseConst::PAYMENT_SETTINGS())
        ->where('key', 'whatsapp_number')
        ->value('value');

    expect($savedNumber)->toBe('628999888777');
});
