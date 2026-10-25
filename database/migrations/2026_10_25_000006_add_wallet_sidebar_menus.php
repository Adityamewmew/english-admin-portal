<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Submenu: "Saldo & Dompet" (ID 81) under Pembayaran (ID 80)
        DB::table('sidebar_menus')->updateOrInsert(
            ['id' => 81],
            [
                'parent_id' => 80,
                'label' => 'Saldo & Dompet',
                'route_name' => 'admin.wallet.index',
                'icon' => null,
                'group' => 'akademik',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Submenu: "Kelola Penarikan" (ID 82) under Pembayaran (ID 80)
        DB::table('sidebar_menus')->updateOrInsert(
            ['id' => 82],
            [
                'parent_id' => 80,
                'label' => 'Kelola Penarikan',
                'route_name' => 'admin.wallet.withdrawals',
                'icon' => null,
                'group' => 'akademik',
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Grant access to Administrator (access_type = 1)
        foreach ([81, 82] as $menuId) {
            DB::table('sidebar_menu_accesses')->updateOrInsert(
                ['sidebar_menu_id' => $menuId, 'access_type' => 1],
                ['created_at' => now()]
            );
        }

        Cache::flush();
    }

    public function down(): void
    {
        DB::table('sidebar_menu_accesses')->whereIn('sidebar_menu_id', [81, 82])->delete();
        DB::table('sidebar_menus')->whereIn('id', [81, 82])->delete();
        Cache::flush();
    }
};
