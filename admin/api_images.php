<?php

// Allow requests from specific origins
header("Access-Control-Allow-Origin: *");
// Allow specific methods (e.g., GET, POST)
header("Access-Control-Allow-Methods: GET, POST");
// Allow specific headers (e.g., Content-Type, Authorization)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'config.php'; // Ensure this file has your database connection

// Query to fetch images from database
$category_id = $_GET['category_id'];
if($category_id) {

    $sql = "SELECT id, file_name, img_path FROM images where category_id = $category_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $images = array();
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        echo json_encode($images);
    } else {
        echo json_encode(array()); // Return empty array if no images found
    }
}

$conn->close();
?>
