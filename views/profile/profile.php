<?php $pageTitle = 'My Profile – TechHub'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<?php
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
?>

<div class="profile-page container">
    <h1>My Profile</h1>

    <div class="profile-card">
        <!-- Avatar -->
        <div class="profile-avatar-wrap">
            <?php if (!empty($user['profile_picture'])): ?>
                <img class="profile-avatar"
                     src="<?= UPLOAD_URL . e($user['profile_picture']) ?>"
                     alt="Profile Picture">
            <?php else: ?>
                <div class="profile-avatar profile-avatar-placeholder">
                    <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <form id="profileForm" method="POST" action="<?= BASE_URL ?>/profile"
              enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

            <fieldset>
                <legend>Basic Info</legend>

                <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           value="<?= e($user['name']) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="error-msg"><?= e($errors['name']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= e($user['email']) ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-msg"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['profile_picture']) ? 'has-error' : '' ?>">
                    <label for="profile_picture">Profile Picture <small>(JPEG/PNG/GIF/WEBP, max 2 MB)</small></label>
                    <input type="file" id="profile_picture" name="profile_picture"
                           accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php if (isset($errors['profile_picture'])): ?>
                        <span class="error-msg"><?= e($errors['profile_picture']) ?></span>
                    <?php endif; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>Change Password <small>(leave blank to keep current)</small></legend>

                <div class="form-group <?= isset($errors['current_password']) ? 'has-error' : '' ?>">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="••••••••">
                    <?php if (isset($errors['current_password'])): ?>
                        <span class="error-msg"><?= e($errors['current_password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['new_password']) ? 'has-error' : '' ?>">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Min 8 characters">
                    <?php if (isset($errors['new_password'])): ?>
                        <span class="error-msg"><?= e($errors['new_password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error-msg"><?= e($errors['confirm_password']) ?></span>
                    <?php endif; ?>
                </div>
            </fieldset>

            <div class="profile-meta">
                <span>Role: <strong><?= e(ucfirst($user['role'])) ?></strong></span>
                <span>Member since: <strong><?= date('M Y', strtotime($user['created_at'])) ?></strong></span>
            </div>

            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
