<?php
// ABOUTME: Manages snapshot updates when posts are saved.
// ABOUTME: Captures contributor names and permalinks to preserve display when contributors are unpublished.

class TDC_Snapshots {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('save_post', array($this, 'update_snapshots'), 10, 3);
    }
    
    /**
     * Update contributor snapshots when a post is saved
     * 
     * @param int $post_id Post ID
     * @param WP_Post $post Post object
     * @param bool $update Whether this is an existing post being updated
     */
    public function update_snapshots($post_id, $post, $update) {
        // Bail on autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Bail on revisions
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        // Only process posts
        if ($post->post_type !== 'post') {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Get assigned contributors
        $contributor_ids = get_field('td_contributors', $post_id, false);
        
        // Build snapshots array
        $snapshots = array();
        if (!empty($contributor_ids) && is_array($contributor_ids)) {
            $snapshots = tdc_build_snapshots_array($contributor_ids);
        }
        
        // Store snapshots as JSON
        $json = json_encode($snapshots);
        update_field('td_contributor_snapshots', $json, $post_id);
        
        // Update timestamp
        update_field('td_contributor_snapshot_updated', current_time('mysql'), $post_id);
    }
    
    /**
     * Get decoded snapshots for a post
     * 
     * @param int $post_id Post ID
     * @return array Array of snapshot objects
     */
    public static function get_snapshots($post_id) {
        $json = get_field('td_contributor_snapshots', $post_id, false);
        
        if (empty($json)) {
            return array();
        }
        
        $snapshots = json_decode($json, true);
        
        return is_array($snapshots) ? $snapshots : array();
    }
}

