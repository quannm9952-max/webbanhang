<?php
require_once __DIR__ . '/includes/bootstrap.php';

// 1. Kết nối DB và lấy danh sách sản phẩm có giá giảm (Khuyến mãi)
$pdo = db_connect();
$stmt = $pdo->query("SELECT * FROM san_pham WHERE gia_giam IS NOT NULL AND gia_giam < gia_ban LIMIT 8");
$discountProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Chương trình Khuyến mãi — SobaMobile';
require __DIR__ . '/includes/header.php';
?>

<main class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:13px">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/shop.php">Cửa hàng</a></li>
            <li class="breadcrumb-item active">Khuyến mãi cực sốc</li>
        </ol>
    </nav>

    <div class="row g-3 mb-5">
        <div class="col-md-6">
            <div class="position-relative overflow-hidden rounded-4 shadow-sm border">
                <img src="<?= asset_url('assets/images/poster1.png') ?>" 
                     class="img-fluid w-100 d-block" 
                     alt="Poster Quảng Cáo 1" 
                     style="min-height: 220px; max-height: 300px; object-fit: cover;">
            </div>
        </div>
        <div class="col-md-6">
            <div class="position-relative overflow-hidden rounded-4 shadow-sm border">
                <img src="<?= asset_url('assets/images/poster2.png') ?>" 
                     class="img-fluid w-100 d-block" 
                     alt="Poster Quảng Cáo 2" 
                     style="min-height: 220px; max-height: 300px; object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="section-heading mb-4">
        <i class="bi bi-tags-fill me-2 text-danger"></i>Sản phẩm đang giảm giá
    </div>

    <?php if (empty($discountProducts)): ?>
        <div class="empty-state bg-white rounded-4 shadow-sm p-5 text-center">
            <i class="bi bi-gift text-muted" style="font-size: 48px;"></i>
            <h5 class="mt-3">Chưa có sản phẩm khuyến mãi nào</h5>
            <p class="text-muted">Hệ thống đang cập nhật các chương trình ưu đãi mới, bạn quay lại sau nhé!</p>
            <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary mt-2">
                <i class="bi bi-bag me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($discountProducts as $p):
                $price = $p['gia_giam'];
                $percent = round((1 - $p['gia_giam'] / $p['gia_ban']) * 100);
            ?>
            <div class="col">
                <div class="product-card h-100 d-flex flex-column border rounded-3 p-3 bg-white position-relative">
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2" style="z-index: 2;">-<?= $percent ?>%</span>
                    
                    <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$p['id_san_pham'] ?>" class="text-decoration-none">
                        <div class="p-img-box text-center mb-3" style="height: 160px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= product_img_url($p['hinh_anh'] ?? $p['hinh_anh_chinh']) ?>"
                                 alt="<?= h($p['ten_san_pham']) ?>" 
                                 loading="lazy"
                                 style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                    </a>
                    
                    <div class="p-content d-flex flex-column flex-grow-1">
                        <div class="p-brand text-muted small text-uppercase mb-1"><?= h($p['ten_thuong_hieu'] ?? 'SobaMobile') ?></div>
                        <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$p['id_san_pham'] ?>" class="p-title fw-bold text-dark text-decoration-none mb-2" style="font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 42px;">
                            <?= h($p['ten_san_pham']) ?>
                        </a>
                        
                        <div class="mt-auto mb-3">
                            <span class="p-price text-danger fw-bold fs-5"><?= format_price($price) ?></span>
                            <span class="p-old-price text-decoration-line-through text-muted small ms-2"><?= format_price($p['gia_ban']) ?></span>
                        </div>
                        
                        <div class="p-actions mt-auto">
                            <button class="btn btn-sm btn-outline-danger w-100 ajax-cart-btn" data-id="<?= (int)$p['id_san_pham'] ?>">
                                <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>