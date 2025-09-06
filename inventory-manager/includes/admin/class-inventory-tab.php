<?php
namespace InventoryManager\Admin;

class Inventory_Tab {
    private $database;

    public function __construct() {
        $this->database = new \InventoryManager\Core\Database();
        add_action('admin_init', [$this, 'handle_request']);
    }

    public function get_title() {
        return 'Inventory';
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
        $item = $id ? $this->database->get_item($id) : null;
        ?>
        <div class="inventory-form-card">
            <div class="card-header">
                <h2><?php echo $id ? '✏️ Edit Product' : '➕ Add New Product'; ?></h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=inventory'); ?>" class="button button-secondary">
                    ← Back to List
                </a>
            </div>

            <form method="post" class="inventory-form">
                <?php wp_nonce_field('inventory_form', 'inventory_nonce'); ?>
                <input type="hidden" name="item_id" value="<?php echo $item ? $item->id : ''; ?>">
                <input type="hidden" name="tab" value="inventory">
                
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
                                <textarea id="description" name="description" required rows="4"
                                          placeholder="Product description..."><?php echo $item ? esc_textarea($item->description) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Quantity *</label>
                                <input type="number" id="quantity" name="quantity" required min="0"
                                       value="<?php echo $item ? esc_attr($item->quantity) : '0'; ?>">
                            </div>

                            <div class="form-group">
                                <label for="store_location">Store Location *</label>
                                <input type="text" id="store_location" name="store_location" required
                                       value="<?php echo $item ? esc_attr($item->store_location) : ''; ?>"
                                       placeholder="Warehouse A, Shelf B2">
                            </div>
                        </div>
                    </div>

                    <div class="form-column">
                        <div class="form-section">
                            <h3>💰 Pricing Information</h3>
                            
                            <div class="form-group">
                                <label for="cost_price">Cost Price ($) *</label>
                                <input type="number" id="cost_price" name="cost_price" required min="0" step="0.01"
                                       value="<?php echo $item ? esc_attr($item->cost_price) : '0.00'; ?>">
                            </div>

                            <div class="form-group">
                                <label for="old_sale_price">Old Sale Price ($) *</label>
                                <input type="number" id="old_sale_price" name="old_sale_price" required min="0" step="0.01"
                                       value="<?php echo $item ? esc_attr($item->old_sale_price) : '0.00'; ?>">
                            </div>

                            <div class="form-group">
                                <label for="new_sale_price">New Sale Price ($) *</label>
                                <input type="number" id="new_sale_price" name="new_sale_price" required min="0" step="0.01"
                                       value="<?php echo $item ? esc_attr($item->new_sale_price) : '0.00'; ?>">
                            </div>

                            <div class="price-calculator">
                                <div class="price-item">
                                    <span>Profit Margin:</span>
                                    <strong id="profit_margin">0%</strong>
                                </div>
                                <div class="price-item">
                                    <span>Price Difference:</span>
                                    <strong id="price_difference">$0.00</strong>
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
                    
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=inventory&action=new'); ?>" 
                       class="button button-secondary">
                        ➕ New Product
                    </a>
                </div>
            </form>
        </div>
        <?php
    }

    private function render_grid() {
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $items = $this->database->get_items($search_term);
        $total_count = $this->database->count_items($search_term);
        ?>
        <div class="inventory-grid-card">
            <div class="card-header">
                <h2>📋 Product Inventory</h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=inventory&action=new'); ?>" 
                   class="button button-primary">
                    ➕ Add New
                </a>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="get" class="inventory-search-form">
                    <input type="hidden" name="page" value="inventory-manager">
                    <input type="hidden" name="tab" value="inventory">
                    <div class="search-box">
                        <input type="search" name="s" placeholder="🔍 Search products by code or description..." 
                               value="<?php echo esc_attr($search_term); ?>"
                               class="search-input">
                        <button type="submit" class="button button-primary search-button">Search</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=inventory'); ?>" 
                               class="button button-secondary clear-search">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if (!empty($search_term)): ?>
            <div class="search-results-info">
                <span class="results-count">Found <?php echo $total_count; ?> product(s) for: "</span>
                <strong>"<?php echo esc_html($search_term); ?>"</strong>"
            </div>
            <?php endif; ?>

            <div class="inventory-table-container">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Location</th>
                            <th>Cost</th>
                            <th>Sale Price</th>
                            <th>Profit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="8" class="no-items">
                                <?php echo empty($search_term) ? 'No products found. Add your first product!' : 'No products found for your search.'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                            <?php
                            $profit = $item->new_sale_price - $item->cost_price;
                            $margin = $item->cost_price > 0 ? ($profit / $item->cost_price) * 100 : 0;
                            ?>
                            <tr>
                                <td class="product-code"><?php echo esc_html($item->product_code); ?></td>
                                <td class="description"><?php echo esc_html(wp_trim_words($item->description, 5)); ?></td>
                                <td class="quantity <?php echo $item->quantity == 0 ? 'out-of-stock' : ($item->quantity < 10 ? 'low-stock' : ''); ?>">
                                    <?php echo number_format($item->quantity); ?>
                                </td>
                                <td class="location"><?php echo esc_html($item->store_location); ?></td>
                                <td class="cost-price">$<?php echo number_format($item->cost_price, 2); ?></td>
                                <td class="sale-price">$<?php echo number_format($item->new_sale_price, 2); ?></td>
                                <td class="profit <?php echo $profit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                                    $<?php echo number_format($profit, 2); ?> 
                                    (<?php echo number_format($margin, 1); ?>%)
                                </td>
                                <td class="actions">
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=inventory&action=edit&id=' . $item->id); ?>" 
                                       class="button button-small" title="Edit">
                                        ✏️
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inventory-manager&tab=inventory&action=delete&id=' . $item->id), 'delete_item_' . $item->id); ?>" 
                                       class="button button-small button-danger" 
                                       onclick="return confirm('Are you sure you want to delete this product?')"
                                       title="Delete">
                                        🗑️
                                    </a>
                                    <button class="button button-small button-secondary generate-pdf" 
                                            data-product-id="<?php echo $item->id; ?>"
                                            title="Generate PDF">
                                        📄
                                    </button>
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

    public function handle_request() {
        // Handle GET delete requests
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['_wpnonce'])) {
            $this->handle_delete_request();
            return;
        }

        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inventory_nonce'])) {
            $this->handle_post_request();
            return;
        }
    }

    private function handle_post_request() {
        if (!wp_verify_nonce($_POST['inventory_nonce'], 'inventory_form')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions');
        }

        $action = sanitize_text_field($_POST['action']);
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

        if ($action === 'save') {
            $this->save_item($item_id);
        } 
        elseif ($action === 'delete' && $item_id > 0) {
            $this->delete_item($item_id);
        }
    }

    private function handle_delete_request() {
        $item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_item_' . $item_id)) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to delete items');
        }

        $this->delete_item($item_id);
    }

    private function save_item($item_id) {
        $data = array(
            'product_code' => sanitize_text_field($_POST['product_code']),
            'description' => sanitize_textarea_field($_POST['description']),
            'quantity' => intval($_POST['quantity']),
            'store_location' => sanitize_text_field($_POST['store_location']),
            'cost_price' => floatval($_POST['cost_price']),
            'old_sale_price' => floatval($_POST['old_sale_price']),
            'new_sale_price' => floatval($_POST['new_sale_price'])
        );

        if ($item_id > 0) {
            $result = $this->database->save_item(array_merge(['id' => $item_id], $data));
            if ($result !== false) {
                add_settings_error('inventory_messages', 'inventory_message', 'Product updated successfully!', 'success');
            } else {
                add_settings_error('inventory_messages', 'inventory_message', 'Error updating product.', 'error');
            }
        } else {
            $result = $this->database->save_item($data);
            if ($result !== false) {
                add_settings_error('inventory_messages', 'inventory_message', 'Product added successfully!', 'success');
            } else {
                add_settings_error('inventory_messages', 'inventory_message', 'Error adding product.', 'error');
            }
        }
    }

    private function delete_item($item_id) {
        $result = $this->database->delete_item($item_id);
        
        if ($result !== false) {
            add_settings_error('inventory_messages', 'inventory_message', 'Product deleted successfully!', 'success');
        } else {
            add_settings_error('inventory_messages', 'inventory_message', 'Error deleting product.', 'error');
        }
    }
}