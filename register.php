<?php
include 'dbconnection.php'; // ລວມເອົາໄຟລ໌ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນຂອງທ່ານ

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ທຳຄວາມສະອາດຂໍ້ມູນປ້ອນເຂົ້າ
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // ເອີ້ນໂລກປົກກະຕິແມ່ນ 'user'

    // ກວດສອບຂໍ້ມູນປ້ອນເຂົ້າ
    if (empty($username)) {
        $errors[] = "ຕ້ອງປ້ອນຊື່ຜູ້ໃຊ້.";
    }

    if (empty($password)) {
        $errors[] = "ຕ້ອງປ້ອນລະຫັດຜ່ານ.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "ລະຫັດຜ່ານບໍ່ຕົງກັນ.";
    }

    // ຖ້າບໍ່ມີຄວາມຜິດພາດ, ດຳເນີນການລົງທະບຽນ
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ຕຽມຄຳສັ່ງເພື່ອເພີ່ມຜູ້ໃຊ້ເຂົ້າໃນຖານຂໍ້ມູນ
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            // ການລົງທະບຽນສຳເລັດ
            echo json_encode(['status' => 'success']);
            exit();
        } else {
            $errors[] = "ຜິດພາດ: " . $stmt->error;
        }

        $stmt->close();
    }

    // ສົ່ງຄືນຄວາມຜິດພາດເປັນ JSON
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລົງທະບຽນ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Style for popup modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
            border-radius: 8px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-blue-900 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">ລົງທະບຽນ</h2>
        <form action="register.php" method="POST" id="registerForm" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">ຊື່ຜູ້ໃຊ້</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">ລະຫັດຜ່ານ</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">ຢືນຢັນລະຫັດຜ່ານ</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">ບົດບາດ</label>
                <select name="role" id="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="user">ຜູ້ໃຊ້</option>
                    <option value="admin">ຜູ້ດູແລ</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">ລົງທະບຽນ</button>
            </div>
        </form>
    </div>

    <!-- Popup modal for error messages -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalContent"></p>
        </div>
    </div>

    <script>
        // JavaScript to handle form submission and display error messages in popup modal
        const form = document.getElementById('registerForm');
        const modal = document.getElementById('myModal');
        const modalContent = document.getElementById('modalContent');
        const closeBtn = document.getElementsByClassName('close')[0];

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            fetch(this.action, {
                method: this.method,
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    modalContent.textContent = data.errors.join(' ');
                    modal.style.display = "block";
                } else {
                    // Registration successful, redirect to login page
                    window.location.replace("login.php");
                }
            })
            .catch(error => console.error('Error:', error));
        });

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
