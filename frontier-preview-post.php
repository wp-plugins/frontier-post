<?php


function  frontier_preview_post($tmp_post_id = 0)
	{
	$concat= get_option("permalink_structure")?"?":"&";  
	
	//fp_log("Post id (preview): ".$tmp_post_id);
	
	frontier_post_output_msg();
	
	$preview_post = get_post($tmp_post_id);
	
	//post variable: $preview_post
	$tmp_content = apply_filters( 'the_content', $preview_post->post_content );
	$tmp_content = str_replace( ']]>', ']]&gt;', $tmp_content );
	
	include_once(frontier_load_form("frontier_post_form_preview.php"));
		
	}  
?>