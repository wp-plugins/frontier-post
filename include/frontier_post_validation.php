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
		global $fps_access_check_msg;
		$tmp_can_do = false;
		if ( current_user_can( 'frontier_post_can_add' ) )
			{
			$tmp_can_do = true;
			$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to add new post")."<br>";
			}
			
		return $tmp_can_do;
	
	}	
	
	
function frontier_can_edit($tmp_post)
	{
	global $fps_access_check_msg;
	
	$cur_user 				= wp_get_current_user();
	$tmp_can_do 			= true;
	
	// Check if the user is allowed to edit posts
	if ( !current_user_can( 'frontier_post_can_edit' ) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to edit posts", "frontier-post")."<br>";
		}
		
	// Users can not edit other users posts unless they have capability "edit_others_posts" (Administrators & Editors) 
	if( ($cur_user->ID != $tmp_post->post_author) && (!current_user_can( 'edit_others_posts' )) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to edit post from another user", "frontier-post")."<br>";
		}
	
	// Check that the age of the post is below the Frontier Post setting
	if ( frontier_post_age($tmp_post->post_date) > get_option('frontier_post_edit_max_age') )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to edit post older than: ", "frontier-post").get_option('frontier_post_edit_max_age')." ".__("days", "frontier-post")."<br>";
		}
	
	// Check that user is allowed to edit posts that already has comments
	if ( (( (int) $tmp_post->comment_count) > 0) && ( (get_option("frontier_post_edit_w_comments") != "true") ))
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to edit post that already has comments", "frontier-post")."<br>";
		}
	
	// Check if user is allowed to edit a post that is already published
	if ( (get_option("frontier_post_change_status", "false") != "true") && ($tmp_post->post_status == "publish") )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to edit post that is published", "frontier-post")."<br>";
		}
		
	// Always allow the boss to edit posts
	if ( current_user_can( 'administrator' ) )
		$tmp_can_do = true;
	
	
	return $tmp_can_do;
	
	}	

function frontier_can_delete($tmp_post)
	{
	$fps_access_check_msg	= "";
	$cur_user 				= wp_get_current_user();
	$tmp_can_do 			= true;
	
	// Check if the user is allowed to delete posts
	if ( !current_user_can( 'frontier_post_can_delete' ) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to delete posts", "frontier-post")."<br>";
		}
	
	// Users can not delete other users posts unless they have capability "delete_others_posts" (Administrators & Editors) 
	if( ($cur_user->ID != $tmp_post->post_author) && (!current_user_can( 'delete_others_posts' )) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to delete post from another user", "frontier-post")."<br>";
		}
		
	
	// Check that the age of the post is below the Frontier Post setting
	if ( frontier_post_age($tmp_post->post_date) > get_option('frontier_post_delete_max_age') )
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to delete post older than: ", "frontier-post").get_option('frontier_post_delete_max_age')." ".__("days", "frontier-post")."<br>";
		}


	// Check that user is allowed to delete posts that already has comments	
	if ( ( (int) $tmp_post->comment_count) > 0 && ( (get_option("frontier_post_del_w_comments") != "true") ))
		{
		$tmp_can_do = false;
		$fps_access_check_msg = $fps_access_check_msg.__("You are not allowed to deelete post that already has comments", "frontier-post")."<br>";
		}	
	
	// Always allow the boss to edit posts
	if ( current_user_can( 'administrator' ) )
		$tmp_can_do = true;
	
	
	
	
	return $tmp_can_do;
	
	}	

?>