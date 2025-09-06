/* Suppliers Tab Styles */
.supplier-stats {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    border-left: 4px solid #667eea;
}

.supplier-stats h4 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 1.1em;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.stat-item {
    background: white;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 0.8em;
    color: #6c757d;
    margin-bottom: 5px;
    font-weight: 500;
}

.stat-value {
    display: block;
    font-size: 1.1em;
    font-weight: 600;
    color: #2c3e50;
}

/* Supplier related data */
.supplier-related-data {
    margin-top: 30px;
    border-top: 2px solid #e9ecef;
    padding-top: 25px;
}

.related-purchases {
    margin-top: 15px;
}

.purchases-list table {
    width: 100%;
    margin-top: 10px;
}

.purchases-list .inventory-table {
    font-size: 0.9em;
}

.purchases-list .inventory-table th {
    padding: 12px 8px;
    font-size: 0.8em;
}

.purchases-list .inventory-table td {
    padding: 10px 8px;
}

/* Supplier grid enhancements */
.supplier-name strong {
    color: #2c3e50;
    font-weight: 600;
}

.supplier-name .text-muted {
    color: #6c757d;
    font-size: 0.85em;
    margin-top: 4px;
    display: block;
}

.email a, .phone a {
    color: #667eea;
    text-decoration: none;
}

.email a:hover, .phone a:hover {
    color: #5a6fd8;
    text-decoration: underline;
}

.stat-badge {
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
    min-width: 30px;
    display: inline-block;
    text-align: center;
}

.total-spent strong {
    color: #27ae60;
    font-size: 1.1em;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}

/* Form enhancements for suppliers */
.supplier-form .form-section h3 {
    color: #667eea;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.supplier-form textarea {
    min-height: 80px;
    resize: vertical;
}

/* Responsive design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-item {
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .supplier-name {
        min-width: 150px;
    }
}

/* Print styles */
@media print {
    .supplier-stats {
        border: 1px solid #000;
        page-break-inside: avoid;
    }
    
    .stat-item {
        border: 1px solid #ccc;
    }
}

/* Animation for stats */
.stat-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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