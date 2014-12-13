<?php
/*
Utilities for Frontier Post plugin
*/

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
	if ( get_option("frontier_post_show_msg", "false") == "true" )
		{
		$tmp_msg = isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '';
		echo '<div class="frontier_post_msg">'.$tmp_msg.'</div>';
		}
	$_REQUEST['frontier-post-msg'] = null;
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
	
function frontier_post_wp_editor_args($editor_type = "full", $media_button = true, $editor_lines = 300, $dfw = false)
	{
	$editor_layout	= array('dfw' => $dfw, 'editor_height' => $editor_lines, 'media_buttons' => $media_button );
	
	// Get tinymce button layout from Frontier Buttons
	if ( ($editor_type == 'full') && (function_exists('frontier_buttons_full_buttons')) )
		{
		$tinymce_buttons = frontier_buttons_full_buttons();
		$tmp = array('tinymce' => $tinymce_buttons);
		array_merge($editor_layout, $tmp);
		}
	
	
	if ($editor_type == "minimal-visual")
		$editor_layout = array_merge($editor_layout, array('teeny' => true, 'quicktags' => false));
	
	if ($editor_type == "minimal-html")
		$editor_layout = array_merge($editor_layout, array('teeny' => true, 'tinymce' => false));
		
	if ($editor_type == "text")	
		$editor_layout = array_merge($editor_layout, array('quicktags' => false, 'tinymce' =>false));
	
	//error_log(print_r($editor_layout, true));
	return $editor_layout;
	}

function fp_log($tmp_msg) {
    if (FRONTIER_POST_DEBUG === true) {
        if (is_array($tmp_msg) || is_object($tmp_msg)) {
            error_log(print_r($tmp_msg, true));
        } else {
            error_log($tmp_msg);
        }
    }
}	

?>