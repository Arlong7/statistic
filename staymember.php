<?php
// Database connection
require_once 'dbconnection.php';

// CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Create
    if ($action == 'create') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $surname = mysqli_real_escape_string($conn, $_POST['surname']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
        $idNumber = mysqli_real_escape_string($conn, $_POST['idNumber']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $dayOfEntry = mysqli_real_escape_string($conn, $_POST['dayOfEntry']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $position = mysqli_real_escape_string($conn, $_POST['position']);
        $member = mysqli_real_escape_string($conn, $_POST['member']);

        $sql = "INSERT INTO StayMember (Name, Surname, Gender, Status, PhoneNumber, IDNumber, Email, DayOfEntry, Address, Position, Member) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssss", $name, $surname, $gender, $status, $phoneNumber, $idNumber, $email, $dayOfEntry, $address, $position, $member);
        if (mysqli_stmt_execute($stmt)) {
            echo "ສໍາເລັດໃສ່ StayMember ໃໝ່ແລ້ວ";
        } else {
            echo "ຜິດພາດ: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Update
    elseif ($action == 'update') {
        $id = $_POST['id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $surname = mysqli_real_escape_string($conn, $_POST['surname']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['phoneNumber']);
        $idNumber = mysqli_real_escape_string($conn, $_POST['idNumber']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $dayOfEntry = mysqli_real_escape_string($conn, $_POST['dayOfEntry']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $position = mysqli_real_escape_string($conn, $_POST['position']);
        $member = mysqli_real_escape_string($conn, $_POST['member']);

        $sql = "UPDATE StayMember 
                SET Name=?, Surname=?, Gender=?, Status=?, PhoneNumber=?, IDNumber=?, Email=?, DayOfEntry=?, Address=?, Position=?, Member=?
                WHERE P_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssssi", $name, $surname, $gender, $status, $phoneNumber, $idNumber, $email, $dayOfEntry, $address, $position, $member, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "ປ່ອນ StayMember ແກ້ໄຂໃໝ່ສໍາເລັດແລ້ວ";
        } else {
            echo "ຜິດພາດ: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Delete
    elseif ($action == 'delete') {
        $id = $_POST['id'];

        $sql = "DELETE FROM StayMember WHERE P_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo "ລົບ StayMember ສໍາເລັດແລ້ວ";
            } else {
                echo "ບໍ່ມີບັນທຶກໃດເພື່ອລົບ";
            }
        } else {
            echo "ຜິດພາດສໍາເລັດໃນການລົບ StayMember: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="lo">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ການຈັດການ StayMember</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath+OT&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Phetsarath OT', sans-serif;
        }

        /* Modal specific styles */
        .modal-content {
            max-width: 600px; /* Adjust width as needed */
            max-height: 80vh; /* Adjust height as needed */
            overflow-y: auto; /* Allow vertical scrolling if needed */
        }
        
    </style>
</head>

<body class="bg-gray-100 flex">
    <?php include("nav.php"); ?>

    <div class="p-2 mt-2 flex-grow">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold mb-6">ການຈັດການ StayMember</h1>

            <!-- Button to open modal for adding StayMember -->
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="openModal('create')">
                ເພີ່ມ StayMember
            </button>

            <!-- Button to print the table -->
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="printTable()">
                ພິມລາຍງານ
            </button>

            <!-- Modal -->
            <div id="myModal" class="modal hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
                <div class="modal-content bg-white p-4 rounded">
                    <h2 class="text-xl font-bold mb-4">ລາຍລະອຽດ StayMember</h2>
                    <form action="" method="POST" id="stayMemberForm">
                        <input type="hidden" name="action" id="modalAction">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">ຊື່</label>
                            <input type="text" name="name" id="name" class="form-input mt-1 block w-full" required>
                        </div>
                        <div class="mb-4">
                            <label for="surname" class="block text-sm font-medium text-gray-700">ນາມສະກຸນ</label>
                            <input type="text" name="surname" id="surname" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="gender" class="block text-sm font-medium text-gray-700">ເພດ</label>
                            <select name="gender" id="gender" class="form-select mt-1 block w-full">
                                <option value="male">ຜູ້ຊາຍ</option>
                                <option value="female">ຜູ້ຍິງ</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">ສະຖານະ</label>
                            <input type="text" name="status" id="status" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700">ເບີໂທ</label>
                            <input type="text" name="phoneNumber" id="phoneNumber" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="idNumber" class="block text-sm font-medium text-gray-700">ເລກບັດໃບປະຈະບັດ</label>
                            <input type="text" name="idNumber" id="idNumber" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">ອີເມວ</label>
                            <input type="email" name="email" id="email" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="dayOfEntry" class="block text-sm font-medium text-gray-700">ວັນທີເຂອນເຂົ້າ</label>
                            <input type="date" name="dayOfEntry" id="dayOfEntry" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">ບໍລິທັດ</label>
                            <input type="text" name="address" id="address" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="position" class="block text-sm font-medium text-gray-700">ສະຖານທີ່</label>
                            <input type="text" name="position" id="position" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="member" class="block text-sm font-medium text-gray-700">ສະມະດາ</label>
                            <select name="member" id="member" class="form-select mt-1 block w-full">
                                <option value="moves_out">ອອກ</option>
                                <option value="moves_in">ເຂົ້າ</option>
                                <option value="alternate_member">ສະມະດາໃຫມ່</option>
                                <option value="complete_member">ສະມະດາເຕັມ</option>
                                <option value="new_member">ສະມະດາໃຫມ່</option>
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2" onclick="closeModal()">ຍົກເຕິມ</button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">ບັນທຶກ</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <table class="min-w-full bg-white border" id="stayMemberTable">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">ຊື່</th>
                        <th class="px-4 py-2 border">ນາມສະກຸນ</th>
                        <th class="px-4 py-2 border">ເພດ</th>
                        <th class="px-4 py-2 border">ສະຖານະ</th>
                        <th class="px-4 py-2 border">ເບີໂທ</th>
                        <th class="px-4 py-2 border">ເລກບັດ</th>
                        <th class="px-4 py-2 border">ອີເມວ</th>
                        <th class="px-4 py-2 border">ວັນທີເຂົ້າ</th>
                        <th class="px-4 py-2 border">ທີ່ຢູ່</th>
                        <th class="px-4 py-2 border">ຕໍາເເໜ່ງ</th>
                        <th class="px-4 py-2 border">ປະເພດ</th>
                        <th class="px-4 py-2 border">ການຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch StayMembers from the database and display in rows
                    $result = $conn->query("SELECT * FROM StayMember");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Name']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Surname']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Gender']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Status']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['PhoneNumber']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['IDNumber']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['DayOfEntry']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Address']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Position']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['Member']) . "</td>";
                        echo "<td class='border px-4 py-2'>";
                        echo "<button class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2' onclick='openModal(\"update\", " . json_encode($row) . ")'>ແກ້ໄຂ</button>";
                        echo "<button class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded' onclick='openModal(\"delete\", " . $row['P_ID'] . ")'>ລົບ</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // JavaScript to open and close modal
        function openModal(action, data = null) {
            const modal = document.getElementById('myModal');
            const modalAction = document.getElementById('modalAction');
            const stayMemberForm = document.getElementById('stayMemberForm');
            const idInput = document.getElementById('id');
            const nameInput = document.getElementById('name');
            const surnameInput = document.getElementById('surname');
            const genderInput = document.getElementById('gender');
            const statusInput = document.getElementById('status');
            const phoneNumberInput = document.getElementById('phoneNumber');
            const idNumberInput = document.getElementById('idNumber');
            const emailInput = document.getElementById('email');
            const dayOfEntryInput = document.getElementById('dayOfEntry');
            const addressInput = document.getElementById('address');
            const positionInput = document.getElementById('position');
            const memberInput = document.getElementById('member');

            stayMemberForm.reset();

            if (action === 'create') {
                modalAction.value = 'create';
            } else if (action === 'update' && data) {
                idInput.value = data.P_ID;
                nameInput.value = data.Name;
                surnameInput.value = data.Surname;
                genderInput.value = data.Gender;
                statusInput.value = data.Status;
                phoneNumberInput.value = data.PhoneNumber;
                idNumberInput.value = data.IDNumber;
                emailInput.value = data.Email;
                dayOfEntryInput.value = data.DayOfEntry;
                addressInput.value = data.Address;
                positionInput.value = data.Position;
                memberInput.value = data.Member;
                modalAction.value = 'update';
            } else if (action === 'delete' && data) {
                if (confirm('ຢືນຢັນການລົບບັນທຶກນີ້ບໍ?')) {
                    idInput.value = data;
                    modalAction.value = 'delete';
                    stayMemberForm.submit();
                }
                return;
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('myModal');
            modal.classList.add('hidden');
        }

        function printTable() {
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>ປະຕິເສດຕາຕະລາງ</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css">');
    printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Phetsarath+OT&display=swap" rel="stylesheet">');
    printWindow.document.write('<style>body { font-family: "Phetsarath OT", sans-serif; }</style>');
    printWindow.document.write('</head><body >');
    printWindow.document.write(document.getElementById('stayMemberTable').outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

    </script>
</body>

</html>
