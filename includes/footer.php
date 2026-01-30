</div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>
                    <?php echo SITE_NAME; ?>
                </h3>
                <p>Your trusted online bookstore for all your reading needs.</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/user/catalog.php">Browse Books</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/user/orders.php">My Orders</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Contact</h4>
                <p>Email: support@bookstore.com</p>
                <p>Phone: (555) 123-4567</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy;
                <?php echo date('Y'); ?>
                <?php echo SITE_NAME; ?>. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>

</html>