<?php
// Doi chieu session voi CSDL; gia va thong tin san pham luon doc tu CSDL.
class CartService
{
    public static function build(): array
    {
        $items = Cart::items();
        if (!$items) {
            return ['lines' => [], 'subtotal' => 0, 'adjusted' => false, 'removed' => []];
        }

        $products = Product::findManyWithImage(array_keys($items));
        $lines = [];
        $subtotal = 0.0;
        $adjusted = false;
        $removed = [];

        foreach ($items as $id => $qty) {
            $id = (int)$id;
            $product = $products[$id] ?? null;

            if (!$product || $product['status'] !== 'active') {
                Cart::remove($id);
                $removed[] = $product['name'] ?? ('Sản phẩm #' . $id);
                continue;
            }

            $stock = max(0, (int)$product['stock']);
            $qty = max(1, (int)$qty);
            if ($stock <= 0) {
                Cart::remove($id);
                $removed[] = $product['name'];
                continue;
            }
            if ($qty > $stock) {
                $qty = $stock;
                Cart::setQuantity($id, $qty);
                $adjusted = true;
            }

            $price = (float)$product['price'];
            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;
            $lines[] = [
                'product_id' => $id,
                'name' => $product['name'],
                'price' => $price,
                'quantity' => $qty,
                'stock' => $stock,
                'line_total' => $lineTotal,
                'image' => $product['image'] ?? null,
            ];
        }

        return compact('lines', 'subtotal', 'adjusted', 'removed');
    }
}
