<?php
/**
 * Plugin Name: AdminX Secure & Maintain
 * Plugin URI: https://github.com/your-username/adminx-secure-maintain
 * Description: Comprehensive WordPress security hardening and maintenance toolkit. Includes security hardening, login protection, activity logging, file monitoring, and database optimization.
 * Version: 1.0.0
 * Author: AdminX Team
 * Author URI: https://adminx.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: adminx-secure-maintain
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ADMINX_SECURE_MAINTAIN_VERSION', '1.0.0');
define('ADMINX_SECURE_MAINTAIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADMINX_SECURE_MAINTAIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ADMINX_SECURE_MAINTAIN_PLUGIN_FILE', __FILE__);

/**
 * Main AdminX Secure & Maintain class
 */
class AdminX_Secure_Maintain {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get single instance
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
        $this->load_dependencies();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'adminx_init'));
        add_action('admin_menu', array($this, 'adminx_add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'adminx_enqueue_admin_scripts'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'adminx_activate'));
        register_deactivation_hook(__FILE__, array($this, 'adminx_deactivate'));
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once ADMINX_SECURE_MAINTAIN_PLUGIN_DIR . 'includes/class-security-hardening.php';
        require_once ADMINX_SECURE_MAINTAIN_PLUGIN_DIR . 'includes/class-login-protection.php';
        require_once ADMINX_SECURE_MAINTAIN_PLUGIN_DIR . 'includes/class-activity-logger.php';
        require_once ADMINX_SECURE_MAINTAIN_PLUGIN_DIR . 'includes/class-file-monitor.php';
        require_once ADMINX_SECURE_MAINTAIN_PLUGIN_DIR . 'includes/class-database-optimizer.php';
        require_once ADMINX_SECURE_MAINTAIN_PLUGIN_DIR . 'includes/class-admin-interface.php';
    }
    
    /**
     * Initialize plugin
     */
    public function adminx_init() {
        // Load text domain for translations
        load_plugin_textdomain('adminx-secure-maintain', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize components
        AdminX_Security_Hardening::get_instance();
        AdminX_Login_Protection::get_instance();
        AdminX_Activity_Logger::get_instance();
        AdminX_File_Monitor::get_instance();
        AdminX_Database_Optimizer::get_instance();
    }
    
    /**
     * Add admin menu
     */
    public function adminx_add_admin_menu() {
        add_menu_page(
            __('AdminX Secure & Maintain', 'adminx-secure-maintain'),
            __('AdminX Security', 'adminx-secure-maintain'),
            'manage_options',
            'adminx-secure-maintain',
            array('AdminX_Admin_Interface', 'adminx_render_main_page'),
            'dashicons-shield-alt',
            30
        );
        
        add_submenu_page(
            'adminx-secure-maintain',
            __('Security Settings', 'adminx-secure-maintain'),
            __('Security', 'adminx-secure-maintain'),
            'manage_options',
            'adminx-security-settings',
            array('AdminX_Admin_Interface', 'adminx_render_security_page')
        );
        
        add_submenu_page(
            'adminx-secure-maintain',
            __('Login Protection', 'adminx-secure-maintain'),
            __('Login Protection', 'adminx-secure-maintain'),
            'manage_options',
            'adminx-login-protection',
            array('AdminX_Admin_Interface', 'adminx_render_login_protection_page')
        );
        
        add_submenu_page(
            'adminx-secure-maintain',
            __('Activity Log', 'adminx-secure-maintain'),
            __('Activity Log', 'adminx-secure-maintain'),
            'manage_options',
            'adminx-activity-log',
            array('AdminX_Admin_Interface', 'adminx_render_activity_log_page')
        );
        
        add_submenu_page(
            'adminx-secure-maintain',
            __('File Monitor', 'adminx-secure-maintain'),
            __('File Monitor', 'adminx-secure-maintain'),
            'manage_options',
            'adminx-file-monitor',
            array('AdminX_Admin_Interface', 'adminx_render_file_monitor_page')
        );
        
        add_submenu_page(
            'adminx-secure-maintain',
            __('Database Optimizer', 'adminx-secure-maintain'),
            __('Database', 'adminx-secure-maintain'),
            'manage_options',
            'adminx-database-optimizer',
            array('AdminX_Admin_Interface', 'adminx_render_database_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function adminx_enqueue_admin_scripts($hook) {
        if (strpos($hook, 'adminx-') !== false) {
            wp_enqueue_style(
                'adminx-secure-maintain-admin',
                ADMINX_SECURE_MAINTAIN_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                ADMINX_SECURE_MAINTAIN_VERSION
            );
            
            wp_enqueue_script(
                'adminx-secure-maintain-admin',
                ADMINX_SECURE_MAINTAIN_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                ADMINX_SECURE_MAINTAIN_VERSION,
                true
            );
            
            wp_localize_script(
                'adminx-secure-maintain-admin',
                'adminx_ajax',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('adminx_secure_maintain_nonce')
                )
            );
        }
    }
    
    /**
     * Plugin activation
     */
    public function adminx_activate() {
        // Create database tables if needed
        $this->adminx_create_tables();
        
        // Set default options
        $this->adminx_set_default_options();
        
        // Schedule cron jobs
        if (!wp_next_scheduled('adminx_daily_maintenance')) {
            wp_schedule_event(time(), 'daily', 'adminx_daily_maintenance');
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function adminx_deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('adminx_daily_maintenance');
    }
    
    /**
     * Create database tables
     */
    private function adminx_create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Activity log table
        $table_name = $wpdb->prefix . 'adminx_activity_log';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(255) NOT NULL,
            object_type varchar(100) NOT NULL,
            object_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Login attempts table
        $table_name_attempts = $wpdb->prefix . 'adminx_login_attempts';
        $sql_attempts = "CREATE TABLE $table_name_attempts (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ip_address varchar(45) NOT NULL,
            username varchar(255) NOT NULL,
            success tinyint(1) NOT NULL DEFAULT 0,
            attempt_time datetime DEFAULT CURRENT_TIMESTAMP,
            user_agent text,
            PRIMARY KEY (id),
            KEY ip_address (ip_address),
            KEY attempt_time (attempt_time)
        ) $charset_collate;";
        
        // File changes table
        $table_name_files = $wpdb->prefix . 'adminx_file_changes';
        $sql_files = "CREATE TABLE $table_name_files (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            file_path varchar(500) NOT NULL,
            change_type varchar(50) NOT NULL,
            file_hash varchar(64),
            detected_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY file_path (file_path),
            KEY detected_at (detected_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($sql_attempts);
        dbDelta($sql_files);
    }
    
    /**
     * Set default plugin options
     */
    private function adminx_set_default_options() {
        $default_options = array(
            'security_disable_xmlrpc' => 1,
            'security_disable_file_editing' => 1,
            'security_hide_wp_version' => 1,
            'login_max_attempts' => 5,
            'login_lockout_duration' => 1800, // 30 minutes
            'file_monitor_enabled' => 1,
            'db_optimizer_auto_clean' => 1
        );
        
        foreach ($default_options as $option => $value) {
            if (get_option('adminx_' . $option) === false) {
                add_option('adminx_' . $option, $value);
            }
        }
    }
}

// Initialize the plugin
function adminx_secure_maintain_init() {
    return AdminX_Secure_Maintain::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'adminx_secure_maintain_init');