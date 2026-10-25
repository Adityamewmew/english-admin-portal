<?php

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function () {
    if (! Schema::hasTable(DatabaseConst::COURSE_CLASSES())) {
        Schema::create(DatabaseConst::COURSE_CLASSES(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('cefr', 10)->unique();
            $table->string('name', 150);
            $table->bigInteger('price')->default(0);
            $table->bigInteger('original_price')->nullable();
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
    }

    if (! Schema::hasTable(DatabaseConst::USER_CLASS_ACCESS())) {
        Schema::create(DatabaseConst::USER_CLASS_ACCESS(), function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36);
            $table->string('cefr', 10);
            $table->string('tier', 20)->default('basic');
            $table->string('status', 20)->default('active');
            $table->string('granted_by', 50)->default('system');
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
        });
    }

    if (! Schema::hasTable(DatabaseConst::PAYMENT_TRANSACTIONS())) {
        Schema::create(DatabaseConst::PAYMENT_TRANSACTIONS(), function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('order_id', 50)->unique();
            $table->string('user_id', 36);
            $table->string('cefr', 10);
            $table->string('tier', 20)->default('basic');
            $table->bigInteger('amount')->default(0);
            $table->string('payment_category', 50)->default('manual_transfer');
            $table->string('payment_channel', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('transfer_proof_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('approved_by', 36)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
});

test('guest is redirected to login from class confirmations route', function () {
    $this->get(route('admin.classes.confirmations'))->assertRedirect(route('login'));
});

test('admin can view class confirmations page with waiting_approval transactions', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    $studentId = (string) Str::uuid();
    DB::table(DatabaseConst::USER())->insert([
        'id' => $studentId,
        'name' => 'Waiting Approval Student',
        'email' => 'waiting-'.Str::random(6).'@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
    ]);

    DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())->insert([
        'id' => (string) Str::uuid(),
        'order_id' => 'INV-WAITING-01',
        'user_id' => $studentId,
        'cefr' => 'A2',
        'tier' => 'basic',
        'amount' => 149000,
        'payment_category' => 'manual_transfer',
        'payment_channel' => 'BCA',
        'status' => 'waiting_approval',
        'transfer_proof_url' => 'https://example.com/proof.jpg',
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.classes.confirmations'))
        ->assertStatus(200)
        ->assertSee('Konfirmasi Kelas Siswa')
        ->assertSee('INV-WAITING-01')
        ->assertSee('Waiting Approval Student')
        ->assertSee('Perlu ACC');
});

test('admin can approve manual transfer to grant class access', function () {
    $admin = User::factory()->make([
        'id' => (string) Str::uuid(),
        'access_type' => UserConst::SUPERADMIN,
        'role' => 'admin',
    ]);

    $studentId = (string) Str::uuid();
    DB::table(DatabaseConst::USER())->insert([
        'id' => $studentId,
        'name' => 'Test Student Approval',
        'email' => 'test-approval-'.Str::random(6).'@example.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
    ]);

    $txId = (string) Str::uuid();
    DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())->insert([
        'id' => $txId,
        'order_id' => 'TRX-TEST-'.strtoupper(Str::random(6)),
        'user_id' => $studentId,
        'cefr' => 'B1',
        'tier' => 'pro',
        'amount' => 199000,
        'payment_category' => 'manual_transfer',
        'payment_channel' => 'BCA',
        'status' => 'verification',
        'transfer_proof_url' => 'https://example.com/proof.jpg',
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.payment_transactions.approve', $txId))
        ->assertRedirect();

    // Verify transaction is paid
    $tx = DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())->where('id', $txId)->first();
    expect($tx->status)->toBe('paid');

    // Verify class access is active
    $access = DB::table(DatabaseConst::USER_CLASS_ACCESS())
        ->where('user_id', $studentId)
        ->where('cefr', 'B1')
        ->first();

    expect($access)->not->toBeNull();
    expect($access->tier)->toBe('pro');
    expect($access->status)->toBe('active');
});
