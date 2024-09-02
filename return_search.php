<?php
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_library";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$details = [];

// การค้นหาข้อมูลการคืนหนังสือ
if (isset($_POST['search'])) {
    $search_b_id = $_POST['search_b_id'];
    
    // ตรวจสอบว่ามีการยืมหนังสือที่ตรงกันหรือไม่
    $sql = "SELECT * FROM tb_borrow_book WHERE b_id = '$search_b_id' AND br_date_rt = '0000-00-00'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $details[] = $row;
        }
    } else {
        $details[] = ['message' => 'ไม่พบข้อมูลการยืมหนังสือ'];
    }
}

// ปิดการเชื่อมต่อ
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Return Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>ค้นหาข้อมูลการคืนหนังสือ</h1>
    
    <?php if (isset($details) && !empty($details)): ?>
        <script>
            window.location.href = 'manage_borrow_return.php';
        </script>
    <?php else: ?>
        <p>ไม่พบข้อมูลการยืมหนังสือ</p>
        <a href="manage_borrow_return.php">กลับไปที่หน้าหลัก</a>
    <?php endif; ?>
</body>
</html>
