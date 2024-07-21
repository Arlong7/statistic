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

// Example queries to fetch data from tables
// Query to get total number of employees
$sqlTotalEmployees = "SELECT COUNT(*) AS totalEmployees FROM Employee";
$totalEmployees = executeQuery($sqlTotalEmployees)[0]['totalEmployees'];

// Query to get total number of complete members
$sqlTotalCompleteMembers = "SELECT COUNT(*) AS totalCompleteMembers FROM CompleteMember";
$totalCompleteMembers = executeQuery($sqlTotalCompleteMembers)[0]['totalCompleteMembers'];

// Query to get total number of alternate members
$sqlTotalAlternateMembers = "SELECT COUNT(*) AS totalAlternateMembers FROM AlternateMember";
$totalAlternateMembers = executeQuery($sqlTotalAlternateMembers)[0]['totalAlternateMembers'];

// Query to get total number of new members
$sqlTotalNewMembers = "SELECT COUNT(*) AS totalNewMembers FROM NewMember";
$totalNewMembers = executeQuery($sqlTotalNewMembers)[0]['totalNewMembers'];

// Query to get total number of stay members
$sqlTotalStayMembers = "SELECT COUNT(*) AS totalStayMembers FROM StayMember";
$totalStayMembers = executeQuery($sqlTotalStayMembers)[0]['totalStayMembers'];

// Query to get total number of member moves in
$sqlTotalMovesIn = "SELECT COUNT(*) AS totalMovesIn FROM MemberMovesIn";
$totalMovesIn = executeQuery($sqlTotalMovesIn)[0]['totalMovesIn'];

// Query to get total number of member moves out
$sqlTotalMovesOut = "SELECT COUNT(*) AS totalMovesOut FROM MemberMovesOut";
$totalMovesOut = executeQuery($sqlTotalMovesOut)[0]['totalMovesOut'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">

    <!-- Navigation -->
    <?php include('nav.php');?>

 

    <!-- Main Content -->
    <div class="container mx-auto mt-8">

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Total Employees Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Total Employees</h2>
                <div class="flex justify-between items-center">
                    <span class="text-3xl font-bold text-blue-500"><?php echo $totalEmployees; ?></span>
                </div>
            </div>

            <!-- Total Members Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Total Members</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-gray-800">Complete Members:</div>
                    <div class="font-bold"><?php echo $totalCompleteMembers; ?></div>
                    <div class="text-gray-800">Alternate Members:</div>
                    <div class="font-bold"><?php echo $totalAlternateMembers; ?></div>
                    <div class="text-gray-800">New Members:</div>
                    <div class="font-bold"><?php echo $totalNewMembers; ?></div>
                    <div class="text-gray-800">Stay Members:</div>
                    <div class="font-bold"><?php echo $totalStayMembers; ?></div>
                </div>
            </div>

            <!-- Member Moves Section -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Member Moves</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-gray-800">Moves In:</div>
                    <div class="font-bold"><?php echo $totalMovesIn; ?></div>
                    <div class="text-gray-800">Moves Out:</div>
                    <div class="font-bold"><?php echo $totalMovesOut; ?></div>
                </div>
            </div>

        </div>

    </div>



</body>
</html>
