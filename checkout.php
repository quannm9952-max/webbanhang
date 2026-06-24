<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/controllers/OrderController.php';

$controller = new OrderController(db_connect());
$controller->handleCheckoutPost();

$data = $controller->checkoutData();
$items = $data['items'];
$total = $data['total'];
$methods = $data['methods'];
$error = $data['error'];
$user_info = $data['user'];
$checkoutPromotions = $data['checkoutPromotions'];

$page_title = 'Thanh toán — TechShop';
require __DIR__ . '/includes/header.php';
?>
<main class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:13px">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/shop.php">Cửa hàng</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/cart.php">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <div class="section-heading mb-4">
        <i class="bi bi-credit-card me-2"></i>Thanh toán
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= h($error) ?>
    </div>
    <?php endif; ?>

    <form method="post">
        <div class="row g-4">
            <!-- Left: Shipping + Payment -->
            <div class="col-lg-7">
                <!-- Shipping Info -->
                <div class="checkout-section mb-4">
                    <h5><i class="bi bi-geo-alt me-2 text-primary"></i>Thông tin giao hàng</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input name="ten_nguoi_nhan" class="form-control"
                                   placeholder="Nhập họ tên đầy đủ"
                                   value="<?= h($_SESSION['ho_ten'] ?? $user_info['ho_ten'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input name="so_dien_thoai_nhan" class="form-control"
                                   placeholder="Nhập số điện thoại"
                                   value="<?= h($_SESSION['so_dien_thoai'] ?? $user_info['so_dien_thoai'] ?? '') ?>"
                                   required pattern="[0-9]{10}" maxlength="10" title="Vui lòng nhập đúng 10 chữ số">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea name="dia_chi_giao_hang" class="form-control" rows="2"
                                      placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" required><?= h($user_info['dia_chi'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi chú đơn hàng</label>
                            <textarea name="ghi_chu" class="form-control" rows="2"
                                      placeholder="VD: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="checkout-section">
                    <h5><i class="bi bi-wallet2 me-2 text-primary"></i>Phương thức thanh toán</h5>
                    <?php foreach ($methods as $k => $m): ?>
                    <label class="payment-option <?= $k === 0 ? 'selected' : '' ?>"
                           onclick="document.querySelectorAll('.payment-option').forEach(e=>e.classList.remove('selected'));this.classList.add('selected')">
                        <input type="radio" name="id_phuong_thuc"
                               value="<?= (int)$m['id_phuong_thuc'] ?>"
                               <?= $k === 0 ? 'checked' : '' ?> required>
                        <div>
                            <div class="fw-700"><?= h($m['ten_phuong_thuc']) ?></div>
                            <?php if (!empty($m['mo_ta'])): ?>
                            <small class="text-muted"><?= h($m['mo_ta']) ?></small>
                            <?php endif; ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-lg-5">
                <div class="cart-summary-card" style="position:sticky;top:100px">
                    <div class="promo-box" onclick="openPromoModal()">
                        <div>
                            <i class="bi bi-percent"></i>
                            <span id="promoText">Chọn khuyến mãi và ưu đãi</span>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </div>

                    <input type="hidden" name="promotion_code" id="promotionCode" value="">
                    <h5 class="fw-800 mb-4">
                        <i class="bi bi-receipt me-2 text-primary"></i>Đơn hàng của bạn
                    </h5>
                    <div style="max-height:320px;overflow-y:auto">
                        <?php foreach ($items as $i): ?>
                        <div class="order-item-row">
                            <div class="d-flex align-items-center gap-2" style="max-width:65%">
                                <img src="<?= product_img_url($i['hinh_anh']) ?>"
                                     style="width:40px;height:40px;object-fit:contain;border-radius:8px;border:1px solid var(--border);flex-shrink:0">
                                <span class="text-truncate"><?= h($i['ten_san_pham']) ?>
                                    <small class="text-muted">x<?= (int)$i['so_luong'] ?></small>
                                </span>
                            </div>
                            <span class="fw-600 text-primary"><?= format_price($i['thanh_tien']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-3">
                    <div class="d-flex justify-content-between mb-2" style="font-size:14px">
                        <span class="text-muted">Tạm tính</span>
                        <span class="fw-600"><?= format_price($total) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3" style="font-size:14px">
                        <span class="text-muted">Phí vận chuyển</span>
                        <span class="text-success fw-600">Miễn phí</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size:14px">
                        <span class="text-muted">Tổng khuyến mãi</span>
                        <span class="fw-600 text-primary" id="discountShow">-0đ</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-800" style="font-size:16px">Tổng thanh toán</span>
                        <span class="fw-800 text-primary" style="font-size:22px" id="finalTotal">
                            <?= format_price($total) ?>
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-check-circle me-2"></i>Đặt hàng ngay
                    </button>
                    <div class="d-flex align-items-center gap-2 mt-3 text-muted justify-content-center" style="font-size:12px">
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        Thanh toán an toàn và bảo mật
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<?php if ($error): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Không thể đặt hàng',
            text: <?= json_encode($error, JSON_UNESCAPED_UNICODE) ?>,
            confirmButtonText: 'Đóng'
        });
    }
});
</script>
<?php endif; ?>
<div class="promo-modal" id="promoModal">
    <div class="promo-panel">
        <div class="promo-header">
            <h4>Khuyến mãi và ưu đãi</h4>
            <button type="button" onclick="closePromoModal()">×</button>
        </div>

        <div class="promo-content">
            <h6>Mã giảm giá</h6>

            <div class="voucher-input">
                <i class="bi bi-ticket-perforated"></i>
                <input type="text" id="promoCodeInput" placeholder="Nhập mã giảm giá của bạn tại đây nhé" autocomplete="off">
                <button type="button" onclick="lookupPromotionCode()">Áp dụng</button>
            </div>
            <div id="promoCodeMessage" class="promo-code-message"></div>

            <h6 class="mt-4">Khuyến mãi</h6>

            <div id="promoList">
                <?php foreach ($checkoutPromotions as $promo): ?>
                    <div class="promo-item <?= $promo['is_applicable'] ? '' : 'disabled' ?>"
                         data-code="<?= h($promo['ma_code']) ?>"
                         data-discount="<?= (float)$promo['discount_amount'] ?>"
                         onclick="togglePromo(this)">
                        <div class="promo-icon">%</div>
                        <div>
                            <b><?= h($promo['ten_khuyen_mai']) ?></b>
                            <small>
                                Mã: <?= h($promo['ma_code']) ?>
                                <?php if ((float)$promo['don_toi_thieu'] > 0): ?>
                                    - Đơn tối thiểu <?= format_price($promo['don_toi_thieu']) ?>
                                <?php endif; ?>
                                <?php if (!$promo['is_applicable']): ?>
                                    - Chưa đủ điều kiện
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="promo-check">✓</div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($checkoutPromotions)): ?>
                    <p id="promoEmptyText" class="promo-empty">Không có mã hiển thị sẵn. Nhập mã giảm giá để kiểm tra.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="promo-footer">
            <div class="promo-total">
                <div id="promoFinalPrice" class="promo-total-price">
                    <?= format_price($total) ?>
                </div>
                <small id="promoSaving" class="promo-total-saving">
                        Tiết kiệm 0đ
                </small>
            </div>

            <button type="button" class="promo-confirm-btn" onclick="confirmPromo()">
                    Xác nhận
            </button>
        </div>
    </div>
</div>

<script>
const orderTotal = <?= (int)$total ?>;
let selectedDiscount = 0;
let selectedPromotionCode = '';

function formatVnd(number) {
    return new Intl.NumberFormat('vi-VN').format(number) + 'đ';
}

function openPromoModal() {
    document.getElementById('promoModal').classList.add('show');
}

function closePromoModal() {
    document.getElementById('promoModal').classList.remove('show');
}

function togglePromo(element) {
    if (element.classList.contains('disabled')) {
        return;
    }

    const wasActive = element.classList.contains('active');

    document.querySelectorAll('.promo-item.active').forEach(item => {
        item.classList.remove('active');
    });

    selectedDiscount = 0;
    selectedPromotionCode = '';

    if (!wasActive) {
        element.classList.add('active');
        selectedDiscount = Number(element.dataset.discount || 0);
        selectedPromotionCode = element.dataset.code || '';
    }

    if (selectedDiscount < 0) selectedDiscount = 0;

    let finalPrice = Math.max(orderTotal - selectedDiscount, 0);

    document.getElementById('promoFinalPrice').innerText = formatVnd(finalPrice);
    document.getElementById('promoSaving').innerText = 'Tiết kiệm ' + formatVnd(selectedDiscount);
}

function setPromoMessage(message, isError = false) {
    const messageBox = document.getElementById('promoCodeMessage');
    messageBox.innerText = message || '';
    messageBox.classList.toggle('error', isError);
}

function appendPromotionItem(promotion) {
    const list = document.getElementById('promoList');
    const emptyText = document.getElementById('promoEmptyText');
    const code = promotion.ma_code || '';
    const existing = Array.from(list.querySelectorAll('.promo-item'))
        .find(item => item.dataset.code === code);

    if (emptyText) {
        emptyText.remove();
    }
    if (existing) {
        return existing;
    }

    const item = document.createElement('div');
    item.className = 'promo-item' + (promotion.is_applicable ? '' : ' disabled');
    item.dataset.code = code;
    item.dataset.discount = promotion.discount_amount || 0;
    item.onclick = function () {
        togglePromo(item);
    };

    const icon = document.createElement('div');
    icon.className = 'promo-icon';
    icon.innerText = '%';

    const body = document.createElement('div');
    const title = document.createElement('b');
    title.innerText = promotion.ten_khuyen_mai || code;
    const note = document.createElement('small');
    note.innerText = 'Mã: ' + code
        + (Number(promotion.don_toi_thieu || 0) > 0 ? ' - Đơn tối thiểu ' + formatVnd(promotion.don_toi_thieu) : '')
        + (!promotion.is_applicable ? ' - Chưa đủ điều kiện' : '');
    body.append(title, note);

    const check = document.createElement('div');
    check.className = 'promo-check';
    check.innerText = '✓';

    item.append(icon, body, check);
    list.prepend(item);
    return item;
}

function lookupPromotionCode() {
    const input = document.getElementById('promoCodeInput');
    const code = input.value.trim();

    if (!code) {
        setPromoMessage('Vui lòng nhập mã giảm giá.', true);
        return;
    }

    fetch('<?= BASE_URL ?>/ajax/promotion_lookup.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: new URLSearchParams({ code })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.promotion) {
                setPromoMessage(data.message || 'Mã giảm giá không hợp lệ.', true);
                return;
            }

            const item = appendPromotionItem(data.promotion);
            setPromoMessage(data.message || 'Đã tìm thấy mã giảm giá.', !data.promotion.is_applicable);

            if (data.promotion.is_applicable) {
                togglePromo(item);
            }
        })
        .catch(() => {
            setPromoMessage('Không thể kiểm tra mã giảm giá. Vui lòng thử lại.', true);
        });
}

function confirmPromo() {
    let finalPrice = Math.max(orderTotal - selectedDiscount, 0);

    document.getElementById('promotionCode').value = selectedPromotionCode;
    document.getElementById('discountShow').innerText = '-' + formatVnd(selectedDiscount);
    document.getElementById('finalTotal').innerText = formatVnd(finalPrice);

    document.getElementById('promoText').innerText =
        selectedPromotionCode ? 'Đã chọn mã ' + selectedPromotionCode : 'Chọn khuyến mãi và ưu đãi';

    closePromoModal();
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
