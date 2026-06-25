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

<section class="admin-dashboard">
    <div class="dashboard-heading">
        <h1>Dashboard</h1>
        <p>Tổng quan hệ thống</p>
    </div>

    <div class="dashboard-stats">
        <article class="dashboard-stat-card">
            <div class="dashboard-stat-label">
                <span class="dashboard-stat-icon icon-cyan"><i class="bi bi-receipt"></i></span>
                <span>Tổng đơn hàng</span>
            </div>
            <strong><?= (int)$s['totalOrders'] ?></strong>
            <small class="stat-info">Tất cả đơn</small>
        </article>

        <article class="dashboard-stat-card">
            <div class="dashboard-stat-label">
                <span class="dashboard-stat-icon icon-orange"><i class="bi bi-cash-coin"></i></span>
                <span>Tổng doanh thu</span>
            </div>
            <strong><?= format_price($s['revenue']) ?></strong>
            <small class="stat-up">Đã giao</small>
        </article>

        <article class="dashboard-stat-card">
            <div class="dashboard-stat-label">
                <span class="dashboard-stat-icon icon-blue"><i class="bi bi-box-seam"></i></span>
                <span>Sản phẩm</span>
            </div>
            <strong><?= $products ?></strong>
            <small class="stat-info">Đang quản lý</small>
        </article>

        <article class="dashboard-stat-card">
            <div class="dashboard-stat-label">
                <span class="dashboard-stat-icon icon-indigo"><i class="bi bi-people-fill"></i></span>
                <span>Khách hàng</span>
            </div>
            <strong><?= $users ?></strong>
            <small class="stat-up">Tài khoản</small>
        </article>
    </div>

    <div class="dashboard-quick">
        <h2>Truy cập nhanh</h2>
        <div class="dashboard-quick-grid">
            <a href="<?= BASE_URL ?>/admin/orders.php" class="dashboard-quick-card">
                <i class="bi bi-box-seam text-primary"></i>
                <span>Quản lý đơn hàng</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/products.php" class="dashboard-quick-card">
                <i class="bi bi-grid text-purple"></i>
                <span>Quản lý sản phẩm</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/users.php" class="dashboard-quick-card">
                <i class="bi bi-people text-success"></i>
                <span>Quản lý người dùng</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/product_form.php" class="dashboard-quick-card">
                <i class="bi bi-plus-circle text-warning"></i>
                <span>Thêm sản phẩm</span>
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/_layout_end.php'; ?>
