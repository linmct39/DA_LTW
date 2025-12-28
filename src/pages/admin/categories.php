<?php
session_start();
require_once("../../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_cat'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        $stmt = $db->prepare("INSERT INTO categories (name, description, created_at) VALUES (?, ?, NOW())");
        if ($stmt->execute([$name, $description])) {
            $message = "Thêm danh mục thành công!";
        } else {
            $message = "Lỗi khi thêm danh mục.";
        }
    } else {
        $message = "Tên danh mục không được để trống.";
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $check = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        $message = "Không thể xóa: Danh mục này đang chứa sản phẩm!";
    } else {
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: categories.php"); // Refresh để mất tham số delete
        exit;
    }
}


$stmt = $db->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Danh Mục</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .admin-container { max-width: 1200px; margin: 40px auto; padding: 20px; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); height: fit-content; }
        .card h3 { margin-bottom: 15px; color: #2E7D32; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-submit { width: 100%; background: #2E7D32; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f9f9f9; color: #2E7D32; }
        .btn-del { color: red; text-decoration: none; font-size: 13px; border: 1px solid red; padding: 3px 8px; border-radius: 4px; }
        .btn-del:hover { background: red; color: white; }
        .msg { padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .success { background: #e8f5e9; color: green; }
        .error { background: #ffebee; color: red; }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="admin-container">
       
        <div class="card">
            <h3>+ Thêm Danh Mục Mới</h3>
            <?php if ($message): ?>
                <div class="msg <?php echo strpos($message, 'thành công') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Tên danh mục</label>
                    <input type="text" name="name" required placeholder="Ví dụ: Hải sản tươi sống">
                </div>
                <div class="form-group">
                    <label>Mô tả (Tùy chọn)</label>
                    <textarea name="description" rows="3" placeholder="Mô tả ngắn..."></textarea>
                </div>
                <button type="submit" name="add_cat" class="btn-submit">THÊM NGAY</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="index.php" style="color: #666;">&larr; Quay lại Quản lý Sản phẩm</a>
            </div>
        </div>


        <div class="card">
            <h3>Danh Sách Danh Mục</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td style="font-weight: bold;"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td style="color: #666; font-size: 14px;"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="categories.php?delete=<?php echo $row['id']; ?>" class="btn-del" onclick="return confirm('Bạn chắc chắn muốn xóa?');">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>