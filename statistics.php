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

// ตรวจสอบการค้นหา
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $search_query = $conn->real_escape_string($search_query);
    $sql = "SELECT * FROM tb_borrow_book 
            WHERE b_id LIKE '%$search_query%' 
            OR m_user LIKE '%$search_query%' 
            OR br_date_br LIKE '%$search_query%' 
            ORDER BY br_date_br DESC";
} else {
    $sql = "SELECT * FROM tb_borrow_book ORDER BY br_date_br DESC";
}

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #343a40;
            margin-bottom: 20px;
        }

        .button-group {
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .box {
            display: inline-block;
            width: 220px;
            height: 120px;
            margin: 15px;
            background-color: #ffffff;
            border: 1px solid #ced4da;
            text-align: center;
            line-height: 60px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .box h2 {
            margin: 0;
            padding: 0;
            font-size: 16px;
            color: #6c757d;
            position: absolute;
            top: 30px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .box:hover h2 {
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>ข้อมูลสถิติการยืม-คืนหนังสือ</h1>
    <div class="button-group">
        <a href="index.php" class="button">กลับไปที่หน้าหลัก</a>
    </div>

    <!-- แสดงข้อมูลสถิติ -->
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

    // แสดงกล่องข้อมูลจำนวนหนังสือที่กำลังถูกยืมอยู่
    $sql_borrowed_books = "SELECT COUNT(*) as total_borrowed FROM tb_borrow_book WHERE br_date_rt = '0000-00-00' OR br_date_rt IS NULL";
    $result_borrowed_books = $conn->query($sql_borrowed_books);
    $borrowed_count = $result_borrowed_books->fetch_assoc()['total_borrowed'];

    echo "<div class='box'><h2>หนังสือที่กำลังถูกยืม</h2>จำนวน: $borrowed_count</div>";

    // สร้างคำสั่ง SQL เพื่อดึงชื่อของทุกตารางในฐานข้อมูล
    $sql = "SHOW TABLES";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array()) {
            $tableName = $row[0];
            
            // ดึงจำนวนข้อมูลจากตารางนั้นๆ
            $sql_count = "SELECT COUNT(*) as total FROM $tableName";
            $result_count = $conn->query($sql_count);
            $count = $result_count->fetch_assoc()['total'];

            // เปลี่ยนชื่อแสดงผลตามที่ต้องการ
            switch ($tableName) {
                case 'tb_member':
                    $displayName = "สมาชิก";
                    break;
                case 'tb_borrow_book':
                    $displayName = "บันทึก ยืม-คืน หนังสือ";
                    break;
                case 'tb_book':
                    $displayName = "หนังสือทั้งหมด";
                    break;
                default:
                    $displayName = "ตาราง: $tableName";
            }

            // แสดงกล่องสี่เหลี่ยมที่มีจำนวนข้อมูล
            echo "<div class='box'><h2>$displayName</h2>จำนวน: $count</div>";
        }
    } else {
        echo "ไม่พบตารางในฐานข้อมูลนี้.";
    }

    // ปิดการเชื่อมต่อ
    $conn->close();
    ?>
</body>
</html>



