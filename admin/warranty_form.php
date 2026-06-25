<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../models/Warranty.php';

$warranty = new Warranty(db_connect());
$eligibleItems = $warranty->eligibleOrderItems();
$error = '';

if (is_post()) {
    if (!empty($_FILES['image']['name'])) {
        $dir = __DIR__ . '/../assets/uploads/warranty/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $name);

        $_POST['hinh_anh'] = 'assets/uploads/warranty/' . $name;
    }

    try {
        $warranty->createFromOrderItem(
            (int)($_POST['id_chi_tiet'] ?? 0),
            trim((string)($_POST['tinh_trang_bao_hanh'] ?? '')),
            trim((string)($_POST['ghi_chu'] ?? '')),
            (string)($_POST['trang_thai'] ?? 'cho_xu_ly'),
            trim((string)($_POST['ngay_yeu_cau'] ?? '') . ' ' . (string)($_POST['gio_yeu_cau'] ?? '')),
            trim((string)($_POST['hinh_anh'] ?? ''))
        );

        $_SESSION['success'] = 'Đã thêm sản phẩm vào danh sách bảo hành.';
        redirect('admin/warranty.php');
    } catch (Throwable $e) {
        $error = $e->getMessage() ?: 'Không thể thêm sản phẩm bảo hành.';
    }
}

$page_title = 'Thêm sản phẩm bảo hành';
require __DIR__ . '/_layout_start.php';
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/admin/warranty.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
    <div>
        <h1 class="mb-0">Thêm sản phẩm bảo hành</h1>
        <p class="text-muted mb-0">Admin chọn sản phẩm từ đơn đã giao để đưa vào danh sách bảo hành.</p>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" data-warranty-form>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                <h5 class="fw-700 mb-4">Thông tin bảo hành</h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Sản phẩm đã giao <span class="text-danger">*</span></label>
                        <select name="id_chi_tiet" class="form-select" required>
                            <option value="">-- Chọn đơn hàng / khách hàng / sản phẩm --</option>
                            <?php foreach ($eligibleItems as $item): ?>
                                <option value="<?= (int)$item['id_chi_tiet'] ?>" <?= (string)($_POST['id_chi_tiet'] ?? '') === (string)$item['id_chi_tiet'] ? 'selected' : '' ?>>
                                    Đơn #<?= (int)$item['id_don_hang'] ?>
                                    - <?= h($item['ho_ten']) ?>
                                    - <?= h($item['ten_san_pham']) ?>
                                    (<?= h($item['ma_san_pham']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Chỉ hiển thị sản phẩm thuộc đơn hàng đã giao và chưa có trong danh sách bảo hành.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trạng thái ban đầu</label>
                        <select name="trang_thai" class="form-select">
                            <?php foreach (Warranty::STATUS_LABELS as $key => $label): ?>
                                <option value="<?= h($key) ?>" <?= (($_POST['trang_thai'] ?? 'cho_xu_ly') === $key) ? 'selected' : '' ?>>
                                    <?= h($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ngày yêu cầu <span class="text-danger">*</span></label>
                        <input name="ngay_yeu_cau" type="date" class="form-control"
                               value="<?= h($_POST['ngay_yeu_cau'] ?? date('Y-m-d')) ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Giờ yêu cầu <span class="text-danger">*</span></label>
                        <input name="gio_yeu_cau" type="text" class="form-control"
                               value="<?= h($_POST['gio_yeu_cau'] ?? date('H:i')) ?>"
                               placeholder="HH:mm" inputmode="numeric" maxlength="5"
                               pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" data-time-mask required>
                        <div class="invalid-feedback">Giờ không hợp lệ. Vui lòng nhập theo dạng 00:00 đến 23:59.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Tình trạng bảo hành</label>
                        <textarea name="tinh_trang_bao_hanh" class="form-control auto-grow-textarea" rows="4"
                                  data-autogrow
                                  placeholder="VD: Lỗi bàn phím, lỗi nguồn, không lên màn hình, cần kiểm tra pin..."><?= h($_POST['tinh_trang_bao_hanh'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Ghi chú tình trạng máy</label>
                        <textarea name="ghi_chu" class="form-control auto-grow-textarea" rows="4"
                                  data-autogrow
                                  placeholder="VD: Máy trầy xước góc trái, tróc sơn mặt lưng, thiếu hộp, thiếu phụ kiện..."><?= h($_POST['ghi_chu'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 shadow-sm">
                <h5 class="fw-700 mb-4">Hình ảnh</h5>

                <div class="mb-3">
                    <label class="form-label">Đường dẫn ảnh (URL)</label>
                    <input name="hinh_anh" class="form-control"
                           placeholder="assets/images/..."
                           value="<?= h($_POST['hinh_anh'] ?? 'assets/images/no-image.jpg') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Hoặc tải ảnh lên</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Tải ảnh lên sẽ ghi đè đường dẫn ở trên.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-3 mt-3">
        <button type="submit" class="btn btn-primary btn-lg" <?= !$eligibleItems ? 'disabled' : '' ?>>
            <i class="bi bi-check-lg me-2"></i>Thêm sản phẩm bảo hành
        </button>
        <a href="<?= BASE_URL ?>/admin/warranty.php" class="btn btn-outline-secondary btn-lg">Hủy</a>
    </div>
</form>

<script>
document.querySelectorAll('[data-autogrow]').forEach(function (textarea) {
    const resize = function () {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    };

    textarea.addEventListener('input', resize);
    resize();
});

document.querySelectorAll('[data-time-mask]').forEach(function (input) {
    const timePattern = /^([01][0-9]|2[0-3]):[0-5][0-9]$/;

    const validateTime = function () {
        const hasEnoughDigits = input.value.replace(/\D/g, '').length === 4;
        const isValid = timePattern.test(input.value);

        input.classList.toggle('is-invalid', hasEnoughDigits && !isValid);
        input.setCustomValidity(isValid ? '' : 'Giờ không hợp lệ. Vui lòng nhập theo dạng 00:00 đến 23:59.');
    };

    const formatTime = function () {
        let digits = input.value.replace(/\D/g, '').slice(0, 4);
        if (digits.length >= 3) {
            digits = digits.slice(0, 2) + ':' + digits.slice(2);
        }

        input.value = digits;
        validateTime();
    };

    input.addEventListener('input', formatTime);
    input.addEventListener('blur', function () {
        validateTime();
    });
    formatTime();
});

document.querySelectorAll('[data-time-mask]').forEach(function (input) {
    const timePattern = /^([01][0-9]|2[0-3]):[0-5][0-9]$/;
    const invalidMessage = 'Giờ không hợp lệ. Vui lòng nhập theo dạng 00:00 đến 23:59.';

    input.dataset.lastAccepted = timePattern.test(input.value) ? input.value : '';

    const formatDigits = function (digits) {
        return digits.length >= 3 ? digits.slice(0, 2) + ':' + digits.slice(2) : digits;
    };

    const isValidPartial = function (digits) {
        if (digits.length >= 2 && Number(digits.slice(0, 2)) > 23) {
            return false;
        }
        if (digits.length === 4 && Number(digits.slice(2, 4)) > 59) {
            return false;
        }
        return true;
    };

    const markValidity = function () {
        const isValid = timePattern.test(input.value);
        input.setCustomValidity(isValid ? '' : invalidMessage);
        return isValid;
    };

    input.addEventListener('input', function () {
        const digits = input.value.replace(/\D/g, '').slice(0, 4);

        if (!isValidPartial(digits)) {
            input.value = input.dataset.lastAccepted || '';
            input.classList.add('is-invalid');
            input.setCustomValidity(invalidMessage);
            return;
        }

        input.value = formatDigits(digits);
        input.dataset.lastAccepted = input.value;
        input.classList.toggle('is-invalid', digits.length === 4 && !timePattern.test(input.value));
        markValidity();
    });

    input.addEventListener('blur', function () {
        input.classList.toggle('is-invalid', !markValidity());
    });
});

document.querySelector('[data-warranty-form]')?.addEventListener('submit', function (event) {
    const timeInput = this.querySelector('[data-time-mask]');
    if (timeInput && !timeInput.checkValidity()) {
        event.preventDefault();
        timeInput.classList.add('is-invalid');
        timeInput.focus();
        timeInput.reportValidity();
    }
});
</script>

<?php require __DIR__ . '/_layout_end.php'; ?>
