<?php
/**
 * The template for displaying Comments.
 */
?>
<div id="comments-box">

	<?php if ( have_comments() ) : ?>

			<h3 id="comments" class="comment-green"><?php comments_number(__('No Comments', 'bm'), __('One Comment', 'bm'), __('% Comments', 'bm') );?></h3>

			<ol class="commentlist">
			<?php wp_list_comments(array( 'callback' => 'mytheme_comment' ,'type'=>'comment')); ?>
			</ol><div class="clear"></div>

			<div class="pagenavi"><?php paginate_comments_links('prev_text='.__('prev page','bm').'&next_text='.__('next page','bm'));?></div>

	<?php endif; ?>
	<!-- end comments display -->

	<?php bm_comment_form(); ?>

</div>