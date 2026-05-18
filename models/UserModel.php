<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {

    public static function findByEmail(string $email): ?array {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $pdo = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password_hash, role, created_at)
             VALUES (:name, :email, :password_hash, :role, NOW())"
        );
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':role'          => $data['role'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo   = getDB();
        $sets  = [];
        $params = [':id' => $id];
        foreach ($data as $col => $val) {
            $sets[] = "`$col` = :$col";
            $params[":$col"] = $val;
        }
        $sql  = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function emailExists(string $email, int $excludeId = 0): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $stmt->execute([$email, $excludeId]);
        return (bool) $stmt->fetch();
    }

    public static function storeRememberToken(int $userId, string $tokenHash): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmt->execute([$tokenHash, $userId]);
    }

    public static function findByRememberToken(string $tokenHash): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ? LIMIT 1");
        $stmt->execute([$tokenHash]);
        return $stmt->fetch() ?: null;
    }

    public static function clearRememberToken(int $userId): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$userId]);
    }
}
