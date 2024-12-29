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

// Fetch available rooms
$query = "
    SELECT 
        r.RoomID,
        r.RoomNumber, 
        r.RoomType, 
        r.Description, 
        r.Pax, 
        r.BasePrice, 
        r.RoomStatus, 
        STUFF((
            SELECT ', ' + a.AmenityName
            FROM RoomAmenity ra
            JOIN Amenity a ON ra.AmenityID = a.AmenityID
            WHERE ra.RoomID = r.RoomID
            FOR XML PATH(''), TYPE
        ).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS Amenities
    FROM Room r
    WHERE r.RoomStatus = 'available'
";
$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
    <link rel="stylesheet" href="s/str.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <main class="room-page">
        <div class="container">
            <h1>Available Rooms</h1>
            <form method="POST" action="booking4.php">
                <div class="room-list">
                    <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)): ?>
                        <div class="room-card">
                            <label>
                                <input type="checkbox" name="selected_rooms[]" value="<?= $row['RoomID'] ?>">
                                Room <?= htmlspecialchars($row['RoomNumber']) ?> (<?= htmlspecialchars($row['RoomType']) ?>)
                            </label>
                            <p><strong>Description:</strong> <?= htmlspecialchars($row['Description']) ?></p>
                            <p><strong>Pax:</strong> <?= htmlspecialchars($row['Pax']) ?></p>
                            <p><strong>Base Price:</strong> RM<?= htmlspecialchars($row['BasePrice']) ?></p>
                            <p><strong>Amenities:</strong> <?= htmlspecialchars($row['Amenities']) ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
                <button type="submit">Book Selected Rooms</button>
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