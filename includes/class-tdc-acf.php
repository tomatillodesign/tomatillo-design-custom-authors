<?php
// ABOUTME: Registers all ACF fields for the Contributors plugin via PHP.
// ABOUTME: Includes relationship field on posts, contributor meta fields, and hidden snapshot fields.

class TDC_ACF {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('acf/init', array($this, 'register_fields'));
    }
    
    public function register_fields() {
        // Relationship field on Posts
        $this->register_post_contributors_field();
        
        // Fields on Contributor CPT
        $this->register_contributor_fields();
        
        // Hidden snapshot fields on Posts
        $this->register_snapshot_fields();
    }
    
    private function register_post_contributors_field() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        acf_add_local_field_group(array(
            'key' => 'group_td_post_contributors',
            'title' => 'Contributors',
            'fields' => array(
                array(
                    'key' => 'field_td_contributors',
                    'label' => 'Contributors',
                    'name' => 'td_contributors',
                    'type' => 'relationship',
                    'instructions' => 'Select one or more contributors for this post. Drag to reorder.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'post_type' => array('contributor'),
                    'taxonomy' => array(),
                    'filters' => array('search'),
                    'elements' => array(),
                    'min' => '',
                    'max' => '',
                    'return_format' => 'id',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'side',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
    
    private function register_contributor_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        acf_add_local_field_group(array(
            'key' => 'group_td_contributor_meta',
            'title' => 'Contributor Information',
            'fields' => array(
                array(
                    'key' => 'field_td_short_bio',
                    'label' => 'Short Bio',
                    'name' => 'td_short_bio',
                    'type' => 'textarea',
                    'instructions' => 'Brief bio displayed in post footer panel. Keep it short (1-2 sentences).',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'rows' => 3,
                    'maxlength' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_td_affiliation',
                    'label' => 'Affiliation',
                    'name' => 'td_affiliation',
                    'type' => 'text',
                    'instructions' => 'Organization or affiliation (optional).',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'maxlength' => '',
                ),
                array(
                    'key' => 'field_td_links',
                    'label' => 'Links',
                    'name' => 'td_links',
                    'type' => 'repeater',
                    'instructions' => 'External links (website, social media, etc.).',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'collapsed' => 'field_td_link_label',
                    'min' => 0,
                    'max' => 10,
                    'layout' => 'table',
                    'button_label' => 'Add Link',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_td_link_label',
                            'label' => 'Label',
                            'name' => 'label',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'maxlength' => '',
                            'placeholder' => 'Website',
                        ),
                        array(
                            'key' => 'field_td_link_url',
                            'label' => 'URL',
                            'name' => 'url',
                            'type' => 'url',
                            'instructions' => '',
                            'required' => 1,
                            'placeholder' => 'https://',
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'contributor',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
    
    private function register_snapshot_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        
        acf_add_local_field_group(array(
            'key' => 'group_td_contributor_snapshots',
            'title' => 'Contributor Snapshots (Internal)',
            'fields' => array(
                array(
                    'key' => 'field_td_contributor_snapshots',
                    'label' => 'Snapshots Data',
                    'name' => 'td_contributor_snapshots',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'rows' => 4,
                ),
                array(
                    'key' => 'field_td_contributor_snapshot_updated',
                    'label' => 'Last Updated',
                    'name' => 'td_contributor_snapshot_updated',
                    'type' => 'date_time_picker',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'display_format' => 'Y-m-d H:i:s',
                    'return_format' => 'Y-m-d H:i:s',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post',
                    ),
                ),
            ),
            'menu_order' => 100,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => false,
        ));
    }
}

