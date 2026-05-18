<?php
// model/UserModel.php

require_once __DIR__ . '/../config/db.php';

class UserModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all customers */
    public function getAllCustomers(): array {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Delete a customer — manually cascade reviews, cart, orders (in case FK not set) */
    public function deleteCustomer(int $id): bool {
        // Only allow deleting customers, never admins
        $check = $this->db->prepare("SELECT id FROM users WHERE id = ? AND role = 'customer'");
        $check->execute([$id]);
        if (!$check->fetch()) return false;

        $this->db->beginTransaction();
        try {
            // 1. Delete reviews
            $this->db->prepare("DELETE FROM reviews WHERE user_id = ?")->execute([$id]);
            // 2. Delete cart items
            $this->db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$id]);
            // 3. Nullify order user_id (keep order history) or delete — we delete cascade
            //    order_items will cascade if FK set, otherwise delete manually
            $orders = $this->db->prepare("SELECT id FROM orders WHERE user_id = ?");
            $orders->execute([$id]);
            foreach ($orders->fetchAll() as $order) {
                $this->db->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order['id']]);
            }
            $this->db->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$id]);
            // 4. Delete user
            $this->db->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'")->execute([$id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /** Find user by ID */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
