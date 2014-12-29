<?php

if ( strlen($frontier_edit_text_before) > 1 )
	echo '<div id="frontier_edit_text_before">'.$frontier_edit_text_before.'</div>';

frontier_post_output_msg();
?>	
	<div class="frontier_post_form"> 

	<table >
	<tbody>
	<form action="" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >
		<!-- Leave hidden fields in form as they are used in the control of the shortcode abilities -->
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<?php wp_nonce_field( 'frontier_add_edit_post', 'frontier_add_edit_post_'.$thispost->ID ); ?>
		<!-- Keep selected categories if no category field on form -->
		<input  type="hidden" name="post_categories" value="<?php echo $cats_selected_txt ;?>">
	<tr>
		<td>
			<table><tbody>
			<tr>
				<td class="frontier_no_border">
					<?php _e("Title", "frontier-post");?>:&nbsp;
					<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
				</td>
				<td  class="frontier_no_border"><?php __("Status", "frontier-post").": ".$post_status_name; ?>
					
					<input type="hidden" id="post_status" name="post_status" value="<?php echo $tmp_post_status; ?>"  ></br>
				</td>
			</tr>
			</tbody></table>
		</td>	
	</tr><tr>
		<td> 
			<?php
			wp_editor($thispost->post_content, 'user_post_desc', frontier_post_wp_editor_args($editor_type, $frontier_media_button, $frontier_editor_lines, false));
			printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' );
			?>
		</td>
	</tr><tr>
		<td><table><tbody>
		<tr>
			<th class="frontier_heading" width="50%"><?php _e("Category", "frontier-post"); ?></th>
			<th class="frontier_heading" width="50%"><?php _e("Tags", "frontier-post"); ?></th>
		</tr><tr>
			<td class="frontier_border" width="50%"><div class="frontier-tax-box">';
				<?php frontier_post_tax_checkbox($catlist, $cats_selected, "categorymulti[]", "frontier_categorymulti"); ?>
			</td>				
			<td class="frontier_border" width="50%">
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[0]))echo $taglist[0];?>" name="user_post_tag1" id="user_post_tag" ></br>
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[1]))echo $taglist[1];?>" name="user_post_tag2" id="user_post_tag" ></br>
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[2]))echo $taglist[2];?>" name="user_post_tag3" id="user_post_tag" ></br>
			</td>
		</tr>
	</tbody></table></td>
	</tr><tr>
		<td>
			<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php _e("Save & Return", "frontier-post");; ?></button>
			<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
		</td>
	</tr>
	</form> 
	</tbody>
	</table>

	</div> <!-- ending div -->  
<?php
	
	// end form file
?>