<?php
require_once __DIR__ . '/../config/database.php';

class HomeModel {

    public static function getTopCategories(): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getFeaturedProducts(int $limit = 6): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "SELECT p.id, p.name, p.manufacturer_review, p.price, p.image_path, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock > 0
             ORDER BY p.created_at DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
