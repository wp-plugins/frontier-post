<?php
/**
 * 
 * Show My Posts Widget
 */
class frontier_my_posts_widget extends WP_Widget 
	{
	
	var $defaults;
	
    /** constructor */
    function frontier_my_posts_widget() 
		{
	
		$this->defaults = array(
    		'title' 			=> __('My posts','frontier-post'),
    		'post_type' 		=> 'post',
			'post_status' 		=> 'publish',
			'order'				=> 'DESC',
			'orderby' 			=> 'post_date', 	
			'limit' 			=> 8,
    		'category' 			=> 0,
    		'postdateformat'	=> 'd/m',
			'cmtdateformat'		=> 'd/m',
			'showcomments'		=> 'posts',
    		'nolistwrap' 		=> false,
    		'no_posts_text' 	=> __('You have no posts', 'frontier-post'),
			'show_add_post'		=> 1,
			'show_post_count'	=> 1,
			'excerpt_length'	=>50,
			'fp_cache_time'		=> FRONTIER_POST_CACHE_TIME,
    		
			);

		// dropdown of date format for posts using (mysql2date function)
		$this->frontier_widget_date_format = array(
			'd/m-y' 	=> 'd/m-y',
			'd/m' 		=> 'd/m',
			'm/d' 		=> 'm/d',
			'm/d-y' 	=> 'm/d-y',
			'y-m-d' 	=> 'y-m-d',
			'nodate'	=> __('Dont show date', 'frontier-post'),
			);
				
		$this->frontier_widget_show_comments = array(
			'posts' 			=> __('Only posts', 'frontier-post'),
			'comments' 			=> __('Posts & comments ', 'frontier-post'),
			'excerpts' 			=> __('Posts and comments excerpt', 'frontier-post'),
			);
		
		
    	$widget_ops = array('description' => __( "List posts of current user (author)", 'frontier-post') );
        parent::WP_Widget(false, $name = 'Frontier My Posts', $widget_ops);	
		}

    /** @see WP_Widget::widget */
    function widget($args, $instance) 
	{
	
	if(is_user_logged_in())
		{
		
		
		global $current_user, $wpdb, $r;
		
    	$instance 			= array_merge($this->defaults, $instance);
    	$author				= (int) $current_user->ID;
		$rec_limit			= (int) (isset($instance['limit']) ? $instance['limit'] : 10);
		$excerpt_length		= (int) (isset($instance['excerpt_length']) ? $instance['excerpt_length'] : 20);
		
		if (isset( $instance['postdateformat'] ) && $instance['postdateformat'] != 'nodate' )
			$show_date 			= true;
		else
			$show_date 			= false;
		
		if (isset( $instance['cmtdateformat'] ) && $instance['cmtdateformat'] != 'nodate' )
			$show_comment_date 	= true;
		else
			$show_comment_date 	= false;
		
		if (isset( $instance['showcomments'] ) && $instance['showcomments'] != 'posts' )
			$show_comments 		= true;
		else
			$show_comments 		= false;
		
		// Get comment icon from theme, first check local file path, if exists set tu url of icon
		$comment_icon	= frontier_get_icon('comment');
		
		// from version 3.4.6 caching will be available, and as such changed to handle in one array.
		
		// cache name must contain author id as results are specific to authors
		$fp_cache_name		= $args['widget_id']."_fpuser_".$author;
		$fp_cache_time		= $instance['fp_cache_time'];
		$fp_cache_test		= "Cache active";
		
		if ( ($fp_cache_time <= 0) || (false === ($fp_wdata = get_transient($fp_cache_name))) )
			{
			$fp_wdata 			= array();

			$fp_wdata['tmp_post_cnt']	= $wpdb->get_var("SELECT count(ID) AS tmp_post_cnt FROM $wpdb->posts WHERE post_author = ".$author." AND post_status = 'publish' AND post_type = 'post'" );
						
		
			// Build sql statement	
			if ($show_comments)
				{
				$tmp_sql = " SELECT 
							 $wpdb->posts.ID 					AS post_id, 
							 $wpdb->posts.post_title 			AS post_title, 
							 $wpdb->posts.post_date 			AS post_date, 
							 $wpdb->comments.comment_ID 		AS comment_id, 
							 $wpdb->comments.comment_author 	AS comment_author,
							 $wpdb->comments.comment_date 		AS comment_date,
							 $wpdb->comments.comment_approved	AS comment_approved,
							 $wpdb->comments.comment_content 	AS comment_content 
								 FROM $wpdb->posts 
								 left OUTER JOIN $wpdb->comments ON 
									 $wpdb->posts.ID = $wpdb->comments.comment_post_ID 
								 WHERE $wpdb->posts.post_status = 'publish' 
								 AND $wpdb->posts.post_type 	= 'post'  
								 AND $wpdb->posts.post_author 	= ".$author."
								 ORDER BY $wpdb->posts.post_date DESC, $wpdb->comments.comment_date_gmt DESC 
								 LIMIT ".$rec_limit*5;
				}
			else
				{
				$tmp_sql = " SELECT $wpdb->posts.ID 	AS post_id, 
							 $wpdb->posts.post_title 	AS post_title, 
							 $wpdb->posts.post_date 	AS post_date 
							 FROM $wpdb->posts 
							 WHERE $wpdb->posts.post_author = ".$author." AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post'  
							 ORDER BY $wpdb->posts.post_date DESC 
							 LIMIT ".$rec_limit*5;
							 // needs to multiply to account for non approved comments
				}
			
			
			$fp_wdata['presult'] 	= $wpdb->get_results($tmp_sql);
		
			if ($fp_cache_time <=0 )
				{
				$fp_cache_test		= "Caching disabled";				
				}
			else
				{
				$fp_cache_test		= "Cache refreshed";				
				set_transient($fp_cache_name, $fp_wdata, $fp_cache_time); 
				} 
		} // end caching		
		
		
		echo $args['before_widget'];
    	if( !empty($instance['title']) )
			{
		    echo $args['before_title'];
		    echo $instance['title'];
		    echo $args['after_title'];
			}
    	
		//echo $args['before_widget'];
		//if ( $args['title'] ) echo $args['before_title'] . $args['title'] . $args['after_title']; 
		//$title = apply_filters('widget_title', empty($instance['title']) ? __('My posts') : $instance['title'], $instance, $this->id_base);
		?>
		
		
		
		
		<div  class="frontier-my-post-widget">
		<ul>
		
		
		<?php 
		$last_post 	= 0;
		$post_cnt	= 0;
		if ( $fp_wdata['presult'] ) 
			{
			foreach ( $fp_wdata['presult'] as $post)
				{
				$tmp_link = "xx";
				if ( $last_post != $post->post_id )
					{ 
					if ($post_cnt >0)
						echo "</li>";
				
					echo "<li>";
				
				
					$post_cnt++;
					if ($show_date)
						{
						echo mysql2date($instance['postdateformat'], $post->post_date); 
						echo '&nbsp;&nbsp;';
						}
					?>
					<a href="<?php echo post_permalink($post->post_id);?>"><?php echo $post->post_title;?></a>
					<?php
					}
					
					$last_post = $post->post_id;
					if ($show_comments && (!empty($post->comment_id)) && ($post->comment_approved == 1))
						{
						echo "</br>".$comment_icon."&nbsp;&nbsp;";
						if ($show_comment_date)
							echo mysql2date($instance['cmtdateformat'], $post->comment_date)." - ";
						echo $post->comment_author; 
						if ( $instance['showcomments'] == 'excerpts' )
							{
							$tmp_comment = substr($post->comment_content, 0, $excerpt_length);
							if (strlen($post->comment_content) > strlen($tmp_comment))
								$tmp_comment = $tmp_comment."...";
							
							echo ":&nbsp"."</br><i>".$tmp_comment."</i>"; 
							}
						}
						
					if ($post_cnt >= $rec_limit)
						{
						break;
						}
				}
			 
			}
			else
			{
				echo "<li>".$instance['no_posts_text']."</li>";
			}
		?>
		</li>
		</ul>
		<?php 
		if (isset($instance['show_add_post']) && $instance['show_add_post'] == 1 && (current_user_can('frontier_post_can_add')))
			{ 
			echo '<p><center><a href="'.frontier_post_add_link().'">'.__("Create New Post", "frontier-post").'</a></center></p>';
			} 
		
		// Count authors posts - get_permalink(fp_get_option('fps_page_id'))
		if (isset($instance['show_post_count']) && $instance['show_post_count'] == 1 )
			{ 
			//$tmp_post_cnt	= $wpdb->get_var("SELECT count(ID) AS tmp_post_cnt FROM $wpdb->posts WHERE post_author = ".$author." AND post_status = 'publish' AND post_type = 'post'" );
			$tmp_post_cnt	= $fp_wdata['tmp_post_cnt'];
			echo '<p><center><a href="'.get_permalink(fp_get_option('fps_page_id')).'">'.__("You have published: ", "frontier-post").$tmp_post_cnt.'&nbsp;'.__("posts", "frontier-post").'</a></center></p>';
			}		
		
		
		?>
		</div>
		<?php

		
		
		echo $args['after_widget'];
		}
	else // If not logged in
		{
		// echo "<p>".__("You need to login to see your posts", "frontier-post")."</p>";
		}
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) 
		{
		$tmp_boolean_fields = array('show_add_post', 'show_post_count');
    	foreach($this->defaults as $key => $value)
			{
    		if( !isset($new_instance[$key]) )
				{
				//check if is one of the logical fields (checkbox) and set value to false, so it isnt empty...
				if (in_array($key, $tmp_boolean_fields))
					$new_instance[$key] = false;
				else
					$new_instance[$key] = $value;
				
				}
			}
    	global $current_user;
		
		// Delete cache
    	//$author				= (int) $current_user->ID;
		// cache name must contain author id as results are specific to authors
		//$fp_cache_name		= $args['widget_id']."_fpuser_".$author;
    	//delete_transient($args[$fp_cache_name]);
    	
    	return $new_instance;
    	
		}

    /** @see WP_Widget::form */
    function form($instance) 
	{
    	$instance = array_merge($this->defaults, $instance);
    	include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
       
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('postdateformat'); ?>"><?php _e('Post date format','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('postdateformat'); ?>" name="<?php echo $this->get_field_name('postdateformat'); ?>">
				<?php foreach($this->frontier_widget_date_format as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['postdateformat']) && $key == $instance['postdateformat']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('showcomments'); ?>"><?php _e('Show comments','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('showcomments'); ?>" name="<?php echo $this->get_field_name('showcomments'); ?>">
				<?php foreach($this->frontier_widget_show_comments as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['showcomments']) && $key == $instance['showcomments']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cmtdateformat'); ?>"><?php _e('Comment date format','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('cmtdateformat'); ?>" name="<?php echo $this->get_field_name('cmtdateformat'); ?>">
				<?php foreach($this->frontier_widget_date_format as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['cmtdateformat']) && $key == $instance['cmtdateformat']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of posts & comments','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" size="3" value="<?php echo esc_attr($instance['limit']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Length of comment excerpt','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" size="3" value="<?php echo esc_attr($instance['excerpt_length']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_add_post'); ?>"><?php _e('Show Add Post link ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_add_post'); ?>" name="<?php echo $this->get_field_name('show_add_post'); ?>" value="1" <?php echo ($instance['show_add_post'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_post_count'); ?>"><?php _e('Show Post Count ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_post_count'); ?>" name="<?php echo $this->get_field_name('show_post_count'); ?>" value="1" <?php echo ($instance['show_post_count'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('no_posts_text'); ?>"><?php _e('No post text','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('no_posts_text'); ?>" name="<?php echo $this->get_field_name('no_posts_text'); ?>" value="<?php echo (!empty($instance['no_posts_text'])) ? $instance['no_posts_text']:__('You have no posts', 'frontier-post'); ?>" >
		</p>
		
		</p>
			<label for="<?php echo $this->get_field_id('fp_cache_time'); ?>"><?php _e('Cache time ?', 'frontier-post'); ?>: </label>
		
		<!--$fp_cache_time_list-->
		<?php
			$tmp_html = '<select name="'.$this->get_field_name('fp_cache_time').'" >';
			foreach($fp_cache_time_list as $key => $value) :    
				$tmp_html = $tmp_html.'<option value="'.$key.'"';
				if ( $key == $instance['fp_cache_time'] )
					$tmp_html = $tmp_html.' selected="selected"';
		
				$tmp_html = $tmp_html.'>'.$value.'</option>';	
			endforeach;
			$tmp_html = $tmp_html.'</select>';
			echo $tmp_html; 
		?>
		
		</p>
        <?php 
    }
    
}    
add_action('widgets_init', create_function('', 'return register_widget("frontier_my_posts_widget");'));
?>