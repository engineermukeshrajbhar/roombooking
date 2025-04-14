<?php
require_once 'classes/Booking.php';
require_once 'classes/Admin.php';

$admin = new Admin();
$booking = new Booking();

if (!$admin->isLoggedIn()) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    if ($booking->cancelBooking($_POST['booking_id'])) {
        $_SESSION['success_message'] = "Booking cancelled successfully";
    } else {
        $_SESSION['error_message'] = "Failed to cancel booking";
    }
}

header('Location: admin.php');
exit;
?>