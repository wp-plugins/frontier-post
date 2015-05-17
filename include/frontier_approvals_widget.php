<?php
/**
 * 
 * Show approvals (only admins)
 */
class frontier_approvals_widget extends WP_Widget 
	{
	
	var $defaults;
	
    /** constructor */
    function frontier_approvals_widget() 
		{
	
		$this->defaults = array(
    		'title' 			=> __('My approvals','frontier-post'),
			'show_draft'		=> false,
			'show_pending'		=> true,
			'show_comments'		=> false,
			'show_comment_spam'	=> true,
    		'nolistwrap' 		=> true,
    		'fp_cache_time'		=> FRONTIER_POST_CACHE_TIME,
    		'no_approvals_text'	=> __('You have no approvals pending', 'frontier-post'),
			);

		
		
    	$widget_ops = array('description' => __( "List number of posts and comments for approval", 'frontier-post') );
        parent::WP_Widget(false, $name = 'Frontier My Approvals', $widget_ops);	
		}

    /** @see WP_Widget::widget */
    function widget($args, $instance) 
	{
	
	if(is_user_logged_in() && current_user_can("edit_others_posts"))
		{
		$instance 			= array_merge($this->defaults, $instance);
		
		// from version 3.4.6 caching will be available, and as such changed to handle in one array.
		$fp_cache_name		= $args['widget_id'];
		$fp_cache_time		= $instance['fp_cache_time'];
		$fp_cache_test		= "Cache active";
		global $wpdb;
		
    	
	
		//error_log(print_r($instance), true);
	
		if ( ($fp_cache_time <= 0) || (false === ($fp_wdata = get_transient($fp_cache_name))) )
			{
			$fp_wdata 		= array();
			if (isset( $instance['show_draft'] ) && $instance['show_draft'] == true )
				{
				$fp_wdata['draft_cnt']	= $wpdb->get_var("SELECT count(id) AS draft_cnt FROM $wpdb->posts WHERE post_status = 'draft'");
				$fp_wdata['draft_txt']	= $fp_wdata['draft_cnt'].' '.__('draft posts','frontier-post');
				$fp_wdata['show_draft']	= true;
				}
			else
				{
				$fp_wdata['show_draft']	= false;
				}
		
			if (isset( $instance['show_pending'] ) && $instance['show_pending'] == true )
				{
				$fp_wdata['pending_cnt']	= $wpdb->get_var("SELECT count(id) AS pending_cnt FROM $wpdb->posts WHERE post_status = 'pending'");
				$fp_wdata['pending_txt']	= $fp_wdata['pending_cnt'].' '.__('posts to be approved','frontier-post');
				$fp_wdata['show_pending']	= true;
				}
			else
				{
				$fp_wdata['show_pending']	= false;
				}
		
			if (isset( $instance['show_comments'] ) && $instance['show_comments'] == true )
				{
				$fp_wdata['cmt_pending_cnt']	= $wpdb->get_var("SELECT count(comment_ID) AS cmt_pending_cnt FROM $wpdb->comments WHERE comment_approved = 0");
				$fp_wdata['cmt_pending_txt']	= $fp_wdata['cmt_pending_cnt'].' '.__('comments to be approved','frontier-post');
				$fp_wdata['show_comments'] 		= true;
				}
			else
				{
				$fp_wdata['show_comments']		= false;
				}
		
			if (isset( $instance['show_comment_spam'] ) && $instance['show_comment_spam'] == true )
				{
				$fp_wdata['cmt_spam_cnt']		= $wpdb->get_var("SELECT count(comment_ID) AS cmt_pending_cnt FROM $wpdb->comments WHERE comment_approved = 'spam'");
				$fp_wdata['cmt_spam_txt']		= $fp_wdata['cmt_spam_cnt'].' '.__('spam comments','frontier-post');
				$fp_wdata['show_comment_spam'] 	= true;
				}
			else
				{
				$fp_wdata['show_comment_spam']	= false;
				}
			
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
    	
		?>
		
		
		
		
		<div  class="frontier-my-post-widget">
		<ul>
			
			
			<?php if ($fp_wdata['show_pending']) 
				{ 
				if (fp_get_option_int('fps_pending_page_id',0) > 0)
					$tmp_link = get_permalink(fp_get_option('fps_pending_page_id'));
				else
					$tmp_link = site_url('/wp-admin/edit.php?post_status=pending&post_type=post');
				
				//echo ."<hr>";
				//echo $tmp_link."<hr>";
				?>
				<li>
					<a href="<?php echo $tmp_link; ?>"><?php echo $fp_wdata['pending_txt'];?></a>
				</li>
			<?php } ?>
			<?php if ($fp_wdata['show_draft']) 
				{ ?>
				<li>
					<a href="<?php echo site_url('/wp-admin/edit.php?post_status=draft&post_type=post')?>"><?php echo $fp_wdata['draft_txt'];?></a>
				</li>
			<?php } ?>
			<?php if ($fp_wdata['show_comments']) 
				{ ?>
				<li>
					<a href="<?php echo site_url('/wp-admin/edit-comments.php?comment_status=moderated')?>"><?php echo $fp_wdata['cmt_pending_txt'];?></a>
				</li>
			<?php } ?>
			<?php if ($fp_wdata['show_comment_spam']) 
				{ ?>
				<li>
					<a href="<?php echo site_url('/wp-admin/edit-comments.php?comment_status=spam')?>"><?php echo $fp_wdata['cmt_spam_txt'];?></a>
				</li>
			<?php } ?>
		</ul>
		</div>
		<?php
		echo $args['after_widget'];
		}
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) 
		{
		$tmp_boolean_fields = array('show_draft', 'show_pending', 'show_comments', 'show_comment_spam');
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
		//error_log("New: ".print_r($new_instance, true)." - old: ".print_r($old_instance, true));
		
		// empty cache
		delete_transient($args['widget_id']);
		
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
			<label for="<?php echo $this->get_field_id('show_pending'); ?>"><?php _e('Show posts pending approval ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_pending'); ?>" name="<?php echo $this->get_field_name('show_pending'); ?>" value="true" <?php echo ($instance['show_pending'] == true) ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_draft'); ?>"><?php _e('Show number of draft posts ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_draft'); ?>" name="<?php echo $this->get_field_name('show_draft'); ?>" value="true" <?php echo ($instance['show_draft'] == true) ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_comments'); ?>"><?php _e('Show comments pending approval ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_comments'); ?>" name="<?php echo $this->get_field_name('show_comments'); ?>" value="true" <?php echo ($instance['show_comments'] == true) ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_comment_spam'); ?>"><?php _e('Show comments marked as spam ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_comment_spam'); ?>" name="<?php echo $this->get_field_name('show_comment_spam'); ?>" value="true" <?php echo ($instance['show_comment_spam'] == true) ? 'checked="checked"':''; ?>/>
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
add_action('widgets_init', create_function('', 'return register_widget("frontier_approvals_widget");'));
?>