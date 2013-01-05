<?php
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>

<div id="content" class="widecolumn">

<?php include (TEMPLATEPATH . '/searchform.php'); ?>


	<div <?php post_class("post"); ?>>
		<h2>count:</h2>
		<ul>
		<li>
		文章总数：<?php $count_posts = wp_count_posts(); echo $published_posts = $count_posts->publish; ?> 篇
		</li>
		<li>
		评论总数：<?php $total_comments = get_comment_count(); echo $total_comments['approved'];?> 条
		</li>
		</ul>
		<h2>yearly archive:</h2>
		<ul><?php wp_get_archives('type=yearly&show_post_count=1'); ?></ul>
		<h2>monthly archive:</h2>
		<ul><?php wp_get_archives('type=monthly&show_post_count=1'); ?></ul>
		<h2>category archive:</h2>
		<ul><?php wp_list_cats('sort_column=name&optioncount=1') ?></ul>
		<h2>posts archive:</h2>
		<ul><?php wp_get_archives('type=postbypost') ?></ul>
	</div>

</div>	

<?php get_sidebar(); ?>
<?php get_footer(); ?>
