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
	
	if( $cur_user->ID != $tmp_post->post_author )
		$tmp_can_do = false;
	
	if ( frontier_post_age($tmp_post->post_date) > get_option('frontier_post_edit_max_age') )
		$tmp_can_do = false;
	
	if ( (( (int) $tmp_post->comment_count) > 0) && ( (get_option("frontier_post_edit_w_comments") != "true") ))
		$tmp_can_do = false;
	
	// If user has capability "edit_others_posts" (Administrators & Editors) always allow them allow them to edit post.
	if ( current_user_can( 'edit_others_posts' ) )
		$tmp_can_do = true;
	
	if ( !current_user_can( 'frontier_post_can_edit' ) )
		$tmp_can_do = false;
	
	return $tmp_can_do;
	
	}	

function frontier_can_delete($tmp_post)
	{
	$cur_user 		= wp_get_current_user();
	
	$tmp_can_do = true;
	
	if( $cur_user->ID != $tmp_post->post_author )
		$tmp_can_do = false;
		
	if ( frontier_post_age($tmp_post->post_date) > get_option('frontier_post_delete_max_age') )
		$tmp_can_do = false;
	
	if ( ( (int) $tmp_post->comment_count) > 0 && ( (get_option("frontier_post_del_w_comments") != "true") ))
		$tmp_can_do = false;
	
	// If user has capability "delete_other_posts" (Administrators & Editors) always allow them allow them to delete post.
	if ( current_user_can( 'delete_other_posts' ) )
		$tmp_can_do = true;
	
	if ( !current_user_can( 'frontier_post_can_delete' ) )
		$tmp_can_do = false;
	
	
	return $tmp_can_do;
	
	}	
	

?>