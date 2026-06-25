<?php
declare(strict_types=1);

class Warranty
{
    public const STATUS_LABELS = [
        'cho_xu_ly' => 'Chờ xử lý',
        'dang_bao_hanh' => 'Đang bảo hành',
        'hoan_thanh' => 'Hoàn thành',
        'tu_choi' => 'Từ chối',
    ];

    public const STATUS_BADGES = [
        'cho_xu_ly' => 'warning text-dark',
        'dang_bao_hanh' => 'primary',
        'hoan_thanh' => 'success',
        'tu_choi' => 'danger',
    ];

    public function __construct(private PDO $pdo)
    {
        $this->ensureTable();
    }

    public function all(string $keyword = '', string $status = ''): array
    {
        $where = [];
        $params = [];

        if ($status !== '' && isset(self::STATUS_LABELS[$status])) {
            $where[] = 'bh.trang_thai = :status';
            $params[':status'] = $status;
        }

        if ($keyword !== '') {
            $where[] = "(
                bh.ma_bao_hanh LIKE :keyword
                OR CAST(bh.id_don_hang AS CHAR) LIKE :keyword
                OR nd.ho_ten LIKE :keyword
                OR nd.email LIKE :keyword
                OR sp.ten_san_pham LIKE :keyword
                OR sp.ma_san_pham LIKE :keyword
            )";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql = "
            SELECT bh.*, nd.ho_ten, nd.email, sp.ten_san_pham, sp.ma_san_pham
            FROM bao_hanh bh
            JOIN nguoi_dung nd ON nd.id_nguoi_dung = bh.id_nguoi_dung
            JOIN san_pham sp ON sp.id_san_pham = bh.id_san_pham
        ";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY bh.ngay_yeu_cau DESC, bh.id_bao_hanh DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function eligibleOrderItems(): array
    {
        return $this->pdo->query("
            SELECT ct.id_chi_tiet, ct.id_don_hang, ct.id_san_pham,
                   nd.ho_ten, nd.email,
                   sp.ten_san_pham, sp.ma_san_pham
            FROM chi_tiet_don_hang ct
            JOIN don_hang dh ON dh.id_don_hang = ct.id_don_hang
            JOIN nguoi_dung nd ON nd.id_nguoi_dung = dh.id_nguoi_dung
            JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham
            LEFT JOIN bao_hanh bh
                   ON bh.id_don_hang = ct.id_don_hang
                  AND bh.id_san_pham = ct.id_san_pham
            WHERE dh.trang_thai_don_hang = 'da_giao'
              AND bh.id_bao_hanh IS NULL
            ORDER BY dh.ngay_dat DESC, ct.id_chi_tiet DESC
        ")->fetchAll();
    }

    public function createFromOrderItem(
        int $detailId,
        string $warrantyCondition = '',
        string $machineCondition = '',
        string $status = 'cho_xu_ly',
        string $requestDate = '',
        string $imagePath = ''
    ): void
    {
        if (!isset(self::STATUS_LABELS[$status])) {
            throw new Exception('Trạng thái bảo hành không hợp lệ.');
        }

        $requestDate = trim($requestDate);
        $requestDateSql = date('Y-m-d H:i:s');
        if ($requestDate !== '') {
            $date = DateTime::createFromFormat('Y-m-d H:i', $requestDate);
            $errors = DateTime::getLastErrors();
            if (!$date || ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
                throw new Exception('Ngày yêu cầu bảo hành không hợp lệ.');
            }
            $requestDateSql = $date->format('Y-m-d H:i:s');
        }

        $stmt = $this->pdo->prepare("
            SELECT ct.id_don_hang, ct.id_san_pham, dh.id_nguoi_dung
            FROM chi_tiet_don_hang ct
            JOIN don_hang dh ON dh.id_don_hang = ct.id_don_hang
            LEFT JOIN bao_hanh bh
                   ON bh.id_don_hang = ct.id_don_hang
                  AND bh.id_san_pham = ct.id_san_pham
            WHERE ct.id_chi_tiet = :detail_id
              AND dh.trang_thai_don_hang = 'da_giao'
              AND bh.id_bao_hanh IS NULL
            LIMIT 1
        ");
        $stmt->execute([':detail_id' => $detailId]);
        $item = $stmt->fetch();

        if (!$item) {
            throw new Exception('Sản phẩm không hợp lệ, chưa giao thành công hoặc đã có trong danh sách bảo hành.');
        }

        $insert = $this->pdo->prepare("
            INSERT INTO bao_hanh
                (ma_bao_hanh, id_don_hang, id_san_pham, id_nguoi_dung, ngay_yeu_cau, trang_thai, tinh_trang_bao_hanh, ghi_chu, hinh_anh)
            VALUES
                (:code, :order_id, :product_id, :user_id, :request_date, :status, :warranty_condition, :machine_condition, :image_path)
        ");
        $insert->execute([
            ':code' => $this->nextCode(),
            ':order_id' => (int)$item['id_don_hang'],
            ':product_id' => (int)$item['id_san_pham'],
            ':user_id' => (int)$item['id_nguoi_dung'],
            ':request_date' => $requestDateSql,
            ':status' => $status,
            ':warranty_condition' => $warrantyCondition !== '' ? $warrantyCondition : null,
            ':machine_condition' => $machineCondition !== '' ? $machineCondition : null,
            ':image_path' => $imagePath !== '' ? $imagePath : 'assets/images/no-image.jpg',
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        if (!isset(self::STATUS_LABELS[$status])) {
            throw new Exception('Trạng thái bảo hành không hợp lệ.');
        }

        $stmt = $this->pdo->prepare("
            UPDATE bao_hanh
            SET trang_thai = :status
            WHERE id_bao_hanh = :id
        ");
        $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
    }

    public static function statusBadge(string $status): string
    {
        return self::STATUS_BADGES[$status] ?? 'secondary';
    }

    private function nextCode(): string
    {
        $next = (int)$this->pdo->query('SELECT COALESCE(MAX(id_bao_hanh), 0) + 1 FROM bao_hanh')->fetchColumn();
        return 'BH' . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }

    private function ensureTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS bao_hanh (
                id_bao_hanh INT NOT NULL AUTO_INCREMENT,
                ma_bao_hanh VARCHAR(20) NOT NULL,
                id_don_hang INT NOT NULL,
                id_san_pham INT NOT NULL,
                id_nguoi_dung INT NOT NULL,
                ngay_yeu_cau DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                trang_thai ENUM('cho_xu_ly','dang_bao_hanh','hoan_thanh','tu_choi') NOT NULL DEFAULT 'cho_xu_ly',
                tinh_trang_bao_hanh TEXT DEFAULT NULL,
                ghi_chu TEXT DEFAULT NULL,
                hinh_anh VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (id_bao_hanh),
                UNIQUE KEY uk_bao_hanh_ma (ma_bao_hanh),
                UNIQUE KEY uk_bao_hanh_order_product (id_don_hang, id_san_pham),
                KEY idx_bao_hanh_user (id_nguoi_dung),
                KEY idx_bao_hanh_product (id_san_pham)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $columns = $this->pdo->query('SHOW COLUMNS FROM bao_hanh')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('tinh_trang_bao_hanh', $columns, true)) {
            $this->pdo->exec('ALTER TABLE bao_hanh ADD COLUMN tinh_trang_bao_hanh TEXT DEFAULT NULL AFTER trang_thai');
            $columns[] = 'tinh_trang_bao_hanh';
        }

        if (!in_array('ghi_chu', $columns, true)) {
            $this->pdo->exec('ALTER TABLE bao_hanh ADD COLUMN ghi_chu TEXT DEFAULT NULL AFTER tinh_trang_bao_hanh');
            $columns[] = 'ghi_chu';
        }

        if (!in_array('hinh_anh', $columns, true)) {
            $this->pdo->exec('ALTER TABLE bao_hanh ADD COLUMN hinh_anh VARCHAR(255) DEFAULT NULL AFTER ghi_chu');
        }
    }
}
