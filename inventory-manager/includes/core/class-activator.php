<?php
namespace InventoryManager\Core;

class Activator {
    public static function activate() {
        $database = new Database();
        $database->create_tables();
        self::add_capabilities();
        self::add_default_data();
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }

    private static function add_capabilities() {
        $roles = ['administrator', 'shop_manager'];
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('manage_inventory');
                $role->add_cap('manage_purchases');
                $role->add_cap('manage_suppliers');
                $role->add_cap('view_reports');
            }
        }
    }

    private static function add_default_data() {
        global $wpdb;
        $database = new Database();
        
        // Add default store
        $stores_table = $database->get_table('stores');
        if ($stores_table) {
            $wpdb->insert($stores_table, [
                'name' => 'Main Warehouse',
                'address' => 'Primary storage location',
                'manager' => 'Store Manager',
                'created_by' => 1,
                'updated_by' => 1
            ]);
        }
    }
}