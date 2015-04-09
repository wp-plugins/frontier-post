<?php


//Display message
frontier_post_output_msg();


if ( strlen($frontier_edit_text_before) > 1 )
	echo '<div id="frontier_edit_text_before">'.$frontier_edit_text_before.'</div>';



//***************************************************************************************
//* Start form
//***************************************************************************************



	echo '<div class="frontier_post_form"> ';
	echo '<form action="" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >';
	
	// do not remove this include, as it holds the hidden fields necessary for the logic to work
	include(FRONTIER_POST_DIR."/forms/frontier_post_form_header.php");	

	wp_nonce_field( 'frontier_add_edit_post', 'frontier_add_edit_post_'.$thispost->ID ); 

?>			
	<table class="frontier-post-taxonomies"><tbody><tr>
	<td class="frontier_no_border">
	<fieldset id="frontier_post_fieldset_title" class="frontier_post_fieldset">
		<legend><?php _e("Title", "frontier-post"); ?></legend>
		<input class="frontier-formtitle"  placeholder="<?php _e('Enter title here', 'frontier-post'); ?>" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="fp_title" >			
	</fieldset>
	<fieldset id="frontier_post_fieldset_status" class="frontier_post_fieldset">
		<legend><?php _e("Status", "frontier-post"); ?></legend>
	
		<?php if ( $hide_post_status )
				{
				echo '<input type="hidden" id="post_status" name="post_status" value="'.$thispost->post_status.'"  >';
				}
			  else
				{
				//echo ' '.__("Status", "frontier-post").': '; 
				?> 
				<select  class="frontier_post_dropdown" id="post_status" name="post_status" >
					<?php foreach($status_list as $key => $value) : ?>   
						<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
							<?php echo $value; ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php } ?>	
	</fieldset>
	</td></tr><tr><td class="frontier_no_border">
	<fieldset class="frontier_post_fieldset">
		<legend><?php _e("Content", "frontier-post"); ?></legend>	
		<div id="frontier_editor_field"> 
		<?php
		wp_editor($thispost->post_content, 'user_post_desc', frontier_post_wp_editor_args($editor_type, $frontier_media_button, $frontier_editor_lines, false));
		printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' );
		?>
		</div>
	</fieldset>
	</td></tr>
	<?php
	
	
	
	//**********************************************************************************
	//* Taxonomies
	//**********************************************************************************
			
	//$tax_list = array("category", "group", "article-type");
	$tax_list 			= $frontier_custom_tax;
	$tax_layout_list 	= fp_get_tax_layout($frontier_custom_tax, $frontier_custom_tax_layout);
	/*
	error_log("Tax list ---->");
	error_log(print_r($tax_list ,true));
	
	error_log("tax layout list---->");
	error_log(print_r($tax_layout_list,true));
	
	*/
	echo '<tr><td class="frontier_no_border">';
	
	//echo '<table class="frontier-post-taxonomies"><tbody><tr>';
	foreach ( $tax_layout_list as $tmp_tax_name => $tmp_tax_layout) 
		{
		if ($tmp_tax_layout != "hide")
			{
			// Cats_selected is set from skript, but only for category
			if ($tmp_tax_name != 'category')
				$cats_selected = wp_get_post_terms($thispost->ID, $tmp_tax_name, array("fields" => "ids"));;
			
			//echo '<td class="frontier-post-tax">';
			echo '<fieldset class="frontier_post_fieldset_tax">';
			echo '<legend class="frontier_post_legend_tax" >'.fp_get_tax_label($tmp_tax_name).'</legend>';
			//echo '<div class="frontier-tax-box">';
			frontier_tax_input($thispost->ID, $tmp_tax_name, $tmp_tax_layout, $cats_selected, $frontier_post_shortcode_parms);
			//echo '</div>';
			//echo '</td>';
			echo '</fieldset>';
			echo PHP_EOL;
			}
		}
	//echo '</tr></tbody></table>';
	
	
	if ( current_user_can( 'frontier_post_tags_edit' ) || fp_get_option_bool("fps_show_feat_img") )
		{
		//echo '<table class="frontier-post-taxonomies"><tbody><tr>';
		
	
		if ( current_user_can( 'frontier_post_tags_edit' ) )
			{ 
			echo '<fieldset class="frontier_post_fieldset_tax">';
			echo '<legend>'.__("Tags", "frontier-post").'</legend>';
			for ($i=0; $i<$fp_tag_count; $i++)
				{
				$tmp_tag = isset($taglist[$i]) ? fp_tag_transform($taglist[$i]) : "";
				//$tmp_tag = array_key_exists($i, $taglist) ? fp_tag_transform($taglist[$i]) : "";
				echo '<input placeholder="'.__("Enter tag here", "frontier-post").'" type="text" value="'.$tmp_tag.'" name="user_post_tag'.$i.'" id="user_post_tag"><br>';	
				}
			echo '</fieldset>';
			} 
	
		if ( fp_get_option_bool("fps_show_feat_img") )
			{
			?>
			<!--<td class="frontier_featured_image">-->
			
			<fieldset class="frontier_post_fieldset_tax">
			<legend><?php _e("Featured image", "frontier-post"); ?></legend>
			<?php
			$FeatImgLinkHTML = '<a title="Select featured Image" href="'.site_url('/wp-admin/media-upload.php').'?post_id='.$post_id.'&amp;type=image&amp;TB_iframe=1'.'" id="set-post-thumbnail" class="thickbox">';
			if (has_post_thumbnail($post_id))
				$FeatImgLinkHTML = $FeatImgLinkHTML.get_the_post_thumbnail($post_id, 'thumbnail').'<br>';
		
			$FeatImgLinkHTML = $FeatImgLinkHTML.__("Select featured image", "frontier-post").'</a>';
	
			echo $FeatImgLinkHTML."<br>";
			echo '<div id="frontier_post_featured_image_txt">'.__("Not updated until post is saved", "frontier-post").'</div>';
			echo '</fieldset>';
			//echo '</td>';
			}
		//echo '</tr></tbody></table>';
		}
		
	if ( current_user_can( 'frontier_post_exerpt_edit' ) )
			{ ?>
			<fieldset class="frontier_post_fieldset_excerpt">
				<legend><?php _e("Excerpt", "frontier-post")?>:</legend>
				<textarea name="user_post_excerpt" id="user_post_excerpt" ><?php if(!empty($thispost->post_excerpt))echo $thispost->post_excerpt;?></textarea>
			</fieldset>
			
	<?php 	} 
	
		echo '</td></tr>';
		
		//****************************************************************************************************
		// Action fires just before the submit buttons
		// Do action 		frontier_post_form_standard
		// $thispost 		Post object for the post  
		// $tmp_task_new  	Equals true if the user is adding a post
		//****************************************************************************************************
		
		do_action('frontier_post_form_standard', $thispost, $tmp_task_new);
		
		
		echo '<tr><td class="frontier_no_border">';
	
	?>
	
	
	
		<fieldset class="frontier_post_fieldset">
		<legend><?php _e("Actions", "frontier-post"); ?></legend>
		<?php
			
		if ( fp_get_option_bool("fps_submit_save") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 		id="user_post_save" 	value="save"><?php _e("Save", "frontier-post"); ?></button>
		<?php }
		if ( fp_get_option_bool("fps_submit_savereturn") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $frontier_return_text; ?></button>
		<?php }
		if ( fp_get_option_bool("fps_submit_preview") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 	id="user_post_preview" 	value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
		<?php } 
		if ( fp_get_option_bool("fps_submit_cancel") )
		{ ?>
		<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
		<?php } ?>
	</fieldset>
	
	</td></tr></table>
</form> 
	
	</div> <!-- ending div -->  
<?php
	
	// end form file
?>