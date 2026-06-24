<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/controllers/ProductController.php';

$data = (new ProductController(db_connect()))->shopData($_GET);

$products = $data['products'];
$categories = $data['categories'];
$brands = $data['brands'];
$cat = $data['cat'];
$brand = $data['brand'];
$q = $data['q'];
$sort = $data['sort'];
$heading = $data['heading'];

$page_title = 'Cửa hàng — TechShop';
require_once __DIR__ . '/includes/header.php';
?>
<main class="container py-2">
    <?php require __DIR__ . '/includes/hero.php'; ?>

    <div class="row g-4 mt-2" id="shop-content">

        <!-- Promotional Banner Top -->
        <div class="col-12 mb-2">
            <div class="d-flex justify-content-between align-items-center bg-primary text-white p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #2563eb, #1e40af) !important;">
                <div>
                    <h4 class="fw-bold mb-1"><i class="bi bi-tags-fill text-warning me-2"></i>Sản phẩm đang khuyến mãi</h4>
                    <p class="mb-0 text-white-50">Săn ngay các deal công nghệ giảm giá cực sâu hôm nay.</p>
                </div>
                <a href="<?= BASE_URL ?>/promotions.php" class="btn btn-warning fw-bold text-dark rounded-pill px-4">Xem tất cả deal</a>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="col-lg-3">
            <div class="filter-sidebar">
                <div class="accordion" id="filterAccordion">
                    
                    <!-- Categories -->
                    <div class="accordion-item border-0 bg-transparent mb-3">
                        <h2 class="accordion-header" id="headingCat">
                            <button class="accordion-button bg-light rounded-3 fw-bold shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat" aria-expanded="true" aria-controls="collapseCat">
                                Danh mục
                            </button>
                        </h2>
                        <div id="collapseCat" class="accordion-collapse collapse show" aria-labelledby="headingCat">
                            <div class="accordion-body px-2 py-3">
                                <ul class="filter-list">
                                    <li>
                                        <a href="<?= BASE_URL ?>/shop.php<?= $brand ? '?brand=' . (int)$brand : '' ?>#shop-content"
                                           class="<?= !$cat ? 'active' : '' ?>">
                                            Tất cả sản phẩm
                                            <span class="filter-badge"><?= array_sum(array_map('intval', array_column($categories, 'so_luong'))) ?></span>
                                        </a>
                                    </li>
                                    <?php foreach ($categories as $c): ?>
                                    <li>
                                        <a href="<?= BASE_URL ?>/shop.php?category=<?= (int)$c['id_danh_muc'] ?><?= $brand ? '&brand=' . (int)$brand : '' ?>#shop-content"
                                           class="<?= $cat === (int)$c['id_danh_muc'] ? 'active' : '' ?>">
                                            <?= h($c['ten_danh_muc']) ?>
                                            <span class="filter-badge"><?= (int)$c['so_luong'] ?></span>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="accordion-item border-0 bg-transparent mb-3">
                        <h2 class="accordion-header" id="headingBrand">
                            <button class="accordion-button bg-light rounded-3 fw-bold shadow-none <?= $brand ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBrand" aria-expanded="<?= $brand ? 'true' : 'false' ?>" aria-controls="collapseBrand">
                                Thương hiệu
                            </button>
                        </h2>
                        <div id="collapseBrand" class="accordion-collapse collapse <?= $brand ? 'show' : '' ?>" aria-labelledby="headingBrand">
                            <div class="accordion-body px-2 py-3">
                                <ul class="filter-list">
                                    <?php foreach ($brands as $b): ?>
                                    <li>
                                        <a href="<?= BASE_URL ?>/shop.php?brand=<?= (int)$b['id'] ?><?= $cat ? '&category=' . (int)$cat : '' ?>#shop-content"
                                           class="<?= $brand === (int)$b['id'] ? 'active' : '' ?>">
                                            <?= h($b['name']) ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
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
                        <?php if ($q): ?>
                            Kết quả cho "<strong><?= h($q) ?></strong>"
                        <?php elseif ($cat): ?>
                            <?= h($heading ?: 'Danh mục') ?>
                        <?php elseif ($brand): ?>
                            <?= h($heading ?: 'Thương hiệu') ?>
                        <?php else: ?>
                            Tất cả sản phẩm
                        <?php endif; ?>
                    </span>
                    <small class="text-muted ms-2">(<?= count($products) ?> sản phẩm)</small>
                </div>
                <form method="GET" class="d-flex align-items-center gap-2">
                    <?php if ($cat): ?><input type="hidden" name="category" value="<?= (int)$cat ?>"><?php endif; ?>
                    <?php if ($brand): ?><input type="hidden" name="brand" value="<?= (int)$brand ?>"><?php endif; ?>
                    <?php if ($q): ?><input type="hidden" name="q" value="<?= h($q) ?>"><?php endif; ?>
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Giá thấp → cao</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá cao → thấp</option>
                    </select>
                </form>
            </div>

            <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="bi bi-search"></i>
                <h5>Không tìm thấy sản phẩm nào</h5>
                <p>Hãy thử tìm kiếm với từ khóa khác.</p>
                <a href="<?= BASE_URL ?>/shop.php#shop-content" class="btn btn-primary mt-2">Xem tất cả</a>
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
                                <span class="p-price"><?= format_price($price) ?></span>
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
