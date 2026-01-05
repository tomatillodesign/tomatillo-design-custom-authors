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
    
    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';
    
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
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function init() {
        // Check for required dependencies
        if (!$this->check_dependencies()) {
            return;
        }
        
        // Initialize components
        TDC_CPT::get_instance();
        TDC_ACF::get_instance();
        TDC_Snapshots::get_instance();
        TDC_Genesis_Hooks::get_instance();
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
        
        // Check for Genesis
        if (!function_exists('genesis')) {
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
        // Only load on relevant pages
        if (is_singular('post') || is_post_type_archive('contributor') || is_singular('contributor')) {
            $should_load = false;
            
            if (is_singular('post')) {
                $contributors = get_field('td_contributors');
                $should_load = !empty($contributors);
            } else {
                $should_load = true;
            }
            
            if ($should_load) {
                wp_enqueue_style(
                    'tdc-contributors',
                    TDC_PLUGIN_URL . 'assets/css/contributors.css',
                    array(),
                    TDC_VERSION
                );
            }
        }
    }
}

// Initialize plugin
Tomatillo_Design_Custom_Authors::get_instance();

