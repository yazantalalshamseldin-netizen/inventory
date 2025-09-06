<?php
namespace InventoryManager\Admin;

class Purchase_Tab {
    private $database;
    private $audit_log;

    public function __construct() {
        $this->database = new \InventoryManager\Core\Database();
        $this->audit_log = new \InventoryManager\Core\AuditLog();
    }

    public function get_title() {
        return __('Purchases', 'inventory-manager');
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
        $purchase = $id ? $this->database->get_record('purchases', $id) : null;
        $suppliers = $this->database->get_records('suppliers', '', ['name'], 'name');
        ?>
        <div class="inventory-form-card">
            <div class="card-header">
                <h2><?php echo $id ? '✏️ Edit Purchase Order' : '➕ New Purchase Order'; ?></h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase'); ?>" class="button button-secondary">
                    ← Back to List
                </a>
            </div>

            <form method="post" class="purchase-form" id="purchaseForm">
                <?php wp_nonce_field('purchase_form', 'purchase_nonce'); ?>
                <input type="hidden" name="purchase_id" value="<?php echo $id ? $id : ''; ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-section">
                            <h3>📋 Purchase Information</h3>
                            
                            <div class="form-group">
                                <label for="purchase_no">Purchase Number *</label>
                                <input type="text" id="purchase_no" name="purchase_no" required 
                                       value="<?php echo $purchase ? esc_attr($purchase->purchase_no) : $this->generate_purchase_number(); ?>"
                                       readonly>
                            </div>

                            <div class="form-group">
                                <label for="reference_invoice">Reference Invoice</label>
                                <input type="text" id="reference_invoice" name="reference_invoice"
                                       value="<?php echo $purchase ? esc_attr($purchase->reference_invoice) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="date_requested">Date Requested *</label>
                                <input type="date" id="date_requested" name="date_requested" required
                                       value="<?php echo $purchase ? esc_attr($purchase->date_requested) : date('Y-m-d'); ?>">
                            </div>

                            <div class="form-group">
                                <label for="date_arrived">Date Arrived</label>
                                <input type="date" id="date_arrived" name="date_arrived"
                                       value="<?php echo $purchase ? esc_attr($purchase->date_arrived) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="supplier_id">Supplier *</label>
                                <select id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?php echo $supplier->id; ?>" 
                                            <?php selected($purchase && $purchase->supplier_id == $supplier->id); ?>>
                                            <?php echo esc_html($supplier->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-column">
                        <div class="form-section">
                            <h3>💰 Cost Information</h3>
                            
                            <div class="form-group">
                                <label for="total_invoice">Total Invoice Amount ($) *</label>
                                <input type="number" id="total_invoice" name="total_invoice" required min="0" step="0.0001"
                                       value="<?php echo $purchase ? esc_attr($purchase->total_invoice) : '0.0000'; ?>"
                                       class="calculation-trigger">
                            </div>

                            <div class="form-group">
                                <label for="total_weight">Total Weight (kg) *</label>
                                <input type="number" id="total_weight" name="total_weight" required min="0" step="0.001"
                                       value="<?php echo $purchase ? esc_attr($purchase->total_weight) : '0.000'; ?>"
                                       class="calculation-trigger">
                            </div>

                            <div class="form-group">
                                <label for="freight_cost">Freight Cost ($) *</label>
                                <input type="number" id="freight_cost" name="freight_cost" required min="0" step="0.0001"
                                       value="<?php echo $purchase ? esc_attr($purchase->freight_cost) : '0.0000'; ?>"
                                       class="calculation-trigger">
                            </div>

                            <div class="form-group">
                                <label for="customs_cost">Customs Cost ($) *</label>
                                <input type="number" id="customs_cost" name="customs_cost" required min="0" step="0.0001"
                                       value="<?php echo $purchase ? esc_attr($purchase->customs_cost) : '0.0000'; ?>"
                                       class="calculation-trigger">
                            </div>

                            <div class="form-group">
                                <label for="other_costs">Other Costs ($) *</label>
                                <input type="number" id="other_costs" name="other_costs" required min="0" step="0.0001"
                                       value="<?php echo $purchase ? esc_attr($purchase->other_costs) : '0.0000'; ?>"
                                       class="calculation-trigger">
                            </div>

                            <div class="form-group">
                                <label for="profit_margin">Profit Margin (%) *</label>
                                <input type="number" id="profit_margin" name="profit_margin" required min="0" max="100" step="0.0001"
                                       value="<?php echo $purchase ? esc_attr($purchase->profit_margin * 100) : '30.0000'; ?>"
                                       class="calculation-trigger">
                                <small>Enter as percentage (e.g., 30 for 30%)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="save" class="button button-primary button-large">
                        💾 <?php echo $id ? 'Update Purchase' : 'Save Purchase'; ?>
                    </button>
                    
                    <?php if ($id): ?>
                    <button type="submit" name="action" value="delete" class="button button-danger button-large"
                            onclick="return confirm('Are you sure you want to delete this purchase order?')">
                        🗑️ Delete Purchase
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase&action=new'); ?>" 
                       class="button button-secondary button-large">
                        ➕ New Purchase
                    </a>
                </div>
            </form>
        </div>
        <?php
    }

    private function render_grid() {
        global $wpdb;
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        
        $query = "SELECT p.*, s.name as supplier_name 
                  FROM {$this->database->get_table('purchases')} p
                  LEFT JOIN {$this->database->get_table('suppliers')} s ON p.supplier_id = s.id";
        
        $where = '';
        $placeholders = [];
        
        if (!empty($search_term)) {
            $where = " WHERE p.purchase_no LIKE %s OR p.reference_invoice LIKE %s OR s.name LIKE %s";
            $placeholders = [
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%'
            ];
        }
        
        $query .= $where . " ORDER BY p.created_at DESC";
        
        if (!empty($placeholders)) {
            $purchases = $wpdb->get_results($wpdb->prepare($query, $placeholders));
        } else {
            $purchases = $wpdb->get_results($query);
        }
        
        $total_count_query = "SELECT COUNT(*) FROM {$this->database->get_table('purchases')} p 
                             LEFT JOIN {$this->database->get_table('suppliers')} s ON p.supplier_id = s.id" . $where;
        
        if (!empty($placeholders)) {
            $total_count = $wpdb->get_var($wpdb->prepare($total_count_query, $placeholders));
        } else {
            $total_count = $wpdb->get_var($total_count_query);
        }
        ?>
        <div class="inventory-grid-card">
            <div class="card-header">
                <h2>📦 Purchase Orders</h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase&action=new'); ?>" 
                   class="button button-primary">
                    ➕ New Purchase
                </a>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="get" class="inventory-search-form">
                    <input type="hidden" name="page" value="inventory-manager">
                    <input type="hidden" name="tab" value="purchase">
                    <div class="search-box">
                        <input type="search" name="s" placeholder="🔍 Search by purchase number, reference, or supplier..." 
                               value="<?php echo esc_attr($search_term); ?>"
                               class="search-input">
                        <button type="submit" class="button button-primary search-button">Search</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase'); ?>" 
                               class="button button-secondary clear-search">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if (!empty($search_term)): ?>
            <div class="search-results-info">
                <span class="results-count">Found <?php echo $total_count; ?> purchase order(s) for: "</span>
                <strong>"<?php echo esc_html($search_term); ?>"</strong>"
            </div>
            <?php endif; ?>

            <div class="inventory-table-container">
                <table class="inventory-table with-lines">
                    <thead>
                        <tr>
                            <th>Purchase No</th>
                            <th>Reference</th>
                            <th>Supplier</th>
                            <th>Date Requested</th>
                            <th>Date Arrived</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                        <tr>
                            <td colspan="9" class="no-items">
                                <?php echo empty($search_term) ? 'No purchase orders found. Create your first purchase!' : 'No purchase orders found for your search.'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td class="purchase-no"><?php echo esc_html($purchase->purchase_no); ?></td>
                                <td class="reference"><?php echo esc_html($purchase->reference_invoice); ?></td>
                                <td class="supplier"><?php echo esc_html($purchase->supplier_name); ?></td>
                                <td class="date-requested"><?php echo date('M j, Y', strtotime($purchase->date_requested)); ?></td>
                                <td class="date-arrived"><?php echo $purchase->date_arrived ? date('M j, Y', strtotime($purchase->date_arrived)) : 'Pending'; ?></td>
                                <td class="total-amount">$<?php echo number_format($purchase->total_invoice, 4); ?></td>
                                <td class="status">
                                    <span class="status-badge status-<?php echo esc_attr($purchase->status); ?>">
                                        <?php echo esc_html(ucfirst($purchase->status)); ?>
                                    </span>
                                </td>
                                <td class="created-by">
                                    <?php 
                                    $user = get_userdata($purchase->created_by);
                                    echo $user ? esc_html($user->display_name) : 'Unknown';
                                    ?>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=purchase&action=edit&id=' . $purchase->id); ?>" 
                                       class="button button-small" title="Edit">
                                        ✏️
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inventory-manager&tab=purchase&action=delete&id=' . $purchase->id), 'delete_purchase'); ?>" 
                                       class="button button-small button-danger" 
                                       onclick="return confirm('Are you sure?')"
                                       title="Delete">
                                        🗑️
                                    </a>
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=product-entry&purchase_id=' . $purchase->id); ?>" 
                                       class="button button-small button-success"
                                       title="Add Products">
                                        ➕ Products
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

    private function generate_purchase_number() {
        global $wpdb;
        $year = date('Y');
        $month = date('m');
        
        $last_number = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT purchase_no FROM {$this->database->get_table('purchases')} 
                 WHERE purchase_no LIKE %s 
                 ORDER BY id DESC LIMIT 1",
                'PO-' . $year . '-' . $month . '-%'
            )
        );

        if ($last_number) {
            $parts = explode('-', $last_number);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return 'PO-' . $year . '-' . $month . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function handle_request() {
        if (!isset($_POST['purchase_nonce']) || !wp_verify_nonce($_POST['purchase_nonce'], 'purchase_form')) {
            return;
        }

        if (!current_user_can('manage_purchases')) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $purchase_id = isset($_POST['purchase_id']) ? intval($_POST['purchase_id']) : 0;

        if ($action === 'save') {
            $this->save_purchase($purchase_id);
        } 
        elseif ($action === 'delete' && $purchase_id > 0) {
            $this->delete_purchase($purchase_id);
        }
    }

    private function save_purchase($purchase_id) {
        global $wpdb;
        
        $data = [
            'purchase_no' => sanitize_text_field($_POST['purchase_no']),
            'reference_invoice' => sanitize_text_field($_POST['reference_invoice']),
            'date_requested' => sanitize_text_field($_POST['date_requested']),
            'date_arrived' => sanitize_text_field($_POST['date_arrived']),
            'supplier_id' => intval($_POST['supplier_id']),
            'total_invoice' => floatval($_POST['total_invoice']),
            'total_weight' => floatval($_POST['total_weight']),
            'freight_cost' => floatval($_POST['freight_cost']),
            'customs_cost' => floatval($_POST['customs_cost']),
            'other_costs' => floatval($_POST['other_costs']),
            'profit_margin' => floatval($_POST['profit_margin']) / 100, // Convert from percentage to decimal
            'updated_by' => get_current_user_id()
        ];

        if ($purchase_id > 0) {
            // Update existing purchase
            $old_data = $this->database->get_record('purchases', $purchase_id);
            $this->database->save_record('purchases', array_merge(['id' => $purchase_id], $data));
            $this->audit_log->log('update', 'purchases', $purchase_id, $old_data, $data);
            $message = 'Purchase order updated successfully!';
        } else {
            // Create new purchase
            $data['created_by'] = get_current_user_id();
            $data['status'] = 'draft';
            $this->database->save_record('purchases', $data);
            $purchase_id = $wpdb->insert_id;
            $this->audit_log->log('create', 'purchases', $purchase_id, null, $data);
            $message = 'Purchase order created successfully!';
        }

        add_settings_error('purchase_messages', 'purchase_message', $message, 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=purchase&action=edit&id=' . $purchase_id));
        exit;
    }

    private function delete_purchase($purchase_id) {
        global $wpdb;
        
        $old_data = $this->database->get_record('purchases', $purchase_id);
        $this->database->delete_record('purchases', $purchase_id);
        $this->audit_log->log('delete', 'purchases', $purchase_id, $old_data);
        
        add_settings_error('purchase_messages', 'purchase_message', 'Purchase order deleted successfully!', 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=purchase'));
        exit;
    }
}