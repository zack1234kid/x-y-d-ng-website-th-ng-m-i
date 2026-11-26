<?php
class CartController extends Controller
{
    public function index(): void
    {
        $cart = CartService::build();
        if ($cart['adjusted']) {
            flash('error', 'Số lượng một số sản phẩm đã được điều chỉnh theo tồn kho hiện tại.');
        }
        if ($cart['removed']) {
            flash('error', 'Đã bỏ sản phẩm không còn kinh doanh hoặc hết hàng: ' . e(implode(', ', $cart['removed'])));
        }
        $this->view('cart/index', ['pageTitle' => 'Giỏ hàng', 'cart' => $cart]);
    }

    public function add(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $product = Product::find($id);

        if (!$product || $product['status'] !== 'active') {
            $this->respond(false, 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');
            return;
        }

        $stock = (int)$product['stock'];
        if ($stock <= 0) {
            $this->respond(false, 'Sản phẩm đã hết hàng.');
            return;
        }

        $current = (int)(Cart::items()[$id] ?? 0);
        $newQty = min($stock, $current + $qty);
        Cart::setQuantity($id, $newQty);
        $message = ($current + $qty > $stock)
            ? 'Đã thêm vào giỏ. Số lượng được giới hạn theo tồn kho (' . $stock . ').'
            : 'Đã thêm sản phẩm vào giỏ hàng.';
        $this->respond(true, $message);
    }

    public function update(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $id = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 0);
        $product = Product::find($id);

        if (!$product || $product['status'] !== 'active' || $qty <= 0 || (int)$product['stock'] <= 0) {
            Cart::remove($id);
        } else {
            Cart::setQuantity($id, min($qty, (int)$product['stock']));
        }
        $this->redirect('cart/index');
    }

    public function remove(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        Cart::remove((int)($_POST['product_id'] ?? 0));
        $this->redirect('cart/index');
    }

    public function clear(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        Cart::clear();
        $this->redirect('cart/index');
    }

    private function respond(bool $success, string $message): void
    {
        $isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => $success,
                'message' => $message,
                'cart_count' => Cart::totalQuantity()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        flash($success ? 'success' : 'error', $message);
        $this->redirect('product/detail/' . (int)($_POST['product_id'] ?? 0));
    }
}
