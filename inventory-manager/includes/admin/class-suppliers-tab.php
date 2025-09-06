<?php
namespace InventoryManager\Admin;

class Suppliers_Tab {
    private $database;
    private $audit_log;

    public function __construct() {
        $this->database = new \InventoryManager\Core\Database();
        $this->audit_log = new \InventoryManager\Core\AuditLog();
    }

    public function get_title() {
        return __('Suppliers', 'inventory-manager');
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_key($_GET['action']) : 'list';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Handle GET delete requests
        if ($action === 'delete' && $id > 0) {
            $this->handle_delete_request($id);
            return;
        }

        if ($action === 'edit' || $action === 'new') {
            $this->render_form($id);
        } else {
            $this->render_grid();
        }
    }

    private function render_form($id = 0) {
        $supplier = $id ? $this->database->get_record('suppliers', $id) : null;
        ?>
        <div class="inventory-form-card">
            <div class="card-header">
                <h2><?php echo $id ? '✏️ Edit Supplier' : '➕ Add New Supplier'; ?></h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=suppliers'); ?>" class="button button-secondary">
                    ← Back to Suppliers
                </a>
            </div>

            <form method="post" class="supplier-form">
                <?php wp_nonce_field('supplier_form', 'supplier_nonce'); ?>
                <input type="hidden" name="supplier_id" value="<?php echo $id ? $id : ''; ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-section">
                            <h3>📋 Basic Information</h3>
                            
                            <div class="form-group">
                                <label for="name">Supplier Name *</label>
                                <input type="text" id="name" name="name" required 
                                       value="<?php echo $supplier ? esc_attr($supplier->name) : ''; ?>"
                                       placeholder="ABC Trading Company">
                            </div>

                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person"
                                       value="<?php echo $supplier ? esc_attr($supplier->contact_person) : ''; ?>"
                                       placeholder="John Smith">
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email"
                                       value="<?php echo $supplier ? esc_attr($supplier->email) : ''; ?>"
                                       placeholder="contact@abctrading.com">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo $supplier ? esc_attr($supplier->phone) : ''; ?>"
                                       placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                    </div>

                    <div class="form-column">
                        <div class="form-section">
                            <h3>🏢 Company Details</h3>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" rows="4"
                                          placeholder="123 Business Street, City, State, ZIP"><?php echo $supplier ? esc_textarea($supplier->address) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tax_id">Tax ID / VAT Number</label>
                                <input type="text" id="tax_id" name="tax_id"
                                       value="<?php echo $supplier ? esc_attr($supplier->tax_id) : ''; ?>"
                                       placeholder="TAX-123456789">
                            </div>

                            <div class="form-group">
                                <label for="payment_terms">Payment Terms</label>
                                <textarea id="payment_terms" name="payment_terms" rows="3"
                                          placeholder="Net 30 days, Bank transfer preferred"><?php echo $supplier ? esc_textarea($supplier->payment_terms) : ''; ?></textarea>
                            </div>

                            <div class="supplier-stats">
                                <h4>📊 Supplier Statistics</h4>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-label">Total Purchases:</span>
                                        <span class="stat-value"><?php echo $this->get_supplier_statistic($id, 'total_purchases'); ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Total Spent:</span>
                                        <span class="stat-value">$<?php echo number_format($this->get_supplier_statistic($id, 'total_spent'), 2); ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Last Purchase:</span>
                                        <span class="stat-value"><?php echo $this->get_supplier_statistic($id, 'last_purchase'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="save" class="button button-primary">
                        💾 <?php echo $id ? 'Update Supplier' : 'Save Supplier'; ?>
                    </button>
                    
                    <?php if ($id): ?>
                    <button type="submit" name="action" value="delete" class="button button-danger"
                            onclick="return confirm('Are you sure you want to delete this supplier? This will affect associated purchase orders.')">
                        🗑️ Delete Supplier
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=suppliers&action=new'); ?>" 
                       class="button button-secondary">
                        ➕ New Supplier
                    </a>
                </div>
            </form>

            <?php if ($id): ?>
            <div class="supplier-related-data">
                <div class="card-header">
                    <h3>📦 Recent Purchase Orders</h3>
                </div>
                <div class="related-purchases">
                    <?php echo $this->render_supplier_purchases($id); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_grid() {
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $suppliers = $this->database->get_records('suppliers', $search_term, ['name', 'contact_person', 'email', 'tax_id'], 'name');
        $total_count = $this->database->count_records('suppliers', $search_term, ['name', 'contact_person', 'email', 'tax_id']);
        ?>
        <div class="inventory-grid-card">
            <div class="card-header">
                <h2>🏢 Suppliers & Vendors</h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=suppliers&action=new'); ?>" 
                   class="button button-primary">
                    ➕ New Supplier
                </a>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="get" class="inventory-search-form">
                    <input type="hidden" name="page" value="inventory-manager">
                    <input type="hidden" name="tab" value="suppliers">
                    <div class="search-box">
                        <input type="search" name="s" placeholder="🔍 Search suppliers by name, contact, or tax ID..." 
                               value="<?php echo esc_attr($search_term); ?>"
                               class="search-input">
                        <button type="submit" class="button button-primary search-button">Search</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=suppliers'); ?>" 
                               class="button button-secondary clear-search">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if (!empty($search_term)): ?>
            <div class="search-results-info">
                <span class="results-count">Found <?php echo $total_count; ?> supplier(s) for: "</span>
                <strong>"<?php echo esc_html($search_term); ?>"</strong>"
            </div>
            <?php endif; ?>

            <div class="inventory-table-container">
                <table class="inventory-table with-lines">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Tax ID</th>
                            <th>Total Purchases</th>
                            <th>Total Spent</th>
                            <th>Last Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="9" class="no-items">
                                <?php echo empty($search_term) ? 'No suppliers found. Add your first supplier!' : 'No suppliers found for your search.'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($suppliers as $supplier): ?>
                            <?php
                            $stats = [
                                'total_purchases' => $this->get_supplier_statistic($supplier->id, 'total_purchases'),
                                'total_spent' => $this->get_supplier_statistic($supplier->id, 'total_spent'),
                                'last_purchase' => $this->get_supplier_statistic($supplier->id, 'last_purchase')
                            ];
                            ?>
                            <tr>
                                <td class="supplier-name">
                                    <strong><?php echo esc_html($supplier->name); ?></strong>
                                    <?php if ($supplier->address): ?>
                                        <br><small class="text-muted"><?php echo esc_html(wp_trim_words($supplier->address, 5)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="contact-person"><?php echo esc_html($supplier->contact_person); ?></td>
                                <td class="email">
                                    <?php if ($supplier->email): ?>
                                        <a href="mailto:<?php echo esc_attr($supplier->email); ?>">
                                            <?php echo esc_html($supplier->email); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="phone">
                                    <?php if ($supplier->phone): ?>
                                        <a href="tel:<?php echo esc_attr($supplier->phone); ?>">
                                            <?php echo esc_html($supplier->phone); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="tax-id"><?php echo esc_html($supplier->tax_id); ?></td>
                                <td class="total-purchases">
                                    <span class="stat-badge"><?php echo number_format($stats['total_purchases']); ?></span>
                                </td>
                                <td class="total-spent">
                                    <strong>$<?php echo number_format($stats['total_spent'], 2); ?></strong>
                                </td>
                                <td class="last-purchase">
                                    <?php echo $stats['last_purchase'] ? date('M j, Y', strtotime($stats['last_purchase'])) : '—'; ?>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=suppliers&action=edit&id=' . $supplier->id); ?>" 
                                       class="button button-small" title="Edit">
                                        ✏️
                                    </a>
                                    <form method="post" style="display: inline-block; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                        <?php wp_nonce_field('delete_supplier_' . $supplier->id); ?>
                                        <input type="hidden" name="supplier_id" value="<?php echo $supplier->id; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="tab" value="suppliers">
                                        <button type="submit" class="button button-small button-danger" title="Delete">
                                            🗑️
                                        </button>
                                    </form>
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase&action=new&supplier_id=' . $supplier->id); ?>" 
                                       class="button button-small button-success"
                                       title="Create Purchase Order">
                                        📦 PO
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

    private function get_supplier_statistic($supplier_id, $statistic) {
        global $wpdb;
        
        if (!$supplier_id) return 0;

        switch ($statistic) {
            case 'total_purchases':
                return $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$this->database->get_table('purchases')} WHERE supplier_id = %d",
                        $supplier_id
                    )
                );

            case 'total_spent':
                return $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT COALESCE(SUM(total_invoice), 0) FROM {$this->database->get_table('purchases')} WHERE supplier_id = %d AND status != 'cancelled'",
                        $supplier_id
                    )
                );

            case 'last_purchase':
                return $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT MAX(created_at) FROM {$this->database->get_table('purchases')} WHERE supplier_id = %d",
                        $supplier_id
                    )
                );

            default:
                return 0;
        }
    }

    private function render_supplier_purchases($supplier_id) {
        global $wpdb;
        
        $purchases = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->database->get_table('purchases')} WHERE supplier_id = %d ORDER BY created_at DESC LIMIT 10",
                $supplier_id
            )
        );

        if (empty($purchases)) {
            return '<p class="no-data">No purchase orders found for this supplier.</p>';
        }

        $output = '<div class="purchases-list">';
        $output .= '<table class="inventory-table with-lines">';
        $output .= '<thead><tr>
            <th>Purchase No</th>
            <th>Reference</th>
            <th>Date Requested</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr></thead>';
        $output .= '<tbody>';

        foreach ($purchases as $purchase) {
            $output .= '<tr>
                <td>' . esc_html($purchase->purchase_no) . '</td>
                <td>' . esc_html($purchase->reference_invoice) . '</td>
                <td>' . date('M j, Y', strtotime($purchase->date_requested)) . '</td>
                <td>$' . number_format($purchase->total_invoice, 2) . '</td>
                <td><span class="status-badge status-' . esc_attr($purchase->status) . '">' . ucfirst($purchase->status) . '</span></td>
                <td>
                    <a href="' . admin_url('admin.php?page=inventory-manager&tab=purchase&action=edit&id=' . $purchase->id) . '" 
                       class="button button-small" title="View">👁️</a>
                </td>
            </tr>';
        }

        $output .= '</tbody></table></div>';
        return $output;
    }

    public function handle_request() {
        // Handle POST requests (form submissions)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_post_request();
        }
        // Handle GET delete requests
        elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $this->handle_delete_request(intval($_GET['id']));
        }
    }

    private function handle_post_request() {
        if (!isset($_POST['supplier_nonce']) || !wp_verify_nonce($_POST['supplier_nonce'], 'supplier_form')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $supplier_id = isset($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 0;

        if ($action === 'save') {
            $this->save_supplier($supplier_id);
        } 
        elseif ($action === 'delete' && $supplier_id > 0) {
            $this->delete_supplier($supplier_id);
        }
    }

    private function handle_delete_request($supplier_id) {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', 'delete_supplier_' . $supplier_id)) {
            wp_die('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to delete suppliers');
        }

        $this->delete_supplier($supplier_id);
    }

    private function save_supplier($supplier_id) {
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'contact_person' => sanitize_text_field($_POST['contact_person']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_textarea_field($_POST['address']),
            'tax_id' => sanitize_text_field($_POST['tax_id']),
            'payment_terms' => sanitize_textarea_field($_POST['payment_terms'])
        );

        if ($supplier_id > 0) {
            $this->database->save_record('suppliers', array_merge(['id' => $supplier_id], $data));
            $message = 'Supplier updated successfully!';
            $this->audit_log->log('supplier_updated', "Supplier #{$supplier_id} updated");
        } else {
            $supplier_id = $this->database->save_record('suppliers', $data);
            $message = 'Supplier added successfully!';
            $this->audit_log->log('supplier_added', "Supplier #{$supplier_id} added");
        }
        
        add_settings_error('inventory_messages', 'inventory_message', $message, 'updated');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=suppliers'));
        exit;
    }

    private function delete_supplier($supplier_id) {
        $this->database->delete_record('suppliers', $supplier_id);
        $this->audit_log->log('supplier_deleted', "Supplier #{$supplier_id} deleted");
        add_settings_error('inventory_messages', 'inventory_message', 'Supplier deleted successfully!', 'updated');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=suppliers'));
        exit;
    }
}