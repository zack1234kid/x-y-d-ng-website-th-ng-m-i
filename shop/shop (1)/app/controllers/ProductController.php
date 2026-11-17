<?php
// app/controllers/ProductController.php
class ProductController extends Controller
{
    public function detail($id = 0): void
    {
        $product = Product::find((int) $id);
        if (!$product || $product['status'] !== 'active') {
            $this->abort404();
            return;
        }

        $category = Category::find((int) $product['category_id']);
        $images   = ProductImage::forProduct((int) $product['id']);
        $related  = $this->fetchRelated((int) $product['category_id'], (int) $product['id']);

        $this->view('product/detail', [
            'pageTitle' => $product['name'],
            'product'   => $product,
            'category'  => $category,
            'images'    => $images,
            'related'   => $related,
        ]);
    }

    /** LIMIT 4 la hang so co dinh trong ma nguon, khong phai du lieu nguoi dung. */
    private function fetchRelated(int $categoryId, int $excludeProductId): array
    {
        $sql = "SELECT p.*, pi.file_name AS image
                FROM products p
                LEFT JOIN product_images pi
                       ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.category_id = :cid AND p.id != :pid AND p.status = 'active'
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT 4";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute(['cid' => $categoryId, 'pid' => $excludeProductId]);
        return $stmt->fetchAll();
    }
}
