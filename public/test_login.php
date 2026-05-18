<?php
// public/test_login.php — Demo login for testing Task 4 without Task 1
session_start();

$action = $_GET['as'] ?? '';

if ($action === 'customer') {
    $_SESSION['user_id'] = 2;
    $_SESSION['name']    = 'Ali Hassan';
    $_SESSION['role']    = 'customer';
    header('Location: /task4_23-51148-1/views/product_details.php?id=1');
    exit;
}
if ($action === 'customer2') {
    $_SESSION['user_id'] = 3;
    $_SESSION['name']    = 'Sara Khan';
    $_SESSION['role']    = 'customer';
    header('Location: /task4_23-51148-1/views/product_details.php?id=1');
    exit;
}
if ($action === 'admin') {
    $_SESSION['user_id'] = 1;
    $_SESSION['name']    = 'Admin User';
    $_SESSION['role']    = 'admin';
    header('Location: /task4_23-51148-1/public/admin/dashboard.php');
    exit;
}
if ($action === 'logout') {
    session_destroy();
    // After merge: redirect to the real login page
    header('Location: /login.php');
    exit;
}

$loggedIn = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task 4 — Demo Login</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #0f1117;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .box {
    background: #1a1d27;
    border: 1px solid #2e3348;
    border-radius: 12px;
    padding: 40px 36px;
    width: 100%;
    max-width: 420px;
    text-align: center;
  }
  .logo { font-size: 22px; font-weight: 700; color: #e8eaf0; margin-bottom: 6px; }
  .logo span { color: #6c63ff; }
  .subtitle { color: #8b91a7; font-size: 13px; margin-bottom: 32px; }
  .btn-row { display: flex; flex-direction: column; gap: 12px; }
  .btn {
    display: block; width: 100%;
    padding: 13px 20px;
    border-radius: 8px;
    font-size: 15px; font-weight: 600;
    border: none; cursor: pointer;
    text-decoration: none;
    transition: opacity .15s;
  }
  .btn:hover { opacity: .85; }
  .btn-customer  { background: #6c63ff; color: #fff; }
  .btn-customer2 { background: #3b82f6; color: #fff; }
  .btn-admin     { background: #e05252; color: #fff; }
  .btn-logout    { background: #2e3348; color: #8b91a7; margin-top: 8px; font-size: 13px; padding: 9px; }
  .status {
    background: #242736;
    border: 1px solid #2e3348;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #8b91a7;
  }
  .status strong { color: #6c63ff; }
</style>
</head>
<body>
<div class="box">
  <div class="logo">PC<span>Shop</span></div>
  <p class="subtitle">Task 4 — Demo Login &nbsp;|&nbsp; Student: 23-51148-1</p>

  <?php if ($loggedIn): ?>
  <div class="status">
    Logged in as <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
    (<?= htmlspecialchars($_SESSION['role']) ?>)
  </div>
  <?php endif; ?>

  <div class="btn-row">
    <a class="btn btn-customer"  href="?as=customer">Customer: Ali Hassan</a>
    <a class="btn btn-customer2" href="?as=customer2">Customer: Sara Khan</a>
    <a class="btn btn-admin"     href="?as=admin">Admin</a>
    <?php if ($loggedIn): ?>
    <a class="btn btn-logout" href="?as=logout">Logout</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
