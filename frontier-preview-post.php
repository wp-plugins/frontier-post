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
	
	$tmp_edit_link = '<a href='.get_permalink().$concat.'task=edit&postid='.$preview_post->ID.'>'.__("Edit Post", "frontier-post").'</a>';
	$tmp_list_link = '<a href='.get_permalink().'>'.__("Return to list", "frontier-post").'</a>';
	
	
	
	//fp_log($preview_post);
	echo "<hr>";
	echo '';
	echo '<div class="frontier_post_preview_title"><center> <h1>Preview Post</center></h1></center></div>';
	echo '<div class="frontier_post_preview_status">';
	echo '<center>';
	echo $tmp_edit_link.'&nbsp&nbsp';
	echo '(status = '.$preview_post->post_status.')';
	echo '&nbsp&nbsp'.$tmp_list_link;
	echo '</center></div>';
	echo "<hr>";
	echo "<h1>".$preview_post->post_title."</h1><br>";
	echo $tmp_content;
	
	//echo "<hr>";
	
	
	
	/*
	$old_post = $post;
	
	//$post = get_post($tmp_post_id);
	the_title();
	the_content();
	
	$post = $old_post;
	exit;
	*/
	//include_once(frontier_load_form("frontier_list.php"));
		
	}  
?>