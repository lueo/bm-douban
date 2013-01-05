<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN" xml:lang="zh-CN">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php 
if(is_category()){
	$description = category_description($cat);	
	$keywords =get_cat_name($cat);
}elseif (is_single()){
	if(post_password_required()){
			$description     = '';
	}else{
		if ($post->post_excerpt) {
			$description     = $post->post_excerpt;
		} else {
			$description = utf8Substr(strip_tags($post->post_content),0,250);
		}
	}
	$keywords = "";
	$tags = wp_get_post_tags($post->ID);
	foreach ($tags as $tag ) {
		$keywords = $keywords . $tag->name . ",";
	}
}
if($description=="")	$description = "曾经喜欢写诗, 曾经喜欢画画, 曾经有过一位好同桌, 现在只有回忆, 虽然还觉得自己是澹澹";
if($keywords=="")	$keywords = "澹澹,澹澹dandan,汕头大学,wordpress,草根播报,人生,记录,回忆,长江新闻与传播学院,兜米,肖建锋,思考,社会,围城,电影,旅行";	
?>
<meta name="keywords" content="<?php echo $keywords;?>" />
<meta name="description" content="<?php echo $description; ?>" />
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
		<?php wp_loginout(); ?>
		<a href="http://bammoo.com/archive">站点地图</a>    
		<a href="http://bammoo.com/message" title="留言">留言</a>
	</div>
    <div class="site-desc"><?php bloginfo('description'); ?></div>
  </div>
</div>

<div id="wrapper">
	<div id="header">

		<h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>

		<div class="nav">

			<div class="nav-items">
				<?php wp_nav_menu( array('theme_location' => 'primary') ); ?>
			</div>

			<div class="nav-search-form">
				<form name="ssform" method="get" action="<?php bloginfo('url'); ?>/index.php">
					<div class="inp">
						<span><input name="s" type="text" title="" size="22" maxlength="60" value="<?php the_search_query(); ?>"></span>
						<span><input class="bn-search" type="submit" value="搜索"></span>
					</div>
				</form>
			</div>

		</div>

</div>