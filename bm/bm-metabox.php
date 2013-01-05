<?php

/*-----------------------------------------------------------------------------------*/
/* custom dashboard widgets  */
/*-----------------------------------------------------------------------------------*/
add_action('wp_dashboard_setup', 'bm_dashboard_widgets');
function bm_dashboard_widgets(){
  global $wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['normal']['core']["dashboard_plugins"]);
  unset($wp_meta_boxes['dashboard']['normal']['core']["wpjam_dashboard_widget"]);
}

/*-----------------------------------------------------------------------------------*/
/* add tips  for editor form  */
/*-----------------------------------------------------------------------------------*/
add_action('submitpost_box', 'custom_fields_tip');
function custom_fields_tip(){
	$h3 = "发布提示";	
	$html=<<<END
<div class="stuffbox meta-box-sortables ui-sortable">
	<h3>$h3</h3>
	<div class="inside">
		<font color="red"><p>发布前请选好分类、添加至少3个标签！如无特殊原因请添加缩略图！</p>	</font>	
	</div>
</div>
END;
	echo $html;
}

function customize_meta_boxes() {
	remove_meta_box('trackbacksdiv','post','normal');
	remove_meta_box('postexcerpt','post','normal');
	remove_meta_box('commentsdiv','post','normal');
	remove_meta_box('trackbacksdiv','page','normal');
	remove_meta_box('postexcerpt','page','normal');
	remove_meta_box('commentsdiv','page','normal');
	remove_meta_box('postcustom','post','normal');
} 
add_action('admin_init','customize_meta_boxes');

 
/*-----------------------------------------------------------------------------------*/
/* add meta boxes  for editor form  */
/*-----------------------------------------------------------------------------------*/
add_action( 'admin_menu', 'bm_postmeta_box' );  
add_action( 'save_post', 'bm_save_postmeta_box' );  

function bm_postmeta_box() {  
	if( function_exists( 'add_meta_box' ) ) {  
		add_meta_box( 'new-meta-boxes', '文章扩展内容', 'bm_display_postmeta_box', 'post', 'normal', 'high' ); 
	}  
}  
  
function bm_display_postmeta_box() {
	global $post,$bm, $meta_boxes;  
	?>  
	  
	<div class="form-wrap">  
	  
	<?php  
	wp_nonce_field( plugin_basename( __FILE__ ), 'bee_meta_boxes_wpnonce', false, true );  

	foreach( $meta_boxes as $meta_box ) {  
		switch ( $meta_box['type'] ) {  
			   
			case "checkbox":  

				$value = $bm[ $meta_box['key'] ];
				if(!is_array($value)) $value=array();
				if($value!=""&&is_array($value)){
					if ( in_array($post->ID, $value) ) {
						$select = true;
					}else{
						$select = false;
					}
				}else{
					$select = false;
				}
				//print_r($value);
				?>
				<div style="width: 180px; float: left;">
					<input  style="width: 30px; float: left;" type="checkbox" id="<?php echo $meta_box[ 'key' ]; ?>" name="<?php echo $meta_box[ 'key' ]; ?>" <?php checked($select); ?>/>
					<label  style="width: 120px; float: left;"  for="<?php echo $meta_box[ 'key' ]; ?>"><?php echo $meta_box[ 'name' ]; ?></label>
				</div>				

			<?php

			  
			break;  

			case "line":  
				echo '<div class="clear"></div>';
			break;  

			case "thumbnail":  
				bm_uploader_function($meta_box);
			break;  

			case "text":  

				$value = get_post_meta($post->ID, $meta_box['key'], true);  
				?>  
				  
				<div class="form-field form-required">
				<label for="<?php echo $meta_box[ 'key' ]; ?>" style="color: red"><?php echo $meta_box[ 'name' ]; ?></label>   
				<input type="text" name="<?php echo $meta_box[ 'key' ]; ?>" value="<?php echo htmlspecialchars( $value ); ?>" />   
				<p><?php echo $meta_box[ 'desc' ]; ?></p>  
				</div>  

			<?php
			  
			break;  

		}	// switch
	}	 //	foreach
?>  
	</div>  
	<?php  
}  

function bm_save_postmeta_box( $pID ) {  
	global $post,$meta_boxes;  
	$bm = get_option(SHORTNAME);

	if ( !wp_verify_nonce( $_POST[ 'bee_meta_boxes_wpnonce' ], plugin_basename(__FILE__) ) )  
		return $pID;  
	  
	if ( !current_user_can( 'edit_post', $pID ))  
		return $pID;  

	  //Note that post ID may reference a post revision and not the last saved post. Use wp_is_post_revision to get the ID of the real post.
	if ( $the_post = wp_is_post_revision($pID) )
	$pID = $the_post;

	foreach( $meta_boxes as $meta_box ) {  
		switch ( $meta_box['type'] ) {  
			case "checkbox":  

				$value = $bm[ $meta_box['key'] ];
				if(!is_array($value)) $value=array();
				if ( isset($_POST[ $meta_box['key'] ]) ) {	// 检查 $pID 是否在数组中
					if (!in_array($pID, $value) ) {
						$value[] = $pID;
					}
				} else {	// 如果 $pID 不在数组中则删除
					$this_key = array_search($pID, $value); 
					unset($value[$this_key]);
				}
				if(count($value)>intval($meta_box['length'])) array_shift ($value);	// 如果数组长度超过限制则删短
				$bm[ $meta_box['key'] ] = $value;
				update_option( SHORTNAME, $bm );

			  
			break;  
			   
			case "text":  

				$value = $_POST[ $meta_box[ 'key' ] ];
				if($value!="")	 update_post_meta( $pID, $meta_box['key'], $value );  

			  
			break;  

		}	// switch
	}	 //	foreach

}  