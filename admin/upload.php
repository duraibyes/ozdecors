<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "ozone"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $uploadDirectory = "uploads/";
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    $category = $_POST['category'];
    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $errors = [];

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $fileName = basename($_FILES['images']['name'][$key]);
        $fileSize = $_FILES['images']['size'][$key];
        $fileTmp = $_FILES['images']['tmp_name'][$key];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check if file type is allowed
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "$fileName is not a valid image file.";
            continue;
        }

        // Check file size
        if ($fileSize > $maxFileSize) {
            $errors[] = "$fileName exceeds the maximum file size of 2MB.";
            continue;
        }

        // Move uploaded file to the upload directory
        $targetFilePath = $uploadDirectory . $fileName;
        if (move_uploaded_file($fileTmp, $targetFilePath)) {
            // Insert file details into the database
            $uploadedOn = date("Y-m-d H:i:s");
            $sql = "INSERT INTO images (file_name, category) VALUES ('$fileName', '$category')";

            if ($conn->query($sql) === TRUE) {
                echo "$fileName has been uploaded and saved to the database successfully.<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $errors[] = "Failed to upload $fileName.";
        }
    }

    // Display errors if there are any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {
        header("Location: images.php");
        exit();
    }
}

$conn->close();
?>
