<?php
session_start();
require_once 'dbconnection.php';

// Check if user is logged in and has the admin or user role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'user')) {
    header("Location: login.php");
    exit();
}

// Function to fetch positions
function getPositions($conn) {
    $sql = "SELECT position_id, position_name FROM position"; 
    $result = $conn->query($sql);
    $positions = [];
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row;
    }
    return $positions;
}

// Function to fetch employees
function getEmployees($conn) {
    $sql = "SELECT employee_id, name FROM employee";
    $result = $conn->query($sql);
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    return $employees;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] === 'admin') {
    $action = $_POST['action'];
    $fields = [
        'Name', 'Surname', 'DayOfEntry', 'position_id', 'employee_id', 
        'DateOfSupport', 'DateOfAlternate', 'DateOfComplete'
    ];
    $data = array_map(function ($field) use ($conn) {
        return $conn->real_escape_string($_POST[$field] ?? '');
    }, $fields);

    if ($action === 'delete') {
        $id = $conn->real_escape_string($_POST['id']);
        $sql = "DELETE FROM StayMember WHERE P_ID='$id'";
        
        if ($conn->query($sql) === TRUE) {
            $conn->close();
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            echo 'Error: ' . $conn->error;
            exit();
        }
    }

    // Verify position_id exists in position table
    $position_id = $data[3];
    $position_check_sql = "SELECT position_id FROM `position` WHERE position_id='$position_id'";
    $position_check_result = $conn->query($position_check_sql);
    if ($position_check_result->num_rows == 0) {
        $conn->close();
        header("Location: {$_SERVER['PHP_SELF']}?message=Error: Invalid position_id&messageType=error");
        exit();
    }

    // Verify employee_id exists in employee table
    $employee_id = $data[4];
    $employee_check_sql = "SELECT employee_id FROM employee WHERE employee_id='$employee_id'";
    $employee_check_result = $conn->query($employee_check_sql);
    if ($employee_check_result->num_rows == 0) {
        $conn->close();
        header("Location: {$_SERVER['PHP_SELF']}?message=Error: Invalid employee_id&messageType=error");
        exit();
    }

    switch ($action) {
        case 'create':
            $sql = "INSERT INTO StayMember (Name, Surname, DayOfEntry, position_id, employee_id, DateOfSupport, DateOfAlternate, DateOfComplete) 
                    VALUES ('" . implode("', '", $data) . "')";
            break;
        case 'update':
            $id = $conn->real_escape_string($_POST['id']);
            $set = implode(', ', array_map(function($key, $value) {
                return "$key='$value'";
            }, $fields, $data));
            $sql = "UPDATE StayMember SET $set WHERE P_ID='$id'";
            break;
    }

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo 'Error: ' . $conn->error;
        exit();
    }
}

$search = $conn->real_escape_string($_GET['search'] ?? '');
$sql = "SELECT sm.*, p.position_name, e.name AS employee_name
        FROM StayMember sm
        JOIN position p ON sm.position_id = p.position_id
        JOIN employee e ON sm.employee_id = e.employee_id";
if ($search) {
    $sql .= " WHERE sm.Name LIKE '%$search%' OR sm.Surname LIKE '%$search%'";
}
$result = $conn->query($sql);

$positions = getPositions($conn);
$employees = getEmployees($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການສະມາຊິກພັກ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath+OT&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Phetsarath OT', sans-serif; }
        .modal-content { max-width: 600px; max-height: 80vh; overflow-y: auto; }
        .scrollable-table-container {
            max-height: 500px; /* Adjust as needed */
            overflow-y: auto;
            position: relative;
        }
        .scrollable-table-container thead {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body class="bg-gray-100 flex">
    <?php include("nav.php"); ?>
    <div class="p-2 mt-2 flex-grow">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold mb-6">ຈັດການສະມາຊິກພັກ</h1>
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <!-- Only show CRUD buttons for admin -->
                <div class="flex mb-4">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="openModal('create')">ເພີ່ມສະມາຊິກພັກ</button>
                    <button onclick="printTable()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
        ພິມ
    </button>
                    
                </div>
            <?php endif; ?>
            <!-- Search Form -->
            <form method="GET" class="mb-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ຄົ້ນຫາ..." class="p-2 border border-gray-300 rounded-lg">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">ຄົ້ນຫາ</button>
            </form>
            <!-- Modal -->
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <div id="modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg w-full max-w-lg relative">
                        <span class="absolute top-2 right-2 text-gray-500 cursor-pointer" onclick="closeModal()">✖</span>
                        <form id="modalForm" method="POST">
                            <input type="hidden" id="modalId" name="id">
                            <input type="hidden" name="action" id="modalAction">
                            <?php foreach (['Name', 'Surname'] as $field) : ?>
                                <div class="mb-4">
                                    <label for="modal<?php echo $field; ?>" class="block text-sm font-medium text-gray-700">
                                        <?php 
                                            switch ($field) {
                                                case 'Name': echo 'ຊື່'; break; // Name
                                                case 'Surname': echo 'ນາມສະກຸນ'; break; // Surname
                                            }
                                        ?>
                                    </label>
                                    <input type="text" id="modal<?php echo $field; ?>" name="<?php echo $field; ?>" class="mt-1 p-2 border border-gray-300 rounded-lg w-full">
                                </div>
                            <?php endforeach; ?>
                            <?php foreach (['DayOfEntry', 'DateOfSupport', 'DateOfAlternate', 'DateOfComplete'] as $field) : ?>
                                <div class="mb-4">
                                    <label for="modal<?php echo $field; ?>" class="block text-sm font-medium text-gray-700">
                                        <?php 
                                            switch ($field) {
                                                case 'DayOfEntry': echo 'ວັນທີ່ເຂົ້າ'; break; // Day Of Entry
                                                case 'DateOfSupport': echo 'ວັນທີ່ສະຫນັບສະຫນູນ'; break; // Date Of Support
                                                case 'DateOfAlternate': echo 'ວັນທີ່ສຳຮອງ'; break; // Date Of Alternate
                                                case 'DateOfComplete': echo 'ວັນທີ່ແລ້ວເສັດ'; break; // Date Of Complete
                                            }
                                        ?>
                                    </label>
                                    <input type="date" id="modal<?php echo $field; ?>" name="<?php echo $field; ?>" class="mt-1 p-2 border border-gray-300 rounded-lg w-full">
                                </div>
                            <?php endforeach; ?>
                            <div class="mb-4">
                                <label for="modalposition_id" class="block text-sm font-medium text-gray-700">ຕຳແໜ່ງ</label>
                                <select id="modalposition_id" name="position_id" class="mt-1 p-2 border border-gray-300 rounded-lg w-full">
                                    <?php foreach ($positions as $position) : ?>
                                        <option value="<?php echo $position['position_id']; ?>"><?php echo $position['position_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="modalemployee_id" class="block text-sm font-medium text-gray-700">ພະນັກງານ</label>
                                <select id="modalemployee_id" name="employee_id" class="mt-1 p-2 border border-gray-300 rounded-lg w-full">
                                    <?php foreach ($employees as $employee) : ?>
                                        <option value="<?php echo $employee['employee_id']; ?>"><?php echo $employee['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2" onclick="closeModal()">ຍົກເລີກ</button>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">ຢືນຢັນ</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Success/Error Message Modal -->
            <?php if (isset($_GET['message'])) : ?>
                <div id="messageModal" class="fixed inset-0 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm relative">
                        <span class="absolute top-2 right-2 text-gray-500 cursor-pointer" onclick="document.getElementById('messageModal').style.display='none'">✖</span>
                        <p class="text-lg <?php echo $_GET['messageType'] === 'success' ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo htmlspecialchars($_GET['message']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <!-- StayMember Table -->
            <div class="scrollable-table-container bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">ຊື່</th>
                            <th class="px-4 py-2">ນາມສະກຸນ</th>
                            <th class="px-4 py-2">ວັນທີ່ເຂົ້າ</th>
                            <th class="px-4 py-2">ຕຳແໜ່ງ</th>
                            <th class="px-4 py-2">ພະນັກງານ</th>
                            <th class="px-4 py-2">ວັນທີ່ສະຫນັບສະຫນູນ</th>
                            <th class="px-4 py-2">ວັນທີ່ສຳຮອງ</th>
                            <th class="px-4 py-2">ວັນທີ່ແລ້ວເສັດ</th>
                            <?php if ($_SESSION['role'] === 'admin') : ?>
                                <th class="px-4 py-2">ແກ້ໄຂ</th>
                                <th class="px-4 py-2">ລຶບ</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $row['P_ID']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['Name']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['Surname']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['DayOfEntry']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['position_name']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['employee_name']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['DateOfSupport']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['DateOfAlternate']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['DateOfComplete']; ?></td>
                                <?php if ($_SESSION['role'] === 'admin') : ?>
                                    <td class="border px-4 py-2">
                                        <button class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded" onclick="openModal('update', <?php echo htmlspecialchars(json_encode($row)); ?>)">ແກ້ໄຂ</button>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?php echo $row['P_ID']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this record?')">ລຶບ</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openModal(action, data = {}) {
            document.getElementById('modalId').value = data.P_ID || '';
            document.getElementById('modalName').value = data.Name || '';
            document.getElementById('modalSurname').value = data.Surname || '';
            document.getElementById('modalDayOfEntry').value = data.DayOfEntry || '';
            document.getElementById('modalposition_id').value = data.position_id || '';
            document.getElementById('modalemployee_id').value = data.employee_id || '';
            document.getElementById('modalDateOfSupport').value = data.DateOfSupport || '';
            document.getElementById('modalDateOfAlternate').value = data.DateOfAlternate || '';
            document.getElementById('modalDateOfComplete').value = data.DateOfComplete || '';
            document.getElementById('modalAction').value = action;
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
        function printTable() {
        var originalContents = document.body.innerHTML;
        var printContents = document.querySelector('.scrollable-table-container').innerHTML;
        
        // Create a temporary DOM element to manipulate the table HTML
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = printContents;

        // Remove the last two columns from the table header and rows
        var table = tempDiv.querySelector('table');
        if (table) {
            var headerCells = table.querySelectorAll('thead th');
            var bodyRows = table.querySelectorAll('tbody tr');

            // Remove the last two header columns
            if (headerCells.length > 2) {
                headerCells[headerCells.length - 1].remove();
                headerCells[headerCells.length - 2].remove();
            }

            // Remove the last two data columns from each row
            bodyRows.forEach(function(row) {
                var cells = row.querySelectorAll('td');
                if (cells.length > 2) {
                    cells[cells.length - 1].remove();
                    cells[cells.length - 2].remove();
                }
            });
        }
        
        // Create the print view content
        document.body.innerHTML = `
            <html>
            <head>
                <title>Print Table</title>
                <style>
                    body { font-family: 'Phetsarath OT', sans-serif; }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 0.8em; /* Adjust font size for print */
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 4px; /* Adjust padding for print */
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                        font-size: 0.7em; /* Smaller header font size */
                    }
                    @media print {
                        body {
                            margin: 0;
                            padding: 0;
                        }
                        .scrollable-table-container {
                            max-height: none; /* Ensure the table is not cut off */
                        }
                    }
                </style>
            </head>
            <body>
                ${tempDiv.innerHTML}
            </body>
            </html>`;
        
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
    </script>
</body>
</html>
