<?php
session_start();
require_once 'db_con.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT CustomerID, FirstName, password FROM Customer WHERE CustomerEmail = ?";
        $params = [$username];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            $error = "An error occurred. Please try again later.";
        } else {
            if (sqlsrv_has_rows($stmt)) {
                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

                if ($password === $row['password']) {
                    $_SESSION['user_id'] = $row['CustomerID'];
                    $_SESSION['username'] = $username;
                    $_SESSION['FirstName'] = $row['FirstName'];
                    header("Location: Homepage.php");
                    exit;
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No user found with that email address.";
            }

            sqlsrv_free_stmt($stmt);
        }
    } else {
        $error = "Both fields are required.";
    }
}

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="s/sl.css">
</head>

<body>
    <div class="login-page">
        <div class="login-container">
            <form method="POST" action="">
                <h1>Login</h1>
                <?php if (!empty($error)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <div class="input-group">
                    <label for="username">Email</label>
                    <input type="email" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
            <a class="register" href="Registerpage.php">Click here for Register</a>
        </div>
    </div>
</body>

</html>