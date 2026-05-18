<?php
// public/api/reviews/delete.php  → POST /api/reviews/delete.php
session_start();
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../controllers/ReviewController.php';

$controller = new ReviewController();
$reviewId   = (int) ($_POST['review_id'] ?? 0);
$controller->deleteReview($reviewId);
