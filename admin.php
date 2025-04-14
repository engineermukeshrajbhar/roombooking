<?php
require_once 'includes/header.php';
require_once 'classes/Admin.php';
require_once 'classes/Booking.php';

$admin = new Admin();
$booking = new Booking();

if (!$admin->isLoggedIn()) {
    header('Location: admin_login.php');
    exit;
}

$bookings = $booking->getAllBookings();
?>

<h1 class="mb-4">Admin Dashboard</h1>
<div class="d-flex justify-content-between mb-4">
    <h2>Current Bookings</h2>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Room</th>
                <th>Name</th>
                <th>Email</th>
                <th>Booking Time</th>
                <th>Expiry Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['email']); ?></td>
                    <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                    <td><?php echo htmlspecialchars($booking['expiry_time']); ?></td>
                    <td>
                        <span class="badge 
                            <?php echo $booking['status'] === 'booked' ? 'bg-success' : 
                                  ($booking['status'] === 'in_queue' ? 'bg-warning text-dark' : 'bg-secondary'); ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($booking['status'] === 'booked' || $booking['status'] === 'in_queue'): ?>
                            <form method="POST" action="cancel_booking.php" style="display: inline;">
                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>