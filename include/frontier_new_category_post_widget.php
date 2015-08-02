<?php
/**
 * 
 * Frontier post - Create post with preset category
 */
class frontier_new_category_posts_widget extends WP_Widget 
	{
	
	var $defaults;
	
    public function __construct()
		{
	
		$this->defaults = array(
    		'title' 			=> __('New post from #category#','frontier-post'),
			'link_name'			=> __('Create ','frontier-post').'#category#'.__(' post','frontier-post'),
    		'category' 			=> 0,
    		'nolistwrap' 		=> false,
    		'show_current_cat'	=> 1,
			);

				
		
		
    	$widget_ops = array('description' => __( "Add new post from category", 'frontier-post') );
        //parent::WP_Widget(false, $name = 'Add category post', $widget_ops);	
		//parent::__construct('frontier-my-posts', 'Frontier My Posts', $widget_ops);
		parent::__construct('frontier-new-category-posts', 'Frontier Add Category Post', $widget_ops);
		
		}


    /** @see WP_Widget::widget */
    public function widget($args, $instance) 
	{
	
	if(is_user_logged_in())
		{
		$tmp_cat_id 	= get_query_var("cat");
		
		// Only show if on category archive pages
		if ( !isset($tmp_cat_id) || $tmp_cat_id == 0 )
			return;
	
    	$instance 			= array_merge($this->defaults, $instance);
    	
		
		$tmp_cat_name 	= get_cat_name($tmp_cat_id);
		//error_log("cat id: ");
		//error_log($tmp_cat_id);
		
		// Set link & Title name
		$tmp_link_name = str_replace('#category#', $tmp_cat_name, $instance['link_name']);
		$tmp_title_name = str_replace('#category#', $tmp_cat_name, $instance['title']);
		
		echo $args['before_widget'];
    	if( !empty($instance['title']) )
			{
		    echo $args['before_title'];
		    //echo $instance['title'];
			echo $tmp_title_name;
		    echo $args['after_title'];
			}
    	
		?>
		
		
		
		
		<div  class="frontier-category-post-widget">
		<?php 
		//echo "Category: ";
		//echo $tmp_cat_name."(".$tmp_cat_id.")";
		echo '<p><center><a href="'.frontier_post_add_link(null, $tmp_cat_id).'&frontier_new_cat_widget=true'.'">'.$tmp_link_name.'</a></center></p>';
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
    public function update($new_instance, $old_instance) 
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
    	return $new_instance;
		}

    /** @see WP_Widget::form */
    public function form($instance) 
	{
    	$instance = array_merge($this->defaults, $instance);
    	
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			<label for="<?php echo $this->get_field_id('link_name'); ?>"><?php _e('Link text', 'frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('link_name'); ?>" name="<?php echo $this->get_field_name('link_name'); ?>" value="<?php echo esc_attr($instance['link_name']); ?>" />
		
		</p>
		
        <?php 
    }
    
}    
add_action('widgets_init', create_function('', 'return register_widget("frontier_new_category_posts_widget");'));
?>