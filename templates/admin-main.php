<?php
/**
 * AdminX Secure & Maintain - Main Admin Template
 *
 * @package AdminX_Secure_Maintain
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get security status
$security_hardening = AdminX_Security_Hardening::get_instance();
$login_protection = AdminX_Login_Protection::get_instance();
$security_status = $security_hardening->adminx_get_security_status();
$login_stats = $login_protection->adminx_get_login_stats();
?>

<div class="adminx-container">
    <div class="adminx-header">
        <h1><?php _e('AdminX Secure & Maintain', 'adminx-secure-maintain'); ?></h1>
        <p><?php _e('Comprehensive security hardening and maintenance tools for WordPress administrators.', 'adminx-secure-maintain'); ?></p>
    </div>

    <div class="adminx-security-overview">
        <div class="adminx-security-item">
            <div class="icon">🛡️</div>
            <div class="status">
                <span id="adminx-xmlrpc-disabled" class="adminx-status <?php echo $security_status['xmlrpc_disabled'] ? 'enabled' : 'disabled'; ?>">
                    <?php echo $security_status['xmlrpc_disabled'] ? __('Enabled', 'adminx-secure-maintain') : __('Disabled', 'adminx-secure-maintain'); ?>
                </span>
            </div>
            <div class="description"><?php _e('XML-RPC Protection', 'adminx-secure-maintain'); ?></div>
        </div>

        <div class="adminx-security-item">
            <div class="icon">📝</div>
            <div class="status">
                <span id="adminx-file-editing-disabled" class="adminx-status <?php echo $security_status['file_editing_disabled'] ? 'enabled' : 'disabled'; ?>">
                    <?php echo $security_status['file_editing_disabled'] ? __('Enabled', 'adminx-secure-maintain') : __('Disabled', 'adminx-secure-maintain'); ?>
                </span>
            </div>
            <div class="description"><?php _e('File Editing Protection', 'adminx-secure-maintain'); ?></div>
        </div>

        <div class="adminx-security-item">
            <div class="icon">🔒</div>
            <div class="status">
                <span id="adminx-version-hidden" class="adminx-status <?php echo $security_status['version_hidden'] ? 'enabled' : 'disabled'; ?>">
                    <?php echo $security_status['version_hidden'] ? __('Enabled', 'adminx-secure-maintain') : __('Disabled', 'adminx-secure-maintain'); ?>
                </span>
            </div>
            <div class="description"><?php _e('Version Hiding', 'adminx-secure-maintain'); ?></div>
        </div>

        <div class="adminx-security-item">
            <div class="icon">🚫</div>
            <div class="status">
                <span id="adminx-blocked-count" class="adminx-status warning">
                    <?php echo count($login_stats['blocked_ips']); ?>
                </span>
            </div>
            <div class="description"><?php _e('Blocked IPs', 'adminx-secure-maintain'); ?></div>
        </div>
    </div>

    <div class="adminx-cards">
        <div class="adminx-card">
            <h3><?php _e('Login Protection', 'adminx-secure-maintain'); ?></h3>
            <p><?php _e('Monitor and protect against brute force attacks.', 'adminx-secure-maintain'); ?></p>
            <table class="adminx-form-table">
                <tr>
                    <th><?php _e('Today\'s Attempts:', 'adminx-secure-maintain'); ?></th>
                    <td><span id="adminx-today-attempts"><?php echo $login_stats['today_attempts']; ?></span></td>
                </tr>
                <tr>
                    <th><?php _e('Failed Attempts:', 'adminx-secure-maintain'); ?></th>
                    <td><span id="adminx-today-failed"><?php echo $login_stats['today_failed']; ?></span></td>
                </tr>
                <tr>
                    <th><?php _e('Blocked IPs:', 'adminx-secure-maintain'); ?></th>
                    <td><span id="adminx-blocked-ips-count"><?php echo count($login_stats['blocked_ips']); ?></span></td>
                </tr>
            </table>
            <p>
                <a href="<?php echo admin_url('admin.php?page=adminx-login-protection'); ?>" class="adminx-button">
                    <?php _e('Manage Login Protection', 'adminx-secure-maintain'); ?>
                </a>
            </p>
        </div>

        <div class="adminx-card">
            <h3><?php _e('Security Settings', 'adminx-secure-maintain'); ?></h3>
            <p><?php _e('Configure security hardening options.', 'adminx-secure-maintain'); ?></p>
            <ul>
                <li><?php echo $security_status['xmlrpc_disabled'] ? '✅' : '❌'; ?> <?php _e('XML-RPC Disabled', 'adminx-secure-maintain'); ?></li>
                <li><?php echo $security_status['file_editing_disabled'] ? '✅' : '❌'; ?> <?php _e('File Editing Disabled', 'adminx-secure-maintain'); ?></li>
                <li><?php echo $security_status['version_hidden'] ? '✅' : '❌'; ?> <?php _e('WordPress Version Hidden', 'adminx-secure-maintain'); ?></li>
                <li><?php echo $security_status['security_headers_enabled'] ? '✅' : '❌'; ?> <?php _e('Security Headers Added', 'adminx-secure-maintain'); ?></li>
            </ul>
            <p>
                <a href="<?php echo admin_url('admin.php?page=adminx-security-settings'); ?>" class="adminx-button">
                    <?php _e('Configure Security', 'adminx-secure-maintain'); ?>
                </a>
            </p>
        </div>

        <div class="adminx-card">
            <h3><?php _e('Quick Actions', 'adminx-secure-maintain'); ?></h3>
            <p><?php _e('Perform common maintenance tasks.', 'adminx-secure-maintain'); ?></p>
            <p>
                <button id="adminx-scan-files" class="adminx-button"><?php _e('Scan Files', 'adminx-secure-maintain'); ?></button>
                <button id="adminx-optimize-db" class="adminx-button secondary"><?php _e('Optimize DB', 'adminx-secure-maintain'); ?></button>
            </p>
            <div id="adminx-quick-results"></div>
        </div>
    </div>
</div>