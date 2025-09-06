/* Enhanced Grid Styles with Line Separation */
.inventory-table.with-lines {
    border: 1px solid #e0e0e0;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: white;
}

.inventory-table.with-lines th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    border-bottom: 2px solid #e0e0e0;
}

.inventory-table.with-lines th:last-child {
    border-right: none;
}

.inventory-table.with-lines td {
    padding: 14px 12px;
    border-right: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
    background: white;
    transition: background-color 0.2s ease;
    position: relative;
}

.inventory-table.with-lines td:last-child {
    border-right: none;
}

.inventory-table.with-lines tr:last-child td {
    border-bottom: none;
}

.inventory-table.with-lines tr:hover td {
    background: #f8f9fa;
}

.inventory-table.with-lines tr:nth-child(even) td {
    background: #fafafa;
}

.inventory-table.with-lines tr:nth-child(even):hover td {
    background: #f0f4f8;
}

/* Zebra striping for better readability */
.inventory-table.with-lines tbody tr:nth-child(odd) td {
    background: white;
}

.inventory-table.with-lines tbody tr:nth-child(odd):hover td {
    background: #f8f9fa;
}

/* Enhanced cell borders */
.inventory-table.with-lines td {
    position: relative;
}

.inventory-table.with-lines td::after {
    content: '';
    position: absolute;
    right: -1px;
    top: 10%;
    height: 80%;
    width: 1px;
    background: #f0f0f0;
}

.inventory-table.with-lines td:last-child::after {
    display: none;
}

/* Form Grid Lines */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin: 25px 0;
    padding: 20px;
    background: #fafafa;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
}

.form-column {
    padding: 20px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.form-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f1f3f4;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.form-section h3 {
    color: #2c3e50;
    font-size: 1.2em;
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #667eea;
}

.form-group {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f8f9fa;
    position: relative;
}

.form-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.form-group::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #e9ecef, transparent);
}

.form-group:last-child::after {
    display: none;
}

/* Navigation Tabs with Grid Lines */
.nav-tab-wrapper {
    display: flex;
    gap: 5px;
    margin-bottom: 25px;
    background: #fff;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.nav-tab {
    padding: 15px 25px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #6c757d;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    background: #f8f9fa;
    color: #495057;
    border-color: #e9ecef;
}

.nav-tab-active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    border-color: #667eea;
}

/* Card Styles with Grid Lines */
.inventory-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    padding: 25px;
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}

.inventory-card h2 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 1.5em;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f3f4;
}

/* Form Card Styles */
.inventory-form-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    padding: 0;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px 30px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.5em;
}

/* Search Container */
.search-container {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.search-box {
    display: flex;
    gap: 10px;
    max-width: 600px;
    align-items: center;
}

.search-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
}

.search-button {
    padding: 12px 20px;
}

.clear-search {
    padding: 12px 20px;
}

/* Purchase Cards Grid */
.purchase-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.purchase-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.purchase-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.purchase-card h3 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 1.2em;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.purchase-details p {
    margin: 8px 0;
    font-size: 0.9em;
    color: #666;
    padding-left: 10px;
    border-left: 2px solid #f1f3f4;
}

.purchase-details strong {
    color: #2c3e50;
}

/* Status badges */
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    border: 1px solid;
}

.status-draft {
    background: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.status-received {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.status-completed {
    background: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

/* Calculation results styling */
.calculation-results {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.result-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.result-item:last-child {
    border-bottom: none;
}

.result-item.total {
    font-weight: 600;
    color: #2c3e50;
    border-top: 2px solid #667eea;
    margin-top: 10px;
    padding-top: 15px;
}

.result-item.final {
    font-weight: 700;
    color: #27ae60;
    font-size: 1.1em;
    border-top: 2px solid #27ae60;
    margin-top: 15px;
    padding-top: 15px;
}

.result-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #667eea, transparent);
    margin: 15px 0;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 15px;
    padding-top: 25px;
    border-top: 2px solid #e9ecef;
    margin-top: 20px;
}

/* Responsive tables */
@media (max-width: 1024px) {
    .inventory-table-container {
        overflow-x: auto;
    }
    
    .inventory-table.with-lines {
        min-width: 1000px;
    }
    
    .purchase-cards {
        grid-template-columns: 1fr;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* Print styles for grids */
@media print {
    .inventory-table.with-lines {
        border: 1px solid #000;
        box-shadow: none;
    }
    
    .inventory-table.with-lines th {
        background: #f0f0f0 !important;
        color: #000 !important;
        border: 1px solid #000 !important;
    }
    
    .inventory-table.with-lines td {
        border: 1px solid #ccc !important;
    }
}

/* No items state */
.no-items {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
}

.no-purchases {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

/* Action buttons */
.actions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.actions .button {
    margin: 2px;
}

/* Grid actions */
.grid-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

/* Search results info */
.search-results-info {
    padding: 15px 20px;
    background: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    color: #1565c0;
    border-radius: 4px;
    margin-bottom: 15px;
}