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
            $city_id = (int)$_POST['city_id'];

            if ($action == 'create') {
                $stmt = $conn->prepare("INSERT INTO villages (name, city_id) VALUES (?, ?)");
                $stmt->bind_param("si", $name, $city_id);
            } else if ($action == 'update') {
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("UPDATE villages SET name=?, city_id=? WHERE id=?");
                $stmt->bind_param("sii", $name, $city_id, $id);
            }

            if ($stmt->execute()) {
                $message = $action == 'create' ? "New village record created successfully" : "Village record updated successfully";
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }

        // Handle Delete action
        if ($action == 'delete') {
            if (isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("DELETE FROM villages WHERE id=?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $message = "Village record deleted successfully";
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

// Fetch villages with city names
$query = "SELECT villages.id, villages.name, cities.name AS city_name 
          FROM villages 
          JOIN cities ON villages.city_id = cities.id";
$villages = $conn->query($query);

$query_city = "SELECT * FROM cities"; // Adjust if the table name for cities is different
$cities = $conn->query($query_city);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Village Management</title>
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
        <h1 class="mb-4 text-center">Village Management</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#villageModal" onclick="openModal('create')">
                Add Village
            </button>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Village ID</th>
                        <th>Village Name</th>
                        <th>City Name</th>
                        <?php if ($is_admin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($village = $villages->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($village['id']); ?></td>
                            <td><?php echo htmlspecialchars($village['name']); ?></td>
                            <td><?php echo htmlspecialchars($village['city_name']); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#villageModal" onclick='openModal("update", <?php echo json_encode($village); ?>)'>
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick='openDeleteModal(<?php echo $village['id']; ?>)'>
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

    <!-- Add/Edit Village Modal -->
    <div class="modal fade" id="villageModal" tabindex="-1" aria-labelledby="villageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="villageModalLabel">Village Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="villageForm" method="POST">
                        <input type="hidden" name="action" id="villageAction">
                        <input type="hidden" name="id" id="villageId">
                        <div class="form-group">
                            <label for="name">Village Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="city_id">City ID</label>
                            <select class="form-control" name="city_id" id="city_id" required>
                                <?php while ($city = $cities->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($city['id']); ?>">
                                        <?php echo htmlspecialchars($city['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <?php if ($is_admin): ?>
                        <button type="submit" class="btn btn-primary" form="villageForm">Save</button>
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
                    <p>Are you sure you want to delete this village?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle modal functionality -->
    <script>
        function openModal(action, village = null) {
            if (action === 'update') {
                $('#villageModalLabel').text('Edit Village');
                $('#villageAction').val('update');
                $('#villageId').val(village.id);
                $('#name').val(village.name);
                $('#city_id').val(village.city_id);
            } else {
                $('#villageModalLabel').text('Add Village');
                $('#villageAction').val('create');
                $('#villageId').val('');
                $('#name').val('');
                $('#city_id').val('');
            }
        }

        function openDeleteModal(id) {
            $('#deleteId').val(id);
        }
    </script>
</body>
</html>
