<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$is_user = isset($_SESSION['role']) && $_SESSION['role'] === 'user';

include 'dbconnection.php'; // Include database connection script

$search_query = '';
$search_term = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($is_admin) {
        // Handle Create and Update actions
        if ($action == 'create' || $action == 'update') {
            $name = $conn->real_escape_string($_POST['name']);
            $surname = $conn->real_escape_string($_POST['surname']);
            $gender = $conn->real_escape_string($_POST['gender']);
            $age = (int)$_POST['age'];
            $dob = $conn->real_escape_string($_POST['dob']);
            $address = $conn->real_escape_string($_POST['address']);
            $phonenumber = $conn->real_escape_string($_POST['phonenumber']);
            $status = $conn->real_escape_string($_POST['status']);
            $email = $conn->real_escape_string($_POST['email']);

            if ($action == 'create') {
                $stmt = $conn->prepare("INSERT INTO Employee (Name, Surname, Gender, Age, DateOfBirth, Address, PhoneNumber, Status, Email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssisssss", $name, $surname, $gender, $age, $dob, $address, $phonenumber, $status, $email);
            } else if ($action == 'update') {
                $id = (int)$_POST['E_ID'];
                $stmt = $conn->prepare("UPDATE Employee SET Name=?, Surname=?, Gender=?, Age=?, DateOfBirth=?, Address=?, PhoneNumber=?, Status=?, Email=? WHERE E_ID=?");
                $stmt->bind_param("sssisssssi", $name, $surname, $gender, $age, $dob, $address, $phonenumber, $status, $email, $id);
            }

            if ($stmt->execute()) {
                $message = $action == 'create' ? "New employee record created successfully" : "Employee record updated successfully";
                header("Location: {$_SERVER['PHP_SELF']}");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }

        // Handle Delete action
        if ($action == 'delete') {
            if (isset($_POST['E_ID'])) {
                $id = (int)$_POST['E_ID'];
                $stmt = $conn->prepare("DELETE FROM Employee WHERE E_ID=?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $message = "Employee record deleted successfully";
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

        // Handle Search functionality for Admins
        if (isset($_POST['search'])) {
            $search_term = $conn->real_escape_string($_POST['search']);
            $search_query = " WHERE Name LIKE '%$search_term%' OR Surname LIKE '%$search_term%' OR Email LIKE '%$search_term%'";
        }
    }
}

$query = "SELECT * FROM Employee" . $search_query;
$employees = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການພະນັກງານ</title>
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
        <h1 class="mb-4 text-center">ຈັດການພະນັກງານ</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <!-- Search Form -->
            <form method="POST" class="mb-4">
                <div class="form-group">
                    <label for="search">ຄົ້ນຫາ</label>
                    <input type="text" class="form-control search-input" name="search" id="search" value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <button type="submit" class="btn btn-primary">ຄົ້ນຫາ</button>
            </form>
            
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#employeeModal" onclick="openModal('create')">
                ເພີ່ມພະນັກງານ
            </button>
        <?php endif; ?>

        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>E_ID</th>
                        <th>ຊື່</th>
                        <th>ນາມສະກຸນ</th>
                        <th>ເພດ</th>
                        <th>ອາຍຸ</th>
                        <th>ວັນເກີດ</th>
                        <th>ທີ່ຢູ່</th>
                        <th>ເບີໂທ</th>
                        <th>ສະຖານະ</th>
                        <th>ອີເມວ</th>
                        <?php if ($is_admin): ?>
                            <th>ກະບວນການ</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['E_ID']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Surname']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Gender']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Age']); ?></td>
                            <td><?php echo htmlspecialchars($employee['DateOfBirth']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Address']); ?></td>
                            <td><?php echo htmlspecialchars($employee['PhoneNumber']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Status']); ?></td>
                            <td><?php echo htmlspecialchars($employee['Email']); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#employeeModal" onclick='openModal("update", <?php echo json_encode($employee); ?>)'>
                                        ແກ້ໄຂ
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" onclick='openDeleteModal(<?php echo $employee['E_ID']; ?>)'>
                                        ລົບ
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">ແບບຟອມພະນັກງານ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="employeeForm" method="POST">
                        <input type="hidden" name="action" id="employeeAction">
                        <input type="hidden" name="E_ID" id="employeeId">
                        <div class="form-group">
                            <label for="name">ຊື່</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">ນາມສະກຸນ</label>
                            <input type="text" class="form-control" name="surname" id="surname" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">ເພດ</label>
                            <input type="text" class="form-control" name="gender" id="gender" required>
                        </div>
                        <div class="form-group">
                            <label for="age">ອາຍຸ</label>
                            <input type="number" class="form-control" name="age" id="age" required>
                        </div>
                        <div class="form-group">
                            <label for="dob">ວັນເກີດ</label>
                            <input type="date" class="form-control" lang="en" name="dob" id="dob" required>
                        </div>
                        <div class="form-group">
                            <label for="address">ທີ່ຢູ່</label>
                            <input type="text" class="form-control" name="address" id="address" required>
                        </div>
                        <div class="form-group">
                            <label for="phonenumber">ເບີໂທ</label>
                            <input type="text" class="form-control" name="phonenumber" id="phonenumber" required>
                        </div>
                        <div class="form-group">
                            <label for="status">ສະຖານະ</label>
                            <input type="text" class="form-control" name="status" id="status" required>
                        </div>
                        <div class="form-group">
                            <label for="email">ອີເມວ</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ປິດ</button>
                    <?php if ($is_admin): ?>
                        <button type="submit" class="btn btn-primary" form="employeeForm">ບັນທຶກ</button>
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
                    <h5 class="modal-title" id="deleteModalLabel">ຢືນຢັນການລົບ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>ທ່ານແນ່ໃຈບໍ່ວ່າຈະລົບພະນັກງານນີ້ບໍ?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="E_ID" id="deleteId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ປິດ</button>
                        <button type="submit" class="btn btn-danger">ລົບ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle modal functionality -->
    <script>
        function openModal(action, employee = null) {
            if (action === 'update') {
                $('#employeeModalLabel').text('ແກ້ໄຂພະນັກງານ');
                $('#employeeAction').val('update');
                $('#employeeId').val(employee.E_ID);
                $('#name').val(employee.Name);
                $('#surname').val(employee.Surname);
                $('#gender').val(employee.Gender);
                $('#age').val(employee.Age);
                $('#dob').val(employee.DateOfBirth);
                $('#address').val(employee.Address);
                $('#phonenumber').val(employee.PhoneNumber);
                $('#status').val(employee.Status);
                $('#email').val(employee.Email);
            } else {
                $('#employeeModalLabel').text('ເພີ່ມພະນັກງານ');
                $('#employeeAction').val('create');
                $('#employeeId').val('');
                $('#name').val('');
                $('#surname').val('');
                $('#gender').val('');
                $('#age').val('');
                $('#dob').val('');
                $('#address').val('');
                $('#phonenumber').val('');
                $('#status').val('');
                $('#email').val('');
            }
        }

        function openDeleteModal(id) {
            $('#deleteId').val(id);
        }
    </script>
</body>
</html>
