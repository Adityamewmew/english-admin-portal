<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSidebarSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Groups
        $groups = [
            [
                'key' => 'utama',
                'label' => 'Utama',
                'color' => 'blue',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'akademik',
                'label' => 'Kurikulum & Pembelajaran',
                'color' => 'emerald',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'sistem',
                'label' => 'Pengaturan Sistem',
                'color' => 'indigo',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($groups as $g) {
            DB::table('sidebar_menu_groups')->updateOrInsert(
                ['key' => $g['key']],
                $g
            );
        }

        // 2. Menus
        $menus = [
            [
                'id' => 1,
                'parent_id' => null,
                'label' => 'Dashboard',
                'route_name' => 'admin.dashboard',
                'icon' => '_admin._layout.icons.sidebar.dashboard',
                'group' => 'utama',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'parent_id' => null,
                'label' => 'Pengguna Aplikasi',
                'route_name' => 'admin.users.index',
                'icon' => '_admin._layout.icons.sidebar.user',
                'group' => 'utama',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'parent_id' => null,
                'label' => 'Modul Kurikulum',
                'route_name' => 'admin.modules.index',
                'icon' => '_admin._layout.icons.sidebar.data_master',
                'group' => 'akademik',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'parent_id' => null,
                'label' => 'Bank Soal (Item Bank)',
                'route_name' => 'admin.questions.index',
                'icon' => 'partials.icons.sidebar.clipboard-list',
                'group' => 'akademik',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'parent_id' => null,
                'label' => 'Manajemen Menu',
                'route_name' => 'admin.sidebar_menu.index',
                'icon' => '_admin._layout.icons.sidebar.settings',
                'group' => 'sistem',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($menus as $m) {
            DB::table('sidebar_menus')->updateOrInsert(
                ['id' => $m['id']],
                $m
            );

            // Grant access to access_type = 1 (Administrator)
            DB::table('sidebar_menu_accesses')->updateOrInsert(
                ['sidebar_menu_id' => $m['id'], 'access_type' => 1],
                [
                    'sidebar_menu_id' => $m['id'],
                    'access_type' => 1,
                    'created_at' => now(),
                ]
            );
        }
    }
}
