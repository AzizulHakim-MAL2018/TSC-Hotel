<?php
session_start();
require_once 'db_con.php';

if (!isset($_SESSION['user_id'])) {
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

// Function to display the footer
function displayFooter()
{
    echo "<footer class='footer'>";
    echo "<div class='container'>";
    echo "<p>&copy; 2024 TSC Hotel. All rights reserved.</p>";
    echo "</div>";
    echo "</footer>";
}

// Fetch and process user data...
$customerID = $_SESSION['user_id'];
$error = "";
$success = "";
   
// Fetch user details
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT CustomerID, FirstName, LastName, customerEmail, customerPhoneNumb FROM Customer WHERE CustomerID = ?";
        $params = [$customerID];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die("Error fetching user data: " . print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($stmt)) {
            $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            die("User not found.");
        }
    }

    // Update user details
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = $_POST['firstName'] ?? null;
        $lastName = $_POST['lastName'] ?? null;
        $email = $_POST['customerEmail'] ?? null;
        $password = $_POST['password'] ?? null;
        $phone = $_POST['customerPhoneNumb'] ?? null;

        $sql = "{CALL UpdateCustomer(?, ?, ?, ?, ?, ?)}";
        $params = [
            $customerID,
            $firstName,
            $lastName,
            $email,
            $password,
            $phone
        ];

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            $error = "Error updating user data: " . print_r(sqlsrv_errors(), true);
        } else {
            $success = "Profile updated successfully.";
            // Refresh user details
            header("Location: profilepage.php");
            exit;
        }
    }

    sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="s/ps.css">
</head>

<body>
    <?php displayHeader($_SESSION['username'], $_SESSION['FirstName']); ?>
    <main>
        <div class="profile-container">
            <h1>Profile</h1>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form method="POST" action="profilepage.php">
                <div class="input-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['FirstName'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['LastName'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <label for="customerEmail">Email</label>
                    <input type="email" id="customerEmail" name="customerEmail" value="<?php echo htmlspecialchars($user['customerEmail'] ?? ''); ?>">
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a new password">
                </div>
                <div class="input-group">
                    <label for="customerPhoneNumb">Phone Number</label>
                    <input type="text" id="customerPhoneNumb" name="customerPhoneNumb" value="<?php echo htmlspecialchars($user['customerPhoneNumb'] ?? ''); ?>">
                </div>
                <button type="submit" class="update-button">Update</button>
            </form>
            <p class="back-home"><a href="Homepage.php">Back to Homepage</a></p>
        </div> 
    </main>
    <?php displayFooter(); ?>
</body>

</html>