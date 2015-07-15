<?php

//Display message
frontier_post_output_msg();

if ( strlen($frontier_edit_text_before) > 1 )
	echo '<div id="frontier_edit_text_before">'.$frontier_edit_text_before.'</div>';

//echo 'postid: '.$thispost->ID;

frontier_post_output_msg();
?>	
	<div class="frontier_post_form"> 
	
	<?php
	echo '<form action="'.$frontier_permalink.'" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >';
	
	// do not remove this include, as it holds the hidden fields necessary for the logic to work
	include(FRONTIER_POST_DIR."/forms/frontier_post_form_header.php");	
	
	wp_nonce_field( 'frontier_add_edit_post', 'frontier_add_edit_post_'.$thispost->ID ); 
	?>
	<table >
	<tbody>
	
	<tr>
		<td>
			<table><tbody>
			<tr>
				<td class="frontier_no_border">
					<?php _e("Title", "frontier-post");?>:&nbsp;
					<input class="frontier-formtitle"  placeholder="<?php _e('Enter title here', 'frontier-post'); ?>" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="fp_title" >				
				</td>
			<?php if ( $hide_post_status )
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
					<select  id="post_status" name="post_status" >
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
			wp_editor($thispost->post_content, 'user_post_desc', frontier_post_wp_editor_args($editor_type, $frontier_media_button, $frontier_editor_height, false));
			printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' );
			?>
		</div></td>
	</tr><tr>
		<td><table><tbody>
		<tr>
		<?php
		if ($category_type != "hide")
			{  
		?>
			<th class="frontier_heading" width="50%"><?php _e("Category", "frontier-post"); ?></th>
		<?php 
			}
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				{ ?>
			<th class="frontier_heading" width="50%"><?php _e("Tags", "frontier-post"); ?></th>
			<?php } else 
				{ ?>
				  <th class="frontier_heading" width="50%">&nbsp;</th>
			<?php } ?>	  
		</tr><tr>
			<?php
			switch ($category_type) 
				{
				case "hide":
					break;
			
				default:
					echo '<td class="frontier_border" width="50%"><div class="frontier-tax-box">';
					frontier_tax_input($thispost->ID, 'category', $category_type, $cats_selected,  $frontier_post_shortcode_parms, $tax_form_lists['category']);
					echo '</br><div class="frontier_helptext">'.__("Select category, multiple can be selected using ctrl key", "frontier-post").'</div>';
					echo '</td>';
					break;
				
				}
			/* Old code
			switch ($category_type) 
				{
				case "hide":
					break;
			
				case "single":
					echo '<td class="frontier_border" width="50%">';
					wp_dropdown_categories(array('id'=>'cat', 'hide_empty' => 0, 'name' => 'cat', 'child_of' => $frontier_parent_cat_id, 'orderby' => 'name', 'selected' => $cats_selected[0], 'hierarchical' => true, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)); 
					break;
			
				case "multi":
					echo '<td class="frontier_border" width="50%">';
					echo frontier_post_tax_multi($catlist, $cats_selected, "categorymulti[]", "frontier_categorymulti", 8);
					echo '</br><div class="frontier_helptext">'.__("Select category, multiple can be selected using ctrl key", "frontier-post").'</div>';
					echo '</td>';
					break;
    
				case "checkbox":
					echo '<td class="frontier_border" width="50%"><div class="frontier-tax-box">';
					echo frontier_post_tax_checkbox($catlist, $cats_selected, "categorymulti[]", "frontier_categorymulti");
					echo '</td>';
					break;
				
				case "readonly":
					echo '<td class="frontier_border" width="50%">';
					foreach ( $cats_selected as $category1) :
						echo $category1->name.", "; 
					endforeach;
					echo '</td>';
					break;
					
				default:
					break;
				}
			*/
				
				?>
				
			<?php if ( current_user_can( 'frontier_post_tags_edit' ) )
				{ ?>
				<td class="frontier_border" width="50%">
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[0]))echo $taglist[0];?>" name="user_post_tag1" id="user_post_tag" ></br>
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[1]))echo $taglist[1];?>" name="user_post_tag2" id="user_post_tag" ></br>
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[2]))echo $taglist[2];?>" name="user_post_tag3" id="user_post_tag" ></br>
				</td>
			<?php } ?>
		
			
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
		if (get_option("frontier_post_show_feat_img", "false") == "true")
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
			
			$frontier_submit_buttons			= get_option("frontier_post_submit_buttons", array('save' => 'true', 'savereturn' => 'true', 'preview' => 'true', 'cancel' => 'true' ) );
			
			
			if ( $frontier_submit_buttons['save'] == "true" )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 		id="user_post_save" 	value="save"><?php _e("Save", "frontier-post"); ?></button>
			<?php }
			if ( $frontier_submit_buttons['savereturn'] == "true" )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $frontier_return_text; ?></button>
			<?php }
			if ( $frontier_submit_buttons['preview'] == "true" )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 	id="user_post_preview" 	value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
			<?php } 
			if ( $frontier_submit_buttons['cancel'] == "true" )
			{ ?>
			<input type="reset" value="<?php _e("Cancel", "frontier-post"); ?>"  name="cancel" id="frontier-post-cancel" onclick="location.href='<?php the_permalink();?>'">
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