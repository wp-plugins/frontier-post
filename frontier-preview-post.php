<?php


function  frontier_preview_post($tmp_post_id)
	{
		
	
	
	frontier_post_output_msg();
	
	
	//fp_log("Preview_post");
	//fp_log("Post Id: ".$tmp_post_id);
	//fp_log("fp cat id Preview: ".($frontier_cat_id ? $frontier_cat_id : "Unknown"));
	
			
	$preview_post = get_post($tmp_post_id);
	$tmp_content = apply_filters( 'the_content', $preview_post->post_content );
	$tmp_content = str_replace( ']]>', ']]&gt;', $tmp_content );
	
	
	
	//fp_log($preview_post);
	echo "<hr>";
	echo "<h1><center>Preview of post</center></h1>";
	echo "<hr>";
	echo "<h1>".$preview_post->post_title."</h1><br>";
	echo $tmp_content;
	echo "<hr>";
	
	
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