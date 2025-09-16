<?php
/*
 * --------------------------------------------------------------------------
 * DATABASE CONFIGURATION
 * --------------------------------------------------------------------------
 *
 * This file contains the configuration for the database connection.
 * It is recommended to use environment variables for sensitive data.
 *
 */

// Database credentials
define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'osp_bulk_db');

/*
 * --------------------------------------------------------------------------
 * CREATE DATABASE CONNECTION
 * --------------------------------------------------------------------------
 *
 * The following code will create a new MySQLi connection.
 * If the connection fails, it will display an error message and exit.
 *
 */

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8mb4 for full Unicode support
if (!$conn->set_charset("utf8mb4")) {
    // If you need to handle this error, you can log it or die
    // printf("Error loading character set utf8mb4: %s\n", $conn->error);
}

/*
 * --------------------------------------------------------------------------
 * (Optional) Example of how to use the connection
 * --------------------------------------------------------------------------
 *
 * You can include this file in your other PHP scripts and use the $conn
 * variable to perform database operations.
 *
 * require_once 'config/db.php';
 *
 * $result = $conn->query("SELECT * FROM products");
 * while ($row = $result->fetch_assoc()) {
 *     // Process data
 * }
 * $conn->close();
 *
 */

?>
