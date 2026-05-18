<?php
// model/OrderModel.php

require_once __DIR__ . '/../config/db.php';

class OrderModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Place order — cart items → orders + order_items, then clear cart */
    public function placeOrder(int $userId, array $cartItems, float $total, string $paymentMethod): int {
        $this->db->beginTransaction();
        try {
            // 1. Insert order
            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id, total_amount, payment_method, status)
                 VALUES (?, ?, ?, 'pending')"
            );
            $stmt->execute([$userId, $total, $paymentMethod]);
            $orderId = (int) $this->db->lastInsertId();

            // 2. Insert order_items + deduct stock
            $itemStmt = $this->db->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                 VALUES (?, ?, ?, ?)"
            );
            $stockStmt = $this->db->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
            );
            foreach ($cartItems as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
                $stockStmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                if ($stockStmt->rowCount() === 0) {
                    throw new RuntimeException("Insufficient stock for product ID " . $item['product_id']);
                }
            }

            // 3. Clear cart
            $clearStmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
            $clearStmt->execute([$userId]);

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Get order with items for confirmation page */
    public function getOrderWithItems(int $orderId): ?array {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS customer_name
             FROM orders o JOIN users u ON u.id = o.user_id
             WHERE o.id = ?"
        );
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $itemStmt = $this->db->prepare(
            "SELECT oi.*, p.name AS product_name, p.image_path
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?"
        );
        $itemStmt->execute([$orderId]);
        $order['items'] = $itemStmt->fetchAll();
        return $order;
    }

    /** Get recent orders (admin dashboard) — inline int for LIMIT */
    public function getRecent(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS customer_name
             FROM orders o JOIN users u ON u.id = o.user_id
             ORDER BY o.order_date DESC
             LIMIT " . (int)$limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Get all orders by user */
    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
