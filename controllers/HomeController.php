<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/HomeModel.php';

class HomeController {

    public function showHome(): void {
        $categories = HomeModel::getTopCategories();
        $featured   = HomeModel::getFeaturedProducts(6);
        require __DIR__ . '/../views/home/home.php';
    }
}
