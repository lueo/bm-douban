<?php
/*
功能:生成缩略图
*/

define("GIF",1);
define("JPG",2);
define("PNG",3);

/**
 * ta_image_resize() - Create a thumbnail from an Image.
 *
 * @param	$file					Filename of the original image(real path) 原图片的绝对路径或者url路径
 * @param	$max_w				Maximum width for the thumbnail 缩略图宽度
 * @param	$max_h				Maximum height for the thumbnail 缩略图高度
 * @param	$crop 				缩略图是否裁剪
 * @param	$suffix				保存缩略图文件名后缀
 * @param	$dest_path		保存缩略图文件的绝对路径
 * @param	$jpeg_quality	jpeg图的质量
 * @param	$filename			缩略图文件名(插件中取日志ID号)
 * @return	string			Thumbnail path on success, false on failure
 * 
 * This function can handle most image file formats which PHP supports.
 * If PHP does not have the functionality to save in a file of the same format, the thumbnail will be created as a jpeg.
 */

// Scale down an image to fit a particular size and save a new copy of the image
function bee_image_resize( $file, $max_w, $max_h, $crop=true, $suffix=null, $dest_path=null, $jpeg_quality=90, $filename=null) {
	$image = bee_load_image( $file );
	if ($image == false) return false;
	
	//getimagesize()函数返回原图像属性, 赋值给一组变量
	list($orig_w, $orig_h, $orig_type) = getimagesize( $file );
	//预处理尺寸，为生成缩略图作准备
	$dims = bee_image_resize_dimensions($orig_w, $orig_h, $max_w, $max_h, $crop);
	if ($dims==false)
		return false;//图片太小等不需要生成缩略图
	list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

	//新建一个真彩色图像
	$newimage = imagecreatetruecolor( $dst_w, $dst_h);

	// preserve PNG transparency
	if ( PNG == $orig_type && function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
		imagealphablending( $newimage, false); //设定图像的混色模式
		imagesavealpha( $newimage, true); //
	}

	//生成缩略图
	imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	// we don't need the original in memory anymore
	imagedestroy( $image );

	// $suffix will be appended to the destination filename, just before the extension
	// 添加图片文件后缀
	if ( !$suffix )
		$suffix = "{$max_w}x{$max_h}";//预期尺寸

	$info = pathinfo($file);//pathinfo() 返回一个结合数组包含有 path  的信息。包括以下的数组单元：dirname，basename  和 extension。 
	$ext = $info['extension'];
	if ( !is_null($dest_path) )// 设置缩略图放置目录
		$dir = $dest_path;
	else $dir = $info['dirname'];
	if ( !is_null($filename) )// 设置缩略图名称
		$name = $filename;
	else $name = basename($file, ".{$ext}");
	
	$destfilename = "{$dir}{$name}.{$ext}"; // 在这里修改

	if ( $orig_type == GIF ) {
		if (!imagegif( $newimage, $destfilename ) )//creates the GIF file in filename from the image image .
			return false;
	}elseif ( $orig_type == PNG ) {
		if (!imagepng( $newimage, $destfilename ) )
			return false;
	}else {
		// all other formats are converted to jpg 其他格式统统转化为jpg后缀
		$destfilename = "{$dir}{$name}.{$ext}";
		if (!imagejpeg( $newimage, $destfilename, $jpeg_quality  ) ) //TODO
			return false;
	}

	imagedestroy( $newimage );

	// Set correct file permissions
	$stat = stat( dirname( $destfilename ));
	$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
	@ chmod( $destfilename, $perms );

	return $destfilename;
}


/**
 * ta_load_image() - Load an image which PHP Supports.
 *
 * @package WordPress
 * @internal Missing Long Description
 * @param	string	$file	Filename of the image to load
 * @return	resource		The resulting image resource on success, Error string on failure.
 *
 */
function bee_load_image( $file ) {//直接由url获得
	if ( ! file_exists( $file ) ) {
		//echo "The file doesn't exist: ".$file;
		return false;
	}

	if ( ! function_exists('imagecreatefromstring') ) {
		echo "The GD image library is not installed. can't create thumbnails.";
		return false;
	}

	// Set artificially high because GD uses uncompressed images in memory
	@ini_set('memory_limit', '256M');
	$image = imagecreatefromstring( file_get_contents( $file ) );//用来将文件的内容读入到一个字符串

	if ( !is_resource( $image ) ) {//检测变量是否为资源类型
		echo "The file is not an image: ".$file;
		return false;
	}

	return $image;
}

// 以下几个辅助函数都是对图片尺寸进行预处理的，假定都正常工作。
// calculate dimensions and coordinates for a resized image that fits within a specified width and height
// if $crop is true, the largest matching central portion of the image will be cropped out and resized to the required size
function bee_image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop=true) {

	if ($orig_w <= 0 || $orig_h <= 0)
		return false;
	// at least one of dest_w or dest_h must be specific
	if ($dest_w <= 0 && $dest_h <= 0)
		return false;
		
	//图片有一维较小则不缩略
	if ($orig_w < $dest_w || $orig_h < $dest_h)
		return false;
		
	if ( $crop ) {

		// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
		$aspect_ratio = $orig_w / $orig_h; //原图宽高比
		$new_w = min($dest_w, $orig_w);
		$new_h = min($dest_h, $orig_h);
		if (!$new_w) {
			$new_w = intval($new_h * $aspect_ratio);//变量转成整数类型
		}
		if (!$new_h) {
			$new_h = intval($new_w / $aspect_ratio);
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h); //缩小比例

		//计算裁剪后的宽、高
		$crop_w = ceil($new_w / $size_ratio); 
		$crop_h = ceil($new_h / $size_ratio);

		//计算裁剪的起始位置
		$s_x = floor(($orig_w - $crop_w)/2); 
		$s_y = floor(($orig_h - $crop_h)/2);
	}else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		//调整最终的缩略区域,智能调整随高度还是随宽度保持比例。
		list( $new_w, $new_h ) = bee_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}
	// if the resulting image would be the same size or larger we don't want to resize it
	if ($new_w >= $orig_w && $new_h >= $orig_h)
		return false;

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	// 缩略图起始坐标x,缩略图起始坐标y,原图起始坐标x,原图起始坐标y,缩略图宽度,缩略图高度,原图宽度,原图高度.
	return array(0, 0, $s_x, $s_y, $new_w, $new_h, $crop_w, $crop_h);

}

// same as wp_shrink_dimensions, except the max parameters are optional.
// if either width or height are empty, no constraint is applied on that dimension.
// 参数：原图宽度,原图高度,缩略图宽度,缩略图高度.
function bee_constrain_dimensions( $current_width, $current_height, $max_width=0, $max_height=0 ) {
	if ( !$max_width and !$max_height )
		return array( $current_width, $current_height );

	$width_ratio = $height_ratio = 1.0;

	if ( $max_width > 0 && $current_width > $max_width )
		$width_ratio = $max_width / $current_width;
	//else return false; //只要宽小于原宽或高小于原高，则退出

	if ( $max_height > 0 && $current_height > $max_height )
		$height_ratio = $max_height / $current_height;
	//else return false;

	// the smaller ratio is the one we need to fit it to the constraining box
	$ratio = min( $width_ratio, 1.5*$height_ratio );
	//$ratio = $width_ratio;

	//宽度仍然不变，灵活调整高度。
	return array( intval($current_width * $width_ratio), intval($current_height * $ratio) );
}



/*
* 如果是远程图片，下载后制作缩略图。
*/

//文章内容下载图片
function bee_save_pic($url,$path,$filename)
{
	if ($url == "") return false;
	if ($path == "") return false;
	if ($filename == "") return false;
	
	//分析后缀,只下载gif，jpg，png图片
	$ext = strrchr($url,"."); //最后出现的.
	if($ext!=".gif" && $ext!=".jpg" && $ext!=".jpeg" && $ext!=".png" && $ext!=".GIF" && $ext!=".JPG" && $ext!=".PNG" && $ext!=".JPEG") 
		return false;
	$filename = $filename.$ext; 
	if(!function_exists('file_get_contents')) 
		return false;
		
	$content=@file_get_contents($url); //读取远程图片内容到字符串
	if ($content == false) return false;
	
	$filename=$path.$filename;
	
	//将指定的filename资源绑定到一个流fp上,读写方式打开.文件不存在则创建，存在则覆盖.
	$fp=fopen($filename,"w+");
	if (@fwrite($fp,$content)) //content的内容写入文件指针fp处
	{
		@fclose($fp);
		return $filename;
	}
	else 
	{
		@fclose($fp);
		return false;
	}
}

?>