<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../models/Warranty.php';

$page_title = 'Bảo hành';
$warranty = new Warranty(db_connect());

if (is_post()) {
    try {
        $action = (string)($_POST['action'] ?? '');

        if ($action === 'update_status') {
            $warranty->updateStatus(
                (int)($_POST['id_bao_hanh'] ?? 0),
                (string)($_POST['trang_thai'] ?? '')
            );
            $_SESSION['success'] = 'Đã cập nhật trạng thái bảo hành.';
        }
    } catch (Throwable $e) {
        $_SESSION['error'] = $e->getMessage() ?: 'Không thể xử lý yêu cầu bảo hành.';
    }

    redirect('admin/warranty.php');
}

$keyword = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$warranties = $warranty->all($keyword, $status);
$success = flash('success');
$error = flash('error');

require __DIR__ . '/_layout_start.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Quản lý bảo hành</h1>
        <p class="text-muted mb-0">Admin thêm sản phẩm đã giao vào bảo hành và xử lý trạng thái cho khách hàng.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/warranty_form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Thêm sản phẩm
    </a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill"></i><?= h($success) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill"></i><?= h($error) ?>
    </div>
<?php endif; ?>

<div class="bg-white p-4 rounded-4 shadow-sm mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Tìm kiếm</label>
            <input name="q" class="form-control" value="<?= h($keyword) ?>" placeholder="Mã bảo hành, đơn hàng, khách hàng, sản phẩm...">
        </div>

        <div class="col-md-4">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <?php foreach (Warranty::STATUS_LABELS as $key => $label): ?>
                    <option value="<?= h($key) ?>" <?= $status === $key ? 'selected' : '' ?>>
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search me-2"></i>Lọc
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-4 rounded-4 shadow-sm table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Mã BH</th>
                <th>Đơn hàng</th>
                <th>Khách hàng</th>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Ngày yêu cầu</th>
                <th>Trạng thái</th>
                <th class="text-end">Xử lý</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($warranties as $item): ?>
            <tr>
                <td class="fw-800"><?= h($item['ma_bao_hanh']) ?></td>
                <td>#<?= (int)$item['id_don_hang'] ?></td>
                <td>
                    <div class="fw-700"><?= h($item['ho_ten']) ?></div>
                    <div class="text-muted small"><?= h($item['email']) ?></div>
                </td>
                <td>
                    <img src="<?= h(product_img_url($item['hinh_anh'] ?? '')) ?>"
                         class="rounded-3 border"
                         style="width:64px;height:64px;object-fit:cover"
                         alt="Ảnh bảo hành">
                </td>
                <td>
                    <div><?= h($item['ten_san_pham']) ?></div>
                    <div class="text-muted small"><?= h($item['ma_san_pham']) ?></div>
                    <?php if (!empty($item['tinh_trang_bao_hanh'])): ?>
                        <div class="small mt-1"><strong>Bảo hành:</strong> <?= h($item['tinh_trang_bao_hanh']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['ghi_chu'])): ?>
                        <div class="small text-muted"><strong>Tình trạng máy:</strong> <?= h($item['ghi_chu']) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= h(date('Y-m-d', strtotime((string)$item['ngay_yeu_cau']))) ?></td>
                <td>
                    <span class="badge rounded-pill bg-<?= h(Warranty::statusBadge($item['trang_thai'])) ?>">
                        <?= h(Warranty::statusLabel($item['trang_thai'])) ?>
                    </span>
                </td>
                <td class="text-end">
                    <form method="post" class="d-inline-flex gap-2 justify-content-end">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id_bao_hanh" value="<?= (int)$item['id_bao_hanh'] ?>">
                        <select name="trang_thai" class="form-select form-select-sm" style="width:160px">
                            <?php foreach (Warranty::STATUS_LABELS as $key => $label): ?>
                                <option value="<?= h($key) ?>" <?= $item['trang_thai'] === $key ? 'selected' : '' ?>>
                                    <?= h($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-primary">Lưu</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (!$warranties): ?>
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    Chưa có sản phẩm nào trong danh sách bảo hành.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>
