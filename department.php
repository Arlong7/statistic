<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

include 'dbconnection.php'; // Include database connection script

$search_query = '';
$search_term = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($is_admin) {
        // Handle Create and Update actions
        if ($action == 'create' || $action == 'update') {
            $name = $conn->real_escape_string($_POST['name']);
            $location = $conn->real_escape_string($_POST['location']);

            if ($action == 'create') {
                $stmt = $conn->prepare("INSERT INTO department (name, location) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $location);
            } else if ($action == 'update') {
                $dep_id = (int)$_POST['dep_id'];
                $stmt = $conn->prepare("UPDATE department SET name=?, location=? WHERE dep_id=?");
                $stmt->bind_param("ssi", $name, $location, $dep_id);
            }

            if ($stmt->execute()) {
                $message = $action == 'create' ? "New department record created successfully" : "Department record updated successfully";
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }

        // Handle Delete action
        if ($action == 'delete') {
            if (isset($_POST['dep_id'])) {
                $dep_id = (int)$_POST['dep_id'];
                $stmt = $conn->prepare("DELETE FROM department WHERE dep_id=?");
                $stmt->bind_param("i", $dep_id);
                if ($stmt->execute()) {
                    $message = "Department record deleted successfully";
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                } else {
                    $message = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "No ID specified for deletion.";
            }
        }
    }
}

// Fetch departments
$query = "SELECT * FROM department";
$departments = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1;
        }
        .search-input {
            max-width: 250px;
        }
    </style>
</head>
<body>
    <?php include("nav.php"); ?>

    <div class="container mt-5 mx-auto">
        <h1 class="mb-4 text-center">Department Management</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#departmentModal" onclick="openModal('create')">
                Add Department
            </button>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>dep_id</th>
                        <th>Name</th>
                        <th>Location</th>
                        <?php if ($is_admin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($department = $departments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($department['dep_id']); ?></td>
                            <td><?php echo htmlspecialchars($department['name']); ?></td>
                            <td><?php echo htmlspecialchars($department['location']); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#departmentModal" onclick='openModal("update", <?php echo json_encode($department); ?>)'>
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick='openDeleteModal(<?php echo $department['dep_id']; ?>)'>
                                        Delete
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Department Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="departmentModalLabel">Department Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="departmentForm" method="POST">
                        <input type="hidden" name="action" id="departmentAction">
                        <input type="hidden" name="dep_id" id="departmentId">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" name="location" id="location" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <?php if ($is_admin): ?>
                        <button type="submit" class="btn btn-primary" form="departmentForm">Save</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this department?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="dep_id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle modal functionality -->
    <script>
        function openModal(action, department = null) {
            if (action === 'update') {
                $('#departmentModalLabel').text('Edit Department');
                $('#departmentAction').val('update');
                $('#departmentId').val(department.dep_id);
                $('#name').val(department.name);
                $('#location').val(department.location);
            } else {
                $('#departmentModalLabel').text('Add Department');
                $('#departmentAction').val('create');
                $('#departmentId').val('');
                $('#name').val('');
                $('#location').val('');
            }
        }

        function openDeleteModal(id) {
            $('#deleteId').val(id);
        }
    </script>
</body>
</html>
