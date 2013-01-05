<?php
$surl = get_bloginfo('stylesheet_directory');
define('THEMEURL', $surl);	//	主题包的url地址
define('BMURL', $surl.'/bm');	//	bm文件夹的url地址
define('BMPATH', TEMPLATEPATH . '/bm');	//	文件路径
define('SHORTNAME',  "bm");

if(!is_admin())
	add_filter( "show_admin_bar", "__return_false" );

	/** l10n */
$locale=get_locale();
if ( empty($locale) )
	$locale = 'zh_CN';
load_textdomain("bm", BMPATH . "/lang/$locale.mo");

/*
全局变量，所有的选项调用通过此变量
除了此处使用global，其他的函数中也要global一次
sidebar.php 也要global此变量
*/
$bm = get_option(SHORTNAME);
global $bm;


// 生成 functions_custom 文件
$custom_file = TEMPLATEPATH.'/functions_custom.php';

if (!is_file($custom_file)) {
	$sample_settings = BMPATH."/bm-settings-default.php";

	$result = copy($sample_settings, $custom_file);
	if ($result == false) {
		echo '复制ok';
	}
}
// 载入 functions_custom 文件
include  $custom_file;
?>