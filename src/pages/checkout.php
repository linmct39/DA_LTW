<?php
session_start();
require_once("../config/database.php");
$database = new Database();
$db = $database->getConnection();
$db->exec("set names utf8");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: products.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$user_query = "SELECT fullname, phone, address FROM users WHERE id = ?";
$stmt_user = $db->prepare($user_query);
$stmt_user->execute([$user_id]);
$user_info = $stmt_user->fetch(PDO::FETCH_ASSOC);

$cart_items = [];
$total_amount = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$query = "SELECT id, name, price, sale_price, stock FROM products WHERE id IN ($ids)";
$stmt = $db->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $price = $row['sale_price'] ?: $row['price'];
    $qty = $_SESSION['cart'][$row['id']];
    
    if ($qty > $row['stock']) {
        die("Sản phẩm '{$row['name']}' chỉ còn {$row['stock']} cái, bạn không thể mua $qty cái.");
    }

    $row['qty'] = $qty;
    $row['final_price'] = $price;
    $row['subtotal'] = $price * $qty;
    $cart_items[] = $row;
    $total_amount += $row['subtotal'];
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];
    
    if ($fullname && $phone && $address) {
        try {
            $db->beginTransaction();

            $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, note, total_amount, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt_order = $db->prepare($sql_order);
            $stmt_order->execute([$user_id, $fullname, $phone, $address, $note, $total_amount]);
            
            $order_id = $db->lastInsertId();

            $sql_detail = "INSERT INTO order_details (order_id, product_id, product_name, price, quantity, subtotal) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_detail = $db->prepare($sql_detail);

            $sql_update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt_update_stock = $db->prepare($sql_update_stock);

            foreach ($cart_items as $item) {
                $stmt_detail->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['final_price'],
                    $item['qty'],
                    $item['subtotal']
                ]);

                $stmt_update_stock->execute([$item['qty'], $item['id']]);
            }

            $db->commit();

            unset($_SESSION['cart']);
            header("Location: order-success.php?id=" . $order_id);
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            $message = "Lỗi hệ thống: " . $e->getMessage();
        }
    } else {
        $message = "Vui lòng điền đầy đủ thông tin nhận hàng.";
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

    .checkout-container {
        max-width: 1200px;
        margin: 40px auto;
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
    }
    
    .checkout-form, .order-summary {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 15px;
    }
    .form-group textarea { height: 100px; resize: vertical; }

    .order-item {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #eee;
        padding: 10px 0;
        font-size: 14px;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
        color: #d32f2f;
        border-top: 2px solid #eee;
        padding-top: 20px;
    }

    .btn-submit {
        width: 100%;
        background: #2E7D32;
        color: white;
        padding: 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 20px;
    }
    .btn-submit:hover { background: #1B5E20; }
    
    .error-msg { color: red; margin-bottom: 15px; }

    @media (max-width: 768px) {
        .checkout-container { grid-template-columns: 1fr; }
    }
</style>

<main class="content">
    <div class="checkout-container">
        <div class="checkout-form">
            <h2 style="margin-bottom: 20px; color: #2E7D32;">Thông Tin Nhận Hàng</h2>
            
            <?php if ($message): ?>
                <div class="error-msg"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Họ và tên người nhận <span style="color:red">*</span></label>
                    <input type="text" name="fullname" required 
                           value="<?php echo isset($user_info['fullname']) ? htmlspecialchars($user_info['fullname']) : ''; ?>"
                           placeholder="Ví dụ: Nguyễn Văn A">
                </div>
                
                <div class="form-group">
                    <label>Số điện thoại <span style="color:red">*</span></label>
                    <input type="text" name="phone" required 
                           value="<?php echo isset($user_info['phone']) ? htmlspecialchars($user_info['phone']) : ''; ?>"
                           placeholder="Ví dụ: 0909123456">
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ giao hàng <span style="color:red">*</span></label>
                    <input type="text" name="address" required 
                           value="<?php echo isset($user_info['address']) ? htmlspecialchars($user_info['address']) : ''; ?>"
                           placeholder="Số nhà, đường, phường/xã...">
                </div>
                
                <div class="form-group">
                    <label>Ghi chú đơn hàng (Tùy chọn)</label>
                    <textarea name="note" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                </div>

                <button type="submit" class="btn-submit">XÁC NHẬN ĐẶT HÀNG</button>
            </form>
        </div>

        <div class="order-summary">
            <h3 style="margin-bottom: 20px;">Đơn hàng của bạn</h3>
            
            <?php foreach ($cart_items as $item): ?>
            <div class="order-item">
                <div>
                    <strong><?php echo htmlspecialchars($item['name']); ?></strong> <br>
                    <small>SL: <?php echo $item['qty']; ?> x <?php echo number_format($item['final_price']); ?></small>
                </div>
                <div><?php echo number_format($item['subtotal']); ?> đ</div>
            </div>
            <?php endforeach; ?>

            <div class="total-row">
                <span>Tổng cộng:</span>
                <span><?php echo number_format($total_amount, 0, ',', '.'); ?> đ</span>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>