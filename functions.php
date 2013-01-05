<?php
$surl = get_bloginfo('stylesheet_directory');
define('THEMEURL', $surl);  //  主题包的url地址

// Add support for a variety of post formats
add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

/** l10n */
load_theme_textdomain('bm', get_template_directory() . '/lang');
add_filter( 'show_admin_bar', '__return_false' );

/* 加载  css  */
add_action( 'wp_print_styles', 'bm_front_specific_styles', 1, 100 );
function bm_front_specific_styles(){
    wp_enqueue_style('style', THEMEURL.'/style.css', false,  '0.0.5');
    wp_enqueue_style('style-lightbox', THEMEURL.'/style-lightbox.css', false,  '0.0.8');
}

add_action('wp_enqueue_scripts','bm_init');
function bm_init(){
    ?>
<script type="text/javascript">
/* Try to get out of frames! */
if ( window.top != window.self ) { 
    window.top.location = self.location.href
}
</script>

    <?php
    //load mini jQuery if not in admin (faster CDN).
    //In admin, we need the default, which is in no-conflicts mode
    if( !is_admin()){
        wp_deregister_script('jquery');
        wp_register_script('jquery', THEMEURL.'/js/jquery-1.3.1.min.js','','','1.1');
    }
    //jQuery
    wp_enqueue_script('jquery');
    wp_enqueue_script('app', THEMEURL.'/js/app.js', array('jquery'), '0.5', true);
    wp_enqueue_script('jquery.lightbox', THEMEURL.'/js/jquery.lightbox.js', array('jquery'), '0.5', true);
    wp_enqueue_script('comments-ajax', THEMEURL.'/comments-ajax.js', array('jquery'), '3.9', true);
}

// This theme uses wp_nav_menu() in one location.
if( function_exists('register_nav_menus') ) {
    register_nav_menus( array(
        'primary' => 'Primary Navigation',
    ) );
}
if( function_exists('register_sidebar') ) {
        register_sidebar(array(
                'name' => __( 'douban side widget', 'bm' ),
                'id' => 'douban-side-widget',
                'description' => __( 'Drag widgets from left', 'bm' ),
                "before_widget" => '<div id="%1$s" class="douban_widget widget %2$s clearfix">',
                "after_widget" => '</div>',
                'before_title' => '<h2 class="green">', // 标题的开始标签
                'after_title' => '   · · · · · · </h2>' // 标题的结束标签
        ));
}

function bm_readmore($display=true){
    $echo = ' <a href="'. get_permalink() . '" class="more">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bm' ) . '</a>';
    if($display==false){
        return $echo; 
    }else{
        echo $echo;
    }
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


// pagenavi
if(!function_exists('pagenavi')){
    function pagenavi($range = 9){
        global $paged, $wp_query;
        if ( !$max_page ) {
            $max_page = $wp_query->max_num_pages;
        }
        if($max_page > 1){
            if(!$paged){
                $paged = 1;
            }
            if($paged != 1){
                echo "<a href='" . get_pagenum_link(1) . "' class='extend' title='跳转到首页'>返回首页</a>";
                echo "<a href='" . get_pagenum_link($paged - 1) . "' class='extend' title='跳转到上一页'>上一页</a>";
            }
            if($max_page > $range){
                if($paged < $range){
                    for($i = 1; $i <= ($range + 1); $i++){
                        echo "<a href='" . get_pagenum_link($i) ."'";
                        if($i==$paged)
                            echo " class='current'";
                        echo ">$i</a>";
                    }
                } elseif ($paged >= ($max_page - ceil(($range/2)))) {
                    for($i = $max_page - $range; $i <= $max_page; $i++){
                        echo "<a href='" . get_pagenum_link($i) ."'";
                        if($i==$paged)
                            echo " class='current'";
                        echo ">$i</a>";
                    }
                } elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))) {
                    for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){
                        echo "<a href='" . get_pagenum_link($i) ."'";
                        if($i==$paged) 
                            echo " class='current'";
                        echo ">$i</a>";
                    }
                }
            } else {
                for($i = 1; $i <= $max_page; $i++){
                    echo "<a href='" . get_pagenum_link($i) ."'";
                    if($i==$paged)
                        echo " class='current'";
                    echo ">$i</a>";
                }
            }
            if($paged != $max_page){
                echo "<a href='" . get_pagenum_link($paged + 1) . "' class='extend' title='跳转到下一页'>下一页</a>";
                echo "<a href='" . get_pagenum_link($max_page) . "' class='extend' title='跳转到最后一页'>最后一页</a>";
            }
        }
    }
}

/********************************************************************
built-in seo titles
********************************************************************/
function seo_titles() {
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

// description 和 keywords 的 meta
if(!function_exists('seo_desc_keywords')){
function seo_desc_keywords() {
    global $cat,$post,$ifr;
    if(is_category()){
        $description = category_description($cat);  
        $keywords =get_cat_name($cat);
    }elseif (is_single()){
        if(post_password_required()){
                $description = '';
        }else{
            if ($post->post_excerpt) {
                $description = strip_tags($post->post_excerpt);
            } else {
                $description = utf8Substr(strip_tags($post->post_content),0,250);
                    // 去掉英文引号
                $description = str_replace("'","",$description);
                $description = str_replace('"',"",$description);
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
    if($keywords=="")
        $keywords = $ifr['site_keywords'];  
    if($description=="")
        $description = $ifr['site_description'];
    echo '<meta name="keywords" content="'.$keywords.'" /><meta name="description" content="'.$description.'" />';
}}

if(!function_exists('entry_meta')){
    function entry_meta( $type = '' ) {
        global $post;
        $seprator = '&nbsp;|&nbsp;';
        switch ( $type ){
            case 'index' :
            case 'archive' :
                if(function_exists('ifr_relativetime')){
                    echo ifr_relativetime( get_the_time ('U') );
                }else{
                    the_time('Y-n-j');
                }
                echo $seprator;
                the_author_posts_link(); 
                if(function_exists('the_views')) { 
                    echo $seprator; 
                    the_views(); 
                }
                break;
            case 'special' :
                if(function_exists('the_time')){
                    the_time('Y-n-j');
                }
                break;
            case 'news' :
            case 'weixin' :
                the_time('Y-n-j H:i');
                break;
            case '' :
            default :
                the_time('Y-n-j');
                echo ', ';
                the_time('H:i');
                echo $seprator;
                the_author_posts_link(); 
                break;
        }
        edit_post_link( $seprator . '编辑', ' &nbsp;'); 
    }
}

/********************************************************************
custom comment display
********************************************************************/
function mytheme_comment($comment, $args, $depth) {
    global $post;
$GLOBALS['comment'] = $comment; ?>
<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
    <div class="comment-author">
        <?php echo get_avatar($comment,$size='32',$default=''); ?>
        <h4 class="com-green">
            <CITE><?php comment_author_link(); ?></CITE>
            <SMALL><?php comment_date(); ?>&nbsp;<?php comment_time(); ?><?php edit_comment_link(__('(Edit)'),'  ','') ?></SMALL> 
            <span class="replay-button" style="display:none;"> <?php comment_reply_link(array('depth' => $depth,'max_depth' => '12', 'reply_text' => "[回复]")) ?></span>
        </h4>
    </div>
    <div class="comment-content">
        <?php comment_text(); echo $children; ?>
    </div>
<?php 
}

add_filter('the_content','jquery_lightbox');
function jquery_lightbox($content){ 
    global $post;
    // jquery_lightbox
    $pattern = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)['\"][^\>]*)>/i";
    $replacement = '$1 rel="lightbox['.$post->ID.']">';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}

function bm_relativetime($timestamp){
    $difference = current_time('timestamp') - $timestamp;

    if($difference >= 31536000){        // if more than a year ago 60*60*24*365
        $int = intval($difference / 31536000);
        $r = $int . ' 年前';
    } elseif($difference >= 3024000){  // if more than five weeks ago    60*60*24*30
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
    } elseif($difference >= 3600){         // if more than an hour ago   60*60
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

// 主题附带小挂件
include "theme-widgets.php";
?>