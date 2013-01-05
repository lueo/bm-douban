<?php get_header(); ?>

	<div id="content" class="post-lists">

		<?php if (have_posts()) : ?>

            <?php if (is_category()) { ?>
            <div class="archive_header"><span class="fl cat">
				<?php _e('Archive', 'bm' ); ?> | <?php echo single_cat_title(); ?></span> <span class="fr catrss"><?php $cat_obj = $wp_query->get_queried_object(); $cat_id = $cat_obj->cat_ID; echo '<a href="'; get_category_rss_link(true, $cat, ''); echo '">本存档的RSS feed</a>'; ?></span></div>
        
            <?php } elseif (is_day()) { ?>
            <div class="archive_header"><?php _e('Archive', 'bm' ); ?> | <?php the_time($GLOBALS['woodate']); ?></div>

            <?php } elseif (is_month()) { ?>
            <div class="archive_header"><?php _e('Archive', 'bm' ); ?> | <?php the_time('F, Y'); ?></div>

            <?php } elseif (is_year()) { ?>
            <div class="archive_header"><?php _e('Archive', 'bm' ); ?> | <?php the_time('Y'); ?></div>

            <?php } elseif (is_author()) { ?>
            <div class="archive_header"><?php _e('Archive by Author', 'bm' ); ?></div>

            <?php } elseif (is_tag()) { ?>
            <div class="archive_header"><?php _e('Tag Archives:', 'bm' ); ?> <?php echo single_tag_title('', true); ?></div>

            <?php } elseif (is_search()) { ?>
            <div class="archive_header"><?php _e('Search Results for: ', 'bm' ); ?> <?php echo get_search_query(); ?></div>
            
            <?php } ?>


                <br style="clear:all" />

		<?php while (have_posts()) : the_post(); ?>
		<div class="post">
				<h2 id="post-<?php the_ID(); ?>" class="green"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?></small>
				
				<div class="entry">
					<?php the_content(); ?>
				</div>
		
				<p class="postmetadata">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p> 

			</div>
	
		<?php endwhile; ?>

		<div class="navigation">
<?php if(function_exists('pagenavi')) { pagenavi(); } ?>
		</div>
	
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
		
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
