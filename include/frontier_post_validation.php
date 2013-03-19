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
	
	
function frontier_can_edit($tmp_post_date, $tmp_comments_cnt)
	{
	$tmp_can_do = true;
	if ( !current_user_can( 'frontier_post_can_edit' ) )
		$tmp_can_do = false;
		
	if ( frontier_post_age($tmp_post_date) > get_option('frontier_post_edit_max_age') )
		$tmp_can_do = false;
	
	if ( (( (int) $tmp_comments_cnt) > 0) && ( (get_option("frontier_edit_w_comments") != "true") ))
		$tmp_can_do = false;
	
	return $tmp_can_do;
	
	}	

function frontier_can_delete($tmp_post_date, $tmp_comments_cnt)
	{
	
	$tmp_can_do = true;
	if ( !current_user_can( 'frontier_post_can_delete' ) )
		$tmp_can_do = false;
		
	if ( frontier_post_age($tmp_post_date) > get_option('frontier_post_delete_max_age') )
		$tmp_can_do = false;
	
	if ( ( (int) $tmp_comments_cnt) > 0 && ( (get_option("frontier_del_w_comments") != "true") ))
		$tmp_can_do = false;
	
	
	return $tmp_can_do;
	
	}	



?>