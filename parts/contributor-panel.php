<?php
// ABOUTME: Template part for contributor panels displayed after post content.
// ABOUTME: Shows thumbnail, bio, affiliation, and links for each contributor.

/**
 * Contributor Panels Template
 * 
 * Variables available:
 * @var array $contributor_ids Array of contributor post IDs
 * @var array $snapshots Array of snapshot data
 */

if (empty($contributor_ids)) {
    return;
}
?>

<div class="tdc-contributor-panels">
    <h3 class="tdc-panels-title">About the Contributor<?php echo count($contributor_ids) > 1 ? 's' : ''; ?></h3>
    
    <?php foreach ($contributor_ids as $contributor_id) : 
        $status = get_post_status($contributor_id);
        $snapshot = tdc_get_snapshot_by_id($snapshots, $contributor_id);
        
        // Get contributor data
        if ($status === 'publish') {
            $name = get_the_title($contributor_id);
            $url = get_permalink($contributor_id);
            $thumbnail_id = get_post_thumbnail_id($contributor_id);
            $short_bio = get_field('td_short_bio', $contributor_id);
            $affiliation = get_field('td_affiliation', $contributor_id);
            $links = get_field('td_links', $contributor_id);
            
            // Fallback for short bio
            if (empty($short_bio)) {
                $short_bio = wp_trim_words(get_the_excerpt($contributor_id), 30, '...');
            }
        } else {
            // Use snapshot data for unpublished contributors
            $name = $snapshot ? $snapshot['name'] : get_the_title($contributor_id);
            $url = null;
            $thumbnail_id = null;
            $short_bio = '';
            $affiliation = '';
            $links = array();
        }
    ?>
    
    <div class="tdc-contributor-panel">
        <div class="tdc-panel-inner">
            <?php if ($thumbnail_id) : ?>
                <div class="tdc-panel-thumbnail">
                    <?php echo wp_get_attachment_image($thumbnail_id, 'thumbnail', false, array('class' => 'tdc-contributor-image')); ?>
                </div>
            <?php endif; ?>
            
            <div class="tdc-panel-content">
                <h4 class="tdc-contributor-name">
                    <?php if ($url) : ?>
                        <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($name); ?></a>
                    <?php else : ?>
                        <?php echo esc_html($name); ?>
                    <?php endif; ?>
                </h4>
                
                <?php if ($affiliation) : ?>
                    <p class="tdc-contributor-affiliation"><?php echo esc_html($affiliation); ?></p>
                <?php endif; ?>
                
                <?php if ($short_bio) : ?>
                    <div class="tdc-contributor-bio"><?php echo wp_kses_post($short_bio); ?></div>
                <?php endif; ?>
                
                <?php if (!empty($links) && is_array($links)) : ?>
                    <div class="tdc-contributor-links">
                        <?php foreach ($links as $link) : ?>
                            <a href="<?php echo esc_url($link['url']); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="tdc-contributor-link">
                                <?php echo esc_html($link['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>
</div>

