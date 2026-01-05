<?php
// ABOUTME: Generates JSON-LD structured data for posts with contributors.
// ABOUTME: Outputs Article schema with author arrays matching visible byline.

class TDC_Schema {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_head', array($this, 'output_schema'), 10);
    }
    
    /**
     * Output JSON-LD schema for posts with contributors
     */
    public function output_schema() {
        // Only on single posts
        if (!is_singular('post')) {
            return;
        }
        
        // Get contributors
        $contributor_ids = get_field('td_contributors');
        
        if (empty($contributor_ids)) {
            return;
        }
        
        // Build schema
        $schema = $this->build_article_schema(get_the_ID(), $contributor_ids);
        
        if (empty($schema)) {
            return;
        }
        
        // Output JSON-LD
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        echo '</script>' . "\n";
    }
    
    /**
     * Build Article schema array
     * 
     * @param int $post_id Post ID
     * @param array $contributor_ids Array of contributor post IDs
     * @return array Schema data
     */
    private function build_article_schema($post_id, $contributor_ids) {
        $post = get_post($post_id);
        
        if (!$post) {
            return array();
        }
        
        // Build authors array
        $authors = array();
        foreach ($contributor_ids as $contributor_id) {
            $author_data = $this->build_author_person($contributor_id);
            if (!empty($author_data)) {
                $authors[] = $author_data;
            }
        }
        
        if (empty($authors)) {
            return array();
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post_id),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => $authors,
        );
        
        // Add featured image if available
        if (has_post_thumbnail($post_id)) {
            $image_id = get_post_thumbnail_id($post_id);
            $image_data = wp_get_attachment_image_src($image_id, 'full');
            if ($image_data) {
                $schema['image'] = array(
                    '@type' => 'ImageObject',
                    'url' => $image_data[0],
                    'width' => $image_data[1],
                    'height' => $image_data[2],
                );
            }
        }
        
        return $schema;
    }
    
    /**
     * Build Person schema for a contributor
     * 
     * @param int $contributor_id Contributor post ID
     * @return array Person schema data
     */
    private function build_author_person($contributor_id) {
        $status = get_post_status($contributor_id);
        
        // Get snapshots for fallback
        $snapshots = TDC_Snapshots::get_snapshots(get_the_ID());
        $snapshot = tdc_get_snapshot_by_id($snapshots, $contributor_id);
        
        $person = array(
            '@type' => 'Person',
        );
        
        // Name is always included
        if ($status === 'publish') {
            $person['name'] = get_the_title($contributor_id);
        } else {
            $person['name'] = $snapshot ? $snapshot['name'] : get_the_title($contributor_id);
        }
        
        // URL only for published contributors
        if ($status === 'publish') {
            $person['url'] = get_permalink($contributor_id);
        }
        
        return $person;
    }
}

