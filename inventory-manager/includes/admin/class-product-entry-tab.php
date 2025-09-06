<?php
namespace InventoryManager\Admin;

class Product_Entry_Tab {
    private $database;
    private $audit_log;

    public function __construct() {
        $this->database = new \InventoryManager\Core\Database();
        $this->audit_log = new \InventoryManager\Core\AuditLog();
    }

    public function get_title() {
        return __('Product Entry', 'inventory-manager');
    }

    public function render() {
        $purchase_id = isset($_GET['purchase_id']) ? intval($_GET['purchase_id']) : 0;
        
        if ($purchase_id > 0) {
            $this->render_product_form($purchase_id);
        } else {
            $this->render_purchase_selection();
        }
    }

    private function render_purchase_selection() {
        global $wpdb;
        $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        
        $query = "SELECT * FROM {$this->database->get_table('purchases')} 
                 WHERE (status = 'draft' OR status = 'received')";
        
        if (!empty($search_term)) {
            $query .= $wpdb->prepare(" AND (purchase_no LIKE %s OR reference_invoice LIKE %s)", 
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%'
            );
        }
        
        $query .= " ORDER BY created_at DESC";
        $purchases = $wpdb->get_results($query);
        ?>
        <div class="inventory-card">
            <div class="card-header">
                <h2>📦 Select Purchase Order</h2>
                <p>Search and select a purchase order to add products</p>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="get" class="inventory-search-form">
                    <input type="hidden" name="page" value="inventory-manager">
                    <input type="hidden" name="tab" value="product-entry">
                    <div class="search-box">
                        <input type="search" name="search" placeholder="🔍 Search by purchase number or reference..." 
                               value="<?php echo esc_attr($search_term); ?>"
                               class="search-input">
                        <button type="submit" class="button button-primary search-button">Search</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry'); ?>" 
                               class="button button-secondary clear-search">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="purchase-selection-grid">
                <?php if (empty($purchases)): ?>
                    <div class="no-purchases">
                        <p><?php echo empty($search_term) ? 'No purchase orders found. Please create a purchase order first.' : 'No purchase orders found for your search.'; ?></p>
                        <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase&action=new'); ?>" 
                           class="button button-primary">
                            ➕ Create Purchase Order
                        </a>
                    </div>
                <?php else: ?>
                    <div class="purchase-cards">
                        <?php foreach ($purchases as $purchase): ?>
                            <div class="purchase-card">
                                <h3><?php echo esc_html($purchase->purchase_no); ?></h3>
                                <div class="purchase-details">
                                    <p><strong>Reference:</strong> <?php echo esc_html($purchase->reference_invoice); ?></p>
                                    <p><strong>Total Amount:</strong> $<?php echo number_format($purchase->total_invoice, 4); ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="status-badge status-<?php echo esc_attr($purchase->status); ?>">
                                            <?php echo esc_html(ucfirst($purchase->status)); ?>
                                        </span>
                                    </p>
                                    <p><strong>Created:</strong> <?php echo date('M j, Y', strtotime($purchase->created_at)); ?></p>
                                    
                                    <?php 
                                    // Count existing products
                                    $product_count = $wpdb->get_var(
                                        $wpdb->prepare(
                                            "SELECT COUNT(*) FROM {$this->database->get_table('purchase_items')} WHERE purchase_id = %d",
                                            $purchase->id
                                        )
                                    );
                                    ?>
                                    <p><strong>Products:</strong> <?php echo $product_count; ?> items</p>
                                </div>
                                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase->id); ?>" 
                                   class="button button-primary">
                                    ➕ Add Products
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    private function render_product_form($purchase_id) {
        global $wpdb;
        
        $purchase = $this->database->get_record('purchases', $purchase_id);
        if (!$purchase) {
            echo '<div class="error"><p>Purchase order not found!</p></div>';
            return;
        }

        // Get existing store locations from items table
        $store_locations = $this->database->get_store_locations();
        
        // Also get stores from stores table
        $stores = $this->database->get_records('stores', '', ['name'], 'name');
        
        // Combine both sources
        $all_locations = [];
        foreach ($stores as $store) {
            $all_locations[$store->name] = $store->name;
        }
        foreach ($store_locations as $location) {
            $all_locations[$location->store_location] = $location->store_location;
        }
        
        $existing_items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->database->get_table('purchase_items')} 
                 WHERE purchase_id = %d 
                 ORDER BY created_at DESC",
                $purchase_id
            )
        );

        $action = isset($_GET['action']) ? sanitize_key($_GET['action']) : 'list';
        $item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
        $item = $item_id ? $this->database->get_record('purchase_items', $item_id) : null;
        ?>
        
        <div class="inventory-form-card">
            <div class="card-header">
                <h2>📦 Add Products to: <?php echo esc_html($purchase->purchase_no); ?></h2>
                <div class="header-actions">
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry'); ?>" 
                       class="button button-secondary">
                        ← Back to Purchases
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase&action=edit&id=' . $purchase_id); ?>" 
                       class="button button-secondary">
                        ✏️ Edit Purchase
                    </a>
                </div>
            </div>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <!-- Product Form -->
                <form method="post" class="product-entry-form" id="productEntryForm">
                    <?php wp_nonce_field('product_entry_form', 'product_entry_nonce'); ?>
                    <input type="hidden" name="purchase_id" value="<?php echo $purchase_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $item ? $item->id : ''; ?>">
                    
                    <div class="form-grid">
                        <div class="form-column">
                            <div class="form-section">
                                <h3>📋 Product Information</h3>
                                
                                <div class="form-group">
                                    <label for="product_code">Product Code *</label>
                                    <input type="text" id="product_code" name="product_code" required 
                                           value="<?php echo $item ? esc_attr($item->product_code) : ''; ?>"
                                           placeholder="PROD-001">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea id="description" name="description" required rows="3"
                                              placeholder="Product description..."><?php echo $item ? esc_textarea($item->description) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="factory_price">Factory Price ($) *</label>
                                    <input type="number" id="factory_price" name="factory_price" required min="0" step="0.0001"
                                           value="<?php echo $item ? esc_attr($item->factory_price) : '0.0000'; ?>"
                                           class="calculation-trigger">
                                </div>

                                <div class="form-group">
                                    <label for="weight">Weight per Unit (kg) *</label>
                                    <input type="number" id="weight" name="weight" required min="0" step="0.001"
                                           value="<?php echo $item ? esc_attr($item->weight) : '0.000'; ?>"
                                           class="calculation-trigger">
                                </div>

                                <div class="form-group">
                                    <label for="quantity">Quantity *</label>
                                    <input type="number" id="quantity" name="quantity" required min="1" step="1"
                                           value="<?php echo $item ? esc_attr($item->quantity) : '1'; ?>"
                                           class="calculation-trigger">
                                </div>
                            </div>
                        </div>

                        <div class="form-column">
                            <div class="form-section">
                                <h3>📍 Storage Information</h3>
                                
                                <div class="form-group">
                                    <label for="store_location">Store Location *</label>
                                    <select id="store_location" name="store_location" required>
                                        <option value="">Select Store Location</option>
                                        <?php foreach ($all_locations as $location): ?>
                                            <option value="<?php echo esc_attr($location); ?>" 
                                                <?php selected($item && $item->store_location == $location); ?>>
                                                <?php echo esc_html($location); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="new_store_location">Or Add New Location:</label>
                                    <input type="text" id="new_store_location" name="new_store_location"
                                           placeholder="Enter new store location">
                                    <small>Leave blank if selecting from existing locations</small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>💰 Pricing Calculations</h3>
                                
                                <div class="calculation-results">
                                    <div class="result-item">
                                        <label>Total Factory Cost:</label>
                                        <span id="total_factory_cost">$0.0000</span>
                                    </div>
                                    
                                    <div class="result-item">
                                        <label>Freight Allocation:</label>
                                        <span id="freight_allocation">$0.0000</span>
                                    </div>
                                    
                                    <div class="result-item">
                                        <label>Customs Allocation:</label>
                                        <span id="customs_allocation">$0.0000</span>
                                    </div>
                                    
                                    <div class="result-item">
                                        <label>Other Costs Allocation:</label>
                                        <span id="other_costs_allocation">$0.0000</span>
                                    </div>
                                    
                                    <div class="result-divider"></div>
                                    
                                    <div class="result-item total">
                                        <label>Product Cost Price:</label>
                                        <span id="calculated_cost">$0.0000</span>
                                    </div>
                                    
                                    <div class="result-item">
                                        <label>Product Sale Price:</label>
                                        <span id="calculated_sale_price">$0.0000</span>
                                    </div>
                                    
                                    <div class="result-item">
                                        <label>Existing Stock:</label>
                                        <span id="existing_stock_info">0 units</span>
                                    </div>
                                    
                                    <div class="result-item final">
                                        <label>Final Sell Price:</label>
                                        <span id="final_sell_price">$0.0000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="action" value="save" class="button button-primary">
                            💾 <?php echo $item ? 'Update Product' : 'Save Product'; ?>
                        </button>
                        
                        <?php if ($item): ?>
                        <button type="submit" name="action" value="delete" class="button button-danger"
                                onclick="return confirm('Are you sure you want to delete this product?')">
                            🗑️ Delete Product
                        </button>
                        <?php endif; ?>
                        
                        <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id); ?>" 
                           class="button button-secondary">
                            ↩️ Cancel
                        </a>
                    </div>
                </form>

            <?php else: ?>
                <!-- Products Grid -->
                <div class="products-grid-section">
                    <div class="grid-actions">
                        <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id . '&action=new'); ?>" 
                           class="button button-primary">
                            ➕ Add New Product
                        </a>
                        
                        <?php if (!empty($existing_items)): ?>
                        <button type="button" id="calculateAllPrices" class="button button-secondary">
                            🧮 Calculate All Prices
                        </button>
                        <button type="button" id="saveToInventory" class="button button-success">
                            💾 Save to Inventory
                        </button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($existing_items)): ?>
                        <div class="inventory-table-container">
                            <table class="inventory-table with-lines">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Description</th>
                                        <th>Factory Price</th>
                                        <th>Weight</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Sale Price</th>
                                        <th>Store Location</th>
                                        <th>Final Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($existing_items as $item): ?>
                                    <tr>
                                        <td class="product-code"><?php echo esc_html($item->product_code); ?></td>
                                        <td class="description"><?php echo esc_html(wp_trim_words($item->description, 5)); ?></td>
                                        <td class="factory-price">$<?php echo number_format($item->factory_price, 4); ?></td>
                                        <td class="weight"><?php echo number_format($item->weight, 3); ?> kg</td>
                                        <td class="quantity"><?php echo number_format($item->quantity); ?></td>
                                        <td class="cost-price calculated">$<?php echo number_format($item->calculated_cost, 4); ?></td>
                                        <td class="sale-price calculated">$<?php echo number_format($item->calculated_sale_price, 4); ?></td>
                                        <td class="store-location"><?php echo esc_html($item->store_location); ?></td>
                                        <td class="final-price">$<?php echo number_format($this->calculate_final_price($item->product_code, $item->calculated_sale_price, $item->quantity), 4); ?></td>
                                        <td class="actions">
                                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id . '&action=edit&id=' . $item->id); ?>" 
                                               class="button button-small" title="Edit">
                                                ✏️
                                            </a>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id . '&action=delete&id=' . $item->id), 'delete_product_item_' . $item->id); ?>" 
                                               class="button button-small button-danger" 
                                               onclick="return confirm('Are you sure you want to delete this product?')"
                                               title="Delete">
                                                🗑️
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="no-items">
                            <p>No products added to this purchase order yet.</p>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id . '&action=new'); ?>" 
                               class="button button-primary">
                                ➕ Add Your First Product
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function handle_request() {
        if (!isset($_POST['product_entry_nonce']) || !wp_verify_nonce($_POST['product_entry_nonce'], 'product_entry_form')) {
            return;
        }

        if (!current_user_can('manage_inventory')) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $purchase_id = intval($_POST['purchase_id']);
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

        if ($action === 'save') {
            $this->save_product_item($purchase_id, $item_id);
        } 
        elseif ($action === 'delete' && $item_id > 0) {
            $this->delete_product_item($purchase_id, $item_id);
        }
    }

    private function save_product_item($purchase_id, $item_id) {
        global $wpdb;
        
        // Get purchase details for calculations
        $purchase = $this->database->get_record('purchases', $purchase_id);
        
        $data = [
            'purchase_id' => $purchase_id,
            'product_code' => sanitize_text_field($_POST['product_code']),
            'description' => sanitize_textarea_field($_POST['description']),
            'factory_price' => floatval($_POST['factory_price']),
            'weight' => floatval($_POST['weight']),
            'quantity' => intval($_POST['quantity']),
            'updated_by' => get_current_user_id()
        ];

        // Handle store location - use new location if provided, otherwise use selected
        if (!empty($_POST['new_store_location'])) {
            $data['store_location'] = sanitize_text_field($_POST['new_store_location']);
        } else {
            $data['store_location'] = sanitize_text_field($_POST['store_location']);
        }

        // Calculate prices using your formulas
        $calculated_prices = $this->calculate_product_prices($data, $purchase);
        $data['calculated_cost'] = $calculated_prices['cost_price'];
        $data['calculated_sale_price'] = $calculated_prices['sale_price'];

        if ($item_id > 0) {
            // Update existing item
            $old_data = $this->database->get_record('purchase_items', $item_id);
            $this->database->save_record('purchase_items', array_merge(['id' => $item_id], $data));
            $this->audit_log->log('update', 'purchase_items', $item_id, $old_data, $data);
            $message = 'Product updated successfully!';
        } else {
            // Create new item
            $data['created_by'] = get_current_user_id();
            $this->database->save_record('purchase_items', $data);
            $item_id = $wpdb->insert_id;
            $this->audit_log->log('create', 'purchase_items', $item_id, null, $data);
            $message = 'Product added successfully!';
        }

        add_settings_error('product_entry_messages', 'product_entry_message', $message, 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id . '&action=edit&id=' . $item_id));
        exit;
    }

    private function calculate_product_prices($product_data, $purchase_data) {
        // Your exact calculation formulas:
        
        // 1. Factory cost for this product
        $factory_cost = $product_data['factory_price'] * $product_data['quantity'];
        
        // 2. Freight allocation: (Weight per unit × Quantity / Total Purchase Weight × Freight Cost)
        $freight_allocation = ($product_data['weight'] * $product_data['quantity'] / $purchase_data->total_weight) * $purchase_data->freight_cost;
        
        // 3. Customs allocation: (Factory Price × Quantity / Total Purchase Invoice × Customs Cost)
        $customs_allocation = ($factory_cost / $purchase_data->total_invoice) * $purchase_data->customs_cost;
        
        // 4. Other costs allocation: (Factory Price × Quantity / Total Purchase Invoice × Other Costs)
        $other_costs_allocation = ($factory_cost / $purchase_data->total_invoice) * $purchase_data->other_costs;
        
        // 5. Total cost price per unit
        $cost_price = ($factory_cost + $freight_allocation + $customs_allocation + $other_costs_allocation) / $product_data['quantity'];
        
        // 6. Sale price with profit margin
        $sale_price = $cost_price * (1 + $purchase_data->profit_margin);

        return [
            'cost_price' => round($cost_price, 4),
            'sale_price' => round($sale_price, 4),
            'calculations' => [
                'factory_cost' => $factory_cost,
                'freight_allocation' => $freight_allocation,
                'customs_allocation' => $customs_allocation,
                'other_costs_allocation' => $other_costs_allocation
            ]
        ];
    }

    private function calculate_final_price($product_code, $new_sale_price, $new_quantity) {
        global $wpdb;
        
        // Check if product already exists in inventory
        $existing_item = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->database->get_table('items')} WHERE product_code = %s",
                $product_code
            )
        );

        if ($existing_item) {
            // Your formula: (New Product Sale Price × New Quantity + Old Product Sale Price × Old Quantity) / Total Quantity
            $total_quantity = $existing_item->quantity + $new_quantity;
            $final_price = (
                ($new_sale_price * $new_quantity) + 
                ($existing_item->new_sale_price * $existing_item->quantity)
            ) / $total_quantity;
            
            return round($final_price, 4);
        }

        return $new_sale_price;
    }

    private function delete_product_item($purchase_id, $item_id) {
        global $wpdb;
        
        // Verify nonce
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'delete_product_item_' . $item_id)) {
            wp_die('Security check failed');
        }
        
        $old_data = $this->database->get_record('purchase_items', $item_id);
        $result = $this->database->delete_record('purchase_items', $item_id);
        
        if ($result !== false) {
            $this->audit_log->log('delete', 'purchase_items', $item_id, $old_data);
            add_settings_error('product_entry_messages', 'product_entry_message', 'Product deleted successfully!', 'success');
        } else {
            add_settings_error('product_entry_messages', 'product_entry_message', 'Error deleting product.', 'error');
        }
        
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase_id));
        exit;
    }
}