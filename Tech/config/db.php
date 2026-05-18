<?php


function getDB(){
    static $pdo = null;

    if($pdo === null){
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=computer_shop5;charset=utf8",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch(PDOException $e){
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}
<?php


function getDB(){
    static $pdo = null;

    if($pdo === null){
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=computer_shop5;charset=utf8",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch(PDOException $e){
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}
