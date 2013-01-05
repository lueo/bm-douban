<?php

/********************************************************************
辩论函数
debate_list() 输出正反中立三方的辩论列表
get_debate_title() 输出辩论者的楼层及反驳对象等
chooseside()	在评论框中出现选择辩论立场的radio
add_side_meta() 存储辩论立场
debate_side_check() 在comment-ajax.php 设置 debate_side 为必填项目
********************************************************************/

function debate_list($comment_array,$stand,$short=false,$amount=0) {
	global $debate,$post;
	if(!is_array($comment_array)) return;
	$comment_array = array_reverse($comment_array, TRUE);
	$i=0;
	foreach($comment_array as $com_id=>$floor){
		$i++;	
		if($amount!=0&&$i>$amount)
			return;
		$comment = get_comment($com_id);
		$GLOBALS['comment'] = $comment; 
		$this_stand = get_comment_meta($comment->comment_ID,"debate_side",true);
		if($this_stand==$stand){ ?>
			<li id="comment-<?php comment_ID(); ?>" <?php if($comment->comment_parent=='0')comment_class();else comment_class("reply"); ?> id="li-comment-<?php comment_ID() ?>">
				<div class="comment-author">
					<CITE><?php echo get_debate_title($comment->comment_parent,$this_stand,$comment->comment_ID); ?></CITE><br>
					<SMALL>(<?php print get_comment_date(); ?>)</SMALL>
					<span class="replay-button" style="display:none;"><?php edit_comment_link(__('Edit'),'  ',' | ') ?> <?php comment_reply_link(array('depth' => 2,'max_depth' => '20', 'reply_text' =>'回应')) ?></span>
				</div>
				<div class="comment-content">
					<?php if($short){ print utf8Substr(get_comment_excerpt(),0, 80,true);}else{ comment_text();} ?>
				</div>
			</li>
		<?php 
		}
	}
}

function get_debate_title($parent_id,$this_stand,$this_id){
	global $debate;
	if($parent_id!="0"){
		$parent_stand = get_comment_meta($parent_id,"debate_side",true);
		$parent_author = get_comment_author($parent_id); 
		if($parent_stand!=$this_stand){
			if($parent_stand=="1"){
				$reply_title = "<a class=\"red\" href=\"#comment-".$parent_id."\" title=\"".$parent_id."\"> 反驳：正方".$debate['dabete_obverse'][$parent_id]."辩 ".$parent_author."</a>";
			}elseif($parent_stand=="0"){
				$reply_title = "<a class=\"red\" href=\"#comment-".$parent_id."\" title=\"".$parent_id."\"> 反驳：反方".$debate['dabete_reverse'][$parent_id]."辩 ".$parent_author."</a>";
			}else{
				$reply_title = "<a class=\"red\" href=\"#comment-".$parent_id."\" title=\"".$parent_id."\"> 反驳：中立".$debate['dabete_other'][$parent_id]."辩 ".$parent_author."</a>";
			}
		}else{
			if($parent_stand=="1"){
				$reply_name = "正方".$debate["dabete_obverse"][$parent_id];
			}elseif($parent_stand=="0"){
				$reply_name = "正方".$debate["dabete_reverse"][$parent_id];
			}else{
				$reply_name = "正方".$debate["dabete_other"][$parent_id];
			}
			$reply_title = "<a class=\"red\" href=\"#comment-".$parent_id."\" title=\"".$parent_id."\"> 回复".$reply_name."辩 ".$parent_author."</a>";
		}
	}
	$title='<div class="author-title">';
	if($this_stand=="1")
		$title.='正方 <span class="red">'.$debate['dabete_obverse'][$this_id]."</span> 辩";
	elseif($this_stand=="0")
		$title.='反方 <span class="red">'.$debate['dabete_reverse'][$this_id]."</span> 辩 ";
	else 
		$title.='中立 <span class="red">'.$debate['dabete_other'][$this_id]."</span> 辩 ";
	$title.='  '.get_comment_author_link().'</div>'.$reply_title;
	return $title;
}

add_action('comment_form', 'chooseside',1);
function chooseside(){
	global $post,$bm;
	if(!in_category($bm['topic']))
		return;
	// 中立值为other是为了方便comments-ajax.php检测
	echo '<p class="debate-stand-buttons">
			<span><label for="obverse" class="stand-buttons">正方</label><input type="radio" name="debate_side" id="obverse" value="1" /></span>
			<span><label for="reverse" class="stand-buttons">反方</label><input type="radio" name="debate_side" id="reverse" value="0" /></span>
			<span><label for="other" class="stand-buttons">中立</label><input type="radio" name="debate_side" id="other" value="other" /></span>
		</p>';
}

add_action ('wp_footer', 'add_debate_js');
function add_debate_js() {
	global $post,$bm;
	if(in_category($bm['topic'])){
?>
	<script type="text/javascript">
	<!--
	$(document).ready(function(){
 
		// animate
		$('.red').click(function(){	
			var light_comment=$(this).attr('title');
			$('#comment-'+light_comment).fadeOut().fadeIn().fadeOut().fadeIn();
		});

		// stand label change background when click
		$('.debate-stand-buttons span label').click(function(){
			var others = $('.debate-stand-buttons span label');
			others.removeClass("stand-button-lightup" );
			var obj = $(this);
			obj.addClass("stand-button-lightup");
		});
	});
	//-->
	</script>
<?php
	}
}

add_action ('comment_post', 'add_side_meta', 1);
function add_side_meta($comment_id) {
	// 去掉值为other 的存储
	if($_POST['debate_side']!=''&&$_POST['debate_side']!='other')
	add_comment_meta($comment_id, 'debate_side', $_POST['debate_side'], true);
}


// 位于 commments-ajax.php 中
function debate_side_check() {
	$comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
	global $bm;
	if(	!in_category($bm['topic'],$comment_post_ID))
		return;
	$debate_side      = ( isset($_POST['debate_side']) ) ? trim($_POST['debate_side']) : null;
	if($debate_side ==null  ) 
	err(__('Error: please select a side.','bm')); // 此处为草根辩论专用判断
}
?>