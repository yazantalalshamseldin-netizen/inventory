/* Inventory Manager Pro - Main Admin Styles */
.inventory-manager-wrapper {
    max-width: 1400px;
    margin: 20px auto;
}

/* ===== TABLE STYLES WITH GRID LINES ===== */
.inventory-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: white;
    margin: 20px 0;
}

.inventory-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    border-bottom: 2px solid #e0e0e0;
}

.inventory-table th:last-child {
    border-right: none;
}

.inventory-table td {
    padding: 14px 12px;
    border-right: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
    background: white;
    transition: background-color 0.2s ease;
    position: relative;
}

.inventory-table td:last-child {
    border-right: none;
}

.inventory-table tr:last-child td {
    border-bottom: none;
}

.inventory-table tr:hover td {
    background: #f8f9fa !important;
}

.inventory-table tr:nth-child(even) td {
    background: #fafafa;
}

.inventory-table tr:nth-child(odd) td {
    background: white;
}

/* Enhanced cell borders */
.inventory-table td::after {
    content: '';
    position: absolute;
    right: -1px;
    top: 10%;
    height: 80%;
    width: 1px;
    background: #f0f0f0;
}

.inventory-table td:last-child::after {
    display: none;
}

/* ===== NAVIGATION TABS ===== */
.nav-tab-wrapper {
    display: flex;
    gap: 5px;
    margin-bottom: 25px;
    background: #fff;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e0e0e0;
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
    border-color: #e0e0e0;
}

.nav-tab-active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    border-color: #667eea;
}

/* ===== CARD STYLES ===== */
.inventory-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    padding: 25px;
    margin-bottom: 25px;
    border: 1px solid #e0e0e0;
}

.inventory-card h2 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 1.5em;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.inventory-form-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    padding: 0;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px 30px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.5em;
    border-bottom: none;
}

/* ===== FORM STYLES ===== */
.inventory-form {
    padding: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.form-column {
    padding: 20px;
    background: white;
    border-radius: 6px;
    border: 1px solid #e0e0e0;
}

.form-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.form-section h3 {
    margin: 0 0 20px 0;
    color: #2c3e50;
    font-size: 1.2em;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* ===== BUTTON STYLES ===== */
.button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    gap: 8px;
    border: 1px solid transparent;
}

.button-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.button-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-2px);
    border-color: #5a6fd8;
}

.button-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    border-color: #e74c3c;
}

.button-danger:hover {
    background: linear-gradient(135deg, #c0392b 0%, #a33224 100%);
    border-color: #c0392b;
}

.button-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.button-secondary:hover {
    background: #5a6268;
    border-color: 5a6268;
}

.button-small {
    padding: 8px 12px;
    font-size: 12px;
}

/* ===== SEARCH & FILTERS ===== */
.search-container {
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
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
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
}

.search-button {
    padding: 12px 20px;
}

.clear-search {
    padding: 12px 20px;
}

.search-results-info {
    padding: 15px 20px;
    background: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    color: #1565c0;
}

.inventory-table-container {
    padding: 20px;
}

/* ===== STATUS INDICATORS ===== */
.out-of-stock {
    color: #e74c3c;
    font-weight: 700;
}

.low-stock {
    color: #f39c12;
    font-weight: 700;
}

.profit-positive {
    color: #27ae60;
    font-weight: 600;
}

.profit-negative {
    color: #e74c3c;
    font-weight: 600;
}

.no-items {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
    background: #f8f9fa;
    border: 2px dashed #e0e0e0;
    border-radius: 8px;
    margin: 20px;
}

.actions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.actions .button {
    margin: 2px;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .inventory-table-container {
        overflow-x: auto;
    }
    
    .inventory-table {
        min-width: 800px;
    }
}

@media (max-width: 768px) {
    .inventory-header h1 {
        font-size: 2em;
    }
    
    .card-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .search-box {
        flex-direction: column;
    }
    
    .inventory-table {
        font-size: 14px;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .nav-tab-wrapper {
        flex-wrap: wrap;
    }
    
    .nav-tab {
        padding: 10px 15px;
        font-size: 14px;
    }
}

/* ===== UTILITY CLASSES ===== */
.text-muted {
    color: #6c757d !important;
}

.text-center {
    text-align: center;
}

.mt-20 {
    margin-top: 20px;
}

.mb-20 {
    margin-bottom: 20px;
}

/* Force grid lines to be visible */
.inventory-table,
.inventory-table th,
.inventory-table td {
    border: 1px solid #e0e0e0 !important;
}

.inventory-table th {
    border-bottom: 2px solid #e0e0e0 !important;
}

/* Ensure table cells have proper spacing */
.inventory-table td {
    padding: 12px 15px !important;
    vertical-align: middle !important;
}

/* Zebra striping for better readability */
.inventory-table tr:nth-child(even) {
    background-color: #fafafa !important;
}

.inventory-table tr:nth-child(odd) {
    background-color: #ffffff !important;
}

/* Hover effect */
.inventory-table tr:hover {
    background-color: #f8f9fa !important;
}

.inventory-table tr:hover td {
    background-color: #f8f9fa !important;
}