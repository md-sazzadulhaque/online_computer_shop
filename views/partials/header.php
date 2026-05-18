<?php
require_once __DIR__ . '/../../config/app.php';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'TechHub – Online Computer Shop' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="nav-brand" href="<?= BASE_URL ?>/">⚡ TechHub</a>
    <div class="nav-links">
        <a href="<?= BASE_URL ?>/">Home</a>
        <?php if (isLoggedIn()): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin">Dashboard</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/profile">👤 <?= e($_SESSION['name']) ?></a>
            <form method="POST" action="<?= BASE_URL ?>/logout" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login">Login</a>
            <a href="<?= BASE_URL ?>/register" class="btn-register">Register</a>
        <?php endif; ?>
    </div>
</nav>

<?php if ($flash): ?>
    <div class="flash flash--<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>
