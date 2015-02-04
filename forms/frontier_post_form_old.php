<?php

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
	
	<table class="frontier_no_border"><tbody>
	<tr>
		<td>
		<table class="frontier_no_border"><tbody><tr>
			<td class="frontier_no_border">
				<?php _e("Title", "frontier-post");?>:&nbsp;
				<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
			</td>
			<?php 
			if ( $hide_post_status )
				{
				echo '<input type="hidden" id="post_status" name="post_status" value="'.$thispost->post_status.'"  >';
				}
			  else
				{
			?>	
		
			<td  class="frontier_no_border"><?php _e("Status", "frontier-post"); ?>:&nbsp;
			<?php 
				if (count($status_list) <=1)
					{
					echo $post_status_name;
					?>
					<input type="hidden" id="post_status" name="post_status" value="<?php echo $tmp_post_status; ?>"  ></br>
					<?php
					}
				else
					{
					?>
					<select  class="frontier_post_dropdown" id="post_status" name="post_status" >
						<?php foreach($status_list as $key => $value) : ?>   
							<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
								<?php echo $value; ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php } ?>	
			</td>
		<?php } // Hide post_status ?>
		</tr>
		</tbody></table>
		</td>
	</tr><tr>
		<td><div id="frontier_editor_field"> 
			<?php
			wp_editor($thispost->post_content, 'user_post_desc', frontier_post_wp_editor_args($editor_type, $frontier_media_button, $frontier_editor_lines, false));
			printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' );
			?>
		</div></td>
	</tr><tr>
		<td><table><tbody>
		<tr>
		<?php
		if ($category_type != "hide")
			{  		
			echo '<th class="frontier_heading" width="50%">'.__("Category", "frontier-post").'</th>';
			}
			
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				{ 
				echo '<th class="frontier_heading" width="50%">'.__("Tags", "frontier-post").'</th>';
			 	} 
			 else 
				{ 
				echo '<th class="frontier_heading" width="50%">&nbsp;</th>';
				} 
		?>	  
		</tr><tr>
			<?php
			switch ($category_type) 
				{
				case "hide":
					break;
			
				default:
					echo '<td class="frontier_border" width="50%"><div class="frontier-tax-box">';
					frontier_tax_input($thispost->ID, 'category', $category_type, $cats_selected,  $frontier_post_shortcode_parms);
					echo '</br><div class="frontier_helptext">'.__("Select category, multible can be selected using ctrl key", "frontier-post").'</div>';
					echo '</td>';
					break;
				
				}
		
			
				?>
			
		<?php if ( current_user_can( 'frontier_post_tags_edit' ) )
			{ ?>
			<td class="frontier_border" width="50%">
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[0]))echo $taglist[0];?>" name="user_post_tag1" id="user_post_tag" ></br>
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[1]))echo $taglist[1];?>" name="user_post_tag2" id="user_post_tag" ></br>
				<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[2]))echo $taglist[2];?>" name="user_post_tag3" id="user_post_tag" ></br>
			</td>
		<?php } 
		
		echo "</tr><tr>";
		
		?>
	
		
	</tr>
	</tbody></table></td>
</tr><tr>
	<td class="frontier_no_border">
		<?php
		?>
	</td>
</tr><tr>
	<?php if ( current_user_can( 'frontier_post_exerpt_edit' ) )
			{ ?>
			<td>
				<?php _e("Excerpt", "frontier-post")?>:</br>
				<textarea name="user_post_excerpt" id="user_post_excerpt"  cols="8" rows="2"><?php if(!empty($thispost->post_excerpt))echo $thispost->post_excerpt;?></textarea>
			</td>
			</tr><tr>
	<?php 	} 
	if ( fp_get_option_bool("fps_show_feat_img") )
		{
	?>
	<th class="frontier_heading" width="50%"><?php _e("Featured image", "frontier-post"); ?></th>
</tr><tr>
	<td class="frontier_border" width="50%">
	<?php
		$FeatImgLinkHTML = '<a title="Select featured Image" href="'.site_url('/wp-admin/media-upload.php').'?post_id='.$post_id.'&amp;type=image&amp;TB_iframe=1'.'" id="set-post-thumbnail" class="thickbox">';
		if (has_post_thumbnail($post_id))
			$FeatImgLinkHTML = $FeatImgLinkHTML.get_the_post_thumbnail($post_id, 'thumbnail').'<br>';
			
		$FeatImgLinkHTML = $FeatImgLinkHTML.__("Select featured image", "frontier-post").'</a>';
		
		echo $FeatImgLinkHTML."<br>";
		_e("Featured image (or new featured image) not visible until post is saved", "frontier-post");
		}
	?>
	
	
	</td>
</tr><tr>
	<td>
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
	</td>
</tr>
</tbody></table>

</form> 
	

	</div> <!-- ending div -->  
<?php
	
	// end form file
?>