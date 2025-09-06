/* Tab Navigation */
.inventory-nav-tabs {
    display: flex;
    gap: 5px;
    margin-bottom: 25px;
    background: #fff;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.inventory-nav-tabs .nav-tab {
    padding: 15px 25px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #6c757d;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.inventory-nav-tabs .nav-tab:hover {
    background: #f8f9fa;
    color: #495057;
}

.inventory-nav-tabs .nav-tab-active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Tab Content */
.inventory-tab-content {
    margin-top: 20px;
}

/* Enhanced Grid Lines for Tables */
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

/* Enhanced cell borders */
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

.coming-soon {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    margin-top: 20px;
}

.coming-soon h3 {
    color: #667eea;
    margin-top: 0;
}

.coming-soon ul {
    columns: 2;
    column-gap: 30px;
}

.coming-soon li {
    margin-bottom: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.coming-soon li:last-child {
    border-bottom: none;
}