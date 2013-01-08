<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php seo_desc_keywords(); ?>
<meta name="viewport" content="width=device-width" />
<title><?php seo_titles(); ?></title>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="top-nav">
  <div class="bd">
    <nav class="top-nav-items">
        <?php wp_loginout(); ?>
        <a href="<?php bloginfo('rss2_url'); ?>">RSS</a>
        <a href="http://bammoo.com/message" title="留言">留言</a>
    </nav>
    <div class="site-desc"><?php bloginfo('description'); ?></div>
  </div>
</div>

<div id="wrapper">

    <header id="header">

        <hgroup>
            <h1>
                <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
            </h1>
        </hgroup>

        <nav class="nav" role="navigation">

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

        </nav>

    </header>