<?php

date_default_timezone_set('Asia/Kolkata');

require_once 'DatabaseConnection.php';
require_once 'DatabaseQuery.php';
require_once 'Room.php';

class Booking {
    private $query;
    private $room;

    public function __construct() {
        $connection = new DatabaseConnection();
        $this->query = new DatabaseQuery($connection);
        $this->room = new Room();
    }

    public function createBooking($roomId, $firstName, $lastName, $email) {
        $this->query->beginTransaction();
        
        try {
            // Check room availability
            $bookingsCount = $this->getActiveBookingsCount($roomId);
            
            $status = 'booked';
            if ($bookingsCount >= 2) {
                $status = 'in_queue';
            }

            $expiryTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
            
            $sql = "INSERT INTO bookings (room_id, first_name, last_name, email, status, expiry_time) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$roomId, $firstName, $lastName, $email, $status, $expiryTime];
            $types = "isssss";
            
            $bookingId = $this->query->insert($sql, $params, $types);
            
            if (!$bookingId) {
                throw new Exception("Booking creation failed");
            }

            $this->processQueue();
            $this->query->commit();
            
            return [
                'status' => 'success', 
                'booking_status' => $status,
                'booking_id' => $bookingId
            ];
        } catch (Exception $e) {
            $this->query->rollback();
            return [
                'status' => 'error', 
                'message' => $e->getMessage()
            ];
        }
    }

    private function getActiveBookingsCount($roomId) {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE room_id = ? AND status = 'booked' AND expiry_time > NOW()";
        $result = $this->query->fetchOne($sql, [$roomId], 'i');
        return $result['count'] ?? 0;
    }

    public function processQueue() {
        // Check for expired bookings
        $this->query->update(
            "UPDATE bookings SET status = 'cancelled' 
             WHERE status = 'booked' AND expiry_time <= NOW()"
        );
        
        // Process queue for each room
        $rooms = $this->room->getAllRooms();
        
        foreach ($rooms as $room) {
            $availableSlots = 2 - $this->getActiveBookingsCount($room['id']);
            
            if ($availableSlots > 0) {
                // Get the next in queue
                $sql = "SELECT id FROM bookings 
                        WHERE room_id = ? AND status = 'in_queue' 
                        ORDER BY booking_time ASC LIMIT ?";
                $queueItems = $this->query->fetchAll($sql, [$room['id'], $availableSlots], 'ii');
                
                foreach ($queueItems as $item) {
                    $this->promoteFromQueue($item['id']);
                }
            }
        }
    }

    private function promoteFromQueue($bookingId) {
        $expiryTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $this->query->update(
            "UPDATE bookings SET status = 'booked', expiry_time = ? 
             WHERE id = ?",
            [$expiryTime, $bookingId],
            'si'
        );
    }

    public function getAllBookings() {
        $sql = "SELECT b.*, r.name as room_name 
                FROM bookings b 
                JOIN rooms r ON b.room_id = r.id 
                ORDER BY b.booking_time DESC";
        return $this->query->fetchAll($sql);
    }

    public function cancelBooking($bookingId) {
        $this->query->beginTransaction();
        
        try {
            // First get the room ID before cancelling
            $sql = "SELECT room_id FROM bookings WHERE id = ?";
            $booking = $this->query->fetchOne($sql, [$bookingId], 'i');
            
            if (!$booking) {
                throw new Exception("Booking not found");
            }

            // Cancel the booking
            $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
            $result = $this->query->update($sql, [$bookingId], 'i');
            
            if (!$result) {
                throw new Exception("Failed to cancel booking");
            }

            // Process the queue for this room
            $this->processQueue();
            
            $this->query->commit();
            return true;
        } catch (Exception $e) {
            $this->query->rollback();
            error_log("Cancel booking error: " . $e->getMessage());
            return false;
        }
    }

    public function getBookingsByEmail($email) {
        $sql = "SELECT b.*, r.name as room_name 
                FROM bookings b 
                JOIN rooms r ON b.room_id = r.id 
                WHERE b.email = ?
                ORDER BY b.booking_time DESC";
        return $this->query->fetchAll($sql, [$email], 's');
    }
}
?>