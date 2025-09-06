<?php
namespace InventoryManager\Admin;

class Invoice_Tab {
    public function get_title() {
        return __('Invoices', 'inventory-manager');
    }

    public function render() {
        ?>
        <div class="inventory-card">
            <div class="card-header">
                <h2>🧾 Invoices</h2>
            </div>
            <div class="card-content">
                <p>Invoicing functionality coming soon! This tab is ready for implementation.</p>
                <div class="coming-soon">
                    <h3>📈 Planned Features:</h3>
                    <ul>
                        <li>Create customer invoices</li>
                        <li>Invoice management</li>
                        <li>Payment tracking</li>
                        <li>PDF invoice generation</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    public function handle_request() {
        // Handle form submissions later
    }
}