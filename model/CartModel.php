<?php
// model/CartModel.php

require_once __DIR__ . '/../config/db.php';

class CartModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Add item to cart (or increase quantity if already exists) */
    public function addToCart(int $userId, int $productId, int $quantity): bool {
        $stmt = $this->db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $row = $stmt->fetch();

        if ($row) {
            $newQty = $row['quantity'] + $quantity;
            $stmt   = $this->db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            return $stmt->execute([$newQty, $row['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO cart(user_id, product_id, quantity) VALUES(?, ?, ?)");
            return $stmt->execute([$userId, $productId, $quantity]);
        }
    }

    /** Get all cart items for a user with product details */
    public function getCartItems(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT c.id AS cart_id, c.quantity,
                    p.id AS product_id, p.name, p.price, p.stock, p.image_path
             FROM   cart c
             JOIN   products p ON c.product_id = p.id
             WHERE  c.user_id = ?"
        );
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
        }

        return $items;
    }

    /** Update quantity of one cart row */
    public function updateCartItem(int $cartId, int $userId, int $quantity): bool {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$quantity, $cartId, $userId]);
    }

    /** Remove one cart row */
    public function removeCartItem(int $cartId, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        return $stmt->execute([$cartId, $userId]);
    }

    /** Get cart grand total */
    public function getCartTotal(int $userId): string {
        $stmt = $this->db->prepare(
            "SELECT SUM(p.price * c.quantity) AS total
             FROM   cart c
             JOIN   products p ON c.product_id = p.id
             WHERE  c.user_id = ?"
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row['total'] ? number_format((float)$row['total'], 2) : '0.00';
    }

    /** Get total item count in cart */
    public function getCartCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row['total'] ? (int)$row['total'] : 0;
    }

    /** Clear all cart items for user */
    public function clearCart(int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}

// ── Procedural wrapper functions (for views/cart.php and views/product_details.php) ──

function addToCart(int $userId, int $productId, int $quantity): bool {
    $m = new CartModel();
    return $m->addToCart($userId, $productId, $quantity);
}

function getCartItems(int $userId): array {
    $m = new CartModel();
    return $m->getCartItems($userId);
}

function updateCartItem(int $cartId, int $userId, int $quantity): bool {
    $m = new CartModel();
    return $m->updateCartItem($cartId, $userId, $quantity);
}

function removeCartItem(int $cartId, int $userId): bool {
    $m = new CartModel();
    return $m->removeCartItem($cartId, $userId);
}

function getCartTotal(int $userId): string {
    $m = new CartModel();
    return $m->getCartTotal($userId);
}

function getCartCount(int $userId): int {
    $m = new CartModel();
    return $m->getCartCount($userId);
}
