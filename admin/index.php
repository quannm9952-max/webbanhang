<?php
require_once __DIR__ . '/../models/Order.php';

$page_title = 'Dashboard';
require __DIR__ . '/_layout_start.php';

$pdo      = db_connect();
$om       = new Order($pdo);
$s        = $om->stats();
$products = (int)$pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();
$users    = (int)$pdo->query("SELECT COUNT(*) FROM nguoi_dung")->fetchColumn();
?>

<h1 class="mb-4">Tổng Quan Hệ Thống</h1>

<!-- Stat Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-6">
        <div class="admin-stat-card">
            <div class="admin-icon-box admin-icon-blue">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <p class="text-muted mb-1">Tổng đơn hàng</p>
            <h3><?= $s['totalOrders'] ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card">
            <div class="admin-icon-box admin-icon-green">
                <i class="bi bi-cash-coin"></i>
            </div>
            <p class="text-muted mb-1">Doanh thu (Đã giao)</p>
            <h3><?= format_price($s['revenue']) ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card">
            <div class="admin-icon-box admin-icon-orange">
                <i class="bi bi-clock-fill"></i>
            </div>
            <p class="text-muted mb-1">Chờ xác nhận</p>
            <h3><?= $s['pending'] ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card">
            <div class="admin-icon-box admin-icon-purple">
                <i class="bi bi-grid-fill"></i>
            </div>
            <p class="text-muted mb-1">Sản phẩm</p>
            <h3><?= $products ?></h3>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="admin-stat-card">
            <div class="admin-icon-box admin-icon-pink">
                <i class="bi bi-people-fill"></i>
            </div>
            <p class="text-muted mb-1">Người dùng</p>
            <h3><?= $users ?></h3>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3">
    <div class="col-12">
        <h5 class="fw-700 mb-3">Truy cập nhanh</h5>
    </div>
    <div class="col-md-3 col-6">
        <a href="<?= BASE_URL ?>/admin/orders.php" class="bg-white p-4 rounded-4 shadow-sm d-flex align-items-center gap-3 text-decoration-none text-dark">
            <i class="bi bi-box-seam text-primary" style="font-size:24px"></i>
            <span class="fw-600">Quản lý đơn hàng</span>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="<?= BASE_URL ?>/admin/products.php" class="bg-white p-4 rounded-4 shadow-sm d-flex align-items-center gap-3 text-decoration-none text-dark">
            <i class="bi bi-grid text-purple" style="font-size:24px;color:#8b5cf6"></i>
            <span class="fw-600">Quản lý sản phẩm</span>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="<?= BASE_URL ?>/admin/users.php" class="bg-white p-4 rounded-4 shadow-sm d-flex align-items-center gap-3 text-decoration-none text-dark">
            <i class="bi bi-people text-success" style="font-size:24px;color:#22c55e"></i>
            <span class="fw-600">Quản lý người dùng</span>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="<?= BASE_URL ?>/admin/product_form.php" class="bg-white p-4 rounded-4 shadow-sm d-flex align-items-center gap-3 text-decoration-none text-dark">
            <i class="bi bi-plus-circle text-warning" style="font-size:24px;color:#f59e0b"></i>
            <span class="fw-600">Thêm sản phẩm</span>
        </a>
    </div>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>
