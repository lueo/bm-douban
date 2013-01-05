<?php
$ngg_table = $wpdb->prefix . "ngg_gallery";

/**
 * build_caogen_js
 */
function add_column_to_ngg() {	
	global $wpdb,$ngg_table;
	if (!check_column_added_to_ngg()) 
    $wpdb->query("ALTER TABLE {$ngg_table} ADD COLUMN postid BIGINT( 20 ) NOT NULL DEFAULT 0;");
	if (check_column_added_to_ngg()) 
		return "the column is exist now!";
	else 
		return "error!";
}
function check_column_added_to_ngg() {	
	global $wpdb,$ngg_table;
	if ($wpdb->query("Describe {$ngg_table} postid") != '') 
		return true;
	else 
		return false;
}


/**
 * Displays the Reaction Button admin menu
 */

add_action('admin_menu', 'add_ngg_manage_plus_menu');
function add_ngg_manage_plus_menu(){
	add_submenu_page(SHORTNAME,SHORTNAME, __('ngg manage plus','bm'), 7,'ngg_manage_plus','submenu_ngg_manage_plus'); 
}

function submenu_ngg_manage_plus() {
		
		// PROCESSING
		if(isset($_POST['add_column_to_ngg'])) {	// delete js file
			$message = add_column_to_ngg();
		}if(isset($_POST['delete'])) {	// delete js file
			$message = "delete success";
		} elseif(isset($_POST['add'])) {  // update or create js file
			$message = build_cg_js();
		} elseif(isset($_POST['save'])) {  // save settings
			update_option('cg_js_pic', $_POST['cg_js_pic']);
			$message = "save success!";
		}
			if($message!='') echo '<div class="updated fade msg" id="message"><p>'.$message.'</p></div>';
		// PROCESSING END
	
	?>
	
	<form action="" method="post" class="themeform">
<div class="caogen_js_pic">
			<h2>build the js file for index page</h2>
	<fieldset><legend>dafault picture setting for home page</legend>
		<label>img src</label>
		<input  name="cg_js_pic" class="text" value="<?php echo get_option('cg_js_pic'); ?>">
	</fieldset>
		<div class="clear"></div>
			<input type="submit" value="add column to ngg" name="add_column_to_ngg" class="button" />
			<input type="submit" value="save" name="save" class="button" />
			<input type="submit" value="delete" name="delete" class="button" />

</div>

<style>
	.caogen_js_pic{ padding: 10px; }
.caogen_js_pic .text{ background: #fff; width: 500px; }
.caogen_js_pic input{ margin: 10px 0; }
</style>
<div class="clear"></div>

	</form>
<?php 
}
?>
