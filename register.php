<?php
include 'dbconnection.php'; // Include your database connection file

$errors = [];

// Fetch employee data for the dropdown
$employees = [];
$employee_query = "SELECT employee_id, CONCAT(employee_id, ' - ', name) AS employee_info FROM employee";
$result = $conn->query($employee_query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_admin = isset($_POST['role_admin']) ? 1 : 0; // Checkbox for admin role
    $role_user = isset($_POST['role_user']) ? 1 : 0;   // Checkbox for user role
    $employee_id = isset($_POST['employee_id']) ? $conn->real_escape_string($_POST['employee_id']) : null;

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if ($role_user && empty($employee_id)) {
        $errors[] = "Employee ID is required for users.";
    }

    // Determine role and handle employee_id
    $role = $role_admin ? 'admin' : ($role_user ? 'user' : ''); // Default role is ''
    $employee_id = $role_admin ? null : $employee_id; // Set employee_id to null if admin

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare statement to insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, employee_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $hashed_password, $role, $employee_id);

        if ($stmt->execute()) {
            // Registration successful
            echo json_encode(['status' => 'success']);
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Return errors as JSON
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Style for popup modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
            border-radius: 8px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-blue-900 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
        <form action="register.php" method="POST" id="registerForm" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="role_admin" name="role_admin" class="mr-2">
                <label for="role_admin" class="text-sm font-medium text-gray-700">Admin</label>
            </div>
            <div class="flex items-center mt-2">
                <input type="checkbox" id="role_user" name="role_user" class="mr-2">
                <label for="role_user" class="text-sm font-medium text-gray-700">User</label>
            </div>
            <div id="employeeField" class="hidden mt-4">
                <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                <select id="employee_id" name="employee_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Select Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
                            <?php echo htmlspecialchars($employee['employee_info']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Register</button>
            </div>
        </form>
    </div>

    <!-- Popup modal for error messages -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalContent"></p>
        </div>
    </div>

    <script>
        // JavaScript to handle form submission and display error messages in popup modal
        const form = document.getElementById('registerForm');
        const roleAdminCheckbox = document.getElementById('role_admin');
        const roleUserCheckbox = document.getElementById('role_user');
        const employeeField = document.getElementById('employeeField');
        const modal = document.getElementById('myModal');
        const modalContent = document.getElementById('modalContent');
        const closeBtn = document.getElementsByClassName('close')[0];

        // Ensure only one checkbox is selected at a time
        roleAdminCheckbox.addEventListener('change', function() {
            if (this.checked) {
                roleUserCheckbox.checked = false; // Uncheck user role
                employeeField.classList.add('hidden');
            }
        });

        roleUserCheckbox.addEventListener('change', function() {
            if (this.checked) {
                roleAdminCheckbox.checked = false; // Uncheck admin role
                employeeField.classList.remove('hidden');
            } else {
                employeeField.classList.add('hidden');
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            fetch(this.action, {
                method: this.method,
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    modalContent.textContent = data.errors.join(' ');
                    modal.style.display = "block";
                } else {
                    // Registration successful, redirect to login page
                    window.location.replace("login.php");
                }
            })
            .catch(error => console.error('Error:', error));
        });

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
