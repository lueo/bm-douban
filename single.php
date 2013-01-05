<?php get_header(); ?>

	<div id="content" class="widecolumn">
				
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="post" id="post-<?php the_ID(); ?>">
			<h2 class="green"><?php the_title(); ?></h2>
<small class="postdata_small"><?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?>    <?php the_category(', ') ?>   	<?php post_comments_feed_link($link_text = 'RSS'); ?></small>
	
			<div class="entrytext">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
	<?php edit_post_link('改', '(', ')'); ?>
				<?php link_pages('<div class="center_pages"><strong>Pages:</strong> ', '</div>', 'number'); ?>

				<br style="clear:both" />	
	<?php the_tags('<div class="link_pages">标签： ', '&nbsp;&nbsp;&nbsp;&nbsp;', '</div>'); ?>
			</div>
		</div>

                <div class="navigation">
                        <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
                        <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
                </div>

				<br style="clear:both" />	
		
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p>Sorry, no posts matched your criteria.</p>
	
<?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
