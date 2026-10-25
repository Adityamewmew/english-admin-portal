<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
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

        // Clean up legacy redundant menu IDs if present
        DB::table('sidebar_menus')->whereIn('id', [302, 303])->delete();
        DB::table('sidebar_menu_accesses')->whereIn('sidebar_menu_id', [302, 303])->delete();

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
                'id' => 304,
                'parent_id' => null,
                'label' => 'Rekap Nilai Siswa',
                'route_name' => 'admin.gradebook.index',
                'icon' => '_admin._layout.icons.sidebar.gradebook',
                'group' => 'utama',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // PARENT ACCORDION: Modul Kurikulum
            [
                'id' => 3,
                'parent_id' => null,
                'label' => 'Modul Kurikulum',
                'route_name' => null,
                'icon' => '_admin._layout.icons.sidebar.data_master',
                'group' => 'akademik',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // SUBMENU 1: Modul Belajar (CRUD Modules)
            [
                'id' => 301,
                'parent_id' => 3,
                'label' => 'Modul Belajar',
                'route_name' => 'admin.modules.index',
                'icon' => null,
                'group' => 'akademik',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // SUBMENU 2: Bank Soal Placement (CRUD Placement Test Questions)
            [
                'id' => 4,
                'parent_id' => 3,
                'label' => 'Bank Soal Placement',
                'route_name' => 'admin.questions.index',
                'icon' => null,
                'group' => 'akademik',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 6,
                'parent_id' => null,
                'label' => 'Manajemen Sertifikat',
                'route_name' => 'admin.certificates.index',
                'icon' => '_admin._layout.icons.sidebar.data_master',
                'group' => 'akademik',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'parent_id' => null,
                'label' => 'Manajemen Kelas',
                'route_name' => 'admin.classes.index',
                'icon' => '_admin._layout.icons.sidebar.data_master',
                'group' => 'akademik',
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'parent_id' => null,
                'label' => 'Metode Pembayaran',
                'route_name' => 'admin.payment.index',
                'icon' => '_admin._layout.icons.sidebar.payment',
                'group' => 'akademik',
                'sort_order' => 5,
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

        // Clear sidebar cache
        Cache::flush();
    }
}
