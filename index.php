<?php get_header(); ?>

    <div id="content" class="post-lists clearfix" role="main">

    <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part( 'content', get_post_format() ); ?>
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
