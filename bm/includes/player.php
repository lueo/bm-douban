<?php
/*
Name: bm-player
Description:  Insert Dewplayer (Flash Mp3 Player) in posts & comments.
*/

add_shortcode('audio', 'bmplayer_func');
function bmplayer_func($atts) {
	global $bm;
	$vars=$atts;
	$files = trim($vars['files']);
	if($bm['player']!=''){
		$dewvs = $bm['player']; 
	}elseif($vars['player']!=''){
		$dewvs = $vars['player']; 
	}else{
		$dewvs = get_option('dewplayer_dewvs'); 
	}
	$player_url = BMURL.'/swf/';
	if ($dewvs == 'Mini') {
		$player = 'dewplayer-mini.swf';
		$width="160";
		$height="20";
	} else if ($dewvs == 'Multi') {
		$player = 'dewplayer-multi.swf';
		$width="240";
		$height="20";
	}else if ($dewvs == 'Bubble') {
		$player = 'dewplayer-bubble.swf';
		$width="250";
		$height="65";
	} else if ($dewvs == 'Vinyl') {
		$player = 'dewplayer-vinyl.swf';
		$width="303";
		$height="113";
	} else {
		$player = 'dewplayer.swf';
		$width="200";
		$height="20";
	}
	$player_url .=$player.'?mp3='.$files;
	if($vars['titles']!='') $player_url .= '&titles='.$vars['titles'];
	if($vars['img']!='') $player_url .= '&images='.$vars['img'];

//		start output
	if($bm['player_div']&&$bm['player_div']!=''){?>
	<script type="text/javascript">
		var audi_swf = "<?php echo $player_url; ?>";
		swfobject.embedSWF(audi_swf, "<?php echo $bm['player_div']; ?>", "<?php echo $width; ?>", "<?php echo $height; ?>", "9.0.0", "expressInstall.swf"); 
	</script>
<?php
		return;
	}else{
		if($bm['swf']=='')$bm['swf']=0;
		$bm['swf']++;	?>
		<script type="text/javascript">
			var audi_swf = "<?php echo $player_url; ?>";
			swfobject.embedSWF(audi_swf, "audioid<?php echo $bm['swf']; ?>", "<?php echo $width; ?>", "<?php echo $height; ?>", "9.0.0", "expressInstall.swf"); 
		</script>
<?php
	}
	return '<div id="audioid'.$bm['swf'].'"></div>';
}


 // Admin Panel   
// Add Options Page
add_action('admin_menu', 'bmplayer_add_pages');
function bmplayer_add_pages(){
	add_submenu_page(SHORTNAME,SHORTNAME, __('bm player','bm'), 7,'bmplayer','bmplayer_options_page'); 
}

function bmplayer_options_page(){ 

	if (isset($_POST['submitted'])) 	{	

		$disp_posts = !isset($_POST['disp_posts'])? 'off': 'on';
		update_option('dewplayer_posts', $disp_posts);
		$disp_comments = !isset($_POST['disp_comments'])? 'off': 'on';
		update_option('dewplayer_comments', $disp_comments);
		$disp_link = !isset($_POST['disp_link'])? 'off': 'on';
		update_option('dewplayer_link', $disp_link);

		$dewvs= ($_POST['dewvs']=="")? 'classic': $_POST['dewvs'];
		update_option('dewplayer_dewvs', $dewvs);

		$dewbg= ($_POST['dewbg']=="")? 'FFFFFF': $_POST['dewbg'];
		update_option('dewplayer_dewbg', $dewbg);
		$dewtrans = !isset($_POST['dewtrans'])? '0': '1';	
		update_option('dewplayer_dewtrans', $dewtrans);
		$dewvolume = (int) ($_POST['dewvolume']=="")? '100': $_POST['dewvolume'];	
		update_option('dewplayer_dewvolume', $dewvolume);
		$dewstart = !isset($_POST['dewstart'])? '0': '1';
		update_option('dewplayer_dewstart', $dewstart);
		$dewreplay = !isset($_POST['dewreplay'])? '0': '1';
		update_option('dewplayer_dewreplay', $dewreplay);
		$dewrandomplay = !isset($_POST['dewrandomplay'])? '0': '1';
		update_option('dewplayer_dewrandomplay', $dewrandomplay);
		$dewshowtime = !isset($_POST['dewshowtime'])? '0': '1';
		update_option('dewplayer_dewshowtime', $dewshowtime);
		$dewnopointer = !isset($_POST['dewnopointer'])? '0': '1';
		update_option('dewplayer_dewnopointer', $dewnopointer);					

		$msg_status = 'Dewplayer options saved.';
						
		_e('<div id="message" class="updated fade"><p>' . $msg_status . '</p></div>');
			
	} 
		// vas me chercher le truc dans la base!
		$disp_link = (get_option('dewplayer_link')=='on') ? 'checked':'';		
		$disp_posts = (get_option('dewplayer_posts')=='on') ? 'checked' :'' ;
		$disp_comments = (get_option('dewplayer_comments')=='on') ? 'checked':'';
		$dewvs = get_option('dewplayer_dewvs');
		$dewbg = get_option('dewplayer_dewbg');
		$dewtrans = (get_option('dewplayer_dewtrans')=='1') ? 'checked':'';
		$dewvolume = get_option('dewplayer_dewvolume');
		$dewstart = (get_option('dewplayer_dewstart')=='1') ? 'checked':'';
		$dewreplay = (get_option('dewplayer_dewreplay')=='1') ? 'checked':'';
		$dewrandomplay = (get_option('dewplayer_dewrandomplay')=='1') ? 'checked':'';
		$dewshowtime = (get_option('dewplayer_dewshowtime')=='1') ? 'checked':'';
		$dewnopointer = (get_option('dewplayer_dewnopointer')=='1') ? 'checked':'';

		if ($dewbg=="") $dewbg="FFFFFF";
		if ($dewvolume=="") $dewvolume="100";
		if ($dewvs=="") $dewvs="classic";
	global $wp_version;	
	$actionurl=$_SERVER['REQUEST_URI'];
    // Configuration Page
    echo <<<END
<div class="wrap" style="max-width:950px !important;">
	<h2>Dewplayer $dewplayer_localversion</h2>
				
	<div id="poststuff" style="margin-top:10px;">
	
	<div id="sideblock" style="float:right;width:220px;margin-left:10px;"> 
		 <h3>Related</h3>

<div id="dbx-content" style="text-decoration:none;">
<ul>
<li><a style="text-decoration:none;" href="http://www.royakhosravi.com/?p=3">DewPlayer</a></li>
</ul><br />
</div>
 	</div>
	
	 <div id="mainblock" style="width:710px">
	 
		<div class="dbx-content">
		 	<form name="rkform" action="$action_url" method="post">
					<input type="hidden" name="submitted" value="1" /> 
						<h3>Usage</h3>                         
<p>Dewplayer Wordpress plugin allows you to insert DewPlayer (a free flash mp3 Player, under Creative Commons licence) in posts & comments and lets you listen to your favorite music online. Multiple files are separated by a pipe (<strong><font color="#FF0000">|</font></strong>).
Just copy Dewplayer code and paste it into your post or comment.</p>
<p>Usage : <strong><font color="#FF0000">[dewplayer:</font></strong>Path to your mp3 files on local or remote site<strong><font color="#FF0000">]</font></strong></p>

<p>Examples: <br>
<strong><font color="#FF0000">[dewplayer:</font></strong>http://www.mymusic.com/mysong.mp3<strong><font color="#FF0000">]</font></strong><br>
<strong><font color="#FF0000">[dewplayer:</font></strong>song1.mp3<strong><font color="#FF0000">|</font></strong>song2.mp3<strong><font color="#FF0000">|</font></strong>song3.mp3<strong><font color="#FF0000">]</font></strong></p>

<h3>Options</h3>
<p><strong>DewPlayer settings</strong></p>

<div><label for="dewvs">DewPlayer Version</label><br>
END;
$arr = array ("classic" => "Classic (200x20)","mini" => "Mini (160x20)","multi" => "Multi (240x20)","Bubble" => "Bubble (250x65)","Vinyl" => "Vinyl (303x113)");
foreach ($arr as $key => $value) {
	echo '&nbsp;&nbsp;&nbsp;<input type="radio" id="dewvs" name="dewvs" value="'.$key.'" ';
	if ($dewvs == $key) echo 'checked';
	echo ' />'.$value.'<br />';
}
    echo <<<END
</div>

<div><input id="check3" type="checkbox" name="disp_posts" $disp_posts />
<label for="check3">Display DewPlayer in posts</label></div>

<div><input id="check4" type="checkbox" name="disp_comments" $disp_comments />
<label for="check4">Display DewPlayer in comments</label></div>
<div><input id="check2" type="checkbox" name="disp_link" $disp_link />
<label for="check2">Display Mp3 link in RSS feed</label></div>

<br><br><strong>DewPlayer Appearence</strong><br><br>
<div><label for="dewbg">Background color  </label><input id="dewbg"  name="dewbg" value="$dewbg" size="7"/>&nbsp;&nbsp;
<label for="dewtrans">or transparent ? </label><input id="dewtrans" type="checkbox" name="dewtrans" $dewtrans />
</div>

<div><label for="dewvolume">Volume </label><input id="dewvolume"  name="dewvolume" value="$dewvolume" size="7"/>%</div>

<div><label for="dewstart">Auto start ? </label><input type="checkbox" id="dewstart" name="dewstart" $dewstart /></div>
<div><label for="dewreplay">Loop ? </label><input type="checkbox" id="dewreplay" name="dewreplay" $dewreplay /></div>
<div><label for="dewrandomplay">Random play ? </label><input type="checkbox" id="dewrandomplay" name="dewrandomplay" $dewrandomplay /></div>
<div><label for="dewshowtime">Time display (mm:ss) ? </label><input type="checkbox" id="dewshowtime" name="dewshowtime" $dewshowtime /></div>
<div><label for="dewnopointer">No cursor ? </label><input type="checkbox" id="dewnopointer" name="dewnopointer" $dewnopointer /></div>
<br>
<br>
<div class="submit"><input type="submit" name="Submit" value="Update options" /></div>
			</form>
		</div>
					
		<br/><br/><h3>&nbsp;</h3>	
	 </div>

	</div>
<h5>DewPlayer plugin by <a href="http://www.royakhosravi.com/">Roya Khosravi</a></h5>
</div>
END;
}

/**
 * Inserts quiky button into media library popup
 * @return the amended form_fields structure
 * @param $form_fields Object
 * @param $post Object
 */
add_filter("attachment_fields_to_edit",insertBmplayerButton, 10, 2);
add_filter("media_send_to_editor", BmplayerSendToEditor);
function insertBmplayerButton($form_fields, $post) {
	global $wp_version;
	
	$file = wp_get_attachment_url($post->ID);
	
	// Only add the extra button if the attachment is an mp3 file
	if ($post->post_mime_type == 'audio/mpeg') {
		$form_fields["url"]["html"] .= "<button type='button' class='button urlaudioplayer audio-player-" . $post->ID . "' value='[audio files=" . attribute_escape($file) . "]' title='[audio files=" . attribute_escape($file) . "]'>Audio Player</button>";
		
		if (version_compare($wp_version, "2.7", "<")) {
			$form_fields["url"]["html"] .= "<script type='text/javascript'>
			jQuery('button.audio-player-" . $post->ID . "').bind('click', function(){jQuery(this).siblings('input').val(this.value);});
			</script>\n";
		}
	}
	
	return $form_fields;
}

/**
 * Format the html inserted when the Audio Player button is used
 * @param $html String
 * @return String 
 */
function BmplayerSendToEditor($html) {
	if (preg_match("/<a ([^=]+=['\"][^\"']+['\"] )*href=['\"](\[audio files=([^\"']+\.mp3)])['\"]( [^=]+=['\"][^\"']+['\"])*>([^<]*)<\/a>/i", $html, $matches)) {
		$html = $matches[2];
		if (strlen($matches[5]) > 0) {
			$html = preg_replace("/]$/i", " titles=" . $matches[5] . "]", $html);
		}
	}
	return $html;
}
?>
