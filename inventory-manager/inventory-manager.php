<?php
/**
 * Plugin Name: Inventory Manager Pro
 * Description: Complete inventory and business management system
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('INVENTORY_MANAGER_VERSION', '1.0.0');
define('INVENTORY_MANAGER_PATH', plugin_dir_path(__FILE__));
define('INVENTORY_MANAGER_URL', plugin_dir_url(__FILE__));

// Create all database tables on activation
register_activation_hook(__FILE__, 'inventory_manager_activate');
function inventory_manager_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Inventory items table
    $table_name = $wpdb->prefix . 'inventory_items';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_code varchar(100) NOT NULL,
        description text NOT NULL,
        quantity int(11) NOT NULL DEFAULT 0,
        store_location varchar(200) NOT NULL,
        cost_price decimal(10,2) NOT NULL,
        old_sale_price decimal(10,2) NOT NULL,
        new_sale_price decimal(10,2) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY product_code (product_code)
    ) $charset_collate;";

    // Suppliers table
    $suppliers_table = $wpdb->prefix . 'inventory_suppliers';
    $sql .= "CREATE TABLE $suppliers_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(200) NOT NULL,
        contact_person varchar(200),
        email varchar(200),
        phone varchar(50),
        address text,
        tax_id varchar(100),
        payment_terms text,
        created_by bigint(20) NOT NULL,
        updated_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Customers table
    $customers_table = $wpdb->prefix . 'inventory_customers';
    $sql .= "CREATE TABLE $customers_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(200) NOT NULL,
        phone varchar(50),
        company varchar(200),
        address text,
        city varchar(100),
        state varchar(100),
        postal_code varchar(20),
        country varchar(100),
        created_by bigint(20) NOT NULL,
        updated_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";

    // Stores table
    $stores_table = $wpdb->prefix . 'inventory_stores';
    $sql .= "CREATE TABLE $stores_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(200) NOT NULL,
        code varchar(50) NOT NULL,
        manager varchar(200),
        phone varchar(50),
        address text,
        city varchar(100),
        state varchar(100),
        postal_code varchar(20),
        country varchar(100),
        created_by bigint(20) NOT NULL,
        updated_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY code (code)
    ) $charset_collate;";

    // Purchases table
    $purchases_table = $wpdb->prefix . 'inventory_purchases';
    $sql .= "CREATE TABLE $purchases_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        purchase_no varchar(50) NOT NULL,
        reference_invoice varchar(100),
        date_requested date NOT NULL,
        date_arrived date,
        supplier_id mediumint(9) NOT NULL,
        total_invoice decimal(10,4) NOT NULL DEFAULT 0.0000,
        total_weight decimal(10,3) NOT NULL DEFAULT 0.000,
        freight_cost decimal(10,4) NOT NULL DEFAULT 0.0000,
        customs_cost decimal(10,4) NOT NULL DEFAULT 0.0000,
        other_costs decimal(10,4) NOT NULL DEFAULT 0.0000,
        profit_margin decimal(5,4) NOT NULL DEFAULT 0.3000,
        status varchar(20) DEFAULT 'draft',
        created_by bigint(20) NOT NULL,
        updated_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY purchase_no (purchase_no)
    ) $charset_collate;";

    // Purchase items table
    $purchase_items_table = $wpdb->prefix . 'inventory_purchase_items';
    $sql .= "CREATE TABLE $purchase_items_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        purchase_id mediumint(9) NOT NULL,
        product_code varchar(100) NOT NULL,
        description text NOT NULL,
        factory_price decimal(10,4) NOT NULL DEFAULT 0.0000,
        weight decimal(10,3) NOT NULL DEFAULT 0.000,
        quantity int(11) NOT NULL DEFAULT 0,
        calculated_cost decimal(10,4) NOT NULL DEFAULT 0.0000,
        calculated_sale_price decimal(10,4) NOT NULL DEFAULT 0.0000,
        store_location varchar(200) NOT NULL,
        created_by bigint(20) NOT NULL,
        updated_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Audit log table
    $audit_log_table = $wpdb->prefix . 'inventory_audit_log';
    $sql .= "CREATE TABLE $audit_log_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        action varchar(50) NOT NULL,
        table_name varchar(100) NOT NULL,
        record_id mediumint(9) NOT NULL,
        old_data text,
        new_data text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add test data
    inventory_manager_add_test_data();
    
    // Add capabilities to roles
    inventory_manager_add_capabilities();
}

// Add user capabilities
function inventory_manager_add_capabilities() {
    $roles = ['administrator', 'shop_manager'];
    $capabilities = [
        'manage_inventory',
        'manage_purchases',
        'manage_suppliers',
        'manage_customers',
        'manage_stores',
        'view_reports'
    ];
    
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            foreach ($capabilities as $cap) {
                $role->add_cap($cap);
            }
        }
    }
}

// Add test data
function inventory_manager_add_test_data() {
    if (get_option('inventory_manager_test_data_added') === 'yes') {
        return;
    }

    global $wpdb;
    
    // Add test inventory items
    $table_name = $wpdb->prefix . 'inventory_items';
    $test_data = [
        [
            'product_code' => 'PROD-001',
            'description' => 'Wireless Bluetooth Mouse - Ergonomic design with smooth scrolling',
            'quantity' => 45,
            'store_location' => 'Warehouse A, Shelf B2',
            'cost_price' => 12.50,
            'old_sale_price' => 29.99,
            'new_sale_price' => 24.99
        ],
        [
            'product_code' => 'PROD-002',
            'description' => 'Mechanical Keyboard - RGB backlit with blue switches',
            'quantity' => 12,
            'store_location' => 'Warehouse B, Shelf C1',
            'cost_price' => 35.00,
            'old_sale_price' => 79.99,
            'new_sale_price' => 69.99
        ],
        [
            'product_code' => 'PROD-003',
            'description' => 'USB-C to USB-C Cable 2m - Fast charging and data transfer',
            'quantity' => 0,
            'store_location' => 'Drawer A, Main Office',
            'cost_price' => 3.50,
            'old_sale_price' => 14.99,
            'new_sale_price' => 12.99
        ]
    ];
    
    foreach ($test_data as $data) {
        $wpdb->insert($table_name, $data);
    }
    
    // Add test suppliers
    $suppliers_table = $wpdb->prefix . 'inventory_suppliers';
    $suppliers_data = [
        [
            'name' => 'ABC Electronics Ltd.',
            'contact_person' => 'John Smith',
            'email' => 'john@abcelectronics.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Tech Street, Silicon Valley, CA 94301',
            'tax_id' => 'TAX-123456789',
            'payment_terms' => 'Net 30 days, Bank transfer',
            'created_by' => 1,
            'updated_by' => 1
        ],
        [
            'name' => 'Global Components Inc.',
            'contact_person' => 'Sarah Johnson',
            'email' => 'sarah@globalcomponents.com',
            'phone' => '+1 (555) 987-6543',
            'address' => '456 Component Ave, Tech City, TX 75001',
            'tax_id' => 'TAX-987654321',
            'payment_terms' => 'Net 15 days, Credit card accepted',
            'created_by' => 1,
            'updated_by' => 1
        ]
    ];
    
    foreach ($suppliers_data as $data) {
        $wpdb->insert($suppliers_table, $data);
    }
    
    // Add test customers
    $customers_table = $wpdb->prefix . 'inventory_customers';
    $customers_data = [
        [
            'first_name' => 'Michael',
            'last_name' => 'Brown',
            'email' => 'michael.brown@example.com',
            'phone' => '+1 (555) 111-2233',
            'company' => 'Tech Solutions Inc.',
            'address' => '789 Business Rd, New York, NY 10001',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'United States',
            'created_by' => 1,
            'updated_by' => 1
        ],
        [
            'first_name' => 'Emily',
            'last_name' => 'Davis',
            'email' => 'emily.davis@example.com',
            'phone' => '+1 (555) 444-5566',
            'company' => 'Digital Innovations LLC',
            'address' => '321 Innovation Drive, San Francisco, CA 94102',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94102',
            'country' => 'United States',
            'created_by' => 1,
            'updated_by' => 1
        ]
    ];
    
    foreach ($customers_data as $data) {
        $wpdb->insert($customers_table, $data);
    }
    
    // Add test stores
    $stores_table = $wpdb->prefix . 'inventory_stores';
    $stores_data = [
        [
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'manager' => 'Robert Wilson',
            'phone' => '+1 (555) 777-8888',
            'address' => '100 Storage Avenue, Industrial District, Chicago, IL 60601',
            'city' => 'Chicago',
            'state' => 'IL',
            'postal_code' => '60601',
            'country' => 'United States',
            'created_by' => 1,
            'updated_by' => 1
        ],
        [
            'name' => 'Retail Store Downtown',
            'code' => 'STORE-DT',
            'manager' => 'Jennifer Lee',
            'phone' => '+1 (555) 999-0000',
            'address' => '200 Main Street, Downtown, Chicago, IL 60602',
            'city' => 'Chicago',
            'state' => 'IL',
            'postal_code' => '60602',
            'country' => 'United States',
            'created_by' => 1,
            'updated_by' => 1
        ]
    ];
    
    foreach ($stores_data as $data) {
        $wpdb->insert($stores_table, $data);
    }
    
    update_option('inventory_manager_test_data_added', 'yes');
}

// MANUALLY load all required files
function inventory_manager_load_files() {
    $files_to_load = [
        // Core classes
        'includes/core/class-database.php',
        'includes/core/class-audit-log.php',
        
        // Admin classes
        'includes/admin/class-admin.php',
        'includes/admin/class-inventory-tab.php',
        'includes/admin/class-purchase-tab.php',
        'includes/admin/class-product-entry-tab.php',
        'includes/admin/class-suppliers-tab.php',
        'includes/admin/class-store-location-tab.php',
        'includes/admin/class-customers-tab.php',
        'includes/admin/class-quotation-tab.php',
        'includes/admin/class-invoice-tab.php',
        'includes/admin/class-transfer-tab.php'
    ];
    
    foreach ($files_to_load as $file) {
        $file_path = INVENTORY_MANAGER_PATH . $file;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}

// Enqueue admin styles and scripts
add_action('admin_enqueue_scripts', 'inventory_manager_admin_scripts');
function inventory_manager_admin_scripts($hook) {
    if (strpos($hook, 'inventory-manager') !== false) {
        // Enqueue CSS
        wp_enqueue_style('inventory-manager-admin', INVENTORY_MANAGER_URL . 'assets/css/admin.css', [], INVENTORY_MANAGER_VERSION);
        wp_enqueue_style('inventory-manager-grids', INVENTORY_MANAGER_URL . 'assets/css/grids.css', [], INVENTORY_MANAGER_VERSION);
        wp_enqueue_style('inventory-manager-suppliers', INVENTORY_MANAGER_URL . 'assets/css/suppliers.css', [], INVENTORY_MANAGER_VERSION);
        wp_enqueue_style('inventory-manager-customers', INVENTORY_MANAGER_URL . 'assets/css/customers.css', [], INVENTORY_MANAGER_VERSION);
        wp_enqueue_style('inventory-manager-stores', INVENTORY_MANAGER_URL . 'assets/css/stores.css', [], INVENTORY_MANAGER_VERSION);
        
        // Enqueue JavaScript
        wp_enqueue_script('inventory-manager-calculations', INVENTORY_MANAGER_URL . 'assets/js/calculations.js', ['jquery'], INVENTORY_MANAGER_VERSION, true);
        
        // Localize script for AJAX
        wp_localize_script('inventory-manager-calculations', 'inventoryManager', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('inventory_manager_nonce')
        ]);
    }
}

// Initialize the plugin
add_action('plugins_loaded', 'inventory_manager_init');
function inventory_manager_init() {
    // Load all required files
    inventory_manager_load_files();
    
    // Initialize admin
    if (is_admin()) {
        new InventoryManager\Admin\Admin();
    }
}

// Add this function to handle redirects properly
function inventory_manager_safe_redirect($url) {
    if (!headers_sent()) {
        wp_redirect($url);
        exit;
    } else {
        // Fallback: JavaScript redirect if headers already sent
        echo '<script>window.location.href="' . esc_url($url) . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . esc_url($url) . '"></noscript>';
        exit;
    }
}