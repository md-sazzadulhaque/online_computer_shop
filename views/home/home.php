<?php $pageTitle = 'TechHub – Home'; ?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Category Bar -->
<section class="category-bar">
    <div class="container">
        <span class="cat-label">Browse:</span>
        <?php foreach ($categories as $cat): ?>
            <a class="cat-chip" href="<?= BASE_URL ?>/category/<?= urlencode(strtolower($cat['name'])) ?>">
                <?= e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
        <?php if (empty($categories)): ?>
            <span class="cat-empty">No categories yet.</span>
        <?php endif; ?>
    </div>
</section>

<!-- Hero -->
<section class="hero">
    <div class="hero-inner">
        <h1>Build Your<br><span>Dream PC</span></h1>
        <p>Premium components, unbeatable prices. Delivered to your door.</p>
        <a class="btn-primary" href="<?= BASE_URL ?>/register">Get Started</a>
    </div>
    <div class="hero-art">
        <div class="cpu-box">
            <div class="cpu-core"></div>
            <div class="cpu-core"></div>
            <div class="cpu-core"></div>
            <div class="cpu-core"></div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured">
    <div class="container">
        <h2 class="section-title">Featured Components</h2>
        <div class="product-grid">
            <?php foreach ($featured as $p): ?>
                <a class="product-card" href="<?= BASE_URL ?>/product/<?= (int)$p['id'] ?>">
                    <div class="product-img-wrap">
                        <?php if (!empty($p['image_path'])): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/products/<?= e($p['image_path']) ?>"
                                 alt="<?= e($p['name']) ?>">
                        <?php else: ?>
                            <div class="product-img-placeholder">🖥️</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <span class="product-cat"><?= e($p['category_name'] ?? '') ?></span>
                        <h3><?= e($p['name']) ?></h3>
                        <p class="product-review"><?= e(mb_substr($p['manufacturer_review'] ?? '', 0, 80)) ?>…</p>
                        <span class="product-price">৳<?= number_format((float)$p['price'], 2) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($featured)): ?>
                <p class="empty-msg">No products available yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>
