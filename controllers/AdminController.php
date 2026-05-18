<?php
// controllers/AdminController.php

require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../model/ReviewModel.php';
require_once __DIR__ . '/../model/OrderModel.php';

class AdminController {

    private UserModel   $userModel;
    private ReviewModel $reviewModel;
    private OrderModel  $orderModel;

    public function __construct() {
        $this->userModel   = new UserModel();
        $this->reviewModel = new ReviewModel();
        $this->orderModel  = new OrderModel();
    }

    /** Admin gate for page requests */
    private function requireAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: /task4_23-51148-1/public/test_login.php');
            exit;
        }
    }

    /** Admin gate for AJAX requests — returns JSON on failure */
    private function requireAdminAjax(): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login as admin.']);
            return false;
        }
        return true;
    }

    /** Admin dashboard with recent orders + reviews */
    public function dashboard(): void {
        $this->requireAdmin();
        $recentOrders  = $this->orderModel->getRecent(5);
        $recentReviews = $this->reviewModel->getRecent(5);
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    /** List all customers */
    public function customers(): void {
        $this->requireAdmin();
        $customers = $this->userModel->getAllCustomers();
        require __DIR__ . '/../views/admin/customers.php';
    }

    /** AJAX DELETE customer */
    public function deleteCustomer(): void {
        header('Content-Type: application/json');
        if (!$this->requireAdminAjax()) return;

        $customerId = (int) ($_POST['customer_id'] ?? 0);

        if ($customerId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid customer ID.']);
            return;
        }

        if ($customerId === (int) $_SESSION['user_id']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Cannot delete yourself.']);
            return;
        }

        $deleted = $this->userModel->deleteCustomer($customerId);
        echo json_encode(['success' => $deleted, 'error' => $deleted ? null : 'Customer not found or is an admin.']);
    }

    /** List all reviews */
    public function reviews(): void {
        $this->requireAdmin();
        $reviews = $this->reviewModel->getAll();
        require __DIR__ . '/../views/admin/reviews.php';
    }

    /** AJAX DELETE review (admin) */
    public function deleteReview(): void {
        header('Content-Type: application/json');
        if (!$this->requireAdminAjax()) return;

        $reviewId = (int) ($_POST['review_id'] ?? 0);

        if ($reviewId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid review ID.']);
            return;
        }

        $deleted = $this->reviewModel->delete($reviewId);
        echo json_encode(['success' => $deleted, 'error' => $deleted ? null : 'Review not found.']);
    }
}
