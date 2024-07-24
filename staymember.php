<?php
session_start();
require_once 'dbconnection.php';

// Check if user is logged in and has the admin or user role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'user')) {
    header("Location: login.php");
    exit();
}

// Handle CRUD operations for admin only
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] === 'admin') {
    $action = $_POST['action'];
    $fields = [
        'Name', 'Surname', 'Gender', 'Status', 'PhoneNumber', 'IDNumber', 'Email',
        'DayOfEntry', 'Address', 'Position', 'Member'
    ];
    $data = array_map(fn($field) => $conn->real_escape_string($_POST[$field] ?? ''), $fields);
    
    switch ($action) {
        case 'create':
            $sql = "INSERT INTO StayMember (Name, Surname, Gender, Status, PhoneNumber, IDNumber, Email, DayOfEntry, Address, Position, Member) 
                    VALUES ('" . implode("', '", $data) . "')";
            break;
        case 'update':
            $id = $conn->real_escape_string($_POST['id']);
            $set = implode(', ', array_map(fn($key, $value) => "$key='$value'", $fields, $data));
            $sql = "UPDATE StayMember SET $set WHERE P_ID='$id'";
            break;
        case 'delete':
            $id = $conn->real_escape_string($_POST['id']);
            $sql = "DELETE FROM StayMember WHERE P_ID='$id'";
            break;
    }

    if ($conn->query($sql) === TRUE) {
        echo "ການປ່ອນຂໍໍ້ມູນສໍາເລັດ!";
    } else {
        echo "ຄໍາລັດ: " . $conn->error;
    }
}

// Fetch data with optional search filter
$search = $conn->real_escape_string($_GET['search'] ?? '');
$sql = "SELECT * FROM StayMember";
if ($search) {
    $sql .= " WHERE Name LIKE '%$search%' OR Surname LIKE '%$search%' OR Email LIKE '%$search%'";
}
$result = $conn->query($sql);
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
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="openModal('create')">ເພີ່ມສະມາຊິກພັກ</button>
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="printTable()">ພິມລາຍງານ</button>
       
         
            <!-- Search Form -->
            <form method="GET" class="mb-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ຄົ້ນຫາ..." class="p-2 border border-gray-300 rounded-lg">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">ຄົ້ນຫາ</button>
            </form>
            <?php endif; ?>
            <!-- Modal -->
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <div id="modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                    <div class="modal-content bg-white p-8 rounded-lg shadow-lg w-96 relative">
                        <span class="absolute top-2 right-2 text-gray-500 cursor-pointer" onclick="closeModal()">✖</span>
                        <form id="modalForm" method="POST">
                            <input type="hidden" id="modalId" name="id">
                            <input type="hidden" name="action" id="modalAction">
                            <?php foreach (['Name', 'Surname', 'Gender', 'Status', 'PhoneNumber', 'IDNumber', 'Email', 'DayOfEntry', 'Address', 'Position', 'Member'] as $field) : ?>
                                <div class="mb-4">
                                    <label for="modal<?php echo $field; ?>" class="block text-sm font-medium text-gray-700">
                                        <?php 
                                            switch ($field) {
                                                case 'Name': echo 'ຊື່'; break; // Name
                                                case 'Surname': echo 'ນາມສະກຸນ'; break; // Surname
                                                case 'Gender': echo 'ເພດ'; break; // Gender
                                                case 'Status': echo 'ສະຖານທະງວະ'; break; // Status
                                                case 'PhoneNumber': echo 'ເບີໂທ'; break; // PhoneNumber
                                                case 'IDNumber': echo 'ເລກບັດປະຈໍາຕົວ'; break; // IDNumber
                                                case 'Email': echo 'ອີເມວ'; break; // Email
                                                case 'DayOfEntry': echo 'ວັນທີເຂອນສະມາຊິກ'; break; // DayOfEntry
                                                case 'Address': echo 'ທີ່ຢູ່'; break; // Address
                                                case 'Position': echo 'ຕຳແໜ່ງ'; break; // Position
                                                case 'Member': echo 'ສະມາຊິກ'; break; // Member
                                            }
                                        ?>
                                    </label>
                                    <input type="text" id="modal<?php echo $field; ?>" name="<?php echo $field; ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            <?php endforeach; ?>
                            <div>
                                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="modalSubmit">ບັນທຶກ</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="scrollable-table-container bg-white border rounded-lg overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border">P_ID</th>
                            <th class="px-4 py-2 border">ຊື່</th> <!-- Name -->
                            <th class="px-4 py-2 border">ນາມສະກຸນ</th> <!-- Surname -->
                            <th class="px-4 py-2 border">ເພດ</th> <!-- Gender -->
                            <th class="px-4 py-2 border">ສະຖານທະງວະ</th> <!-- Status -->
                            <th class="px-4 py-2 border">ເບີໂທ</th> <!-- PhoneNumber -->
                            <th class="px-4 py-2 border">ເລກບັດປະຈໍາຕົວ</th> <!-- IDNumber -->
                            <th class="px-4 py-2 border">ອີເມວ</th> <!-- Email -->
                            <th class="px-4 py-2 border">ວັນທີເຂອນສະມາຊິກ</th> <!-- DayOfEntry -->
                            <th class="px-4 py-2 border">ທີ່ຢູ່</th> <!-- Address -->
                            <th class="px-4 py-2 border">ຕຳແໜ່ງ</th> <!-- Position -->
                            <th class="px-4 py-2 border">ສະມາຊິກ</th> <!-- Member -->
                            <?php if ($_SESSION['role'] === 'admin') : ?>
                                <th class="px-4 py-2 border">ບົດບັດ</th> <!-- Actions -->
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($row['P_ID']); ?></td>
                                <?php foreach ($row as $key => $value) : ?>
                                    <?php if ($key !== 'P_ID') : ?>
                                        <td class="border px-4 py-2"><?php echo htmlspecialchars($value); ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if ($_SESSION['role'] === 'admin') : ?>
                                    <td class="border px-4 py-2">
                                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2" onclick='openModal("update", <?php echo json_encode($row); ?>)'>ແກ້ໄຂ</button>
                                        <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick='openModal("delete", <?php echo $row['P_ID']; ?>)'>ລົບ</button>
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
        function openModal(action, data = null) {
            const form = document.getElementById('modalForm');
            const fields = ['Name', 'Surname', 'Gender', 'Status', 'PhoneNumber', 'IDNumber', 'Email', 'DayOfEntry', 'Address', 'Position', 'Member'];
            
            form.reset();
            document.getElementById('modalAction').value = action;
            document.getElementById('modalId').value = data?.P_ID || '';
            
            fields.forEach(field => {
                document.getElementById('modal' + field).value = data ? data[field] : '';
            });
            
            if (action === 'delete') {
                form.querySelector('button').textContent = 'ຢືນຢັນການລົບ';
            } else {
                form.querySelector('button').textContent = 'ບັນທຶກ';
            }
            
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        function printTable() {
            const printWindow = window.open('', '', 'height=600,width=800');
            const tableHtml = document.querySelector('.scrollable-table-container').outerHTML;
            printWindow.document.write('<html><head><title>ພິມລາຍງານ</title>');
            printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid black; padding: 8px; text-align: left; } th { background-color: #f2f2f2; } </style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h1>ລາຍງານສະມາຊິກພັກ</h1>');
            printWindow.document.write(tableHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
    </script>
</body>
</html>
