<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: loginpage.php");
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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking Home</title>
    <link rel="stylesheet" href="s/styles.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>

    <section class="hero">
        <div class="container">
            <h1>Welcome to TSC Hotel</h1>
            <p>Your comfort is our priority. Book your perfect stay with us!</p>
            <a href="room4.php" class="btn">Explore Rooms</a>
        </div>
    </section>

    <section class="featured-rooms">
        <div class="container">
            <h2>Featured Rooms</h2>
            <div class="room-grid">
                <div class='room'>
                    <img src='s/deluxe_r.jpg' alt='Room Image'>
                    <h3>Deluxe Room</h3>
                    <p>A spacious room with all modern amenities.</p>
                    <p class='price'>$250/night</p>
                </div>
                <div class='room'>
                    <img src='s/family_r.jpeg' alt='Room Image'>
                    <h3>Family Room</h3>
                    <p>Comfortable and affordable accommodation.</p>
                    <p class='price'>$400/night</p>
                </div>
                <div class='room'>
                    <img src='s/suite_r.jpg' alt='Room Image'>
                    <h3>Suite</h3>
                    <p>Luxurious suite with premium facilities.</p>
                    <p class='price'>$700/night</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TSC Hotel. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>