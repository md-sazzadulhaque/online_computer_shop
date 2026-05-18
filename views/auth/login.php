<?php $pageTitle = 'Login – TechHub'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<?php
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);
?>

<div class="auth-page">
    <div class="auth-card">
        <h1>Welcome Back</h1>
        <p class="auth-sub">Log in to your TechHub account.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="<?= BASE_URL ?>/login" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

            <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= e($old['email'] ?? '') ?>"
                       placeholder="you@example.com" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-msg"><?= e($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Your password" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-msg"><?= e($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-inline">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember_me" value="1">
                    <span>Remember me for 30 days</span>
                </label>
            </div>

            <button type="submit" class="btn-primary btn-full">Log In</button>
        </form>

        <p class="auth-footer">No account? <a href="<?= BASE_URL ?>/register">Register here</a></p>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
