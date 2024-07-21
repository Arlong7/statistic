<?php
// ເຊື່ອມຕໍ່ການເຊື່ອມຕໍ່ການຖາມງານຖາມງານຖາມງານຖາມງານຖາມງານຖາມງານ

include 'dbconnection.php';

// ກວດສອບວັນທີ່ກຳລັງເຂົ້າຂອງຜູ້ໃຊ້
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ກວດສອບຜູ້ໃຊ້ເງື່ອນໄຂ
$userRole = $_SESSION['role'];

// ເຮັດການສົ່ງຮຽບຮ້ອຍຂອງຮູບເຂົ້າ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // ຈຳນວນຄ່າໂດຍຫນ້າວັນທີ
    if ($userRole === 'admin') {
        if ($action === 'create') {
            handleCreate($conn);
        } elseif ($action === 'update') {
            handleUpdate($conn);
        } elseif ($action === 'delete') {
            handleDelete($conn);
        }
    }
    // ຍ້າຍທາງທີ່ເຮົາຈະພັກຈາກການວາງເລີ່ມເຮັດການ
    header("Location: movesin.php");
    exit();
}

// ຟັງເປົ້າການວັນທີ່ຂອງສະມາຊິກ
function handleCreate($conn) {
    $midate = $_POST['midate'];
    $mireason = $_POST['mireason'];
    $p_id = $_POST['p_id'];

    try {
        createMemberMoveIn($conn, $midate, $mireason, $p_id);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// ຟັງເປົ້າການປັບປຸງການວັນທີ່ຂອງສະມາຊິກ
function handleUpdate($conn) {
    $miId = $_POST['miId'];
    $midate = $_POST['midate'];
    $mireason = $_POST['mireason'];
    $p_id = $_POST['p_id'];

    try {
        updateMemberMoveIn($conn, $miId, $midate, $mireason, $p_id);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// ຟັງເປົ້າການລຶບການວັນທີ່ຂອງສະມາຊິກ
function handleDelete($conn) {
    $miId = $_POST['miId'];

    try {
        deleteMemberMoveIn($conn, $miId);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// ຟັງເປົ້າການພະນັກງານເຂົ້າມາດຈາກຖານຂໍ້ມູນ
function createMemberMoveIn($conn, $midate, $mireason, $p_id) {
    $stmt = $conn->prepare("INSERT INTO MemberMovesIn (MIdate, Reason, P_ID) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $midate, $mireason, $p_id);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// ຟັງເປົ້າການປັບປຸງການວັນທີ່ຂອງສະມາຊິກ
function updateMemberMoveIn($conn, $miId, $midate, $mireason, $p_id) {
    $stmt = $conn->prepare("UPDATE MemberMovesIn SET MIdate=?, Reason=?, P_ID=? WHERE MI_ID=?");
    $stmt->bind_param("ssii", $midate, $mireason, $p_id, $miId);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// ຟັງເປົ້າການລຶບການວັນທີ່ຂອງສະມາຊິກ
function deleteMemberMoveIn($conn, $miId) {
    $stmt = $conn->prepare("DELETE FROM MemberMovesIn WHERE MI_ID=?");
    $stmt->bind_param("i", $miId);
    $stmt->execute();

    if ($stmt->errno) {
        throw new Exception($stmt->error);
    }
}

// ຟັງເປົ້າການຈັດການທາງຂໍ້ມູນທັງສອງຈາກຖານຂໍ້ມູນ
function getMemberMovesIn($conn) {
    $sql = "SELECT MemberMovesIn.MI_ID, MemberMovesIn.MIdate, MemberMovesIn.Reason, StayMember.Name
            FROM MemberMovesIn
            JOIN StayMember ON MemberMovesIn.P_ID = StayMember.P_ID";
    return $conn->query($sql);
}

// ຟັງເປົ້າການຈັດການທາງຂໍ້ມູນທັງສອງຈາກຖານຂໍ້ມູນ
function getAllStayMembers($conn) {
    $sql = "SELECT P_ID, Name FROM StayMember";
    return $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຂະແໜງ CRUD ເຂົ້າມາດຫມາຍ</title>
       <!-- bootstrap core css -->
       <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

<!-- fonts style -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

<!-- font awesome style -->
<link href="css/font-awesome.min.css" rel="stylesheet" />

<!-- Custom styles for this template -->
<link href="css/style.css" rel="stylesheet" />
<!-- responsive style -->
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
    <h2 class="text-2xl font-bold mb-6">ສະມາຊີກຍ້າຍເຂົ້າ</h2>
    <?php if ($userRole === 'admin'): ?>
    <button id="open-modal" class="bg-blue-500 text-white px-4 py-2 rounded">ບັນທຶກ</button>
    <?php endif; ?>
    <table class="table min-w-full bg-white mt-6">
        <thead>
            <tr>
                <th class="px-4 py-2">MI ID</th>
                <th class="px-4 py-2">ວັນທີ່</th>
                <th class="px-4 py-2">ເຫດຜົນ</th>
                <th class="px-4 py-2">ຊື່ສະມາຊິກ</th>
                <?php if ($userRole === 'admin'): ?>
                <th class="px-4 py-2">ການຈັດການ</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = getMemberMovesIn($conn);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["MI_ID"]) . "</td>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["MIdate"]) . "</td>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Reason"]) . "</td>";
                    echo "<td class='border px-4 py-2'>" . htmlspecialchars($row["Name"]) . "</td>";
                    if ($userRole === 'admin') {
                        echo "<td class='border px-4 py-2'>";
                        echo "<button class='bg-green-500 text-white px-4 py-2 rounded update-btn' data-id='" . htmlspecialchars($row["MI_ID"]) . "'>ປັບປຸງ</button> ";
                        echo "<button class='bg-red-500 text-white px-4 py-2 rounded delete-btn' data-id='" . htmlspecialchars($row["MI_ID"]) . "'>ລຶບ</button>";
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td class='border px-4 py-2' colspan='5'>ບໍ່ພົບຂໍ້ມູນ</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4" id="modal-title">ບັນທຶກຂະແໜງເຂົ້າມາດຫມາຍ</h2>
        <form id="modal-form" method="POST" action="">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="miId" id="miId">
            <div class="mb-4">
                <label class="block text-gray-700">ວັນທີ່</label>
                <input type="date" name="midate" id="midate" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">ເຫດຜົນ</label>
                <input type="text" name="mireason" id="mireason" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">ເລືອກຊື່ສະມາຊິກ</label>
                <select name="p_id" id="p_id" class="w-full px-3 py-2 border rounded">
                    <?php
                    $members = getAllStayMembers($conn);
                    if ($members->num_rows > 0) {
                        while($row = $members->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row["P_ID"]) . "'>" . htmlspecialchars($row["Name"]) . "</option>";
                        }
                    } else {
                        echo "<option value=''>ບໍ່ພົບສະມາຊິກ</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ບັນທຶກ</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded ml-2" onclick="closeModal()">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('open-modal').addEventListener('click', function() {
    document.getElementById('modal-title').textContent = 'ບັນທຶກຂະແໜງເຂົ້າມາດຫມາຍ';
    document.getElementById('action').value = 'create';
    document.getElementById('modal-form').reset();
    document.getElementById('p_id').parentElement.style.display = 'block';
    document.getElementById('modal').classList.remove('hidden');
    document.body.classList.add('modal-active');
});

document.querySelectorAll('.update-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr').children;
        document.getElementById('modal-title').textContent = 'ປັບປຸງຂະແໜງເຂົ້າມາດ';
        document.getElementById('action').value = 'update';
        document.getElementById('miId').value = row[0].textContent.trim();
        document.getElementById('midate').value = row[1].textContent.trim();
        document.getElementById('mireason').value = row[2].textContent.trim();
        document.getElementById('p_id').value = row[3].textContent.trim();
        document.getElementById('p_id').parentElement.style.display = 'none'; // ເຮົາຈະເປັນວິຖານຂອງການປັບປຸງ
        document.getElementById('modal').classList.remove('hidden');
        document.body.classList.add('modal-active');
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const miId = this.getAttribute('data-id');
        if (confirm('ທ່ານແມ່ນເຈົ້າຕ້ອງການລຶບການນີ້ແມ່ນບໍ?')) {
            deleteMemberMoveIn(miId);
        }
    });
});

function deleteMemberMoveIn(miId) {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('miId', miId);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.body.classList.remove('modal-active');
}
</script>

</body>
</html>
