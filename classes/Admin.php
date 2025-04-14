<?php
require_once 'DatabaseConnection.php';
require_once 'DatabaseQuery.php';

class Admin {
    private $query;

    public function __construct() {
        $connection = new DatabaseConnection();
        $this->query = new DatabaseQuery($connection);
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM admin WHERE username = ?";
        $admin = $this->query->fetchOne($sql, [$username], 's');
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
        
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    public function logout() {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        session_destroy();
    }

    public function createAdmin($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
        return $this->query->insert($sql, [$username, $hashedPassword], 'ss');
    }
}
?>