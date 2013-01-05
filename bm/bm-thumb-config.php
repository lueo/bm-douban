<?php
/****************** 博客URL和存储目录 *************************/

$siteurl = get_option('siteurl'); //博客网址
if (substr($siteurl, -1) != "/") $siteurl = $siteurl."/";//保证左斜杠结尾

$newABSPATH = str_replace("\\","/",ABSPATH);  //右斜杠替换为左斜杠
//$rooturl = str_replace("\\","/",getenv("DOCUMENT_ROOT")); //右斜杠替换为左斜杠
$rooturl = $newABSPATH;
if (substr($rooturl, -1) != "/") $rooturl = $rooturl."/";//保证左斜杠结尾

$uploadDir = str_replace($rooturl,"",strstr($newABSPATH,$rooturl)) . "wp-content/uploads/";
$thumbDir = str_replace($rooturl,"",strstr($newABSPATH,$rooturl)) . "wp-content/uploads/ta-thumbnails-cache/";
$downloadDir=str_replace($rooturl,"",strstr($newABSPATH,$rooturl)) . "wp-content/uploads/ta-thumbnails-cache/TAdownload/";


$uploadpath = $rooturl.$uploadDir;
$destpath = $rooturl.$thumbDir;
$downloadpath = $rooturl.$downloadDir;

/**************** 反馈您站点的基本信息 ************************/
// 以下是调试代码，如果插件不能正常工作，请将下面这句注释(echo前面的两个左斜杠)去掉，让插件打印信息，然后反馈到插件主页。

//echo "newABSPATH: ".$newABSPATH."<br>"."rooturl: ".$rooturl."<br>"."thumbDir: ".$thumbDir."<br>"."downloadDir: ".$downloadDir."<br>"."siteurl: ".$siteurl."<br>"."destpath: ".$destpath."<br>"."downloadpath: ".$downloadpath."<br>";


/**************** 以下代码请不要随意改动 ************************/


if(!file_exists($uploadpath)) { 
	if(!(@mkdir($uploadpath,0755))) { 
		if(is_admin()) {
			echo _e("提示：很抱歉，无法创建缩略图目录，请手动创建目录 ", 'wp_thumbnails')."<b>".$uploadpath."</b>"._e("权限设置为755.")."<br>"; 
			return; 
		}
	} 
}

if(!file_exists($destpath)) { 
	if(!(@mkdir($destpath,0755))) { 
		if(is_admin()) {
			echo _e("提示：很抱歉，无法创建缩略图目录，请手动创建目录 ")."<b>".$destpath."</b>"._e("权限设置为755.")."<br>"; 
			return; 
		}
	} 
}

if(!file_exists($downloadpath)) { 
	if(!(@mkdir($downloadpath,0755))) { 
		if(is_admin()) {
			echo _e("提示：很抱歉，无法创建缩略图目录，请手动创建目录 ")."<b>".$downloadpath."</b>"._e("权限设置为755.")."<br>"; 
			return; 
		}
	} 
}

?>