<?php get_header(); ?>

	<div id="content" class="post-lists">

	<?php if (have_posts()) : ?>
		
		<?php while (have_posts()) : the_post(); ?>
				
			<div class="post" id="post-<?php the_ID(); ?>">
				<h2 class="green"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?>    </a></h2>
				<small class="postdata_small"><?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?></small>
				
				<div class="entry">
					<?php the_content(); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php comments_popup_link('暂无回应', '(1个回应)', '(%个回应)'); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link('(改)'); ?>
				</div>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
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
