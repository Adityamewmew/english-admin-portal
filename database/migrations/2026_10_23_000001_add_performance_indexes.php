<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexes = [
            // Table: module_sections (Relasi materi pembelajaran)
            'module_sections' => [
                'CREATE INDEX IF NOT EXISTS idx_module_sections_module_id ON module_sections (module_id, order_index)',
                'CREATE INDEX IF NOT EXISTS idx_module_sections_sub_module_id ON module_sections (sub_module_id)',
            ],

            // Table: sub_modules (Relasi sub-modul)
            'sub_modules' => [
                'CREATE INDEX IF NOT EXISTS idx_sub_modules_module_id ON sub_modules (module_id, order_index)',
            ],

            // Table: user_module_progress (Progress modul siswa)
            'user_module_progress' => [
                'CREATE UNIQUE INDEX IF NOT EXISTS idx_user_module_progress_user_mod ON user_module_progress (user_id, module_id)',
                'CREATE INDEX IF NOT EXISTS idx_user_module_progress_user_status ON user_module_progress (user_id, status)',
            ],

            // Table: curriculum_modules (Filter CEFR & level modul)
            'curriculum_modules' => [
                'CREATE INDEX IF NOT EXISTS idx_curriculum_modules_cefr_level ON curriculum_modules (cefr, level_id, order_index)',
            ],

            // Table: item_bank (Bank soal placement test)
            'item_bank' => [
                'CREATE INDEX IF NOT EXISTS idx_item_bank_skill_cefr ON item_bank (skill, cefr)',
            ],

            // Table: placement_results (Hasil tes penempatan)
            'placement_results' => [
                'CREATE INDEX IF NOT EXISTS idx_placement_results_user_created ON placement_results (user_id, created_at)',
            ],

            // Table: call_sessions (Riwayat voice session AI)
            'call_sessions' => [
                'CREATE INDEX IF NOT EXISTS idx_call_sessions_user_created ON call_sessions (user_id, created_at)',
            ],

            // Table: chat_sessions & chat_messages (Chatbot AI)
            'chat_sessions' => [
                'CREATE INDEX IF NOT EXISTS idx_chat_sessions_user_created ON chat_sessions (user_id, created_at)',
            ],
            'chat_messages' => [
                'CREATE INDEX IF NOT EXISTS idx_chat_messages_session_created ON chat_messages (session_id, created_at)',
            ],

            // Table: users (Filter siswa / admin)
            'users' => [
                'CREATE INDEX IF NOT EXISTS idx_users_access_cefr ON users (access_type, current_cefr)',
            ],
        ];

        foreach ($indexes as $table => $queries) {
            if (Schema::hasTable($table)) {
                foreach ($queries as $sql) {
                    try {
                        DB::statement($sql);
                    } catch (\Throwable $e) {
                        // Ignore if already exists or not supported in specific driver
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dropIndexes = [
            'idx_module_sections_module_id',
            'idx_module_sections_sub_module_id',
            'idx_sub_modules_module_id',
            'idx_user_module_progress_user_mod',
            'idx_user_module_progress_user_status',
            'idx_curriculum_modules_cefr_level',
            'idx_item_bank_skill_cefr',
            'idx_placement_results_user_created',
            'idx_call_sessions_user_created',
            'idx_chat_sessions_user_created',
            'idx_chat_messages_session_created',
            'idx_users_access_cefr',
        ];

        foreach ($dropIndexes as $indexName) {
            try {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
};
