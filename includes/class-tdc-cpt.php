<?php
// ABOUTME: Registers the Contributor custom post type.
// ABOUTME: Configures CPT with public URLs, archive, and proper supports.

class TDC_CPT {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_contributor_cpt'));
    }
    
    public function register_contributor_cpt() {
        $labels = array(
            'name'                  => _x('Contributors', 'Post Type General Name', 'tomatillo-design-custom-authors'),
            'singular_name'         => _x('Contributor', 'Post Type Singular Name', 'tomatillo-design-custom-authors'),
            'menu_name'             => __('Contributors', 'tomatillo-design-custom-authors'),
            'name_admin_bar'        => __('Contributor', 'tomatillo-design-custom-authors'),
            'archives'              => __('Contributor Archives', 'tomatillo-design-custom-authors'),
            'attributes'            => __('Contributor Attributes', 'tomatillo-design-custom-authors'),
            'parent_item_colon'     => __('Parent Contributor:', 'tomatillo-design-custom-authors'),
            'all_items'             => __('All Contributors', 'tomatillo-design-custom-authors'),
            'add_new_item'          => __('Add New Contributor', 'tomatillo-design-custom-authors'),
            'add_new'               => __('Add New', 'tomatillo-design-custom-authors'),
            'new_item'              => __('New Contributor', 'tomatillo-design-custom-authors'),
            'edit_item'             => __('Edit Contributor', 'tomatillo-design-custom-authors'),
            'update_item'           => __('Update Contributor', 'tomatillo-design-custom-authors'),
            'view_item'             => __('View Contributor', 'tomatillo-design-custom-authors'),
            'view_items'            => __('View Contributors', 'tomatillo-design-custom-authors'),
            'search_items'          => __('Search Contributor', 'tomatillo-design-custom-authors'),
            'not_found'             => __('Not found', 'tomatillo-design-custom-authors'),
            'not_found_in_trash'    => __('Not found in Trash', 'tomatillo-design-custom-authors'),
            'featured_image'        => __('Featured Image', 'tomatillo-design-custom-authors'),
            'set_featured_image'    => __('Set featured image', 'tomatillo-design-custom-authors'),
            'remove_featured_image' => __('Remove featured image', 'tomatillo-design-custom-authors'),
            'use_featured_image'    => __('Use as featured image', 'tomatillo-design-custom-authors'),
            'insert_into_item'      => __('Insert into contributor', 'tomatillo-design-custom-authors'),
            'uploaded_to_this_item' => __('Uploaded to this contributor', 'tomatillo-design-custom-authors'),
            'items_list'            => __('Contributors list', 'tomatillo-design-custom-authors'),
            'items_list_navigation' => __('Contributors list navigation', 'tomatillo-design-custom-authors'),
            'filter_items_list'     => __('Filter contributors list', 'tomatillo-design-custom-authors'),
        );
        
        $args = array(
            'label'                 => __('Contributor', 'tomatillo-design-custom-authors'),
            'description'           => __('External content contributors', 'tomatillo-design-custom-authors'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'revisions', 'excerpt'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array(
                'slug'              => 'contributors',
                'with_front'        => false,
            ),
        );
        
        register_post_type('contributor', $args);
    }
}

