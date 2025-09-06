/* Store Locations Tab Styles */
.store-stats {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 25px;
    margin: 25px 0;
    border-left: 4px solid #667eea;
}

.store-stats h3 {
    margin: 0 0 20px 0;
    color: #2c3e50;
    font-size: 1.2em;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.store-stats .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.store-stats .stat-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.store-stats .stat-label {
    display: block;
    font-size: 0.85em;
    color: #6c757d;
    margin-bottom: 8px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.store-stats .stat-value {
    display: block;
    font-size: 1.3em;
    font-weight: 700;
    color: #2c3e50;
}

/* Store related data */
.store-related-data {
    margin-top: 30px;
    border-top: 2px solid #e9ecef;
    padding-top: 25px;
}

.store-inventory {
    margin-top: 15px;
}

.store-inventory .inventory-table {
    font-size: 0.9em;
}

.store-inventory .inventory-table th {
    padding: 12px 8px;
    font-size: 0.8em;
}

.store-inventory .inventory-table td {
    padding: 10px 8px;
}

/* Store grid enhancements */
.store-code strong {
    color: #667eea;
    font-weight: 700;
    font-size: 1.1em;
}

.store-name strong {
    color: #2c3e50;
    font-weight: 600;
}

.store-name .text-muted {
    color: #6c757d;
    font-size: 0.85em;
    margin-top: 4px;
    display: block;
}

.location span[title] {
    cursor: help;
    border-bottom: 1px dotted #667eea;
}

/* Stock indicators */
.stock-indicators {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.stock-warning, .stock-danger, .stock-good {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
    white-space: nowrap;
}

.stock-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.stock-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.stock-good {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.inventory-value strong {
    color: #27ae60;
    font-size: 1.1em;
}

/* Form enhancements for stores */
.store-form .form-section h3 {
    color: #667eea;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.store-form textarea {
    min-height: 80px;
    resize: vertical;
}

.store-form input[readonly] {
    background-color: #f8f9fa;
    border-color: #e9ecef;
    color: #6c757d;
}

/* Responsive design */
@media (max-width: 768px) {
    .store-stats .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stock-indicators {
        justify-content: center;
    }
    
    .store-name, .location {
        min-width: 120px;
    }
}

/* Print styles */
@media print {
    .store-stats {
        border: 1px solid #000;
        page-break-inside: avoid;
    }
    
    .stat-item {
        border: 1px solid #ccc;
    }
}

/* Animation for stats */
.stat-item {
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Status badges in inventory */
.status-badge.out-of-stock {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.low-stock {
    background: #fff3cd;
    color: #856404;
}

.status-badge.in-stock {
    background: #d4edda;
    color: #155724;
}

/* No data state */
.no-data {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
}