<?php
// File: view_booking.php
session_start();
require_once 'db_con.php';

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

function displayFooter()
{
    echo "<footer class='footer'>";
    echo "<div class='container'>";
    echo "<p>&copy; 2024 TSC Hotel. All rights reserved.</p>";
    echo "</div>";
    echo "</footer>";
}

$userId = $_SESSION['user_id'];

try {
    $query = "EXEC ViewBooking @CustomerID = ?";
    $stmt = sqlsrv_query($conn, $query, [$userId]);

    if ($stmt === false) {
        throw new Exception("Failed to execute query: " . print_r(sqlsrv_errors(), true));
    }

    $bookings = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['BookingID']) && $row['BookingID'] !== 'N/A') {
            $bookings[] = $row;
        }
    }
} catch (Exception $e) {
    die("Error fetching bookings: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_booking_id'])) {
    $bookingIdToDelete = $_POST['delete_booking_id'];

    try {
        $deleteQuery = "EXEC DeleteBooking @BookingID = ?";
        $deleteStmt = sqlsrv_query($conn, $deleteQuery, [$bookingIdToDelete]);

        if ($deleteStmt === false) {
            throw new Exception("Failed to delete booking: " . print_r(sqlsrv_errors(), true));
        }

        header("Location: bookingrecord.php");
        exit;
    } catch (Exception $e) {
        die("Error deleting booking: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Bookings</title>
    <link rel="stylesheet" href="s/s2.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>
    <main>
        <h1>Your Bookings</h1>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Rooms</th>
                    <th>Status</th>
                    <th>amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['BookingID']); ?></td>
                        <td><?= isset($booking['StartDate']) ? htmlspecialchars($booking['StartDate']->format('Y-m-d')) : 'N/A'; ?></td>
                        <td><?= isset($booking['EndDate']) ? htmlspecialchars($booking['EndDate']->format('Y-m-d')) : 'N/A'; ?></td>
                        <td><?= htmlspecialchars($booking['RoomNumbers'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($booking['Status'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($booking['Amount'] ?? 'N/A'); ?></td>
                        <td>
                            <a class="r_button" href="editbooking.php?booking_id=<?= htmlspecialchars($booking['BookingID']); ?>">Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_booking_id" value="<?= htmlspecialchars($booking['BookingID']); ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <?php displayFooter(); ?>
</body>

</html>