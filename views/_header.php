<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Computer Shop') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0f1117;
            --surface: #1a1d27;
            --surface2: #242736;
            --border: #2e3348;
            --accent: #6c63ff;
            --accent-hover: #5a52e0;
            --danger: #e05252;
            --success: #52c97f;
            --warning: #f0a500;
            --text: #e8eaf0;
            --muted: #8b91a7;
            --radius: 10px;
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* NAVBAR */
        .navbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar-brand { font-size: 1.2rem; font-weight: 700; color: var(--text); letter-spacing: -0.5px; }
        .navbar-brand span { color: var(--accent); }
        .navbar-links { display: flex; align-items: center; gap: 1.5rem; font-size: 0.9rem; }
        .navbar-links a { color: var(--muted); font-weight: 500; transition: color .2s; }
        .navbar-links a:hover { color: var(--text); text-decoration: none; }
        .badge-pill {
            background: var(--accent); color: #fff;
            border-radius: 20px; padding: 2px 8px; font-size: 0.75rem; font-family: 'DM Mono', monospace;
        }

        /* LAYOUT */
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.7rem; font-weight: 700; letter-spacing: -0.5px; }
        .page-header p { color: var(--muted); margin-top: 0.25rem; font-size: 0.92rem; }

        /* CARDS */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0.5rem 1.1rem; border-radius: 8px; font-size: 0.88rem;
            font-weight: 600; font-family: 'DM Sans', sans-serif;
            cursor: pointer; border: none; transition: all .15s;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-danger { background: rgba(224,82,82,.15); color: var(--danger); border: 1px solid rgba(224,82,82,.3); }
        .btn-danger:hover { background: var(--danger); color: #fff; }
        .btn-sm { padding: 0.35rem 0.8rem; font-size: 0.8rem; }

        /* TABLE */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th { text-align: left; padding: 0.65rem 1rem; color: var(--muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid var(--border); }
        td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }

        /* FLASH */
        .flash { padding: 0.9rem 1.2rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
        .flash-success { background: rgba(82,201,127,.12); border: 1px solid rgba(82,201,127,.3); color: var(--success); }
        .flash-error { background: rgba(224,82,82,.12); border: 1px solid rgba(224,82,82,.3); color: var(--danger); }

        /* FORMS */
        .form-group { margin-bottom: 1.1rem; }
        label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--muted); margin-bottom: 0.4rem; }
        input[type="text"], input[type="number"], input[type="email"], select, textarea {
            width: 100%; background: var(--surface2); border: 1px solid var(--border);
            border-radius: 8px; padding: 0.6rem 0.9rem; color: var(--text);
            font-family: 'DM Sans', sans-serif; font-size: 0.9rem;
            transition: border-color .2s;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--accent); }
        textarea { resize: vertical; min-height: 100px; }
        .field-error { color: var(--danger); font-size: 0.8rem; margin-top: 4px; display: none; }

        /* STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.2rem 1.5rem; }
        .stat-card .label { font-size: 0.78rem; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
        .stat-card .value { font-size: 1.8rem; font-weight: 700; font-family: 'DM Mono', monospace; margin-top: 4px; }
    </style>
</head>
<body>
<nav class="navbar">
    <span class="navbar-brand">PC<span>Shop</span></span>
    <div class="navbar-links">
        <?php if (!empty($_SESSION['user_id'])): ?>
            <span style="color:var(--muted);">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="/task4_23-51148-1/public/admin/dashboard.php">Dashboard</a>
                <a href="/task4_23-51148-1/public/admin/customers.php">Customers</a>
                <a href="/task4_23-51148-1/public/admin/reviews.php">Reviews</a>
            <?php else: ?>
                <a href="/task4_23-51148-1/views/product_details.php?id=1">Browse</a>
                <a href="/task4_23-51148-1/views/cart.php">Cart</a>
                <a href="/task4_23-51148-1/public/test_login.php">My Orders</a>
            <?php endif; ?>
            <a href="/task4_23-51148-1/public/test_login.php?as=logout" style="color:var(--danger);">Logout</a>
        <?php else: ?>
            <a href="/task4_23-51148-1/public/test_login.php">Login</a>
            <a href="/task4_23-51148-1/public/test_login.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
