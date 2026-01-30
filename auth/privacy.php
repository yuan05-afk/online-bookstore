<?php
/**
 * Privacy Policy Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';

$pageTitle = 'Privacy Policy';
require_once __DIR__ . '/../includes/auth_header.php';
?>

<section class="auth-section">
    <div class="auth-card auth-card-wide fade-in">
        <div class="auth-card-body">
            <div class="auth-card-header">
                <div class="auth-card-icon">
                    <iconify-icon icon="solar:shield-check-linear" width="24" stroke-width="1.5"></iconify-icon>
                </div>
                <h2 class="auth-card-title">Privacy Policy</h2>
                <p class="auth-card-subtitle">Last updated:
                    <?php echo date('F j, Y'); ?>
                </p>
            </div>

            <div class="auth-content-text" style="color: var(--auth-zinc-600); line-height: 1.6; font-size: 0.9375rem;">
                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">1. Information
                    Collection</h3>
                <p style="margin-bottom: 1.5rem;">We collect information you provide directly to us, such as when you
                    create an account, make a purchase, or communicate with us.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">2. Use of Information
                </h3>
                <p style="margin-bottom: 1.5rem;">We use your information to provide, maintain, and improve our
                    services, process transactions, and send you related information.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">3. Use of Cookies
                </h3>
                <p style="margin-bottom: 1.5rem;">We may use cookies and similar technologies to collect information
                    about your interactions with our services.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">4. Data Security</h3>
                <p style="margin-bottom: 1.5rem;">We implement reasonable security measures to protect your personal
                    information.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">5. Third-Party
                    Services</h3>
                <p style="margin-bottom: 2rem;">Our website may contain links to third-party websites. We are not
                    responsible for the privacy practices of those third parties.</p>

                <div style="padding-top: 1rem; border-top: 1px solid var(--auth-zinc-200);">
                    <a href="login.php" class="auth-btn-primary"
                        style="display: inline-flex; width: auto; padding: 0.5rem 1.5rem;">Back to Sign In</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>