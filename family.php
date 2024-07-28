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

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($is_admin) {
        // Handle Create and Update actions
        if ($action == 'create' || $action == 'update') {
            $name = $conn->real_escape_string($_POST['name']);
            $surname = $conn->real_escape_string($_POST['surname']);
            $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
            $house_no = $conn->real_escape_string($_POST['house_no']);
            $home_unit = $conn->real_escape_string($_POST['home_unit']);
            $employee_id = (int)$_POST['employee_id'];
            $religion = $conn->real_escape_string($_POST['religion']);
            $nationality = $conn->real_escape_string($_POST['nationality']);
            $ethnicity = $conn->real_escape_string($_POST['ethnicity']);

            if ($action == 'create') {
                $stmt = $conn->prepare("INSERT INTO family (name, surname, date_of_birth, house_no, home_unit, employee_id, religion, nationality, ethnicity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssissss", $name, $surname, $date_of_birth, $house_no, $home_unit, $employee_id, $religion, $nationality, $ethnicity);
            } else if ($action == 'update') {
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("UPDATE family SET name=?, surname=?, date_of_birth=?, house_no=?, home_unit=?, employee_id=?, religion=?, nationality=?, ethnicity=? WHERE id=?");
                $stmt->bind_param("ssssissssi", $name, $surname, $date_of_birth, $house_no, $home_unit, $employee_id, $religion, $nationality, $ethnicity, $id);
            }

            if ($stmt->execute()) {
                $message = $action == 'create' ? "New family record created successfully" : "Family record updated successfully";
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
                $stmt = $conn->prepare("DELETE FROM family WHERE id=?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $message = "Family record deleted successfully";
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

// Fetch family members
$query = "SELECT f.*, e.name AS employee_name, e.surname AS employee_surname FROM family f LEFT JOIN employee e ON f.employee_id = e.employee_id";
$family = $conn->query($query);

// Fetch employees for the dropdown
$query_employees = "SELECT employee_id, name, surname FROM employee";
$employees = $conn->query($query_employees);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Management</title>
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
        <h1 class="mb-4 text-center">Family Management</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#familyModal" onclick="openModal('create')">
                Add Family Member
            </button>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Date of Birth</th>
                        <th>House No</th>
                        <th>Home Unit</th>
                        <th>Employee</th>
                        <th>Religion</th>
                        <th>Nationality</th>
                        <th>Ethnicity</th>
                        <?php if ($is_admin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $family->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['id']); ?></td>
                            <td><?php echo htmlspecialchars($member['name']); ?></td>
                            <td><?php echo htmlspecialchars($member['surname']); ?></td>
                            <td><?php echo htmlspecialchars($member['date_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($member['house_no']); ?></td>
                            <td><?php echo htmlspecialchars($member['home_unit']); ?></td>
                            <td><?php echo htmlspecialchars($member['employee_name'] . ' ' . $member['employee_surname']); ?></td>
                            <td><?php echo htmlspecialchars($member['religion']); ?></td>
                            <td><?php echo htmlspecialchars($member['nationality']); ?></td>
                            <td><?php echo htmlspecialchars($member['ethnicity']); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#familyModal" onclick='openModal("update", <?php echo json_encode($member); ?>)'>
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick='openDeleteModal(<?php echo $member['id']; ?>)'>
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

    <!-- Add/Edit Family Modal -->
    <div class="modal fade" id="familyModal" tabindex="-1" role="dialog" aria-labelledby="familyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="familyModalLabel">Add Family Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="family-id">
                        <input type="hidden" name="action" id="family-action" value="create">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" id="surname" name="surname" required>
                        </div>
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="form-group">
                            <label for="house_no">House No</label>
                            <input type="text" class="form-control" id="house_no" name="house_no">
                        </div>
                        <div class="form-group">
                            <label for="home_unit">Home Unit</label>
                            <input type="text" class="form-control" id="home_unit" name="home_unit">
                        </div>
                        <div class="form-group">
                            <label for="employee_id">Employee</label>
                            <select class="form-control" id="employee_id" name="employee_id" required>
                                <?php while ($employee = $employees->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($employee['employee_id']); ?>"><?php echo htmlspecialchars($employee['name'] . ' ' . $employee['surname']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="religion">Religion</label>
                            <input type="text" class="form-control" id="religion" name="religion">
                        </div>
                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <input type="text" class="form-control" id="nationality" name="nationality">
                        </div>
                        <div class="form-group">
                            <label for="ethnicity">Ethnicity</label>
                            <input type="text" class="form-control" id="ethnicity" name="ethnicity">
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Family Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this family member?</p>
                        <input type="hidden" name="id" id="delete-id">
                        <input type="hidden" name="action" value="delete">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(action, family = null) {
            document.getElementById('family-action').value = action;
            document.getElementById('familyModalLabel').innerText = action === 'create' ? 'Add Family Member' : 'Edit Family Member';
            document.getElementById('family-id').value = family ? family.id : '';
            document.getElementById('name').value = family ? family.name : '';
            document.getElementById('surname').value = family ? family.surname : '';
            document.getElementById('date_of_birth').value = family ? family.date_of_birth : '';
            document.getElementById('house_no').value = family ? family.house_no : '';
            document.getElementById('home_unit').value = family ? family.home_unit : '';
            document.getElementById('employee_id').value = family ? family.employee_id : '';
            document.getElementById('religion').value = family ? family.religion : '';
            document.getElementById('nationality').value = family ? family.nationality : '';
            document.getElementById('ethnicity').value = family ? family.ethnicity : '';
        }

        function openDeleteModal(id) {
            document.getElementById('delete-id').value = id;
        }
    </script>
</body>
</html>
