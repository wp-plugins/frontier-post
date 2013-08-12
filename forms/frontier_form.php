<?php

	$frontier_task = $_REQUEST['task'] ? $_REQUEST['task'] :"?";
	// get options
	$saved_options 		= get_option('frontier_post_options', array() );
	
	//get users role:
	$users_role 				= frontier_get_user_role();
	
	$editor_type 				= $saved_options[$users_role]['editor'] ? $saved_options[$users_role]['editor'] : "full"; 
	$frontier_post_mce_custom	= (get_option("frontier_post_mce_custom")) ? get_option("frontier_post_mce_custom") : "disable";
	$frontier_post_mce_button	= get_option("frontier_post_mce_button", array());
		
	$category_type 				= $saved_options[$users_role]['category'] ? $saved_options[$users_role]['category'] : "multi"; 
	$default_category			= $saved_options[$users_role]['default_category'] ? $saved_options[$users_role]['default_category'] : get_option("default_category"); 
	$preview_label 				= __("Preview", "frontier-post");
	
	
	if(!isset($thispost->post_type))
		{
			$thispost->post_type = 'post';
		}

	if(!isset($thispost->post_content))
		{
			$thispost->post_content = '';
		}
	
	if (isset($thispost->ID))
		{
		$post_id = $thispost->ID;
		}
	
	frontier_media_fix( $post_id );
	
	$user_can_edit_this_post = true;

	if ($thispost->post_author != $current_user->ID && (!current_user_can( 'administrator' )))
		$user_can_edit_this_post = false;
	
	if (($frontier_task == "new") && (!current_user_can( 'frontier_post_can_add' )))
		$user_can_edit_this_post = false;
	
	//build post status list based on current status and users capability
	$tmp_status_list = get_post_statuses( );
	
	// Remove private status from array
	unset($tmp_status_list['private']);
	
	// Remove draft status from array if user is not allowed to use drafts
	if (!current_user_can('frontier_post_can_draft'))
	unset($tmp_status_list['draft']);
	
	
	$status_list 		= array();
	$tmp_post_status 	= $thispost->post_status ? $thispost->post_status : "unknown";
	
	$status_readonly = "";
	
	if ($tmp_post_status == "publish")
		{
		$status_readonly = "readonly";
		$status_list[$tmp_post_status] = $tmp_status_list[$tmp_post_status];
		if (!current_user_can( 'frontier_post_can_publish' ))
			{
			$user_can_edit_this_post = false;
			}
		}
	else
		{
		$status_list = $tmp_status_list;
		if (!current_user_can( 'frontier_post_can_publish' ))
			{
			unset($status_list['publish']);
			}
		}
	

	// -- Setup wp_editor layout
	// full: full Tiny MCE
	// minimal-visual: Teeny layout
	// minimal-html: simple layout with html options
	// text: text only
	
	
	
	// Editor settings
	$editor_layout = array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300 );
	
	
	
	if ($editor_type == "full" && $frontier_post_mce_custom == "true")
		{
		$tinymce_options = array(
			'theme_advanced_buttons1' 	=> ($frontier_post_mce_button[0] ? $frontier_post_mce_button[0] : ''),
			'theme_advanced_buttons2' 	=> ($frontier_post_mce_button[1] ? $frontier_post_mce_button[1] : ''),
			'theme_advanced_buttons3' 	=> ($frontier_post_mce_button[2] ? $frontier_post_mce_button[2] : ''),
			'theme_advanced_buttons4' 	=> ($frontier_post_mce_button[3] ? $frontier_post_mce_button[3] : '')
			);
	
		$tmp = array('tinymce' => $tinymce_options);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
	
	if (!current_user_can( 'frontier_post_can_media' ))
		{
		$tmp = array('media_buttons' => false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
	
	if ($editor_type == "minimal-visual")
		{
		$tmp = array('teeny' => true, 'quicktags' => false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
		
	if ($editor_type == "minimal-html")
		{
		$tmp = array('teeny' => true, 'tinymce' => false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
		
	if ($editor_type == "text")	
		{
		$tmp = array('quicktags' => false, 'tinymce' =>false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
		
	
	// Build list of categories (3 levels)
	if ($category_type == "multi")
		{
		$cats_selected	= $thispost->post_category;
		if (empty($cats_selected[0]))
			$cats_selected[0] = $default_category;
			
		$catlist 		= array();
		foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => 0)) as $category1) :
			$tmp = Array('cat_ID' => $category1->cat_ID, 'cat_name' => $category1->cat_name);
			array_push($catlist, $tmp);
			foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $category1->cat_ID)) as $category2) :
				$tmp = Array('cat_ID' => $category2->cat_ID, 'cat_name' => "-- ".$category2->cat_name);
				array_push($catlist, $tmp);
				foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $category2->cat_ID)) as $category3) :
					$tmp = Array('cat_ID' => $category3->cat_ID, 'cat_name' => "-- -- ".$category3->cat_name);
					array_push($catlist, $tmp);
				endforeach; // Level 3
			endforeach; // Level 2
		endforeach; //Level 1
		}
		
	if ($category_type == "single")
		{
		if(isset($thispost->ID) )
			{
			$postcategory=get_the_category($thispost->ID); 
			if (array_key_exists(0, $postcategory))
				$postcategoryid = $postcategory[0]->term_id;
			else
				$postcategoryid	= $default_category;				
			}
		else
			{
			$postcategoryid	= $default_category;				
			}	
		}
		
	if ( current_user_can( 'frontier_post_tags_edit' )  )
		{
		$taglist = array();
		if (isset($thispost->ID))
			{
			$tmptags = get_the_tags($thispost->ID);
			if ($tmptags)
				{
				foreach ($tmptags as $tag) :
					array_push($taglist, $tag->name);
				endforeach;
				}
			}
		}
	
	if ($user_can_edit_this_post)
	{
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
					wp_dropdown_categories(array('id'=>'cat', 'hide_empty' => 0, 'name' => 'cat', 'orderby' => 'name', 'selected' => $postcategoryid, 'hierarchical' => true)); 
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
			<?php 	} ?>
		<td>
			<input type="submit"   name="user_post_submit" id="user_post_submit" value=<?php _e("Save", "frontier-post"); ?>>
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