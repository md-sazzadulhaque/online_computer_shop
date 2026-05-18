<?php $pageTitle = 'Register – TechHub'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<?php
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old']);
?>

<div class="auth-page">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p class="auth-sub">Join TechHub and start building.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert--error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="<?= BASE_URL ?>/register" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

            <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name"
                       value="<?= e($old['name'] ?? '') ?>"
                       placeholder="John Doe" required>
                <?php if (isset($errors['name'])): ?>
                    <span class="error-msg"><?= e($errors['name']) ?></span>
                <?php endif; ?>
            </div>

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
                       placeholder="Min 8 characters" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-msg"><?= e($errors['password']) ?></span>
                <?php endif; ?>
                <div class="pw-strength" id="pwStrength"></div>
            </div>

            <div class="form-group <?= isset($errors['confirm']) ? 'has-error' : '' ?>">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm"
                       placeholder="Repeat password" required>
                <?php if (isset($errors['confirm'])): ?>
                    <span class="error-msg"><?= e($errors['confirm']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['role']) ? 'has-error' : '' ?>">
                <label for="role">Account Type</label>
                <select id="role" name="role">
                    <option value="customer" <?= (($old['role'] ?? '') === 'customer') ? 'selected' : '' ?>>Customer</option>
                    <option value="admin"    <?= (($old['role'] ?? '') === 'admin')    ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn-primary btn-full">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="<?= BASE_URL ?>/login">Log in</a></p>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
