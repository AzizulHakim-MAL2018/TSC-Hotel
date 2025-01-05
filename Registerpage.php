<?php
session_start();
require_once 'db_con.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password) && !empty($phone)) {
        $sql = "{CALL InsertCustomer(?, ?, ?, ?, ?)}";
        $params = [$firstName, $lastName, $email, $password, $phone];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            $error = "An error occurred. Please try again later.";
        } else {
            header("Location: Loginpage.php");
            exit;
        }

        if ($stmt) {
            sqlsrv_free_stmt($stmt);
        }
    } else {
        $error = "All fields are required.";
    }
}

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="s/sl.css">
</head>

<body>
    <div class="register-container">
        <form method="POST" action="">
            <h1>Register</h1>
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <div class="input-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="input-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <button type="submit" class="register-button">Register</button>
        </form>
        <a class="register" href="Loginpage.php">Click here for login</a>
    </div>
</body>

</html>