<?php

require ("bm-thumb-config.php");
require ("bm-thumb-meta.php");
require ("bm-thumb-core.php");
require ("bm-thumb-options.php");
require ("bm-thumb-clean.php");

function the_thumb ($args = ''){
	// 如果调用时没有指定参数，则采用后台设置的参数; 否则采用函数自身的默认参数。
	$wp_thumbnails_options = get_option('bee_thumb_options');
	
	//只允许传递参数：宽、高、位置、裁剪
	if(!empty($args)) {
		$default_args = array();	  
	  $default_args['width'] 		= $wp_thumbnails_options['width_of_home_images'];
	  $default_args['height']		= $wp_thumbnails_options['height_of_home_images'];
	  $default_args['crop'] 			= $wp_thumbnails_options['crop_home_images'];
	  $default_args['class'] 			= '';
	  $default_args['gbk'] 			= false;
	  $default_args['link'] 			= false;
		$args = wp_parse_args($args, $default_args);//合并参数，若 args 中无某参数，则以默认参数 default_args 代替
				
		$wp_thumbnails_options['width_of_home_images'] 	= $args['width'];
	  $wp_thumbnails_options['height_of_home_images'] = $args['height'];
	  $wp_thumbnails_options['crop_home_images'] 			= $args['crop'];
	}
	
	$thumbsize 				= $wp_thumbnails_options['width_of_home_images'];
	$height 					= $wp_thumbnails_options['height_of_home_images'];
	$target 					= $wp_thumbnails_options['homepage_new_window'];
	$defaultimage 		= $wp_thumbnails_options['homepage_default_image'];
	$crop 						= $wp_thumbnails_options['crop_home_images'];
	$disable_external = $wp_thumbnails_options['disable_external'];
	$media 						= $wp_thumbnails_options['media_page'];
	$video_thumb 			= $wp_thumbnails_options['video_thumb'];
	$video_link_target= $wp_thumbnails_options['video_link_target'];
	
	$crop = true;
			
	global $thumbDir, $downloadDir, $siteurl, $rooturl, $destpath, $downloadpath; //申明全局变量
		
	//获取年、月
	global $wpdb,$post; // 访问数据库
	$post_id = $post->ID;
	$now = current_time('mysql', 1);
	$the_post_date = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year 
		FROM $wpdb->posts 
		WHERE ID = $post_id
		AND post_status = 'publish' 
		AND post_date_gmt < '$now' 
		LIMIT 1");
	$year = $the_post_date->year;
	$month = $the_post_date->month;
	
	//---------- 从自定义域中获取图片地址
	$thumbnail = get_post_meta($post_id, "ta-thumbnail", true);
	if ($thumbnail == ''  or strstr($thumbnail,'NoPicturesFound')) { //这篇日志还没有扫描过
		update_bee_thumb_meta($post); // 自动填充自定义域
		$thumbnail = get_post_meta($post_id, "ta-thumbnail", true);
	}
	if ($thumbnail == '') { return; }
	if ($thumbnail == 'NoMediaFound') { //无图片
		if ($defaultimage == "") return;
		$thumbnail = $defaultimage;
	}else {	
		$array = explode(";", $thumbnail);
		$iNumberOfPics = count($array)-1; // 图片数量 
		$thumbnail = $array[0];
	}		
	$original = $thumbnail; //原始大图
	if(!strstr($thumbnail,'ta_video_')){ //普通图片(非视频)
		if (!strstr($thumbnail,$siteurl)) { // 图片
			if ($disable_external == "true") return;  //禁止显示外链图片
		}elseif (strstr($thumbnail,$siteurl) or ($disable_external == "false")) {//本地图片或允许外链图片
			$original = $thumbnail;
			$filename = $post_id."-1";
			$thumbnail = bee_is_thumb_exist($filename, $thumbsize, $height, $crop, $year, $month);

			if ($thumbnail == false) { //如果图片不存在
				$thumbnail = $original;
				$thumbnail = str_replace($siteurl,$rooturl,$thumbnail);//转换为绝对路径	
				
				//将图片按照规格存放在不同子目录
				if ($crop == true) $subfolder = "{$thumbsize}x{$height}-c/";
				else $subfolder = "{$thumbsize}x{$height}-u/";
				$subfolder = $destpath.$subfolder;
				
				if(!file_exists($subfolder)) { 
				if(!(@mkdir($subfolder,0755))) { 
					if(is_admin()) {
						echo "提示：很抱歉，无法创建缩略图子目录，请手动创建目录".$subfolder."，权限设置为755。<br>"; 
						return; 
						}
					} 
				}
				
				$subfolder .= "$year/";
				if(!file_exists($subfolder)) { 
					if(!(@mkdir($subfolder,0755))) { 
						if(is_admin()) {
							echo "提示：很抱歉，无法创建缩略图子目录，请手动创建目录".$subfolder."，权限设置为755。<br>"; 
							return; 
						}
					} 
				}
				
				$subfolder .= "$month/";
				if(!file_exists($subfolder)) { 
					if(!(@mkdir($subfolder,0755))) { 
						if(is_admin()) {
							echo "提示：很抱歉，无法创建缩略图子目录，请手动创建目录".$subfolder."，权限设置为755。<br>"; 
							return; 
						}
					} 
				}
			
				$thumbnail = bee_image_resize($thumbnail, $thumbsize, $height, $crop, null, $subfolder, 90, $filename); //制作缩略图
				$thumbnail = bee_is_thumb_exist($filename, $thumbsize, $height, $crop, $year, $month);

				if ($thumbnail == false)  { $thumbnail =  $original;} //如果缩略图尺寸过大，只好显示原图
			}
		}
	}elseif($media!="image"){	// 符合视频缩略图
		
		$result = bee_getvideo($thumbnail); 
		$thumbnail = $result['imageurl'];

		if($video_link_target=="video") { //点击打开视频
			$permalink = $result['video'];
		}
		if($video_thumb=="video") { //直接显示视频
			$video = $result['video'];
			$thumbnail_video = "<embed src=\"$video\" quality=\"high\" width=\"$thumbsize\" height=\"$height\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\"></embed>";
			$thumbnail = "";
		}
	}else {
		return;
	}

	$title=$post->post_title;
	if($permalink=="image") {
		$permalink = $original;
	}else {
		$permalink = get_permalink($post_id);
	}
	if($args['class']!=""){
		$addclass = ' class="'.$args['class'].'"';
	}else{
		$addclass = '';
	}
	if($thumbnail) {
		if($args['gbk']&&function_exists('utf_to_gbk')){
			$title=utf_to_gbk($title);
			$thumbnail=utf_to_gbk($thumbnail);
		}
		$outputimg='<img alt="'.$title.'" '.$addclass.' width="'.$thumbsize.'" src="'.$thumbnail.'"  />';
		if(!$args['link']){
			// 不加入文章链接
			$output=$outputimg;
		}else{
			$output="<a href=\"$permalink\"  rel=\"nofollow\"  title=\"$title\" target=\"$target\">".$outputimg."</a>";
		}
	}else { //视频缩略图
		$output=$thumbnail_video;
	}
	return $output; // 只返回不显示 
}



?>