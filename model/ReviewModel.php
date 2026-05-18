<?php
// model/ReviewModel.php

require_once __DIR__ . '/../config/db.php';

class ReviewModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all reviews for a product */
    public function getByProduct(int $productId): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, COALESCE(u.name, r.reviewer_name) AS reviewer_name
             FROM reviews r
             LEFT JOIN users u ON u.id = r.user_id
             WHERE r.product_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /** Get all reviews (admin view) */
    public function getAll(): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, p.name AS product_name, COALESCE(u.name, r.reviewer_name) AS reviewer_name
             FROM reviews r
             LEFT JOIN products p ON p.id = r.product_id
             LEFT JOIN users u ON u.id = r.user_id
             ORDER BY r.created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Add a review — save raw text, no HTML encoding here */
    public function add(int $productId, int $userId, string $reviewerName, string $comment): int {
        $stmt = $this->db->prepare(
            "INSERT INTO reviews (product_id, user_id, reviewer_name, comment)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$productId, $userId, $reviewerName, $comment]);
        return (int) $this->db->lastInsertId();
    }

    /** Find a single review by ID */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Delete a review */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /** Recent reviews (admin dashboard) — explicit int binding for LIMIT */
    public function getRecent(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT r.*, p.name AS product_name, COALESCE(u.name, r.reviewer_name) AS reviewer_name
             FROM reviews r
             LEFT JOIN products p ON p.id = r.product_id
             LEFT JOIN users u ON u.id = r.user_id
             ORDER BY r.created_at DESC
             LIMIT " . (int)$limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
