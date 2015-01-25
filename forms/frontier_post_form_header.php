<?php


//***************************************************************************************
//* Header with hidden fields
//***************************************************************************************


?>	
		<!-- Leave hidden fields in form as they are used in the control of the shortcode abilities -->
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
		<input type="hidden" name="posttype" id="posttype" value="<?php echo (isset($thispost->post_type) ? $thispost->post_type : 'post'); ?>">
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<!-- Keep selected categories if no category field on form -->
		<input  type="hidden" name="post_categories" value="<?php echo $cats_selected_txt ;?>">
<?php
	
	// end form header file
?>