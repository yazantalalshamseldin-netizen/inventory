<?php
namespace InventoryManager\Admin;

class Admin {
    private $tabs = [];

    public function __construct() {
        $this->init_tabs();
        $this->register_hooks();
    }

    private function init_tabs() {
        // Create tab instances with proper implementations
        $this->tabs['inventory'] = new Inventory_Tab();
        $this->tabs['purchase'] = new Purchase_Tab();
        $this->tabs['product-entry'] = new Product_Entry_Tab();
        $this->tabs['suppliers'] = new Suppliers_Tab();
        $this->tabs['stores'] = new Store_Location_Tab();
        $this->tabs['customers'] = new Customers_Tab();
        $this->tabs['quotations'] = new Quotation_Tab();
        $this->tabs['invoices'] = new Invoice_Tab();
        $this->tabs['transfers'] = new Transfer_Tab();
    }

    private function register_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'handle_requests']);
    }

    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            'Inventory Manager',
            'Inventory',
            'manage_options',
            'inventory-manager',
            [$this, 'display_admin_page'],
            'dashicons-clipboard',
            30
        );

        // Submenus for each tab
        foreach ($this->tabs as $tab_key => $tab) {
            add_submenu_page(
                'inventory-manager',
                $tab->get_title(),
                $tab->get_title(),
                'manage_options',
                'inventory-manager&tab=' . $tab_key,
                [$this, 'display_admin_page']
            );
        }
    }

    public function display_admin_page() {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'inventory';
        
        // Check if tab exists, fallback to inventory
        if (!isset($this->tabs[$current_tab])) {
            $current_tab = 'inventory';
        }
        ?>
        <div class="wrap">
            <h1>Inventory Manager Pro</h1>
            
            <nav class="nav-tab-wrapper">
                <?php foreach ($this->tabs as $tab_key => $tab): ?>
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=' . $tab_key); ?>" 
                       class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                       <?php echo $tab->get_title(); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="tab-content">
                <?php 
                // Display admin notices
                settings_errors('inventory_messages');
                
                // Render the current tab
                $this->tabs[$current_tab]->render(); 
                ?>
            </div>
        </div>
        <?php
    }

    public function handle_requests() {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'inventory';
        
        // Check if tab exists, fallback to inventory
        if (!isset($this->tabs[$current_tab])) {
            $current_tab = 'inventory';
        }
        
        if (isset($this->tabs[$current_tab])) {
            $this->tabs[$current_tab]->handle_request();
        }
    }
}