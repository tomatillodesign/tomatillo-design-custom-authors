<?php
// ABOUTME: Adds contributor filter dropdown to Posts list table.
// ABOUTME: Allows filtering posts by assigned contributor in admin.

class TDC_Admin_Filters {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('restrict_manage_posts', array($this, 'add_contributor_filter'));
        add_filter('parse_query', array($this, 'filter_by_contributor'));
    }
    
    /**
     * Add contributor filter dropdown to posts list
     * 
     * @param string $post_type Current post type
     */
    public function add_contributor_filter($post_type) {
        if ($post_type !== 'post') {
            return;
        }
        
        // Get all published contributors
        $contributors = get_posts(array(
            'post_type' => 'contributor',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        
        if (empty($contributors)) {
            return;
        }
        
        $selected = isset($_GET['contributor_filter']) ? intval($_GET['contributor_filter']) : 0;
        
        ?>
        <select name="contributor_filter" id="contributor_filter">
            <option value="0"><?php _e('All Contributors', 'tomatillo-design-custom-authors'); ?></option>
            <?php foreach ($contributors as $contributor) : ?>
                <option value="<?php echo esc_attr($contributor->ID); ?>" <?php selected($selected, $contributor->ID); ?>>
                    <?php echo esc_html($contributor->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    /**
     * Filter posts by selected contributor
     * 
     * @param WP_Query $query Current query
     */
    public function filter_by_contributor($query) {
        global $pagenow;
        
        // Only on posts list page
        if (!is_admin() || $pagenow !== 'edit.php' || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'post') {
            return;
        }
        
        // Check if filter is set
        if (!isset($_GET['contributor_filter']) || $_GET['contributor_filter'] == 0) {
            return;
        }
        
        $contributor_id = intval($_GET['contributor_filter']);
        
        // Add meta query to filter by contributor
        $meta_query = array(
            array(
                'key' => 'td_contributors',
                'value' => '"' . $contributor_id . '"',
                'compare' => 'LIKE',
            ),
        );
        
        $query->set('meta_query', $meta_query);
    }
}

