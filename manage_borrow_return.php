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

$message = "";
$details = [];

// การประมวลผลฟอร์มการยืมหนังสือ
if (isset($_POST['borrow'])) {
    $b_id = $conn->real_escape_string($_POST['b_id']);
    $m_user = $conn->real_escape_string($_POST['m_user']);
    $borrow_date = date('Y-m-d');  // วันที่ปัจจุบัน
    
    // ตรวจสอบว่ามีหนังสือและผู้ใช้ในฐานข้อมูล
    $sql_check_book = "SELECT * FROM tb_book WHERE b_id = '$b_id'";
    $sql_check_user = "SELECT * FROM tb_member WHERE m_user = '$m_user'";
    $result_book = $conn->query($sql_check_book);
    $result_user = $conn->query($sql_check_user);
    
    if ($result_book->num_rows > 0 && $result_user->num_rows > 0) {
        // ตรวจสอบว่ามีการยืมหนังสืออยู่ก่อนหน้านี้
        $sql_check_borrow = "SELECT * FROM tb_borrow_book WHERE b_id = '$b_id' AND m_user = '$m_user' AND br_date_rt = '0000-00-00'";
        $result_borrow = $conn->query($sql_check_borrow);
        
        if ($result_borrow->num_rows == 0) {
            $sql = "INSERT INTO tb_borrow_book (br_date_br, br_date_rt, b_id, m_user, br_fine) 
                    VALUES ('$borrow_date', '0000-00-00', '$b_id', '$m_user', 0)";
            
            if ($conn->query($sql) === TRUE) {
                $message = "ข้อมูลการยืมหนังสือถูกบันทึกเรียบร้อยแล้ว";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $message = "หนังสือเล่มนี้ถูกยืมอยู่แล้ว";
        }
    } else {
        $message = "ไม่พบข้อมูลหนังสือหรือผู้ใช้";
    }
}

// การประมวลผลฟอร์มการคืนหนังสือ
if (isset($_POST['return'])) {
    if (isset($_POST['b_id']) && isset($_POST['m_user'])) {
        $b_id = $conn->real_escape_string($_POST['b_id']);
        $m_user = $conn->real_escape_string($_POST['m_user']);
        $return_date = date('Y-m-d');
        $fine = !empty($_POST['fine']) ? $conn->real_escape_string($_POST['fine']) : 0;
        
        // ตรวจสอบว่ามีข้อมูลการยืมหนังสือที่ต้องการคืนในฐานข้อมูลหรือไม่
        $sql = "UPDATE tb_borrow_book SET br_date_rt = '$return_date', br_fine = '$fine' 
                WHERE b_id = '$b_id' AND m_user = '$m_user' AND br_date_rt = '0000-00-00'";
        
        if ($conn->query($sql) === TRUE) {
            $message = "ข้อมูลการคืนหนังสือถูกบันทึกเรียบร้อยแล้ว";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "ข้อมูลที่จำเป็นสำหรับการคืนหนังสือขาดหาย";
    }
}

// การค้นหาข้อมูลการคืนหนังสือ
if (isset($_POST['search'])) {
    $search_b_id = $conn->real_escape_string($_POST['search_b_id']);
    
    // ตรวจสอบว่ามีข้อมูลหนังสือที่กำลังยืมอยู่ในฐานข้อมูลหรือไม่
    $sql = "SELECT * FROM tb_borrow_book WHERE b_id = '$search_b_id' AND br_date_rt = '0000-00-00'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $details[] = $row;
        }
    } else {
        $details[] = ['message' => 'ไม่พบข้อมูลหนังสือที่ถูกยืมอยู่'];
    }
} else {
    $details[] = ['message' => 'โปรดระบุข้อมูลที่ต้องการค้นหา'];
}

// การค้นหาข้อมูลผู้ใช้และหนังสือ
if (isset($_GET['action'])) {
    $action = $conn->real_escape_string($_GET['action']);
    
    if ($action === 'get_user_info' && isset($_GET['m_user'])) {
        $m_user = $conn->real_escape_string($_GET['m_user']);
        $sql = "SELECT * FROM tb_member WHERE m_user = '$m_user'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['status' => 'success', 'name' => $user['m_name']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลผู้ใช้']);
        }
    } elseif ($action === 'get_book_info' && isset($_GET['b_id'])) {
        $b_id = $conn->real_escape_string($_GET['b_id']);
        $sql = "SELECT * FROM tb_book WHERE b_id = '$b_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            echo json_encode(['status' => 'success', 'name' => $book['b_name']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลหนังสือ']);
        }
    }
    
    exit();
}



// ปิดการเชื่อมต่อ
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Borrowing and Returning</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function showForm(formType) {
            document.getElementById('borrow-form').style.display = formType === 'borrow' ? 'block' : 'none';
            document.getElementById('return-form').style.display = formType === 'return' ? 'block' : 'none';
        }

        function showSuccessMessage(message) {
            alert(message);
            if (message === "ข้อมูลการคืนหนังสือถูกบันทึกเรียบร้อยแล้ว") {
                window.location.href = 'manage_borrow_return.php'; // กลับไปที่ฟอร์มหลัก
            }
        }

        function fetchUserInfo() {
            var mUser = $('#m_user').val();
            if (mUser) {
                $.ajax({
                    url: 'manage_borrow_return.php',
                    type: 'GET',
                    data: { action: 'get_user_info', m_user: mUser },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#user_info').text(response.name);
                            $('#user_error').text('');
                        } else {
                            $('#user_info').text('');
                            $('#user_error').text(response.message);
                        }
                    }
                });
            } else {
                $('#user_info').text('');
                $('#user_error').text('');
            }
        }
        
        function fetchBookInfo() {
            var bId = $('#b_id').val();
            if (bId) {
                $.ajax({
                    url: 'manage_borrow_return.php',
                    type: 'GET',
                    data: { action: 'get_book_info', b_id: bId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#book_info').text(response.name);
                            $('#book_error').text('');
                        } else {
                            $('#book_info').text('');
                            $('#book_error').text(response.message);
                        }
                    }
                });
            } else {
                $('#book_info').text('');
                $('#book_error').text('');
            }
        }
    </script>
</head>
<body>
    <!-- ฟอร์มการยืมหนังสือ -->
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Borrowing and Returning</title>
    <link rel="stylesheet" href="css.css">
    <style>
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function showForm(formType) {
            document.getElementById('borrow-form').style.display = formType === 'borrow' ? 'block' : 'none';
            document.getElementById('return-form').style.display = formType === 'return' ? 'block' : 'none';
        }

        function showSuccessMessage(message) {
            alert(message);
            if (message === "ข้อมูลการคืนหนังสือถูกบันทึกเรียบร้อยแล้ว") {
                window.location.href = 'manage_borrow_return.php'; // กลับไปที่ฟอร์มหลัก
            }
        }

        function fetchUserInfo() {
            var mUser = $('#m_user').val();
            if (mUser) {
                $.ajax({
                    url: 'manage_borrow_return.php',
                    type: 'GET',
                    data: { action: 'get_user_info', m_user: mUser },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#user_info').text(response.name);
                            $('#user_error').text('');
                        } else {
                            $('#user_info').text('');
                            $('#user_error').text(response.message);
                        }
                    }
                });
            } else {
                $('#user_info').text('');
                $('#user_error').text('');
            }
        }
        
        function fetchBookInfo() {
            var bId = $('#b_id').val();
            if (bId) {
                $.ajax({
                    url: 'manage_borrow_return.php',
                    type: 'GET',
                    data: { action: 'get_book_info', b_id: bId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#book_info').text(response.name);
                            $('#book_error').text('');
                        } else {
                            $('#book_info').text('');
                            $('#book_error').text(response.message);
                        }
                    }
                });
            } else {
                $('#book_info').text('');
                $('#book_error').text('');
            }
        }
    </script>
</head>
<body>
    <h1>จัดการการยืมและคืนหนังสือ</h1>
    <a href="index.php" class="back-button">กลับไปที่หน้าแรก</a>
    <!-- แสดงข้อความผลลัพธ์ -->
    <p class="message"><?php echo $message; ?></p>

    <!-- ฟอร์มการยืมหนังสือ -->
    <button onclick="showForm('borrow')">ยืมหนังสือ</button>
    <button onclick="showForm('return')">คืนหนังสือ</button>

    <!-- ฟอร์มการยืมหนังสือ -->
    <div id="borrow-form" style="display:none;">
        <h2>ฟอร์มการยืมหนังสือ</h2>
        <form method="POST" action="">
            <label for="b_id">รหัสหนังสือ:</label>
            <input type="text" id="b_id" name="b_id" onkeyup="fetchBookInfo()">
            <span id="book_info"></span>
            <span id="book_error" class="error"></span>
            <br>
            <label for="m_user">รหัสผู้ใช้:</label>
            <input type="text" id="m_user" name="m_user" onkeyup="fetchUserInfo()">
            <span id="user_info"></span>
            <span id="user_error" class="error"></span>
            <br>
            <input type="submit" name="borrow" value="ยืมหนังสือ">
        </form>
    </div>

    <!-- ฟอร์มการคืนหนังสือ -->
    <div id="return-form" style="display:none;">
        <h2>ฟอร์มการคืนหนังสือ</h2>
        <form method="POST" action="">
            <label for="b_id">รหัสหนังสือ:</label>
            <input type="text" id="b_id" name="b_id" onkeyup="fetchBookInfo()">
            <span id="book_info"></span>
            <span id="book_error" class="error"></span>
            <br>
            <label for="m_user">รหัสผู้ใช้:</label>
            <input type="text" id="m_user" name="m_user" onkeyup="fetchUserInfo()">
            <span id="user_info"></span>
            <span id="user_error" class="error"></span>
            <br>
            <label for="fine">ค่าปรับ:</label>
            <input type="text" id="fine" name="fine">
            <br>
            <input type="submit" name="return" value="คืนหนังสือ">
        </form>
    </div>

    <!-- ฟอร์มการค้นหาข้อมูลการคืนหนังสือ -->
    <h2>ค้นหาข้อมูลการคืนหนังสือ</h2>
    <form method="POST" action="">
        <label for="search_b_id">รหัสหนังสือ:</label>
        <input type="text" id="search_b_id" name="search_b_id">
        <input type="submit" name="search" value="ค้นหา">
    </form>

    <?php if (!empty($details)) { ?>
    <h3>ข้อมูลการคืนหนังสือ</h3>
    <table>
        <thead>
            <tr>
                <th>รหัสหนังสือ</th>
                <th>รหัสผู้ใช้</th>
                <th>วันที่ยืม</th>
                <th>วันที่คืน</th>
                <th>ค่าปรับ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $detail) { ?>
                <?php if (isset($detail['message'])) { ?>
                    <tr>
                        <td colspan="5"><?php echo $detail['message']; ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td><?php echo $detail['b_id']; ?></td>
                        <td><?php echo $detail['m_user']; ?></td>
                        <td><?php echo $detail['br_date_br']; ?></td>
                        <td><?php echo $detail['br_date_rt']; ?></td>
                        <td><?php echo $detail['br_fine']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>
</body>
</html>

