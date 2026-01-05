<?php
// ABOUTME: Helper functions and utilities for the Contributors plugin.
// ABOUTME: Includes formatting functions like Oxford comma list builder and link logic.

/**
 * Format a list of contributor names with Oxford comma
 * 
 * @param array $names Array of contributor names
 * @return string Formatted string with proper Oxford comma usage
 */
function tdc_format_contributor_list($names) {
    if (empty($names)) {
        return '';
    }
    
    $count = count($names);
    
    if ($count === 1) {
        return $names[0];
    }
    
    if ($count === 2) {
        return $names[0] . ' and ' . $names[1];
    }
    
    // 3 or more: use Oxford comma
    $last = array_pop($names);
    return implode(', ', $names) . ', and ' . $last;
}

/**
 * Check if a contributor should be linked
 * 
 * @param int $contributor_id Contributor post ID
 * @return bool True if contributor is published
 */
function tdc_should_link_contributor($contributor_id) {
    return get_post_status($contributor_id) === 'publish';
}

/**
 * Get contributor display name with optional link
 * 
 * @param int $contributor_id Contributor post ID
 * @param array $snapshot Optional snapshot data for fallback
 * @return string HTML for contributor name (linked or plain text)
 */
function tdc_get_contributor_name_html($contributor_id, $snapshot = null) {
    $status = get_post_status($contributor_id);
    
    if ($status === 'publish') {
        $name = get_the_title($contributor_id);
        $url = get_permalink($contributor_id);
        return '<a href="' . esc_url($url) . '">' . esc_html($name) . '</a>';
    }
    
    // Use snapshot name if available, otherwise get current title
    if ($snapshot && isset($snapshot['name'])) {
        $name = $snapshot['name'];
    } else {
        $name = get_the_title($contributor_id);
    }
    
    return esc_html($name);
}

/**
 * Build snapshot data for a contributor
 * 
 * @param int $contributor_id Contributor post ID
 * @return array Snapshot data with id, name, and permalink
 */
function tdc_build_contributor_snapshot($contributor_id) {
    return array(
        'id' => $contributor_id,
        'name' => get_the_title($contributor_id),
        'permalink' => get_permalink($contributor_id)
    );
}

/**
 * Build snapshots array from contributor IDs
 * 
 * @param array $contributor_ids Array of contributor post IDs
 * @return array Array of snapshot objects
 */
function tdc_build_snapshots_array($contributor_ids) {
    if (empty($contributor_ids)) {
        return array();
    }
    
    $snapshots = array();
    foreach ($contributor_ids as $id) {
        $snapshots[] = tdc_build_contributor_snapshot($id);
    }
    
    return $snapshots;
}

/**
 * Get snapshot by contributor ID
 * 
 * @param array $snapshots Array of snapshot objects
 * @param int $contributor_id Contributor post ID
 * @return array|null Snapshot data or null if not found
 */
function tdc_get_snapshot_by_id($snapshots, $contributor_id) {
    foreach ($snapshots as $snapshot) {
        if (isset($snapshot['id']) && $snapshot['id'] === $contributor_id) {
            return $snapshot;
        }
    }
    return null;
}

