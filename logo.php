<?php
session_start();
include_once 'dbconnection.php';

function executeQuery($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    $data = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

$sqlTotalEmployees = "SELECT COUNT(*) AS totalEmployees FROM Employee";
$totalEmployees = executeQuery($sqlTotalEmployees)[0]['totalEmployees'];

$sqlTotalUsers = "SELECT COUNT(*) AS totalUsers FROM Users";
$totalUsers = executeQuery($sqlTotalUsers)[0]['totalUsers'];

$sqlTotalStayMembers = "SELECT COUNT(*) AS totalStayMembers FROM StayMember";
$totalStayMembers = executeQuery($sqlTotalStayMembers)[0]['totalStayMembers'];

$sqlTotalTradeUnion = "SELECT COUNT(*) AS totalTradeUnion FROM Trade_Union";
$totalTradeUnion = executeQuery($sqlTotalTradeUnion)[0]['totalTradeUnion'];

$sqlTotalYouth = "SELECT COUNT(*) AS totalYouth FROM Youth";
$totalYouth = executeQuery($sqlTotalYouth)[0]['totalYouth'];

$sqlTotalWomen = "SELECT COUNT(*) AS totalWomen FROM Women";
$totalWomen = executeQuery($sqlTotalWomen)[0]['totalWomen'];

$sqlTotalVillages = "SELECT COUNT(*) AS totalVillages FROM Villages";
$totalVillages = executeQuery($sqlTotalVillages)[0]['totalVillages'];

$sqlTotalProvinces = "SELECT COUNT(*) AS totalProvinces FROM Provinces";
$totalProvinces = executeQuery($sqlTotalProvinces)[0]['totalProvinces'];

$sqlTotalCities = "SELECT COUNT(*) AS totalCities FROM Cities";
$totalCities = executeQuery($sqlTotalCities)[0]['totalCities'];

$sqlTotalDepartments = "SELECT COUNT(*) AS totalDepartments FROM Department";
$totalDepartments = executeQuery($sqlTotalDepartments)[0]['totalDepartments'];

$sqlTotalFamily = "SELECT COUNT(*) AS totalFamily FROM family";
$totalFamily = executeQuery($sqlTotalFamily)[0]['totalFamily'];

$sqlTotalDivisions = "SELECT COUNT(*) AS totalDivisions FROM Division";
$totalDivisions = executeQuery($sqlTotalDivisions)[0]['totalDivisions'];

?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @font-face {
            font-family: 'Phetsarath OT';
            src: url('fonts/PhetsarathOT-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Phetsarath OT';
            src: url('fonts/PhetsarathOT-Bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        body {
            font-family: 'Phetsarath OT', sans-serif;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
</head>
<body class="bg-gray-100 flex">

    <?php include('nav.php'); ?>

    <div class="container mx-auto mt-8 px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມພະນັກງານ</h2>
                <canvas id="employeesChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມຜູ້ໃຊ້</h2>
                <canvas id="usersChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມສະມາຊິກພັກ</h2>
                <canvas id="stayMembersChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມກໍາມະບານ</h2>
                <canvas id="tradeUnionChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມຊາວໜຸ່ມ</h2>
                <canvas id="youthChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມແມ່ຍິງ</h2>
                <canvas id="womenChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມບ້ານ</h2>
                <canvas id="villagesChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມແຂວງ</h2>
                <canvas id="provincesChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມເມືອງ</h2>
                <canvas id="citiesChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມພະແນກ</h2>
                <canvas id="divisionsChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມຄອບຄົວ</h2>
                <canvas id="familyChart"></canvas>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-800">ລວມກົມ</h2>
                <canvas id="departmentsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const chartData = {
            employees: <?php echo $totalEmployees; ?>,
            users: <?php echo $totalUsers; ?>,
            stayMembers: <?php echo $totalStayMembers; ?>,
            tradeUnion: <?php echo $totalTradeUnion; ?>,
            youth: <?php echo $totalYouth; ?>,
            women: <?php echo $totalWomen; ?>,
            villages: <?php echo $totalVillages; ?>,
            provinces: <?php echo $totalProvinces; ?>,
            cities: <?php echo $totalCities; ?>,
            departments: <?php echo $totalDepartments; ?>,
            family: <?php echo $totalFamily; ?>,
            divisions: <?php echo $totalDivisions; ?>
        };

        function createChart(context, label, data, bgColor, borderColor) {
            new Chart(context, {
                type: 'bar',
                data: {
                    labels: [label],
                    datasets: [{
                        label: label,
                        data: [data],
                        backgroundColor: bgColor,
                        borderColor: borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            createChart(document.getElementById('employeesChart'), 'ລວມພະນັກງານ', chartData.employees, 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)');
            createChart(document.getElementById('usersChart'), 'ລວມຜູ້ໃຊ້', chartData.users, 'rgba(255, 99, 132, 0.2)', 'rgba(255, 99, 132, 1)');
            createChart(document.getElementById('stayMembersChart'), 'ລວມສະມາຊິກພັກ', chartData.stayMembers, 'rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)');
            createChart(document.getElementById('tradeUnionChart'), 'ລວມກໍາມະບານ', chartData.tradeUnion, 'rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)');
            createChart(document.getElementById('youthChart'), 'ລວມຊາວໜຸ່ມ', chartData.youth, 'rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)');
            createChart(document.getElementById('womenChart'), 'ລວມແມ່ຍິງ', chartData.women, 'rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)');
            createChart(document.getElementById('villagesChart'), 'ລວມບ້ານ', chartData.villages, 'rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)');
            createChart(document.getElementById('provincesChart'), 'ລວມແຂວງ', chartData.provinces, 'rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)');
            createChart(document.getElementById('citiesChart'), 'ລວມເມືອງ', chartData.cities, 'rgba(255, 159, 64, 0.2)', 'rgba(255, 159, 64, 1)');
            createChart(document.getElementById('departmentsChart'), 'ລວມກົມ', chartData.departments, 'rgba(255, 206, 86, 0.2)', 'rgba(255, 206, 86, 1)');
            createChart(document.getElementById('familyChart'), 'ລວມຄອບຄົວ', chartData.family, 'rgba(255, 99, 132, 0.2)', 'rgba(255, 99, 132, 1)');
            createChart(document.getElementById('divisionsChart'), 'ລວມພະແນກ', chartData.divisions, 'rgba(54, 162, 235, 0.2)', 'rgba(54, 162, 235, 1)');
        });
    </script>

</body>
</html>
