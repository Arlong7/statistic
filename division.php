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
            $division_name = $conn->real_escape_string($_POST['division_name']);

            if ($action == 'create') {
                $stmt = $conn->prepare("INSERT INTO division (division_name) VALUES (?)");
                $stmt->bind_param("s", $division_name);
            } else if ($action == 'update') {
                $division_id = (int)$_POST['division_id'];
                $stmt = $conn->prepare("UPDATE division SET division_name=? WHERE division_id=?");
                $stmt->bind_param("si", $division_name, $division_id);
            }

            if ($stmt->execute()) {
                $message = $action == 'create' ? "New division record created successfully" : "Division record updated successfully";
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }

        // Handle Delete action
        if ($action == 'delete') {
            if (isset($_POST['division_id'])) {
                $division_id = (int)$_POST['division_id'];
                $stmt = $conn->prepare("DELETE FROM division WHERE division_id=?");
                $stmt->bind_param("i", $division_id);
                if ($stmt->execute()) {
                    $message = "Division record deleted successfully";
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

// Fetch divisions
$query = "SELECT * FROM division";
$divisions = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Division Management</title>
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
        <h1 class="mb-4 text-center">Division Management</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#divisionModal" onclick="openModal('create')">
                Add Division
            </button>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Division ID</th>
                        <th>Division Name</th>
                        <?php if ($is_admin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($division = $divisions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($division['division_id']); ?></td>
                            <td><?php echo htmlspecialchars($division['division_name']); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#divisionModal" onclick='openModal("update", <?php echo json_encode($division); ?>)'>
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick='openDeleteModal(<?php echo $division['division_id']; ?>)'>
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

    <!-- Add/Edit Division Modal -->
    <div class="modal fade" id="divisionModal" tabindex="-1" aria-labelledby="divisionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="divisionModalLabel">Division Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="divisionForm" method="POST">
                        <input type="hidden" name="action" id="divisionAction">
                        <input type="hidden" name="division_id" id="divisionId">
                        <div class="form-group">
                            <label for="division_name">Division Name</label>
                            <input type="text" class="form-control" name="division_name" id="division_name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <?php if ($is_admin): ?>
                        <button type="submit" class="btn btn-primary" form="divisionForm">Save</button>
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
                    <p>Are you sure you want to delete this division?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="division_id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle modal functionality -->
    <script>
        function openModal(action, division = null) {
            if (action === 'update') {
                $('#divisionModalLabel').text('Edit Division');
                $('#divisionAction').val('update');
                $('#divisionId').val(division.division_id);
                $('#division_name').val(division.division_name);
            } else {
                $('#divisionModalLabel').text('Add Division');
                $('#divisionAction').val('create');
                $('#divisionId').val('');
                $('#division_name').val('');
            }
        }

        function openDeleteModal(id) {
            $('#deleteId').val(id);
        }
    </script>
</body>
</html>
