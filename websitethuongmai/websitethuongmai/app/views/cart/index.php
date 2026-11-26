<?php
$lines = $cart['lines'] ?? [];
$subtotal = (float)($cart['subtotal'] ?? 0);
?>
<style>
.cart-page{color:#1f2d49}
.cart-breadcrumb{font-size:.92rem;color:#718096;margin-bottom:1.1rem}
.cart-title{font-size:1.8rem;font-weight:750;margin-bottom:1.35rem}
.cart-layout{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:22px;align-items:start}
.cart-panel,.cart-summary,.cart-empty{background:#fff;border:1px solid #e1e7f0;border-radius:14px;overflow:hidden}
.cart-panel-head{display:grid;grid-template-columns:minmax(220px,1fr) 115px 170px 125px 75px;gap:12px;padding:17px 20px;background:#f7f9fd;border-bottom:1px solid #e5eaf2;font-weight:700}
.cart-row{display:grid;grid-template-columns:minmax(220px,1fr) 115px 170px 125px 75px;gap:12px;align-items:center;padding:17px 20px;border-bottom:1px solid #edf0f5}
.cart-row:last-child{border-bottom:0}
.cart-product{display:flex;align-items:center;gap:14px;min-width:0}
.cart-product img{width:92px;height:92px;object-fit:contain;background:#f7f8fa;border:1px solid #edf0f3;border-radius:10px;padding:5px;flex-shrink:0}
.cart-product-name{font-weight:700;color:#263653;text-decoration:none;display:block;margin-bottom:5px}
.cart-product-name:hover{color:#0d6efd}
.cart-muted{font-size:.86rem;color:#7a879b}
.cart-price{font-weight:700;color:#1769df;white-space:nowrap}
.cart-line-total{font-weight:750;white-space:nowrap}
.cart-qty-form{display:flex;align-items:center;gap:5px}
.cart-qty-form input{width:54px;text-align:center;padding:.35rem .2rem}
.cart-qty-form button{width:30px;height:34px;padding:0;display:grid;place-items:center}
.cart-remove{border:0;background:transparent;color:#dc3545;padding:5px;font-size:.9rem}
.cart-remove:hover{text-decoration:underline}
.cart-panel-actions{display:flex;justify-content:space-between;align-items:center;padding:15px 20px;background:#fff}
.cart-summary{padding:0 19px 18px;position:sticky;top:18px}
.cart-summary-head{margin:0 -19px 17px;padding:17px 19px;background:#1e2c49;color:white;font-size:1.12rem;font-weight:700}
.cart-summary-line{display:flex;justify-content:space-between;gap:12px;margin:13px 0;color:#566176}
.cart-summary-total{border-top:1px solid #e3e8ef;margin-top:17px;padding-top:17px;display:flex;justify-content:space-between;align-items:center;font-size:1.1rem;font-weight:750}
.cart-summary-total strong{font-size:1.35rem;color:#1268df}
.cart-checkout{width:100%;padding:12px;margin-top:19px;font-weight:700;border-radius:9px}
.cart-safe{text-align:center;font-size:.82rem;color:#78859a;margin-top:13px}
.cart-empty{padding:42px 20px;text-align:center}
.cart-empty-icon{width:76px;height:76px;border-radius:50%;display:grid;place-items:center;margin:0 auto 15px;background:#edf4ff;color:#1769df;font-size:2.25rem}
.cart-empty h2{font-size:1.25rem;font-weight:750}
.cart-empty p{color:#748197;margin-bottom:20px}
.cart-continue{display:inline-block;border:1px solid #1769df;color:#1769df;border-radius:9px;padding:10px 19px;text-decoration:none;font-weight:650}
.cart-continue:hover{background:#1769df;color:#fff}
@media(max-width:1100px){
 .cart-layout{grid-template-columns:minmax(0,1fr) 290px}
 .cart-panel-head,.cart-row{grid-template-columns:minmax(180px,1fr) 95px 150px 105px 55px;padding-left:14px;padding-right:14px;gap:8px}
 .cart-product img{width:74px;height:74px}
}
@media(max-width:800px){
 .cart-layout{grid-template-columns:1fr}
 .cart-summary{position:static}
 .cart-panel-head{display:none}
 .cart-row{grid-template-columns:minmax(0,1fr) auto;gap:10px;padding:14px}
 .cart-product{grid-column:1/-1}
 .cart-row .cart-price:before{content:"Đơn giá: ";font-size:.8rem;color:#8490a2;font-weight:400}
 .cart-qty-form{grid-column:1}
 .cart-line-total{grid-column:2;grid-row:3;text-align:right}
 .cart-row>form:last-child{grid-column:2;grid-row:2;text-align:right}
 .cart-panel-actions{gap:10px;flex-wrap:wrap;padding:12px 14px}
}
</style>

<div class="cart-page">
    <div class="cart-breadcrumb">
        <a href="<?= e(base_url('')) ?>" class="text-decoration-none">Trang chủ</a>
        <span class="mx-2">›</span> Giỏ hàng
    </div>

    <h1 class="cart-title">🛒 Giỏ hàng của bạn</h1>

    <?php if (empty($lines)): ?>
        <section class="cart-empty">
            <div class="cart-empty-icon">🛒</div>
            <h2>Giỏ hàng của bạn đang trống!</h2>
            <p>Hãy thêm những sản phẩm yêu thích để tiếp tục mua sắm nhé.</p>
            <a class="btn btn-primary px-4 py-2" href="<?= e(base_url('')) ?>">Tiếp tục mua sắm</a>
        </section>
    <?php else: ?>
        <div class="cart-layout">
            <section class="cart-panel">
                <div class="cart-panel-head">
                    <div>Sản phẩm</div>
                    <div>Đơn giá</div>
                    <div>Số lượng</div>
                    <div>Thành tiền</div>
                    <div>Thao tác</div>
                </div>

                <?php foreach ($lines as $line): ?>
                    <div class="cart-row">
                        <div class="cart-product">
                            <a href="<?= e(base_url('product/detail/' . (int)$line['product_id'])) ?>">
                                <img src="<?= e(product_image_url($line['image'], 'thumb')) ?>"
                                     alt="<?= e($line['name']) ?>">
                            </a>
                            <div class="min-w-0">
                                <a class="cart-product-name" href="<?= e(base_url('product/detail/' . (int)$line['product_id'])) ?>">
                                    <?= e($line['name']) ?>
                                </a>
                                <div class="cart-muted">Còn <?= (int)$line['stock'] ?> sản phẩm</div>
                            </div>
                        </div>

                        <div class="cart-price"><?= format_vnd($line['price']) ?></div>

                        <form method="POST" action="<?= e(base_url('cart/update')) ?>" class="cart-qty-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int)$line['product_id'] ?>">
                            <button class="btn btn-outline-secondary btn-sm" type="button" aria-label="Giảm số lượng"
                                    onclick="changeCartQty(this,-1)">−</button>
                            <input class="form-control form-control-sm" type="number" name="quantity"
                                   value="<?= (int)$line['quantity'] ?>" min="1" max="<?= (int)$line['stock'] ?>"
                                   aria-label="Số lượng">
                            <button class="btn btn-outline-secondary btn-sm" type="button" aria-label="Tăng số lượng"
                                    onclick="changeCartQty(this,1)">+</button>
                            <button class="btn btn-sm btn-primary ms-1" type="submit" title="Cập nhật số lượng">Lưu</button>
                        </form>

                        <div class="cart-line-total"><?= format_vnd($line['line_total']) ?></div>

                        <form method="POST" action="<?= e(base_url('cart/remove')) ?>"
                              onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int)$line['product_id'] ?>">
                            <button class="cart-remove" type="submit">🗑 Xóa</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <div class="cart-panel-actions">
                    <a href="<?= e(base_url('')) ?>" class="cart-continue">← Tiếp tục mua sắm</a>
                    <form method="POST" action="<?= e(base_url('cart/clear')) ?>"
                          onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?');">
                        <?= csrf_field() ?>
                        <button class="btn btn-outline-danger btn-sm" type="submit">Xóa toàn bộ giỏ hàng</button>
                    </form>
                </div>
            </section>

            <aside class="cart-summary">
                <div class="cart-summary-head">▤ &nbsp; Tóm tắt đơn hàng</div>
                <div class="cart-summary-line">
                    <span>Tạm tính</span>
                    <strong><?= format_vnd($subtotal) ?></strong>
                </div>
                <div class="cart-summary-line">
                    <span>Phí vận chuyển</span>
                    <span class="text-muted">Tính ở bước thanh toán</span>
                </div>
                <div class="cart-summary-total">
                    <span>Tổng tiền hàng</span>
                    <strong><?= format_vnd($subtotal) ?></strong>
                </div>
                <button class="btn btn-primary cart-checkout" type="button" disabled
                        title="Chức năng thanh toán sẽ được bổ sung ở buổi tiếp theo">
                    Tiến hành đặt hàng
                </button>
                <div class="cart-safe">🔒 Thông tin giỏ hàng được lưu trong phiên làm việc</div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<script>
function changeCartQty(button, change) {
    const form = button.closest('form');
    const input = form.querySelector('input[name="quantity"]');
    const min = Number(input.min || 1);
    const max = Number(input.max || 999999);
    const current = Number(input.value || min);
    input.value = Math.min(max, Math.max(min, current + change));
}
</script>
