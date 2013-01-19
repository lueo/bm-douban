<?php get_header(); ?>

<div id="content" class="widecolumn" role="main">

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <?php get_template_part( 'content', get_post_format() ); ?>

        <nav class="navigation clearfix">

                <div class="f_l t_l"><?php previous_post_link('&laquo; %link') ?></div>

                <div class="f_r t_r"><?php next_post_link('%link &raquo;') ?></div>

        </nav>

    <?php comments_template(); ?>

    <?php endwhile;  ?>

    <?php  else: ?>

        <?php get_template_part( 'content', 'none' ); ?>

<?php endif; ?>


</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
