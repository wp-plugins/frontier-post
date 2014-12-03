<?php
/*
Validations for Frontier Post plugin
*/

function frontier_post_age($tmp_post_date)
	{
	return round((time() - strtotime($tmp_post_date))/(24*60*60));					
	}

function frontier_can_add()
	{
		$tmp_can_do = false;
		if ( current_user_can( 'frontier_post_can_add' ) )
		$tmp_can_do = true;
	
		return $tmp_can_do;
	
	}	
	
	
function frontier_can_edit($tmp_post)
	{
	$cur_user 		= wp_get_current_user();
	
	$tmp_can_do = true;
	if ( !current_user_can( 'frontier_post_can_edit' ) )
		$tmp_can_do = false;
	
	if ( frontier_post_age($tmp_post->post_date) > get_option('frontier_post_edit_max_age') )
		$tmp_can_do = false;
	
	if ( (( (int) $tmp_post->comment_count) > 0) && ( (get_option("frontier_post_edit_w_comments") != "true") ))
		$tmp_can_do = false;
	
	// If user has capability "edit_others_posts" (Administrators & Editors) always allow them allow them to edit post.
	if ( !current_user_can( 'edit_others_posts' ) )
		$tmp_can_do = true;
	
	return $tmp_can_do;
	
	}	

function frontier_can_delete($tmp_post)
	{
	$cur_user 		= wp_get_current_user();
	
	$tmp_can_do = true;
	if ( !current_user_can( 'frontier_post_can_delete' ) )
		$tmp_can_do = false;

	if( $cur_user->ID != $tmp_post->post_author )
		$tmp_can_do = false;
		
	if ( frontier_post_age($tmp_post->post_date) > get_option('frontier_post_delete_max_age') )
		$tmp_can_do = false;
	
	if ( ( (int) $tmp_post->comment_count) > 0 && ( (get_option("frontier_post_del_w_comments") != "true") ))
		$tmp_can_do = false;
	
	// If user has capability "delete_other_posts" (Administrators & Editors) always allow them allow them to delete post.
	if ( !current_user_can( 'delete_other_posts' ) )
		$tmp_can_do = true;

	
	
	
	return $tmp_can_do;
	
	}	

function frontier_tax_list($tmp_tax_name, $exclude_list, $parent_tax	)
	{
	$tmp_tax_list 		= array();
	$level_sep			= "-- ";
	
	foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $parent_tax, 'exclude' => $exclude_list, 'show_count' => true)) as $tax1) :
			$tmp = Array('cat_ID' => $tax1->cat_ID, 'cat_name' => $tax1->cat_name);
			array_push($tmp_tax_list, $tmp);
			foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $tax1->cat_ID, 'exclude' => $exclude_list, 'show_count' => true)) as $tax2) :
				$tmp = Array('cat_ID' => $tax2->cat_ID, 'cat_name' => $level_sep.$tax2->cat_name);
				array_push($tmp_tax_list, $tmp);
				foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $tax2->cat_ID, 'exclude' => $exclude_list, 'show_count' => true)) as $tax3) :
					$tmp = Array('cat_ID' => $tax3->cat_ID, 'cat_name' => $level_sep.$level_sep.$tax3->cat_name);
					array_push($tmp_tax_list, $tmp);
				endforeach; // Level 3
			endforeach; // Level 2
		endforeach; //Level 1
	
	return $tmp_tax_list;
	}

Function frontier_post_tax_multi($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id, $tmp_size)
	{
	$tmp_html = '<select name="'.$tmp_name.'" id="'.$tmp_id.'" multiple="multiple" size="'.$tmp_size.'">';
	
	foreach ( $tmp_cat_list as $category1) :
		$tmp_html = $tmp_html.'<option value="'.$category1['cat_ID'].'"'; 
		if ( $tmp_selected && in_array( $category1['cat_ID'], $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.'selected="selected"'; 
			}
		$tmp_html = $tmp_html.'>'.$category1['cat_name'].'</option>';
	endforeach;
	$tmp_html = $tmp_html.'</select>';
	return $tmp_html;					 
	}

Function frontier_post_tax_checkbox($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	$tmp_html = '';
	foreach ( $tmp_cat_list as $category1) :
		$tmp_html = $tmp_html.'<input type="checkbox" ';
		//$tmp_html = $tmp_html.' id="'.$tmp_id.'"'; 
		$tmp_html = $tmp_html.' name="'.$tmp_name.'"';
		
		$tmp_html = $tmp_html.' value="'.$category1['cat_ID'].'"'; 
		if ( $tmp_selected && in_array( $category1['cat_ID'], $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.'checked="checked"'; 
			}
		$tmp_html = $tmp_html.'>'.$category1['cat_name'].'<br />';
		endforeach; 
	return $tmp_html;	
	}		

function frontier_post_set_msg($tmp_msg)
	{
	if ( ( isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '' ) != '' )
		$_REQUEST['frontier-post-msg'] = $_REQUEST['frontier-post-msg']."<br>".$tmp_msg;
	else
		$_REQUEST['frontier-post-msg'] = $tmp_msg;
	}

function frontier_post_output_msg()
	{
	$tmp_msg = isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '';
	$_REQUEST['frontier-post-msg'] = null;
	echo '<div class="frontier_post_msg">'.$tmp_msg.'</div>';
	}
	
function frontier_get_comment_icon()
	{
	$comment_icon			= TEMPLATEPATH."/images/comments.png";
		
	//print_r("Comment icon: ".$comment_icon);
		
	if (file_exists($comment_icon))
		{
		$comment_icon_html	= "<img src='".get_bloginfo('template_directory')."/images/comments.png'></img>";
		}
	else
		{
		$comment_icon		= ABSPATH."/wp-includes/images/wlw/wp-comments.png";
		// if no icon in theme, check wp-includes, and if it isnt the use a space
		if (file_exists($comment_icon))
			{
			$comment_icon_html	= "<img src='".get_bloginfo('url')."/wp-includes/images/wlw/wp-comments.png'></img>";
			}
		else
			{
			$comment_icon_html	= "&nbsp;";
			}
		}
	return $comment_icon_html;
	}
	
?>