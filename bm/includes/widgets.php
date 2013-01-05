<?php

/**
 * comment
 */
class FooWidget extends WP_Widget {
    /** 构造函数 */
    function FooWidget() {
		$widget_ops = array('classname' => 'BmDisplayComment', 'description' => __( 'display comment details', 'bm') );
		$this->WP_Widget('FooWidget', __('BM display comment plus', 'bm'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$exclude_id = intval($instance['exclude_id']);
		$only_id = intval($instance['only_id']);
		echo $before_widget.$before_title.$title.$after_title;
		echo '<ul>'."\n";

		global $wpdb;
		$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,comment_date, comment_post_ID,comment_author,
		SUBSTRING(comment_content,1,50) AS com_excerpt
		FROM $wpdb->comments
		LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
		$wpdb->posts.ID)
		WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' ";
		if($exclude_id!=''){ $sql.=" AND comment_post_ID != $exclude_id ";	 }
		if($only_id!=''){ $sql.=" AND comment_post_ID = $only_id ";	 }
		$sql.="ORDER BY comment_date DESC	LIMIT 8";
		$comments = $wpdb->get_results($sql);
		$output = '';
		foreach ($comments as $comment) {
			$output .= "\n<li><a href=\"" . get_permalink($comment->ID) .
			"#comment-" . $comment->comment_ID . "\" title=\"".$comment->comment_author."在《 " .
			$comment->post_title . "》上说\">";
			if($only_id!=''){ 
				$output.="<font style=\"font-weight: 700;\">".bm_relativetime(strtotime($comment->comment_date))."：</font>";
			}else{
				$output.="<font style=\"font-weight: 700;\">".$comment->comment_author."：</font>";
			}
			$output.=strip_tags($comment->com_excerpt);
			$output.="...</a>";
		}
		echo $output;

		echo '</ul>'."\n";
		echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {		
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['exclude_id'] = intval($new_instance['exclude_id']);
		$instance['only_id'] = intval($new_instance['only_id']);
		return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Recent Comment Plus') );
        $title = esc_attr($instance['title']);
		$exclude_id = intval($instance['exclude_id']);
		$only_id = intval($instance['only_id']);
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude_id'); ?>"><?php _e('exclude_id:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude_id'); ?>" name="<?php echo $this->get_field_name('exclude_id'); ?>" type="text" value="<?php echo $exclude_id; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('only_id'); ?>"><?php _e('only_id:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('only_id'); ?>" name="<?php echo $this->get_field_name('only_id'); ?>" type="text" value="<?php echo $only_id; ?>" /></label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
        <?php 
    }

} // class FooWidget
add_action('widgets_init', create_function('', 'return register_widget("FooWidget");'));



/**
 * tag lists
 */
class BmTagWidget extends WP_Widget {
    /** 构造函数 */
    function BmTagWidget() {
		$widget_ops = array('classname' => 'BmTagWidget', 'description' => __( 'display tags as a list', 'bm') );
		$this->WP_Widget('BmTagWidget', __('Bm Tag Widget', 'bm'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		echo $before_widget.$before_title.$title.$after_title;
		echo '<ul>'."\n";

		$amount 	= $instance['amount'];
		if ($amount ==''||$amount==0) 
			$amount = 10;

		$html = '';
		foreach (get_tags( array('number' => $amount , 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => false) ) as $tag){
				$tag_link = get_tag_link($tag->term_id);
				$html .= "<li><a href='{$tag_link}' title='{$tag->name} Tag' class='{$tag->slug}'>";
				$html .= "{$tag->name} ({$tag->count})</a></li>";
		}
		echo $html;

		echo '</ul>'."\n";
		echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {		
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['amount'] = strip_tags($new_instance['amount']);
		return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Tags List', 
            'amount' => '10') );
        $title = esc_attr($instance['title']);
        $amount = esc_attr($instance['amount']);
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('amount:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" type="text" value="<?php echo $amount; ?>" /></label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
        <?php 
    }

} // class FooWidget
add_action('widgets_init', create_function('', 'return register_widget("BmTagWidget");'));

 // reader list
class BmReadersWidget extends WP_Widget {
    /** 构造函数 */
    function BmReadersWidget() {
		$widget_ops = array('classname' => 'BmReadersWidget', 'description' => __( 'face list of readers', 'bm') );
		$this->WP_Widget('BmReadersWidget', __('Bm Readers Widget', 'bm'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
		global $wpdb;
        extract( $args );
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		echo $before_widget.$before_title.$title.$after_title;
		echo '<ul>';

		$amount 	= $instance['amount'];
		$exclude_users 	= $instance['exclude_users'];
		$time_limit 	= ($instance['time_limit']==0)?1:$instance['time_limit'];
		$time_limit_type 	= ($instance['time_limit_type']=="")?"MONTH":$instance['time_limit_type'];
		if ($amount ==''||$amount==0) 
			$amount = 9;

		$sql="SELECT COUNT(comment_author) AS cnt, comment_author, comment_author_url, comment_author_email 
		FROM (SELECT * FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->posts.ID=$wpdb->comments.comment_post_ID) 
		WHERE comment_date > date_sub( NOW(), INTERVAL $time_limit $time_limit_type ) 
		AND post_password='' 
		AND comment_approved='1' ";
		if ($exclude_users !='') {
			$exclude_users=explode(',',$exclude_users);
			$exclude_users_sql='';
			$end = count ($exclude_users)-1;
			foreach($exclude_users as $k=>$exclude_user){
				$mark="";
				if( $end > $k ){
					$mark=" , ";
				}
				$exclude_users_sql .= " '".$exclude_user."' ".$mark;
				$i++;
			}
			$sql .= "  AND comment_author NOT IN ( $exclude_users_sql ) ";
		}
		$sql .="	AND comment_type='')  
		AS tempcmt GROUP BY comment_author 
		ORDER BY cnt DESC 
		LIMIT $amount";

		$counts = $wpdb->get_results($sql);
		$mostactive='';
		foreach ($counts as $count) {
			$c_url = $count->comment_author_url;
			if ($c_url == "") $c_url = get_option('home');
			$mostactive .= '<li><a href="'.$c_url.'" title="' . $count->comment_author . ' ('. $count->cnt . 'comments)">' . get_avatar($count->comment_author_email, 60) . '<span class="title">'.$count->comment_author .'</span></a>
		</li>';
		}
		echo $mostactive;
		echo '</ul>'."\n";
		echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {		
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['time_limit']	= (int) $new_instance['time_limit'];
		$instance['time_limit_type']	= $new_instance['time_limit_type'];
		$instance['amount'] = strip_tags($new_instance['amount']);
		$instance['exclude_users'] = strip_tags($new_instance['exclude_users']);
		return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Reader Wall', 
            'time_limit' => '1',
            'time_limit_type' => 'MONTH', 
            'amount' => '9') );
        $title = esc_attr($instance['title']);
        $time_limit = esc_attr($instance['time_limit']);
        $time_limit_type = esc_attr($instance['time_limit_type']);
        $amount = esc_attr($instance['amount']);
        $exclude_users = esc_attr($instance['exclude_users']);
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<?php _e('time limit settings:','bm'); ?><br />
			<label for="<?php echo $this->get_field_id('time_limit'); ?>">
			<input style="width: 50px;" id="<?php echo $this->get_field_id('time_limit'); ?>" name="<?php echo $this->get_field_name('time_limit');?>" type="text" value="<?php echo $time_limit; ?>" />
			</label>
			<select id="<?php echo $this->get_field_id('time_limit_type'); ?>" name="<?php echo $this->get_field_name('time_limit_type'); ?>" >
				<option <?php selected("week" , $instance['time_limit_type']); ?> value="week"><?php _e('week(s)','bm'); ?></option>
				<option <?php selected("month" , $instance['time_limit_type']); ?> value="month"><?php _e('month(s)','bm'); ?></option>
				<option <?php selected("year" , $instance['time_limit_type']); ?> value="year"><?php _e('year(s)','bm'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude_users'); ?>"><?php _e('exclude users\' name: (use , to sperate 2 or more users)','bm'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude_users'); ?>" name="<?php echo $this->get_field_name('exclude_users'); ?>" type="text" value="<?php echo $exclude_users; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('faces amount:','bm'); ?> <input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" type="text" value="<?php echo $amount; ?>" /></label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
        <?php 
    }

} // class FooWidget
add_action('widgets_init', create_function('', 'return register_widget("BmReadersWidget");'));

class bmNggWidget extends WP_Widget {
    
   	function bmNggWidget() {
		$widget_ops = array('classname' => 'bm_ngg_images', 'description' => __( 'display images from galleries', 'bm') );
		$this->WP_Widget('bm-ngg-images', __('BM NextGEN Widget', 'bm'), $widget_ops);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']	= strip_tags($new_instance['title']);
		$instance['items']	= (int) $new_instance['items'];
		$instance['width']	= (int) $new_instance['width'];
		$instance['height']	= (int) $new_instance['height'];

		return $instance;
	}

	function form( $instance ) {
		
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Gallery', 
            'items' => '4',
            'height' => '70', 
            'width' => '100') );
		$title  = esc_attr( $instance['title'] );
		$items  = intval  ( $instance['items'] );
        $height = esc_attr( $instance['height'] );
		$width  = esc_attr( $instance['width'] );

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :','nggallery'); ?>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title');?>" type="text" class="widefat" value="<?php echo $title; ?>" />
			</label>
		</p>
			
		<p>
			<?php _e('Show :','nggallery'); ?><br />
			<label for="<?php echo $this->get_field_id('items'); ?>">
			<input style="width: 50px;" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items');?>" type="text" value="<?php echo $items; ?>" />
		</p>

		<p>
			<?php _e('Width x Height :','nggallery'); ?><br />
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" /> x
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" /> (px)
		</p>
		
	<?php
	
	}

	function widget( $args, $instance ) {
		extract( $args );
        
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title'], $instance, $this->id_base);

		global $wpdb;
				
		$items 	= $instance['items'];

		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->nggpictures WHERE exclude != 1 ");
		if ($count < $instance['items']) 
			$instance['items'] = $count;
		
		$imageList = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 ORDER by rand() limit {$items}");
		                      
		echo $before_widget . $before_title . $title . $after_title;
		echo "\n" . '<div class="ngg-widget entry-content">'. "\n";
	
		if (is_array($imageList)){
			foreach($imageList as $image) {
				// get the URL constructor
				$image = new nggImage($image);

				// get the effect code
				$thumbcode = $image->get_thumbcode( $widget_id );
				
				// enable i18n support for alttext and description
				$alttext      =  htmlspecialchars( stripslashes( nggGallery::i18n($image->alttext) ));
				$description  =  htmlspecialchars( stripslashes( nggGallery::i18n($image->description) ));
				$title  =  "【".htmlspecialchars( stripslashes( nggGallery::i18n($image->title) ))."】";
				
				//TODO:For mixed portrait/landscape it's better to use only the height setting, if widht is 0 or vice versa
				$out = '<a href="' . $image->imageURL . '" title="' . $title.$description . '" ' . $thumbcode .'>';

				$out .= '<img src="'.$image->thumbURL.'" width="'.$instance['width'].'" height="'.$instance['height'].'" title="'.$alttext.'" alt="'.$alttext.'" />';			
				
				echo $out . '</a>'."\n";
				
			}
		}
		
		echo '</div>'."\n";
		echo $after_widget;
		
	}

}// end widget class

// register it
if(class_exists("nggGallery"))
	add_action('widgets_init', create_function('', 'return register_widget("bmNggWidget");'));

### Class: WP-Polls Widget
 class Bm_WP_Widget_Polls extends WP_Widget {
	// Constructor
	function Bm_WP_Widget_Polls() {
		$widget_ops = array('description' => __('show WP-Polls polls plus', 'bm'));
		$this->WP_Widget('bm-polls-widget', __('BM Polls', 'bm'), $widget_ops);
	}

	// Display Widget
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		$poll_id = intval($instance['poll_id']);
		$display_pollarchive = intval($instance['display_pollarchive']);
		echo $before_widget;
		if(!empty($title)) {
			echo $before_title.$title.$after_title;
		}
		get_poll($poll_id);	
		if($display_pollarchive) {
			display_polls_archive_link();
		}
		echo $after_widget;
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['poll_id'] = intval($new_instance['poll_id']);
		$instance['display_pollarchive'] = intval($new_instance['display_pollarchive']);
		return $instance;
	}

	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Polls', 'bm'), 'poll_id' => 0, 'display_pollarchive' => 1));
		$title = esc_attr($instance['title']);
		$poll_id = intval($instance['poll_id']);
		$display_pollarchive = intval($instance['display_pollarchive']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bm'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('display_pollarchive'); ?>"><?php _e('Display Polls Archive Link Below Poll?', 'bm'); ?>
				<select name="<?php echo $this->get_field_name('display_pollarchive'); ?>" id="<?php echo $this->get_field_id('display_pollarchive'); ?>" class="widefat">
					<option value="0"<?php selected(0, $display_pollarchive); ?>><?php _e('No', 'bm'); ?></option>
					<option value="1"<?php selected(1, $display_pollarchive); ?>><?php _e('Yes', 'bm'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('poll_id'); ?>"><?php _e('Poll To Display:', 'bm'); ?>
				<select name="<?php echo $this->get_field_name('poll_id'); ?>" id="<?php echo $this->get_field_id('poll_id'); ?>" class="widefat">
					<option value="-1"<?php selected(-1, $poll_id); ?>><?php _e('Do NOT Display Poll (Disable)', 'bm'); ?></option>
					<option value="-2"<?php selected(-2, $poll_id); ?>><?php _e('Display Random Poll', 'bm'); ?></option>
					<option value="0"<?php selected(0, $poll_id); ?>><?php _e('Display Latest Poll', 'bm'); ?></option>
					<optgroup>&nbsp;</optgroup>
					<?php
					$polls = $wpdb->get_results("SELECT pollq_id, pollq_question FROM $wpdb->pollsq ORDER BY pollq_id DESC");
					if($polls) {
						foreach($polls as $poll) {
							$pollq_question = stripslashes($poll->pollq_question);
							$pollq_id = intval($poll->pollq_id);
							if($pollq_id == $poll_id) {
								echo "<option value=\"$pollq_id\" selected=\"selected\">$pollq_question</option>\n";
							} else {
								echo "<option value=\"$pollq_id\">$pollq_question</option>\n";
							}
						}
					}
					?>
				</select>
			</label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}

if(function_exists("get_poll"))
	add_action('widgets_init', create_function('', 'return register_widget("Bm_WP_Widget_Polls");'));
?>