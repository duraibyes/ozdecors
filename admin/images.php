<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}

// Logout logic
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
$max_files = 20;
// Set maximum upload file size
ini_set('upload_max_filesize', '50M'); // Adjust as needed

// Set maximum POST size
ini_set('post_max_size', '50M'); // Adjust as needed

// Set maximum number of files that can be uploaded in a single request
ini_set('max_file_uploads', 100); // Adjust as needed

// Fetch categories for dropdown
$sql = "SELECT id, name FROM categories";
$category_result = $conn->query($sql);

// Fetch images with category names
$sql = "SELECT i.id, i.file_name, i.img_path, i.category_id, c.name AS category_name FROM images i LEFT JOIN categories c ON i.category_id = c.id";
$result = $conn->query($sql);

// Initialize variables for editing
$edit_image_id = "";
$edit_image_name = "";
$edit_image_category_id = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add Image
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $category_id = $_POST['category_id'];

        // Array to store uploaded file details
        $uploaded_files = [];

         // Count total files
        $total_files = count($_FILES['image_name']['name']);

        // Check if total files exceed maximum limit
        if ($total_files > $max_files) {
            echo "Exceeded maximum limit of $max_files files.";
            exit();
        }

        // Loop through each file
        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['image_name']['name'][$i];
            $file_tmp = $_FILES['image_name']['tmp_name'][$i];

            // Upload directory (adjust as needed)
            $upload_directory = "uploads/";

            // Generate unique file name to avoid overwriting
            $upload_file = $upload_directory . uniqid() . '_' . $file_name;

            // Move uploaded file to specified directory
            if (move_uploaded_file($file_tmp, $upload_file)) {
                // File uploaded successfully, insert into database
                $sql = "INSERT INTO images (file_name, img_path, category_id) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $file_name, $upload_file, $category_id);

                if ($stmt->execute()) {
                    // Record inserted successfully
                    $uploaded_files[] = $file_name; // Track uploaded file names
                } else {
                    // Error in SQL execution
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                $stmt->close();
            } else {
                // Failed to move uploaded file
                echo "Error uploading file: " . $file_name;
            }
        }

        // Redirect to prevent form resubmission on refresh
        header("Location: images.php");
        exit();
    }

    // Edit Image
    elseif (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $edit_image_id = $_POST['edit_image_id'];
        $edit_image_name = $_POST['image_name'];
        $edit_image_category_id = $_POST['category_id'];

        // Update image details in database
        $sql = "UPDATE images SET file_name = ?, category_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $edit_image_name, $edit_image_category_id, $edit_image_id);

        if ($stmt->execute()) {
            // Redirect to prevent form resubmission on refresh
            header("Location: images.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="d-flex align-items-center">
                <h1>Image Management &nbsp;</h1> 
                <a href="category.php" class="h3"> / Category Management</a>
            </div>
            <div>
                <form action="images.php" method="post" style="text-align: right;">
                    <button type="submit" class="btn btn-danger" name="logout">Logout</button>
                </form>
            </div>
        </div>
        <button class="btn btn-success mb-3" onclick="openModal('add')">Add Image</button>
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="imageForm" action="images.php" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">Add Image</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="action" name="action" value="add">
                            <input type="hidden" id="edit_image_id" name="edit_image_id">
                            <div class="form-group">
                                <label for="image_name">Images:</label>
                                <input type="file" id="image_name" name="image_name[]" class="form-control-file" multiple required>
                                <div>
                                    <span class="text-danger">Maximum 20 files can upload.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category:</label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    if ($category_result->num_rows > 0) {
                                        while($row = $category_result->fetch_assoc()) {
                                            echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No categories found</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <table  id="imageTable" class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $i. "</td>
                            <td>" . $row["file_name"]. "</td>
                            <td>" . $row["category_name"]. "</td>
                            <td>
                                <button class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row["id"] . ")'>Delete</button>
                            </td>
                        </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='4'>No images found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this image?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="delete_image.php" method="post">
                        <input type="hidden" id="delete_image_id" name="image_id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>

$(document).ready(function() {
    $('#imageTable').DataTable();
});

        function openModal(mode, id = null, name = null, category_id = null) {
            if (mode === 'edit') {
                $('#imageModalLabel').text('Edit Image');
                $('#action').val('edit');
                $('#edit_image_id').val(id);
                // Since you're editing multiple images, handling initial input values might need more complex logic.
                // This example assumes single field updates, adjust as per your requirements.
            } else {
                $('#imageModalLabel').text('Add Image');
                $('#action').val('add');
                $('#edit_image_id').val('');
                $('#image_name').val('');
                $('#category_id').val('');
            }
            $('#imageModal').modal('show');
        }

        function confirmDelete(id) {
            $('#delete_image_id').val(id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
