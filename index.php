<?php get_header(); ?>

    <div id="content" class="post-lists clearfix">

    <?php if (have_posts()) : ?>
        
        <?php while (have_posts()) : the_post(); ?>
                
            <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
                <h2 class="green clearfix"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?>    </a></h2>
                <small class="postdata_small clearfix"><?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?>  <?php the_category(', ') ?>    </small>
                
                <div class="entry clearfix">
                    <?php the_content(); ?>
                    <div class="entry-index-meta clearfix t_r">
                        <?php comments_popup_link('暂无回应', '(1个回应)', '(%个回应)'); ?>
                        <?php edit_post_link('(改)'); ?>
                    </div>
                </div>
            </article>

        <?php endwhile; ?>

        <div class="navigation clearfix">
<?php if(function_exists('pagenavi')) { pagenavi(); } ?>
        </div>
        
    <?php else : ?>

        <h2 class="center">Not Found</h2>
        <p class="center">Sorry, but you are looking for something that isn't here.</p>
        <?php include (TEMPLATEPATH . "/searchform.php"); ?>

    <?php endif; ?>

    </div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
