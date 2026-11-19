
<?php
// app/models/Product.php

class Product extends Model
{
    protected static string $table = 'products';

    // Danh sách cách sắp xếp được phép
    public const SORT_OPTIONS = [
        'newest'     => 'p.created_at DESC',
        'price_asc'  => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'bestseller' => 'p.sold_count DESC',
    ];

    // Tìm kiếm, lọc, sắp xếp và phân trang
    public static function search(
        array $filters,
        int $page = 1,
        int $perPage = 12
    ): array {

        $where = ["p.status = 'active'"];
        $params = [];

        // 1. Tìm kiếm từ khóa
        $keyword = trim((string)($filters['q'] ?? ''));

        if ($keyword !== '') {
            $where[] = '(p.name LIKE :kw1
                         OR p.sku LIKE :kw2
                         OR p.description LIKE :kw3)';

            $params['kw1'] = '%' . $keyword . '%';
            $params['kw2'] = '%' . $keyword . '%';
            $params['kw3'] = '%' . $keyword . '%';
        }

        // 2. Lọc danh mục
        $categoryId = (int)($filters['category'] ?? 0);

        if ($categoryId > 0) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        // 3. Lọc thương hiệu
        $brands = array_filter(
            (array)($filters['brand'] ?? []),
            fn($b) => trim((string)$b) !== ''
        );

        if (!empty($brands)) {
            $placeholders = [];

            foreach (array_values($brands) as $i => $brand) {
                $key = 'brand' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $brand;
            }

            $where[] = 'p.brand IN (' .
                implode(',', $placeholders) . ')';
        }

        // 4. Lọc giá từ
        if (
            ($filters['price_from'] ?? '') !== ''
            && is_numeric($filters['price_from'])
        ) {
            $where[] = 'p.price >= :price_from';
            $params['price_from'] = (float)$filters['price_from'];
        }

        // 5. Lọc giá đến
        if (
            ($filters['price_to'] ?? '') !== ''
            && is_numeric($filters['price_to'])
        ) {
            $where[] = 'p.price <= :price_to';
            $params['price_to'] = (float)$filters['price_to'];
        }

        $whereSql = implode(' AND ', $where);

        // 6. Sắp xếp bằng whitelist
        $sortKey = (string)($filters['sort'] ?? 'newest');

        $orderBy = self::SORT_OPTIONS[$sortKey]
            ?? self::SORT_OPTIONS['newest'];

        // 7. Phân trang
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        // Truy vấn 1: Đếm tổng sản phẩm
        $countSql = "
            SELECT COUNT(*)
            FROM products p
            WHERE $whereSql
        ";

        $countStmt = static::db()->prepare($countSql);
        $countStmt->execute($params);

        Database::$queryCount++;

        $total = (int)$countStmt->fetchColumn();

        // Truy vấn 2: Lấy sản phẩm và ảnh đại diện
        $sql = "
            SELECT p.*, pi.file_name AS image
            FROM products p

            LEFT JOIN product_images pi
                ON pi.product_id = p.id
                AND pi.is_primary = 1

            WHERE $whereSql

            ORDER BY $orderBy

            LIMIT :limit OFFSET :offset
        ";

        $stmt = static::db()->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        Database::$queryCount++;

        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $total > 0
                ? (int)ceil($total / $perPage)
                : 1
        ];
    }

    // Lấy danh sách thương hiệu không trùng nhau
    public static function distinctBrands(): array
    {
        $sql = "
            SELECT DISTINCT brand
            FROM products
            WHERE status = 'active'
            AND brand IS NOT NULL
            AND brand != ''
            ORDER BY brand ASC
        ";

        return static::db()
            ->query($sql)
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    // Kiểm tra SKU đã tồn tại
    public static function skuExists(
        string $sku,
        ?int $excludeId = null
    ): bool {

        $sql = 'SELECT COUNT(*) FROM products WHERE sku = :sku';
        $params = ['sku' => $sku];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }
}
