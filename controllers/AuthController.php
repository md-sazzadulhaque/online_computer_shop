<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    // ── REGISTER ──────────────────────────────────────────────────────────────
    public function showRegister(): void {
        require __DIR__ . '/../views/auth/register.php';
    }

    public function handleRegister(): void {
        verifyCsrf();

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['confirm']       ?? '';
        $role     = $_POST['role']          ?? 'customer';
        $errors   = [];

        // Server-side validation
        if (mb_strlen($name) < 2)                      $errors['name']    = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']   = 'Invalid email address.';
        if (mb_strlen($password) < 8)                  $errors['password'] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)                    $errors['confirm']  = 'Passwords do not match.';
        if (!in_array($role, ['admin', 'customer']))   $errors['role']     = 'Invalid role.';
        if (empty($errors) && UserModel::emailExists($email))
            $errors['email'] = 'This email is already registered.';

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old']         = compact('name', 'email', 'role');
            redirect('/register');
        }

        UserModel::create(compact('name', 'email', 'password', 'role'));
        setFlash('success', 'Account created! Please log in.');
        redirect('/login');
    }

    // ── LOGIN ─────────────────────────────────────────────────────────────────
    public function showLogin(): void {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function handleLogin(): void {
        verifyCsrf();

        $email      = trim($_POST['email']    ?? '');
        $password   = $_POST['password']      ?? '';
        $rememberMe = isset($_POST['remember_me']);
        $errors     = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']    = 'Invalid email.';
        if (empty($password))                           $errors['password'] = 'Password is required.';

        if (!$errors) {
            $user = UserModel::findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors['general'] = 'Invalid email or password.';
            }
        }

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old']         = ['email' => $email];
            redirect('/login');
        }

        // Start session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        // Remember Me
        if ($rememberMe) {
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            UserModel::storeRememberToken($user['id'], $tokenHash);
            setcookie('remember_me', $token, [
                'expires'  => time() + 30 * 24 * 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        setFlash('success', 'Welcome back, ' . e($user['name']) . '!');
        redirect('/');
    }

    // ── LOGOUT ────────────────────────────────────────────────────────────────
    public function handleLogout(): void {
        verifyCsrf();
        if (isLoggedIn()) {
            UserModel::clearRememberToken((int) $_SESSION['user_id']);
        }
        session_destroy();
        setcookie('remember_me', '', ['expires' => time() - 3600, 'path' => '/']);
        redirect('/login');
    }

    // ── Auto-login via Remember Me cookie (called in index.php) ──────────────
    public static function tryRememberMe(): void {
        if (!isLoggedIn() && isset($_COOKIE['remember_me'])) {
            $tokenHash = hash('sha256', $_COOKIE['remember_me']);
            $user = UserModel::findByRememberToken($tokenHash);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];
            }
        }
    }
}
