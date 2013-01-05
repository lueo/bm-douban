<?php
add_action('init','bm_init');
add_filter('the_content','jquery_lightbox',0);
add_filter( 'posts_orderby', 'sort_query_by_post_in', 10, 2 );	//自定义文章排序

function bm_init(){	 
	
	//load mini jQuery if not in admin (faster CDN). 
	//In admin, we need the default, which is in no-conflicts mode
	if( !is_admin()){
	   wp_deregister_script('jquery'); 
		wp_register_script('jquery', BMURL.'/js/jquery-1.4.2.min.js');
	}
	
 	//jQuery
    wp_enqueue_script('jquery');  
}
  /************************ custom logos ***********************/
add_action('admin_head', 'custom_logo');
add_action('login_head', 'custom_login_logo');
function custom_logo() {
  echo '<style type="text/css">
    #wphead { background: url('.BMURL.'/images/admin-logo-head.png) no-repeat center bottom  !important; }
    #header-logo { background-image: url('.BMURL.'/images/admin-logo.png) !important; }
    </style>';
}
 
function custom_login_logo() {
  echo '<style type="text/css">
    h1 a { background-image:url('.BMURL.'/images/admin-login-logo.png) !important; }
    </style>';
}
	// 流量统计
function bm_sitecounter(){
	global $bm;
	if($bm['site_counter']!=''){?>
<script src="<?php echo $bm['site_counter']; ?>" language="JavaScript"></script>
<?php
	}
}

function bm_thumbnail($args = ''){
	if(function_exists('the_thumb')) {
		$pos = strpos($args, 'display=false');
		$output =	the_thumb($args);
		if ($pos === false) {
			echo $output;
		} else {
			return $output; // 只返回不显示 
		}
	}
}

function bm_postinfo($fix=''){
	global $post;
	echo __('in text','bm').'<b>';
	the_category(', ');
	echo '</b>'.$fix;
	echo get_the_date().$fix;
	comments_popup_link(__('No Comments','bm'), __('1 Comment','bm'),  __('% Comments','bm'));
	edit_post_link($fix.__('Edit','bm'));
}

function bm_readmore($display=true){
	$echo = ' <a href="'. get_permalink() . '" class="more">' . __( 'Continue reading&nbsp;&raquo;&nbsp;', 'bm' ) . '</a>';
	if($display==false){
		return $echo; 
	}else{
		echo $echo;
	}
}

function bm_breadcrumbs(){
	if(is_home()){ 
	}else{ ?>
<div id="breadcrumbs">
<?php _e('You are here: ','bm'); ?><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php _e('home ','bm'); ?></a>&nbsp;&raquo;&nbsp;
<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. 

		if (is_category()) { 	/* If this is a category archive */
			single_cat_title(); 

		} elseif( is_tag() ) { 	/* If this is a tag archive */ 
			_e('posts tagged','bm');echo ": &nbsp;&nbsp;";single_tag_title();

		} elseif (is_day()) {  /* If this is a daily archive */ 
			the_time('F jS, Y'); 

		 } elseif (is_month()) { 	/* If this is a monthly archive */ 
			the_time('F, Y'); 

		} elseif (is_year()) { 	/* If this is a yearly archive */
			the_time('Y'); 

		} elseif (is_author()) { 	/* If this is an author archive */
			print AUTHOR_ARCHIVE; 

		} elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { 	/* If this is a paged archive */
			 print BLOG_ARCHIVES; 

		}elseif(is_single()){ 
			 the_category(', ');  echo '&nbsp;&raquo;&nbsp;';  the_title('');  

		}elseif(is_page()){
			 the_title('');  

		}elseif(is_search()){
			printf( __( 'Search Results for: %s', 'bm' ), '<span>' . get_search_query() . '</span>' );

		}else{ 
			_e('404','bm'); 

		} 	?>
	</div><!-- breadcrumbs -->
<?php 	}
}
function bm_relativetime($timestamp){
	$difference = current_time('timestamp') - $timestamp;

	if($difference >= 31536000){        // if more than a year ago 60*60*24*365
		$int = intval($difference / 31536000);
		$r = $int . ' 年前';
	} elseif($difference >= 3024000){  // if more than five weeks ago	 60*60*24*30
		$int = intval($difference / 3024000);
		$r = $int . ' 月前';
	} elseif($difference >= 604800){        // if more than a week ago 60*60*24*7
		$int = intval($difference / 604800);
		$r = $int . ' 周前';
	} elseif($difference >= 86400){      // if more than a day ago 60*60*24
		$int = intval($difference / 86400);
		if ($int == 1) {
			$r = '昨天';
		} elseif($int == 2) {
			$r = '前天';
		}else{
			$r = $int . ' 天前';
		}
	} elseif($difference >= 3600){         // if more than an hour ago	 60*60
		$int = intval($difference / 3600);
		$r = $int . ' 小时前';
	} elseif($difference >= 60){            // if more than a minute ago
		$int = intval($difference / 60);
		$r = $int . ' 分钟前';
	} else {                                // if less than a minute ago
		$r = '刚刚';
	}

	return $r;
}

/********************************************************************
截取Utf-8字符串
********************************************************************/
function utf8Substr($str, $from, $len,$fix=false){
    $substr = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);
	if($fix==true&&strlen($str)>($len-$from)){
		return $substr.'...';
	}else{
		return $substr;
	}
}

/********************************************************************
pagenavi
********************************************************************/
function pagenavi($range = 9){
	global $paged, $wp_query;
	if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
	if($max_page > 1){if(!$paged){$paged = 1;}
	if($paged != 1){echo "<a href='" . get_pagenum_link(1) . "' class='extend' title='跳转到首页'> 返回首页 </a>";}
	previous_posts_link(' 上一页 ');
    if($max_page > $range){
		if($paged < $range){for($i = 1; $i <= ($range + 1); $i++){echo "<a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='current'";echo ">$i</a>";}}
    elseif($paged >= ($max_page - ceil(($range/2)))){
		for($i = $max_page - $range; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
		if($i==$paged)echo " class='current'";echo ">$i</a>";}}
	elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
		for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){echo "<a href='" . get_pagenum_link($i) ."'";if($i==$paged) echo " class='current'";echo ">$i</a>";}}}
    else{for($i = 1; $i <= $max_page; $i++){echo "<a href='" . get_pagenum_link($i) ."'";
    if($i==$paged)echo " class='current'";echo ">$i</a>";}}
	next_posts_link(' 下一页 ');
    if($paged != $max_page){echo "<a href='" . get_pagenum_link($max_page) . "' class='extend' title='跳转到最后一页'> 最后一页 </a>";}}
}

/********************************************************************
seo functions in header.php
********************************************************************/
function seo_desc_keywords() {
	global $cat,$post,$bm;
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
			$keywords = "";
			$tags = wp_get_post_tags($post->ID);
			foreach ($tags as $tag ) {
				$keywords = $keywords . $tag->name . ",";
			}
		}
	}else{
	$keywords = "";
	$description="";
	}
	if($keywords=="")	$keywords = $bm['site_keywords'];	
	if($description=="")	$description = $bm['site_description'];
	echo '<meta name="keywords" content="'.$keywords.'" />
	<meta name="description" content="'.$description.'" />';
}

function seotitles() {
	if (is_tag()) {
	$output = wp_title('Posts tagged: ', true).' - '.get_bloginfo('name');
	} elseif (is_search()) {
	$output = 'Search results for: ';
	the_search_query();
	$output = ' - '.get_bloginfo('name');
	} elseif (is_404()) {
	$output = 'Page not found!';
	} elseif (is_home()) {
	$output = get_bloginfo('name').' - '.get_bloginfo('description');
	} else {
	$output = wp_title('', true).' - '.get_bloginfo('name');
	}
	echo $output;
}

function get_timespan_most_viewed($mode = '', $limit = 10, $days = 7, $display = true) {
	global $wpdb, $post;
	$limit_date = current_time('timestamp') - ($days*86400);
	$limit_date = date("Y-m-d H:i:s",$limit_date);
	$where = '';
	$temp = '';
	if(!empty($mode) && $mode != 'both') {
		$where = "post_type = '$mode'";
	} else {
		$where = '1=1';
	}
	$most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_date < '".current_time('mysql')."' AND post_date > '".$limit_date."' AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER  BY views DESC LIMIT $limit");
	if($most_viewed) {
		foreach ($most_viewed as $post) {
			$post_title = get_the_title();
			$post_views = intval($post->views);
			$post_views = number_format($post_views);
			$temp .= "<li><a href=\"".get_permalink()."\">$post_title</a></li>";
		}
	} else {
		$temp = '<li>'.__('N/A', 'wp-postviews').'</li>'."\n";
	}
	if($display) {
		echo $temp;
	} else {
		return $temp;
	}
}


/********************************************************************
根据数组排序
********************************************************************/
function sort_query_by_post_in( $sortby, $thequery ) {
	if ( isset($thequery->query['post__in']) && !empty($thequery->query['post__in']) && isset($thequery->query['orderby']) && $thequery->query['orderby'] == 'post__in' )
		$sortby = "find_in_set(ID, '" . implode( ',', $thequery->query['post__in'] ) . "')";
	return $sortby;
}

	// subjects
function get_subject(){
	global $post,$bm;
	if (in_category('广州亚运会')):
        $subject = '<div class="the_subject"><center><br><font color="red"><b>>>更多精彩，请点击进入：</b></font><b><a href="?category_name=广州亚运会" target="_blank"><font color="blue"><u>广州亚运会专题</u></font></a></b></center></div><br><br><br>';
	elseif (in_category('重走西北角')):
        $subject = '<div class="the_subject"><center><br><font color="red"><b>>>更多精彩，请点击进入：</b></font><b><a href="?category_name=northwest" target="_blank"><font color="blue"><u>重走西北角专题</u></font></a></b></center></div><br><br><br>';
	elseif (in_category('世博会')):
        $subject = '<div class="the_subject"><center><br><font color="red"><b>>>更多精彩，请点击进入：</b></font><b><a href="?category_name=expo" target="_blank"><font color="blue"><u>世博会专题</u></font></a></b></center></div><br><br><br>';
	elseif (in_category('汕头交通专题')):
        $subject = '<div class="the_subject"><center><br><font color="red"><b>>>更多精彩，请点击进入：</b></font><b><a href="?category_name=汕头交通专题" target="_blank"><font color="blue"><u>汕头交通专题</u></font></a></b></center></div><br><br><br>';
    else :
        $subject = '';
    endif;
	return $subject;
}

function jquery_lightbox($content){
	global $post;
	// jquery_lightbox
	$pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)['\"][^\>]*)>/i";
	$replacement = '$1 rel="lightbox['.$post->ID.']">';
	$content=do_shortcode($content);
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}
function get_this_tags(){
	global $post;
	// tags
	$this_tags = get_the_tag_list( '', ', ' );
    if( $this_tags != "") :
        $this_tags = '<span class="link_pages">本文标签：'.$this_tags.'</span>';
    else :
        $this_tags = '';
    endif;
	return $this_tags;
}

	// share 
function get_share_code(){
	global $post;
	return '<div id="share">分享到：<a href="javascript:void(0)" onclick="share(\'sina\');"  rel="nofollow" id="sina-share" title="新浪微博">新浪微博</a><a href="javascript:void(0)" onclick="share(\'renren\');" rel="nofollow" id="renren-share" title="人人网">人人网</a><a href="javascript:void(0)" onclick="share(\'qzone\');" rel="nofollow" id="qzone-share" title="QQ空间">QQ空间</a><a href="javascript:void(0)" onclick="share(\'tencent\');" rel="nofollow" id="tencent-share" title="腾讯微博">腾讯微博</a><a href="javascript:void(0)" onclick="share(\'douban\');" rel="nofollow" id="douban-share" title="豆瓣">豆瓣</a><a href="javascript:void(0)" onclick="share(\'wy\');" rel="nofollow" id="netease-share" title="网易微博">网易微博</a></div><div class="clear"></div>'; 
}


/********************************************************************
多媒体内容抓取
update_video_meta	自动在发布或更新文章时抓取视频放入post_meta中
********************************************************************/
add_action('publish_post', 'update_video_meta', 0);
add_action('save_post', 'update_video_meta', 0);
function update_video_meta($post_ID){
	$meta =	get_post_meta($post_ID, 'video', true);
	$video_post = get_post($post_ID);
	$video_post_content = $video_post->post_content;
	$flashvar = get_video_var($video_post_content);
	if( $flashvar!=""){
		if( $meta =="" ){
			add_post_meta($post_ID, 'video', $flashvar, true);	// 不存在时添加自定义字段，存在时更新。   
		}else {
			update_post_meta($post_ID, 'video', $flashvar);
		}
	}
}

function get_video_var($content) {
	$pattern = "/<param name=\"src\" value=\"(.+?)\".*?>/"; 
	preg_match_all($pattern,$content,$matches);
	if(!empty($matches[1][0])) {
		$flashvar = $matches[1][0];
	}
	return $flashvar;
}
function has_video(){
	global $post;
	$video = get_post_meta($post->ID, 'video', true);
	if($video != "") return true;
	else return false;
}


add_filter("get_avatar", "bm_get_avatar",10,4);
function bm_get_avatar($avatar, $id_or_email='',$size='32') {
	if (is_object($id_or_email)){
		$user_id = $id_or_email->user_id;
	}elseif (is_string($id_or_email)){
		require_once(ABSPATH . WPINC . '/ms-functions.php');
		$user_id = get_user_id_from_string( $id_or_email );
	}else{
		global $comment;
		if(is_object($comment)) {
			$user_id = $comment->user_id;
		}
	}
	$scid = get_user_meta($user_id, 'scid',true);
	$qcimg = get_user_meta($user_id, 'qcimg',true);
	if($scid!=''){
		$out = 'http://tp3.sinaimg.cn/'.$scid.'/50/1.jpg';
		$avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
		return $avatar;
	}else {
		if($qcimg !=''){
			$out = $qcimg.'/100';
			$avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
			return $avatar;
		}else{
			return $avatar;
		}
	}
}


// used in search.php & archive.php etc.
function loop_items(){
	while (have_posts()) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="pose item">

			<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<div class="clear"></div>
			<div class="entry">
			<div class="post-thumb"><?php bm_thumbnail('num=1&width=200&height=140'); ?></div>
			<p><?php print utf8Substr(get_the_excerpt(),0, 150); ?><?php bm_readmore(); ?></p>
			<div class="clear"></div>
			
			</div>

			</div>
			<div class="clear"></div>
<?php 
	endwhile;
}


/********************************************************************
custom comment display
********************************************************************/
function mytheme_comment($comment, $args, $depth) {
	global $post;
$GLOBALS['comment'] = $comment; ?>
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
		<?php comment_text();  ?>
	</div>
<?php 
}

function bm_comment_form( $args = array()) {
	global $user_identity,$post;
	$defaults = array(
		'title_reply'          => __( 'Leave a Reply' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
	);

	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );
	
	if ('open' == $post->comment_status){ ?>
	<div id="respond">

		<h3><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?>→</h3>

		<div class="cancel-comment-reply">
			<small><?php cancel_comment_reply_link(); ?></small>
		</div><!-- cancel-comment-reply -->

		<?php if ( get_option('comment_registration') && !$user_ID ){ ?>

			<p><?php _e('you must be','bm'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in','bm'); ?></a> <?php _e('to post comment','bm'); ?>.</p>

		<?php }else{ ?>

			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

				<?php if ( is_user_logged_in() ){ ?>

					<p><?php _e('logged as','bm'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account"><?php _e('log out','bm'); ?> &raquo;</a></p>

				<?php }else{ ?>

					<p>
					<label><small>“姓名”和“邮箱<?php _e('(wont publish)','bm'); ?>”是必填项目</small></label></p>

					<p><input class="text" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
					<label for="author"><small><?php  _e('name','bm'); ?></small></label></p>

					<p><input class="text" type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
					<label for="email"><small><?php _e('(mail)','bm'); ?></small></label></p>

					<p><input class="text" type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
					<label for="url"><small><?php _e( 'website', 'bm' ); ?></small></label></p>

				<?php } //user_ID  ?>

				<?php do_action('comment_form', $post->ID); ?>

				<p><textarea name="comment" id="comment" class="resizable" tabindex="4"></textarea></p>

				<p><input class="input_submit" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e( 'submit', 'bm' ); ?>" />
				<?php comment_id_fields(); ?>
				</p>

			</form>

		<?php } // comment_registration ?>
	</div>
	<?php } // comment_status

}
?>