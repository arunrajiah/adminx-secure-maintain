<?php
/**
 * AdminX Security Hardening Class
 *
 * @package AdminX_Secure_Maintain
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Security Hardening functionality
 */
class AdminX_Security_Hardening {
    
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
        // Disable XML-RPC if enabled
        if (get_option('adminx_security_disable_xmlrpc')) {
            add_filter('xmlrpc_enabled', '__return_false');
            add_filter('wp_headers', array($this, 'adminx_remove_xmlrpc_pingback_header'));
        }
        
        // Disable file editing if enabled
        if (get_option('adminx_security_disable_file_editing')) {
            if (!defined('DISALLOW_FILE_EDIT')) {
                define('DISALLOW_FILE_EDIT', true);
            }
        }
        
        // Hide WordPress version if enabled
        if (get_option('adminx_security_hide_wp_version')) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', array($this, 'adminx_remove_version'));
        }
        
        // Additional security headers
        add_action('send_headers', array($this, 'adminx_add_security_headers'));
        
        // Remove unnecessary meta tags
        add_action('init', array($this, 'adminx_remove_unnecessary_headers'));
        
        // Disable directory browsing
        add_action('init', array($this, 'adminx_disable_directory_browsing'));
    }
    
    /**
     * Remove XML-RPC pingback header
     */
    public function adminx_remove_xmlrpc_pingback_header($headers) {
        unset($headers['X-Pingback']);
        return $headers;
    }
    
    /**
     * Remove WordPress version from generator
     */
    public function adminx_remove_version() {
        return '';
    }
    
    /**
     * Add security headers
     */
    public function adminx_add_security_headers() {
        if (!is_admin()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
    
    /**
     * Remove unnecessary headers
     */
    public function adminx_remove_unnecessary_headers() {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    }
    
    /**
     * Disable directory browsing
     */
    public function adminx_disable_directory_browsing() {
        // This would typically be handled via .htaccess
        // But we can add some PHP-level protection
        if (!function_exists('adminx_block_directory_access')) {
            function adminx_block_directory_access() {
                $request_uri = $_SERVER['REQUEST_URI'];
                if (substr($request_uri, -1) === '/' && !is_admin()) {
                    // Check if it's trying to access a directory without index
                    $path = ABSPATH . ltrim($request_uri, '/');
                    if (is_dir($path) && !file_exists($path . 'index.php') && !file_exists($path . 'index.html')) {
                        wp_die(__('Directory access is forbidden.', 'adminx-secure-maintain'), 403);
                    }
                }
            }
            add_action('init', 'adminx_block_directory_access');
        }
    }
    
    /**
     * Get security status
     */
    public function adminx_get_security_status() {
        return array(
            'xmlrpc_disabled' => !xmlrpc_enabled(),
            'file_editing_disabled' => defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT,
            'version_hidden' => !has_action('wp_head', 'wp_generator'),
            'security_headers_enabled' => true // Always true when plugin is active
        );
    }
    
    /**
     * Update security settings
     */
    public function adminx_update_security_settings($settings) {
        $allowed_settings = array(
            'adminx_security_disable_xmlrpc',
            'adminx_security_disable_file_editing',
            'adminx_security_hide_wp_version'
        );
        
        foreach ($allowed_settings as $setting) {
            if (isset($settings[$setting])) {
                update_option($setting, (bool) $settings[$setting]);
            }
        }
        
        return true;
    }
}
