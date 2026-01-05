<?php
// ABOUTME: Integrates with Genesis Framework to override entry meta and inject contributor content.
// ABOUTME: Removes default author output and adds custom byline with footer panels.

class TDC_Genesis_Hooks {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook into after_setup_theme to ensure we run before Genesis processes hooks
        add_action('after_setup_theme', array($this, 'setup_genesis_hooks'), 15);
    }
    
    /**
     * Setup Genesis hooks (runs after Genesis is loaded)
     */
    public function setup_genesis_hooks() {
        // Only initialize if Genesis is active
        if (!function_exists('genesis')) {
            return;
        }
        
        // Remove default Genesis post info and replace with our custom version
        remove_action('genesis_entry_header', 'genesis_post_info', 12);
        add_action('genesis_entry_header', array($this, 'custom_entry_header'), 12);
        
        // Override entry meta filter as backup
        add_filter('genesis_post_info', array($this, 'custom_post_info'), 10, 1);
        
        // Add contributor panels after entry content (before related posts and footer)
        add_action('genesis_after_entry_content', array($this, 'render_contributor_panels'), 10);
    }
    
    /**
     * Custom entry header with contributor byline
     */
    public function custom_entry_header() {
        // Only modify single post pages
        if (!is_singular('post')) {
            genesis_post_info();
            return;
        }
        
        // Get contributors
        $contributor_ids = get_field('td_contributors');
        
        // Build the entry meta HTML
        $date = '<time class="entry-time">' . get_the_date() . '</time>';
        
        // If no contributors, show date only
        if (empty($contributor_ids)) {
            echo '<p class="entry-meta">' . $date . '</p>';
            return;
        }
        
        // Build contributor byline
        $snapshots = TDC_Snapshots::get_snapshots(get_the_ID());
        $names_html = array();
        
        foreach ($contributor_ids as $contributor_id) {
            $snapshot = tdc_get_snapshot_by_id($snapshots, $contributor_id);
            $names_html[] = tdc_get_contributor_name_html($contributor_id, $snapshot);
        }
        
        // Format with Oxford comma
        $byline = $this->format_byline_html($names_html);
        
        // Output custom format: Date • By Contributors
        echo '<p class="entry-meta">' . $date . ' <span class="tdc-separator">•</span> By ' . $byline . '</p>';
    }
    
    /**
     * Override Genesis post info (entry meta)
     * 
     * @param string $post_info Default post info markup
     * @return string Modified post info markup
     */
    public function custom_post_info($post_info) {
        // Only modify single post pages
        if (!is_singular('post')) {
            return $post_info;
        }
        
        // Get contributors
        $contributor_ids = get_field('td_contributors');
        
        // If no contributors, show date only (remove author)
        if (empty($contributor_ids)) {
            return '[post_date]';
        }
        
        // Build contributor byline
        $snapshots = TDC_Snapshots::get_snapshots(get_the_ID());
        $names_html = array();
        
        foreach ($contributor_ids as $contributor_id) {
            $snapshot = tdc_get_snapshot_by_id($snapshots, $contributor_id);
            $names_html[] = tdc_get_contributor_name_html($contributor_id, $snapshot);
        }
        
        // Format with Oxford comma
        $byline = $this->format_byline_html($names_html);
        
        // Return custom format: Date • By Contributors
        return '[post_date] <span class="tdc-separator">•</span> By ' . $byline;
    }
    
    /**
     * Format byline with Oxford comma in HTML
     * 
     * @param array $names_html Array of HTML-formatted contributor names
     * @return string Formatted byline HTML
     */
    private function format_byline_html($names_html) {
        $count = count($names_html);
        
        if ($count === 1) {
            return $names_html[0];
        }
        
        if ($count === 2) {
            return $names_html[0] . ' and ' . $names_html[1];
        }
        
        // 3 or more: Oxford comma
        $last = array_pop($names_html);
        return implode(', ', $names_html) . ', and ' . $last;
    }
    
    /**
     * Render contributor panels after entry content
     */
    public function render_contributor_panels() {
        // Only on single posts
        if (!is_singular('post')) {
            return;
        }
        
        // Get contributors
        $contributor_ids = get_field('td_contributors');
        
        if (empty($contributor_ids)) {
            return;
        }
        
        // Load template part
        $snapshots = TDC_Snapshots::get_snapshots(get_the_ID());
        
        include TDC_PLUGIN_DIR . 'parts/contributor-panel.php';
    }
}

