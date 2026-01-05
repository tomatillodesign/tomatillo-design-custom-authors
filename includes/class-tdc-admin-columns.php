<?php
// ABOUTME: Adds Contributors column to Posts list table in WordPress admin.
// ABOUTME: Displays assigned contributors with links to their profile pages.

class TDC_Admin_Columns {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('manage_post_posts_columns', array($this, 'add_contributors_column'));
        add_action('manage_post_posts_custom_column', array($this, 'render_contributors_column'), 10, 2);
        add_filter('manage_edit-post_sortable_columns', array($this, 'make_sortable'));
    }
    
    /**
     * Add Contributors column to posts list table
     * 
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function add_contributors_column($columns) {
        // Insert Contributors column before date
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'date') {
                $new_columns['contributors'] = __('Contributors', 'tomatillo-design-custom-authors');
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }
    
    /**
     * Render Contributors column content
     * 
     * @param string $column Column name
     * @param int $post_id Post ID
     */
    public function render_contributors_column($column, $post_id) {
        if ($column !== 'contributors') {
            return;
        }
        
        $contributor_ids = get_field('td_contributors', $post_id, false);
        
        if (empty($contributor_ids)) {
            echo '<span style="color: #999;">—</span>';
            return;
        }
        
        $names = array();
        foreach ($contributor_ids as $contributor_id) {
            $status = get_post_status($contributor_id);
            $name = get_the_title($contributor_id);
            
            if ($status === 'publish') {
                $url = get_permalink($contributor_id);
                $names[] = '<a href="' . esc_url($url) . '" target="_blank">' . esc_html($name) . '</a>';
            } else {
                $names[] = '<span style="color: #999;">' . esc_html($name) . ' (draft)</span>';
            }
        }
        
        echo implode(', ', $names);
    }
    
    /**
     * Make Contributors column sortable
     * 
     * @param array $columns Sortable columns
     * @return array Modified columns
     */
    public function make_sortable($columns) {
        // Note: Sorting by relationship field is complex and may not work well
        // Commenting out for now as it's not a critical feature
        // $columns['contributors'] = 'contributors';
        return $columns;
    }
}

