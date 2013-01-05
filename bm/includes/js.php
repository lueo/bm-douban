<?php
// the file route but not the url
define ('__PATH__',str_replace("\\","/",WP_CONTENT_DIR)."/") ;

add_action('publish_post', 'build_cg_js', 0);
add_action('delete_post', 'build_cg_js', 0);
//add_action('private_to_publish', 'build_cg_js', 0);
add_action('edit_post', 'build_cg_js', 0);
/**
 * build_caogen_js
 */
function build_cg_js() {	
	$filename = __PATH__.get_option('cg_js_src');
	if($filename=='') 
		return;
	$handle = fopen ($filename,"w"); //open file or create the file if there is no file
	$content=get_cg_content();
   /*
check for writable
   */
   if (!is_writable ($filename)){
		die ("file:".$filename."is not writable, please check it.");
   }
   if (!fwrite ($handle,$content)){   //write content
		die ("file".$filename."can not be write.");
   }
   fclose ($handle); //close
  
   $message= 'update "'.$filename.'" success';
   return $message; 
}
function utf_to_gbk($content=""){
	return iconv("UTF-8","GB2312//IGNORE",$content);
}

function get_cg_content(){	
	global $options;
	global $post,$bm;
	$output = 'document.write(\'<div class=tab_content>';
	$getposts = new WP_query(); 
	if(!is_array($bm['dis_dhw'])) $bm['dis_dhw']=array();
	$query_args=array(
	   'post__not_in'=>$bm['dis_dhw'],		// exclude spcific ones
		'post_status'=>'publish',		 // only published post
	   'showposts'=>11
	   );
   $getposts->query($query_args); 
	global $wp_query; $wp_query->in_the_loop = true; $count = 0;// use $count to check whether is the first one.
	while ($getposts->have_posts()) : $getposts->the_post();	

		$title = utf_to_gbk($post->post_title);
		if(strlen($title)>32){
			$title = substr($title,0,32).'...';
		}
		$link = get_permalink();
		if( $count == 0 ) { 	//if $count==0 means this is the first article of this cat, this one will be show with a thumbnail
			$content=utf8Substr(get_the_excerpt(),0, 180);
			$content=str_replace("\n","",utf_to_gbk(strip_tags($content)));
			if(strlen($content)>78){
				$content=substr($content,0, 70).'...';// only when content is over will show the ... tag
			}
			 if(function_exists('the_thumb')) {	
				$img = the_thumb('num=1&width=100&height=70&display=false&gbk=true');
			}
			if($img==""&&get_option('caogen_js_pic')!=""){
					$img='<img src='.get_option('caogen_js_pic').' title='.$title.' />';
			}
			$output .= '<div class=thumb><a href='.$link.' target=_blank>'.$img.$content.'</a></div>';
		} else { 
			//other articles will be show in list
			$output .= '·<a href='.$link.' target=_blank>'.$title.'</a><br>';
		}
	$count ++; endwhile;
	$output .= '</div></div>\');';
	return $output;
}  //get_content

/**
 * Displays the Reaction Button admin menu
 */

add_action('admin_menu', 'add_cg_js_menu');
function add_cg_js_menu(){
	add_submenu_page(SHORTNAME,SHORTNAME, __('caogen js','bm'), 7,'build_cg_js','cg_js_submenu'); 
}

function cg_js_submenu() {
		
		// PROCESSING
		if(isset($_POST['delete'])) {	// delete js file
			$message = "delete success";
		} elseif(isset($_POST['add'])) {  // update or create js file
			$message = build_cg_js();
		} elseif(isset($_POST['save'])) {  // save settings
			update_option('cg_js_pic', $_POST['cg_js_pic']);
			update_option('cg_js_src', $_POST['cg_js_src']);
			$message = "save success!";
		}
			if($message!='') echo '<div class="updated fade msg" id="message"><p>'.$message.'</p></div>';
		// PROCESSING END
	
	?>
	
	<form action="" method="post" class="themeform">
<div class="caogen_js_pic">
		<h2><?php _e('build the js file for index page','bm'); ?></h2>
		<p>
			<label for="cg_js_pic"><?php _e('dafault picture setting for home page','bm'); ?><input  name="cg_js_pic" class="text" value="<?php echo get_option('cg_js_pic'); ?>"></label>
		</p>
		<p>
			<label for="cg_js_src"><?php _e('js dir:','bm');  ?><input  name="cg_js_src" class="text" value="<?php echo get_option('cg_js_src'); ?>"><?php echo '<strong>relative to " '.__PATH__.' "</strong>'; ?></label>
		</p>
</div>
<div class="clear"></div>
<div>
	<input type="submit" value="creat or update" name="add" class="button" />
	<input type="submit" value="save" name="save" class="button" />
	<input type="submit" value="delete" name="delete" class="button" />


	<h2><?php echo get_bloginfo('url').'/wp-content/'.get_option('cg_js_src'); ?></h2>
			<ul id="sliderContent">
				<?php 
				global $bm;
				$getposts = new WP_query(); 
				$dis_dhw = $bm['dis_dhw'];
				if(!is_array($dis_dhw)) $dis_dhw=array();
//	print_r($dis_dhw);
				$query_args=array(
				   'post__not_in'=>$dis_dhw,		// exclude spcific ones
					'post_status'=>'publish',		 // only published post
				   'showposts'=>11
				   );
				   $getposts->query($query_args); ?>
				<?php while ($getposts->have_posts()) : $getposts->the_post(); ?>		
				<li><h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2><?php print utf8Substr(get_the_excerpt(),0, 150); ?>
				</li>	
				<?php endwhile;wp_reset_query();  ?>
			</ul>

</div><!-- cptab end -->

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
