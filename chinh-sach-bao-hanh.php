<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Chính sách bảo hành — TechShop';
require __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:13px">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/shop.php">Cửa hàng</a></li>
            <li class="breadcrumb-item active">Chính sách bảo hành</li>
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
                        <a href="<?= BASE_URL ?>/tra-cuu-bao-hanh.php">
                            <span><i class="bi bi-shield-check me-2"></i> Tra cứu bảo hành</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/chinh-sach-bao-hanh.php" class="active">
                            <span><i class="bi bi-file-earmark-text me-2"></i> Chính sách bảo hành</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="section-heading mb-4">
                <i class="bi bi-file-earmark-text me-2"></i>Chính sách bảo hành tại TechShop
            </div>
            
            <div class="bg-white rounded-4 shadow-sm p-4">
                <h5 class="fw-bold mb-3 text-primary">1. Thời gian bảo hành tiêu chuẩn</h5>
                <p>Tất cả các sản phẩm thiết bị công nghệ, linh kiện điện tử mua tại TechShop đều được áp dụng chính sách bảo hành chính hãng từ 12 đến 24 tháng tùy thuộc vào danh mục sản phẩm.</p>
                
                <h5 class="fw-bold mb-3 text-primary mt-4">2. Điều kiện được bảo hành</h5>
                <ul>
                    <li>Sản phẩm còn trong thời hạn bảo hành căn cứ theo hóa đơn hoặc số điện thoại mua hàng.</li>
                    <li>Tem bảo hành, số Serial/IMEI trên sản phẩm phải còn nguyên vẹn, không bị mờ hoặc rách.</li>
                    <li>Sản phẩm gặp lỗi kỹ thuật do nhà sản xuất phát sinh trong quá trình sử dụng bình thường.</li>
                </ul>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>