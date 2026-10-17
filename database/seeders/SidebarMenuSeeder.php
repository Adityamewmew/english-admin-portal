<?php

namespace Database\Seeders;

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SidebarMenuSeeder extends Seeder
{
    /**
     * Seed sidebar_menu_groups, sidebar_menus, and sidebar_menu_accesses tables.
     */
    public function run(): void
    {
        DB::table(DatabaseConst::SIDEBAR_MENU_GROUP())->truncate();
        DB::table(DatabaseConst::SIDEBAR_MENU())->truncate();
        DB::table(DatabaseConst::SIDEBAR_MENU_ACCESS())->truncate();

        $now = now();

        // =====================================================================
        // GROUPS
        // =====================================================================
        DB::table(DatabaseConst::SIDEBAR_MENU_GROUP())->insert([
            ['key' => 'utama',     'label' => 'Main Menu',               'color' => 'blue',    'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'akademik',  'label' => 'Academic & Curriculum',   'color' => 'emerald', 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sistem',    'label' => 'System Settings',         'color' => 'indigo',  'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $superadmin = UserConst::SUPERADMIN;
        $superadminOnly = [$superadmin];

        // 1. Dashboard
        $dashboardId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Dashboard',
            'route_name' => 'admin.dashboard',
            'icon' => '_admin._layout.icons.sidebar.dashboard',
            'group' => 'utama',
            'sort_order' => 10,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($dashboardId, $superadminOnly, $now);

        // 2. Users
        $penggunaId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Users',
            'route_name' => 'admin.users.index',
            'icon' => '_admin._layout.icons.sidebar.user',
            'group' => 'utama',
            'sort_order' => 20,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($penggunaId, $superadminOnly, $now);

        // 3. Parent: Curriculum Management
        $curriculumParentId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Curriculum Management',
            'route_name' => null,
            'icon' => '_admin._layout.icons.sidebar.data_master',
            'group' => 'utama',
            'sort_order' => 15,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($curriculumParentId, $superadminOnly, $now);

        // 3.1 Modules
        $modulesId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'parent_id' => $curriculumParentId,
            'label' => 'Modules',
            'route_name' => 'admin.modules.index',
            'icon' => null,
            'group' => 'utama',
            'sort_order' => 1,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($modulesId, $superadminOnly, $now);

        // 3.2 Sub Modules
        $subModulesId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'parent_id' => $curriculumParentId,
            'label' => 'Sub Modules',
            'route_name' => 'admin.sub_modules.index',
            'icon' => null,
            'group' => 'utama',
            'sort_order' => 2,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($subModulesId, $superadminOnly, $now);

        // 3.3 Placement Test
        $placementId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'parent_id' => $curriculumParentId,
            'label' => 'Placement Test',
            'route_name' => 'admin.questions.index',
            'icon' => null,
            'group' => 'utama',
            'sort_order' => 3,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($placementId, $superadminOnly, $now);

        // 4. Menu Settings
        $sidebarMgmtId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Menu Settings',
            'route_name' => 'admin.sidebar_menu.index',
            'icon' => '_admin._layout.icons.sidebar.settings',
            'group' => 'sistem',
            'sort_order' => 10,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($sidebarMgmtId, $superadminOnly, $now);

    }

    /**
     * Insert access records for a given sidebar menu item.
     *
     * @param  array<int>  $accessTypes
     */
    private function assignAccess(int $sidebarMenuId, array $accessTypes, mixed $now): void
    {
        $inserts = array_map(fn ($type) => [
            'sidebar_menu_id' => $sidebarMenuId,
            'access_type' => $type,
            'created_at' => $now,
        ], $accessTypes);

        DB::table(DatabaseConst::SIDEBAR_MENU_ACCESS())->insert($inserts);
    }
}
