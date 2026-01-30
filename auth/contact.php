<?php
/**
 * Contact Us Page (Fake)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';

$pageTitle = 'Contact Us';
require_once __DIR__ . '/../includes/auth_header.php';
?>

<section class="auth-section">
    <div class="auth-card fade-in">
        <div class="auth-card-body">
            <div class="auth-card-header">
                <div class="auth-card-icon">
                    <iconify-icon icon="solar:chat-line-linear" width="24" stroke-width="1.5"></iconify-icon>
                </div>
                <h2 class="auth-card-title">Get in Touch</h2>
                <p class="auth-card-subtitle">We'd love to hear from you.</p>
            </div>

            <div class="auth-content-text"
                style="color: var(--auth-zinc-600); line-height: 1.6; font-size: 0.9375rem; text-align: center;">
                <p style="margin-bottom: 2rem;">
                    Have questions about our bookstore or need assistance with an order? Reach out to our customer
                    support team.
                </p>

                <div
                    style="background: var(--auth-zinc-50); padding: 1.5rem; border-radius: 0.75rem; border: 1px solid var(--auth-zinc-200); margin-bottom: 2rem;">
                    <div style="margin-bottom: 1rem;">
                        <iconify-icon icon="solar:letter-linear" width="20"
                            style="color: var(--auth-zinc-900); display: block; margin: 0 auto 0.5rem;"></iconify-icon>
                        <strong style="display: block; color: var(--auth-zinc-900);">Email Support</strong>
                        <span>support@onlinebookstore.com</span>
                    </div>

                    <div>
                        <iconify-icon icon="solar:phone-linear" width="20"
                            style="color: var(--auth-zinc-900); display: block; margin: 1rem auto 0.5rem;"></iconify-icon>
                        <strong style="display: block; color: var(--auth-zinc-900);">Phone Support</strong>
                        <span>(555) 123-4567</span>
                    </div>
                </div>

                <div style="padding-top: 1rem;">
                    <a href="login.php" class="auth-btn-primary">Back to Sign In</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>