<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session at the beginning
session_start();

// Include database connection
include 'dbconnection.php';

// Check if the action parameter is set
if(isset($_POST['action'])) {
    // Define the action to be performed
    $action = $_POST['action'];

    // Perform the corresponding action based on role
    if($action === 'create' && isAdmin()) {
        handleCreate($conn);
    } elseif($action === 'update' && isAdmin()) {
        handleUpdate($conn);
    } elseif($action === 'delete' && isAdmin()) {
        handleDelete($conn);
    } else {
        echo "ການປະຕິບັດບໍ່ຖືກຕ້ອງຫຼືບໍ່ມີສິດທີ່ພົບ.";
        exit();
    }
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to handle member creation
function handleCreate($conn) {
    $modate = $_POST['modate'];
    $reason = $_POST['reason'];
    $pId = $_POST['pId'];

    try {
        createMemberMoveOut($conn, $modate, $reason, $pId);
        redirectToSelf();
    } catch (Exception $e) {
        echo "ຂໍ້ຜິດພາດ: " . $e->getMessage();
    }
}

// Function to handle member update
function handleUpdate($conn) {
    $moId = $_POST['moId'];
    $modate = $_POST['modate'];
    $reason = $_POST['reason'];
    $pId = $_POST['pId'];

    try {
        updateMemberMoveOut($conn, $moId, $modate, $reason, $pId);
        redirectToSelf();
    } catch (Exception $e) {
        echo "ຂໍ້ຜິດພາດ: " . $e->getMessage();
    }
}

// Function to handle member deletion
function handleDelete($conn) {
    $moId = $_POST['moId'];

    try {
        deleteMemberMoveOut($conn, $moId);
        redirectToSelf();
    } catch (Exception $e) {
        echo "ຂໍ້ຜິດພາດ: " . $e->getMessage();
    }
}

// Function to create a new member move out record
function createMemberMoveOut($conn, $modate, $reason, $pId) {
    $stmt = $conn->prepare("INSERT INTO MemberMovesOut (Modate, Reason, P_ID) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $modate, $reason, $pId);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// Function to update an existing member move out record
function updateMemberMoveOut($conn, $moId, $modate, $reason, $pId) {
    $stmt = $conn->prepare("UPDATE MemberMovesOut SET Modate=?, Reason=?, P_ID=? WHERE MO_ID=?");
    $stmt->bind_param("ssii", $modate, $reason, $pId, $moId);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// Function to delete an existing member move out record
function deleteMemberMoveOut($conn, $moId) {
    $stmt = $conn->prepare("DELETE FROM MemberMovesOut WHERE MO_ID=?");
    $stmt->bind_param("i", $moId);
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

// Function to retrieve member move out records from the database
function getMemberMovesOut($conn) {
    $sql = "SELECT m.MO_ID, m.Modate, m.Reason, s.Name, s.P_ID FROM MemberMovesOut m INNER JOIN StayMember s ON m.P_ID = s.P_ID";
    return $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ການບໍລິການຂາຍສະມາຊິກອອກ</title>
       <!-- ຟັງໂຫຼດສໍາເລັດຂອງເປົ້າຂອງທ່ານ -->
       <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

<!-- ຮູບແບບຂໍ້ມູນສະແດງ -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

<!-- ຮູບແບບມູນຄ່າ -->
<link href="css/font-awesome.min.css" rel="stylesheet" />

<!-- ຮູບແບບຕໍ່ໄປໃນຕາຕະລາງ -->
<link href="css/style.css" rel="stylesheet" />
<!-- ຮູບແບບປົກກະຕິ -->
<link href="css/responsive.css" rel="stylesheet" />
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
        .table td, .table th {
            border: 1px solid #e2e8f0;
            padding: 8px;
        }
        .table th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: bold;
            text-align: left;
        }
    </style>
</head>
<body class="bg-gray-100 flex">
<?php include("nav.php");?>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">ສະມາຊີກຍ້າຍອອກ</h2>
    <?php if(isAdmin()): ?>
    <button id="open-modal" class="bg-blue-500 text-white px-4 py-2 rounded">ເພີ່ມ</button>
    <?php endif; ?>
    <table class="table min-w-full bg-white mt-6">
        <thead>
            <tr>
                <th class="px-4 py-2">MO ID</th>
                <th class="px-4 py-2">ວັນທີ່</th>
                <th class="px-4 py-2">ເຫດຜົນ</th>
                <th class="px-4 py-2">ຊື່ສະມາຊີກ</th>
                <th class="px-4 py-2">ການຈັດການ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = getMemberMovesOut($conn);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["MO_ID"]) . "</td>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Modate"]) . "</td>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Reason"]) . "</td>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["P_ID"]) . " - " . htmlspecialchars($row["Name"]) . "</td>";
                    echo "<td class='border px-4 py-2'>";
                    if(isAdmin()) {
                        echo "<button class='bg-green-500 text-white px-4 py-2 rounded update-btn' data-id='" . htmlspecialchars($row["MO_ID"]) . "'>ປັບປຸງ</button> ";
                        echo "<button class='bg-red-500 text-white px-4 py-2 rounded delete-btn' data-id='" . htmlspecialchars($row["MO_ID"]) . "'>ລຶບລາຍການ</button>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td class='border px-4 py-2' colspan='5'>ບໍ່ມີການປັບປຸງລາຍການ</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<?php if(isAdmin()): ?>
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4" id="modal-title">ເພີ່ມ</h2>
        <form id="modal-form" method="POST" action="">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="moId" id="moId">
            <div class="mb-4">
                <label class="block text-gray-700">ວັນທີລາຍການລາອກ</label>
                <input type="date" name="modate" id="modate" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">ເຫດຜົນ</label>
                <input type="text" name="reason" id="reason" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">ເລືອກ P_ID - ຊື່</label>
                <select name="pId" id="pId" class="w-full px-3 py-2 border rounded">
                    <?php
                    // ຟັງເປົ້າສຳລັບ P_ID ແລະ ຊື່ຈາກ StayMember table
                    $sql = "SELECT P_ID, Name FROM StayMember";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row["P_ID"]) . "'>" . htmlspecialchars($row["P_ID"]) . " - " . htmlspecialchars($row["Name"]) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-modal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">ຍົກເລີກ</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ບັນທຶກ</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

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
        var moId = row.querySelector('td:nth-child(1)').textContent;
        var modate = row.querySelector('td:nth-child(2)').textContent;
        var reason = row.querySelector('td:nth-child(3)').textContent;
        var pIdName = row.querySelector('td:nth-child(4)').textContent;
        var pId = pIdName.split(' - ')[0]; // Extract P_ID from "P_ID - Name"

        openModal('update', moId, modate, reason, pId);
    });
});

document.querySelectorAll('.delete-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        if(confirm('ທ່ານແນ່ໃຈບໍ່ຈະລົບລາຍການນີ້?')) {
            var moId = this.getAttribute('data-id');
            document.getElementById('action').value = 'delete';
            document.getElementById('moId').value = moId;
            document.getElementById('modal-form').submit();
        }
    });
});

function openModal(action, moId = '', modate = '', reason = '', pId = '') {
    document.getElementById('action').value = action;
    document.getElementById('moId').value = moId;
    document.getElementById('modate').value = modate;
    document.getElementById('reason').value = reason;
    document.getElementById('pId').value = pId;
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
