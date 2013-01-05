<?php

/*
* 自动维护自定义域
*/
//if (is_admin())	return;//后台不要调用
//if (!is_single())	return;//只在single页调用

### Function: Add Custom Fields

//add_action('publish_post', 'update_bee_thumb_meta_action');
//add_action('save_post', 'update_bee_thumb_meta_action'); 
add_action('wp_head', 'update_bee_thumb_meta');

		
### Function: Delete Custom Field
add_action('delete_post', 'delete_bee_thumb_fields');
function delete_bee_thumb_fields($post_ID) {
	delete_post_meta($post_ID, 'ta-thumbnail');
	//以及删除该文章的缩略图
}

//function update_bee_thumb_meta_action() {
	//update_bee_thumb_meta("", true);
//}

function update_bee_thumb_meta($post="", $from_action=false) // 自动填充自定义域
{
	$bee_thumb_options = get_bee_thumb_options();
	$auto_replace			= $bee_thumb_options['auto_replace']; //是否替换
	$auto_replace_exception	= $bee_thumb_options['auto_replace_exception']; //排除
	$limit 						= $bee_thumb_options['limit'];
	
	if($auto_replace=="true") {
		if(!is_single() && !is_page())	return; //确保只在single页或page页执行，以免页面卡住打不开。
		$limit = 20; //无穷大，确保所有图片都被本地化
	}
	
	//if ($from_action===true &&(!is_admin() || $bee_thumb_options['auto_recheck']=='false')) {
		//return;
	//}
	
	if($post=="") {
		global $post;
	}
	global $siteurl;

	if ($post->post_type == 'revision' || $post->post_status != 'publish') { 
		return;//不能对修订版进行更新，否则造成数据库重复条目;也不对未发表日志进行更新
	}
  
  $postid = $post->ID; 
  
	add_post_meta($postid, 'ta-thumbnail', '', true);	//  ta-thumbnail不存在时添加自定义字段，存在时不做处理。    
	$thumbnail = get_post_meta($postid, 'ta-thumbnail', true);
	
	/****************************************************/
	
			
	if ($thumbnail == '' or strstr($thumbnail,'NoPicturesFound')) {//若字段尚未填充，升级为NoMediaFound
		
		//获取年、月
		global $wpdb;
		$now = current_time('mysql', 1);
		$the_post_date = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year 
			FROM $wpdb->posts 
			WHERE ID = $postid
			AND post_status = 'publish' 
			AND post_date_gmt < '$now' 
			LIMIT 1");
		$year = $the_post_date->year;
		$month = $the_post_date->month;
		
		$disable_external = $bee_thumb_options['disable_external'];
	  $replace					= "false"; //是否需要替换
  
  	/************* begin of 处理缩略图 ****************/
		$imageContent = $post->post_content;		
		$imageContent = do_shortcode($imageContent);	// 替换短码
		// 正则表达式，可能还存在漏判的问题。
		$imagePattern = '~<img [^\>]*\ />~';//匹配一般
		preg_match($imagePattern,$imageContent,$aPics); 
		$iNumberOfPics = count($aPics); // 检查一下至少有一张图片 
		if ($iNumberOfPics < 1) { //另一种方式扫描
			preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|jpeg|png|JPG|GIF|BMP|JPEG))\"?.+>/i',$imageContent,$aPics);
			$iNumberOfPics = count($aPics); // 再次检查一下至少有一张图片 
		}
		$thumbnail = $aPics[0];
		$count = 1;
		if ( $thumbnail !="" ) { 
			
			if($auto_replace=="true") { //普通图片	
				//提取url地址，填充到自定义域。仅仅支持以下几种后缀图片格式（支持大写）。
				$len0 = strpos($thumbnail, 'src=');
				$len1 = strpos($thumbnail, 'http', $len0);
				if ($len1 === false) continue;
				$len2 = stripos($thumbnail, '.jpeg', $len1);
				if ($len2 === false) {
					$len2 = stripos($thumbnail, '.jpg', $len1);
					if ($len2 === false) {
						$len2 = stripos($thumbnail, '.png', $len1);
						if ($len2 === false) {
							$len2 = stripos($thumbnail, '.gif', $len1);
							if ($len2 === false) {
								continue;
							}
						}
					}
					$thumbnail=substr($thumbnail,$len1,$len2-$len1+4);
				}
				$original = $thumbnail;
			}else{ //普通图片	
				//提取url地址，填充到自定义域。仅仅支持以下几种后缀图片格式（支持大写）。
				$len0 = strpos($thumbnail, 'src=');
				//增加对无http前缀的支持
				if(strstr($thumbnail, "src=\"/")) {
					$thumbnail = str_replace("src=\"/","src=\"".$siteurl."/", $thumbnail); //加上http头
				}
				$len1 = strpos($thumbnail, 'http', $len0);
				if ($len1 === false) continue;
				$len2 = stripos($thumbnail, '.jpeg', $len1);
				if ($len2 === false) {
					$len2 = stripos($thumbnail, '.jpg', $len1);
					if ($len2 === false) {
						$len2 = stripos($thumbnail, '.bmp', $len1);
						if ($len2 === false) {
							$len2 = stripos($thumbnail, '.png', $len1);
							if ($len2 === false) {
								$len2 = stripos($thumbnail, '.gif', $len1);
								if ($len2 === false) {
									continue;
								}
							}
						}
					}
					$thumbnail=substr($thumbnail,$len1,$len2-$len1+4);
				}
				else $thumbnail=substr($thumbnail,$len1,$len2-$len1+5);
			}

			if( strstr($thumbnail,$siteurl.'wp-content/gallery/')) //如果是 gallery 图集中的缩略图，则替换成原图
					$thumbnail = str_replace("thumbs/thumbs_","", $thumbnail); 

			if (($disable_external == "true") and (!strstr($thumbnail,$siteurl))){ //禁止显示外链图片
				return;
			}else { //制作本地缩略图（包括下载远程图片）,返回缩略图地址
				$thumbnailbackup = $thumbnail;
				$thumbnail = bee_create_thumb($thumbnail, $postid."-".$count, $year, $month);//传递文章ID，图片序号
			}

			if ($thumbnail == false) {//各种原因失败了，忽略此图
				//什么也不做
			}else {
				
				//replace
				if ($original!="" && $auto_replace=="true" && !strstr($original, $siteurl)) { //替换外链图片
					$exceptions = explode(";", $auto_replace_exception);
					$flag="true";
					foreach($exceptions as $exception) { //排除
						//echo "exclude: ".$exception."<br>";
						if($exception=="") break;
						if(strstr($original, $exception)) { $flag="false"; break;}
					}
						
					if($flag=="true") {
						$imageContent = str_replace($original,$thumbnail,$imageContent);//将原始图片替换为本地图片
						$replace = "true";
					}
				}
				
				
				$ta_thumbnail = $ta_thumbnail.$thumbnail.";";//以分号作为分隔符
				if($count==1){  //一旦提取到第一张图片，立刻填充，防止图片过多超时
					update_post_meta($postid, 'ta-thumbnail', $ta_thumbnail);
				}
				$count = $count + 1;
			}
			

		}
		/*** end of 处理缩略图 ***/
		
		
		
		if ($ta_thumbnail == "") {
			$ta_thumbnail = "NoMediaFound";// 没找到图片，标记为NoMediaFound
		}

		update_post_meta($postid, 'ta-thumbnail', $ta_thumbnail); //填充到自定义域
		
		//远程图片本地化
		if ($auto_replace=="true" && $replace == "true") {
			$my_post = array();
			$my_post['ID'] = $postid;
			$my_post['post_content'] = $imageContent;
			// Update the post into the database
			wp_update_post( $my_post );
		}
		
		return $ta_thumbnail;
	}
}


//感谢Cgeek，http://www.cgeek.org/?p=379
//依次检查优酷、酷六、土豆，发现一个即返回
function bee_get_vedio_thumbs($content) {
	
	$head = "ta_video_";
	$result = "";
	
	if(strstr($content,'56.com')) {
		preg_match_all("/http:\/\/player\.56\.com\/(\w+)[\=|\/v.swf]/", $content, $matches);
		if(!empty($matches[1][0])) {
			$flashvar = $matches[1][0];
		}
		if($flashvar) {
		    $link = 'http://www.56.com/u43/'.$flashvar.'.html';
			$text=@file_get_contents($link);
			if($text) {
				preg_match("/\"img\"\:\"http\:(.*)\.jpg/",$text,$match);
				$img=$match[0];
				$imageurl = stripslashes(str_replace('"img":"',"", $img)); 
			}
		}
	}
	
	if($flashvar && $imageurl) {
		$result = $head."56|".$flashvar."|".$imageurl.";";
		return $result;
	}
	
	
	if(strstr($content,'youku.com')) { //图像大小：128*96
		//优酷视频地址，如http://player.youku.com/player.php/sid/XMTAxNjk4OTMy/v.swf
		//或者网页地址：http://v.youku.com/v_show/id_XMTAxNjk4OTMy.html
		//要注意一点：优酷网上的视频地址较早些的是：http://v.youku.com/v_show/id_XMTAxNjk4OTMy=.html
		//分析视频网址，获取视频编码号
		preg_match_all("/http:\/\/v\.youku\.com\/v\_show\/id\_(\w+)[\=|.html]/", $content, $matches);
		//preg_match_all("/id\_(\w+)[\=|.html]/", $content, $matches);
		if(!empty($matches[1][0])) {
			$flashvar = $matches[1][0];
		}
		else {
			preg_match_all("/http:\/\/player\.youku\.com\/player\.php\/sid\/(\w+)[\=|\/v.swf]/", $content, $matches);
			if(!empty($matches[1][0])) {
				$flashvar = $matches[1][0];
			}
		}
		
		if($flashvar) {
		    //获取视频页面内容，存于$text中
		    $link = 'http://v.youku.com/v_show/id_'.$flashvar.'.html';
		    
				$text=@file_get_contents($link);
				if($text) {
			    //获取视频标题
					//preg_match("/<title>(.*?) - (.*)<\/title>/",  $text, $title);
			    
			    //获取优酷网上某一视频对应的视频截图，经分析，视频的截图的图片地址在该视频页面html代码里以
			    //<li class="download"></li>标记里的最后一个http://vimg....
					//例如http:vimg20.yoqoo.com/0100641F4649B9D27344B00131FBB6AFDF5175-7D35-930B-E43C-99C59F918E00
					
					preg_match_all("/<li class=\"download\"(.*)<\/li>/",$text,$match2);
					$match2[1][0] = str_replace("http://v.youku.com", "", $match2[1][0]); //里面只有两个链接，去掉这个，只剩下图片链接
					preg_match("/http:\/\/(.*)\|\"\>/",$match2[1][0],$imageurl);
					if (!empty($imageurl[1])) {
						$imageurl = 'http://'.$imageurl[1];
					}
				}
			}
	}
	
	if($flashvar && $imageurl) {
		$result = $head."youku|".$flashvar."|".$imageurl.";";
		return $result;
	}
	
	if(strstr($content,'ku6.com')) { //图像大小：132*99
		// http://v.ku6.com/show/sPysxoPI8pe51o5c.html
		// http://player.ku6.com/refer/sPysxoPI8pe51o5c/v.swf
    //对于酷6网，末尾以index_开头的地址需要另外分析其视频编码，本插件不支持
    
    preg_match_all("/http:\/\/v\.ku6\.com\/show\/([\w\-]+)\.html/", $content, $matches);
    if(!empty($matches[1][0])) {
			$flashvar = $matches[1][0];
		}
		else {
			preg_match_all("/http:\/\/player\.ku6\.com\/refer\/([\w\-]+)\/v.swf/", $content, $matches);
			if(!empty($matches[1][0])) {
				$flashvar = $matches[1][0];
			}
		}
	
		if($flashvar) {
	    //获取视频页面内容，存于$text中
	    $link = 'http://v.ku6.com/show/'.$flashvar.'.html';
			$text=@file_get_contents($link);
			if($text) {
				//preg_match("/<title>(.*?) - (.*)<\/title>/",  $text, $title);
		    //经分析，酷六的视频截图地址在视频页面的<span class="s_pic“></span>标签之间
				preg_match_all("/<span class=\"s_pic\">(.*)<\/span>/",$text,$imageurl);
				if (!empty($imageurl[1][0])) {
					$imageurl = $imageurl[1][0]; 
				}
			}
		}
	}
	
	if($flashvar && $imageurl) {
		$result = $head."ku6|".$flashvar."|".$imageurl.";";
		return $result;
	}
	
	if(strstr($content,'tudou.com')){ //图像大小：120*90
		//http://www.tudou.com/programs/view/_ke1lzCnBYw/
		//http://www.tudou.com/v/_ke1lzCnBYw/v.swf
		
		//preg_match_all("/view\/([\w\-]+)\//", $link, $matches);
		preg_match_all("/http:\/\/www\.tudou\.com\/programs\/view\/([\w\-]+)\//", $content, $matches);
		if(!empty($matches[1][0])) {
			$flashvar = $matches[1][0];
		}		
		else {
			preg_match_all("/http:\/\/www\.tudou\.com\/v\/([\w\-]+)\/v.swf/", $content, $matches);
			if(!empty($matches[1][0])) {
				$flashvar = $matches[1][0]; 
			}
		}
		
		if($flashvar) {
			$link = 'http://www.tudou.com/programs/view/'.$flashvar.'/';
			$text = @file_get_contents($link);  
			
			if($text) {
				//preg_match("/<title>(.*?) - (.*)<\/title>/",  $tudou, $title);
				preg_match_all("/<span class=\"s_pic\">(.*)jpg<\/span>/",$text,$imageurl);
				if (!empty($imageurl[1][0])) {
					$imageurl = $imageurl[1][0];
					$imageurl .= "jpg";
				}			
			}	
		}
	}

	if($flashvar && $imageurl) {
		$result = $head."tudou|".$flashvar."|".$imageurl.";";
	}
	return $result;
}


function bee_getvideo($thumbnail)
{
	$result = array();
	$array = explode("|", $thumbnail);	
	if($array[0]=="ta_video_youku") {
		$result['link'] = 'http://v.youku.com/v_show/id_'.$array[1].'.html';
		$result['video'] = 'http://player.youku.com/player.php/sid/'.$array[1].'/v.swf';
	}
	else if($array[0]=="ta_video_ku6") {
		$result['link'] = 'http://v.ku6.com/show/'.$array[1].'.html';
		$result['video'] = 'http://player.ku6.com/refer/'.$array[1].'/v.swf';
	}
	else if($array[0]=="ta_video_tudou") {
		$result['link'] = 'http://www.tudou.com/programs/view/'.$array[1].'/';
		$result['video'] = 'http://www.tudou.com/v/'.$array[1].'/v.swf';
	}
	$result['flashvar'] = $array[1];
	$result['imageurl'] = $array[2];
	return $result;
}


function bee_create_thumb($thumbnail, $filename, $year, $month)
{
	if ($thumbnail == '') return false;
	if ($filename == '') return false;
	global $downloadDir, $siteurl, $rooturl, $destpath, $downloadpath; //申明全局变量
	
	
	//修改逻辑，仅仅制作首页缩略图，其他规格的缩略图在调用时按需生成。
	$bee_thumb_options = get_bee_thumb_options();

	$home_width 			= $bee_thumb_options['width_of_home_images'];
	$home_height 			= $bee_thumb_options['height_of_home_images'];
	$crop = $bee_thumb_options['crop_home_images'];	
	if($crop == "crop") $crop = true;
	else $crop = false;
	
	$isconv = false;
	if (strstr($thumbnail,$siteurl)) { // 本地图片
		$thumbnail = str_replace($siteurl,$rooturl,$thumbnail);//网址替换成主机本地地址
		$thumbnail = str_replace('..',$rooturl,$thumbnail);//有的本地图片地址省略了站名
		$thumbnailbackup = $thumbnail;
		if(!file_exists($thumbnail)) {
			//echo $thumbnail."这个地址认为文件不存在thumbnail<br>";
			$thumbnail = iconv('UTF-8','GB2312',urldecode($thumbnail));
			//echo $thumbnail."转码后thumbnail<br>";
			if(!file_exists($thumbnail)) {
				//echo $thumbnail."这个文件也不存在thumbnail<br>";
				return false;
			}else {
				//echo "文件存在！<br>";
				$isconv = true;
				$thumbnailbackup2 = $thumbnailbackup;
				$thumbnailbackup = $thumbnail;
			}	
		}
	}else {  // 远程图片
		
		$tempdownloadpath = $downloadpath."$year/";
		if(!file_exists($tempdownloadpath)) { 
			if(!(@mkdir($tempdownloadpath,0755))) { 
				if(is_admin()) {
					echo "提示：很抱歉，无法创建缩略图子目录，请手动创建目录".$tempdownloadpath."，权限设置为755。<br>"; 
					return; 
				}
			} 
		}
		
		$tempdownloadpath = $tempdownloadpath."$month/";
		if(!file_exists($tempdownloadpath)) { 
			if(!(@mkdir($tempdownloadpath,0755))) { 
				if(is_admin()) {
					echo "提示：很抱歉，无法创建缩略图子目录，请手动创建目录".$tempdownloadpath."，权限设置为755。<br>"; 
					return; 
				}
			} 
		}
	
		$thumbnail = bee_save_pic($thumbnail, $tempdownloadpath, $filename); //下载图片
		$thumbnailbackup = $thumbnail;
		if ($thumbnail == false) {//下载不成功，可能图片链接失效
			return false;
		}
	}
	
	//将图片按照规格存放在不同子目录
	if ($crop == true) $subfolder = "{$home_width}x{$home_height}-c/";
	else $subfolder = "{$home_width}x{$home_height}-u/";
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
	
	// 分别制作缩略图
	$thumbnail = bee_image_resize( $thumbnailbackup, $home_width, $home_height, $crop, null, $subfolder, 90, $filename); //直接制作首页缩略图
	if ($thumbnail == false) return false; //缩略不成功。
	
	
	if ($isconv == false) $thumbnailbackup = str_replace($rooturl,$siteurl,$thumbnailbackup);
	else $thumbnailbackup = str_replace($rooturl,$siteurl,$thumbnailbackup2);
	return $thumbnailbackup;
}

// 检查缩略图是否存在
function bee_is_thumb_exist($filename, $thumbwidth, $thumbheight, $crop, $year, $month)
{
	global $siteurl, $rooturl, $destpath; //申明全局变量
	
	if ($crop == true) $crop = "-c";
	else $crop = "-u";
	$subfolder = "{$thumbwidth}x{$thumbheight}{$crop}/$year/$month/";
	$file = $destpath.$subfolder.$filename; //图片按规格存放于子目录
	
	$filejpg = $file.".jpg";
	$filejpeg = $file.".jpeg";
	$filegif = $file.".gif";
	$filepng = $file.".png";
	$file = str_replace("\\","/",$file);
	
	if(file_exists($filejpg)) $file =  $filejpg;
	else if (file_exists($filejpeg))  $file =  $filejpeg;
	else if (file_exists($filegif))  $file =  $filegif;
	else if (file_exists($filepng)) $file =  $filepng;
	else return false;

	$file =  str_replace($rooturl,$siteurl,$file);

	return $file;
}

?>