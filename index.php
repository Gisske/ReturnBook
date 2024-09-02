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
    <title>Library Borrowing Records</title>
    <style>
        /* ตั้งค่าพื้นฐานสำหรับหน้าเว็บ */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 300px;
            margin-right: 10px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        button[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #dee2e6;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: 16px;
            background-color: #ffffff;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        th {
            background-color: #007bff;
            color: #ffffff;
        }

        tbody tr:hover {
            background-color: #e9ecef;
            color: #007bff;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .button:active {
            background-color: #1e7e34;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <h1>ข้อมูลการยืม-คืนหนังสือ</h1>
    
    <form method="POST" action="">
        <input type="text" name="search_query" placeholder="ค้นหาตามชื่อหนังสือ หรือชื่อผู้ยืม-คืน" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" name="search">ค้นหา</button>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>วันที่ยืม</th>
                <th>วันที่คืน</th>
                <th>รหัสหนังสือ</th>
                <th>ชื่อผู้ใช้</th>
                <th>ค่าปรับ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["br_date_br"] . "</td>";
                    echo "<td>" . ($row["br_date_rt"] == '0000-00-00' ? 'ยังไม่คืน' : $row["br_date_rt"]) . "</td>";
                    echo "<td>" . $row["b_id"] . "</td>";
                    echo "<td>" . $row["m_user"] . "</td>";
                    echo "<td>" . $row["br_fine"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>ไม่มีข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <div class="button-group">
        <a href="manage_borrow_return.php" class="button">จัดการข้อมูลการยืม-คืน</a>
        <a href="statistics.php" class="button">ข้อมูลสถิติ</a>
    </div>
</body>
</html>

<?php
// ปิดการเชื่อมต่อ
$conn->close();
?>
