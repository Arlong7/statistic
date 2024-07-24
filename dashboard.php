<?php
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
?>

<!DOCTYPE html>
<html lang="en">
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
    <div class="container mx-auto mt-8">

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <!-- Total Employees Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-users text-blue-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Total Employees</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-blue-500"><?php echo $totalEmployees; ?></span>
                </div>
            </div>

            <!-- Total Users Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user text-green-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Total Users</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-green-500"><?php echo $totalUsers; ?></span>
                </div>
            </div>

            <!-- Total Stay Members Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user text-red-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Total Stay Members</h2>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-red-500"><?php echo $totalStayMembers; ?></span>
                </div>
            </div>

        </div>

    </div>

    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
