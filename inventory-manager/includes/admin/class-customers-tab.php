<?php
namespace InventoryManager\Admin;

class Customers_Tab {
    private $database;
    private $audit_log;

    public function __construct() {
        $this->database = new \InventoryManager\Core\Database();
        $this->audit_log = new \InventoryManager\Core\AuditLog();
    }

    public function get_title() {
        return __('Customers', 'inventory-manager');
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
        $customer = $id ? $this->database->get_record('customers', $id) : null;
        ?>
        <div class="inventory-form-card">
            <div class="card-header">
                <h2><?php echo $id ? '✏️ Edit Customer' : '👥 Add New Customer'; ?></h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=customers'); ?>" class="button button-secondary">
                    ← Back to Customers
                </a>
            </div>

            <form method="post" class="customer-form">
                <?php wp_nonce_field('customer_form', 'customer_nonce'); ?>
                <input type="hidden" name="customer_id" value="<?php echo $id ? $id : ''; ?>">
                
                <div class="form-grid">
                    <div class="form-column">
                        <div class="form-section">
                            <h3>👤 Personal Information</h3>
                            
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required 
                                       value="<?php echo $customer ? esc_attr($customer->first_name) : ''; ?>"
                                       placeholder="John">
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required 
                                       value="<?php echo $customer ? esc_attr($customer->last_name) : ''; ?>"
                                       placeholder="Doe">
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo $customer ? esc_attr($customer->email) : ''; ?>"
                                       placeholder="john.doe@example.com">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo $customer ? esc_attr($customer->phone) : ''; ?>"
                                       placeholder="+1 (555) 123-4567">
                            </div>

                            <div class="form-group">
                                <label for="company">Company</label>
                                <input type="text" id="company" name="company"
                                       value="<?php echo $customer ? esc_attr($customer->company) : ''; ?>"
                                       placeholder="ABC Corporation">
                            </div>
                        </div>
                    </div>

                    <div class="form-column">
                        <div class="form-section">
                            <h3>📍 Address Information</h3>
                            
                            <div class="form-group">
                                <label for="address">Street Address</label>
                                <textarea id="address" name="address" rows="3"
                                          placeholder="123 Main Street"><?php echo $customer ? esc_textarea($customer->address) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city"
                                       value="<?php echo $customer ? esc_attr($customer->city) : ''; ?>"
                                       placeholder="New York">
                            </div>

                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state"
                                       value="<?php echo $customer ? esc_attr($customer->state) : ''; ?>"
                                       placeholder="NY">
                            </div>

                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code"
                                       value="<?php echo $customer ? esc_attr($customer->postal_code) : ''; ?>"
                                       placeholder="10001">
                            </div>

                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country"
                                       value="<?php echo $customer ? esc_attr($customer->country) : ''; ?>"
                                       placeholder="United States">
                            </div>
                        </div>

                        <div class="customer-stats">
                            <h4>📊 Customer Statistics</h4>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <span class="stat-label">Total Orders:</span>
                                    <span class="stat-value"><?php echo $this->get_customer_statistic($id, 'total_orders'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Total Spent:</span>
                                    <span class="stat-value">$<?php echo number_format($this->get_customer_statistic($id, 'total_spent'), 2); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Last Order:</span>
                                    <span class="stat-value"><?php echo $this->get_customer_statistic($id, 'last_order'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="save" class="button button-primary">
                        💾 <?php echo $id ? 'Update Customer' : 'Save Customer'; ?>
                    </button>
                    
                    <?php if ($id): ?>
                    <button type="submit" name="action" value="delete" class="button button-danger"
                            onclick="return confirm('Are you sure you want to delete this customer?')">
                        🗑️ Delete Customer
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=customers&action=new'); ?>" 
                       class="button button-secondary">
                        👥 New Customer
                    </a>
                </div>
            </form>

            <?php if ($id): ?>
            <div class="customer-related-data">
                <div class="card-header">
                    <h3>📦 Order History</h3>
                </div>
                <div class="customer-orders">
                    <?php echo $this->render_customer_orders($id); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_grid() {
        $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $customers = $this->database->get_records('customers', $search_term, ['first_name', 'last_name', 'email', 'company', 'phone'], 'last_name, first_name');
        $total_count = $this->database->count_records('customers', $search_term, ['first_name', 'last_name', 'email', 'company', 'phone']);
        ?>
        <div class="inventory-grid-card">
            <div class="card-header">
                <h2>👥 Customers</h2>
                <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=customers&action=new'); ?>" 
                   class="button button-primary">
                    👥 New Customer
                </a>
            </div>

            <!-- Search Form -->
            <div class="search-container">
                <form method="get" class="inventory-search-form">
                    <input type="hidden" name="page" value="inventory-manager">
                    <input type="hidden" name="tab" value="customers">
                    <div class="search-box">
                        <input type="search" name="s" placeholder="🔍 Search customers by name, email, or company..." 
                               value="<?php echo esc_attr($search_term); ?>"
                               class="search-input">
                        <button type="submit" class="button button-primary search-button">Search</button>
                        <?php if (!empty($search_term)): ?>
                            <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=customers'); ?>" 
                               class="button button-secondary clear-search">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if (!empty($search_term)): ?>
            <div class="search-results-info">
                <span class="results-count">Found <?php echo $total_count; ?> customer(s) for: "</span>
                <strong>"<?php echo esc_html($search_term); ?>"</strong>"
            </div>
            <?php endif; ?>

            <div class="inventory-table-container">
                <table class="inventory-table with-lines">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Last Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" class="no-items">
                                <?php echo empty($search_term) ? 'No customers found. Add your first customer!' : 'No customers found for your search.'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                            <?php
                            $stats = [
                                'total_orders' => $this->get_customer_statistic($customer->id, 'total_orders'),
                                'total_spent' => $this->get_customer_statistic($customer->id, 'total_spent'),
                                'last_order' => $this->get_customer_statistic($customer->id, 'last_order')
                            ];
                            ?>
                            <tr>
                                <td class="customer-name">
                                    <strong><?php echo esc_html($customer->first_name . ' ' . $customer->last_name); ?></strong>
                                    <?php if ($customer->company): ?>
                                        <br><small class="text-muted">🏢 <?php echo esc_html($customer->company); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="contact-info">
                                    <?php if ($customer->email): ?>
                                        <a href="mailto:<?php echo esc_attr($customer->email); ?>">
                                            📧 <?php echo esc_html($customer->email); ?>
                                        </a>
                                        <br>
                                    <?php endif; ?>
                                    <?php if ($customer->phone): ?>
                                        <a href="tel:<?php echo esc_attr($customer->phone); ?>">
                                            📞 <?php echo esc_html($customer->phone); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="company"><?php echo esc_html($customer->company); ?></td>
                                <td class="location">
                                    <?php if ($customer->city && $customer->country): ?>
                                        <?php echo esc_html($customer->city); ?>, <?php echo esc_html($customer->country); ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="total-orders">
                                    <span class="stat-badge"><?php echo number_format($stats['total_orders']); ?></span>
                                </td>
                                <td class="total-spent">
                                    <strong>$<?php echo number_format($stats['total_spent'], 2); ?></strong>
                                </td>
                                <td class="last-order">
                                    <?php echo $stats['last_order'] ? date('M j, Y', strtotime($stats['last_order'])) : '—'; ?>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=customers&action=edit&id=' . $customer->id); ?>" 
                                       class="button button-small" title="Edit Customer">
                                        ✏️
                                    </a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=inventory-manager&tab=customers&action=delete&id=' . $customer->id), 'delete_customer'); ?>" 
                                       class="button button-small button-danger" 
                                       onclick="return confirm('Are you sure?')"
                                       title="Delete Customer">
                                        🗑️
                                    </a>
                                    <a href="<?php echo admin_url('admin.php?page=inventory-manager&tab=quotations&action=new&customer_id=' . $customer->id); ?>" 
                                       class="button button-small button-success"
                                       title="Create Quotation">
                                        📋 Quote
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

    private function get_customer_statistic($customer_id, $statistic) {
        // Placeholder for customer statistics
        // Will be implemented with invoices/quotations functionality
        switch ($statistic) {
            case 'total_orders':
                return 0;
            case 'total_spent':
                return 0;
            case 'last_order':
                return null;
            default:
                return 0;
        }
    }

    private function render_customer_orders($customer_id) {
        // Placeholder for customer orders
        // Will be implemented with invoices/quotations functionality
        return '<p class="no-data">No order history available yet. This will show customer quotations and invoices.</p>';
    }

    public function handle_request() {
        if (!isset($_POST['customer_nonce']) || !wp_verify_nonce($_POST['customer_nonce'], 'customer_form')) {
            return;
        }

        if (!current_user_can('manage_customers')) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;

        if ($action === 'save') {
            $this->save_customer($customer_id);
        } 
        elseif ($action === 'delete' && $customer_id > 0) {
            $this->delete_customer($customer_id);
        }
    }

    private function save_customer($customer_id) {
        global $wpdb;
        
        $data = [
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'company' => sanitize_text_field($_POST['company']),
            'address' => sanitize_textarea_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'postal_code' => sanitize_text_field($_POST['postal_code']),
            'country' => sanitize_text_field($_POST['country']),
            'updated_by' => get_current_user_id()
        ];

        if ($customer_id > 0) {
            // Update existing customer
            $old_data = $this->database->get_record('customers', $customer_id);
            $this->database->save_record('customers', array_merge(['id' => $customer_id], $data));
            $this->audit_log->log('update', 'customers', $customer_id, $old_data, $data);
            $message = 'Customer updated successfully!';
        } else {
            // Create new customer
            $data['created_by'] = get_current_user_id();
            $this->database->save_record('customers', $data);
            $customer_id = $wpdb->insert_id;
            $this->audit_log->log('create', 'customers', $customer_id, null, $data);
            $message = 'Customer created successfully!';
        }

        add_settings_error('customer_messages', 'customer_message', $message, 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=customers&action=edit&id=' . $customer_id));
        exit;
    }

    private function delete_customer($customer_id) {
        global $wpdb;
        
        // Check if customer has orders (will be implemented with invoices/quotations)
        $order_count = 0; // This will be implemented later

        if ($order_count > 0) {
            add_settings_error('customer_messages', 'customer_message', 
                'Cannot delete customer. There are ' . $order_count . ' orders associated with this customer.', 'error');
            wp_redirect(admin_url('admin.php?page=inventory-manager&tab=customers&action=edit&id=' . $customer_id));
            exit;
        }

        $old_data = $this->database->get_record('customers', $customer_id);
        $this->database->delete_record('customers', $customer_id);
        $this->audit_log->log('delete', 'customers', $customer_id, $old_data);
        
        add_settings_error('customer_messages', 'customer_message', 'Customer deleted successfully!', 'success');
        wp_redirect(admin_url('admin.php?page=inventory-manager&tab=customers'));
        exit;
    }
}