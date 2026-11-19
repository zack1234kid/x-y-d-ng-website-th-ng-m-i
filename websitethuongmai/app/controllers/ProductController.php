
<?php
// app/controllers/ProductController.php

class ProductController extends Controller
{
    // Số sản phẩm hiển thị trên mỗi trang
    private const PER_PAGE = 12;

    // Các kiểu sắp xếp sản phẩm
    public const SORT_LABELS = [
        'newest'     => 'Mới nhất',
        'price_asc'  => 'Giá tăng dần',
        'price_desc' => 'Giá giảm dần',
        'bestseller' => 'Bán chạy',
    ];

    // ==========================================
    // CHI TIẾT SẢN PHẨM
    // ==========================================
    public function detail($id = 0): void
    {
        $product = Product::find((int) $id);

        if (!$product || $product['status'] !== 'active') {
            $this->abort404();
            return;
        }

        $category = Category::find((int) $product['category_id']);
        $images   = ProductImage::forProduct((int) $product['id']);
        $related  = $this->fetchRelated(
            (int) $product['category_id'],
            (int) $product['id']
        );

        $this->view('product/detail', [
            'pageTitle' => $product['name'],
            'product'   => $product,
            'category'  => $category,
            'images'    => $images,
            'related'   => $related,
        ]);
    }

    // ==========================================
    // TÌM KIẾM, LỌC VÀ SẮP XẾP SẢN PHẨM - BUỔI 5
    // ==========================================
    public function search(): void
    {
        // Lấy từ khóa và điều kiện lọc từ URL
        $filters = [
            'q'          => trim((string) ($_GET['q'] ?? '')),
            'category'   => $_GET['category'] ?? '',
            'brand'      => $_GET['brand'] ?? [],
            'price_from' => $_GET['price_from'] ?? '',
            'price_to'   => $_GET['price_to'] ?? '',
            'sort'       => $_GET['sort'] ?? 'newest',
        ];

        // Lấy số trang hiện tại
        $page = max(1, (int) ($_GET['page'] ?? 1));

        // Tìm kiếm sản phẩm
        $result = Product::search(
            $filters,
            $page,
            self::PER_PAGE
        );

        // Hiển thị trang kết quả
        $this->view('product/search', [
            'pageTitle' => 'Tìm kiếm sản phẩm',
            'filters'   => $filters,
            'categories' => Category::activeList(),
            'brands'    => Product::distinctBrands(),
            'result'    => $result,
            'sortLabels' => self::SORT_LABELS,
        ]);
    }

    // ==========================================
    // LẤY SẢN PHẨM LIÊN QUAN
    // ==========================================
    private function fetchRelated(
        int $categoryId,
        int $excludeProductId
    ): array {
        $sql = "SELECT p.*, pi.file_name AS image
                FROM products p
                LEFT JOIN product_images pi
                       ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.category_id = :cid
                  AND p.id != :pid
                  AND p.status = 'active'
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT 4";

        $stmt = Database::getInstance()->prepare($sql);

        $stmt->execute([
            'cid' => $categoryId,
            'pid' => $excludeProductId
        ]);

        return $stmt->fetchAll();
    }
}
