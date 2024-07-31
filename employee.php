
<?php
include 'dbconnection.php';
session_start();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'create') {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $date_of_birth = $_POST['date_of_birth'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];
        $status = $_POST['status'];
        $email = $_POST['email'];
        $village_id = $_POST['village_id'];
        $city_id = $_POST['city_id'];
        $province_id = $_POST['province_id'];
        $division_id = $_POST['division_id'];

        // Sanitize inputs to prevent SQL injection
        $name = $conn->real_escape_string($name);
        $surname = $conn->real_escape_string($surname);
        $gender = $conn->real_escape_string($gender);
        $age = $conn->real_escape_string($age);
        $date_of_birth = $conn->real_escape_string($date_of_birth);
        $address = $conn->real_escape_string($address);
        $phone_number = $conn->real_escape_string($phone_number);
        $status = $conn->real_escape_string($status);
        $email = $conn->real_escape_string($email);
        $village_id = $conn->real_escape_string($village_id);
        $city_id = $conn->real_escape_string($city_id);
        $province_id = $conn->real_escape_string($province_id);
        $division_id = $conn->real_escape_string($division_id);

        $sql = "INSERT INTO employee (name, surname, gender, age, date_of_birth, address, phone_number, status, email, village_id, city_id, province_id, division_id)
                VALUES ('$name', '$surname', '$gender', '$age', '$date_of_birth', '$address', '$phone_number', '$status', '$email', '$village_id', '$city_id', '$province_id', '$division_id')";

        if ($conn->query($sql) === TRUE) {
            echo "New employee created successfully";
        } else {
            http_response_code(500);
            echo "Error: " . $conn->error;
        }
    } elseif ($action == 'update') {
        $employee_id = $_POST['employee_id'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $date_of_birth = $_POST['date_of_birth'];
        $address = $_POST['address'];
        $phone_number = $_POST['phone_number'];
        $status = $_POST['status'];
        $email = $_POST['email'];
        $village_id = $_POST['village_id'];
        $city_id = $_POST['city_id'];
        $province_id = $_POST['province_id'];
        $division_id = $_POST['division_id'];

        // Sanitize inputs
        $employee_id = $conn->real_escape_string($employee_id);
        $name = $conn->real_escape_string($name);
        $surname = $conn->real_escape_string($surname);
        $gender = $conn->real_escape_string($gender);
        $age = $conn->real_escape_string($age);
        $date_of_birth = $conn->real_escape_string($date_of_birth);
        $address = $conn->real_escape_string($address);
        $phone_number = $conn->real_escape_string($phone_number);
        $status = $conn->real_escape_string($status);
        $email = $conn->real_escape_string($email);
        $village_id = $conn->real_escape_string($village_id);
        $city_id = $conn->real_escape_string($city_id);
        $province_id = $conn->real_escape_string($province_id);
        $division_id = $conn->real_escape_string($division_id);

        $sql = "UPDATE employee SET name='$name', surname='$surname', gender='$gender', age='$age', date_of_birth='$date_of_birth', address='$address', phone_number='$phone_number', status='$status', email='$email', village_id='$village_id', city_id='$city_id', province_id='$province_id', division_id='$division_id'
                WHERE employee_id='$employee_id'";

        if ($conn->query($sql) === TRUE) {
            echo "Employee updated successfully";
        } else {
            http_response_code(500);
            echo "Error: " . $conn->error;
        }
    } elseif ($action == 'delete') {
        $employee_id = $_POST['employee_id'];

        // Sanitize input
        $employee_id = $conn->real_escape_string($employee_id);

        // Disable foreign key checks, perform delete, re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=0;");
        $sql = "DELETE FROM employee WHERE employee_id='$employee_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Employee deleted successfully";
        } else {
            http_response_code(500);
            echo "Error: " . $conn->error;
        }
        $conn->query("SET FOREIGN_KEY_CHECKS=1;");
    }
}

// Fetch data for the select options
$village_result = $conn->query("SELECT id, name FROM villages");
$city_result = $conn->query("SELECT id, name FROM cities");
$province_result = $conn->query("SELECT id, name FROM provinces");
$department_result = $conn->query("SELECT division_id, division_name FROM division");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
       @media print {
            .container {
                width: 100%;
                padding: 0;
                margin: 0;
            }
            table {
                font-size: 0.6em; /* Even smaller font size */
                width: 100%; /* Full width */
                border-collapse: collapse; /* Collapse borders */
                margin: 0; /* Remove margin */
                padding: 0; /* Remove padding */
            }
            th, td {
                padding: 1px; /* Even reduced padding */
                border: 0.5px solid #ddd; /* Thin border for better visibility */
            }
            th {
                background-color: #f0f0f0; /* Lighter background for headers */
            }
            .no-print {
                display: none; /* Hide elements with class 'no-print' */
            }
            .btn {
                display: none; /* Hide buttons when printing */
            }
        }
    </style>
<body>
    <?php include('nav.php');?>
    <div class="container mt-5">
        <h2 class="mb-4">Employee Management</h2>
        <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">Add Employee</button>
        <button class="btn btn-secondary mb-4" onclick="printTable()">Print Table</button>

        <table class="table table-bordered" id="employeeTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Email</th>
                    <th>Village</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT e.*, v.name AS village_name, c.name AS city_name, p.name AS province_name, d.division_name AS division_name FROM employee e
                                        JOIN villages v ON e.village_id = v.id
                                        JOIN cities c ON e.city_id = c.id
                                        JOIN provinces p ON e.province_id = p.id
                                        JOIN division d ON e.division_id = d.division_id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['employee_id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['surname']}</td>";
                    echo "<td>{$row['gender']}</td>";
                    echo "<td>{$row['age']}</td>";
                    echo "<td>{$row['date_of_birth']}</td>";
                    echo "<td>{$row['address']}</td>";
                    echo "<td>{$row['phone_number']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['village_name']}</td>";
                    echo "<td>{$row['city_name']}</td>";
                    echo "<td>{$row['province_name']}</td>";
                    echo "<td>{$row['division_name']}</td>";
                    echo "<td>";
                    echo "<button class='btn btn-warning btn-sm' onclick='openEditModal(" . json_encode($row) . ")'>Edit</button>";
                    echo " <button class='btn btn-danger btn-sm' onclick='deleteEmployee({$row['employee_id']})'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addEmployeeForm">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="addName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addSurname" class="form-label">Surname</label>
                            <input type="text" class="form-control" id="addSurname" name="surname" required>
                        </div>
                        <div class="mb-3">
                            <label for="addGender" class="form-label">Gender</label>
                            <input type="text" class="form-control" id="addGender" name="gender" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAge" class="form-label">Age</label>
                            <input type="number" class="form-control" id="addAge" name="age" required>
                        </div>
                        <div class="mb-3">
                            <label for="addDateOfBirth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="addDateOfBirth" name="date_of_birth" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="addAddress" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="addPhoneNumber" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="addPhoneNumber" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="addStatus" class="form-label">Status</label>
                            <input type="text" class="form-control" id="addStatus" name="status" required>
                        </div>
                        <div class="mb-3">
                            <label for="addEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="addVillage" class="form-label">Village</label>
                            <select class="form-control" id="addVillage" name="village_id" required>
                                <?php while ($row = $village_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addCity" class="form-label">City</label>
                            <select class="form-control" id="addCity" name="city_id" required>
                                <?php while ($row = $city_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addProvince" class="form-label">Province</label>
                            <select class="form-control" id="addProvince" name="province_id" required>
                                <?php while ($row = $province_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addDepartment" class="form-label">Department</label>
                            <select class="form-control" id="addDepartment" name="division_id" required>
                                <?php while ($row = $department_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['division_id'] ?>"><?= $row['division_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEmployeeForm">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="editEmployeeId" name="employee_id">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSurname" class="form-label">Surname</label>
                            <input type="text" class="form-control" id="editSurname" name="surname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editGender" class="form-label">Gender</label>
                            <input type="text" class="form-control" id="editGender" name="gender" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAge" class="form-label">Age</label>
                            <input type="number" class="form-control" id="editAge" name="age" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDateOfBirth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="editDateOfBirth" name="date_of_birth" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="editAddress" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhoneNumber" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="editPhoneNumber" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <input type="text" class="form-control" id="editStatus" name="status" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editVillage" class="form-label">Village</label>
                            <select class="form-control" id="editVillage" name="village_id" required>
                                <?php
                                $village_result->data_seek(0); // Reset the pointer to the beginning
                                while ($row = $village_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editCity" class="form-label">City</label>
                            <select class="form-control" id="editCity" name="city_id" required>
                                <?php
                                $city_result->data_seek(0); // Reset the pointer to the beginning
                                while ($row = $city_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editProvince" class="form-label">Province</label>
                            <select class="form-control" id="editProvince" name="province_id" required>
                                <?php
                                $province_result->data_seek(0); // Reset the pointer to the beginning
                                while ($row = $province_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editDepartment" class="form-label">Department</label>
                            <select class="form-control" id="editDepartment" name="division_id" required>
                                <?php
                                $department_result->data_seek(0); // Reset the pointer to the beginning
                                while ($row = $department_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['division_id'] ?>"><?= $row['division_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        function openEditModal(employee) {
            document.getElementById('editEmployeeId').value = employee.employee_id;
            document.getElementById('editName').value = employee.name;
            document.getElementById('editSurname').value = employee.surname;
            document.getElementById('editGender').value = employee.gender;
            document.getElementById('editAge').value = employee.age;
            document.getElementById('editDateOfBirth').value = employee.date_of_birth;
            document.getElementById('editAddress').value = employee.address;
            document.getElementById('editPhoneNumber').value = employee.phone_number;
            document.getElementById('editStatus').value = employee.status;
            document.getElementById('editEmail').value = employee.email;
            document.getElementById('editVillage').value = employee.village_id;
            document.getElementById('editCity').value = employee.city_id;
            document.getElementById('editProvince').value = employee.province_id;
            document.getElementById('editDepartment').value = employee.division_id;
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
        function deleteEmployee(employeeId) {
    if (confirm('Are you sure you want to delete this employee?')) {
        var form = new FormData();
        form.append('action', 'delete');
        form.append('employee_id', employeeId);

        fetch('', {
            method: 'POST',
            body: form
        }).then(response => {
            // Check if the response is successful
            if (response.ok) {
                location.reload(); // Reload the page to reflect changes
            } else {
                console.error('Error:', response.statusText);
            }
        }).catch(error => {
            console.error('Error:', error);
        });
    }
}

        document.getElementById('addEmployeeForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = new FormData(this);

            fetch('', {
                method: 'POST',
                body: form
            }).then(response => response.text())
              .then(data => {
                  alert(data);
                  location.reload();
              }).catch(error => {
                  console.error('Error:', error);
              });
        });

        document.getElementById('editEmployeeForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var form = new FormData(this);

            fetch('', {
                method: 'POST',
                body: form
            }).then(response => response.text())
              .then(data => {
                  alert(data);
                  location.reload();
              }).catch(error => {
                  console.error('Error:', error);
              });
        });
        function printTable() {
            var printWindow = window.open('', '', 'height=600,width=800');
            var tableHtml = document.getElementById('employeeTable').outerHTML;
            printWindow.document.write('<html><head><title>Print Table</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
            printWindow.document.write('</head><body >');
            printWindow.document.write('<h1>Employee Table</h1>');
            printWindow.document.write(tableHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
</body>
</html>