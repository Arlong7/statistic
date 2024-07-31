<?php
include 'dbconnection.php'; // Include database connection file
session_start();

$response = array();

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Handle AJAX Requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if ($action == 'create') {
        $date = $_POST['date'];
        $employee_id = $_POST['employee_id'];

        // Check if the employee_id exists
        $check_employee_sql = "SELECT employee_id FROM employee WHERE employee_id = ?";
        $stmt = $conn->prepare($check_employee_sql);
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Employee exists, proceed with insertion
            $sql = "INSERT INTO youth (date, employee_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $date, $employee_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Entry added successfully.";
            } else {
                $response['success'] = false;
                $response['error'] = "Error executing query: " . $stmt->error;
            }
        } else {
            $response['success'] = false;
            $response['error'] = "Error: Employee ID does not exist.";
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $date = $_POST['date'];
        $employee_id = $_POST['employee_id'];

        // Check if the employee_id exists
        $check_employee_sql = "SELECT employee_id FROM employee WHERE employee_id = ?";
        $stmt = $conn->prepare($check_employee_sql);
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Employee exists, proceed with update
            $sql = "UPDATE youth SET date = ?, employee_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sii', $date, $employee_id, $id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Entry updated successfully.";
            } else {
                $response['success'] = false;
                $response['error'] = "Error executing query: " . $stmt->error;
            }
        } else {
            $response['success'] = false;
            $response['error'] = "Error: Employee ID does not exist.";
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];

        // Check if the row exists before attempting to delete
        $check_youth_sql = "SELECT id FROM youth WHERE id = ?";
        $stmt = $conn->prepare($check_youth_sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Proceed with delete
            $sql = "DELETE FROM youth WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Entry deleted successfully.";
            } else {
                $response['success'] = false;
                $response['error'] = "Error executing query: " . $stmt->error;
            }
        } else {
            $response['success'] = false;
            $response['error'] = "Error: Record not found.";
        }
    } else {
        $response['success'] = false;
        $response['error'] = "Error: No action specified.";
    }

    echo json_encode($response);
    exit();
}

// Fetch Data
$sql = "SELECT y.id, y.date, y.employee_id, e.surname, e.name 
        FROM youth y 
        JOIN employee e ON y.employee_id = e.employee_id";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

// Fetch Employees for the dropdown
$employees_sql = "SELECT employee_id, name, surname FROM employee";
$employees_result = $conn->query($employees_sql);

if (!$employees_result) {
    die("Error fetching employees: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Union Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Style for modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
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
<body class="bg-gray-100 flex">
    <?php include('nav.php'); ?> <!-- Navigation sidebar -->
    <div class="container mx-auto mt-8">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Youth Management</h1>
            <button onclick="openModal('createModal')" class="bg-blue-500 text-white px-4 py-2 rounded">Add Entry</button>
        </div>

        <div id="errorMessage" class="bg-red-500 text-white p-4 mb-4 hidden"></div>

        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Date</th>
                    <th class="py-2 px-4 border-b">Employee ID</th>
                    <th class="py-2 px-4 border-b">Employee Name</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody id="dataTable">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr data-id="<?php echo $row['id']; ?>">
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['id']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['date']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['employee_id']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($row['name']) . ' ' . htmlspecialchars($row['surname']); ?></td>
                        <td class="border px-4 py-2">
                            <button onclick="openModal('updateModal', <?php echo $row['id']; ?>, '<?php echo $row['date']; ?>', <?php echo $row['employee_id']; ?>)" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                            <button onclick="deleteEntry(<?php echo $row['id']; ?>)" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('createModal')">&times;</span>
                <h2 class="text-xl font-bold">Add</h2>
                <form id="createForm">
                    <label for="date" class="block mb-2">Date:</label>
                    <input type="date" id="date" name="date" class="border border-gray-300 p-2 w-full mb-4" required>
                    
                    <label for="employee_id" class="block mb-2">Employee:</label>
                    <select id="employee_id" name="employee_id" class="border border-gray-300 p-2 w-full mb-4" required>
                        <?php while ($emp = $employees_result->fetch_assoc()): ?>
                            <option value="<?php echo $emp['employee_id']; ?>"><?php echo htmlspecialchars($emp['employee_id']) . ' - ' . htmlspecialchars($emp['name']) . ' ' . htmlspecialchars($emp['surname']); ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="button" onclick="createEntry()" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                </form>
            </div>
        </div>

        <!-- Update Modal -->
        <div id="updateModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('updateModal')">&times;</span>
                <h2 class="text-xl font-bold">Update</h2>
                <form id="updateForm">
                    <input type="hidden" id="update_id" name="id">
                    
                    <label for="update_date" class="block mb-2">Date:</label>
                    <input type="date" id="update_date" name="date" class="border border-gray-300 p-2 w-full mb-4" required>
                    
                    <label for="update_employee_id" class="block mb-2">Employee:</label>
                    <select id="update_employee_id" name="employee_id" class="border border-gray-300 p-2 w-full mb-4" required>
                        <?php 
                        // Reset the pointer to the beginning of the employee result set
                        $employees_result->data_seek(0); 
                        while ($emp = $employees_result->fetch_assoc()): ?>
                            <option value="<?php echo $emp['employee_id']; ?>"><?php echo htmlspecialchars($emp['employee_id']) . ' - ' . htmlspecialchars($emp['name']) . ' ' . htmlspecialchars($emp['surname']); ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="button" onclick="updateEntry()" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId, id = null, date = '', employeeId = null) {
            document.getElementById(modalId).style.display = 'block';

            if (id) {
                // Populate update modal fields
                document.getElementById('update_id').value = id;
                document.getElementById('update_date').value = date;
                document.getElementById('update_employee_id').value = employeeId;
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function createEntry() {
            const form = document.getElementById('createForm');
            const formData = new FormData(form);
            formData.append('action', 'create');

            fetch('youth.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Reload data
                      location.reload();
                  } else {
                      document.getElementById('errorMessage').innerText = data.error;
                      document.getElementById('errorMessage').classList.remove('hidden');
                  }
              });
        }

        function updateEntry() {
            const form = document.getElementById('updateForm');
            const formData = new FormData(form);
            formData.append('action', 'update');

            fetch('youth.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      // Reload data
                      location.reload();
                  } else {
                      document.getElementById('errorMessage').innerText = data.error;
                      document.getElementById('errorMessage').classList.remove('hidden');
                  }
              });
        }

        function deleteEntry(id) {
            if (confirm('Are you sure you want to delete this entry?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                fetch('youth.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          // Reload data
                          location.reload();
                      } else {
                          document.getElementById('errorMessage').innerText = data.error;
                          document.getElementById('errorMessage').classList.remove('hidden');
                      }
                  });
            }
        }
    </script>
</body>
</html>
