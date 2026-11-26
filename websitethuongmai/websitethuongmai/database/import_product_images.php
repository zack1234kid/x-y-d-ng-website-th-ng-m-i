<?php
// database/import_product_images.php
// Chay MOT LAN sau khi giai nen project va import schema.sql.
// Anh da duoc dat san trong public/uploads/products/ va /thumbs/.

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance();

    $products = [
        'SP0001' => ['dien-thoai-smart-x10', 'Điện thoại Smart X10'],
        'SP0002' => ['dien-thoai-smart-x10-pro', 'Điện thoại Smart X10 Pro'],
        'SP0003' => ['may-tinh-bang-tabone-10', 'Máy tính bảng TabOne 10'],
        'SP0004' => ['dien-thoai-mini-s', 'Điện thoại Mini S'],
        'SP0005' => ['may-tinh-bang-tabone-lite', 'Máy tính bảng TabOne Lite'],
        'SP0006' => ['ao-so-mi-nam-dai-tay', 'Áo sơ mi nam dài tay'],
        'SP0007' => ['quan-tay-nam-cong-so', 'Quần tây nam công sở'],
        'SP0008' => ['ao-thun-nam-co-tron', 'Áo thun nam cổ tròn'],
        'SP0009' => ['ao-khoac-nam-chong-nuoc', 'Áo khoác nam chống nước'],
        'SP0010' => ['vay-lien-than-nu', 'Váy liền thân nữ'],
        'SP0011' => ['ao-kieu-nu-tay-phong', 'Áo kiểu nữ tay phồng'],
        'SP0012' => ['chan-vay-xep-ly', 'Chân váy xếp ly'],
        'SP0013' => ['ao-len-nu-co-lo', 'Áo len nữ cổ lọ'],
        'SP0014' => ['giay-sneaker-the-thao-nam', 'Giày sneaker thể thao nam'],
        'SP0015' => ['giay-cao-got-nu-7cm', 'Giày cao gót nữ 7cm'],
        'SP0016' => ['dep-sandal-nam-quai-ngang', 'Dép sandal nam quai ngang'],
        'SP0017' => ['giay-the-thao-nu-det-luoi', 'Giày thể thao nữ dệt lưới'],
        'SP0018' => ['noi-com-dien-1-8l', 'Nồi cơm điện 1.8L'],
        'SP0019' => ['may-xay-sinh-to-da-nang', 'Máy xay sinh tố đa năng'],
        'SP0020' => ['bo-noi-inox-5-mon', 'Bộ nồi inox 5 món'],
    ];

    $insert = $db->prepare(
        'INSERT INTO product_images
            (product_id, file_name, original_name, alt_text, is_primary, sort_order, created_at)
         VALUES (:product_id, :file_name, :original_name, :alt_text, 1, 0, NOW())'
    );
    $findProduct = $db->prepare('SELECT id, name FROM products WHERE sku = :sku LIMIT 1');
    $findPrimary = $db->prepare(
        'SELECT id FROM product_images WHERE product_id = :product_id AND is_primary = 1 LIMIT 1'
    );

    $added = 0;
    $skipped = 0;
    echo '<h2>Thêm ảnh sản phẩm ShopOnline</h2><ul>';

    foreach ($products as $sku => [$slug, $alt]) {
        $fileName = $sku . '.jpg';
        $mainPath = __DIR__ . '/../public/uploads/products/' . $fileName;
        $thumbPath = __DIR__ . '/../public/uploads/products/thumbs/' . $fileName;

        if (!is_file($mainPath) || !is_file($thumbPath)) {
            echo '<li>Thiếu file ảnh: ' . htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') . '</li>';
            continue;
        }

        $findProduct->execute(['sku' => $sku]);
        $product = $findProduct->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo '<li>Không tìm thấy sản phẩm có mã ' . htmlspecialchars($sku, ENT_QUOTES, 'UTF-8') . '</li>';
            continue;
        }

        $findPrimary->execute(['product_id' => $product['id']]);
        if ($findPrimary->fetchColumn()) {
            $skipped++;
            echo '<li>Đã có ảnh đại diện: ' . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . '</li>';
            continue;
        }

        $insert->execute([
            'product_id' => $product['id'],
            'file_name' => $fileName,
            'original_name' => $fileName,
            'alt_text' => $product['name'],
        ]);

        $added++;
        echo '<li>Đã thêm ảnh: ' . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . '</li>';
    }

    echo '</ul><p><strong>Hoàn tất.</strong> Đã thêm ' . $added .
         ' ảnh; bỏ qua ' . $skipped . ' sản phẩm đã có ảnh đại diện.</p>';
    echo '<p>Hãy mở trang chủ ShopOnline để kiểm tra.</p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h3>Có lỗi xảy ra</h3><pre>' .
         htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') .
         '</pre>';
}
