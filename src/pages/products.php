<?php
require_once("../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT p.*, c.name as category_name
            FROM products p 
            LEFT JOIN categories c on p.category_id = c.id
            WHERE p.is_active = 1";

$params = [];

if (!empty($search)) {
    $query .= " AND p.name LIKE ?";
    $params[] = "%{$search}%"; 
}

if (!empty($category_id)) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}
$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query); 
$stmt->execute($params); 
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
    .page-container > * {
        grid-row: auto !important; 
    }
</style>

<main class="content">
    <section class="content-section">
        <h2 class="section-title">
            <?php echo $search ? "Kết quả tìm kiếm: '" . htmlspecialchars($search) . "'" : "Tất cả sản phẩm"; ?>
        </h2>
        
        <div class="products-grid">
            <?php if ($stmt->rowCount() > 0): ?>
                <?php while ($product = $stmt->fetch(PDO::FETCH_ASSOC)):
                    $final_price = $product['sale_price'] ?: $product['price']; 
                    $has_discount = $product['sale_price'] && $product['sale_price'] < $product['price']; 
                    $in_stock = $product['stock'] > 0; 
                    $discount_percent = $has_discount ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;
                ?>
                    <div class="product-card">
                        <?php if ($has_discount): ?>
                            <div class="discount-badge">Giảm <?php echo $discount_percent; ?>%</div>
                        <?php endif; ?>
                        
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-image-wrapper">
                            <img src="../assets/images/<?php echo $product['image']; ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="product-image"
                                onerror="this.src='../assets/images/logo.png'">
                        </a>

                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>
                            <p class="product-category">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </p>
                            
                            <div class="product-price">
                                <span class="current-price">
                                    <?php echo number_format($final_price, 0, ',', '.'); ?> đ
                                </span>
                                <?php if ($has_discount): ?>
                                    <span class="old-price">
                                        <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="add-to-cart <?php echo !$in_stock ? 'disabled' : ''; ?>"
                                <?php echo !$in_stock ? 'disabled' : ''; ?>
                                data-product-id="<?php echo $product['id']; ?>">
                                <?php echo $in_stock ? 'THÊM VÀO GIỎ' : 'HẾT HÀNG'; ?>
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                    <a href="products.php" style="color: #2E7D32; font-weight: bold;">Xem tất cả sản phẩm</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<script src="../assets/js/scrpits.js"></script>