<?php
function bm_ajax_process_message(){
?>
<div id="woo-popup-save" class="woo-save-popup"><div class="woo-save-save">Options Updated</div></div>
<div id="woo-popup-reset" class="woo-save-popup"><div class="woo-save-reset">Options Reset</div></div>      
<?php
}
function bm_ajax_save(){
?>
	<div id="bm_opts_save">
		<img style="display:none" src="<?php echo BMURL; ?>/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." /><input type="submit" value="<?php _e('Save'); ?>" class="button-primary submit-button" />
	</div>     
<?php
}

add_action( 'admin_menu', 'bm_addThemePage' );
function bm_addThemePage() {
	// Check all the Options, then if the no options are created for a relative sub-page... it's not created.
	if(function_exists('add_object_page')){
		add_object_page(SHORTNAME,__('bm options','bm'), 7,SHORTNAME, 'bm_options', BMURL.'/images/cp.png');
	}else{
		add_menu_page(SHORTNAME,__('bm options','bm'), 7,SHORTNAME, 'bm_options', BMURL.'/images/cp.png'); 
	}
	add_submenu_page(SHORTNAME,SHORTNAME, __('options','bm'), 7,SHORTNAME,'bm_options'); 
}

function bm_options() {
	global $options;
?>  
<div class="wrap" id="woo_container">
<?php bm_ajax_process_message(); ?>
<form action="" enctype="multipart/form-data" id="wooform">

	<div class="bm_opts">
		<?php 	bm_option_helper($options); ?>
	 </div>
<?php bm_ajax_save(); ?>

</form>
<?php  
}

add_action('admin_head', 'bm_admin_head');  
function bm_admin_head() {  
	switch ($_GET['page']){
		case "bm" :
		case "bm_uploads" :
			print '<link rel="stylesheet" href="'.BMURL.'/bm-style.css" type="text/css" media="screen" />';
			print '<script type="text/javascript" src="'.BMURL.'/js/bm-ajaxupload.js"></script>';
?>
	<script type="text/javascript">
jQuery(document).ready(function(){

	//Update Message popup
	jQuery.fn.center = function () {
		this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2+jQuery(window).scrollTop() + "px"},100);
		this.css("left", 250 );
		return this;
	}


	jQuery('#woo-popup-save').center();
	jQuery('#woo-popup-reset').center();
	jQuery(window).scroll(function() { 

		jQuery('#woo-popup-save').center();
		jQuery('#woo-popup-reset').center();

	});

		//AJAX Upload
	jQuery('.image_upload_button').each(function(){
	
	var clickedObject = jQuery(this);
	var clickedID = jQuery(this).attr('id');	
	new AjaxUpload(clickedID, {
		  action: '<?php echo admin_url("admin-ajax.php"); ?>',
		  name: clickedID, // File upload name
		  data: { // Additional data to send
				action: 'woo_ajax_post_action',
				type: 'upload',
				data: clickedID },
		  autoSubmit: true, // Submit file after selection
		  responseType: false,
		  onChange: function(file, extension){},
		  onSubmit: function(file, extension){
				clickedObject.text('Uploading'); // change button text, when user selects file	
				this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
				interval = window.setInterval(function(){
					var text = clickedObject.text();
					if (text.length < 13){	clickedObject.text(text + '.'); }
					else { clickedObject.text('Uploading'); } 
				}, 200);
		  },
		  onComplete: function(file, response) {
		   
			window.clearInterval(interval);
			clickedObject.text('Upload Image');	
			this.enable(); // enable upload button
			
			// If there was an error
			if(response.search('Upload Error') > -1){
				var buildReturn = '<span class="upload-error">' + response + '</span>';
				jQuery(".upload-error").remove();
				clickedObject.parent().after(buildReturn);
			
			}
			else{
				var buildReturn = '<img class="hide woo-option-image" id="image_'+clickedID+'" src="'+response+'" width="300" alt="" />';
				jQuery(".upload-error").remove();
				jQuery("#image_" + clickedID).remove();	
				clickedObject.parent().after(buildReturn);
				jQuery('img#image_'+clickedID).fadeIn();
				clickedObject.next('span').fadeIn();
				clickedObject.parent().prev('input').val(response);
			}
		  }
		});
	
	});



	
	//AJAX Remove (clear option value)
	jQuery('.image_reset_button').click(function(){

		var clickedObject = jQuery(this);
		var clickedID = jQuery(this).attr('id');
		var theID = jQuery(this).attr('title');

		var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
	
		var data = {
			action: 'woo_ajax_post_action',
			type: 'image_reset',
			data: theID
		};
		
		jQuery.post(ajax_url, data, function(response) {
			var image_to_remove = jQuery('#image_' + theID);
			var button_to_hide = jQuery('#reset_' + theID);
			image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
			button_to_hide.fadeOut();
			clickedObject.parent().prev('input').val('');
			
			
			
		});
		
		return false; 
		
	});   	 	


	//Save everything else
	jQuery('#wooform').submit(function(){
		
			function newValues() {
			  var serializedValues = jQuery("#wooform").serialize();
			  return serializedValues;
			}
			jQuery(":checkbox, :radio").click(newValues);
			jQuery("select").change(newValues);
			jQuery('.ajax-loading-img').fadeIn();
			var serializedReturn = newValues();
			 
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
		
			 //var data = {data : serializedReturn};
			var data = {
				type: 'submit',
				action: 'woo_ajax_post_action',
				data: serializedReturn
			};
			
			jQuery.post(ajax_url, data, function(response) {
				var success = jQuery('#woo-popup-save');
				var loading = jQuery('.ajax-loading-img');
				loading.fadeOut();  
				success.fadeIn();
				window.setTimeout(function(){
				   success.fadeOut(); 
				   
										
				}, 2000);
			});
			
			return false; 
			
		});   	 



}); // ready	 
	</script>
<?php
		break;
	}
} 
?>