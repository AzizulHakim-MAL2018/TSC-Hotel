<?php
session_start();
require_once 'db_con.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Loginpage.php");
    exit;
}

// Function to display the header
function displayHeader($username, $FirstName)
{
    echo "<header class='header'>";
    echo "<div class='container'>";
    echo "<div class='logo'><a href='Homepage.php'>TSC Hotel</a></div>";
    echo "<nav class='nav'>";
    echo "<ul>";
    echo "<li><a href='Homepage.php'>Home</a></li>";
    echo "<li><a href='room4.php'>Rooms</a></li>";
    echo "<li><a href='bookingrecord.php'>Record</a></li>";
    echo "<li><a href='reviewpage.php'>Review</a></li>";
    echo "<li><a href='contact.php'>Contact</a></li>";
    echo "<li><a href='loginpage.php' class='logout-button'>Logout</a></li>";
    echo "</ul>";
    echo "</nav>";
    echo "<div class='user-info'><a href='profilepage.php'>Welcome, $FirstName ($username)</a></div>";
    echo "</div>";
    echo "</header>";
}

// Get the booking ID from the query string
if (!isset($_GET['booking_id'])) {
    header("Location: bookingrecord.php");
    exit;
}

$bookingId = intval($_GET['booking_id']);

// Fetch the booking details
$bookingQuery = "
    SELECT 
        b.BookingID, 
        b.StartDate, 
        b.EndDate, 
        b.TotalPrice, 
        br.RoomID,
        r.RoomNumber, 
        r.BasePrice
    FROM Booking b
    LEFT JOIN BookingRoom br ON b.BookingID = br.BookingID
    LEFT JOIN Room r ON br.RoomID = r.RoomID
    WHERE b.BookingID = ?
";
$params = [$bookingId];
$bookingResult = sqlsrv_query($conn, $bookingQuery, $params);

if ($bookingResult === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

// Fetch all rooms for selection
$roomQuery = "
    SELECT 
        RoomID, 
        RoomNumber, 
        RoomType, 
        BasePrice, 
        Pax, 
        RoomStatus 
    FROM Room 
    WHERE RoomStatus = 'available'
";
$roomResult = sqlsrv_query($conn, $roomQuery);

if ($roomResult === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

// Store booking details for display
$bookingDetails = [];
while ($row = sqlsrv_fetch_array($bookingResult, SQLSRV_FETCH_ASSOC)) {
    $bookingDetails[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="s/s2.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <main class="booking-page">
        <div class="container">
            <h1>Edit Booking</h1>

            <!-- Display existing booking details -->
            <h2>Current Booking Details</h2>
            <table class="room-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookingDetails as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['RoomNumber']) ?></td>
                            <td><?= htmlspecialchars($booking['StartDate']->format('Y-m-d')) ?></td>
                            <td><?= htmlspecialchars($booking['EndDate']->format('Y-m-d')) ?></td>
                            <td>RM<?= htmlspecialchars($booking['TotalPrice']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Update Booking</h2>
            <form method="POST" action="">
                <div class="room-list">
                    <table class="room-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Room Number</th>
                                <th>Room Type</th>
                                <th>Pax</th>
                                <th>per night Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = sqlsrv_fetch_array($roomResult, SQLSRV_FETCH_ASSOC)): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_rooms[]" value="<?= $row['RoomID'] ?>">
                                    </td>
                                    <td><?= htmlspecialchars($row['RoomNumber']) ?></td>
                                    <td><?= htmlspecialchars($row['RoomType']) ?></td>
                                    <td><?= htmlspecialchars($row['Pax']) ?></td>
                                    <td>RM<?= htmlspecialchars($row['BasePrice']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" required>

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" required>

                <button type="submit" name="update_booking">Update Booking</button>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking'])) {
                $startDate = $_POST['start_date'];
                $endDate = $_POST['end_date'];
                $selectedRooms = $_POST['selected_rooms'] ?? [];

                if (empty($selectedRooms)) {
                    echo "<div class='message error'>Please select at least one room.</div>";
                } else {
                    $totalPrice = 0;

                    foreach ($selectedRooms as $roomId) {
                        $roomPriceQuery = "SELECT BasePrice FROM Room WHERE RoomID = ?";
                        $roomPriceResult = sqlsrv_query($conn, $roomPriceQuery, [$roomId]);

                        if ($roomRow = sqlsrv_fetch_array($roomPriceResult, SQLSRV_FETCH_ASSOC)) {
                            $totalPrice += $roomRow['BasePrice'];
                        }
                    }

                    $updateQuery = "
                        UPDATE Booking 
                        SET StartDate = ?, EndDate = ?, TotalPrice = ? 
                        WHERE BookingID = ?
                    ";
                    $updateParams = [$startDate, $endDate, $totalPrice, $bookingId];

                    if (sqlsrv_query($conn, $updateQuery, $updateParams)) {
                        echo "<div class='message success'>Booking updated successfully.</div>";
                        echo "<script>setTimeout(() => window.location.href = 'bookingrecord.php', 2000);</script>";
                    } else {
                        echo "<div class='message error'>Failed to update booking.</div>";
                    }
                }
            }
            ?>
        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TSC Hotel. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>