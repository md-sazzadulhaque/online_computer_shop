<?php
require_once(__DIR__ . '/../config/db.php');

// Get all products
function getAllProducts(){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*,
                c.name      AS category_name,
                parent.name AS parent_name,
                b.name      AS brand_name
         FROM   products p
         LEFT JOIN categories c      ON p.category_id = c.id
         LEFT JOIN categories parent ON c.parent_id   = parent.id
         LEFT JOIN brands b          ON p.brand_id    = b.id
         ORDER BY p.id"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get one product by id
function getProductById($id){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*,
                c.name      AS category_name,
                parent.name AS parent_name,
                b.name      AS brand_name
         FROM   products p
         LEFT JOIN categories c      ON p.category_id = c.id
         LEFT JOIN categories parent ON c.parent_id   = parent.id
         LEFT JOIN brands b          ON p.brand_id    = b.id
         WHERE  p.id = ?"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: false;
}

// Get top-level categories 
function getTopCategories(){
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all subcategories 
function getAllSubcategories(){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT c.*, p.name AS parent_name
         FROM   categories c
         JOIN   categories p ON c.parent_id = p.id
         ORDER BY p.name, c.name"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all brands with category name
function getAllBrands(){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT b.*, c.name AS category_name
         FROM   brands b
         LEFT JOIN categories c ON b.category_id = c.id
         ORDER BY b.name"
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get products by top-level category
function getProductsByCategory($category_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ? OR parent_id = ?");
    $stmt->execute([$category_id, $category_id]);
    $ids  = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if(empty($ids)) return [];

    $ph   = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        "SELECT p.*,
                c.name      AS category_name,
                parent.name AS parent_name,
                b.name      AS brand_name
         FROM   products p
         LEFT JOIN categories c      ON p.category_id = c.id
         LEFT JOIN categories parent ON c.parent_id   = parent.id
         LEFT JOIN brands b          ON p.brand_id    = b.id
         WHERE  p.category_id IN ($ph)
         ORDER BY p.id"
    );
    $stmt->execute($ids);
    return $stmt->fetchAll();
}

// Get products by subcategory id
function getProductsBySubcategory($sub_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*,
                c.name      AS category_name,
                parent.name AS parent_name,
                b.name      AS brand_name
         FROM   products p
         LEFT JOIN categories c      ON p.category_id = c.id
         LEFT JOIN categories parent ON c.parent_id   = parent.id
         LEFT JOIN brands b          ON p.brand_id    = b.id
         WHERE  p.category_id = ?
         ORDER BY p.id"
    );
    $stmt->execute([$sub_id]);
    return $stmt->fetchAll();
}

// Get products by brand id
function getProductsByBrand($brand_id){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT p.*,
                c.name      AS category_name,
                parent.name AS parent_name,
                b.name      AS brand_name
         FROM   products p
         LEFT JOIN categories c      ON p.category_id = c.id
         LEFT JOIN categories parent ON c.parent_id   = parent.id
         LEFT JOIN brands b          ON p.brand_id    = b.id
         WHERE  p.brand_id = ?
         ORDER BY p.id"
    );
    $stmt->execute([$brand_id]);
    return $stmt->fetchAll();
}

// Get category by id
function getCategoryById($id){
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "SELECT c.*, p.name AS parent_name
         FROM   categories c
         LEFT JOIN categories p ON c.parent_id = p.id
         WHERE c.id = ?"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: false;
}

// Get brand by id
function getBrandById($id){
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    return $row ?: false;
}

// AJAX search + filter 
function searchAndFilter($keyword, $min_price, $max_price, $category_id, $brand_id){
    $pdo    = getDB();
    $params = [];
    $where  = "WHERE 1=1";

    if($keyword !== ''){
        $like    = '%' . $keyword . '%';
        $where  .= " AND (p.name LIKE ? OR p.description LIKE ? OR b.name LIKE ? OR c.name LIKE ?)";
        array_push($params, $like, $like, $like, $like);
    }

    if($min_price !== ''){
        $where   .= " AND p.price >= ?";
        $params[] = (float)$min_price;
    }

    if($max_price !== ''){
        $where   .= " AND p.price <= ?";
        $params[] = (float)$max_price;
    }

    if($category_id !== ''){
        $stmt2 = $pdo->prepare("SELECT id FROM categories WHERE id = ? OR parent_id = ?");
        $stmt2->execute([$category_id, $category_id]);
        $ids   = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        if(!empty($ids)){
            $ph    = implode(',', array_fill(0, count($ids), '?'));
            $where .= " AND p.category_id IN ($ph)";
            $params = array_merge($params, $ids);
        }
    }

    if($brand_id !== ''){
        $where   .= " AND p.brand_id = ?";
        $params[] = (int)$brand_id;
    }

    $stmt = $pdo->prepare(
        "SELECT p.*,
                c.name      AS category_name,
                parent.name AS parent_name,
                b.name      AS brand_name
         FROM   products p
         LEFT JOIN categories c      ON p.category_id = c.id
         LEFT JOIN categories parent ON c.parent_id   = parent.id
         LEFT JOIN brands b          ON p.brand_id    = b.id
         $where
         ORDER BY p.price ASC"
    );
    $stmt->execute($params);
    return $stmt->fetchAll();
}
