<?php
session_start();
require_once("../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $product_id => $qty) {
            if ($qty == 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $qty;
            }
        }
    }


    if (isset($_POST['remove_item'])) {
        $product_id = $_POST['remove_item'];
        unset($_SESSION['cart'][$product_id]);
    }
}

$cart_items = [];
$total_price = 0;


if (!empty($_SESSION['cart'])) {

    $ids = implode(',', array_keys($_SESSION['cart']));


    $query = "SELECT id, name, price, sale_price, image FROM products WHERE id IN ($ids)";
    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $price = $row['sale_price'] ?: $row['price'];
        $qty = $_SESSION['cart'][$row['id']];
        $subtotal = $price * $qty;


        $row['qty'] = $qty;
        $row['final_price'] = $price;
        $row['subtotal'] = $subtotal;

        $cart_items[] = $row;
        $total_price += $subtotal;
    }
}
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
        width: 100%;
    }

    /*loi css tao riêng cho  */
    .cart-container {
        max-width: 1200px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .cart-table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        color: #1B3A1B;
        font-weight: 600;
        border-bottom: 2px solid #2E7D32;
    }

    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .cart-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .qty-input {
        width: 60px;
        padding: 5px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn-remove {
        color: #dc3545;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        text-decoration: underline;
    }

    .cart-summary {
        display: flex;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #eee;
    }

    .summary-box {
        width: 300px;
        text-align: right;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .summary-total {
        font-size: 20px;
        font-weight: bold;
        color: #d32f2f;
    }

    .btn-checkout {
        display: block;
        width: 100%;
        background: linear-gradient(135deg, #2E7D32, #4CAF50);
        color: white;
        text-align: center;
        padding: 12px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        margin-top: 15px;
        transition: 0.3s;
    }

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
    }

    .btn-update {
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ccc;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 4px;
        margin-left: 10px;
    }

    .empty-cart {
        text-align: center;
        padding: 50px 0;
    }
</style>

<main class="content">
    <div class="cart-container">
        <h2 class="section-title">Giỏ Hàng Của Bạn</h2>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <img src="../assets/images/empty-cart.png" alt="Giỏ hàng trống" style="width: 150px; margin-bottom: 20px; opacity: 0.5;">
                <p>Giỏ hàng đang trống.</p>
                <a href="products.php" class="btn-checkout" style="width: 200px; margin: 20px auto;">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td style="display: flex; gap: 15px; align-items: center;">
                                    <img src="../assets/images/<?php echo $item['image']; ?>" class="cart-img" onerror="this.src='../assets/images/logo.png'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo number_format($item['final_price'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="1" class="qty-input">
                                </td>
                                <td style="font-weight: bold; color: #2E7D32;">
                                    <?php echo number_format($item['subtotal'], 0, ',', '.'); ?> đ
                                </td>
                                <td>
                                    <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" class="btn-remove" onclick="return confirm('Bạn có chắc muốn xóa không?')">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: right;">
                    <button type="submit" name="update_cart" class="btn-update">Cập nhật giỏ hàng</button>
                </div>
            </form>

            <div class="cart-summary">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($total_price, 0, ',', '.'); ?> đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Tổng cộng:</span>
                        <span class="summary-total"><?php echo number_format($total_price, 0, ',', '.'); ?> đ</span>
                    </div>
                    <a href="checkout.php" class="btn-checkout">TIẾN HÀNH THANH TOÁN</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>