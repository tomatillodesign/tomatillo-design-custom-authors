<?php
// ABOUTME: Contributors archive page template with Genesis integration.
// ABOUTME: Lists all published contributors in a simple card layout.

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Start Genesis
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'tdc_contributors_archive_loop');

function tdc_contributors_archive_loop() {
    ?>
    <div class="tdc-contributors-archive">
        
        <header class="archive-description">
            <h1 class="archive-title">Contributors</h1>
            <p>Meet the contributors who write for this site.</p>
        </header>
        
        <?php if (have_posts()) : ?>
            
            <div class="tdc-contributors-grid">
                
                <?php while (have_posts()) : the_post(); 
                    $short_bio = get_field('td_short_bio');
                    $affiliation = get_field('td_affiliation');
                    
                    // Fallback for short bio
                    if (empty($short_bio)) {
                        $short_bio = wp_trim_words(get_the_excerpt(), 30, '...');
                    }
                ?>
                    
                    <article <?php post_class('tdc-contributor-card'); ?>>
                        <div class="tdc-card-inner">
                            
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="tdc-card-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'tdc-card-image')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="tdc-card-content">
                                <h2 class="tdc-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <?php if ($affiliation) : ?>
                                    <p class="tdc-card-affiliation"><?php echo esc_html($affiliation); ?></p>
                                <?php endif; ?>
                                
                                <?php if ($short_bio) : ?>
                                    <div class="tdc-card-bio"><?php echo wp_kses_post($short_bio); ?></div>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="tdc-card-link">View Profile →</a>
                            </div>
                            
                        </div>
                    </article>
                    
                <?php endwhile; ?>
                
            </div>
            
            <?php
            // Pagination
            genesis_posts_nav();
            
        else :
            echo '<p>No contributors found.</p>';
        endif;
        ?>
        
    </div>
    <?php
}

genesis();

