<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ການນຳທາງໜ້າຈໍດ້ານຂ້າງ</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <style>
    @font-face {
      font-family: 'Phetsarath OT';
      src: url('path_to_phetsarath_ot.ttf') format('truetype'); /* Update with the correct path to your Phetsarath OT font file */
    }
    body {
      font-family: 'Phetsarath OT', sans-serif;
    }
    .nav-item {
      transition: color 0.3s ease, background-color 0.3s ease, border-left 0.3s ease;
    }
    .nav-item:hover {
      color: #ffffff; /* white text on hover */
      background-color: #2563eb; /* blue-700 background on hover */
      border-left: 4px solid #1e40af; /* darker blue-800 border on hover */
    }
    .active-link {
      color: #ffffff; /* white text for active link */
      background-color: #2563eb; /* blue-700 background for active link */
      border-left: 4px solid #1e40af; /* darker blue-800 border for active link */
    }
    .dropdown {
      background-color: #3b82f6; /* blue-500 */
    }
  </style>
</head>
<body class="bg-gray-100 flex">

  <!-- Sidebar -->
  <aside class="bg-gradient-to-r from-blue-500 to-purple-600 w-64 h-screen shadow-lg">
    <div class="px-4 py-6">
      <a href="dashboard.php" class="font-bold text-white text-2xl block border-b-4 border-white mb-6">ສະຖິຕິ</a>
      <nav>
        <a href="home.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ໜ້າຫຼັກ</a>

        <a href="employee.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ພະນັກງານ</a>
        
        <div class="mt-2 relative">
          <a href="#" id="membersToggle" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ສະມາຊິກ</a>
          <div id="membersLinks" class="hidden dropdown mt-2 rounded-lg shadow-lg absolute left-0 w-full z-10">
            <a href="staymember.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ສະມາຊິກພັກ</a>
            <div class="border-t border-white mx-4"></div>
            <a href="complete_member.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ສະມາຊິກສົມບູນ</a>
            <a href="alternate_member.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ສະມາຊິກສຳຮອງ</a>
            <div class="border-t border-white mx-4"></div>
            <a href="newmember.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ສະມາຊິກໃໝ່</a>
            <a href="movesin.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ຍ້າຍເຂົ້າ</a>
            <a href="movesout.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ຍ້າຍອອກ</a>
          </div>
        </div>
        
        <?php
          // Check if a session is already started
          if (session_status() === PHP_SESSION_NONE) {
              session_start();
          }

          // Check user role
          $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

          // Show "Sign Up" link only for non-user roles
          if ($role !== 'user'): ?>
            <a href="register.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ລົງທະບຽນ</a>
        <?php endif; ?>
        <a href="http://www.sia.gov.la/sia/backend/web/index.php?r=site/index" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ກ່ຽວກັບ</a>
        <a href="logout.php" class="nav-item block py-3 px-4 text-white font-semibold rounded-lg">ອອກຈາກລະບົບ</a>
      </nav>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-grow p-4">
    <div class="container mx-auto">
      <!-- Add your page content here -->
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get the current path (e.g., /dashboard.php)
      const path = window.location.pathname;

      // Select all navigation links
      const navLinks = document.querySelectorAll('.nav-item');

      // Loop through each link and add the active-link class if it matches the current path
      navLinks.forEach(link => {
        if (link.getAttribute('href') === path) {
          link.classList.add('active-link');
        }
      });

      // Add click event for toggling the Members links without refreshing
      const membersToggle = document.getElementById('membersToggle');
      const membersLinks = document.getElementById('membersLinks');
      
      membersToggle.addEventListener('click', function(event) {
        event.preventDefault();
        membersLinks.classList.toggle('hidden');
      });
    });
  </script>
</body>
</html>
