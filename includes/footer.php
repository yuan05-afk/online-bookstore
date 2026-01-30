</div>
</main>

<footer class="user-footer">
    <div class="user-footer-content">
        <div class="user-footer-section">
            <div class="user-footer-logo">
                <iconify-icon icon="solar:book-bold" width="24"></iconify-icon>
                <span><?php echo SITE_NAME; ?></span>
            </div>
            <p class="user-footer-tagline">Your trusted online bookstore for all your reading needs.</p>
        </div>

        <div class="user-footer-section">
            <h4>Quick Links</h4>
            <ul class="user-footer-links">
                <li>
                    <a href="<?php echo SITE_URL; ?>/user/catalog.php">
                        <iconify-icon icon="solar:book-linear" width="16"></iconify-icon>
                        Browse Books
                    </a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL; ?>/user/orders.php">
                        <iconify-icon icon="solar:box-linear" width="16"></iconify-icon>
                        My Orders
                    </a>
                </li>
            </ul>
        </div>

        <div class="user-footer-section">
            <h4>Contact</h4>
            <ul class="user-footer-contact">
                <li>
                    <iconify-icon icon="solar:letter-linear" width="16"></iconify-icon>
                    <a href="mailto:support@bookstore.com">support@bookstore.com</a>
                </li>
                <li>
                    <iconify-icon icon="solar:phone-linear" width="16"></iconify-icon>
                    <span>(555) 123-4567</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="user-footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </div>
</footer>

<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>

</html>