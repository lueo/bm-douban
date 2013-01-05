<?php

/*
* 后台设置
*/


//  默认参数
$bee_thumb_default_options = array(
	"width_of_home_images"=>	'150', 
	"height_of_home_images"=>	 '120', 
	"homepage_new_window"=>	 '_blank', 
	"homepage_default_image"=>"", 
	"crop_home_images"=>'crop', 
	"disable_external"=>"false",
	"auto_replace"=>"false", 
	"auto_replace_exception"=>"",
	"video_thumb"=>'post', 
	"video_link_target"=>'_blank'
);

//  设置默认参数值并返回。
function get_bee_thumb_options()
{
	global $bee_thumb_default_options;
	if (!is_array(get_option('bee_thumb_options'))) // 添加到数据库表项; 如果已经存在, 则什么也不做。
		add_option('bee_thumb_options', $bee_thumb_default_options);
	$bee_thumb_options = get_option('bee_thumb_options');
	
	return $bee_thumb_options;
}
add_action('admin_menu', 'add_bm_thumb_menu');
function add_bm_thumb_menu(){
	add_submenu_page(SHORTNAME,SHORTNAME, __('thumnails settings','bm'), 7,'bm_thumb','add_bm_thumb_page'); 
}
// 生成后台设置页面
function add_bm_thumb_page(){
	global $bee_thumb_default_options;
    $bee_thumb_options = get_bee_thumb_options();

    // 如果表单已经提交, 则清理。($_POST为PHP超全局变量(PHP版本 >= 5.0))
    if (isset($_POST['clean']))    {
    	bee_clean(); 
    }
    
    if (isset($_POST['clean-custom-fields']))    {
    	bee_clean_custom_fields(); 
    }
    
    if (isset($_POST['clean-download']))    {
    	bee_clean_download(); 
    }
    
    if (isset($_POST['clean-old-thumbnails']))    {
    	bee_clean_old_thumbnails(); 
    }
    
    if (isset($_POST['reset-configuration']))    {
    	delete_option("bee_thumb_options"); //delete option
    	$bee_thumb_options = get_bee_thumb_options(); //reset option
    }
    // 如果表单已经提交, 则保存数据。($_POST为PHP超全局变量(PHP版本 >= 5.0))
     if (isset($_POST['submit'])){
		foreach($bee_thumb_default_options as $key=>$value){
					$bee_thumb_options[$key] = $_POST[$key];
		}
	update_option('bee_thumb_options', $bee_thumb_options);
	$bee_thumb_options = get_bee_thumb_options();
}

?>

<div class="wrap">

<form method="post" action="">
	
	<p><h2><?php _e("设置外链图片", 'bm'); ?>: </h2>
	<h3><?php _e("更改外链图片设置后, 需要点击本页底部“彻底清理插件”才能生效。", 'bm'); ?></h3>
	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;padding-top:5px;" for="disable_external"><?php _e("如何处理外链图片", 'bm'); ?> : </label>
		<div style="float:left;"><input type="radio" id="disable_external" name="disable_external" <?php if ($bee_thumb_options["disable_external"] == "false") print "checked='checked'"; ?> value="false" />  <?php _e("自动下载外链图片到本地, 自动生成缩略图。(Yupoo等相册可以直接调用外链缩略图)", 'bm'); ?><br /><input type="radio" id="disable_external" name="disable_external" <?php if ($bee_thumb_options["disable_external"] == "fake") print "checked='checked'"; ?> value="fake" />  <?php _e("不制作缩略图, 将原图片缩小显示, 节省空间和流量, 但图片有可能变形。 ", 'bm'); ?><br /><input type="radio" id="disable_external" name="disable_external" <?php if ($bee_thumb_options["disable_external"] == "true") print "checked='checked'"; ?> value="true" />  <?php _e("(有些博客受主机环境所限制, 无法下载远程图片) 忽略一切外链图片, 完全不显示。", 'bm'); ?></div>
			
	</div>
	<div class="clear"></div>



	<p><h2><?php _e("首页缩略图", 'bm'); ?>: </h2>

	<div style="clear: both;padding-top:10px;">
				<label>
				<input name="auto_replace" type="checkbox" id="auto_replace" value="true"  <?php echo ($bee_thumb_options["auto_replace"] == 'true') ? 'checked' : ''; ?>>
				<?php _e("自动将文章中的远程图片替换为本地图片, 本操作消耗资源比较大，慎用。",'bm');?>
				</label>
				<br />
				<label for="auto_replace_exception"><?php _e("图片本地化排除如下域名，如排除你使用的外链图床。<br />请注意：如果你的网站是example.com, 那么子域名图床如img.example.com也算作外链图床，需要排除。<br />多个域名用英文分号 ; 隔开", 'bm'); ?></label>
				<br />
				<input type="text" id="auto_replace_exception" name="auto_replace_exception"  style="width:550px;" <?php if ($bee_thumb_options["auto_replace_exception"]) print "value='" . $bee_thumb_options["auto_replace_exception"] . "'"; ?>/>		
	</div>

	<div class="clear"></div>

	<p><h2><?php _e("首页缩略图", 'bm'); ?>: </h2>

	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;padding-top:7px;"  for="size_of_homeimages"><?php _e("尺寸", 'bm'); ?> : </label>
		<div style="float:left;"><?php _e("宽度", 'bm'); ?><input type="text" style="width:40px;" id="width_of_home_images" name="width_of_home_images" size="1" maxlength="3" <?php if ($bee_thumb_options["width_of_home_images"]) print "value='" . $bee_thumb_options["width_of_home_images"] . "'"; ?>/><?php _e("像素", 'bm'); ?>, <?php _e("高度", 'bm'); ?><input type="text" style="width:40px;" id="height_of_home_images" name="height_of_home_images" size="1" maxlength="3" <?php if ($bee_thumb_options["height_of_home_images"]) print "value='" . $bee_thumb_options["height_of_home_images"] . "'"; ?>/><?php _e("像素", 'bm'); ?><br /></div>
	</div>

	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;padding-top:7px;" for="crop_home_images"><?php _e("裁剪", 'bm'); ?> : </label>
		<div style="float:left;"><input type="radio" id="crop_home_images" name="crop_home_images" <?php if ($bee_thumb_options["crop_home_images"] == "uncrop") print "checked='checked'"; ?> value="uncrop" />  <?php _e("智能保持原图比例", 'bm'); ?> <input type="radio" id="crop_home_images" name="crop_home_images" <?php if ($bee_thumb_options["crop_home_images"]!= "uncrop") print "checked='checked'"; ?> value="crop" />  <?php _e("精确裁剪成上述宽度和高度", 'bm'); ?> </div>
	</div>

	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;padding-top:7px;" for="homepage_position"><?php _e("位置", 'bm'); ?> : </label>
		<div style="float:left;"><input type="radio" id="homepage_position" name="homepage_position" <?php if ($bee_thumb_options["homepage_position"] == "left") print "checked='checked'"; ?> value="left" />  <?php _e("靠左显示", 'bm'); ?> <input type="radio" id="homepage_position" name="homepage_position" <?php if ($bee_thumb_options["homepage_position"] == "right") print "checked='checked'"; ?> value="right" />  <?php _e("靠右显示", 'bm'); ?> <input type="radio" id="homepage_position" name="homepage_position" <?php if ($bee_thumb_options["homepage_position"] == "random") print "checked='checked'"; ?> value="random" />  <?php _e("左右随机出现", 'bm'); ?> <br /><input type="radio" id="homepage_position" name="homepage_position" <?php if ($bee_thumb_options["homepage_position"] == "center") print "checked='checked'"; ?> value="center" />  <?php _e("居中显示(居中的时候文字无法环绕, 图片尺寸设大一点会比较美观)", 'bm'); ?></div>
	</div>

	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;padding-top:7px;" for="homepage_new_window"><?php _e("窗口", 'bm'); ?> : </label>
		<div style="float:left;"><input type="radio" id="homepage_new_window" name="homepage_new_window" <?php if ($bee_thumb_options["homepage_new_window"] == "_blank") print "checked='checked'"; ?> value="_blank" />  <?php _e("在新窗口中打开", 'bm'); ?> <input type="radio" id="homepage_new_window" name="homepage_new_window" <?php if ($bee_thumb_options["homepage_new_window"] != "_blank") print "checked='checked'"; ?> value="_self" />  <?php _e("在当前窗口打开", 'bm'); ?> </div>
	</div>

	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;padding-top:7px;" for="homepage_link_target"><?php _e("链接目标", 'bm'); ?> : </label>
		<div style="float:left;"><input type="radio" id="homepage_link_target" name="homepage_link_target" <?php if ($bee_thumb_options["homepage_link_target"] == "post") print "checked='checked'"; ?> value="post" />  <?php _e("点击打开文章", 'bm'); ?> <input type="radio" id="homepage_link_target" name="homepage_link_target" <?php if ($bee_thumb_options["homepage_link_target"] == "image") print "checked='checked'"; ?> value="image" />  <?php _e("点击打开原始大图", 'bm'); ?> </div>
	</div>

	<div style="clear: both;padding-top:10px;">
		<label style="float:left;width:150px;text-align:right;padding-right:6px;" for="homepage_default_image"><?php _e("默认图片", 'bm'); ?> : </label>
		<div style="float:left;"><input type="text" id="homepage_default_image" name="homepage_default_image"  style="width:400px;" <?php if ($bee_thumb_options["homepage_default_image"]) print "value='" . $bee_thumb_options["homepage_default_image"] . "'"; ?>/> <br /><?php _e("文章中不存在图片时, 如果要显示默认缩略图片, 请填写图片地址。", 'bm'); ?><br /><?php _e("例如", 'bm'); ?>http://niaolei.org.cn/wp-content/uploads/icon/taiji.jpg <br /><?php _e("最好是本地图片。", 'bm'); ?></div>
	</div>

	<div style="clear: both;padding-top:2px;padding-bottom:2px;text-align:center;">
	<p class="submit"><input type="submit" name="submit" value="<?php _e("点击更新设置 &raquo;", 'bm'); ?>" /></p>
	</div>


	<div style="clear: both;padding-top:20px;">
	<hr>
	</div>

</form>


<form method="post" action="">

	<div style="clear: both;padding-top:10px;"></div>
	<p><h2><?php _e("清理和重置", 'bm'); ?>: </h2> 
	<div style="clear: both;padding-top:10px;"></div>

	<div style="float:left;padding-left:100px;padding-top:7px;">
	<p class="submit"><input type="submit" name="reset-configuration" value="<?php _e("点击还原后台所有选项到默认设置 &raquo;  安全！", 'bm'); ?>" /></p>
	<p class="submit"><input type="submit" name="clean-old-thumbnails" value="<?php _e("点击清理不再用到的缩略图 &raquo;  安全！", 'bm'); ?>" /></p>
	</div>

	<div style="clear: both;"></div>

	<div style="float:left;padding-left:100px;padding-top:7px;">
	<?php _e("如果你的空间有限, 可以一键清理下载到本地的远程图片, 即删除/wp-content/uploads/ta-thumbnails-cache/TAdownload文件夹下所有图片: ", 'bm'); ?><br>
	<?php _e("这样做的前提是:后台的缩略图尺寸都不再改动, 即不需要生成和调用新尺寸的图片。只要不再改动尺寸, 已有的缩略图仍然可以正常显示: ", 'bm'); ?>
	<p class="submit"><input type="submit" name="clean-download" value="<?php _e("点击清理下载的远程文件 &raquo;  慎用！", 'bm'); ?>" /></p>
	</div>

	<div style="clear: both;"></div>

	<div style="float:left;padding-left:100px;padding-top:7px;">
	<?php _e("一键清理全部自定义域ta-thumbnail, 使插件重新检测缩略图: ", 'bm'); ?>
	<p class="submit"><input type="submit" name="clean-custom-fields" value="<?php _e("点击清理自定义域 &raquo;  慎用！", 'bm'); ?>" /></p>
	</div>

	<div style="clear: both;"></div>

	<div style="float:left;padding-left:100px;padding-top:7px;">
	<?php _e("一键清理数据库并删除所有下载的和生成的缩略图。之后逐一点开文章, 插件会重新生成缩略图: ", 'bm'); ?>
	<p class="submit"><input type="submit" name="clean" value="<?php _e("点击彻底清理 &raquo;  慎用！", 'bm'); ?>" /></p>
	</div>

</form>

</div>

<?php
}

?>