<?php
// views/reviews/reviews_section.php
// Include this file inside the product detail page (Task 3 integration point)
// Required variables: $product (array with id, name), $reviews (array from ReviewModel)
// Session must already be started before including this file.
?>

<section class="reviews-section" id="reviews" style="margin-top: 2.5rem;">
    <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:1.2rem; display:flex; align-items:center; gap:.6rem;">
        Customer Reviews
        <span class="badge-pill"><?= count($reviews) ?></span>
    </h2>

    <!-- Flash messages for this section -->
    <div id="review-flash"></div>

    <!-- Review list -->
    <div id="reviews-list">
        <?php if (empty($reviews)): ?>
            <p style="color:var(--muted); font-size:.9rem;">No reviews yet. Be the first!</p>
        <?php else: ?>
            <?php foreach ($reviews as $rv): ?>
            <div class="review-card card" id="review-<?= (int)$rv['id'] ?>"
                 style="margin-bottom:.85rem; padding:1rem 1.2rem; position:relative;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <strong style="font-size:.95rem;"><?= htmlspecialchars($rv['reviewer_name']) ?></strong>
                        <span style="color:var(--muted); font-size:.8rem; margin-left:.6rem;">
                            <?= date('M d, Y', strtotime($rv['created_at'])) ?>
                        </span>
                    </div>
                    <?php
                    $canDelete = !empty($_SESSION['user_id']) && (
                        ($_SESSION['role'] ?? '') === 'admin' ||
                        (int)$_SESSION['user_id'] === (int)$rv['user_id']
                    );
                    ?>
                    <?php if ($canDelete): ?>
                        <button class="btn btn-danger btn-sm delete-review-btn"
                                data-id="<?= (int)$rv['id'] ?>"
                                title="Delete review">&#10005;</button>
                    <?php endif; ?>
                </div>
                <p style="margin-top:.5rem; font-size:.9rem; line-height:1.6; color:var(--text);">
                    <?= nl2br(htmlspecialchars($rv['comment'])) ?>
                </p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Post review form (customers only) -->
    <?php if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'customer'): ?>
    <div class="card" style="margin-top:1.5rem; padding:1.5rem;">
        <h3 style="font-size:1rem; font-weight:700; margin-bottom:1rem;">Write a Review</h3>
        <form id="review-form" novalidate>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" readonly
                       style="opacity:.7; cursor:not-allowed;">
            </div>
            <div class="form-group">
                <label for="review-comment">Comment <span style="color:var(--danger)">*</span></label>
                <textarea id="review-comment" name="comment"
                          placeholder="Share your experience with this product..." maxlength="1000"></textarea>
                <div class="field-error" id="comment-error">Comment cannot be empty (min 3 characters).</div>
                <div style="text-align:right; font-size:.75rem; color:var(--muted); margin-top:4px;">
                    <span id="char-count">0</span>/1000
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="submit-review-btn">Post Review</button>
        </form>
    </div>
    <?php elseif (empty($_SESSION['user_id'])): ?>
    <p style="margin-top:1.2rem; color:var(--muted); font-size:.9rem;">
        <a href="/task4_23-51148-1/public/test_login.php">Login</a> to post a review.
    </p>
    <?php endif; ?>
</section>

<script>
(function () {
    const flash = document.getElementById('review-flash');

    function showFlash(msg, type = 'success') {
        flash.innerHTML = `<div class="flash flash-${type}">${msg}</div>`;
        setTimeout(() => { flash.innerHTML = ''; }, 4000);
    }

    // Character counter
    const commentBox = document.getElementById('review-comment');
    if (commentBox) {
        commentBox.addEventListener('input', () => {
            document.getElementById('char-count').textContent = commentBox.value.length;
        });
    }

    // Submit review via AJAX
    const form = document.getElementById('review-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const comment = commentBox.value.trim();
            const errEl   = document.getElementById('comment-error');

            // JS validation
            errEl.style.display = 'none';
            if (comment.length < 3) {
                errEl.style.display = 'block';
                commentBox.focus();
                return;
            }

            const btn = document.getElementById('submit-review-btn');
            btn.disabled = true;
            btn.textContent = 'Posting…';

            const formData = new FormData(form);
            // Override name with the static input
            formData.set('comment', comment);

            try {
                const res  = await fetch('/task4_23-51148-1/public/api/reviews/add.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Inject new review at top of list
                    const list  = document.getElementById('reviews-list');
                    const noMsg = list.querySelector('p');
                    if (noMsg) noMsg.remove();

                    const div = document.createElement('div');
                    div.className = 'review-card card';
                    div.id        = 'review-' + data.review_id;
                    div.style     = 'margin-bottom:.85rem; padding:1rem 1.2rem; position:relative;';
                    div.innerHTML = `
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                            <div>
                                <strong style="font-size:.95rem;">${escHtml(data.reviewer_name)}</strong>
                                <span style="color:var(--muted);font-size:.8rem;margin-left:.6rem;">just now</span>
                            </div>
                            <button class="btn btn-danger btn-sm delete-review-btn" data-id="${data.review_id}" title="Delete">&#10005;</button>
                        </div>
                        <p style="margin-top:.5rem;font-size:.9rem;line-height:1.6;">${escHtml(data.comment).replace(/\n/g,'<br>')}</p>
                    `;
                    list.insertAdjacentElement('afterbegin', div);
                    bindDeleteButton(div.querySelector('.delete-review-btn'));

                    // Update badge count
                    const badge = document.querySelector('#reviews .badge-pill');
                    if (badge) badge.textContent = parseInt(badge.textContent || 0) + 1;

                    commentBox.value = '';
                    document.getElementById('char-count').textContent = '0';
                    showFlash('Review posted successfully!', 'success');
                } else {
                    showFlash(data.error || 'Failed to post review.', 'error');
                }
            } catch {
                showFlash('Network error. Please try again.', 'error');
            }

            btn.disabled    = false;
            btn.textContent = 'Post Review';
        });
    }

    // Delete review
    function bindDeleteButton(btn) {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this review?')) return;
            const id  = btn.dataset.id;
            const res = await fetch('/task4_23-51148-1/public/api/reviews/delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'review_id=' + encodeURIComponent(id)
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('review-' + id)?.remove();
                const badge = document.querySelector('#reviews .badge-pill');
                if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent) - 1);
                showFlash('Review deleted.', 'success');
            } else {
                showFlash(data.error || 'Failed to delete review.', 'error');
            }
        });
    }

    document.querySelectorAll('.delete-review-btn').forEach(bindDeleteButton);

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
