<?php
declare(strict_types=1);

class Promotion
{
    public function __construct(private PDO $pdo) {}

    public function visibleCheckoutPromotions(float $orderTotal): array
    {
        $stmt = $this->pdo->query("
            SELECT *
            FROM khuyen_mai
            WHERE kieu_giam = 'tien_mat'
              AND trang_thai = 'dang_dien_ra'
              AND hien_thi_checkout = 1
              AND ma_code IS NOT NULL
              AND ma_code <> ''
              AND NOW() BETWEEN ngay_bat_dau AND ngay_ket_thuc
            ORDER BY so_tien_giam DESC, id_khuyen_mai DESC
        ");

        return array_map(
            fn (array $promotion): array => $this->formatForCheckout($promotion, $orderTotal),
            $stmt->fetchAll()
        );
    }

    public function findCheckoutPromotionByCode(string $code, float $orderTotal): ?array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM khuyen_mai
            WHERE kieu_giam = 'tien_mat'
              AND trang_thai = 'dang_dien_ra'
              AND ma_code IS NOT NULL
              AND ma_code <> ''
              AND UPPER(ma_code) = :code
              AND NOW() BETWEEN ngay_bat_dau AND ngay_ket_thuc
            LIMIT 1
        ");
        $stmt->execute([':code' => $code]);

        $promotion = $stmt->fetch();
        return $promotion ? $this->formatForCheckout($promotion, $orderTotal) : null;
    }

    private function formatForCheckout(array $promotion, float $orderTotal): array
    {
        $minOrder = (float)($promotion['don_toi_thieu'] ?? 0);
        $discount = (float)($promotion['so_tien_giam'] ?? 0);
        $isApplicable = $orderTotal >= $minOrder && $discount > 0;
        $discountAmount = $isApplicable ? min($discount, $orderTotal) : 0.0;

        return [
            'id_khuyen_mai' => (int)$promotion['id_khuyen_mai'],
            'ma_code' => (string)$promotion['ma_code'],
            'ten_khuyen_mai' => (string)$promotion['ten_khuyen_mai'],
            'so_tien_giam' => $discount,
            'don_toi_thieu' => $minOrder,
            'discount_amount' => $discountAmount,
            'is_applicable' => $isApplicable,
            'hien_thi_checkout' => (int)($promotion['hien_thi_checkout'] ?? 1),
        ];
    }
}
