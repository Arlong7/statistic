<?php
session_start();
include 'dbconnection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to retrieve all members (READ operation)
function getMembers($conn) {
    $sql = "SELECT * FROM CompleteMember";
    return $conn->query($sql);
}

// Redirect users with role "user" from creating, updating, or deleting
if ($_SESSION['role'] === 'user') {
    // Redirect to index or members list if trying to perform create, update, delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'], ['create', 'update', 'delete'])) {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}

// Handle form actions (create, update, delete) if user is admin
if ($_SESSION['role'] === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $cm_id = $_POST['cm_id'] ?? null;
    $name = $_POST['name'];
    $position = $_POST['position'];
    $address = $_POST['address'] ?? null;
    $email = $_POST['email'] ?? null;
    $entryDate = $_POST['entryDate'] ?? null;
    $phoneNumber = $_POST['phoneNumber'] ?? null;

    switch ($action) {
        case 'create':
            createMember($conn, $name, $position, $address, $email, $entryDate, $phoneNumber);
            break;
        case 'update':
            updateMember($conn, $cm_id, $name, $position, $address, $email, $entryDate, $phoneNumber);
            break;
        case 'delete':
            deleteMember($conn, $cm_id);
            break;
    }
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Function to create a new member
function createMember($conn, $name, $position, $address, $email, $entryDate, $phoneNumber) {
    $stmt = $conn->prepare("INSERT INTO CompleteMember (Name, Position, Address, Email, EntryDate, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $position, $address, $email, $entryDate, $phoneNumber);
    $stmt->execute();
    $stmt->close();
}

// Function to update a member
function updateMember($conn, $cm_id, $name, $position, $address, $email, $entryDate, $phoneNumber) {
    $stmt = $conn->prepare("UPDATE CompleteMember SET Name=?, Position=?, Address=?, Email=?, EntryDate=?, PhoneNumber=? WHERE CM_ID=?");
    $stmt->bind_param("ssssssi", $name, $position, $address, $email, $entryDate, $phoneNumber, $cm_id);
    $stmt->execute();
    $stmt->close();
}

// Function to delete a member
function deleteMember($conn, $cm_id) {
    $stmt = $conn->prepare("DELETE FROM CompleteMember WHERE CM_ID=?");
    $stmt->bind_param("i", $cm_id);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ສະມາຊິກສົມບູນ</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: "Phetsarath OT";
            src: url("fonts/Phetsarath_OT.woff2") format("woff2"), url("fonts/Phetsarath_OT.woff") format("woff");
        }
        
        body {
            font-family: "Phetsarath OT", sans-serif;
        }

        @media print {
            .no-print { display: none; }
            body {
                font-family: "Phetsarath OT", sans-serif;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex">
<?php include("nav.php");?>
<div class="container mx-auto p-6">
    <?php if ($_SESSION['role'] === 'admin' && isset($_POST['action']) && ($_POST['action'] === 'update' || $_POST['action'] === 'delete')): ?>
        <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">ຂໍ້ມູນທີ່ປັບປຸງສຳເລັດຮຽບຮ້ອຍ</div>
        <script>
            setTimeout(() => {
                document.querySelector('.bg-green-500').remove();
            }, 3000); // 3 seconds
        </script>
    <?php endif; ?>
    <h2 class="text-2xl font-bold mb-6">ສະມາຊິກສົມບູນ</h2>
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <button id="open-modal" class="bg-blue-500 text-white px-4 py-2 rounded">ເພີ່ມສະມາຊິກ</button>
    <?php endif; ?>

    <table class="min-w-full bg-white mt-6" id="members-table">
        <thead>
            <tr>
                <th class="px-4 py-2">ລະຫັດ</th>
                <th class="px-4 py-2">ຊື່</th>
                <th class="px-4 py-2">ຕໍາແໜ່ງ</th>
                <th class="px-4 py-2">ທີ່ຢູ່</th>
                <th class="px-4 py-2">ອີເມວ</th>
                <th class="px-4 py-2">ວັນທີ່ເຂົ້າຮ່ວມ</th>
                <th class="px-4 py-2">ເບີໂທລະສັບ</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th class="px-4 py-2">ການຈັດການ</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = getMembers($conn);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='border px-4 py-2'>" . $row["CM_ID"] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row["Name"] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row["Position"] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row["Address"] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row["Email"] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row["EntryDate"] . "</td>";
                    echo "<td class='border px-4 py-2'>" . $row["PhoneNumber"] . "</td>";
                    if ($_SESSION['role'] === 'admin') {
                        echo "<td class='border px-4 py-2'>";
                        echo "<button class='bg-green-500 text-white px-4 py-2 rounded update-btn' data-id='" . $row["CM_ID"] . "' data-name='" . $row["Name"] . "' data-position='" . $row["Position"] . "' data-address='" . $row["Address"] . "' data-email='" . $row["Email"] . "' data-entrydate='" . $row["EntryDate"] . "' data-phonenumber='" . $row["PhoneNumber"] . "'>ປັບປຸງ</button> ";
                        echo "<button class='bg-red-500 text-white px-4 py-2 rounded delete-btn' data-id='" . $row["CM_ID"] . "'>ລຶບ</button>";
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' class='border px-4 py-2'>ບໍ່ມີຂໍ້ມູນສະມາຊິກ</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden modal" id="crud-modal">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded shadow-md w-full max-w-lg mx-2">
            <h2 class="text-2xl font-bold mb-6" id="modal-title">ຟອມການສະມາຊິກ</h2>
            <form action="complete_member.php" method="POST">
                <input type="hidden" id="action" name="action" value="create">
                <input type="hidden" id="cm_id" name="cm_id" value="">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">ຊື່:</label>
                    <input type="text" id="name" name="name" required class="w-full border border-gray-300 p-2 rounded">
                </div>
                <div class="mb-4">
                    <label for="position" class="block text-gray-700">ຕໍາແໜ່ງ:</label>
                    <input type="text" id="position" name="position" required class="w-full border border-gray-300 p-2 rounded">
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-gray-700">ທີ່ຢູ່:</label>
                    <input type="text" id="address" name="address" class="w-full border border-gray-300 p-2 rounded">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">ອີເມວ:</label>
                    <input type="email" id="email" name="email" class="w-full border border-gray-300 p-2 rounded">
                </div>
                <div class="mb-4">
                    <label for="entryDate" class="block text-gray-700">ວັນທີ່ເຂົ້າຮ່ວມ:</label>
                    <input type="date" id="entryDate" name="entryDate" class="w-full border border-gray-300 p-2 rounded">
                </div>
                <div class="mb-4">
                    <label for="phoneNumber" class="block text-gray-700">ເບີໂທລະສັບ:</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" class="w-full border border-gray-300 p-2 rounded">
                </div>
                <div class="flex justify-end">
                    <button type="button" id="close-modal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">ຍົກເລີກ</button>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ສົ່ງ</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const openModalBtn = document.getElementById('open-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const modal = document.getElementById('crud-modal');
    const modalTitle = document.getElementById('modal-title');
    const actionInput = document.getElementById('action');
    const cmIdInput = document.getElementById('cm_id');
    const nameInput = document.getElementById('name');
    const positionInput = document.getElementById('position');
    const addressInput = document.getElementById('address');
    const emailInput = document.getElementById('email');
    const entryDateInput = document.getElementById('entryDate');
    const phoneNumberInput = document.getElementById('phoneNumber');

    const resetModal = () => {
        cmIdInput.value = '';
        nameInput.value = '';
        positionInput.value = '';
        addressInput.value = '';
        emailInput.value = '';
        entryDateInput.value = '';
        phoneNumberInput.value = '';
    };

    openModalBtn.addEventListener('click', () => {
        resetModal();
        modalTitle.textContent = 'ເພີ່ມສະມາຊິກ';
        actionInput.value = 'create';
        modal.classList.remove('hidden');
        document.body.classList.add('modal-active');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.classList.remove('modal-active');
    });

    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', () => {
            const cm_id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const position = button.getAttribute('data-position');
            const address = button.getAttribute('data-address');
            const email = button.getAttribute('data-email');
            const entryDate = button.getAttribute('data-entrydate');
            const phoneNumber = button.getAttribute('data-phonenumber');

            cmIdInput.value = cm_id;
            nameInput.value = name;
            positionInput.value = position;
            addressInput.value = address;
            emailInput.value = email;
            entryDateInput.value = entryDate;
            phoneNumberInput.value = phoneNumber;

            modalTitle.textContent = 'ປັບປຸງສະມາຊິກ';
            actionInput.value = 'update';
            modal.classList.remove('hidden');
            document.body.classList.add('modal-active');
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const cm_id = button.getAttribute('data-id');

            if (confirm(`ທ່ານແມ່ນແທ້ບໍ່ທີ່ຈະລຶບສະມາຊິກທີ່ມີລະຫັດ ${cm_id} ແທ້ບໍ່?`)) {
                cmIdInput.value = cm_id;
                actionInput.value = 'delete';
                document.querySelector('form').submit();
            }
        });
    });
</script>
</body>
</html>
