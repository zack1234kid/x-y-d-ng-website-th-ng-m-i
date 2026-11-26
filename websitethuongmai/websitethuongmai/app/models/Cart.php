<?php
// Gio hang chi luu product_id => quantity trong session.
class Cart
{
    private const SESSION_KEY = 'cart';

    private static function ensure(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    public static function items(): array
    {
        self::ensure();
        return $_SESSION[self::SESSION_KEY];
    }

    public static function add(int $productId, int $qty): void
    {
        self::ensure();
        if ($productId <= 0 || $qty <= 0) return;
        $_SESSION[self::SESSION_KEY][$productId] =
            (int)($_SESSION[self::SESSION_KEY][$productId] ?? 0) + $qty;
    }

    public static function setQuantity(int $productId, int $qty): void
    {
        self::ensure();
        if ($productId <= 0) return;
        if ($qty <= 0) {
            unset($_SESSION[self::SESSION_KEY][$productId]);
            return;
        }
        $_SESSION[self::SESSION_KEY][$productId] = $qty;
    }

    public static function remove(int $productId): void
    {
        self::ensure();
        unset($_SESSION[self::SESSION_KEY][$productId]);
    }

    public static function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }

    public static function totalQuantity(): int
    {
        self::ensure();
        return (int) array_sum($_SESSION[self::SESSION_KEY]);
    }
}
