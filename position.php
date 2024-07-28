<?php
session_start();
require_once 'dbconnection.php';

// Check if user is logged in and has the admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $position_id = $_POST['position_id'] ?? '';
    $position_name = $conn->real_escape_string($_POST['position_name'] ?? '');

    switch ($action) {
        case 'create':
            $sql = "INSERT INTO Position (position_name) VALUES ('$position_name')";
            break;
        case 'update':
            $sql = "UPDATE Position SET position_name='$position_name' WHERE position_id='$position_id'";
            break;
        case 'delete':
            $sql = "DELETE FROM Position WHERE position_id='$position_id'";
            break;
    }

    if ($conn->query($sql) === TRUE) {
        echo "<p class='text-green-500'>Operation successful!</p>";
    } else {
        echo "<p class='text-red-500'>Error: " . $conn->error . "</p>";
    }
}

// Fetch data for display
$search = $conn->real_escape_string($_GET['search'] ?? '');
$sql = "SELECT * FROM Position";
if ($search) {
    $sql .= " WHERE position_name LIKE '%$search%'";
}
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Position Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath+OT&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Phetsarath OT', sans-serif; }
        .modal-content { max-width: 600px; }
        .scrollable-table-container {
            max-height: 500px;
            overflow-y: auto;
            position: relative;
        }
        .scrollable-table-container thead {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php include("nav.php"); ?>

    <div class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Position Management</h1>

        <!-- Search Form -->
        <div class="flex justify-between items-center mb-6">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="openModal('create')">Add Position</button>
            <form method="GET" class="flex items-center space-x-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search..." class="p-2 border border-gray-300 rounded-md">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Search</button>
            </form>
        </div>

        <!-- Modal -->
        <div id="modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
            <div class="modal-content bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <span class="absolute top-2 right-2 text-gray-500 cursor-pointer" onclick="closeModal()">✖</span>
                <form id="modalForm" method="POST">
                    <input type="hidden" id="modalAction" name="action">
                    <input type="hidden" id="modalPositionId" name="position_id">
                    <div class="mb-4">
                        <label for="modalPositionName" class="block text-sm font-medium text-gray-700">Position Name</label>
                        <input type="text" id="modalPositionName" name="position_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="modalSubmit">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Position Table -->
        <div class="scrollable-table-container bg-white border rounded-lg overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Position ID</th>
                        <th class="px-4 py-2 border">Position Name</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['position_id']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['position_name']); ?></td>
                            <td class="border px-4 py-2">
                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2" onclick='openModal("update", <?php echo json_encode($row); ?>)'>Edit</button>
                                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded" onclick='openModal("delete", <?php echo $row['position_id']; ?>)'>Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function openModal(action, data = null) {
            const form = document.getElementById('modalForm');
            document.getElementById('modalAction').value = action;
            document.getElementById('modalPositionId').value = data?.position_id || '';
            document.getElementById('modalPositionName').value = data ? data.position_name : '';
            
            document.getElementById('modalSubmit').textContent = action === 'delete' ? 'Confirm Delete' : 'Save';
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>
</body>
</html>
