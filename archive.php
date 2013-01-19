<?php get_header(); ?>

    <section id="primary" class="site-content">
        <div id="content" class="post-lists" role="main">

            <?php if (have_posts()) : ?>

                <header class="archive-header clearfix">
                    <?php if (is_category()) { ?>
                        <span class="fl cat">
                        <?php _e('Archive', 'bm' ); ?> | <?php echo single_cat_title(); ?></span> <span class="fr catrss"><?php $cat_obj = $wp_query->get_queried_object(); $cat_id = $cat_obj->cat_ID; echo '<a href="'; get_category_rss_link(true, $cat, ''); echo '">本存档的RSS feed</a>'; ?></span>

                    <?php } elseif (is_day()) { ?>
                        <?php _e('Archive', 'bm' ); ?> | <?php the_time($GLOBALS['woodate']); ?>

                    <?php } elseif (is_month()) { ?>
                        <?php _e('Archive', 'bm' ); ?> | <?php the_time('F, Y'); ?>

                    <?php } elseif (is_year()) { ?>
                        <?php _e('Archive', 'bm' ); ?> | <?php the_time('Y'); ?>

                    <?php } elseif (is_author()) { ?>
                        <?php _e('Archive by Author', 'bm' ); ?>

                    <?php } elseif (is_tag()) { ?>
                        <?php _e('Tag Archives:', 'bm' ); ?> <?php echo single_tag_title('', true); ?>

                    <?php } elseif (is_search()) { ?>
                        <?php _e('Search Results for: ', 'bm' ); ?> <?php echo get_search_query(); ?>

                    <?php } ?>
                </header>


            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part( 'content', get_post_format() ); ?>
            <?php endwhile; ?>

            <div class="navigation clearfix">
                <?php if(function_exists('pagenavi')) { pagenavi(); } ?>
            </div>

        <?php else : ?>
            <?php get_template_part( 'content', 'none' ); ?>
        <?php endif; ?>

        </div>
    </section><!-- #primary -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
