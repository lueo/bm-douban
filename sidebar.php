<div id="sidebar">
<?php // 如果没有使用 Widget 才显示以下内容, 否则会显示 Widget 定义的内容
if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) :
?>
        <!-- widget 1 -->
		<div class="douban_widget">
			<h2>文章分类 · · · · · ·</h2>
			<ul>
			<?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=1'); ?>
			</ul>
		</div>

        <!-- widget 2 -->
		<div class="douban_widget">
			<h2>页面 · · · · · ·</h2>
			<ul>
			<?php wp_list_pages('title_li=&depth=1'); ?>
			</ul>
		</div>

		<!-- widget 3 -->
		<div class="douban_widget">
			<h2>搜索 · · · · · ·</h2>
			<ul>
			<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</ul>
		</div>

		<!-- widget 4 -->
		<div class="douban_widget">
			<h2>月份存档 · · · · · ·</h2>
			<ul>
			<?php wp_get_archives('type=monthly&show_post_count=0'); ?>
			</ul>
		</div>

		<!-- widget 5 -->
		<div class="douban_widget">
			<h2>链接 · · · · · ·</h2>
			<ul>
			<?php get_links('-1', '<li>', '</li>', '<br>', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
			</ul>
		</div>

		<!-- widget 6 -->
		<div class="douban_widget">
			<h2>会员 · · · · · ·</h2>
			<ul>
			<?php wp_register(); ?>
			<?php wp_meta(); ?>
			</ul>
		</div>

<?php endif; ?>
</div>
<div class="clear"></div>

