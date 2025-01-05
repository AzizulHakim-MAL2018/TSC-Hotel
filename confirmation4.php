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

// Validate booking ID
$bookingID = $_GET['booking_id'] ?? null;
if (!$bookingID || !ctype_digit($bookingID)) { // Ensure booking_id is a valid numeric value
    header("Location: room4.php?error=invalid_booking");
    exit;
}

// Fetch booking details (excluding room info for now)
$query = "
    SELECT 
        b.BookingID,
        b.StartDate,
        b.EndDate,
        b.NumbOfRoomBooking,
        b.TotalPrice,
        p.PaymentStatus
    FROM Booking b
    JOIN Payment p ON b.PaymentID = p.PaymentID
    WHERE b.BookingID = ?
";
$params = [(int)$bookingID];
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    error_log("SQL Error: " . print_r(sqlsrv_errors(), true));
    header("Location: room4.php?error=query_failed");
    exit;
}

$booking = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$booking) {
    header("Location: room4.php?error=no_booking_found");
    exit;
}

// Fetch associated room details
$roomQuery = "
    SELECT 
        r.RoomNumber,
        r.RoomType
    FROM BookingRoom br
    JOIN Room r ON br.RoomID = r.RoomID
    WHERE br.BookingID = ?
";
$roomStmt = sqlsrv_query($conn, $roomQuery, $params);

if ($roomStmt === false) {
    error_log("SQL Error: " . print_r(sqlsrv_errors(), true));
    header("Location: room4.php?error=query_failed");
    exit;
}

// Prepare room data
$rooms = [];
while ($room = sqlsrv_fetch_array($roomStmt, SQLSRV_FETCH_ASSOC)) {
    $rooms[] = $room;
}

// Handle null dates
$startDate = $booking['StartDate'] ? $booking['StartDate']->format('Y-m-d') : "Not Available";
$endDate = $booking['EndDate'] ? $booking['EndDate']->format('Y-m-d') : "Not Available";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="s/str.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <main class="confirmation-page">
        <div class="container">
            <h1>Booking Confirmation</h1>
            <p>Thank you for your booking! Here are the details:</p>
            <div class="booking-details">
                <p><strong>Booking ID:</strong> <?= htmlspecialchars($booking['BookingID']) ?></p>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($startDate) ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($endDate) ?></p>
                <p><strong>Number of Rooms:</strong> <?= htmlspecialchars($booking['NumbOfRoomBooking']) ?></p>
                <p><strong>Total Price:</strong> RM<?= htmlspecialchars($booking['TotalPrice']) ?></p>
                <p><strong>Payment Status:</strong> <?= htmlspecialchars($booking['PaymentStatus']) ?></p>

                <h3>Room Details</h3>
                <ul>
                    <?php foreach ($rooms as $room): ?>
                        <li padding: 20px;>
                            <strong>Room Number:</strong> <?= htmlspecialchars($room['RoomNumber']) ?>,
                            <strong>Room Type:</strong> <?= htmlspecialchars($room['RoomType']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button onclick="window.location.href='Homepage.php'">Go to Home</button>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TSC Hotel. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>