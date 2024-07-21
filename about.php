<?php
session_start();

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);

// If user is logged in, hide the about section
if ($loggedIn) {
    $hideAbout = true;
} else {
    $hideAbout = false;
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ປະຫວັດຄວາມເປັນມາຂອງ ຄກສພ-ອກຫລ</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 20px;
            padding: 0;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Phetsarath_OT', Arial, sans-serif; /* Apply Phetsarath_OT font */
        }
        h2 {
            color: #333;
        }
        p {
            margin-bottom: 15px;
            text-align: justify; /* Justify text for Lao script readability */
        }
    </style>
</head>
<body>
    <?php include('nav.php');?>
    <div class="container">
    <a href="http://www.sia.gov.la/sia/backend/web/index.php?r=site/index">ອົງການກວດກາພັກ</a>
        <h2>ປະຫວັດຄວາມເປັນມາຂອງ ຄກສພ-ອກຫລ</h2>
        
        <h2>ປີ 1982 - ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ III</h2>
        <p>ກັບການຈັດຕັ້ງສະເພາະ ຕາມລໍາດັບຮ່ວມ, ເລກທີ 02 ລົງວັນທີ 16 ກຸມພາ 1982 ຂອງຄະນະບໍລິຫານງານສູນກາງພັກ.</p>

        <h2>ປີ 1986 - ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ IV</h2>
        <p>ກັບຄະນະກວດກາພັກ-ລັດ, ຮອດທ້າຍປີ 1988 ໂຮມຄະນະຈັດຕັ້ງສູນກາງພັກ.</p>

        <h2>ປີ 1991 - ກອງປະຊຸມໃຫຍ່ຂອງພັກຄັ້ງທີ V</h2>
        <p>ກັບຄະນະກວດກາສູນກາງພັກ ແລະ ຄະນະກວດກາລັດຖະບານ.</p>

        <h2>ປີ 1996 - ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ VI</h2>
        <p>ກັບຄະນະກວດກາສູນກາງພັກຕົກລົງໂຮມຄະນະກວດກາພັກ, ກວດກາລັດເຂົ້າກັນຄືນອີກຄັ້ງ.</p>

        <h2>ປີ 2001 - ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ VII</h2>
        <p>ກວດກາສູນກາງພັກ ແລະ ອົງການກວດກາແຫ່ງລັດ.</p>

        <h2>ປີ 2006 - ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ VIII</h2>
        <p>ຈຶ່ງຕົກລົງໂຮມເອົາ 2 ອົງການກວດກາພັກ, ກວດກາລັດຖະບານ.</p>

        <h2>ປີ 2011 - ກອງປະຊຸມໃຫຍ່ຄັ້ງທີ IX</h2>
        <p>ກວດກາສູນກາງພັກ ແລະ ອົງການກວດກາແຫ່ງລັດ.</p>
</div>
</body>
</html>
