<?php
// Include the database connection file
require_once 'db_con.php';

// SQL query to fetch data
$sql = "SELECT * FROM customer"; // Replace with your table name
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

// Display the data
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    print_r($row); // Or process your rows as needed
}

// Free the statement and close the connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
