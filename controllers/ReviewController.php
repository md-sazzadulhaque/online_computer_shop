<?php
// controllers/ReviewController.php

require_once __DIR__ . '/../model/ReviewModel.php';

class ReviewController {

    private ReviewModel $model;

    public function __construct() {
        $this->model = new ReviewModel();
    }

    /** AJAX POST /api/reviews/add */
    public function addReview(): void {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();

        // Auth check — only logged-in customers
        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'customer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'You must be logged in as a customer to post a review.']);
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $comment   = trim($_POST['comment'] ?? '');

        // Server-side validation
        if ($productId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid product.']);
            return;
        }
        if (strlen($comment) < 3) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Comment must be at least 3 characters.']);
            return;
        }
        if (strlen($comment) > 1000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Comment must not exceed 1000 characters.']);
            return;
        }

        // Save RAW to DB — htmlspecialchars only at display time, NOT before DB write
        $reviewerName = $_SESSION['name'] ?? 'Customer';

        $newId = $this->model->add($productId, (int) $_SESSION['user_id'], $reviewerName, $comment);

        // Return HTML-escaped version for safe JS injection
        echo json_encode([
            'success'       => true,
            'review_id'     => $newId,
            'reviewer_name' => htmlspecialchars($reviewerName, ENT_QUOTES, 'UTF-8'),
            'comment'       => htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /** AJAX POST /api/reviews/delete */
    public function deleteReview(int $reviewId): void {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
            return;
        }

        $review = $this->model->findById($reviewId);
        if (!$review) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Review not found.']);
            return;
        }

        $role   = $_SESSION['role'] ?? '';
        $userId = (int) $_SESSION['user_id'];

        // Admin can delete any; customer can only delete own
        if ($role !== 'admin' && (int) $review['user_id'] !== $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Permission denied.']);
            return;
        }

        $this->model->delete($reviewId);
        echo json_encode(['success' => true]);
    }

    /** Admin page: list all reviews */
    public function adminReviews(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: /task4_23-51148-1/public/test_login.php');
            exit;
        }
        $reviews = $this->model->getAll();
        require __DIR__ . '/../views/admin/reviews.php';
    }
}
