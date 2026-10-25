<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Convert "Manajemen Kelas" (ID 7) to parent accordion
        DB::table('sidebar_menus')->where('id', 7)->update([
            'route_name' => null,
            'updated_at' => now(),
        ]);

        // 2. Add or update Submenu 1: "Daftar Kelas" (ID 701)
        DB::table('sidebar_menus')->updateOrInsert(
            ['id' => 701],
            [
                'parent_id' => 7,
                'label' => 'Daftar Kelas',
                'route_name' => 'admin.classes.index',
                'icon' => null,
                'group' => 'akademik',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Add or update Submenu 2: "Konfirmasi Kelas" (ID 702)
        DB::table('sidebar_menus')->updateOrInsert(
            ['id' => 702],
            [
                'parent_id' => 7,
                'label' => 'Konfirmasi Kelas',
                'route_name' => 'admin.classes.confirmations',
                'icon' => null,
                'group' => 'akademik',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Grant access to Administrator (access_type = 1)
        foreach ([701, 702] as $menuId) {
            DB::table('sidebar_menu_accesses')->updateOrInsert(
                ['sidebar_menu_id' => $menuId, 'access_type' => 1],
                ['created_at' => now()]
            );
        }

        // 5. Clear sidebar caches
        Cache::flush();
    }

    public function down(): void
    {
        DB::table('sidebar_menu_accesses')->whereIn('sidebar_menu_id', [701, 702])->delete();
        DB::table('sidebar_menus')->whereIn('id', [701, 702])->delete();

        DB::table('sidebar_menus')->where('id', 7)->update([
            'route_name' => 'admin.classes.index',
            'updated_at' => now(),
        ]);

        Cache::flush();
    }
};
