<?php


//Display message
frontier_post_output_msg();



		
	$tmp_edit_link = '<a href='.get_permalink().$concat.'task=edit&postid='.$preview_post->ID.'>'.__("Edit Post", "frontier-post").'</a>';
	$tmp_list_link = '<a href='.get_permalink().'>'.__("Return", "frontier-post").'</a>';
	
	?>
	
	<hr>
	
	<div class="frontier_post_preview_title"><center> <h1>Preview Post</center></h1></center></div>
	<div class="frontier_post_preview_status">
	<center><?php echo $tmp_edit_link.'&nbsp&nbsp('.__("status", "frontier_post").'='.$preview_post->post_status.')&nbsp&nbsp'.$tmp_list_link; ?></center></div>'
	<hr>
	<h1><?php echo $preview_post->post_title; ?></h1><br>
	<?php echo $tmp_content; ?>
	
<?php	
 
?>