<?php
// public/api/reviews/add.php  → POST /api/reviews/add.php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../controllers/ReviewController.php';

$controller = new ReviewController();
$controller->addReview();
