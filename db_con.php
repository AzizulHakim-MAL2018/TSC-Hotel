<?php
// Database connection details
$serverName = "localhost,1433"; // Update server and port if different
$connectionOptions = [
    "Database" => "DB_TSCHotel", // Replace with your database name
    "UID" => "sa",              // Replace with your SQL Server username
    "PWD" => "C0mp2001!",       // Replace with your SQL Server password
    "CharacterSet" => "UTF-8"
];

// Establish a connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

