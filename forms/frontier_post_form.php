<?php


if ( strlen($frontier_edit_text_before) > 1 )
	echo '<div id="frontier_edit_text_before">'.$frontier_edit_text_before.'</div>';

//echo 'postid: '.$thispost->ID;
//error_log(print_r(get_post_types(array('public' => true)), true));

//error_log(print_r($thispost, true));
//error_log(print_r(get_post_custom($thispost->ID), true));
//error_log(print_r(get_post_meta($thispost->ID), true));
//error_log(print_r(get_post_types(),true));
//error_log(print_r(get_taxonomies(array('public'   => true, '_builtin' => false)));
//error_log(print_r(get_taxonomies(array('public'   => true)), true) );
//error_log("exclude_cats: ".$frontier_post_excl_cats);

//echo "Post_ type: ".$thispost->post_type."<br>";


frontier_post_output_msg();

//***************************************************************************************
//* Get Custom taxonomy: groups
//***************************************************************************************

//$tax_group_selected = wp_get_post_terms( $thispost->ID, 'group', array("fields" => "ids"));
//error_log(print_r($tax_group_selected, true));
//$frontier_custom_tax = array('group');

// get list of taxonomies from shortcode ($frontier_custom_tax), get values from the db and store them in array, and extract them as variables
/*
error_log(print_r($frontier_custom_tax, true));
$fp_tax_db_values = array();
foreach ( $frontier_custom_tax as $tmp_tax_name ) 
	{
	if ( !empty($tmp_tax_name) )
		{
		error_log(frontier_tax_field_name($tmp_tax_name));
		$tmp_value 			= wp_get_post_terms( $thispost->ID, $tmp_tax_name, array("fields" => "ids"));
		// Set the field name as array key
		$fp_tax_db_values[frontier_tax_field_name($tmp_tax_name)] 	= $tmp_value; 
		}
	}	
error_log(print_r($fp_tax_db_values, true));
*/

//***************************************************************************************
//* Start form
//***************************************************************************************


?>	
	<div class="frontier_post_form"> 

	<table >
	<tbody>
	<form action="" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >
		<!-- Leave hidden fields in form as they are used in the control of the shortcode abilities -->
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
		<input type="hidden" name="posttype" id="posttype" value="<?php echo (isset($thispost->post_type) ? $thispost->post_type : 'post'); ?>">
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
				
				/*
				case "single":
					echo '<td class="frontier_border" width="50%">';
					wp_dropdown_categories(array('id'=>'cat', 'hide_empty' => 0, 'name' => 'categorymulti[]', 'child_of' => $frontier_parent_cat_id, 'orderby' => 'name', 'selected' => $cats_selected[0], 'hierarchical' => true, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)); 
					break;
			
				case "multi":
					echo '<td class="frontier_border" width="50%">';
					echo frontier_post_tax_multi($catlist, $cats_selected, "categorymulti[]", "frontier_categorymulti", 8);
					echo '</br><div class="frontier_helptext">'.__("Select category, multible can be selected using ctrl key", "frontier-post").'</div>';
					echo '</td>';
					break;
    
				case "checkbox":
					echo '<td class="frontier_border" width="50%"><div class="frontier-tax-box">';
					echo frontier_post_tax_checkbox($catlist, $cats_selected, "categorymulti[]", "frontier_categorymulti");
					echo '</td>';
					break;
				
				case "radio":
					echo '<td class="frontier_border" width="50%"><div class="frontier-tax-box">';
					echo frontier_post_tax_radio($catlist, $cats_selected, "categorymulti[]", "frontier_categorymulti");
					echo '</td>';
					break;
				
				
				
				case "readonly":
					echo '<td class="frontier_border" width="50%">';
					foreach ( $cats_selected as $category1) :
						echo $category1->name.", "; 
					endforeach;
					echo '</td>';
					break;
				*/
				
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
			
			//frontier_tax_input($thispost->ID, $frontier_custom_tax, 'checkbox');
			
			/*
			echo '<td class="frontier_border" width="50%">';
			echo '<strong>Group:</strong><br><div class="frontier-tax-box">'; 
			// setup list of groups (Custom taxonomy)

			$tmp_tax_name	= "group";
			$tmp_tax_list 	= frontier_tax_list($tmp_tax_name);
			$tmp_field_name = "fp_tax_"."group"."[]";
			echo frontier_post_tax_checkbox($tmp_tax_list, $tax_group_selected, $tmp_field_name, $tmp_field_name);
			echo '</td>';
			
			echo '<td class="frontier_border" width="50%">';
			echo '<strong>Group:</strong><br><div class="frontier-tax-box">'; 
			// setup list of groups (Custom taxonomy)

			$tmp_tax_name	= "group";
			$tmp_tax_list 	= frontier_tax_list($tmp_tax_name);
			$tmp_field_name = "fp_tax_"."group"."[]";
			echo frontier_post_tax_checkbox($tmp_tax_list, $tax_group_selected, $tmp_field_name, $tmp_field_name);
			echo '</td>';
			*/
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
	</form> 
	</tbody>
	</table>

	</div> <!-- ending div -->  
<?php
	
	// end form file
?>