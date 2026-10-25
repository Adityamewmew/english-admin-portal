<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('user_bank_accounts')) {
            Schema::create('user_bank_accounts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('user_id');
                $table->string('bank_code', 20); // e.g. 014 (BCA), 002 (BRI)
                $table->string('bank_swift_code', 20)->nullable(); // e.g. CENAIDJA
                $table->string('bank_name', 100);
                $table->string('account_number', 50);
                $table->string('account_name', 150);
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_verified')->default(false);
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'is_primary']);
            });
        }

        if (! Schema::hasTable('withdrawals')) {
            Schema::create('withdrawals', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('user_id');
                $table->uuid('bank_account_id');
                $table->string('reference_no', 100)->unique();
                $table->string('singapay_trx_id', 100)->nullable();
                $table->decimal('amount', 15, 2); // Net nominal to beneficiary
                $table->decimal('admin_fee', 15, 2)->default(2500); // Admin fee charged to user
                $table->decimal('total_debit', 15, 2); // amount + admin_fee
                $table->decimal('singapay_cost', 15, 2)->default(1200); // SingaPay actual cost
                $table->string('status', 30)->default('pending'); // pending, approved, processing, success, rejected, failed
                $table->text('notes')->nullable();
                $table->string('failure_reason')->nullable();
                $table->string('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'status']);
                $table->index('reference_no');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('user_bank_accounts');
    }
};
