<?php

/********************************************************************
显示作者信息popup
bm_author_replace 过滤文章正文内容中的作者姓名，遇到符合的作者名称存储到 $bm['profile'] 中
bm_get_users 提供级别大于等于作者的用户列表
bm_author_js	在页脚加载popup的js文件，并输出 $bm['profile'] 的内容
********************************************************************/
add_filter('the_content','bm_author_replace','99');
function bm_author_replace($content)
{
	global $bm;
	if(!is_single())
		return $content;

	 $users = bm_get_users();
	 if ($users) {
		 foreach($users as $key=>$user) {
			 $authorname = $user->display_name;
			if(strstr($content,$authorname)){
				$scid = get_usermeta($user->ID, 'scid');
				$desc = utf8Substr(get_usermeta($user->ID, 'description'),0, 18).'...';
				$size=96;
				if($scid!=''){
					$out = 'http://tp3.sinaimg.cn/'.$scid.'/180/1.jpg';
				}else{
					$out = THEMEURL.'/images/nopic.gif';
				}
				$avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
				// some jquery texts should add to footer
				 $bm['profile'].='$(".author'.$user->ID.'").simpletip({
				content: "<div class=\"author-info\">'.$avatar.'<dl><dd>'.$user->display_name.'</dd><dt>'.$desc.'</dt><dt><a href=\"'.$user->user_url.'\" target=\"_blank\" title=\"'.$user->display_name.'的博客\">博客</a>-<a href=\"http://t.sina.com.cn/'.$scid.'\" target=\"_blank\" title=\"'.$user->display_name.'的微博\">微博</a>-<a href=\"'.get_option('home').'/?s='.$user->display_name.'\" target=\"_blank\" title=\"'.$user->display_name.'的所有文章\">文章</a></dt></dl></div>",
			   fixed: true,
			   position: \'top\'
			});';
			 $authorpopup  = "<span class='profile author".$user->ID."'>$authorname</span>";
			$content = str_replace($authorname,$authorpopup,$content);
			}
		 }
	 } 
    return $content; 
}
function  bm_get_users() {
	global $wpdb;
	$users = $wpdb->get_results( "SELECT user_id, user_id AS ID, user_login, display_name,user_url, user_email, meta_value FROM $wpdb->users, $wpdb->usermeta WHERE {$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND meta_key = '{$wpdb->prefix}user_level' AND meta_value >=1  ORDER BY {$wpdb->usermeta}.user_id" );
	return $users;
}
function  bm_author_js() { 
	global $bm;
	if(is_singular()){
		if($bm['profile']!=""):?> 
			<script type="text/javascript" src="<?php echo THEMEURL; ?>/js/jquery.simpletip-1.3.1.pack.js"></script> 
			<script type="text/javascript">
			$(document).ready(function() {
				<?php echo $bm['profile'];	 ?>
			});
		</script>
<?php	endif; 
	}
}
add_action('wp_footer','bm_author_js','1000');
?>