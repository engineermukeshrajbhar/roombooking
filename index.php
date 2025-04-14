<?php

date_default_timezone_set('Asia/Kolkata');

require_once 'includes/header.php';
require_once 'classes/Room.php';
require_once 'classes/Booking.php';

$room = new Room();
$booking = new Booking();

// Process booking form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    $result = $booking->createBooking(
        $_POST['room_id'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email']
    );
    
    if ($result['status'] === 'success') {
        if ($result['booking_status'] === 'in_queue') {
            $message = "You are in queue. You'll be notified if a slot becomes available.";
        } else {
            $message = "Booking successful! You have 5 minutes to complete your booking.";
        }
    } else {
        $error = $result['message'];
    }
}

$rooms = $room->getAvailableRooms();
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <h1 class="text-center mb-4">Book a Room</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <?php foreach ($rooms as $room): 
                $available = $room['booked_count'] < 2;
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card room-card <?php echo !$available ? 'booked' : ''; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                            <p class="card-text">
                                Status: <?php echo $available ? 'Available' : 'Fully Booked'; ?><br>
                                Current bookings: <?php echo $room['booked_count']; ?>/2
                            </p>
                            
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal" 
                                data-room-id="<?php echo $room['id']; ?>"
                                data-room-name="<?php echo htmlspecialchars($room['name']); ?>"
                                <?php echo !$available ? 'disabled' : ''; ?>>
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book <span id="modalRoomName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="room_id" id="modalRoomId">
                    
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var bookingModal = document.getElementById('bookingModal');
    bookingModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var roomId = button.getAttribute('data-room-id');
        var roomName = button.getAttribute('data-room-name');
        
        document.getElementById('modalRoomId').value = roomId;
        document.getElementById('modalRoomName').textContent = roomName;
    });
</script>

<?php require_once 'includes/footer.php'; ?>