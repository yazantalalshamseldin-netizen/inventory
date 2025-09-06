<?php
namespace InventoryManager\Core;

class Database {
    private $tables;

    public function __construct() {
        global $wpdb;
        $this->tables = [
            'items' => $wpdb->prefix . 'inventory_items',
            'suppliers' => $wpdb->prefix . 'inventory_suppliers',
            'customers' => $wpdb->prefix . 'inventory_customers',
            'stores' => $wpdb->prefix . 'inventory_stores',
            'purchases' => $wpdb->prefix . 'inventory_purchases',
            'purchase_items' => $wpdb->prefix . 'inventory_purchase_items',
            'audit_log' => $wpdb->prefix . 'inventory_audit_log'
        ];
    }

    public function get_table($table_name) {
        return isset($this->tables[$table_name]) ? $this->tables[$table_name] : null;
    }

    public function get_items($search_term = '') {
        global $wpdb;
        
        $where = '';
        if (!empty($search_term)) {
            $where = $wpdb->prepare(
                "WHERE product_code LIKE %s OR description LIKE %s",
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%'
            );
        }
        
        return $wpdb->get_results("SELECT * FROM {$this->tables['items']} {$where} ORDER BY created_at DESC");
    }

    public function get_item($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tables['items']} WHERE id = %d", $id));
    }

    public function save_item($data) {
        global $wpdb;
        
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $wpdb->update($this->tables['items'], $data, ['id' => $id]);
        } else {
            unset($data['id']);
            return $wpdb->insert($this->tables['items'], $data);
        }
    }

    public function delete_item($id) {
        global $wpdb;
        return $wpdb->delete($this->tables['items'], ['id' => $id]);
    }

    public function count_items($search_term = '') {
        global $wpdb;
        
        $where = '';
        if (!empty($search_term)) {
            $where = $wpdb->prepare(
                "WHERE product_code LIKE %s OR description LIKE %s",
                '%' . $wpdb->esc_like($search_term) . '%',
                '%' . $wpdb->esc_like($search_term) . '%'
            );
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->tables['items']} {$where}");
    }

    // Generic methods for all tables
    public function get_records($table, $search_term = '', $search_fields = [], $order_by = 'id', $order = 'DESC') {
        global $wpdb;
        
        $where = '';
        $placeholders = [];
        
        if (!empty($search_term) && !empty($search_fields)) {
            $like_clauses = [];
            foreach ($search_fields as $field) {
                $like_clauses[] = "$field LIKE %s";
                $placeholders[] = '%' . $wpdb->esc_like($search_term) . '%';
            }
            $where = "WHERE " . implode(' OR ', $like_clauses);
        }
        
        if (!empty($placeholders)) {
            $query = $wpdb->prepare(
                "SELECT * FROM {$this->tables[$table]} {$where} ORDER BY $order_by $order",
                $placeholders
            );
        } else {
            $query = "SELECT * FROM {$this->tables[$table]} {$where} ORDER BY $order_by $order";
        }
        
        return $wpdb->get_results($query);
    }

    public function get_record($table, $id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->tables[$table]} WHERE id = %d", $id));
    }

    public function save_record($table, $data) {
        global $wpdb;
        
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            return $wpdb->update($this->tables[$table], $data, ['id' => $id]);
        } else {
            unset($data['id']);
            return $wpdb->insert($this->tables[$table], $data);
        }
    }

    public function delete_record($table, $id) {
        global $wpdb;
        return $wpdb->delete($this->tables[$table], ['id' => $id]);
    }

    public function count_records($table, $search_term = '', $search_fields = []) {
        global $wpdb;
        
        $where = '';
        $placeholders = [];
        
        if (!empty($search_term) && !empty($search_fields)) {
            $like_clauses = [];
            foreach ($search_fields as $field) {
                $like_clauses[] = "$field LIKE %s";
                $placeholders[] = '%' . $wpdb->esc_like($search_term) . '%';
            }
            $where = "WHERE " . implode(' OR ', $like_clauses);
        }
        
        if (!empty($placeholders)) {
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tables[$table]} {$where}",
                $placeholders
            );
        } else {
            $query = "SELECT COUNT(*) FROM {$this->tables[$table]} {$where}";
        }
        
        return $wpdb->get_var($query);
    }

    // Get distinct store locations
    public function get_store_locations() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT DISTINCT store_location 
             FROM {$this->tables['items']} 
             WHERE store_location != '' 
             ORDER BY store_location"
        );
    }

    // Audit log methods
    public function log_audit($user_id, $action, $table_name, $record_id, $old_data = null, $new_data = null) {
        global $wpdb;
        
        return $wpdb->insert($this->tables['audit_log'], [
            'user_id' => $user_id,
            'action' => $action,
            'table_name' => $table_name,
            'record_id' => $record_id,
            'old_data' => $old_data ? serialize($old_data) : null,
            'new_data' => $new_data ? serialize($new_data) : null
        ]);
    }
}