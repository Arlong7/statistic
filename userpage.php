<?php
include 'dbconnection.php'; // Ensure this file handles connection errors

// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

// Fetch the employee_id from the users table based on user_id
$sql = "
    SELECT employee_id 
    FROM users 
    WHERE id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$employee_id = null;
if ($row = $result->fetch_assoc()) {
    $employee_id = $row['employee_id'];
}

$stmt->close();

// If employee_id is found, fetch employee details, staymember, and family information
if ($employee_id) {
    $sql = "
        SELECT e.*, 
               v.name AS village_name, 
               c.name AS city_name, 
               p.name AS province_name, 
               d.division_name AS division_name,
               s.P_ID,
               s.Name AS staymember_name,
               s.Surname AS staymember_surname,
               s.DayOfEntry,
               s.position_id,
               pos.position_name AS position_name,
               s.DateOfSupport,
               s.DateOfAlternate,
               s.DateOfComplete,
               f.id AS family_id,
               f.name AS family_name,
               f.surname AS family_surname,
               f.date_of_birth AS family_date_of_birth,
               f.house_no,
               f.home_unit,
               f.religion,
               f.nationality,
               f.ethnicity
        FROM employee e
        LEFT JOIN villages v ON e.village_id = v.id
        LEFT JOIN cities c ON e.city_id = c.id
        LEFT JOIN provinces p ON e.province_id = p.id
        LEFT JOIN division d ON e.division_id = d.division_id
        LEFT JOIN staymember s ON e.employee_id = s.employee_id
        LEFT JOIN position pos ON s.position_id = pos.position_id
        LEFT JOIN family f ON e.employee_id = f.employee_id
        WHERE e.employee_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Phetsarath OT', sans-serif;
            overflow: auto;
        }
        .a4-container {
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            margin: 0 auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
            position: relative;
            overflow-y: auto; /* Make container scrollable */
        }
        @media print {
            .a4-container {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                background-color: white;
            }
            .print-button {
                display: none; /* Hide print button when printing */
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen relative">

    <!-- Return to Login button -->
    <div class="absolute top-4 left-4">
        <a href="login.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"></path>
            </svg>
            ກັບ
        </a>
    </div>

    <div class="a4-container flex-grow">
        <div class="text-center mb-6">
            <img src="images/Lao.svg" alt="Logo" class="mx-auto mb-4" width="150" />
            <p class="text-sm text-gray-600">
                ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ<br />
                ສັນຕິພາບ ເອກະລາດ ປະຊາທິປະໄຕ ອອກໄປ ຄວາມໃຫ້ຄົບຄືນ
            </p>
        </div>
        <div class="space-y-6">
            <?php if (!empty($employees)) : ?>
                <?php foreach ($employees as $employee) : ?>
                    <div class="p-6 border border-gray-300 rounded-md shadow-md mb-6 bg-white">
                        <h3 class="text-2xl font-bold text-center mb-4">ຊີວະປະຫວັດ</h3>
                        <a class="block font-semibold text-xl mb-4"><span class="text-lg">1. </span>ປະຫວັດຫຍໍ້</a>
                        <p><strong>- ລະຫັດພະນັກງານ:</strong> <?php echo htmlspecialchars($employee['employee_id']); ?></p>
                        <p><strong>- ຊື່:</strong> <?php echo htmlspecialchars($employee['name']); ?></p>
                        <p><strong>- ນາມສະກຸນ:</strong> <?php echo htmlspecialchars($employee['surname']); ?></p>
                        <p><strong>- ເພດ:</strong> <?php echo htmlspecialchars($employee['gender']); ?></p>
                        <p><strong>- ອາຍຸ:</strong> <?php echo htmlspecialchars($employee['age']); ?></p>
                        <p><strong>- ວັນເກິດ:</strong> <?php echo htmlspecialchars($employee['date_of_birth']); ?></p>
                        <p><strong>- ທີ່ຢູ່:</strong> <?php echo htmlspecialchars($employee['address']); ?></p>
                        <p><strong>- ເລກໂທ:</strong> <?php echo htmlspecialchars($employee['phone_number']); ?></p>
                        <p><strong>- ສະຖານະ:</strong> <?php echo htmlspecialchars($employee['status']); ?></p>
                        <p><strong>- ອີເມວ:</strong> <?php echo htmlspecialchars($employee['email']); ?></p>
                        <p><strong>- ບ້ານ:</strong> <?php echo htmlspecialchars($employee['village_name']); ?></p>
                        <p><strong>- ເມືອງ:</strong> <?php echo htmlspecialchars($employee['city_name']); ?></p>
                        <p><strong>- ແຂວງ:</strong> <?php echo htmlspecialchars($employee['province_name']); ?></p>
                        <p><strong>- ພະແນກ:</strong> <?php echo htmlspecialchars($employee['division_name']); ?></p>
                        
                        <!-- Display staymember details -->
                        <h3 class="text-xl font-semibold mt-6 mb-4"><span class="text-lg">2. </span>ວັນເດືອນປີເຂົ້າອົງການຈັດຕັ້ງ</h3>
                        <?php if (!empty($employee['P_ID'])) : ?>
                            <p><strong>- ລະຫັດ:</strong> <?php echo htmlspecialchars($employee['P_ID']); ?></p>
                            <p><strong>- ຊື່:</strong> <?php echo htmlspecialchars($employee['staymember_name']); ?></p>
                            <p><strong>- ນາມສະກຸນ:</strong> <?php echo htmlspecialchars($employee['staymember_surname']); ?></p>
                            <p><strong>- ວັນທີເຂົ້າ:</strong> <?php echo htmlspecialchars($employee['DayOfEntry']); ?></p>
                            <p><strong>- ຕຳແຫນ່ງ:</strong> <?php echo htmlspecialchars($employee['position_name']); ?></p>
                            <p><strong>- ວັນທີສະໜັບສະໜູນ:</strong> <?php echo htmlspecialchars($employee['DateOfSupport']); ?></p>
                            <p><strong>- ວັນທີເຂົ້າສໍາຮອງ:</strong> <?php echo htmlspecialchars($employee['DateOfAlternate']); ?></p>
                            <p><strong>- ວັນທີເຂົ້າສົມບູນ:</strong> <?php echo htmlspecialchars($employee['DateOfComplete']); ?></p>
                        <?php else: ?>
                            <p class="text-center text-red-500 font-semibold">ບໍ່ມີຂໍໍ້ມູນສະມາຊິກ</p>
                        <?php endif; ?>
                        
                        <!-- Display family details -->
                        <h3 class="text-xl font-semibold mt-6 mb-4"><span class="text-lg">3. </span>ປະເພດຄອບຄົວ</h3>
                        <?php if (!empty($employee['family_id'])) : ?>
                            <p><strong>- ລະຫັດ:</strong> <?php echo htmlspecialchars($employee['family_id']); ?></p>
                            <p><strong>- ຊື່:</strong> <?php echo htmlspecialchars($employee['family_name']); ?></p>
                            <p><strong>- ນາມສະກຸນ:</strong> <?php echo htmlspecialchars($employee['family_surname']); ?></p>
                            <p><strong>- ວັນເກິດ:</strong> <?php echo htmlspecialchars($employee['family_date_of_birth']); ?></p>
                            <p><strong>- ບ້ານເລກ:</strong> <?php echo htmlspecialchars($employee['house_no']); ?></p>
                            <p><strong>- ຫນ່ວຍ:</strong> <?php echo htmlspecialchars($employee['home_unit']); ?></p>
                            <p><strong>- ສາສະໜາ:</strong> <?php echo htmlspecialchars($employee['religion']); ?></p>
                            <p><strong>- ຊັນຊາດ:</strong> <?php echo htmlspecialchars($employee['nationality']); ?></p>
                            <p><strong>- ຊົນເຜົ່າ:</strong> <?php echo htmlspecialchars($employee['ethnicity']); ?></p>
                        <?php else: ?>
                            <p class="text-center text-red-500 font-semibold">ບໍ່ມີຂໍໍ້ມູນຄອບຄົວ</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-red-500 font-semibold">ບໍ່ມີຂໍໍ້ມູນສໍາລັບພະນັກງານນີ້</p>
            <?php endif; ?>
        </div>

        <!-- Print Button -->
        <div class="text-center mt-8">
            <button onclick="window.print()" class="print-button inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                ພິມ
            </button>
        </div>
    </div>
</body>
</html>
