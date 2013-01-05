<div id="sidebar" class="clearfix">

	<?php // 如果没有使用 Widget 才显示以下内容, 否则会显示 Widget 定义的内容
	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('douban-side-widget') ) :
	?>
			<div class="douban_widget">
				<h2>widgets · · · · · ·</h2>
				<p>请<a href="<?php echo admin_url('widgets.php'); ?>" target="_blank" title="后台 widgets 设置页">点击此处</a>前往管理后台设置 widgets</p>
				<p>其中有一些以 BM 开头的 widgets 是本主题特别提供的！</p>
			</div>

	<?php endif; ?>

</div>
