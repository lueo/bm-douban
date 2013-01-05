<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<h2><?php the_title(); ?></h2>
<small class="postdata_small"><?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?>    <?php post_comments_feed_link($link_text = 'RSS'); ?></small>
			<div class="entrytext">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
					<?php edit_post_link('改', '(', ')'); ?>
			</div>
		</div>
<?php comments_template(); ?>
	  <?php endwhile; endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
