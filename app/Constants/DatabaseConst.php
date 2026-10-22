<?php

namespace App\Constants;

class DatabaseConst
{
    const SQL_READ = 'pgsql';

    public static function USER(): string
    {
        return 'users';
    }

    public static function SIDEBAR_MENU(): string
    {
        return 'sidebar_menus';
    }

    public static function SIDEBAR_MENU_ACCESS(): string
    {
        return 'sidebar_menu_accesses';
    }

    public static function SIDEBAR_MENU_GROUP(): string
    {
        return 'sidebar_menu_groups';
    }

    public static function CURRICULUM_MODULES(): string
    {
        return 'curriculum_modules';
    }

    public static function CURRICULUM_LEVELS(): string
    {
        return 'curriculum_levels';
    }

    public static function MODULE_SECTIONS(): string
    {
        return 'module_sections';
    }

    public static function SUB_MODULES(): string
    {
        return 'sub_modules';
    }

    public static function USER_MODULE_PROGRESS(): string
    {
        return 'user_module_progress';
    }

    public static function USER_SUB_MODULE_PROGRESS(): string
    {
        return 'user_sub_module_progress';
    }

    public static function ITEM_BANK(): string
    {
        return 'item_bank';
    }

    public static function PLACEMENT_RESULTS(): string
    {
        return 'placement_results';
    }

    public static function CERTIFICATE_TEMPLATES(): string
    {
        return 'certificate_templates';
    }

    public static function USER_CERTIFICATES(): string
    {
        return 'user_certificates';
    }

    public static function DB_CORE(): string
    {
        return config('database.connections.pgsql.database', 'db-eddys');
    }
}
