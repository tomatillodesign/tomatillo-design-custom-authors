<?php
// ABOUTME: Single contributor page template with Genesis integration.
// ABOUTME: Displays contributor profile header and list of their posts using Yak card markup.

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Remove Genesis featured image output (we'll handle it in our template)
remove_action('genesis_entry_content', 'genesis_do_post_image', 8);

// Start Genesis
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'tdc_contributor_single_loop');

function tdc_contributor_single_loop() {
    if (!have_posts()) {
        return;
    }
    
    while (have_posts()) : the_post();
        
        $contributor_id = get_the_ID();
        $affiliation = get_field('td_affiliation');
        $links = get_field('td_links');
        
        ?>
        <article <?php post_class('tdc-contributor-single'); ?>>
            
            <header class="tdc-contributor-header">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="tdc-contributor-image-wrap">
                        <?php the_post_thumbnail('medium', array('class' => 'tdc-contributor-featured-image')); ?>
                    </div>
                <?php endif; ?>
                
                <div class="tdc-contributor-header-content">
                    <h1 class="tdc-contributor-title"><?php the_title(); ?></h1>
                    
                    <?php if ($affiliation) : ?>
                        <p class="tdc-contributor-affiliation-large"><?php echo esc_html($affiliation); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($links) && is_array($links)) : ?>
                        <div class="tdc-contributor-links-list">
                            <?php foreach ($links as $link) : ?>
                                <a href="<?php echo esc_url($link['url']); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="tdc-contributor-link-large">
                                    <?php echo esc_html($link['label']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </header>
            
            <?php if (get_the_content()) : ?>
                <div class="tdc-contributor-bio-full">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
            
        </article>
        
        <?php
        // Query posts by this contributor
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $posts_query = TDC_Templates::query_posts_by_contributor($contributor_id, $paged);
        
        if ($posts_query->have_posts()) :
        ?>
            
            <section class="tdc-contributor-posts">
                <h2 class="tdc-contributor-posts-title">Posts by <?php the_title(); ?></h2>
                
                <div class="yak-archive-grid">
                    <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                        
                        <article <?php post_class('yak-archive-card'); ?>>
                            <div class="yak-archive-entry">
                                
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="yak-archive-image">
                                        <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                            <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="yak-archive-body">
                                    <header class="yak-entry-header">
                                        <h3 class="yak-entry-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <p class="yak-entry-date">
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                        </p>
                                    </header>
                                    
                                    <?php if (has_excerpt()) : ?>
                                        <div class="yak-entry-excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                            </div>
                        </article>
                        
                    <?php endwhile; ?>
                </div>
                
                <?php
                // Pagination
                if ($posts_query->max_num_pages > 1) {
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $posts_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '&laquo; Previous',
                        'next_text' => 'Next &raquo;',
                    ));
                    echo '</div>';
                }
                ?>
                
            </section>
            
            <?php
            wp_reset_postdata();
        endif;
        
    endwhile;
}

genesis();

