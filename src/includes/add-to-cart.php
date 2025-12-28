<?php
session_start();
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$product_id = isset($data['product_id']) ? $data['product_id'] : null;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: Thiếu ID sản phẩm']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

$total_items = array_sum($_SESSION['cart']);

echo json_encode([
    'success' => true,
    'message' => 'Thêm thành công',
    'total_items' => $total_items,
    'cart_data' => $_SESSION['cart']
]);
?>