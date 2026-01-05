<?php
/**
 * Plugin Name: Tomatillo Design Custom Authors
 * Plugin URI: https://tomatillodesign.com
 * Description: Custom author/contributor system for WordPress posts with Genesis Framework integration
 * Version: 1.0.0
 * Author: Tomatillo Design
 * Author URI: https://tomatillodesign.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: tomatillo-design-custom-authors
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// ABOUTME: Main plugin file that bootstraps the Contributors plugin.
// ABOUTME: Defines constants, loads dependencies, and initializes all plugin components.

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('TDC_VERSION', '1.0.0');
define('TDC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TDC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TDC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader for plugin classes
spl_autoload_register(function ($class) {
    $prefix = 'TDC_';
    $base_dir = TDC_PLUGIN_DIR . 'includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Convert class name to file name (keep tdc- prefix)
    // TDC_CPT becomes class-tdc-cpt.php
    $file = $base_dir . 'class-' . strtolower(str_replace('_', '-', $class)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load helper functions
require_once TDC_PLUGIN_DIR . 'includes/class-tdc-helpers.php';

/**
 * Main plugin class
 */
class Tomatillo_Design_Custom_Authors {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Initialize Genesis hooks early (needs to run before genesis_setup)
        add_action('after_setup_theme', array($this, 'init_genesis_early'), 14);
        
        // Initialize everything else on plugins_loaded
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize Genesis hooks early (before Genesis sets up its hooks)
     */
    public function init_genesis_early() {
        // Initialize Genesis hooks component early
        TDC_Genesis_Hooks::get_instance();
    }
    
    public function init() {
        // Check for required dependencies
        if (!$this->check_dependencies()) {
            return;
        }
        
        // Initialize components (Genesis hooks already initialized early)
        TDC_CPT::get_instance();
        TDC_ACF::get_instance();
        TDC_Snapshots::get_instance();
        TDC_Templates::get_instance();
        TDC_Admin_Columns::get_instance();
        TDC_Admin_Filters::get_instance();
        TDC_Schema::get_instance();
        
        // Load assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    
    private function check_dependencies() {
        $missing = array();
        
        // Check for ACF Pro
        if (!class_exists('ACF')) {
            $missing[] = 'Advanced Custom Fields Pro';
        }
        
        // Check for Genesis Framework
        // Use multiple checks since genesis() function may not be loaded yet at plugins_loaded
        $genesis_active = false;
        
        if (function_exists('genesis')) {
            $genesis_active = true;
        } elseif (defined('PARENT_THEME_NAME') && PARENT_THEME_NAME === 'Genesis') {
            $genesis_active = true;
        } elseif (defined('GENESIS_SETTINGS_FIELD') && GENESIS_SETTINGS_FIELD) {
            $genesis_active = true;
        } elseif (function_exists('genesis_get_option')) {
            $genesis_active = true;
        }
        
        if (!$genesis_active) {
            // Check if Genesis is the parent theme
            $theme = wp_get_theme();
            if ($theme->get('Template') === 'genesis' || $theme->get_template() === 'genesis') {
                $genesis_active = true;
            }
        }
        
        if (!$genesis_active) {
            $missing[] = 'Genesis Framework';
        }
        
        if (!empty($missing)) {
            add_action('admin_notices', function() use ($missing) {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>Tomatillo Design Custom Authors:</strong> This plugin requires ';
                echo esc_html(implode(' and ', $missing));
                echo ' to be installed and activated.';
                echo '</p></div>';
            });
            return false;
        }
        
        return true;
    }
    
    public function enqueue_styles() {
        // Load on single posts (for entry header styling), contributor pages, and contributor archives
        if (is_singular('post') || is_post_type_archive('contributor') || is_singular('contributor')) {
            wp_enqueue_style(
                'tdc-contributors',
                TDC_PLUGIN_URL . 'assets/css/contributors.css',
                array(),
                TDC_VERSION
            );
        }
    }
}

/**
 * Plugin activation hook
 * Registers the CPT and flushes permalinks
 */
function tdc_activate_plugin() {
    // Register contributor CPT (duplicate registration for activation only)
    register_post_type('contributor', array(
        'public' => true,
        'has_archive' => true,
        'rewrite' => array(
            'slug' => 'contributors',
            'with_front' => false,
        ),
    ));
    
    // Flush rewrite rules so /contributors/ URLs work immediately
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'tdc_activate_plugin');

/**
 * Plugin deactivation hook
 * Flushes permalinks to clean up rewrite rules
 */
function tdc_deactivate_plugin() {
    // Flush rewrite rules to remove contributor URLs
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'tdc_deactivate_plugin');

// Initialize plugin
Tomatillo_Design_Custom_Authors::get_instance();
