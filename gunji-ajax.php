<?php
/** Sets up the WordPress Environment. */
require_once(dirname(__FILE__)."/../../../wp-load.php"); // 此 comments-ajax.php 位於主題資料夾,所以位置已不同

nocache_headers();

extract($_REQUEST);

$start = $now_showing;
$output = array();
$comlist=array();
$limitclause="LIMIT " . $start . "," . $show_more;

global $wpdb;
$q = "SELECT ID, post_title, comment_ID, comment_date, comment_post_ID, comment_parent, comment_author, comment_author_email, comment_author_url, comment_content FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' AND comment_post_ID = $post_id ORDER BY comment_date_gmt DESC $limitclause";
$comments=$wpdb->get_results($q);

if(is_array($comments)){
foreach ($comments as $comment) {
	$start+=1; ?>
<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div class="comment-author">
		<?php echo get_avatar($comment,$size='48',$default=''); ?>
		<h4 class="com-green">
			<CITE><?php print get_comment_author_link(); ?></CITE>
			<SMALL><?php print get_comment_date(); ?>&nbsp;<?php print get_comment_time(); ?><?php edit_comment_link(__('(Edit)'),'  ','') ?></SMALL> 
			<span class="replay-button" style="display:none;"> <?php comment_reply_link(array('depth' => $depth,'max_depth' => '12', 'reply_text' => "[回复]")) ?></span>
        </h4>
	</div>
	<div class="comment-content">
		<?php comment_text(); ?>
	</div>
</li>
<?php
}
}
?>