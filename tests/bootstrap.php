<?php
// ABOUTME: PHPUnit test bootstrap file for Contributors plugin unit tests.
// ABOUTME: Loads plugin classes and sets up minimal WordPress environment mocks.

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load helper functions
require_once dirname(__DIR__) . '/includes/class-tdc-helpers.php';

// Define plugin constants
define('TDC_PLUGIN_DIR', dirname(__DIR__));
define('TDC_PLUGIN_URL', 'http://example.com/wp-content/plugins/tomatillo-design-custom-authors/');
define('TDC_VERSION', '1.0.0');

// Mock WordPress functions needed for unit tests
if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return $url;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

