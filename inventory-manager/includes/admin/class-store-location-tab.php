<?php
namespace InventoryManager\Admin;

class Store_Location_Tab {
    private $database;
    private $audit_log;

    public function __construct() {
        $this->database = new \InventoryManager\Core\Database();
        $this->audit_log = new \InventoryManager\Core\AuditLog();
    }

    public function get_title() {
        return __('Store Locations', 'inventory-manager');
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_key($_GET['action']) : 'list';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action === 'edit' || $action === 'new') {
            $this->render_form($id);
        } else {
            $this->render_grid();
        }
    }

    private function render_form($id = 0) {
        global $wpdb;
        $store = $id ? $wpdb->get_row("SELECT * FROM {$this->database->get_table('stores')} WHERE id = $id") : null;
        ?>
        <div class="inventory-form-card">
            <div class="card-header">
                <h2><?php echo $id ? '✏️ Edit Store Location' : '🏪 Add New Store Location'; ?></h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=stores'); ?>" class="button button-secondary">
                    ← Back to Stores
                </a>
            </div>

            <form method="post" class="store-form">
                <?php wp_nonce_field('store_form', 'store_nonce'); ?>
                <input type="hidden" name="store_id" value="<?php echo $id ? $id : ''; ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-section">
                            <h3>📋 Store Information</h3>
                            
                            <div class="form-group">
                                <label for="name">Store Name *</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo $store ? esc_attr($store->name) : ''; ?>"
                                       placeholder="Main Warehouse">
                            </div>

                            <div class="form-group">
                                <label for="code">Store Code *</label>
                                <input type="text" id="code" name="code" required 
                                       value="<?php echo $store ? esc_attr($store->code) : $this->generate_store_code(); ?>"
                                       placeholder="WH-MAIN" readonly>
                                <small>Unique identifier for this store</small>
                            </div>

                            <div class="form-group">
                                <label for="manager">Store Manager</label>
                                <input type="text" id="manager" name="manager"
                                       value="<?php echo $store ? esc_attr($store->manager) : ''; ?>"
                                       placeholder="John Smith">
                            </div>

                            <div class="form-group">
                                <label for="phone">Store Phone</label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo $store ? esc_attr($store->phone) : ''; ?>"
                                       placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                    </div>

                    <div class="form-column">
                        <div class="form-section">
                            <h3>📍 Location Details</h3>
                            
                            <div class="form-group">
                                <label for="address">Full Address *</label>
                                <textarea id="address" name="address" required rows="4"
                                          placeholder="123 Business Street, City, State, ZIP Code"><?php echo $store ? esc_textarea($store->address) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city"
                                       value="<?php echo $store ? esc_attr($store->city) : ''; ?>"
                                       placeholder="New York">
                            </div>

                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state"
                                       value="<?php echo $store ? esc_attr($store->state) : ''; ?>"
                                       placeholder="NY">
                            </div>

                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code"
                                       value="<?php echo $store ? esc_attr($store->postal_code) : ''; ?>"
                                       placeholder="10001">
                            </div>

                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country"
                                       value="<?php echo $store ? esc_attr($store->country) : ''; ?>"
                                       placeholder="United States">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="store-stats">
                    <h3>📊 Inventory Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-label">Total Products:</span>
                            <span class="stat-value"><?php echo $this->get_store_statistic($id, 'total_products'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Total Value:</span>
                            <span class="stat-value">$<?php echo number_format($this->get_store_statistic($id, 'total_value'), 2); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Low Stock Items:</span>
                            <span class="stat-value"><?php echo $this->get_store_statistic($id, 'low_stock'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Out of Stock:</span>
                            <span class="stat-value"><?php echo $this->get_store_statistic($id, 'out_of_stock'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="save" class="button button-primary">
                        💾 <?php echo $id ? 'Update Store' : 'Save Store'; ?>
                    </button>
                    
                    <?php if ($id): ?>
                    <button type="submit" name="action" value="delete" class="button button-danger"
                            onclick="return confirm('Are you sure you want to delete this store location? This will affect inventory records.')">
                        🗑️ Delete Store
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=stores&action=new'); ?>" 
                       class="button button-secondary">
                        🏪 New Store
                    </a>
                </div>
            </form>

            <?php if ($id): ?>
            <div class="store-related-data">
                <div class="card-header">
                    <h3>📦 Current Inventory</h3>
                </div>
                <div class="store-inventory">
                    <?php echo $this->render_store_inventory($id); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_grid() {
        global $wpdb;
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $where = '';

        if (!empty($search_term)) {
            $where = $wpdb->prepare(
                "WHERE name LIKE %s OR code LIKE %s OR address LIKE %s OR manager LIKE %s",
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%'
            );
        }

        $stores = $wpdb->get_results("SELECT * FROM {$this->database->get_table('stores')} {$where} ORDER BY name");
        $total_count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->database->get_table('stores')} {$where}");
        ?>
        <div class="inventory-grid-card">
            <div class="card-header">
                <h2>🏪 Store Locations</h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=stores&action=new'); ?>" 
                   class="button button-primary">
                    🏪 New Store
                </a>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="get" class="inventory-search-form">
                    <input type="hidden" name="page" value="inventory-manager">
                    <input type="hidden" name="tab" value="stores">
                    <div class="search-box">
                        <input type="search" name="s" placeholder="🔍 Search stores by name, code, or address..." 
                               value="<?php echo esc_attr($search_term); ?>"
                               class="search-input">
                        <button type="submit" class="button button-primary search-button">Search</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=stores'); ?>" 
                               class="button button-secondary clear-search">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if (!empty($search_term)): ?>
            <div class="search-results-info">
                <span class="results-count">Found <?php echo $total_count; ?> store(s) for: "</span>
                <strong>"<?php echo esc_html($search_term); ?>"</strong>"
            </div>
            <?php endif; ?>

            <div class="inventory-table-container">
                <table class="inventory-table with-lines">
                    <thead>
                        <tr>
                            <th>Store Code</th>
                            <th>Store Name</th>
                            <th>Manager</th>
                            <th>Location</th>
                            <th>Total Products</th>
                            <th>Inventory Value</th>
                            <th>Stock Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stores)): ?>
                        <tr>
                            <td colspan="8" class="no-items">
                                <?php echo empty($search_term) ? 'No store locations found. Add your first store!' : 'No stores found for your search.'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($stores as $store): ?>
                            <?php
                            $stats = [
                                'total_products' => $this->get_store_statistic($store->id, 'total_products'),
                                'total_value' => $this->get_store_statistic($store->id, 'total_value'),
                                'low_stock' => $this->get_store_statistic($store->id, 'low_stock'),
                                'out_of_stock' => $this->get_store_statistic($store->id, 'out_of_stock')
                            ];
                            ?>
                            <tr>
                                <td class="store-code">
                                    <strong><?php echo esc_html($store->code); ?></strong>
                                </td>
                                <td class="store-name">
                                    <strong><?php echo esc_html($store->name); ?></strong>
                                    <?php if ($store->phone): ?>
                                        <br><small class="text-muted">📞 <?php echo esc_html($store->phone); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="manager"><?php echo esc_html($store->manager); ?></td>
                                <td class="location">
                                    <?php if ($store->address): ?>
                                        <span title="<?php echo esc_attr($store->address); ?>">
                                            <?php echo esc_html(wp_trim_words($store->address, 4)); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="total-products">
                                    <span class="stat-badge"><?php echo number_format($stats['total_products']); ?></span>
                                </td>
                                <td class="inventory-value">
                                    <strong>$<?php echo number_format($stats['total_value'], 2); ?></strong>
                                </td>
                                <td class="stock-status">
                                    <div class="stock-indicators">
                                        <?php if ($stats['low_stock'] > 0): ?>
                                            <span class="stock-warning" title="Low Stock Items">⚠️ <?php echo $stats['low_stock']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($stats['out_of_stock'] > 0): ?>
                                            <span class="stock-danger" title="Out of Stock Items">🔴 <?php echo $stats['out_of_stock']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($stats['low_stock'] == 0 && $stats['out_of_stock'] == 0): ?>
                                            <span class="stock-good">✅ Good</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=stores&action=edit&id=' . $store->id); ?>" 
                                       class="button button-small" title="Edit Store">
                                        ✏️
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inventory-manager&tab=stores&action=delete&id=' . $store->id), 'delete_store'); ?>" 
                                       class="button button-small button-danger" 
                                       onclick="return confirm('Are you sure?')"
                                       title="Delete Store">
                                        🗑️
                                    </a>
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=inventory&location=' . urlencode($store->name)); ?>" 
                                       class="button button-small button-success"
                                       title="View Inventory">
                                        📦 View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    private function get_store_statistic($store_id, $statistic) {
        global $wpdb;
        
        if (!$store_id) return 0;

        $store_name = $wpdb->get_var("SELECT name FROM {$this->database->get_table('stores')} WHERE id = $store_id");
        if (!$store_name) return 0;

        switch ($statistic) {
            case 'total_products':
                return $wpdb->get_var(
                    "SELECT COUNT(*) FROM {$this->database->get_table('items')} 
                     WHERE store_location = '" . esc_sql($store_name) . "'"
                );

            case 'total_value':
                return $wpdb->get_var(
                    "SELECT COALESCE(SUM(cost_price * quantity), 0) FROM {$this->database->get_table('items')} 
                     WHERE store_location = '" . esc_sql($store_name) . "'"
                );

            case 'low_stock':
                return $wpdb->get_var(
                    "SELECT COUNT(*) FROM {$this->database->get_table('items')} 
                     WHERE store_location = '" . esc_sql($store_name) . "' 
                     AND quantity > 0 AND quantity < 10"
                );

            case 'out_of_stock':
                return $wpdb->get_var(
                    "SELECT COUNT(*) FROM {$this->database->get_table('items')} 
                     WHERE store_location = '" . esc_sql($store_name) . "' 
                     AND quantity = 0"
                );

            default:
                return 0;
        }
    }

    private function render_store_inventory($store_id) {
        global $wpdb;
        
        $store_name = $wpdb->get_var("SELECT name FROM {$this->database->get_table('stores')} WHERE id = $store_id");
        if (!$store_name) return '<p class="no-data">Store not found.</p>';

        $inventory = $wpdb->get_results(
            "SELECT * FROM {$this->database->get_table('items')} 
             WHERE store_location = '" . esc_sql($store_name) . "'
             ORDER BY product_code"
        );

        if (empty($inventory)) {
            return '<p class="no-data">No inventory found in this store.</p>';
        }

        $output = '<div class="inventory-list">';
        $output .= '<table class="inventory-table with-lines">';
        $output .= '<thead><tr>
            <th>Product Code</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Cost Price</th>
            <th>Sale Price</th>
            <th>Total Value</th>
            <th>Status</th>
        </tr></thead>';
        $output .= '<tbody>';

        foreach ($inventory as $item) {
            $status_class = '';
            $status_text = '';
            
            if ($item->quantity == 0) {
                $status_class = 'out-of-stock';
                $status_text = 'Out of Stock';
            } elseif ($item->quantity < 10) {
                $status_class = 'low-stock';
                $status_text = 'Low Stock';
            } else {
                $status_class = 'in-stock';
                $status_text = 'In Stock';
            }

            $output .= '<tr>
                <td>' . esc_html($item->product_code) . '</td>
                <td>' . esc_html(wp_trim_words($item->description, 5)) . '</td>
                <td>' . number_format($item->quantity) . '</td>
                <td>$' . number_format($item->cost_price, 4) . '</td>
                <td>$' . number_format($item->new_sale_price, 4) . '</td>
                <td><strong>$' . number_format($item->cost_price * $item->quantity, 2) . '</strong></td>
                <td><span class="status-badge ' . $status_class . '">' . $status_text . '</span></td>
            </tr>';
        }

        $output .= '</tbody></table></div>';
        return $output;
    }

    private function generate_store_code() {
        global $wpdb;
        
        $last_code = $wpdb->get_var(
            "SELECT code FROM {$this->database->get_table('stores')} 
             ORDER BY id DESC LIMIT 1"
        );

        if ($last_code && preg_match('/WH-(\d+)/', $last_code, $matches)) {
            $next_number = intval($matches[1]) + 1;
        } else {
            $next_number = 1;
        }

        return 'WH-' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }

    public function handle_request() {
        if (!isset($_POST['store_nonce']) || !wp_verify_nonce($_POST['store_nonce'], 'store_form')) {
            return;
        }

        if (!current_user_can('manage_inventory')) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $store_id = isset($_POST['store_id']) ? intval($_POST['store_id']) : 0;

        if ($action === 'save') {
            $this->save_store($store_id);
        } 
        elseif ($action === 'delete' && $store_id > 0) {
            $this->delete_store($store_id);
        }
    }

    private function save_store($store_id) {
        global $wpdb;
        
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'code' => sanitize_text_field($_POST['code']),
            'manager' => sanitize_text_field($_POST['manager']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_textarea_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'postal_code' => sanitize_text_field($_POST['postal_code']),
            'country' => sanitize_text_field($_POST['country']),
            'updated_by' => get_current_user_id()
        ];

        if ($store_id > 0) {
            // Update existing store
            $old_data = $wpdb->get_row("SELECT * FROM {$this->database->get_table('stores')} WHERE id = $store_id");
            $wpdb->update($this->database->get_table('stores'), $data, ['id' => $store_id]);
            $this->audit_log->log('update', 'stores', $store_id, $old_data, $data);
            $message = 'Store updated successfully!';
        } else {
            // Create new store
            $data['created_by'] = get_current_user_id();
            $wpdb->insert($this->database->get_table('stores'), $data);
            $store_id = $wpdb->insert_id;
            $this->audit_log->log('create', 'stores', $store_id, null, $data);
            $message = 'Store created successfully!';
        }

        add_settings_error('store_messages', 'store_message', $message, 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=stores&action=edit&id=' . $store_id));
        exit;
    }

    private function delete_store($store_id) {
        global $wpdb;
        
        // Check if store has inventory
        $store_name = $wpdb->get_var("SELECT name FROM {$this->database->get_table('stores')} WHERE id = $store_id");
        $inventory_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->database->get_table('items')} WHERE store_location = '" . esc_sql($store_name) . "'"
        );

        if ($inventory_count > 0) {
            add_settings_error('store_messages', 'store_message', 
                'Cannot delete store. There are ' . $inventory_count . ' inventory items in this store.', 'error');
            wp_redirect(admin_url('admin.php?page=inventory-manager&tab=stores&action=edit&id=' . $store_id));
            exit;
        }

        $old_data = $wpdb->get_row("SELECT * FROM {$this->database->get_table('stores')} WHERE id = $store_id");
        $wpdb->delete($this->database->get_table('stores'), ['id' => $store_id]);
        $this->audit_log->log('delete', 'stores', $store_id, $old_data);
        
        add_settings_error('store_messages', 'store_message', 'Store deleted successfully!', 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=stores'));
        exit;
    }
}