<?php
session_start();
require_once 'db_con.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.php");
    exit;
}

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

$customerID = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = intval($_POST['rating']);
    $comment = htmlspecialchars($_POST['comment']);

    $sql = "INSERT INTO Review (customerID, Rating, Comment) VALUES (?, ?, ?)";
    $params = [$customerID, $rating, $comment];

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt && sqlsrv_execute($stmt)) {
        // Redirect with a success message
        header("Location: reviewpage.php?success=Review submitted successfully!");
        exit;
    } else {
        $errorMessage = "Error submitting Review: " . print_r(sqlsrv_errors(), true);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="s/ps.css">
    <title>Review</title>
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <div class="profile-container">
        <h1>Review</h1>
        <?php if (isset($successMessage)) echo "<p style='color: green;'>$successMessage</p>"; ?>
        <?php if (isset($errorMessage)) echo "<p style='color: red;'>$errorMessage</p>"; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label for="rating">Rating (1-5):</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required>
            </div>
            <div class="input-group">
                <label for="comment">Comment:</label>
                <textarea id="comment" name="comment" rows="4" maxlength="100" required></textarea>
            </div>
            <button type="submit" class="update-button">Submit Review</button>
        </form>
    </div>

    <?php displayFooter(); ?>
</body>

</html>