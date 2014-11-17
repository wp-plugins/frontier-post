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
	
	if ( (( (int) $tmp_comments_cnt) > 0) && ( (get_option("frontier_post_edit_w_comments") != "true") ))
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
	
	if ( ( (int) $tmp_comments_cnt) > 0 && ( (get_option("frontier_post_del_w_comments") != "true") ))
		$tmp_can_do = false;
	
	
	return $tmp_can_do;
	
	}	

function frontier_tax_list($tmp_tax_name, $exclude_list)
	{
	$tmp_tax_list 		= array();
	$parent_tax			= 0;
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


?>