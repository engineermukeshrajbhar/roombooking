<?php
require_once 'DatabaseConnection.php';
require_once 'DatabaseQuery.php';

class Room {
    private $query;

    public function __construct() {
        $connection = new DatabaseConnection();
        $this->query = new DatabaseQuery($connection);
    }

    public function getAvailableRooms() {
        $sql = "SELECT r.*, 
                (SELECT COUNT(*) FROM bookings b WHERE b.room_id = r.id AND b.status = 'booked' AND b.expiry_time > NOW()) AS booked_count
                FROM rooms r";
        return $this->query->fetchAll($sql);
    }

    public function getRoomById($id) {
        $sql = "SELECT * FROM rooms WHERE id = ?";
        return $this->query->fetchOne($sql, [$id], 'i');
    }

    public function getAllRooms() {
        $sql = "SELECT * FROM rooms ORDER BY name";
        return $this->query->fetchAll($sql);
    }
}
?>