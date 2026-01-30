<?php
/**
 * Terms of Service Page
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/security.php';

$pageTitle = 'Terms of Service';
require_once __DIR__ . '/../includes/auth_header.php';
?>

<section class="auth-section">
    <div class="auth-card auth-card-wide fade-in">
        <div class="auth-card-body">
            <div class="auth-card-header">
                <div class="auth-card-icon">
                    <iconify-icon icon="solar:document-text-linear" width="24" stroke-width="1.5"></iconify-icon>
                </div>
                <h2 class="auth-card-title">Terms of Service</h2>
                <p class="auth-card-subtitle">Last updated:
                    <?php echo date('F j, Y'); ?>
                </p>
            </div>

            <div class="auth-content-text" style="color: var(--auth-zinc-600); line-height: 1.6; font-size: 0.9375rem;">
                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">1. Introduction</h3>
                <p style="margin-bottom: 1.5rem;">Welcome to
                    <?php echo SITE_NAME; ?>. By accessing our website and using our services, you agree to be bound by
                    these Terms of Service. Please read them carefully.
                </p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">2. User Accounts</h3>
                <p style="margin-bottom: 1.5rem;">To access certain features, you must create an account. You are
                    responsible for maintaining the confidentiality of your account credentials and for all activities
                    that occur under your account.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">3. Book Purchases
                </h3>
                <p style="margin-bottom: 1.5rem;">All purchases are subject to availability. Prices are subject to
                    change without notice. We reserve the right to refuse or cancel any order for any reason.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">4. Intellectual
                    Property</h3>
                <p style="margin-bottom: 1.5rem;">The content on this website, including text, graphics, logos, and book
                    covers, is protected by copyright and other intellectual property laws.</p>

                <h3 style="color: var(--auth-zinc-900); font-weight: 600; margin-bottom: 0.75rem;">5. Limitation of
                    Liability</h3>
                <p style="margin-bottom: 2rem;">We shall not be liable for any indirect, incidental, special,
                    consequential, or punitive damages resulting from your use of our services.</p>

                <div style="padding-top: 1rem; border-top: 1px solid var(--auth-zinc-200);">
                    <a href="login.php" class="auth-btn-primary"
                        style="display: inline-flex; width: auto; padding: 0.5rem 1.5rem;">Back to Sign In</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>