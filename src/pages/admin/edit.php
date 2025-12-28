<?php
session_start();
require_once("../../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");


if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];


$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) { die("Không tìm thấy sản phẩm"); }

$error_message = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    
    
    $price = str_replace([',', ' '], '', $_POST['price']);
    
  
    $sale_price_input = str_replace([',', ' '], '', $_POST['sale_price']);
    $sale_price = ($sale_price_input !== '') ? $sale_price_input : NULL;
    
    $stock = $_POST['stock'];
    $unit = $_POST['unit'];
    $description = $_POST['description'];
    
 
    $image = $product['image'];

 
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/images/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $filename;
        }
    }

    $sql = "UPDATE products SET name=?, category_id=?, price=?, sale_price=?, stock=?, unit=?, description=?, image=? WHERE id=?";
    $stmt = $db->prepare($sql);
    
    try {
        if ($stmt->execute([$name, $category_id, $price, $sale_price, $stock, $unit, $description, $image, $id])) {
            header("Location: index.php");
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            $error_message = "Lỗi SQL: " . $errorInfo[2];
        }
    } catch (Exception $e) {
        $error_message = "Lỗi hệ thống: " . $e->getMessage();
    }
}


$cats = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);


$units = ['kg', 'gam', 'hộp', 'gói', 'bó', 'trái', 'chai', 'thùng', 'lít'];
?>


<?php include '../../includes/header.php'; ?>


<style>
  
    .page-container {
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh !important;
    }
    .content {
        flex: 1 !important; 
        width: 100%;
    }

    .form-container { 
        max-width: 800px; 
        margin: 40px auto; 
        padding: 30px; 
        background: #fff; 
        border-radius: 10px; 
        box-shadow: 0 0 20px rgba(0,0,0,0.1); 
    }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
    .form-group input, .form-group select, .form-group textarea { 
        width: 100%; 
        padding: 10px; 
        border: 1px solid #ddd; 
        border-radius: 5px; 
    }
    .btn-submit { 
        background: #fbc02d; 
        color: #333; 
        border: none; 
        padding: 12px 30px; 
        border-radius: 5px; 
        cursor: pointer; 
        font-weight: bold; 
        width: 100%; 
    }
    .btn-submit:hover { background: #f9a825; }
    .current-img { 
        width: 100px; 
        margin-top: 10px; 
        border: 1px solid #ddd; 
        border-radius: 5px; 
        padding: 2px;
    }
    .error-msg { 
        color: red; 
        background: #ffebee; 
        padding: 10px; 
        margin-bottom: 20px; 
        border-radius: 5px; 
        text-align: center; 
    }
    .flex-row { display: flex; gap: 20px; }
    .flex-col { flex: 1; }
    
    /* Responsive cho mobile */
    @media (max-width: 600px) {
        .flex-row { flex-direction: column; gap: 0; }
    }
</style>

<!-- 3. Nội dung chính -->
<main class="content">
    <div class="form-container">
        <h2 style="margin-bottom: 20px; text-align: center; color: #2E7D32;">Cập Nhật Sản Phẩm #<?php echo $id; ?></h2>
        
        <?php if ($error_message): ?>
            <div class="error-msg">
                <strong>Không cập nhật được!</strong><br>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên sản phẩm</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Danh mục</label>
                <select name="category_id" required>
                    <?php foreach ($cats as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex-row">
                <div class="form-group flex-col">
                    <label>Giá gốc (VNĐ)</label>
                    <input type="number" name="price" value="<?php echo $product['price']; ?>" min="0" step="any" required>
                </div>
                <div class="form-group flex-col">
                    <label>Giá khuyến mãi (VNĐ)</label>
                    <input type="number" name="sale_price" value="<?php echo $product['sale_price']; ?>" min="0" step="any">
                </div>
            </div>

          
            <div class="flex-row">
                <div class="form-group flex-col" style="flex: 2;">
                    <label>Số lượng</label>
                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
                </div>
                <div class="form-group flex-col" style="flex: 1;">
                    <label>Đơn vị tính</label>
                    <select name="unit" required>
                        <?php foreach ($units as $u): ?>
                            <option value="<?php echo $u; ?>" <?php echo (isset($product['unit']) && $product['unit'] == $u) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($u); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Hình ảnh</label>
                <input type="file" name="image" accept="image/*">
                <br>
                <img src="../../assets/images/<?php echo $product['image']; ?>" class="current-img" onerror="this.src='../../assets/images/logo.png'">
                <p style="font-size: 13px; color: #666;">(Để trống nếu không muốn thay đổi ảnh)</p>
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <button type="submit" class="btn-submit">CẬP NHẬT</button>
            <br><br>
            <a href="index.php" style="display: block; text-align: center; color: #666; text-decoration: none;">&larr; Quay lại danh sách</a>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>