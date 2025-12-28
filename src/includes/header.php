<!-- HEADER -->
<?php 
    require_once __DIR__ . '/../config/database.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($db)) {
        $database = new Database();
        $db = $database->getConnection();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Holicoli Organic - Thực Phẩm Hữu Cơ</title>
</head>
<body>
    <div class="page-container">
        <header class="header">
            <div class="logo">
                <a href="/index.php">
                    <img src="/assets/images/logo.png" alt="Organic Shop Logo" class="logo-image">
                </a>
            </div>
            
            <div class="searchbar">
                <form action="/pages/products.php" method="GET" style="display: flex; width: 100%;">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Tìm Kiếm</button>
                </form>
            </div>
            
            <nav class="nav-items">
                
                <a href="https://github.com/linmct39/TH_LapTrinhWeb" target="_blank" style="
                    display: flex; 
                    align-items: center; 
                    gap: 5px; 
                    background: #24292e; 
                    color: white; 
                    padding: 6px 12px; 
                    border-radius: 20px; 
                    font-size: 13px;
                    text-decoration: none;
                    font-weight: 600;
                    transition: background 0.3s;
                " onmouseover="this.style.background='#000'" onmouseout="this.style.background='#24292e'">
                    <i class="fab fa-github"></i> Lab Thực Hành
                </a>
                

                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="color: #2E7D32; font-weight: 600; font-size: 14px;">
                        Chào, <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                    </span>
                    <a href="/pages/logout.php" style="color: #d32f2f; font-size: 14px;">(Đăng xuất)</a>
                
                <?php else: ?>
                    <a href="/pages/login.php">Đăng nhập / Đăng ký</a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    
                    <a href="/pages/admin/categories.php" style="color: #1976D2; font-weight: bold; border: 1px solid #1976D2; padding: 5px 10px; border-radius: 20px; font-size: 13px; text-decoration: none;">
                        📂 Quản lý DM
                    </a>
                    <a href="/pages/admin/index.php" style="color: #2E7D32; font-weight: bold; border: 1px solid #2E7D32; padding: 5px 10px; border-radius: 20px; font-size: 13px; text-decoration: none;">
                        📦 Quản lý SP
                    </a>

                <?php else: ?>
                    
                    <a href="/pages/cart.php" style="position: relative; display: inline-flex; align-items: center;">
                        🛒 Giỏ hàng
                        <span id="cart-count" style="
                            background: #ff4757; 
                            color: white; 
                            border-radius: 50%; 
                            padding: 2px 6px; 
                            font-size: 11px; 
                            font-weight: bold;
                            position: absolute; 
                            top: -8px; 
                            right: -12px;
                            min-width: 18px;
                            text-align: center;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                            transition: transform 0.2s ease;
                            display: <?php echo !empty($_SESSION['cart']) ? 'inline-block' : 'none'; ?>;
                        ">
                            <?php echo !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>

                <?php endif; ?>
            </nav>
        </header>