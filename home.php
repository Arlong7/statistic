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
</head>
<body>
    <?php include("nav.php"); ?>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-4 ">ໜ້າຫຼັກ</h1>
        
        <?php if ($role === 'admin'): ?>
            <!-- Content for admin -->
            <p class="text-white container bg-blue-500 p-4 rounded-2xl w-1/2">
                ປະຫວັດຄວາມເປັນມາຂອງ ຄກສພ-ອກຫລ
ໃນປີ1982ກອງປະຊຸມໃຫຍ່ຄັ້ງທີIIIຂອງພັກໄດ້ມີການປັບປຸງກົງຈັກການຈັດຕັ້ງທີ່ເຮັດວຽກງານກວດກາໃຫ້ເປັນການຈັດຕັ້ງສະເພາະ ຕາມລໍາດັບຮ່ວມ ເລກທີ 02 ລົງວັນທີ 16 ກຸມພາ 1982 ຂອງຄະນະບໍລິຫານງານສູນກາງພັກ, ຄະນະປະຈໍາສະພາປະຊາຊົນສູງສຸດ ແລະ ສະພາລັດຖະມົນຕີເອີ້ນວ່າ: ຄະນະກຍາມະການກວດກາພັກ ແລະ ລັດ ຂອງສູນກາງພັກປະຊາຊົນປະຕິວັດລາວແລະສະພາລັດຖະມົນຕີສປປລາວ;ໄລຍະນີ້ກົງຈັກຄະນະກຍາມະການກວດກາພັກ ແລະ ລັດຂັ້ນແຂວງ ແລະ ຂັ້ນເມືອງ ກໍ່ໄດ້ສ້າງຕັ້ງຂຶ້ນບາງແຂວງ ແລະ ເມືອງຈໍານວນໜຶ່ງ.
ປີ1986ກອງປະຊຸມໃຫຍ່ຄັ້ງທີIVຂອງພັກໄດ້ມີການປັບປຸງກົງຈັກການຈັດຕັ້ງເອີ້ນວ່າ:ຄະນະກຍາມະການກວດກາພັກ-ລັດ, ຮອດທ້າຍປີ 1988 ໄດ້ໂຮມຄະນະຈັດຕັ້ງສູນກາງພັກ ແລະ ຄະນະກວດກາພັກ-ລັດ ເຂົ້າກັນ ເອີ້ນວ່າ: ກະຊວງຈັດຕັ້ງ-ກວດກາ ຫຼັງຈາກນັ້ນຜ່ານການເຄື່ອນໄຫວເຫັນວ່າບໍ່ເໝາະສົມ, ຮອດທ້າຍປີ 1989 ສູນກາງພັກ ຈຶ່ງຕົກລົງແຍກຄະນະກວດກາອອກຕ່າງຫາກ ເອີ້ນວ່າ: ຄະນະກຍາມະການກວດກາພັກ-ລັດແລະ ຄະນະກຍາມະການກວດກາພັກ-ລັດ ປະມານການປັບປຸງປະເມີນບຸນຢ່າງພາກການຊຶ້ງຄຳ ເປັນສາຫຼັບວຽກງານຂອງພັກແມ່ຂອງພັກ, ແລະ ຂັ້ນເມືອງກໍດີແລະ ເມືອງຂວາງທີ່ບົດລົງຕາມສະຖາບົດຂອງພັກ, ແລະ ລົງມັນຂອງພັກ.
            </p>
            <a href="manage_users.php" class="text-white p-4 rounded-2xl  bg-blue-700">ຈັດຕັ້ງຜູ້ໃຊ້</a>
        <?php else: ?>
            <!-- Content for non-admin users -->
            <div class="my-4">
                <p class="text-gray-700">
                    ທ່ານບໍ່ສາມາດໃຊ້ການໂຄງການນີ້ໄດ້ http://www.sia.gov.la/sia/backend/web/images/download/Content_2024071702072708.jpg
                </p>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    ຍອມຮັບ
                </button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
