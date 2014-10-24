<?php

	//error_log("post_id - f1: ".$post_id);
	
	if ($user_can_edit_this_post)
	{
	
	//echo "Prev cat: ".$frontier_previous_category."<br>";
	
?>	
	<script type="text/javascript">
		var filenames="";
	</script>

	<div class="frontier_post_form"> 

	<table >
	<tbody>
	<form action="" method="post" name="wppu" id="wppu" enctype="multipart/form-data" >
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<input type="hidden" name="parent_cat" value="<?php echo $_REQUEST['parent_cat'];?>">
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
	<tr>
		<td>
			<table><tbody>
			<tr>
				<td class="frontier_no_border">
					<?php _e("Title", "frontier-post");?>:&nbsp;
					<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
				</td>
				
				<td  class="frontier_no_border"><?php _e("Status", "frontier-post"); ?>:&nbsp;
				<?php 
				if (count($status_list) <=1)
					{
					$status_name = array_values($status_list);
					$status_value = array_keys($status_list);
					echo $status_name[0];
					?>
					<input type="hidden" id="post_status" name="post_status" value="<?php echo $status_value[0]; ?>"  ></br>
					<?php
					}
				else
					{ ?>
					<select  id="post_status" name="post_status" <?php echo $status_readonly; ?>>
						<?php foreach($status_list as $key => $value) : ?>   
							<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
								<?php echo $value; ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php } ?>	
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
			if ($category_type != "hide")
				{
				?>
				<td class="frontier_border" width="50%">
				<?php
				
				
				if ($category_type == "single")
					{
					wp_dropdown_categories(array('id'=>'cat', 'hide_empty' => 0, 'name' => 'cat', 'child_of' => $parent_category, 'orderby' => 'name', 'selected' => $postcategoryid, 'hierarchical' => true, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)); 
					}
				else
					{
					?>
					<select name="categorymulti[]" id="frontier_categorymulti" multiple="multiple" size="8">
					<?php  
					
					foreach ( $catlist as $category1) : ?>
						<option value="<?php echo $category1['cat_ID']; ?>" <?php if ( $cats_selected && in_array( $category1['cat_ID'], $cats_selected ) ) { echo 'selected="selected"'; }?>><?php echo $category1['cat_name']; ?></option>
					<?php endforeach; ?>
					</select>
					</br><div class="frontier_helptext"><?php _e("Select category, multible can be selected using ctrl key", "frontier-post"); ?></div>
					</td>
					<?php 
					} // end multis select 
				} // end hide category 
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
			<button class="button" type="submit" name="user_post_save" id="user_post_save" value="save"><?php _e("Save", "frontier-post"); ?></button>
			<button class="button" type="submit" name="user_post_submit" id="user_post_submit" value="savereturn"><?php _e("Save & Return", "frontier-post"); ?></button>
			<button class="button" type="submit" name="user_post_preview" id="user_post_preview" value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
			
			<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
		</td>
	</tr>
	</form> 
	</tbody>
	</table>

	</div> <!-- ending div -->  
<?php
	}
	else
	{
	_e("You are not allowed to edit this post !","frontier-post");
	}
	// end form file
?>