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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
</head>
<body class="bg-gray-100 flex">

    <!-- Navigation -->
    <?php include('nav.php');?>

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

            <!-- Total Members Section with Progress Bars -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-users text-green-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Total Members</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">Complete Members</span>
                        <span class="font-bold"><?php echo $totalCompleteMembers; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: <?php echo ($totalCompleteMembers / $totalEmployees) * 100; ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">Alternate Members</span>
                        <span class="font-bold"><?php echo $totalAlternateMembers; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" style="width: <?php echo ($totalAlternateMembers / $totalEmployees) * 100; ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">New Members</span>
                        <span class="font-bold"><?php echo $totalNewMembers; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-yellow-500 h-2.5 rounded-full" style="width: <?php echo ($totalNewMembers / $totalEmployees) * 100; ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-800">Stay Members</span>
                        <span class="font-bold"><?php echo $totalStayMembers; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-red-500 h-2.5 rounded-full" style="width: <?php echo ($totalStayMembers / $totalEmployees) * 100; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Member Moves Section with Charts -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exchange-alt text-red-500 text-3xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Member Moves</h2>
                </div>
                <canvas id="memberMovesChart"></canvas>
            </div>

        </div>

    </div>

    <script>
        // Chart.js script for member moves section
        const ctx = document.getElementById('memberMovesChart').getContext('2d');
        const memberMovesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Moves In', 'Moves Out'],
                datasets: [{
                    label: 'Number of Moves',
                    data: [<?php echo $totalMovesIn; ?>, <?php echo $totalMovesOut; ?>],
                    backgroundColor: ['#4B77BE', '#E74C3C'],
                    borderColor: ['#2E86C1', '#C0392B'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
