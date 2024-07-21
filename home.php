<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check user role
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ກ່ຽວກັບພວກເຮົາ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <style>
        .swiper-container {
            width: 300px;
            height: 200px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
        }
        .swiper-slide img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body class="bg-gray-100 flex">
    <?php include("nav.php"); ?>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white p-8 rounded-lg shadow-lg mx-auto w-full lg:w-2/3 xl:w-1/2 flex flex-col items-center">
            <!-- Swiper Slider -->
            <div class="swiper-container mb-6">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="images/Content_2024071602070208.png" alt="Image 1">
                    </div>
                    <div class="swiper-slide">
                        <img src="images/2.png" alt="Image 2">
                    </div>
                    <div class="swiper-slide">
                        <img src="images/17 (1).png" alt="Image 3">
                    </div>
                </div>
            </div>

            <h1 class="text-3xl font-bold mb-6 text-center">ໜ້າຫຼັກ</h1>

            <?php if ($role === 'admin'): ?>
                <!-- Manage Users Button -->
                <a href="manage_users.php" class="bg-blue-500 text-white px-4 py-2 rounded-md mb-4">Manage Users</a>
                <!-- Content for admin -->
                <p class="text-gray-700">
                    ປະຫວັດຄວາມເປັນມາຂອງ ຄກສພ-ອກຫລີຈະສູນ ປີ 1982 ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ III ຂອງພັກໄດ້ມີການປັບປຸງກົງຈັກການຈັດຕັ້ງທີ່ເຮັດວຽກງານກວດກາໃຫ້ເປັນການຈັດຕັ້ງສະເພາະ ຕາມລໍາດັບຮ່ວມ ເລກທີ 02 ລົງວັນທີ 16 ກຸມພາ 1982 ຂອງຄະນະບໍລິຫານງານສູນກາງພັກ, ຄະນະປະຈໍາສະພາປະຊາຊົນສູງສຸດ ແລະ ສະພາລັດຖະມົນຕີເອີ້ນວ່າ: ຄະນະກຍາມະການກວດກາພັກ ແລະ ລັດ ຂອງສູນກາງພັກປະຊາຊົນປະຕິວັດລາວແລະສະພາລັດຖະມົນຕີສປປລາວ;ໄລຍະນີ້ກົງຈັກຄະນະກຍາມະການກວດກາພັກ ແລະ ລັດຂັ້ນແຂວງ ແລະ ຂັ້ນເມືອງ ກໍ່ໄດ້ສ້າງຕັ້ງຂຶ້ນບາງແຂວງ ແລະ ເມືອງຈໍານວນໜຶ່ງ.
                    ປີ1986ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ IV ຂອງພັກໄດ້ມີການປັບປຸງກົງຈັກການຈັດຕັ້ງເອີ້ນວ່າ: ຄະນະກຍາມະການກວດກາພັກ-ລັດ, ຮອດທ້າຍປີ 1988 ໄດ້ໂຮມຄະນະຈັດຕັ້ງສູນກາງພັກ ແລະ ຄະນະກວດກາພັກ-ລັດ ເຂົ້າກັນ ເອີ້ນວ່າ: ກະຊວງຈັດຕັ້ງ-ກວດກາ ຫຼັງຈາກນັ້ນຜໍ່ໄດ້ສ້າງຄະນະກວດກາອອກຕ່າງຫາກ ເອີ້ນວ່າ: ຄະນະກຍາມະການກວດກາພັກ-ລັດແລະ ຄະນະກຍາມະການກວດກາພັກ-ລັດ ປະມານການປັບປຸງປະເມີນບຸນຢ່າງພາກການຊຶ້ງຄຳ ເປັນສາຫຼັບວຽກເຮັດວຽກຫຼັງຈາກນັ້ນຄວາມພາວະນະສືບຕໍ່ມີໃຫ້ອອກໃຫ້ມີຄວາມສົດໃຈ. ທ່ານບໍ່ສາມາດໃຊ້ການໂຄງການນີ້ໄດ້.
                </p>
            <?php else: ?>
                <!-- Content for non-admin users -->
                <p class="text-gray-700">
                    ທ່ານບໍ່ສາມາດໃຊ້ການໂຄງການນີ້ໄດ້. ຂໍແອບພິເສດກໍ່ສາມາດໃຊ້ກະດູກໃຈແລະຄວາມເປັນຄົນປ່ອນໃນເຄື່ອງການໂຄງການປະຈໍາເພື່ອກະກຽມສິງລົດ. ພິເສດບໍ່ເຮັດແຕ່ງສົດໃຈເພື່ອບໍ່ຄວາມເປັນຄົນປ່ອນເພື່ອອອກໃຫ້ມີຄວາມສົດໃຈ. ກໍລອງບໍ່ແອບອີງກໍ່ອຍຈາຍໃຈຄືບໍ່ຈັດຄວາມສົດໃຈ. ທ່ານບໍ່ສາມາດໃຊ້ການໂຄງການນີ້ໄດ້. ຂໍແອບພິເສດກໍ່ສາມາດໃຊ້ກະດູກໃຈແລະຄວາມເປັນຄົນປ່ອນໃນເຄື່ອງການໂຄງການປະຈໍາເພື່ອກະກຽມສິງລົດ.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 10,
                autoplay: {
                    delay: 3000, // Adjust auto-slide delay (in milliseconds)
                    disableOnInteraction: false
                },
                loop: true
            });
        });
    </script>
</body>
</html>
