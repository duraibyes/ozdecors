<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image_id'])) {
    $image_id = $_POST['image_id'];

    // Delete image from database
    $sql = "DELETE FROM images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $image_id);

    if ($stmt->execute()) {
        // Deletion successful
        header("Location: images.php");
        exit();
    } else {
        // Error in SQL execution
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
