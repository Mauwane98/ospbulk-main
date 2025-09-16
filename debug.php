<?php
require_once 'config/db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";

// Check categories table
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

echo "<h2>Categories</h2>";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. "<br>";
    }
} else {
    echo "0 results in categories table<br>";
}

// Check products table
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

echo "<h2>Products</h2>";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. "<br>";
    }
} else {
    echo "0 results in products table<br>
";
}

// Check posts table
$sql = "SELECT * FROM posts";
$result = $conn->query($sql);

echo "<h2>Posts</h2>";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Title: " . $row["title"]. "<br>";
    }
} else {
    echo "0 results in posts table<br>";
}

// Check events table
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

echo "<h2>Events</h2>";
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Title: " . $row["title"]. "<br>
";
    }
} else {
    echo "0 results in events table<br>";
}

$conn->close();
?>