<?php
require_once 'database.php';
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Kết nối thành công rồi!! <br>";
    try {
        $query = "SELECT* FROM categories";
        $a = $db->prepare($query); // bien nay de minh tao ra luu cau lenh truy van da chuan bi tu cau SQL
        // luc nay moi thu thtuc thi cau lenh
        $a->execute();
        //luc nay kq truy van luu trong bien a
        echo "<h3> Danh sach Categories trong database</h3>";
        // thuc hien lap qua tung dong den khi het du lieu
        while ($row = $a->fetch(PDO::FETCH_ASSOC)) {
            // no tra ve kq la mang ket hop key la ten cot
            echo "-" . $row['name'] . "<br>";
        }
    } catch (PDOException $err) {
        echo "Lỗi truy vấn " . $err->getMessage();
    }
}
