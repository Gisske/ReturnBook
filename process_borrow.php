<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "your_db_username", "your_db_password", "db_library");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีค่าที่คาดหวังใน $_POST
$m_user = isset($_POST['m_user']) ? $_POST['m_user'] : null;
$b_id = isset($_POST['b_id']) ? $_POST['b_id'] : null;
$br_date_rt = isset($_POST['br_date_rt']) ? $_POST['br_date_rt'] : '0000-00-00';
$br_fine = isset($_POST['br_fine']) ? $_POST['br_fine'] : 0;

// ตรวจสอบค่าที่ได้รับจากฟอร์ม
if ($m_user === null || $b_id === null) {
    die("ข้อมูลไม่ครบถ้วน");
}

// เตรียมคำสั่ง SQL
$sql = "INSERT INTO borrow_table (m_user, b_id, br_date_br, br_date_rt, br_fine) VALUES (?, ?, NOW(), ?, ?)";

// ใช้ prepared statement
$stmt = $conn->prepare($sql);

// ตรวจสอบการเตรียมคำสั่ง SQL
if ($stmt === false) {
    die("เตรียมคำสั่ง SQL ไม่สำเร็จ: " . $conn->error);
}

// ติดตั้งชนิดข้อมูลของ parameters: s = string, i = integer, d = double
$stmt->bind_param("ssis", $m_user, $b_id, $br_date_rt, $br_fine);

// ตรวจสอบการดำเนินการ
if ($stmt->execute()) {
    echo "บันทึกข้อมูลสำเร็จ";
} else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
