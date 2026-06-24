<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = 'Bảo hành';
require __DIR__ . '/_layout_start.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="mb-1">Quản lý bảo hành</h1>
        <p class="text-muted mb-0">Theo dõi và xử lý các yêu cầu bảo hành từ khách hàng.</p>
    </div>
</div>

<div class="bg-white p-4 rounded-4 shadow-sm mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Tìm kiếm</label>
            <input name="q" class="form-control" placeholder="Mã đơn hàng, khách hàng, sản phẩm...">
        </div>

        <div class="col-md-4">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="cho_xu_ly">Chờ xử lý</option>
                <option value="dang_bao_hanh">Đang bảo hành</option>
                <option value="hoan_thanh">Hoàn thành</option>
                <option value="tu_choi">Từ chối</option>
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
                <th>Sản phẩm</th>
                <th>Ngày yêu cầu</th>
                <th>Trạng thái</th>
                <th class="text-end">Xử lý</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="fw-800">BH001</td>
                <td>#1</td>
                <td>
                    <div class="fw-700">Nguyễn Văn A</div>
                    <div class="text-muted small">nguyenvana@gmail.com</div>
                </td>
                <td>Logitech K380</td>
                <td>2026-06-18</td>
                <td>
                    <span class="badge rounded-pill bg-warning text-dark">Chờ xử lý</span>
                </td>
                <td class="text-end">
                    <select class="form-select form-select-sm d-inline-block" style="width:150px">
                        <option>Chờ xử lý</option>
                        <option>Đang bảo hành</option>
                        <option>Hoàn thành</option>
                        <option>Từ chối</option>
                    </select>
                    <button class="btn btn-sm btn-primary">Lưu</button>
                </td>
            </tr>

            <tr>
                <td class="fw-800">BH002</td>
                <td>#3</td>
                <td>
                    <div class="fw-700">Trần Minh Quân</div>
                    <div class="text-muted small">quan@example.com</div>
                </td>
                <td>Chuột Logitech M331</td>
                <td>2026-06-17</td>
                <td>
                    <span class="badge rounded-pill bg-primary">Đang bảo hành</span>
                </td>
                <td class="text-end">
                    <select class="form-select form-select-sm d-inline-block" style="width:150px">
                        <option>Chờ xử lý</option>
                        <option selected>Đang bảo hành</option>
                        <option>Hoàn thành</option>
                        <option>Từ chối</option>
                    </select>
                    <button class="btn btn-sm btn-primary">Lưu</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>