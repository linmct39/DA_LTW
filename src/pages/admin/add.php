<?php
session_start();
require_once("../../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : NULL;
    $stock = $_POST['stock'];
    $unit = $_POST['unit']; 
    $description = $_POST['description'];
    
   
    $image = "logo.png"; 
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/images/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $filename;
        }
    }

    $sql = "INSERT INTO products (name, category_id, price, sale_price, stock, unit, description, image, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    $stmt = $db->prepare($sql);
    
    if ($stmt->execute([$name, $category_id, $price, $sale_price, $stock, $unit, $description, $image])) {
        header("Location: index.php");
        exit;
    } else {
        $message = "Lỗi khi thêm sản phẩm!";
    }
}


$cats = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .form-container { max-width: 800px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-submit { background: #2E7D32; color: white; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        
 
        .flex-row { display: flex; gap: 20px; }
        .flex-col { flex: 1; }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="content">
        <div class="form-container">
            <h2 style="color: #2E7D32; margin-bottom: 20px; text-align: center;">Thêm Sản Phẩm Mới</h2>
            
            <?php if ($message): ?>
                <p style="color: red; text-align: center;"><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="name" required placeholder="Ví dụ: Kẹo dẻo trái cây">
                </div>
                
                <div class="form-group">
                    <label>Danh mục</label>
                    <select name="category_id" required>
                        <?php foreach ($cats as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex-row">
                    <div class="form-group flex-col">
                        <label>Giá gốc (VNĐ)</label>
                        <input type="number" name="price" required placeholder="50000">
                    </div>
                    <div class="form-group flex-col">
                        <label>Giá khuyến mãi (VNĐ)</label>
                        <input type="number" name="sale_price" placeholder="Để trống nếu không giảm">
                    </div>
                </div>

       
                <div class="flex-row">
                    <div class="form-group flex-col" style="flex: 2;">
                        <label>Số lượng tồn kho</label>
                        <input type="number" name="stock" value="10" required>
                    </div>
                    <div class="form-group flex-col" style="flex: 1;">
                        <label>Đơn vị tính</label>
                        <select name="unit" required>
                            <option value="kg">Kg</option>
                            <option value="gam">Gam (g)</option>
                            <option value="hộp">Hộp</option>
                            <option value="gói">Gói</option>
                            <option value="bó">Bó</option>
                            <option value="trái">Trái/Quả</option>
                            <option value="chai">Chai</option>
                            <option value="thùng">Thùng</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Hình ảnh</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Mô tả chi tiết</label>
                    <textarea name="description" rows="5"></textarea>
                </div>

                <button type="submit" class="btn-submit">LƯU SẢN PHẨM</button>
                <br><br>
                <a href="index.php" style="display: block; text-align: center; color: #666; text-decoration: none;">&larr; Quay lại danh sách</a>
            </form>
        </div>
    </div>
</body>
</html>