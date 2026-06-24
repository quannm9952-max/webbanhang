<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/controllers/ProductController.php';

$data = (new ProductController(db_connect()))->promotionsData($_GET);

$products = $data['products'];
$categories = $data['categories'];
$brands = $data['brands'];
$cat = $data['cat'];
$brand = $data['brand'];
$q = $data['q'];
$sort = $data['sort'];
$heading = $data['heading'];

$page_title = 'Khuyến mãi — TechShop';
require_once __DIR__ . '/includes/header.php';
?>
<main class="container py-2">
    <!-- Promotional Banner -->
    <div class="rounded-4 mb-4" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 40px 20px; text-align: center; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);">
        <h1 class="display-5 fw-bold mb-2"><i class="bi bi-lightning-charge-fill text-warning"></i> Săn Deal Khuyến Mãi</h1>
        <p class="lead mb-0">Các sản phẩm đang có mức giá ưu đãi cực tốt. Nhanh tay chọn ngay!</p>
    </div>

    <div class="row g-4 mt-2" id="shop-content">

        <!-- Sidebar -->
        <aside class="col-lg-3">
            <div class="filter-sidebar">
                <div class="accordion" id="filterAccordion">
                    <!-- Category Accordion -->
                    <div class="accordion-item border-0 bg-transparent mb-3">
                        <h2 class="accordion-header" id="headingCategories">
                            <button class="accordion-button bg-light rounded-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="true" aria-controls="collapseCategories">
                                Danh mục
                            </button>
                        </h2>
                        <div id="collapseCategories" class="accordion-collapse collapse show" aria-labelledby="headingCategories">
                            <div class="accordion-body px-2 py-3">
                                <ul class="filter-list">
                                    <li>
                                        <a href="<?= BASE_URL ?>/promotions.php#shop-content" class="active">
                                            Tất cả khuyến mãi
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Products -->
        <section class="col-lg-9">
            <!-- Sort bar -->
            <div class="sort-bar">
                <div>
                    <span class="section-heading">
                        Sản phẩm Khuyến mãi
                    </span>
                    <small class="text-muted ms-2">(<?= count($products) ?> sản phẩm)</small>
                </div>
                <form method="GET" class="d-flex align-items-center gap-2">
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Giá thấp → cao</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá cao → thấp</option>
                    </select>
                </form>
            </div>

            <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="bi bi-emoji-frown text-muted" style="font-size: 3rem;"></i>
                <h5>Hiện tại chưa có chương trình khuyến mãi</h5>
                <p>Vui lòng quay lại sau nhé.</p>
                <a href="<?= BASE_URL ?>/shop.php" class="btn btn-primary mt-2">Tiếp tục mua sắm</a>
            </div>
            <?php else: ?>
            <div class="row row-cols-2 row-cols-md-3 g-3">
                <?php foreach ($products as $p):
                    $price = (!empty($p['gia_giam']) && $p['gia_giam'] < $p['gia_ban']) ? $p['gia_giam'] : $p['gia_ban'];
                    $hasDiscount = !empty($p['gia_giam']) && $p['gia_giam'] < $p['gia_ban'];
                    $discountPct = $hasDiscount ? round((1 - $p['gia_giam'] / $p['gia_ban']) * 100) : 0;
                ?>
                <div class="col">
                    <div class="product-card">
                        <?php if ($hasDiscount): ?>
                            <span class="p-badge">-<?= $discountPct ?>%</span>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$p['id_san_pham'] ?>">
                            <div class="p-img-box">
                                <img src="<?= product_img_url($p['hinh_anh']) ?>"
                                     alt="<?= h($p['ten_san_pham']) ?>" loading="lazy">
                            </div>
                        </a>
                        <div class="p-content">
                            <div class="p-brand"><?= h($p['ten_thuong_hieu'] ?? '') ?></div>
                            <a href="<?= BASE_URL ?>/product.php?id=<?= (int)$p['id_san_pham'] ?>" class="p-title"><?= h($p['ten_san_pham']) ?></a>
                            <div class="mt-auto">
                                <span class="p-price text-danger"><?= format_price($price) ?></span>
                                <?php if ($hasDiscount): ?>
                                    <span class="p-old-price"><?= format_price($p['gia_ban']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="p-actions mt-3 d-flex gap-2">
                                <button class="btn btn-primary flex-fill ajax-buy-now-btn py-2 fw-bold" style="font-size: 14px;" data-id="<?= (int)$p['id_san_pham'] ?>">
                                    Mua ngay
                                </button>
                                <button class="btn btn-outline-primary flex-fill ajax-cart-btn py-2" data-id="<?= (int)$p['id_san_pham'] ?>" title="Thêm vào giỏ" style="max-width: 45px; padding: 0;">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                <button class="btn btn-outline-danger flex-fill ajax-favorite-btn py-2" data-id="<?= (int)$p['id_san_pham'] ?>" title="Yêu thích" style="max-width: 45px; padding: 0;">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
