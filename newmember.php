<?php
session_start();
include 'dbconnection.php';

// ກວດສອບວ່າຄຳວ່າງສົມບູນ action ໄດ້ຮັບໄດ້ຢູ່ໃນ $_POST ຫຼືບໍ່
$action = isset($_POST['action']) ? $_POST['action'] : '';

// ກວດສອບວ່າຜູ້ໃຊ້ໄດ້ເຂົ້າສູ່ລະບົບບໍ່
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ກວດສອບວ່າສິດທິຂອງຜູ້ໃຊ້ໄດ້ກໍານົດໄດ້ແລະເປັນສາມາດຕັ້ງຄ່າ
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

if ($role !== 'admin' && ($action === 'create' || $action === 'update' || $action === 'delete')) {
    // ສົ່ງຜູ້ໃຊ້ສົ່ງຄືນໄປກັບ dashboard ຫຼືໃຫ້ຫົວຂໍ້ຜິດພາດ
    header("Location: dashboard.php");
    exit();
}

if ($action == 'create') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $accessCode = mysqli_real_escape_string($conn, $_POST['accessCode']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
    $dateOfEmployment = $_POST['dateOfEmployment'];

    $sql = "INSERT INTO NewMember (Name, Gender, Address, Email, AccessCode, Position, PhoneNumber, DateOfEmployment) 
            VALUES ('$name', '$gender', '$address', '$email', '$accessCode', '$position', '$phoneNumber', '$dateOfEmployment')";

    if ($conn->query($sql) === TRUE) {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'update') {
    $nmId = $_POST['nmId'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $accessCode = mysqli_real_escape_string($conn, $_POST['accessCode']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
    $dateOfEmployment = $_POST['dateOfEmployment'];

    $sql = "UPDATE NewMember SET 
            Name = '$name', Gender = '$gender', Address = '$address', Email = '$email', AccessCode = '$accessCode', 
            Position = '$position', PhoneNumber = '$phoneNumber', DateOfEmployment = '$dateOfEmployment' 
            WHERE NM_ID = '$nmId'";

    if ($conn->query($sql) === TRUE) {
        header("Location: newmember.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action == 'delete') {
    $nmId = $_POST['nmId'];

    $sql = "DELETE FROM NewMember WHERE NM_ID = '$nmId'";

    if ($conn->query($sql) === TRUE) {
        header("Location: newmember.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ສົ່ງຂໍ້ມູນຜູ້ໃຊ້ທັງໝົດສະແດງ
$sql_select = "SELECT * FROM NewMember";
$result = $conn->query($sql_select);

// ປິດການເຊື່ອມຕໍ່ຖານຂອງຖອກການຂອງຖານຂອງ
$conn->close();
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຂໍ້ມູນສະມາຊິກທັງໝົດ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">
    <?php include("nav.php"); ?>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">ຂໍ້ມູນສະມາຊິກທ່ານເປັນ CRUD</h2>
        <?php if ($role === 'admin'): ?>
            <button id="open-modal" class="bg-blue-500 text-white px-4 py-2 rounded">ເພີ່ມສະມາຊິກໃໝ່</button>
        <?php else: ?>
            <p class="text-red-500 text-sm mb-4">ທ່ານບໍ່ມີສິດທິທີ່ຈະເພີ່ມ, ແກ້ວຫຼາຍບໍ່ບໍ່ພົບໃຫ້ປັບປຸງຫລືລົບສະມາຊິກ.</p>
        <?php endif; ?>
        <table class="table min-w-full bg-white mt-6">
            <thead>
                <tr>
                    <th class="px-4 py-2">ລະຫັດ </th>
                    <th class="px-4 py-2">ຊື່</th>
                    <th class="px-4 py-2">ເພດ</th>
                    <th class="px-4 py-2">ທີ່ຢູ່</th>
                    <th class="px-4 py-2">ອີເມວ</th>
                    <th class="px-4 py-2">ລະຫັດ</th>
                    <th class="px-4 py-2">ຕຳແໜ່ງ</th>
                    <th class="px-4 py-2">ເບີໂທລະສັບ</th>
                    <th class="px-4 py-2">ວັນທີ</th>
                    <?php if ($role === 'admin'): ?>
                        <th class="px-4 py-2">ຄວາມຮັບທີ່ດີ</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["NM_ID"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Name"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Gender"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Address"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Email"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["AccessCode"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Position"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["PhoneNumber"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["DateOfEmployment"]) . "</td>";
                        if ($role === 'admin') {
                            echo "<td class='border px-4 py-2'>";
                            echo "<button class='bg-green-500 text-white px-4 py-2 rounded update-btn' data-id='" . htmlspecialchars($row["NM_ID"]) . "'>ປັບປຸງ</button> ";
                            echo "<button class='bg-red-500 text-white px-4 py-2 rounded delete-btn' data-id='" . htmlspecialchars($row["NM_ID"]) . "'>ລົບ</button>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td class='border px-4 py-2' colspan='10'>ບໍ່ພົບກໍລະນີຂໍ້ມູນ</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-h-full overflow-y-auto w-1/3">
            <h2 class="text-2xl font-bold mb-4" id="modal-title">ເພີ່ມສະມາຊິກໃໝ່</h2>
            <form id="modal-form" method="POST" action="newmember.php">
                <input type="hidden" name="action" id="action">
                <input type="hidden" name="nmId" id="nmId">
                <div class="mb-4">
                    <label class="block text-gray-700">ຊື່</label>
                    <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ເພດ</label>
                    <input type="text" name="gender" id="gender" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ທີ່ຢູ່</label>
                    <input type="text" name="address" id="address" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ອີເມວ</label>
                    <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ລະຫັດ</label>
                    <input type="text" name="accessCode" id="accessCode" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ຕຳແໜ່ງ</label>
                    <input type="text" name="position" id="position" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ເບີໂທລະສັບ</label>
                    <input type="text" name="phoneNumber" id="phoneNumber" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">ວັນທີນໍ້າເຂົ້າ</label>
                    <input type="date" name="dateOfEmployment" id="dateOfEmployment" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="flex justify-end">
                    <button type="button" id="close-modal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">ຍົກເລີກ</button>
                    <?php if ($role === 'admin'): ?>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ບັນທຶກ</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('open-modal').addEventListener('click', function() {
        openModal('create');
    });

    document.getElementById('close-modal').addEventListener('click', function() {
        closeModal();
    });

    document.querySelectorAll('.update-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = this.closest('tr');
            var nmId = row.querySelector('td:nth-child(1)').textContent;
            var name = row.querySelector('td:nth-child(2)').textContent;
            var gender = row.querySelector('td:nth-child(3)').textContent;
            var address = row.querySelector('td:nth-child(4)').textContent;
            var email = row.querySelector('td:nth-child(5)').textContent;
            var accessCode = row.querySelector('td:nth-child(6)').textContent;
            var position = row.querySelector('td:nth-child(7)').textContent;
            var phoneNumber = row.querySelector('td:nth-child(8)').textContent;
            var dateOfEmployment = row.querySelector('td:nth-child(9)').textContent;

            openModal('update', nmId, name, gender, address, email, accessCode, position, phoneNumber, dateOfEmployment);
        });
    });

    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            if(confirm('ທ່ານແມ່ນຕ້ອງການລົບການບັນທຶກນີ້ບໍ່?')) {
                var nmId = this.getAttribute('data-id');
                document.getElementById('action').value = 'delete';
                document.getElementById('nmId').value = nmId;
                document.getElementById('modal-form').submit();
            }
        });
    });

    function openModal(action, nmId = '', name = '', gender = '', address = '', email = '', accessCode = '', position = '', phoneNumber = '', dateOfEmployment = '') {
        document.getElementById('action').value = action;
        document.getElementById('nmId').value = nmId;
        document.getElementById('name').value = name;
        document.getElementById('gender').value = gender;
        document.getElementById('address').value = address;
        document.getElementById('email').value = email;
        document.getElementById('accessCode').value = accessCode;
        document.getElementById('position').value = position;
        document.getElementById('phoneNumber').value = phoneNumber;
        document.getElementById('dateOfEmployment').value = dateOfEmployment;
        document.getElementById('modal').classList.remove('hidden');
        document.body.classList.add('modal-active');
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
        document.body.classList.remove('modal-active');
    }
    </script>
</body>
</html>
