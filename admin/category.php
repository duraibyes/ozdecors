<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}

include 'config.php';
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
// Fetch categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

// Initialize variables for editing
$edit_category_id = "";
$edit_category_name = "";
$edit_category_status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submissions for both add and edit
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            $category_name = $_POST['category_name'];
            $status = isset($_POST['status']) ? '1' : '0';

            $sql = "INSERT INTO categories (name, status) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("si", $category_name, $status);

            if ($stmt->execute()) {
                // Redirect to prevent form resubmission on refresh
                header("Location: category.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $stmt->close();
        } elseif ($action == 'edit') {
            $edit_category_id = $_POST['edit_category_id'];
            $edit_category_name = $_POST['category_name'];
            $edit_category_status = isset($_POST['status']) ? 1 : 0;

            $sql = "UPDATE categories SET name = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii", $edit_category_name, $edit_category_status, $edit_category_id);

            if ($stmt->execute()) {
                // Redirect to prevent form resubmission on refresh
                header("Location: category.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <div class="d-flex align-items-center">
                <h1>Category Management &nbsp;</h1> 
                <a href="images.php" class="h3"> / Image Management</a>
            </div>
            <div>
                <form action="category.php" method="post" style="text-align: right;">
                    <button type="submit" class="btn btn-danger" name="logout">Logout</button>
                </form>
            </div>
        </div>
        <button class="btn btn-success mb-3" onclick="openModal('add')">Add Category</button>
        <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="categoryForm" action="category.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="action" name="action" value="add">
                            <input type="hidden" id="edit_category_id" name="edit_category_id">
                            <div class="form-group">
                                <label for="category_name">Category Name:</label>
                                <input type="text" id="category_name" name="category_name" class="form-control" required>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status">
                                <label class="form-check-label" for="status">Status</label>
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
        <table id="categoryTable" class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
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
                            <td>" . $row["name"]. "</td>
                            <td>" . ($row["status"] ? 'Active' : 'Inactive') . "</td>
                            <td>
                                <button class='btn btn-warning btn-sm' onclick='openModal(\"edit\", " . $row["id"] . ", \"" . $row["name"] . "\", " . $row["status"] . ")'>Edit</button>
                            </td>
                        </tr>";
                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='4'>No categories found</td></tr>";
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
                    Are you sure you want to delete this category?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="delete_category.php" method="post">
                        <input type="hidden" id="delete_category_id" name="category_id">
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
            $('#categoryTable').DataTable();
        });
        function openModal(mode, id = null, name = null, status = null) {
            if (mode === 'edit') {
                $('#categoryModalLabel').text('Edit Category');
                $('#action').val('edit');
                $('#edit_category_id').val(id);
                $('#category_name').val(name);
                $('#status').prop('checked', status);
            } else {
                $('#categoryModalLabel').text('Add Category');
                $('#action').val('add');
                $('#edit_category_id').val('');
                $('#category_name').val('');
                $('#status').prop('checked', false);
            }
            $('#categoryModal').modal('show');
        }

        function confirmDelete(id) {
            $('#delete_category_id').val(id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
