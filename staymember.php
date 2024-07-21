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

        $sql = "INSERT INTO StayMember (Name, Surname, Gender, Status, PhoneNumber, IDNumber, Email, DayOfEntry, Address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssss", $name, $surname, $gender, $status, $phoneNumber, $idNumber, $email, $dayOfEntry, $address);
        if (mysqli_stmt_execute($stmt)) {
            echo "New StayMember created successfully";
        } else {
            echo "Error: " . mysqli_error($conn);
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

        $sql = "UPDATE StayMember 
                SET Name=?, Surname=?, Gender=?, Status=?, PhoneNumber=?, IDNumber=?, Email=?, DayOfEntry=?, Address=?
                WHERE P_ID=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssi", $name, $surname, $gender, $status, $phoneNumber, $idNumber, $email, $dayOfEntry, $address, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "StayMember updated successfully";
        } else {
            echo "Error: " . mysqli_error($conn);
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
                echo "StayMember deleted successfully";
            } else {
                echo "No records found for deletion";
            }
        } else {
            echo "Error deleting StayMember: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayMember Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
</head>

<body class="bg-gray-100 flex">
    <?php include("nav.php"); ?>

    <div class="flex-1 p-6">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold mb-6">StayMember Management</h1>

            <!-- Button to open modal for adding StayMember -->
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4" onclick="openModal('create')">
                Add StayMember
            </button>

            <!-- Modal -->
            <div id="myModal" class="modal hidden fixed inset-0 w-full h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
                <div class="modal-content bg-white p-4 rounded w-full max-w-md overflow-y-auto">
                    <h2 class="text-xl font-bold mb-4">StayMember Details</h2>
                    <form action="" method="POST" id="stayMemberForm">
                        <input type="hidden" name="action" id="modalAction">
                        <input type="hidden" name="id" id="id">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="form-input mt-1 block w-full" required>
                        </div>
                        <div class="mb-4">
                            <label for="surname" class="block text-sm font-medium text-gray-700">Surname</label>
                            <input type="text" name="surname" id="surname" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <select name="gender" id="gender" class="form-select mt-1 block w-full">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <!-- Add more options if needed -->
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <input type="text" name="status" id="status" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phoneNumber" id="phoneNumber" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="idNumber" class="block text-sm font-medium text-gray-700">ID Number</label>
                            <input type="text" name="idNumber" id="idNumber" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="dayOfEntry" class="block text-sm font-medium text-gray-700">Day of Entry</label>
                            <input type="date" name="dayOfEntry" id="dayOfEntry" class="form-input mt-1 block w-full">
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" class="form-textarea mt-1 block w-full"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">Save</button>
                            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded" onclick="closeModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display StayMembers in a table -->
            <table class="min-w-full bg-white mt-6 border border-gray-300">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Surname</th>
                        <th class="px-4 py-2 border">Gender</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Phone Number</th>
                        <th class="px-4 py-2 border">ID Number</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">Day of Entry</th>
                        <th class="px-4 py-2 border">Address</th>
                        <th class="px-4 py-2 border">Actions</th>
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
                        echo "<td class='border px-4 py-2'>";
                        echo "<button class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2' onclick='openModal(\"update\", " . json_encode($row) . ")'>Edit</button>";
                        echo "<button class='bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded' onclick='openModal(\"delete\", " . $row['P_ID'] . ")'>Delete</button>";
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
                modalAction.value = 'update';
            } else if (action === 'delete' && data) {
                modalAction.value = 'delete';
                idInput.value = data;
                stayMemberForm.submit();
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('myModal');
            modal.classList.add('hidden');
        }
    </script>
</body>

</html>
