<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

$currentPath = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($page_title ?? 'Admin') ?> — SobaMobile Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= asset_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="<?= BASE_URL ?>/admin/index.php" class="brand-logo text-white mb-4">
            <span class="brand-icon">T</span>
            <span>Soba<span>Mobile</span></span>
        </a>

        <a class="<?= $currentPath === 'index.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/index.php">
            <i class="bi bi-speedometer2"></i> Tổng Quan
        </a>
        <a class="<?= $currentPath === 'orders.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/orders.php">
            <i class="bi bi-box-seam"></i> Đơn hàng
        </a>
        <a class="<?= in_array($currentPath, ['warranty.php', 'warranty_form.php'], true) ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/warranty.php">
            <i class="bi bi-shield-check"></i> Bảo hành
        </a>
        <a class="<?= $currentPath === 'reviews.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/reviews.php">
            <i class="bi bi-star"></i> Feedback
        </a>
        <a class="<?= $currentPath === 'chat.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/chat.php">
            <i class="bi bi-chat-dots"></i> Chat
        </a>
        <a class="<?= $currentPath === 'products.php' || $currentPath === 'product_form.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/products.php">
            <i class="bi bi-grid"></i> Sản phẩm
        </a>
        <a class="<?= $currentPath === 'categories.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/categories.php">
            <i class="bi bi-tags"></i> Danh mục
        </a>
        <a class="<?= $currentPath === 'brands.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/brands.php">
            <i class="bi bi-award"></i> Thương hiệu
        </a>
        <a class="<?= $currentPath === 'users.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/users.php">
            <i class="bi bi-people"></i> Người dùng
        </a>
        <a class="<?= $currentPath === 'payment_methods.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/payment_methods.php">
            <i class="bi bi-wallet2"></i> Thanh toán
        </a>
        <a class="<?= $currentPath === 'promotions.php' || $currentPath === 'promotion_form.php' ? 'active' : '' ?>"
           href="<?= BASE_URL ?>/admin/promotions.php">
            <i class="bi bi-percent"></i> Khuyến mãi
        </a>
        <hr style="border-color:rgba(255,255,255,0.15);margin:12px 0">
    </aside>

    <main class="admin-main">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <div class="admin-topbar">
            <div class="dropdown">
                <button class="admin-user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                    <span>Admin</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end admin-dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="<?= BASE_URL ?>">
                            <i class="bi bi-shop"></i>
                            Về shop
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
