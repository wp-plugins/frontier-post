<?php

	
	
	
	
	
?>	
	<div class="frontier_post_form"> 

	<table >
	<tbody>
	<form action="" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >
		<!-- Leave hidden fields in form as they are used in the control of the shortcode abilities -->
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<input type="hidden" name="parent_cat" value="<?php echo $_REQUEST['parent_cat'];?>">
		<input type="hidden" name="frontier_cat_id" value="<?php echo $_REQUEST['frontier_cat_id'];?>">
		<input type="hidden" name="return_category_archive" value="<?php echo $_REQUEST['return_category_archive'];?>">
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
		<input type="hidden" name="return_p_id" id="id" value="<?php echo $_REQUEST['frontier_return_page_id']; ?>">
		<?php wp_nonce_field( 'frontier_add_edit_post' ); ?>
		<!-- Keep selected categories if no category field on form -->
		<input  type="hidden" name="post_categories" value="<?php echo implode(',', $cats_selected) ;?>">
	<tr>
		<td>
			<table><tbody>
			<tr>
				<td class="frontier_no_border">
					<?php _e("Title", "frontier-post");?>:&nbsp;
					<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
				</td>
				
				<td  class="frontier_no_border"><?php _e("Status", "frontier-post"); ?>:&nbsp;
					<select  id="post_status" name="post_status" <?php echo $status_readonly; ?>>
						<?php foreach($status_list as $key => $value) : ?>   
							<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
								<?php echo $value; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				
			</tr>
			</tbody></table>
		</td>	
	</tr><tr>
		<td> 
			<?php
			wp_editor($thispost->post_content, 'user_post_desc', $editor_layout);
			printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' );
			?>
		</td>
	</tr><tr>
		<td><table><tbody>
		<tr>
			<th class="frontier_heading" width="50%"><?php _e("Category", "frontier-post"); ?></th>
			<th class="frontier_heading" width="50%"><?php _e("Tags", "frontier-post"); ?></th>
		</tr><tr>
			<td class="frontier_border" width="40%">
				<?php 
				wp_dropdown_categories(array('id'=>'cat', 'hide_empty' => 0, 'name' => 'cat', 'child_of' => $parent_category, 'orderby' => 'name', 'selected' => $cats_selected[0], 'hierarchical' => true, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)); 
				?>
			</td>
				
			<td class="frontier_border" width="60%">
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[0]))echo $taglist[0];?>" name="user_post_tag1" id="user_post_tag" >
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[1]))echo $taglist[1];?>" name="user_post_tag2" id="user_post_tag" >
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[2]))echo $taglist[2];?>" name="user_post_tag3" id="user_post_tag" >
			</td>
		</tr>
		</tbody></table></td>
		
		
		</td>
	</tr><tr>
		<td>
			<?php
			//
			if ( isset($_REQUEST['frontier_return_text']) && ($_REQUEST['frontier_return_text'] != "false") )
				$save_return_text = $_REQUEST['frontier_return_text'];
			else
				$save_return_text = __("Save & Return", "frontier-post");
			
			$frontier_submit_buttons			= get_option("frontier_post_submit_buttons", array('save' => 'true', 'savereturn' => 'true', 'preview' => 'true', 'cancel' => 'true' ) );
			
			if ( $frontier_submit_buttons['save'] == "true" )
			{ ?>
				<button class="button" type="submit" name="user_post_save" 		id="user_post_save" 	value="save"><?php _e("Save", "frontier-post"); ?></button>
			<?php }
			if ( $frontier_submit_buttons['savereturn'] == "true" )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $save_return_text; ?></button>
			<?php }
			if ( $frontier_submit_buttons['savereturn'] == "true" )
			{ ?>
				<button class="button" type="submit" name="user_post_preview" 	id="user_post_preview" 	value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
			<?php } 
			if ( $frontier_submit_buttons['cancel'] == "true" )
			{ ?>
			<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
			<?php } ?>
		</td>
	</tr>
	</form> 
	</tbody>
	</table>

	</div> <!-- ending div -->  
<?php
	
	// end form file
?>