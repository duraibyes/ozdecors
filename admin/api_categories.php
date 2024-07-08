<?php

// Allow requests from specific origins
header("Access-Control-Allow-Origin: *");
// Allow specific methods (e.g., GET, POST)
header("Access-Control-Allow-Methods: GET, POST");
// Allow specific headers (e.g., Content-Type, Authorization)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'config.php'; // Ensure this file has your database connection

// Query to fetch categories from database
$sql = "SELECT id, name, status FROM categories";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $categories = array();
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    echo json_encode($categories);
} else {
    echo json_encode(array()); // Return empty array if no categories found
}

$conn->close();
?>
