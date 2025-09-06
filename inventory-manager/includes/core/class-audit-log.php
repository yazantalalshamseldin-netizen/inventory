<?php
namespace InventoryManager\Core;

class AuditLog {
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    public function log($action, $table_name, $record_id, $old_data = null, $new_data = null) {
        $user_id = get_current_user_id();
        if (!$user_id) return false;

        return $this->database->log_audit(
            $user_id,
            $action,
            $table_name,
            $record_id,
            $old_data,
            $new_data
        );
    }

    public function get_logs($table_name = null, $record_id = null, $limit = 100) {
        global $wpdb;
        
        $where = [];
        $params = [];

        if ($table_name) {
            $where[] = 'table_name = %s';
            $params[] = $table_name;
        }

        if ($record_id) {
            $where[] = 'record_id = %d';
            $params[] = $record_id;
        }

        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT al.*, u.user_login, u.display_name 
                 FROM {$this->database->get_table('audit_log')} al
                 LEFT JOIN {$wpdb->users} u ON al.user_id = u.ID
                 {$where_clause}
                 ORDER BY al.created_at DESC 
                 LIMIT %d";

        $params[] = $limit;

        if (!empty($where)) {
            $query = $wpdb->prepare($query, $params);
        } else {
            $query = $wpdb->prepare($query, $limit);
        }

        return $wpdb->get_results($query);
    }
}