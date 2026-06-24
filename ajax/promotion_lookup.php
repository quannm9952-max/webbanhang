<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Promotion.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn cần đăng nhập để dùng mã giảm giá.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$code = trim((string)($_POST['code'] ?? ''));
if ($code === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng nhập mã giảm giá.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = db_connect();
$cart = new Cart($pdo);
$cart->getOrCreateCartByUserId((int)$_SESSION['id_nguoi_dung']);
$orderTotal = $cart->getTotalPrice();

$promotion = (new Promotion($pdo))->findCheckoutPromotionByCode($code, $orderTotal);
if (!$promotion) {
    echo json_encode([
        'success' => false,
        'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => $promotion['is_applicable']
        ? 'Đã tìm thấy mã giảm giá.'
        : 'Mã đúng nhưng đơn hàng chưa đủ điều kiện áp dụng.',
    'promotion' => $promotion,
], JSON_UNESCAPED_UNICODE);
