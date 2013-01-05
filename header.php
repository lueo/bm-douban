<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN" xml:lang="zh-CN">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php seo_desc_keywords(); ?>
<title><?php seotitles(); ?></title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
</head>
<body>
<div class="top-nav">
  <div class="bd">
	<div class="top-nav-items">
		<a href="http://localhost/blog/about" title="关于我">关于我</a>
		<a href="http://localhost/blog/message" title="留言">留言</a>
		<a href="http://localhost/blog/miniblog" title="轨迹">轨迹</a>
	</div>
    <div class="site-desc"><?php bloginfo('description'); ?></div>
  </div>
</div>

<div id="wrapper">
	<div id="header">

		<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>


			<div class="nav-search-form">
				<form name="ssform" method="get" action="<?php bloginfo('url'); ?>/index.php">
					<div>
						<span><input name="s" type="text" title="" size="22" maxlength="60" value="<?php the_search_query(); ?>"></span>
						<span><input class="bn-search" type="submit" value="搜索"></span>
					</div>
				</form>
			</div>


</div>