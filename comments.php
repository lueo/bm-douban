<div class="comments-box">

	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'twentyeleven' ); ?></p>
	</div><!-- #comments -->
	<?php
			/* Stop the rest of comments.php from being processed,
			 * but don't kill the script entirely -- we still have
			 * to fully load the template.
			 */
			return;
		endif;
	?>

	<!-- 评论列表 -->
	<?php if ( have_comments() ){ ?>

		<h2 id="comments" class="comment-green">
			<?php comments_number(__('No Comments', 'bm'), __('One Comment', 'bm'), __('% Comments', 'bm') );?>　· · · · · ·
		</h2>

		<ol class="commentlist clearfix">
			<?php wp_list_comments('type=comment&callback=mytheme_comment'); ?>
		</ol>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
        <nav id="comment-nav-above" class="clearfix">
            <div class="nav-previous alignleft"><?php previous_comments_link( __( '&larr; 旧一点的评论' ) ); ?></div>
            <div class="nav-next alignright"><?php next_comments_link( __( '新一点的评论 &rarr;' ) ); ?></div>
        </nav>
        <?php endif; // check for comment navigation ?>

	<?php } ?>
	<!-- end 评论列表 -->

	<?php if ('open' == $post->comment_status) : // 评论表单	?>

	<div id="respond">
		<h2 class="comment-green">评论　· · · · · ·  	</h2>

		<div class="cancel-comment-reply">
			<span class="small"><?php cancel_comment_reply_link('取消'); ?></span>
		</div><!-- cancel-comment-reply -->

		<?php if ( get_option('comment_registration') && !$user_ID ){ // 是否登录 ?>

			<p><?php print YOU_MUST_BE; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php print LOGGED_IN; ?></a> <?php print TO_POST_COMMENT; ?>.</p>

		<?php }else{ ?>

			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

				<?php if ( !$user_ID ) : ?>

					<p><input class="text" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
					<label for="author"><small><?php _e('Name', 'bm') ?>(<?php if ($req) _e('required', 'bm'); ?>)</small></label></p>

					<p><input class="text" type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
					<label for="email"><small><?php _e('Mail', 'bm') ?> (<?php _e('wont publish', 'bm'); ?>) (<?php if ($req) _e('required', 'bm'); ?>)</small></label></p>

					<p><input class="text" type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
					<label for="url"><small><?php _e('website', 'bm') ?></small></label></p>

				<?php endif; ?>


					<?php do_action('comment_form', $post->ID); ?>

					<p><textarea name="comment" id="comment" rows="4" cols="54" class="resizable" tabindex="4"></textarea></p>

					<p><input class="input_submit" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e( 'submit comment', 'bm' ); ?>" />
					<?php comment_id_fields(); ?>
					</p>

			</form>

		<?php } // 是否登录 ?>

	</div>
	<?php endif; // 评论表单 ?>

</div>