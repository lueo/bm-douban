<?php
/*-----------------------------------------------------------------------------------*/
/* auto create option lists */
/*-----------------------------------------------------------------------------------*/
function bm_option_helper($opts){
	$bm = get_option(SHORTNAME);

	foreach ($opts as $value) {  
		switch ( $value['type'] ) {  
		   
		case "h2":  

			echo '<h2>'.$value['name'].'</h2><input type="text" name="opt_type" value="'.$value['key'].'" hidden />';
		  
		break;  

		case "upload":
			
			echo  bm_uploader_function($value);
			
		break;
		   
		case 'text':  
			if ( $bm[ $value['key'] ] != "") { 
				$text = stripslashes($bm[ $value['key'] ] ); 
			} else {
				$text = $value['std'];
			}	  
			echo '<div class="bm_input bm_text">  
			<label for="'.$value['key'].'">'.$value['name'].'</label>  
			<input name="'.$value['key'].'" id="'.$value['key'].'" type="'.$value['type'].'" value="'.$text.'" />  
		 <small>'.$value['desc'].'</small><div class="clearfix"></div></div>';

		break;  
		   
		case 'textarea':  
			if ( $bm[ $value['key'] ] != "") { 
				$text = stripslashes($bm[ $value['key'] ] ); 
			} else {
				$text = $value['std'];
			}	 

			  
			echo '<div class="bm_input bm_textarea">  
				<label for="'.$value['key'].'">'.$value['name'].'</label>  
				<textarea name="'.$value['key'].'" type="'.$value['type'].'" cols="" rows="">'.$text.'</textarea>  
			 <small>'.$value['desc'].'</small><div class="clearfix"></div>  
			   
			 </div>';  
			
		break;  
		   
		case 'select':  

			echo '<div class="bm_input bm_select">  
				<label for="'.$value['key'].'">'.$value['name'].'</label>  
				<select name="'.$value['key'].'" id="'.$value['key'].'">';
			  
			 foreach ($value['options'] as $option) { 
				$name = get_cat_name($option);
				if( $name=="" ) $name = get_post($option)->post_title;
				 if ($bm[ $value['key'] ] == $option) { $addhtml = 'selected="selected"'; }else{ $addhtml=''; }
				echo '<option value="'.$option.'" '.$addhtml.'>'.$name.'</option>';
			} 
			echo '</select><small>'.$value['desc'].'</small><div class="clearfix"></div></div>';  

		break;  
		   
		case "checkbox":  

			  
			echo '<div class="bm_input bm_checkbox">  
				<label for="'.$value['key'].'">'.$value['name'].'</label>';
				  
			if(get_option($value['key'])){ $checked = "checked=\"checked\""; }else{ $checked = "";}  
			echo '<input type="checkbox" name="'.$value['key'].'" id="'.$value['key'].'" value="true" '.$checked.' />  
			  
			  
				<small>'.$value['desc'].'</small><div class="clearfix"></div>  
			 </div>  ';
		break; 
		   
		}  
	}  
}

/*-----------------------------------------------------------------------------------*/
/* upload function */
/*-----------------------------------------------------------------------------------*/

function bm_uploader_function($value){
	$bm = get_option(SHORTNAME);
    
	$output = '<div class="bm_input bm_text"><label for="'.$value['key'].'">'.$value['name'].'</label>  ';
    $upload = $bm[$value['key']];
	

	$val = $value['std'];
	if ( $bm[ $value['key'] ] != "") { $val = $bm[$value['key']]; }
	$output .= '<input class="woo-input" name="'. $value['key'] .'" id="'. $value['key'] .'_upload" type="text" value="'. $val .'" />';

	
	$output .= '<div class="upload_button_div"><span class="button image_upload_button" id="'.$value['key'].'">Upload Image</span>';
	
	if(!empty($upload)) {$hide = '';} else { $hide = 'hide';}
	
	$output .= '<span class="button image_reset_button '. $hide.'" id="reset_'. $value['key'] .'" title="' . $value['key'] . '">Remove</span>';
	$output .='</div>' . "\n";
    $output .= '<div class="clear"></div>' . "\n";
	if(!empty($upload)){
    	$output .= '<a class="woo-uploaded-image" href="'. $upload . '">';
    	$output .= '<img id="image_'.$id.'" src="'.$upload.'" alt="" />';
    	$output .= '</a>';
		}
	$output .= '<div class="clearfix"></div></div>' . "\n"; 


return $output;
}


/*-----------------------------------------------------------------------------------*/
/* Ajax Save Action - woo_ajax_callback */
/*-----------------------------------------------------------------------------------*/

add_action('wp_ajax_woo_ajax_post_action', 'woo_ajax_callback');

function woo_ajax_callback() {
	$bm = get_option(SHORTNAME);
	global $wpdb; // this is how you get access to the database
	$themename = get_option('template') . "_";
	//Uploads
	if($_POST['type'] == 'upload'){
		
		$clickedID = $_POST['data']; // Acts as the name
		$filename = $_FILES[$clickedID];
		$override['test_form'] = false;
	    $override['action'] = 'wp_handle_upload';    
	    $uploaded_file = wp_handle_upload($filename,$override);
		 
	            $upload_tracking[] = $clickedID;
	            update_option( $clickedID , $uploaded_file['url'] );
				//update_option( $themename . $clickedID , $uploaded_file['url'] );
		 if(!empty($uploaded_file['error'])) {echo 'Upload Error: ' . $uploaded_file['error']; }	
		 else { echo $uploaded_file['url']; } // Is the Response
	}

	elseif($_POST['type'] == 'image_reset'){
			
			$id = $_POST['data']; // Acts as the name
		    global $wpdb;
            $query = "DELETE FROM $wpdb->options WHERE option_name LIKE '$id'";
            $wpdb->query($query);
            //die;
	
	}

	elseif($_POST['type'] == 'submit') {
		$data = $_POST['data'];
		parse_str($data,$output);

//		print_r($output);
		
		if(is_array($output)){
			global $options;
			foreach ($options as $value) {

				if( isset( $output[ $value['key'] ] ) ) { 
					$bm[ $value['key'] ]= $output[ $value['key'] ];
					update_option( SHORTNAME, $bm );
				}
			} 
		}
        
		
	}
	


  die();

}
?>