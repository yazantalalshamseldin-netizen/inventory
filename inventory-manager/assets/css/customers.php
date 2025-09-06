/* Customers Tab Styles */
.customer-stats {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
    border-left: 4px solid #2196f3;
}

.customer-stats h4 {
    margin: 0 0 15px 0;
    color: #1565c0;
    font-size: 1.1em;
}

.customer-stats .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}

.customer-stats .stat-item {
    background: white;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #bbdefb;
    text-align: center;
}

.customer-stats .stat-label {
    display: block;
    font-size: 0.8em;
    color: #546e7a;
    margin-bottom: 6px;
    font-weight: 500;
}

.customer-stats .stat-value {
    display: block;
    font-size: 1.1em;
    font-weight: 600;
    color: #1565c0;
}

/* Customer related data */
.customer-related-data {
    margin-top: 30px;
    border-top: 2px solid #e3f2fd;
    padding-top: 25px;
}

.customer-orders {
    margin-top: 15px;
}

/* Customer grid enhancements */
.customer-name strong {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1em;
}

.customer-name .text-muted {
    color: #6c757d;
    font-size: 0.85em;
    margin-top: 4px;
    display: block;
}

.contact-info a {
    color: #2196f3;
    text-decoration: none;
    display: block;
    margin-bottom: 4px;
}

.contact-info a:hover {
    color: #1976d2;
    text-decoration: underline;
}

.company {
    font-weight: 500;
    color: #37474f;
}

.location {
    color: #546e7a;
    font-size: 0.9em;
}

.total-orders .stat-badge {
    background: #2196f3;
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
    color: #4caf50;
    font-size: 1.1em;
}

.last-order {
    color: #546e7a;
    font-size: 0.9em;
}

/* Form enhancements for customers */
.customer-form .form-section h3 {
    color: #2196f3;
    border-bottom: 2px solid #2196f3;
    padding-bottom: 10px;
}

.customer-form input[type="tel"],
.customer-form input[type="email"] {
    direction: ltr;
}

.customer-form .form-group {
    position: relative;
}

.customer-form .form-group::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #bbdefb, transparent);
}

/* Responsive design */
@media (max-width: 1024px) {
    .customer-stats .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .contact-info {
        min-width: 150px;
    }
}

@media (max-width: 768px) {
    .customer-stats .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .customer-name, .contact-info {
        min-width: 140px;
    }
    
    .contact-info a {
        font-size: 0.9em;
    }
}

/* Animation for customer cards */
.customer-stats .stat-item {
    transition: all 0.3s ease;
}

.customer-stats .stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.2);
}

/* Print styles */
@media print {
    .customer-stats {
        border: 1px solid #2196f3;
        page-break-inside: avoid;
    }
    
    .contact-info a {
        color: #000 !important;
        text-decoration: none !important;
    }
}

/* Enhanced action buttons */
.actions .button-success {
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    border-color: #4caf50;
}

.actions .button-success:hover {
    background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
    border-color: #45a049;
}

/* Customer type indicators */
.customer-type {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.7em;
    font-weight: 600;
    text-transform: uppercase;
    margin-left: 8px;
}

.type-regular {
    background: #e3f2fd;
    color: #1976d2;
}

.type-vip {
    background: #fff3e0;
    color: #f57c00;
}

.type-wholesale {
    background: #e8f5e9;
    color: #388e3c;
}

/* Customer since date */
.customer-since {
    font-size: 0.8em;
    color: #78909c;
    margin-top: 4px;
    display: block;
}