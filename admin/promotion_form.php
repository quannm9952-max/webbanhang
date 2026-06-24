<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../models/AdminCatalog.php';

$pdo = db_connect();
$m = new AdminCatalog($pdo);

$id = (int)($_GET['id'] ?? 0);
$km = $id ? $m->findPromotion($id) : null;

if (is_post()) {
    try {
        $pdo->beginTransaction();

        $m->savePromotion($_POST, $id);
        $promotionId = $id ?: (int)$pdo->lastInsertId();

        $m->syncPromotionProducts($promotionId, $_POST['products'] ?? []);

        $pdo->commit();
        redirect('admin/promotions.php');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$page_title = $km ? 'Sửa khuyến mãi' : 'Thêm khuyến mãi';
require __DIR__ . '/_layout_start.php';

$products = $m->productsForPromotionSelect();
$selected = $id ? $m->getPromotionProductIds($id) : [];
$moneyInputValue = static function (mixed $value): string {
    $number = (float)($value ?? 0);
    return $number > 0 ? number_format($number, 0, ',', '.') . ' đ' : '';
};
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/admin/promotions.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
    <div>
        <h1 class="mb-0"><?= $km ? 'Sửa khuyến mãi' : 'Thêm khuyến mãi' ?></h1>
        <p class="text-muted mb-0">Tạo mã giảm giá cho trang thanh toán hoặc giảm giá theo sản phẩm.</p>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
<?php endif; ?>

<form method="post" class="bg-white p-4 rounded-4 shadow-sm">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="mb-3">
                <label class="form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                <input name="ten_khuyen_mai" class="form-control" required
                       value="<?= h($km['ten_khuyen_mai'] ?? '') ?>"
                       placeholder="VD: Sale tháng 5">
            </div>

<div class="mb-3">
    <label class="form-label">Mã giảm giá</label>
    <input name="ma_code" class="form-control"
           value="<?= h($km['ma_code'] ?? '') ?>"
           placeholder="VD: SALE50, PHONE600">
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch"
           id="hienThiCheckout" name="hien_thi_checkout" value="1"
           <?= (int)($km['hien_thi_checkout'] ?? 1) === 1 ? 'checked' : '' ?>>
    <label class="form-check-label fw-600" for="hienThiCheckout">
        Hiển thị mã này ở trang thanh toán
    </label>
    <small class="text-muted d-block">
        Tắt mục này thì mã sẽ bị ẩn, người dùng phải nhập đúng mã mới thấy và áp dụng.
    </small>
</div>

<div class="mb-3">
    <label class="form-label">Kiểu giảm</label>
    <select name="kieu_giam" id="promotionDiscountType" class="form-select">
        <option value="phan_tram" <?= ($km['kieu_giam'] ?? 'phan_tram') === 'phan_tram' ? 'selected' : '' ?>>
            Giảm theo phần trăm sản phẩm
        </option>
        <option value="tien_mat" <?= ($km['kieu_giam'] ?? '') === 'tien_mat' ? 'selected' : '' ?>>
            Mã giảm tiền khi thanh toán
        </option>
    </select>
</div>

<div class="mb-3" id="percentDiscountField">
    <label class="form-label">Phần trăm giảm (%)</label>
    <input name="phan_tram_giam" type="number" min="0" max="100" step="0.01"
           class="form-control"
           value="<?= h($km['phan_tram_giam'] ?? 0) ?>">
</div>

<div class="mb-3" id="cashDiscountField">
    <label class="form-label">Số tiền giảm</label>
    <input name="so_tien_giam" type="text" inputmode="numeric"
           class="form-control js-vnd-input"
           value="<?= h($moneyInputValue($km['so_tien_giam'] ?? 0)) ?>"
           placeholder="VD: 50.000 đ">
</div>

<div class="mb-3">
    <label class="form-label">Đơn tối thiểu</label>
    <input name="don_toi_thieu" type="text" inputmode="numeric"
           class="form-control js-vnd-input"
           value="<?= h($moneyInputValue($km['don_toi_thieu'] ?? 0)) ?>"
           placeholder="VD: 100.000 đ">
</div>

            <div class="mb-3">
                <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                <input name="ngay_bat_dau" type="datetime-local" class="form-control" required
                       value="<?= !empty($km['ngay_bat_dau']) ? date('Y-m-d\TH:i', strtotime($km['ngay_bat_dau'])) : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                <input name="ngay_ket_thuc" type="datetime-local" class="form-control" required
                       value="<?= !empty($km['ngay_ket_thuc']) ? date('Y-m-d\TH:i', strtotime($km['ngay_ket_thuc'])) : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="trang_thai" class="form-select">
                    <option value="dang_dien_ra" <?= ($km['trang_thai'] ?? '') === 'dang_dien_ra' ? 'selected' : '' ?>>Đang diễn ra</option>
                    <option value="ket_thuc" <?= ($km['trang_thai'] ?? '') === 'ket_thuc' ? 'selected' : '' ?>>Kết thúc</option>
                    <option value="an" <?= ($km['trang_thai'] ?? '') === 'an' ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <label class="form-label">Chọn sản phẩm áp dụng</label>

            <div class="border rounded-3 p-3" style="max-height:420px; overflow:auto;">
                <?php foreach ($products as $sp): ?>
                    <label class="d-flex align-items-center justify-content-between gap-3 border-bottom py-2">
                        <span>
                            <input type="checkbox"
                                   name="products[]"
                                   value="<?= (int)$sp['id_san_pham'] ?>"
                                   <?= in_array((int)$sp['id_san_pham'], $selected, true) ? 'checked' : '' ?>>
                            <span class="ms-2"><?= h($sp['ten_san_pham']) ?></span>
                        </span>
                        <span class="text-muted small"><?= format_price($sp['gia']) ?></span>
                    </label>
                <?php endforeach; ?>

                <?php if (empty($products)): ?>
                    <p class="text-muted mb-0">Chưa có sản phẩm để áp dụng.</p>
                <?php endif; ?>
            </div>

            <small class="text-muted d-block mt-2">
                Chỉ những sản phẩm được tick mới được áp dụng khuyến mãi.
            </small>
        </div>
    </div>

    <div class="d-flex gap-3 mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-lg me-2"></i>Lưu khuyến mãi
        </button>
        <a href="<?= BASE_URL ?>/admin/promotions.php" class="btn btn-outline-secondary btn-lg">Hủy</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('promotionDiscountType');
    const percentField = document.getElementById('percentDiscountField');
    const cashField = document.getElementById('cashDiscountField');
    const percentInput = percentField.querySelector('input');
    const cashInput = cashField.querySelector('input');
    const moneyInputs = document.querySelectorAll('.js-vnd-input');

    function formatVndInput(input) {
        const rawValue = input.value.replace(/[^\d]/g, '');

        if (!rawValue) {
            input.value = '';
            return;
        }

        input.value = new Intl.NumberFormat('vi-VN').format(Number(rawValue)) + ' đ';
    }

    function syncDiscountFields() {
        const isCashDiscount = typeSelect.value === 'tien_mat';

        percentField.style.display = isCashDiscount ? 'none' : '';
        cashField.style.display = isCashDiscount ? '' : 'none';

        percentInput.disabled = isCashDiscount;
        cashInput.disabled = !isCashDiscount;
    }

    typeSelect.addEventListener('change', syncDiscountFields);
    moneyInputs.forEach(function (input) {
        input.addEventListener('blur', function () {
            formatVndInput(input);
        });
        input.addEventListener('focus', function () {
            input.value = input.value.replace(/[^\d]/g, '');
        });
    });
    syncDiscountFields();
});
</script>

<?php require __DIR__ . '/_layout_end.php'; ?>
