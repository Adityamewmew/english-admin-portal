<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insert or update parent menu "Pembayaran" (ID 80)
        DB::table('sidebar_menus')->updateOrInsert(
            ['id' => 80],
            [
                'parent_id' => null,
                'label' => 'Pembayaran',
                'route_name' => null,
                'icon' => '_admin._layout.icons.sidebar.payment',
                'group' => 'akademik',
                'sort_order' => 5,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Set "Metode Pembayaran" (ID 8) as child of ID 80
        DB::table('sidebar_menus')->where('id', 8)->update([
            'parent_id' => 80,
            'icon' => null,
            'sort_order' => 1,
            'updated_at' => now(),
        ]);

        // 3. Set "Transaksi Masuk" (ID 9) as child of ID 80
        DB::table('sidebar_menus')->where('id', 9)->update([
            'parent_id' => 80,
            'icon' => null,
            'sort_order' => 2,
            'updated_at' => now(),
        ]);

        // 4. Grant access to Administrator (access_type = 1)
        DB::table('sidebar_menu_accesses')->updateOrInsert(
            ['sidebar_menu_id' => 80, 'access_type' => 1],
            ['created_at' => now()]
        );

        // 5. Clear sidebar caches
        Cache::flush();
    }

    public function down(): void
    {
        DB::table('sidebar_menus')->where('id', 8)->update([
            'parent_id' => null,
            'icon' => '_admin._layout.icons.sidebar.payment',
            'sort_order' => 5,
            'updated_at' => now(),
        ]);

        DB::table('sidebar_menus')->where('id', 9)->update([
            'parent_id' => null,
            'icon' => '_admin._layout.icons.sidebar.payment',
            'sort_order' => 6,
            'updated_at' => now(),
        ]);

        DB::table('sidebar_menu_accesses')->where('sidebar_menu_id', 80)->delete();
        DB::table('sidebar_menus')->where('id', 80)->delete();

        Cache::flush();
    }
};
