<?php
// ABOUTME: Handles template loading for contributor CPT single and archive pages.
// ABOUTME: Filters template_include to use custom templates for contributor post type.

class TDC_Templates {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('template_include', array($this, 'load_contributor_template'));
    }
    
    /**
     * Load custom templates for contributor CPT
     * 
     * @param string $template Template path
     * @return string Modified template path
     */
    public function load_contributor_template($template) {
        if (is_singular('contributor')) {
            $custom_template = TDC_PLUGIN_DIR . 'templates/single-contributor.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        if (is_post_type_archive('contributor')) {
            $custom_template = TDC_PLUGIN_DIR . 'templates/archive-contributor.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Query posts by contributor
     * 
     * @param int $contributor_id Contributor post ID
     * @param int $paged Page number for pagination
     * @return WP_Query
     */
    public static function query_posts_by_contributor($contributor_id, $paged = 1) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => get_option('posts_per_page', 10),
            'paged' => $paged,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'td_contributors',
                    'value' => '"' . $contributor_id . '"',
                    'compare' => 'LIKE',
                ),
            ),
        );
        
        return new WP_Query($args);
    }
}

