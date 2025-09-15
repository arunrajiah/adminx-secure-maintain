<?php
/**
 * AdminX Login Protection Class
 *
 * @package AdminX_Secure_Maintain
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Login Protection functionality
 */
class AdminX_Login_Protection {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_login_failed', array($this, 'adminx_log_failed_login'));
        add_action('wp_login', array($this, 'adminx_log_successful_login'), 10, 2);
        add_filter('authenticate', array($this, 'adminx_check_login_attempts'), 30, 3);
        add_action('wp_ajax_adminx_unblock_ip', array($this, 'adminx_unblock_ip'));
        add_action('wp_ajax_adminx_get_blocked_ips', array($this, 'adminx_get_blocked_ips'));
    }
    
    /**
     * Log failed login attempt
     */
    public function adminx_log_failed_login($username) {
        global $wpdb;
        
        $ip_address = $this->adminx_get_client_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        $wpdb->insert(
            $wpdb->prefix . 'adminx_login_attempts',
            array(
                'ip_address' => $ip_address,
                'username' => sanitize_text_field($username),
                'success' => 0,
                'user_agent' => sanitize_text_field($user_agent),
                'attempt_time' => current_time('mysql')
            ),
            array('%s', '%s', '%d', '%s', '%s')
        );
        
        // Check if IP should be blocked
        $this->adminx_check_and_block_ip($ip_address);
    }
    
    /**
     * Log successful login
     */
    public function adminx_log_successful_login($user_login, $user) {
        global $wpdb;
        
        $ip_address = $this->adminx_get_client_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        $wpdb->insert(
            $wpdb->prefix . 'adminx_login_attempts',
            array(
                'ip_address' => $ip_address,
                'username' => sanitize_text_field($user_login),
                'success' => 1,
                'user_agent' => sanitize_text_field($user_agent),
                'attempt_time' => current_time('mysql')
            ),
            array('%s', '%s', '%d', '%s', '%s')
        );
        
        // Clear failed attempts for this IP on successful login
        $this->adminx_clear_failed_attempts($ip_address);
    }
    
    /**
     * Check login attempts and block if necessary
     */
    public function adminx_check_login_attempts($user, $username, $password) {
        if (empty($username) || empty($password)) {
            return $user;
        }
        
        $ip_address = $this->adminx_get_client_ip();
        
        // Check if IP is currently blocked
        if ($this->adminx_is_ip_blocked($ip_address)) {
            $lockout_duration = get_option('adminx_login_lockout_duration', 1800);
            $minutes = ceil($lockout_duration / 60);
            
            return new WP_Error(
                'adminx_ip_blocked',
                sprintf(
                    __('Too many failed login attempts. Please try again in %d minutes.', 'adminx-secure-maintain'),
                    $minutes
                )
            );
        }
        
        return $user;
    }
    
    /**
     * Get client IP address
     */
    private function adminx_get_client_ip() {
        $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    
    /**
     * Check if IP should be blocked
     */
    private function adminx_check_and_block_ip($ip_address) {
        global $wpdb;
        
        $max_attempts = get_option('adminx_login_max_attempts', 5);
        $lockout_duration = get_option('adminx_login_lockout_duration', 1800);
        
        // Count failed attempts in the last lockout period
        $failed_attempts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}adminx_login_attempts 
             WHERE ip_address = %s 
             AND success = 0 
             AND attempt_time > DATE_SUB(NOW(), INTERVAL %d SECOND)",
            $ip_address,
            $lockout_duration
        ));
        
        if ($failed_attempts >= $max_attempts) {
            // Add to blocked IPs
            $blocked_until = date('Y-m-d H:i:s', time() + $lockout_duration);
            update_option('adminx_blocked_ip_' . md5($ip_address), $blocked_until);
        }
    }
    
    /**
     * Check if IP is blocked
     */
    private function adminx_is_ip_blocked($ip_address) {
        $blocked_until = get_option('adminx_blocked_ip_' . md5($ip_address));
        
        if ($blocked_until && strtotime($blocked_until) > time()) {
            return true;
        }
        
        // Clean up expired blocks
        if ($blocked_until) {
            delete_option('adminx_blocked_ip_' . md5($ip_address));
        }
        
        return false;
    }
    
    /**
     * Clear failed attempts for IP
     */
    private function adminx_clear_failed_attempts($ip_address) {
        // Remove from blocked IPs
        delete_option('adminx_blocked_ip_' . md5($ip_address));
    }
    
    /**
     * Get login statistics
     */
    public function adminx_get_login_stats() {
        global $wpdb;
        
        $stats = array();
        
        // Total attempts today
        $stats['today_attempts'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}adminx_login_attempts 
             WHERE DATE(attempt_time) = CURDATE()"
        );
        
        // Failed attempts today
        $stats['today_failed'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}adminx_login_attempts 
             WHERE DATE(attempt_time) = CURDATE() AND success = 0"
        );
        
        // Currently blocked IPs
        $stats['blocked_ips'] = $this->adminx_get_currently_blocked_ips();
        
        return $stats;
    }
    
    /**
     * Get currently blocked IPs
     */
    private function adminx_get_currently_blocked_ips() {
        global $wpdb;
        
        $blocked_ips = array();
        $options = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} 
             WHERE option_name LIKE 'adminx_blocked_ip_%'"
        );
        
        foreach ($options as $option) {
            if (strtotime($option->option_value) > time()) {
                $ip_hash = str_replace('adminx_blocked_ip_', '', $option->option_name);
                $blocked_ips[] = array(
                    'hash' => $ip_hash,
                    'blocked_until' => $option->option_value
                );
            } else {
                // Clean up expired blocks
                delete_option($option->option_name);
            }
        }
        
        return $blocked_ips;
    }
    
    /**
     * Unblock IP via AJAX
     */
    public function adminx_unblock_ip() {
        check_ajax_referer('adminx_secure_maintain_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'adminx-secure-maintain'));
        }
        
        $ip_hash = sanitize_text_field($_POST['ip_hash']);
        delete_option('adminx_blocked_ip_' . $ip_hash);
        
        wp_send_json_success(__('IP unblocked successfully', 'adminx-secure-maintain'));
    }
    
    /**
     * Get blocked IPs via AJAX
     */
    public function adminx_get_blocked_ips() {
        check_ajax_referer('adminx_secure_maintain_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'adminx-secure-maintain'));
        }
        
        $blocked_ips = $this->adminx_get_currently_blocked_ips();
        wp_send_json_success($blocked_ips);
    }
}
