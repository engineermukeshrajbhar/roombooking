<?php
require_once 'includes/header.php';
require_once 'classes/Booking.php';

$booking = new Booking();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $userBookings = $booking->getBookingsByEmail($email);
}
?>

<div class="container">
    <h2>Check Your Booking Status</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Enter Your Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Check Status</button>
    </form>

    <?php if (isset($userBookings)): ?>
        <div class="mt-4">
            <h3>Your Bookings</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Status</th>
                        <th>Booking Time</th>
                        <th>Expiry Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userBookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['room_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $booking['status'] === 'booked' ? 'success' : 
                                    ($booking['status'] === 'in_queue' ? 'warning' : 'secondary') 
                                ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($booking['booking_time']) ?></td>
                            <td><?= htmlspecialchars($booking['expiry_time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>