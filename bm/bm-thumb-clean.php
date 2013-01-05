<?php

/*
* 清理缩略图和自定义字段，重新生成缩略图
*/
function bee_clean() {
	
	bee_clean_custom_fields();
		
	global $destpath;//申明全局变量
	if (substr($destpath, -1) == "/") $folderPath = substr($destpath, 0, -1); //去掉结尾的'/'
	if (bee_clean_image_folder($folderPath, true) === false) {
		//echo "clean failed";
	}
}

function bee_clean_custom_fields() {
	global $wpdb;
	$wpdb->query("DELETE FROM $wpdb->postmeta
		WHERE meta_key LIKE  'ta-thumbnail'");//重置自定义字段
}

function bee_clean_download() {	
	global $downloadpath;//申明全局变量
	if (substr($downloadpath, -1) == "/") $folderPath = substr($downloadpath, 0, -1); //去掉结尾的'/'
	if (bee_clean_image_folder($folderPath, false) === false) {
		//echo "clean failed";
	}
}

function bee_clean_old_thumbnails() {	
	global $destpath;//申明全局变量, 即ta-thumbnails-cache文件夹
	if (substr($destpath, -1) == "/") $folderPath = substr($destpath, 0, -1); //去掉结尾的'/'
	
	//删除ta-thumbnails-cache下面两级子目录内的所有文件，除TAdownload之外。
	if (bee_clean_image_folder($folderPath, true, 0, 2, "TAdownload") === false) {
		//echo "clean failed";
	}
}

//完全递归删除folderPath下所有文件。遍历子目录，但不删除子目录。
function bee_clean_image_folder($folderPath, $recursive=false, $depth=0, $limit=10, $exclude="") {
	if ( @is_dir ( $folderPath ) ) {
		$dh  = @opendir($folderPath);
		while( false !== ( $value = @readdir( $dh ) ) ) {
			if ( $value != "." && $value != ".." && $value!=$exclude) {
				$value = $folderPath . "/" . $value; 
				if ( @is_dir ( $value ) && $recursive && $depth < $limit) { //如果是子目录，则继续遍历
					bee_clean_image_folder( $value, true, $depth+1, $limit, $exclude); 
				} else { //否则如果是文件，则执行删除操作 
					@unlink( $value );
				}
			}
		}
		//return @rmdir( $folderPath );
		return true;
	} else {
		return false;
	}
}

?>