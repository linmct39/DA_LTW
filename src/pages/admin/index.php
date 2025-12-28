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


$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.name LIKE ? 
          ORDER BY p.id DESC";

$stmt = $db->prepare($query);
$stmt->execute(["%$search%"]);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Admin</title>
 
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
       
        .admin-container { max-width: 1200px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #2E7D32; padding-bottom: 15px; }
        
        .btn-add { background: #2E7D32; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
      
        .btn-cat { background: #1976D2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px; }
        
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th, .admin-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        .admin-table th { background: #f8f9fa; color: #2E7D32; }
        .thumb-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .action-btn { padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; margin-right: 5px; color: white; }
        .btn-edit { background: #fbc02d; color: #333; }
        .btn-delete { background: #d32f2f; }
        .search-box { display: flex; gap: 10px; }
        .search-box input { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
   
    <?php include '../../includes/header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h2>📦 Quản Lý Sản Phẩm</h2>
            <div class="search-box">
                <form method="GET" style="display: flex; gap: 5px;">
                    <input type="text" name="search" placeholder="Tìm tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-add" style="border:none; cursor:pointer;">🔍</button>
                </form>
                <a href="add.php" class="btn-add">+ Thêm SP Mới</a>
            </div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Kho</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td>
            
                        <img src="/assets/images/<?php echo $row['image']; ?>" class="thumb-img" onerror="this.src='/assets/images/logo.png'">
                    </td>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td>
                        <?php if ($row['sale_price']): ?>
                            <del style="color:#999; font-size:12px;"><?php echo number_format($row['price']); ?></del><br>
                            <span style="color:#d32f2f; font-weight:bold;"><?php echo number_format($row['sale_price']); ?> đ</span>
                        <?php else: ?>
                            <?php echo number_format($row['price']); ?> đ
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="color: <?php echo $row['stock'] > 0 ? 'green' : 'red'; ?>">
                            <?php echo $row['stock']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit">Sửa</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>