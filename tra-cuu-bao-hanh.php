<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Tra cứu bảo hành — TechShop';
require __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:13px">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/shop.php">Cửa hàng</a></li>
            <li class="breadcrumb-item active">Tra cứu bảo hành</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <ul class="filter-list mb-0">
                    <li>
                        <a href="<?= BASE_URL ?>/account.php">
                            <span><i class="bi bi-person me-2"></i> Thông tin tài khoản</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/cart.php">
                            <span><i class="bi bi-cart3 me-2"></i> Giỏ hàng của bạn</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/my_orders.php">
                            <span><i class="bi bi-clock-history me-2"></i> Lịch sử đơn hàng</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/tra-cuu-bao-hanh.php" class="active">
                            <span><i class="bi bi-shield-check me-2"></i> Tra cứu bảo hành</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/chinh-sach-bao-hanh.php">
                            <span><i class="bi bi-file-earmark-text me-2"></i> Chính sách bảo hành</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="section-heading mb-4">
                <i class="bi bi-shield-check me-2"></i>Tra cứu thông tin bảo hành
            </div>
            
            <div class="bg-white rounded-4 shadow-sm p-4 text-center">
                <i class="bi bi-search text-muted mb-3" style="font-size: 48px; display: block;"></i>
                <p class="text-muted">Tính năng kiểm tra thời hạn và lịch sử bảo hành đang được cập nhật.</p>
                <p class="small text-secondary">Vui lòng liên hệ hotline 1900 1234 để được hỗ trợ trực tiếp.</p>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>