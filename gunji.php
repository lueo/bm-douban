<?php
/*
Template Name: gunji
*/
?>
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

	<!-- You can start editing here. -->
	<div class="comments-box">


		<?php 	bm_comment_form(); 	?>

			<h2 id="comments" class="comment-green"><?php comments_number(__('No Comments', 'bm'), __('One Comment', 'bm'), __('% Comments', 'bm') );?>　· · · · · ·  	</h2>

			<ol class="commentlist" id="gunji">
	<?php 
	global $post;
	$comment_array = get_comments('order=ASC&post_id='.$post->ID);
	$total_comments = count($comment_array);
	if(!is_array($comment_array)) return;
	$comment_array = array_reverse($comment_array, TRUE);

	$i=0;
	$now_show=30;
	$show_more=10;
	foreach($comment_array as $com_id){
		$i++;	
		if($i>$now_show)
			continue;
		$comment = get_comment($com_id);
		$GLOBALS['comment'] = $comment; ?>
			<li id="comment-<?php comment_ID(); ?>" <?php if($comment->comment_parent=='0')comment_class();else comment_class("reply"); ?> id="li-comment-<?php comment_ID() ?>">
				<div class="comment-author">
					<?php echo get_avatar($comment,$size='48',$default=''); ?>
					<h4 class="com-green">
						<CITE><?php comment_author_link(); ?></CITE>
						<SMALL><?php comment_date(); ?>&nbsp;<?php comment_time(); ?><?php edit_comment_link(__('(Edit)'),'  ','') ?></SMALL> 
						<span class="replay-button" style="display:none;"> <?php comment_reply_link(array('depth' => $depth,'max_depth' => '12', 'reply_text' => "[回复]")) ?></span>
					</h4>
				</div>
				<div class="comment-content">
					<?php comment_text();  ?>
				</div>
			</li>
		<?php 
	}
	?>
			</ol>
			<p><a href="javascript:void(0);" id="more">显示更多>></a></p>
			<p id="gunji-loading">LOADING...</p>
		<form id="gunji-form">
			<input type="hidden" name="now_showing" id="now_showing" value="<?php echo $now_show; ?>" />
			<input type="hidden" name="show_more" id="show_more" value="<?php echo $show_more; ?>" />
			<input type="hidden" name="post_id" id="post_id" value="<?php echo $post->ID; ?>" />
			<input type="hidden" name="total_cmts" id="total_cmts" value="<?php echo $total_comments; ?>" />
		</form>
			<div class="clear"></div>

		<script type="text/javascript">
		<!--
			//Variables
		var loading = jQuery("#gunji-loading");
		var more = jQuery("#more");
		var target_page, query, now_showing, total_cmts;

		//show loading bar
		function showLoading(){
			loading.slideDown("slow");
		}
		//hide loading bar
		function hideLoading(){
			loading.slideUp("slow");
		};

		//update now showing after every click of show more
		function update_now(){
			now_showing = parseInt(jQuery("#now_showing").attr("value")) + parseInt(jQuery("#show_more").attr("value"));
			jQuery("#now_showing").attr("value", now_showing );
			
			//hide show more when total comments are shown
			total_cmts = parseInt(jQuery("#total_cmts").attr("value"));
			if(now_showing >= total_cmts)
				more.slideUp("slow");
		}

		//When show more clicked
		more.click(function(){
			showLoading();
			
			//define target page and query string
			target_page = "<?php echo THEMEURL; ?>/gunji-ajax.php";
			query = jQuery("#gunji-form").serialize();

		/** Ajax */
			jQuery.ajax( {
				url: target_page,
				data: query,
				type: 'post',

				error: function(request) {
					hideLoading();
					content_location.after('error');
					},

				success: function(data) {
					hideLoading();
					jQuery("#gunji li:last").after(data);
					update_now();
				}
			}); // end Ajax
		  return false;
		});

		//intially hide loading bar
		hideLoading();
		//-->
		</script>

  <?php endwhile; endif; ?>

	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
