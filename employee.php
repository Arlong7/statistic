<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user's role is defined and is "admin"
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    // Admin can access this page, show content
    include 'dbconnection.php'; // Include database connection script
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'];
        
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
            } else {
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
    }
    
    $employees = $conn->query("SELECT * FROM Employee");
} else {
    // If user's role is not "admin", display message and deny access
    $message = "You do not have access to this page.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Management</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
  <?php include("nav.php"); ?>

  <div class="container mt-5 mx-auto">
    <h1 class="mb-4 text-center">ຈັດການພະນັກງານ</h1>

    <?php if (isset($message)): ?>
      <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?>">
        <?php echo $message; ?>
      </div>
    <?php else: ?>
      <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#employeeModal" onclick="openModal('create')">
        ເພີ່ມພະນັກງານ
      </button>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>E_ID</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Date of Birth</th>
            <th>Address</th>
            <th>Phone Number</th>
            <th>Status</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($employee = $employees->fetch_assoc()): ?>
            <tr>
              <td><?php echo $employee['E_ID']; ?></td>
              <td><?php echo $employee['Name']; ?></td>
              <td><?php echo $employee['Surname']; ?></td>
              <td><?php echo $employee['Gender']; ?></td>
              <td><?php echo $employee['Age']; ?></td>
              <td><?php echo $employee['DateOfBirth']; ?></td>
              <td><?php echo $employee['Address']; ?></td>
              <td><?php echo $employee['PhoneNumber']; ?></td>
              <td><?php echo $employee['Status']; ?></td>
              <td><?php echo $employee['Email']; ?></td>
              <td>
                <button class="btn btn-warning" data-toggle="modal" data-target="#employeeModal" onclick="openModal('update', <?php echo htmlspecialchars(json_encode($employee)); ?>)">Edit</button>
                <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal" onclick="openDeleteModal(<?php echo $employee['E_ID']; ?>)">Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- Employee Modal -->
  <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="employeeModalLabel">Employee Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="employeeForm" method="POST">
            <input type="hidden" name="action" id="employeeAction">
            <input type="hidden" name="E_ID" id="employeeId">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="form-group">
              <label for="surname">Surname</label>
              <input type="text" class="form-control" name="surname" id="surname" required>
            </div>
            <div class="form-group">
              <label for="gender">Gender</label>
              <input type="text" class="form-control" name="gender" id="gender" required>
            </div>
            <div class="form-group">
              <label for="age">Age</label>
              <input type="number" class="form-control" name="age" id="age" required>
            </div>
            <div class="form-group">
              <label for="dob" lang="en">Date of Birth</label>
              <input type="date" class="form-control" lang="en" name="dob" id="dob" required>
            </div>
            <div class="form-group">
              <label for="address">Address</label>
              <input type="text" class="form-control" name="address" id="address" required>
            </div>
            <div class="form-group">
              <label for="phonenumber">Phone Number</label>
              <input type="text" class="form-control" name="phonenumber" id="phonenumber" required>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <input type="text" class="form-control" name="status" id="status" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" form="employeeForm">Save changes</button>
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
          <p>Are you sure you want to delete this employee?</p>
        </div>
        <div class="modal-footer">
          <form id="deleteForm" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="E_ID" id="deleteId">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript to handle modal functionality -->
  <script>
    // Function to open employee modal for create or update
    function openModal(action, employee = null) {
      if (action === 'update') {
        $('#employeeModalLabel').text('Edit Employee');
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
        $('#employeeModalLabel').text('Add Employee');
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

    // Function to open delete modal and set employee ID
    function openDeleteModal(id) {
      $('#deleteId').val(id);
    }

    // Submit form handler for delete confirmation
    $('#deleteForm').on('submit', function(e) {
      e.preventDefault();
      var form = $(this);
      var url = form.attr('action');
      $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(data) {
          location.reload();
        }
      });
    });
  </script>

</body>
</html>
