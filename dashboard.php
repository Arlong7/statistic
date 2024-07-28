<?php
// Start the session
session_start();

// Include database connection
include_once 'dbconnection.php'; // Assuming this file has your MySQL connection details

// Function to execute SQL queries and fetch data
function executeQuery($sql) {
    global $conn; // $conn is assumed to be your MySQL connection object

    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

// Query to get total number of employees
$sqlTotalEmployees = "SELECT COUNT(*) AS totalEmployees FROM Employee";
$totalEmployees = executeQuery($sqlTotalEmployees)[0]['totalEmployees'];

// Query to get total number of users
$sqlTotalUsers = "SELECT COUNT(*) AS totalUsers FROM Users"; // Adjust the table name as needed
$totalUsers = executeQuery($sqlTotalUsers)[0]['totalUsers'];

// Query to get total number of stay members
$sqlTotalStayMembers = "SELECT COUNT(*) AS totalStayMembers FROM StayMember";
$totalStayMembers = executeQuery($sqlTotalStayMembers)[0]['totalStayMembers'];

// Query to get total number of trade unions
$sqlTotalTradeUnion = "SELECT COUNT(*) AS totalTradeUnion FROM Trade_Union"; // Adjust table name and column as needed
$totalTradeUnion = executeQuery($sqlTotalTradeUnion)[0]['totalTradeUnion'];

// Query to get total number of youth
$sqlTotalYouth = "SELECT COUNT(*) AS totalYouth FROM Youth"; // Adjust table name and column as needed
$totalYouth = executeQuery($sqlTotalYouth)[0]['totalYouth'];

// Query to get total number of women
$sqlTotalWomen = "SELECT COUNT(*) AS totalWomen FROM Women"; // Adjust table name and column as needed
$totalWomen = executeQuery($sqlTotalWomen)[0]['totalWomen'];

// Query to get total number of villages
$sqlTotalVillages = "SELECT COUNT(*) AS totalVillages FROM Villages"; // Adjust table name and column as needed
$totalVillages = executeQuery($sqlTotalVillages)[0]['totalVillages'];

// Query to get total number of provinces
$sqlTotalProvinces = "SELECT COUNT(*) AS totalProvinces FROM Provinces"; // Adjust table name and column as needed
$totalProvinces = executeQuery($sqlTotalProvinces)[0]['totalProvinces'];

// Query to get total number of cities
$sqlTotalCities = "SELECT COUNT(*) AS totalCities FROM Cities"; // Adjust table name and column as needed
$totalCities = executeQuery($sqlTotalCities)[0]['totalCities'];

// Query to get total number of departments
$sqlTotalDepartments = "SELECT COUNT(*) AS totalDepartments FROM Department"; // Adjust table name and column as needed
$totalDepartments = executeQuery($sqlTotalDepartments)[0]['totalDepartments'];
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
</head>
<body class="bg-gray-100 flex">

    <!-- Navigation -->
    <?php include('nav.php'); ?>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 px-4">

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <!-- Total Employees Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-users text-blue-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມພະນັກງານ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-blue-500"><?php echo $totalEmployees; ?></span>
                </div>
            </div>

            <!-- Total Users Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user text-green-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມຜູ້ໃຊ້</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-green-500"><?php echo $totalUsers; ?></span>
                </div>
            </div>

            <!-- Total Stay Members Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user text-red-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມສະມາຊິກພັກ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-red-500"><?php echo $totalStayMembers; ?></span>
                </div>
            </div>

            <!-- Total Trade Unions Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-people-arrows text-purple-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມກໍາມະບານ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-purple-500"><?php echo $totalTradeUnion; ?></span>
                </div>
            </div>

            <!-- Total Youth Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-graduate text-yellow-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມຊາວໜຸ່ມ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-yellow-500"><?php echo $totalYouth; ?></span>
                </div>
            </div>

            <!-- Total Women Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-female text-pink-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມແມ່ຍິງ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-pink-500"><?php echo $totalWomen; ?></span>
                </div>
            </div>

            <!-- Total Villages Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-home text-teal-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມບ້ານ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-teal-500"><?php echo $totalVillages; ?></span>
                </div>
            </div>

            <!-- Total Provinces Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-map-signs text-indigo-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມແຂວງ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-indigo-500"><?php echo $totalProvinces; ?></span>
                </div>
            </div>

            <!-- Total Cities Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-city text-orange-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມເມືອງ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-orange-500"><?php echo $totalCities; ?></span>
                </div>
            </div>

            <!-- Total Departments Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-building text-blue-600 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">ລວມພະແນກ</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-blue-600"><?php echo $totalDepartments; ?></span>
                </div>
            </div>

        </div>

    </div>

    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
