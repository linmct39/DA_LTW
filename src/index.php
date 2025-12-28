<?php
include 'includes/header.php'; 
?>


<style>
    .banner {
        position: relative;
        overflow: hidden;
        background: #333; 

        min-height: 600px; 
        width: 100%;
        padding: 80px 20px; 
        
     
        flex-shrink: 0;
        
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff;
    }
    
    .banner-slides {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1; 
    }
    
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
    }
    
    .slide.active {
        opacity: 1;
    }
    
    .banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); 
        z-index: 2;
    }

    .banner-content {
        position: relative;
        z-index: 10; 
        max-width: 800px;
        width: 100%;
        padding: 20px;
    }
    
    .banner-content h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .banner-content p {
        font-size: 1.2rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

   
    .banner-btn {
        display: inline-block;
        background: #4CAF50;
        color: white !important;
        padding: 15px 40px;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        border: 2px solid transparent;
        
    
        position: relative;
        z-index: 20; 
    }

    .banner-btn:hover {
        background: transparent;
        border-color: #4CAF50;
        color: #4CAF50 !important;
        transform: translateY(-3px);
    }
    
    .banner-content > * {
        animation: fadeInUp 1s ease-out;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .content {
        position: relative;
        z-index: 5; 
        background-color: #f8f9fa; 
        padding-top: 40px; 
    }

    @media (max-width: 768px) {
        .banner {
            min-height: 400px;
        }
        .banner-content h1 { font-size: 2rem; }
    }
</style>

<section class="banner">

    <div class="banner-slides">
        <div class="slide active" style="background-image: url('assets/images/banner1.jpg');"></div>
        <div class="slide" style="background-image: url('assets/images/banner2.jpg');"></div>
    </div>
    
    
    <div class="banner-overlay"></div>

    <div class="banner-content">
        <h1>Thực Phẩm Hữu Cơ Chất Lượng Cao</h1>
        <p>Cam kết 100% tự nhiên, an toàn cho sức khỏe gia đình bạn</p>
        <a href="pages/products.php" class="banner-btn">Mua Ngay</a>
    </div>
</section>

<main class="content">

    <section class="content-section">
        <h2 class="section-title">Danh Mục Sản Phẩm</h2>
        <div class="categories-grid">
            <?php
  
            $cat_query = "SELECT * FROM categories";
            $cat_stmt = $db->prepare($cat_query);
            $cat_stmt->execute();
            
            while ($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="category-card">
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                <p><?php echo htmlspecialchars($category['description']); ?></p>
                <a href="pages/products.php?category=<?php echo $category['id']; ?>">Xem Sản Phẩm</a>
            </div>
            <?php endwhile; ?>
        </div>
    </section>


    <section class="content-section">
        <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
        <div class="products-grid">
            <?php
         
            $product_query = "SELECT p.*, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            WHERE p.is_active = 1 
                            ORDER BY p.created_at DESC 
                            LIMIT 6";
            $product_stmt = $db->prepare($product_query);
            $product_stmt->execute();
            
            while ($product = $product_stmt->fetch(PDO::FETCH_ASSOC)):
                $final_price = $product['sale_price'] ?: $product['price'];
                $has_discount = $product['sale_price'] && $product['sale_price'] < $product['price'];
                $in_stock = $product['stock'] > 0;
                $discount_percent = $has_discount ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;
            ?>
            <div class="product-card">
                <?php if ($has_discount): ?>
                <div class="discount-badge">GIẢM <?php echo $discount_percent; ?>%</div>
                <?php endif; ?>
                
                <a href="pages/product_detail.php?id=<?php echo $product['id']; ?>" class="product-image-wrapper">
                    <img src="assets/images/<?php echo $product['image']; ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="product-image"
                         onerror="this.src='assets/images/logo.png'">
                </a>
            
                <div class="product-info">
                    
                    <h3 class="product-name">
                        <a href="pages/product_detail.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h3>
                    <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                    
                    <div class="product-price">
                        <span class="current-price"><?php echo number_format($final_price, 0, ',', '.'); ?>đ</span>
                        <?php if ($has_discount): ?>
                        <span class="old-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-stock">
                        <?php echo $in_stock ? "Còn {$product['stock']} {$product['unit']}" : "Hết hàng"; ?>
                    </div>
                    
                    <?php if ($product['is_organic']): ?>
                    <div class="organic-badge">HỮU CƠ</div>
                    <?php endif; ?>
                    
                    <button class="add-to-cart <?php echo !$in_stock ? 'disabled' : ''; ?>" 
                            <?php echo !$in_stock ? 'disabled' : ''; ?>
                            data-product-id="<?php echo $product['id']; ?>">
                        <?php echo $in_stock ? 'Thêm Vào Giỏ' : 'Hết Hàng'; ?>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="view-all">
            <a href="pages/products.php" class="view-all-btn">Xem Tất Cả Sản Phẩm</a>
        </div>
    </section>


    <section class="content-section">
        <h2 class="section-title">Vì Sao Chọn Organic Shop?</h2>
        <div class="categories-grid">
            <div class="category-card">
                <h3>🌱 100% Hữu Cơ</h3>
                <p>Tất cả sản phẩm đều được chứng nhận hữu cơ, không sử dụng hóa chất độc hại</p>
            </div>
            
            <div class="category-card">
                <h3>🚚 Giao Hàng Nhanh</h3>
                <p>Giao hàng tận nơi trong vòng 2 giờ, đảm bảo độ tươi ngon của sản phẩm</p>
            </div>
            
            <div class="category-card">
                <h3>💰 Giá Tốt Nhất</h3>
                <p>Cam kết giá cạnh tranh nhất thị trường với chất lượng tốt nhất</p>
            </div>
            
            <div class="category-card">
                <h3>✅ Đảm Bảo Chất Lượng</h3>
                <p>Hoàn tiền 100% nếu sản phẩm không đúng như mô tả</p>
            </div>
        </div>
    </section>
</main>
        
<?php include 'includes/footer.php'; ?>
<script src="/assets/js/scrpits.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.banner-slides .slide');
        if (slides.length > 0) {
            let currentSlide = 0;
            function nextSlide() {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }
            setInterval(nextSlide, 5000);
        }
    });
</script>
</body>
</html>