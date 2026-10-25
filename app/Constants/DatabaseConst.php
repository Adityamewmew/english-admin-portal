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

    public static function COURSE_CLASSES(): string
    {
        return 'course_classes';
    }

    public static function USER_CLASS_ACCESS(): string
    {
        return 'user_class_access';
    }

    public static function USER_DAILY_AI_USAGE(): string
    {
        return 'user_daily_ai_usage';
    }

    public static function PAYMENT_METHODS(): string
    {
        return 'payment_methods';
    }

    public static function PAYMENT_SETTINGS(): string
    {
        return 'payment_settings';
    }

    public static function PAYMENT_TRANSACTIONS(): string
    {
        return 'payment_transactions';
    }

    public static function USER_BANK_ACCOUNTS(): string
    {
        return 'user_bank_accounts';
    }

    public static function WITHDRAWALS(): string
    {
        return 'withdrawals';
    }

    public static function WALLETS(): string
    {
        return 'wallets';
    }

    public static function WALLET_TRANSACTIONS(): string
    {
        return 'transactions';
    }

    public static function DB_CORE(): string
    {
        return config('database.connections.pgsql.database', 'db-eddys');
    }
}
