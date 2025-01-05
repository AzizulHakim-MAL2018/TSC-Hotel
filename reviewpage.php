<?php
session_start();
require_once 'db_con.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.php");
    exit;
}

// Functions to display the header and footer
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

// Fetch reviews from the database
$sql = "SELECT customerID, Rating, Comment, CreatedAt FROM Review ORDER BY CreatedAt DESC";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="s/ps.css">
    <title>View Reviews</title>
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <div class="profile-container">
        <h1>Customer Reviews</h1>
        <div style="text-align: right; margin-bottom: 15px;">
            <a href="addreviewpage.php" class="update-button" style="display: inline-block; text-align: center; width: auto;">Add Review</a>
        </div>
        <div class="reviews">
            <?php
            // Check if there are any reviews
            if (sqlsrv_has_rows($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<div class='review'>";
                    echo "<p><strong>Customer ID:</strong> " . htmlspecialchars($row['customerID']) . "</p>";
                    echo "<p><strong>Rating:</strong> " . htmlspecialchars($row['Rating']) . " / 5</p>";
                    echo "<p><strong>Comment:</strong> " . htmlspecialchars($row['Comment']) . "</p>";
                    echo "<p><small><strong>Date:</strong> " . $row['CreatedAt']->format('Y-m-d H:i:s') . "</small></p>";
                    echo "<hr>";
                    echo "</div>";
                }
            } else {
                echo "<p>No reviews available.</p>";
            }
            ?>
        </div>
    </div>

    <?php displayFooter(); ?>
</body>

</html>