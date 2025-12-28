<?php include '../includes/header.php'; ?>

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

    .success-container {
        text-align: center;
        padding: 80px 20px;
        background: #fff;
        max-width: 800px;
        margin: 40px auto;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .success-icon {
        font-size: 80px;
        color: #2E7D32;
        margin-bottom: 20px;
    }
    .btn-home {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 30px;
        background: #2E7D32;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-home:hover {
        background: #1B5E20;
        transform: translateY(-2px);
    }
</style>

<main class="content">
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1 style="color: #2E7D32; margin-bottom: 15px;">Đặt Hàng Thành Công!</h1>
        <p style="font-size: 16px; color: #555;">Cảm ơn bạn đã mua sắm tại Organic Shop.</p>
        
        <div style="background: #f9f9f9; padding: 15px; margin: 20px auto; display: inline-block; border-radius: 8px;">
            <p style="margin: 0; color: #333;">Mã đơn hàng của bạn là:</p>
            <strong style="font-size: 24px; color: #d32f2f;">#<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '---'; ?></strong>
        </div>
        
        <p style="color: #666;">Chúng tôi sẽ liên hệ sớm nhất để xác nhận đơn hàng.</p>
        
        <a href="../index.php" class="btn-home">Tiếp Tục Mua Sắm</a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>