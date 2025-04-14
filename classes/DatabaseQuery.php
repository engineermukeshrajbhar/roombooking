<?php
class DatabaseQuery {
    private $connection;

    public function __construct(DatabaseConnection $connection) {
        $this->connection = $connection;
    }

    public function executeQuery($sql, $params = [], $types = '') {
        $stmt = $this->connection->getConnection()->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->getConnection()->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt;
    }

    public function fetchAll($sql, $params = [], $types = '') {
        try {
            $stmt = $this->executeQuery($sql, $params, $types);
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    public function fetchOne($sql, $params = [], $types = '') {
        try {
            $stmt = $this->executeQuery($sql, $params, $types);
            $result = $stmt->get_result();
            return $result->fetch_assoc() ?: [];
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    public function insert($sql, $params = [], $types = '') {
        try {
            $stmt = $this->executeQuery($sql, $params, $types);
            return $stmt->insert_id;
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function update($sql, $params = [], $types = '') {
        try {
            $stmt = $this->executeQuery($sql, $params, $types);
            return $stmt->affected_rows;
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function beginTransaction() {
        $this->connection->getConnection()->begin_transaction();
    }

    public function commit() {
        $this->connection->getConnection()->commit();
    }

    public function rollback() {
        $this->connection->getConnection()->rollback();
    }
}
?>