<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ການນຳທາງໜ້າຈໍດ້ານຂໍາງ</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <style>
    @font-face {
      font-family: 'Phetsarath OT';
      src: url('path_to_phetsarath_ot.ttf') format('truetype');
    }
    body {
      font-family: 'Phetsarath OT', sans-serif;
    }
    .nav-item {
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .nav-item:hover {
      background-color: #4b5563; /* gray-600 */
      color: #ffffff;
    }
    .active-link {
      background-color: #2563eb; /* blue-700 */
      color: #ffffff;
    }
    .dropdown-menu {
      display: none;
    }
    .dropdown-menu.show {
      display: block;
    }
  </style>
</head>
<body class="bg-gray-100 flex">

  <!-- Sidebar -->
  <aside class="bg-gray-800 w-64 h-screen shadow-lg fixed top-0 left-0 z-10">
    <div class="px-4 py-6 h-full flex flex-col">
      <a href="dashboard.php" class="font-bold text-white text-2xl block border-b-4 border-white mb-6">ສະຖິຕິ</a>
      <nav class="space-y-2 flex-grow">
        <a href="home.php" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg">ໜ້າຫຼັກ</a>
        
        <!-- Dropdown Menu -->
        <div class="relative">
          <button id="dropdown-button-1" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg flex items-center justify-between">
            ຈັດການຂໍ້ມູນພື້ນຖານ
            <i class="fas fa-chevron-right ml-2"></i>
          </button>
          <div id="dropdown-menu-1" class="dropdown-menu absolute left-full top-0 bg-white shadow-lg rounded-lg w-48 ml-2">
            <a href="village.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ບ້ານ</a>
            <a href="city.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ເມືອງ</a>
            <a href="province.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ແຂວງ</a>
            <a href="department.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ພະແນກ</a>
            <a href="position.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ຕໍາແໜ່ງ</a>
          </div>
        </div>

        <div class="relative">
          <button id="dropdown-button-2" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg flex items-center justify-between">
            ຈັດການຂໍ້ມູນພະນັກງານ
            <i class="fas fa-chevron-right ml-2"></i>
          </button>
          <div id="dropdown-menu-2" class="dropdown-menu absolute left-full top-0 bg-white shadow-lg rounded-lg w-48 ml-2">
            <a href="employee.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ພະນັກງານ</a>
            <a href="family.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ຄອບຄົວ</a>
          </div>
        </div>

        <div class="relative">
          <button id="dropdown-button-3" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg flex items-center justify-between">
            ຈັດການຂໍ້ມູນການຈັດຕັ້ງ
            <i class="fas fa-chevron-right ml-2"></i>
          </button>
          <div id="dropdown-menu-3" class="dropdown-menu absolute left-full top-0 bg-white shadow-lg rounded-lg w-48 ml-2">
            <a href="youth.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ຊາວໜຸ່ມ</a>
            <a href="trade_union.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ກໍາມະບານ</a>
            <a href="women.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200">ແມ່ຍງ</a>
          </div>
        </div>

        <a href="staymember.php" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg">ສະມາຊິກພັກ</a>

        <?php
          if (session_status() === PHP_SESSION_NONE) session_start();
          if (isset($_SESSION['role']) && $_SESSION['role'] !== 'user'): ?>
            <a href="register.php" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg">ລົງທະບຽນ</a>
        <?php endif; ?>
        <a href="dashboard.php" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg">ລາຍງານທັງໝົດ</a>
        <a href="http://www.sia.gov.la/sia/backend/web/index.php?r=site/index" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg">ກ່ຽວກັບ</a>
        <a href="logout.php" class="nav-item block py-3 px-4 text-gray-300 font-semibold rounded-lg">ອອກຈາກລະບົບ</a>
      </nav>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-grow ml-64 p-4">
    <div class="container mx-auto">
      <!-- Page Content -->
      <div id="page-content">
        <!-- Add your page content here -->
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const path = window.location.pathname;
      document.querySelectorAll('.nav-item').forEach(link => {
        if (link.getAttribute('href') === path) {
          link.classList.add('active-link');
        }
      });

      document.getElementById('dropdown-button-1').addEventListener('click', function() {
        document.getElementById('dropdown-menu-1').classList.toggle('show');
      });

      document.getElementById('dropdown-button-2').addEventListener('click', function() {
        document.getElementById('dropdown-menu-2').classList.toggle('show');
      });

      document.getElementById('dropdown-button-3').addEventListener('click', function() {
        document.getElementById('dropdown-menu-3').classList.toggle('show');
      });
    });
  </script>
</body>
</html>
