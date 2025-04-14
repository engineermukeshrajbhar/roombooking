<?php
class DatabaseConnection {
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbname = 'room_booking_system';
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function __destruct() {
        $this->closeConnection();
    }
}
?>