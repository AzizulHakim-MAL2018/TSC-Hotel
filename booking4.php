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
    echo "<li><a href='contact.php'>Contact</a></li>";
    echo "<li><a href='loginpage.php' class='logout-button'>Logout</a></li>";
    echo "</ul>";
    echo "</nav>";
    echo "<div class='user-info'>Welcome, $FirstName ($username)</div>";
    echo "</div>";
    echo "</header>";
}

// Check if rooms were selected or retained
if (!isset($_POST['selected_rooms']) && !isset($_POST['room_ids'])) {
    echo "No rooms were selected.";
    exit;
}

// Retain selected rooms for subsequent form submissions
$selectedRooms = isset($_POST['selected_rooms']) ? $_POST['selected_rooms'] : explode(',', $_POST['room_ids']);
$roomIDs = implode(',', array_map('intval', $selectedRooms)); // Ensure integers for security

// Fetch room details for price calculation
$query = "
    SELECT RoomID, BasePrice 
    FROM Room 
    WHERE RoomID IN ($roomIDs)
";
$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

// Calculate total base price
$totalBasePrice = 0;
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $totalBasePrice += $row['BasePrice'];
}

// Only process form submission if POST data is complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_date'], $_POST['end_date'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    $numRooms = count($selectedRooms);
    $customerId = $_SESSION['user_id'];
    $paymentStatus = "Pending"; // Set a default value for payment status

    // Calculate total payment
    $startDateTime = strtotime($startDate);
    $endDateTime = strtotime($endDate);
    $days = ($endDateTime - $startDateTime) / (60 * 60 * 24);
    $totalPrice = $days * $totalBasePrice;

    // Call stored procedure to insert booking
    $insertQuery = "{CALL InsertBooking(?, ?, ?, ?, ?, ?, ?)}";
    $insertParams = [$customerId, $startDate, $endDate, $numRooms, $roomIDs, $totalPrice, $paymentStatus];
    $insertStmt = sqlsrv_query($conn, $insertQuery, $insertParams);

    if ($insertStmt === false) {
        die("Stored procedure execution failed: " . print_r(sqlsrv_errors(), true));
    }

    // Execute a separate query to fetch the last inserted BookingID
    $bookingIDQuery = "SELECT TOP 1 BookingID FROM Booking WHERE CustomerID = ? ORDER BY BookingID DESC";
    $params = [$customerId];
    $stmt = sqlsrv_query($conn, $bookingIDQuery, $params);

    if ($stmt === false) {
        die("Failed to retrieve BookingID: " . print_r(sqlsrv_errors(), true));
    }

    $newBookingID = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['BookingID'];

    if (!$newBookingID) {
        die("Failed to retrieve booking ID.");
    }

    // Redirect to confirmation page
    header("Location: confirmation4.php?booking_id=" . $newBookingID);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
    <link rel="stylesheet" href="s/str.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <main class="booking-page">
        <div class="container">
            <h1>Confirm Booking</h1>
            <form method="POST">
                <input type="hidden" name="room_ids" value="<?= htmlspecialchars($roomIDs) ?>">

                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" required>

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" required>

                <p><strong>Total Rooms Selected:</strong> <?= count($selectedRooms) ?></p>
                <p><strong>Estimated Total Price:</strong> RM<?= $totalBasePrice ?> per day</p>

                <button type="submit">Confirm Booking</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TSC Hotel. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>