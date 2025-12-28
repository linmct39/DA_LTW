<?php
require_once("../config/database.php");

$database = new Database();
$db = $database->getConnection();

$db->exec("set names utf8");

$id = isset($_GET['id']) ? $_GET['id'] : die('Lỗi: Không tìm thấy ID sản phẩm.');

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = ? LIMIT 0,1";

$stmt = $db->prepare($query);

$stmt->bindParam(1, $id);

$stmt->execute();

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Sản phẩm không tồn tại.');
}

$final_price = $product['sale_price'] ?: $product['price'];

$has_discount = $product['sale_price'] && $product['sale_price'] < $product['price'];

$discount_percent = $has_discount ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;
?>

<?php include '../includes/header.php'; ?>

<style>
    .page-container {
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh !important;
    }
    .content {
        flex: 1 !important;
    }

    .detail-container {
        max-width: 1100px;
        margin: 20px auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .product-detail-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 20px;
    }

    .detail-image-box {
        width: 100%;
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        background: #fff;
    }

    .detail-image {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: contain;
    }

    .discount-badge-detail {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #ff4757;
        color: white;
        padding: 5px 12px;
        font-weight: bold;
        border-radius: 5px;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .detail-info h1 {
        font-size: 28px;
        color: #1B3A1B;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .detail-category {
        color: #666;
        font-size: 14px;
        margin-bottom: 20px;
        display: inline-block;
        background: #f0f0f0;
        padding: 4px 10px;
        border-radius: 15px;
    }

    .detail-price {
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .detail-price span:first-child {
        font-size: 26px;
        color: #d32f2f;
        font-weight: bold;
    }

    .detail-old-price {
        font-size: 18px;
        color: #999;
        text-decoration: line-through;
    }

    .detail-desc {
        line-height: 1.8;
        color: #444;
        margin-bottom: 30px;
    }

    .add-cart-form {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-top: 20px;
    }

    .quantity-input {
        width: 70px;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        text-align: center;
        font-weight: bold;
    }

    .btn-add-cart-big {
        background: linear-gradient(135deg, #2E7D32, #4CAF50);
        color: white;
        padding: 12px 35px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3);
        text-transform: uppercase;
    }

    .btn-add-cart-big:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(46, 125, 50, 0.4);
    }

    @media (max-width: 768px) {
        .product-detail-wrapper {
            grid-template-columns: 1fr; 
            gap: 20px;
        }
        .detail-image {
            max-height: 300px;
        }
        .detail-info h1 {
            font-size: 24px;
        }
    }
</style>

<main class="content">
    <div class="detail-container">
        
        <div style="margin-bottom: 10px; color: #888; font-size: 14px;">
            <a href="products.php" style="color: #2E7D32; text-decoration: none; font-weight: 600;"> < Quay lại danh sách</a> 
        </div>

        <div class="product-detail-wrapper">
            <div class="detail-image-box">
                <?php if ($has_discount): ?>
                    <div class="discount-badge-detail">
                        Giảm <?php echo $discount_percent; ?>%
                    </div>
                <?php endif; ?>
                
                <img src="../assets/images/<?php echo $product['image']; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="detail-image"
                     onerror="this.src='../assets/images/logo.png'">
            </div>

            <div class="detail-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="detail-category">Danh mục: <?php echo htmlspecialchars($product['category_name']); ?></p>

                <div class="detail-price">
                    <span><?php echo number_format($final_price, 0, ',', '.'); ?> đ</span>
                    <?php if ($has_discount): ?>
                        <span class="detail-old-price"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</span>
                    <?php endif; ?>
                </div>

                <div class="detail-desc">
                    <strong>Mô tả sản phẩm:</strong><br>
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>

                <div style="margin-bottom: 20px;">
                    <strong>Tình trạng:</strong> 
                    <?php if ($product['stock'] > 0): ?>
                        <span style="color: #2E7D32; font-weight: bold;">✔ Còn hàng (<?php echo $product['stock']; ?>)</span>
                    <?php else: ?>
                        <span style="color: #d32f2f; font-weight: bold;">✖ Hết hàng</span>
                    <?php endif; ?>
                </div>

                <?php if ($product['stock'] > 0): ?>
                    <div class="add-cart-form">
                        <label for="quantity" style="font-weight: 600; color: #555;">Số lượng:</label>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                        
                        <button class="btn-add-cart-big add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                            Thêm vào giỏ
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/scrpits.js"></script>