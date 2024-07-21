<?php
session_start();
include 'dbconnection.php';

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to retrieve members from the database
function getMembers($conn) {
    $sql = "SELECT * FROM AlternateMember";
    return $conn->query($sql);
}

// Check if the action parameter is set
if (isset($_POST['action'])) {
    // Check if user is admin to perform create, update, delete actions
    if ($_SESSION['role'] !== 'admin') {
        echo "You do not have permission to perform this action.";
        exit();
    }

    // Define the action to be performed
    $action = $_POST['action'];

    // Perform the corresponding action
    if ($action === 'create') {
        handleCreate($conn);
    } elseif ($action === 'update') {
        handleUpdate($conn);
    } elseif ($action === 'delete') {
        handleDelete($conn);
    } else {
        echo "Invalid action.";
        exit();
    }
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Function to handle member creation
function handleCreate($conn) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $address = $_POST['address'] ?? null;
    $email = $_POST['email'] ?? null;
    $entryDate = $_POST['entryDate'] ?? null;
    $phoneNumber = $_POST['phoneNumber'] ?? null;

    try {
        createMember($conn, $name, $position, $address, $email, $entryDate, $phoneNumber);
        redirectToSelf();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to handle member update
function handleUpdate($conn) {
    $am_id = $_POST['am_id'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $entryDate = $_POST['entryDate'];
    $phoneNumber = $_POST['phoneNumber'];

    try {
        updateMember($conn, $am_id, $address, $email, $entryDate, $phoneNumber);
        redirectToSelf();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to handle member deletion
function handleDelete($conn) {
    $am_id = $_POST['am_id'];

    try {
        deleteMember($conn, $am_id);
        redirectToSelf();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Function to create a new member
function createMember($conn, $name, $position, $address, $email, $entryDate, $phoneNumber) {
    $stmt = $conn->prepare("INSERT INTO AlternateMember (AM_ID, Name, Position, Address, Email, EntryDate, PhoneNumber) VALUES (NULL, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $position, $address, $email, $entryDate, $phoneNumber);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// Function to update an existing member
function updateMember($conn, $am_id, $address, $email, $entryDate, $phoneNumber) {
    $stmt = $conn->prepare("UPDATE AlternateMember SET Address=?, Email=?, EntryDate=?, PhoneNumber=? WHERE AM_ID=?");
    $stmt->bind_param("ssssi", $address, $email, $entryDate, $phoneNumber, $am_id);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// Function to delete an existing member
function deleteMember($conn, $am_id) {
    $stmt = $conn->prepare("DELETE FROM AlternateMember WHERE AM_ID=?");
    $stmt->bind_param("i", $am_id);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// Function to redirect back to the same page
function redirectToSelf() {
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alternate Member CRUD</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
        @media print {
            body {
                font-family: 'Phetsarath OT', sans-serif;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex">

    <?php include("nav.php"); ?>
    
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">ລາຍຊື່ສະມາຊິກສຳຮອງ</h2>
        <!-- Display Add Member button only if user is admin -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            
        <button id="open-modal" class="bg-blue-500 text-white px-4 py-2 rounded">ເພີ່ມສະມາຊິກ</button>
        <?php endif; ?>
        <button id="print-btn" class="bg-green-500 text-white px-4 py-2 rounded ml-4">ພິມລາຍການ</button>
        <table id="members-table" class="min-w-full bg-white mt-6">
            <thead>
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">ຊື່</th>
                    <th class="px-4 py-2">ຕໍາແໜ່ງ</th>
                    <th class="px-4 py-2">ທີ່ຢູ່</th>
                    <th class="px-4 py-2">ອີເມວ</th>
                    <th class="px-4 py-2">ວັນທີ</th>
                    <th class="px-4 py-2">ເບີໂທລະສັບ</th>
                    <!-- Display Actions column only if user is admin -->
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
                        echo "<td class='border px-4 py-2'>" . $row["AM_ID"] . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Name"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Position"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Address"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Email"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["EntryDate"]) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["PhoneNumber"]) . "</td>";
                        // Display Update and Delete buttons only if user is admin
                        if ($_SESSION['role'] === 'admin') {
                            echo "<td class='border px-4 py-2'>";
                            echo "<button class='bg-green-500 text-white px-4 py-2 rounded update-btn' data-id='" . $row["AM_ID"] . "' data-name='" . htmlspecialchars($row["Name"]) . "' data-position='" . htmlspecialchars($row["Position"]) . "' data-address='" . htmlspecialchars($row["Address"]) . "' data-email='" . htmlspecialchars($row["Email"]) . "' data-entrydate='" . htmlspecialchars($row["EntryDate"]) . "' data-phonenumber='" . htmlspecialchars($row["PhoneNumber"]) . "'>Update</button> ";
                            echo "<button class='bg-red-500 text-white px-4 py-2 rounded delete-btn' data-id='" . $row["AM_ID"] . "' data-name='" . htmlspecialchars($row["Name"]) . "' data-position='" . htmlspecialchars($row["Position"]) . "'>Delete</button>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center py-4'>No members found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Add/Edit Member -->
    <div id="modal" class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">ເພີ່ມສະມາຊິກ</p>
                    <div class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"/>
                        </svg>
                    </div>
                </div>
                <form id="member-form" method="POST" action="">
                    <input type="hidden" name="action" id="action" value="create">
                    <input type="hidden" name="am_id" id="am_id" value="">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">ຊື່</label>
                        <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="position" class="block text-gray-700">ຕໍາແໜ່ງ</label>
                        <input type="text" id="position" name="position" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="address" class="block text-gray-700">ທີ່ຢູ່</label>
                        <input type="text" id="address" name="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">ອີເມວ</label>
                        <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="entryDate" class="block text-gray-700">ວັນທີ</label>
                        <input type="date" id="entryDate" name="entryDate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="phoneNumber" class="block text-gray-700">ເບີໂທລະສັບ</label>
                        <input type="text" id="phoneNumber" name="phoneNumber" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="button" class="modal-close bg-gray-500 text-white px-4 py-2 rounded mr-2">ຍົກເລີກ</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ບັນທຶກ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Open modal
        document.getElementById('open-modal').addEventListener('click', function() {
            document.getElementById('modal').classList.remove('hidden');
            document.querySelector('body').classList.add('modal-active');
            document.getElementById('action').value = 'create';
        });

        // Close modal
        document.querySelectorAll('.modal-close').forEach(function(element) {
            element.addEventListener('click', function() {
                document.getElementById('modal').classList.add('hidden');
                document.querySelector('body').classList.remove('modal-active');
                document.getElementById('member-form').reset();
            });
        });

        // Update member
        document.querySelectorAll('.update-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const data = button.dataset;
                document.getElementById('am_id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('position').value = data.position;
                document.getElementById('address').value = data.address;
                document.getElementById('email').value = data.email;
                document.getElementById('entryDate').value = data.entrydate;
                document.getElementById('phoneNumber').value = data.phonenumber;
                document.getElementById('action').value = 'update';
                document.getElementById('modal').classList.remove('hidden');
                document.querySelector('body').classList.add('modal-active');
            });
        });

        // Delete member
        document.querySelectorAll('.delete-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const data = button.dataset;
                if (confirm(`Are you sure you want to delete ${data.name} (${data.position})?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="am_id" value="${data.id}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Print table
        document.getElementById('print-btn').addEventListener('click', function() {
            var printContents = document.getElementById('members-table').outerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        });
    </script>
</body>
</html>
