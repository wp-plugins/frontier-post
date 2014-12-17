<?php
/*
Admin settings menu - Frontier Post
*/

include("include/frontier_post_defaults.php");

function frontier_post_settings_menu() 
	{
	//create new top-level menu
	add_options_page('Frontier Post', 'Frontier Post', 'administrator', __FILE__, 'frontier_post_settings_page');
	}

function frontier_post_settings_page() 
	{
		include("include/frontier_post_defaults.php");
		//must check that the user has the required capability 
		if (!current_user_can('manage_options'))
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}

		global $wp_version;
		global $wp_roles;
		global $tmp_cap_list;
		
		
		
		if ( !isset( $wp_roles ) )
			$wp_roles = new WP_Roles();
				
		$roles 			= $wp_roles->get_names();
		
		// check for edit_published posts, for the edit_redir option
		$cap2check = "edit_published_posts";
		
		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if( isset($_POST[ "frontier_isupdated_hidden" ]) && $_POST[ "frontier_isupdated_hidden" ] == 'Y' ) 
			{
				
				// get form data, and save it
				update_option("frontier_post_edit_max_age", ( (int) $_POST[ "frontier_post_edit_max_age" ] ) );
				update_option("frontier_post_delete_max_age", ( (int) $_POST[ "frontier_post_delete_max_age" ] ) );
				update_option("frontier_post_ppp", ( (int) $_POST[ "frontier_post_ppp"] ) );
				update_option("frontier_post_page_id", ( (int) $_POST[ "frontier_post_page_id"] ) );
				update_option("frontier_post_del_w_comments", ( isset($_POST[ "frontier_post_del_w_comments"]) ? $_POST[ "frontier_post_del_w_comments"] : "false" ) );
				update_option("frontier_post_edit_w_comments", ( isset($_POST[ "frontier_post_edit_w_comments"]) ? $_POST[ "frontier_post_edit_w_comments"] : "false" ) );
				update_option("frontier_post_use_draft", ( isset($_POST[ "frontier_post_use_draft"]) ? $_POST[ "frontier_post_use_draft"] : "false" ) );
				update_option("frontier_post_author_role", ( isset($_POST[ "frontier_post_author_role"]) ? $_POST[ "frontier_post_author_role"] : "false" ) );
				update_option("frontier_post_mail_to_approve", ( isset($_POST[ "frontier_post_mail_to_approve"]) ? $_POST[ "frontier_post_mail_to_approve"] : "false" ) );
				update_option("frontier_post_mail_approved", ( isset($_POST[ "frontier_post_mail_approved"]) ? $_POST[ "frontier_post_mail_approved"] : "false" ) );
				update_option("frontier_post_mail_address", ( isset($_POST[ "frontier_post_mail_address"]) ? $_POST[ "frontier_post_mail_address"] : "false" ) );
				update_option("frontier_post_excl_cats", $_POST[ "frontier_post_excl_cats"]);
				update_option("frontier_post_show_feat_img", ( isset($_POST[ "frontier_post_show_feat_img"]) ? $_POST[ "frontier_post_show_feat_img"] : "false" ) );
				update_option("frontier_post_show_login", ( isset($_POST[ "frontier_post_show_login"]) ? $_POST[ "frontier_post_show_login"] : "false" ) );
				update_option("frontier_post_change_status", ( isset($_POST[ "frontier_post_change_status"]) ? $_POST[ "frontier_post_change_status"] : "false" ) );
				update_option("frontier_post_catid_list", ( isset($_POST[ "frontier_post_catid_list"]) ? $_POST[ "frontier_post_catid_list"] : "" ) );
				update_option("frontier_post_hide_status", ( isset($_POST[ "frontier_post_hide_status"]) ? $_POST[ "frontier_post_hide_status"] : "false" ) );
				update_option("frontier_post_show_msg", ( isset($_POST[ "frontier_post_show_msg"]) ? $_POST[ "frontier_post_show_msg"] : "false" ) );
				
				update_option("frontier_post_editor_lines", (int) $_POST[ "frontier_post_editor_lines" ]);
				update_option("frontier_post_hide_title_ids", $_POST[ "frontier_post_hide_title_ids"]  );
				
				
				update_option("frontier_default_status", ( isset($_POST[ "frontier_default_status"]) ? $_POST[ "frontier_default_status"] : "publish" ) );
				update_option("frontier_default_editor", ( isset($_POST[ "frontier_default_editor"]) ? $_POST[ "frontier_default_editor"] : "full" ) );
				update_option("frontier_default_cat_select", ( isset($_POST[ "frontier_default_cat_select"]) ? $_POST[ "frontier_default_cat_select"] : "checkbox" ) );
				
				$frontier_submit_buttons			= array(
						'save' 			=> (isset($_POST[ "frontier_submit_save"]) 			? $_POST[ "frontier_submit_save"] 		: "false" ), 
						'savereturn' 	=> (isset($_POST[ "frontier_submit_savereturn"]) 	? $_POST[ "frontier_submit_savereturn"] : "false" ),
						'preview' 		=> (isset($_POST[ "frontier_submit_preview"]) 		? $_POST[ "frontier_submit_preview"] 	: "false" ),
						'cancel' 		=> (isset($_POST[ "frontier_submit_cancel"]) 		? $_POST[ "frontier_submit_cancel"] 	: "false" )
						);
		
				update_option("frontier_post_submit_buttons", $frontier_submit_buttons);
				
				
				if (get_option("frontier_post_author_role") == "true")
					{
					// add role if it doesnt exists
					if (!get_role($frontier_author_role_name))
						add_role($frontier_author_role_name, __("Frontend Author"), $frontier_author_default_caps);					
					}
				else
					{
					// remove role if it  exists
					if (get_role($frontier_author_role_name))
						remove_role($frontier_author_role_name);					
					}
			
			
			// only save caps if managed from within Frontier Post, else save default editor and category select type
			if ( get_option("frontier_post_external_cap ", "false") == "true" )
				{
				update_option("frontier_post_external_cap", ( isset($_POST[ "frontier_post_external_cap"]) ? $_POST[ "frontier_post_external_cap"] : "false" ) );
				}
			else
				{
				update_option("frontier_post_external_cap", ( isset($_POST[ "frontier_post_external_cap"]) ? $_POST[ "frontier_post_external_cap"] : "false" ) );
				
				// Need to reinstate roles, as they have been manipulated above
				$wp_roles	= new WP_Roles();
				$roles 	  	= $wp_roles->get_names();
				
				//save capability settings
				$tmp_cap_list	= $frontier_option_list;			
				$saved_options = get_option('frontier_post_options', array() );
				foreach( $roles as $key => $item ) 
					{
					$xrole = get_role($key);
					$xrole_caps = $xrole->capabilities;
					if (( array_key_exists($cap2check, $xrole_caps) ) && ($xrole_caps[$cap2check]) )
						$tmp_cap_ok = true;
					else
						$tmp_cap_ok = false;
				
					foreach($tmp_cap_list as $tmp_cap)
						{
						
						$tmp_name		= 'frontier_post_'.$key.'_'.$tmp_cap;
						$def_value		= "false";
						
						if ($tmp_cap == 'editor')
							$def_value		= "minimal-visual";
						
						if ($tmp_cap == 'category')
							$def_value		= "multi";
							
						if (isset($_POST[ $tmp_name]))
							$tmp_value		= ( $_POST[ $tmp_name] ? $_POST[ $tmp_name] : $def_value );
						else
							$tmp_value		= $def_value;
							
						// set capability, but not for editor and category
						
						if (($tmp_cap != 'editor') && ($tmp_cap != 'category') && ($tmp_cap != 'default_category'))
							{
							if ( $tmp_value == "true" )
								$xrole->add_cap( 'frontier_post_'.$tmp_cap );
							else
								$xrole->remove_cap( 'frontier_post_'.$tmp_cap );
							}
						$saved_options[$key][$tmp_cap] = $tmp_value;
						
						} //caps
					
					
					} // roles
					
				// Save options
				//error_log("saving options");
				update_option('frontier_post_options', $saved_options);
				
				} // End external managed capabilities
				
				
				// Put an settings updated message on the screen
				?>
					<div class="updated"><p><strong><?php _e('Settings saved.', 'frontier-post' ); ?></strong></p></div>
				<?php
			}
		
		// get values from db
		$frontier_post_edit_max_age 		= get_option('frontier_post_edit_max_age', 0);
		$frontier_post_delete_max_age 		= get_option('frontier_post_delete_max_age', 0);
		$frontier_post_ppp					= get_option('frontier_post_ppp', 25);
		$frontier_post_page_id				= get_option('frontier_post_page_id');
		$frontier_post_del_w_comments		= (get_option("frontier_post_del_w_comments")) ? get_option("frontier_post_del_w_comments") : "false";
		$frontier_post_edit_w_comments		= (get_option("frontier_post_edit_w_comments")) ? get_option("frontier_post_edit_w_comments") : "false";
		$frontier_post_author_role			= (get_option("frontier_post_author_role")) ? get_option("frontier_post_author_role") : "false";
		$frontier_post_mail_to_approve		= (get_option("frontier_post_mail_to_approve")) ? get_option("frontier_post_mail_to_approve") : "false"; 
		$frontier_post_mail_approved		= (get_option("frontier_post_mail_approved")) ? get_option("frontier_post_mail_approved") : "false"; 
		$frontier_post_mail_address			= (get_option("frontier_post_mail_address")) ? get_option("frontier_post_mail_address") : get_option("admin-email"); 
		$frontier_post_excl_cats			= get_option("frontier_post_excl_cats", "") ;
		$frontier_post_show_feat_img		= (get_option("frontier_post_show_feat_img")) ? get_option("frontier_post_show_feat_img") : "false";
		$frontier_post_show_login			= (get_option("frontier_post_show_login")) ? get_option("frontier_post_show_login") : "false";
		$frontier_post_change_status		= (get_option("frontier_post_change_status")) ? get_option("frontier_post_change_status") : "false";
		$frontier_post_catid_list 			= (get_option("frontier_post_catid_list")) ? get_option("frontier_post_catid_list") : "false"; 
		$frontier_post_editor_lines 		= get_option('frontier_post_editor_lines', 300);
		$frontier_default_status			= get_option("frontier_default_status", "publish");
		$frontier_post_external_cap			= (get_option("frontier_post_external_cap")) ? get_option("frontier_post_external_cap") : "false";
		$frontier_post_hide_status			= get_option("frontier_post_hide_status", "false");
		$frontier_post_show_msg				= get_option("frontier_post_show_msg", "false");
		$frontier_post_hide_title_ids		= get_option("frontier_post_hide_title_ids", "");
		
		$frontier_default_editor			= get_option("frontier_default_editor", "full");
		$frontier_default_cat_select		= get_option("frontier_default_cat_select", "checkbox");
		$frontier_submit_buttons			= get_option("frontier_post_submit_buttons", $frontier_default_submit);
		
		$frontier_submit_buttons['save']		= isset($frontier_submit_buttons['save']) 		? $frontier_submit_buttons['save'] 			: "true";
		$frontier_submit_buttons['savereturn']	= isset($frontier_submit_buttons['savereturn']) ? $frontier_submit_buttons['savereturn'] 	: "true";
		$frontier_submit_buttons['preview']		= isset($frontier_submit_buttons['preview']) 	? $frontier_submit_buttons['preview'] 		: "true";		
		$frontier_submit_buttons['cancel']		= isset($frontier_submit_buttons['cancel']) 	? $frontier_submit_buttons['cancel'] 		: "true";		
		
		$tmp_status_list 					= get_post_statuses( );
		$saved_options 						= get_option('frontier_post_options', array() );
		
	
		?>
	
		<div class="wrap">
		<div class="frontier-admin-menu">
		<h2><?php _e("Frontier Post Settings", "frontier-post") ?> </h2>
		

		<form name="frontier_post_settings" method="post" action="">
			<input type="hidden" name="frontier_isupdated_hidden" value="Y">
			<table border="1">
				<tr>
					<td><?php _e("Allow edit of posts with comments", "frontier-post"); ?>:</td>
					<td><center><input type="checkbox" name="frontier_post_edit_w_comments" value="true" <?php echo ($frontier_post_edit_w_comments == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Max age in days to allow edit of post", "frontier-post");?>:</td>
					<td><input type="text" name="frontier_post_edit_max_age" value="<?php echo $frontier_post_edit_max_age; ?>" /></td>
				</tr><tr>
					<td><?php _e("Allow deletion of posts with comments", "frontier-post"); ?>:</td>
					<td><center><input type="checkbox" name="frontier_post_del_w_comments" value="true" <?php echo ($frontier_post_del_w_comments == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Max age in days to allow delete of post", "frontier-post"); ?>:</td>
					<td><input type="text" name="frontier_post_delete_max_age" value="<?php echo $frontier_post_delete_max_age; ?>" /></td>
				
				</tr><tr>
					<td><?php _e("Number of post per page", "frontier-post"); ?>:</td>
					<td><input type="text" name="frontier_post_ppp" value="<?php echo $frontier_post_ppp; ?>" /></td>
					<td><?php _e("Page containing [frontier-post] shortcode:", "frontier-post"); ?></td>
					<td>
						<?php 
						wp_dropdown_pages(array('id'=>'frontier_post_page_id', 'dept' => 1, 'hide_empty' => 0, 'name' => 'frontier_post_page_id', 'selected' => $frontier_post_page_id, 'hierarchical' => true, 'show_option_none' => __('None'))); 
						?>
					</td>
				
				</tr><tr>
					<td><?php _e("Send email to Admins on post to approve", "frontier-post"); ?>:</td>
					<td><center><input type="checkbox" name="frontier_post_mail_to_approve" value="true" <?php echo ($frontier_post_mail_to_approve == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Send email to author when post is approved", "frontier-post"); ?>:</td>
					<td><center><input type="checkbox" name="frontier_post_mail_approved" value="true" <?php echo ($frontier_post_mail_approved == "true") ? 'checked':''; ?>></center></td>
				</tr><tr>
					<td><?php _e("Default status for new posts", "frontier-post"); ?>:</td>
					<td><select  id="frontier_default_status" name="frontier_default_status" >
						<?php foreach($tmp_status_list as $key => $value) : ?>   
							<option value='<?php echo $key ?>' <?php echo ( $key == $frontier_default_status) ? "selected='selected'" : ' ';?>>
								<?php echo $value; ?>
							</option>
						<?php endforeach; ?>
					</select></td>
				</tr><tr>
					<td><?php _e("Approver email (ex: name1@domain.xx, name2@domain.xx)", "frontier-post"); ?>:</td>
					<td colspan="3" ><input size="100" type="text" name="frontier_post_mail_address" value="<?php echo $frontier_post_mail_address; ?>" /></td>
				</tr><tr>
					<td><?php _e("Exclude categories (comma separated list of IDs)", "frontier-post"); ?>:</td>
					<td colspan="3" ><input size="100" type="text" name="frontier_post_excl_cats" value="<?php echo $frontier_post_excl_cats; ?>" /></td>
				</tr>
			</table>
	<?php
	// Do not show cababilities if managed externally	
	if ( $frontier_post_external_cap == "true" )
		{
		?>
		<hr>
		<table border="1">
			<tr>
				<th colspan="4"></center><?php _e("Capabilities managed externally, below is for all roles", "frontier-post"); ?></center></th>
			</tr><tr>
				<td><?php _e("Default Editor", "frontier-post"); ?>:</td>
				<td><select  id="frontier_default_editor" name="frontier_default_editor" >
					<?php foreach($editor_types as $value => $key) : ?>   
						<option value='<?php echo $key ?>' <?php echo ( $key == $frontier_default_editor) ? "selected='selected'" : ' ';?>>
							<?php echo $value; ?>
						</option>
					<?php endforeach; ?>
				</select></td>
				<td><?php _e("Default category select", "frontier-post"); ?>:</td>
				<td><select  id="frontier_default_cat_select" name="frontier_default_cat_select" >
					<?php foreach($category_types as $value => $key) : ?>   
						<option value='<?php echo $key ?>' <?php echo ( $key == $frontier_default_cat_select) ? "selected='selected'" : ' ';?>>
							<?php echo $value; ?>
						</option>
					<?php endforeach; ?>
				</select></td>
		</table><hr>	
		<?php		
		}
	else
		{
	?>
			<table border="1">
					<tr>
					<th colspan="8"></center><?php _e("Capabilities by user role", "frontier-post"); ?></center></th>
					<tr></tr>
					<tr></tr>
						<th width="10%"><?php _e("Role", "frontier-post")?></th>
						<th width="10%"><?php _e("Can Add", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Can Edit", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Can Publish", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Private Posts", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Allow Drafts", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Can Delete", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Frontier Edit", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Show admin bar", "frontier-post"); ?></th>
					
					</tr><tr>
					
			<?php
			
			//Build table based on values from options
			
			$saved_options 	= get_option('frontier_post_options', array() );
			$tmp_cap_list	= $frontier_option_list;
			
			//Only show first part of options, so the table doesnt get too wide
			$tmp_cap_list	= array_slice($tmp_cap_list,0,$frontier_option_slice);
			
			foreach( $roles as $key => $item ) 
				{
				
				echo '<tr>';
				echo '<td>'.$item.'</td>';
				
				if ( !array_key_exists($key, $saved_options) )
					$saved_options[$key] = array();
				
				$tmp_role_settings = $saved_options[$key];
				
				$xrole = get_role($key);
				$xrole_caps = $xrole->capabilities;
				
				if (( array_key_exists($cap2check, $xrole_caps) ) && ($xrole_caps[$cap2check]) )
					$tmp_cap_ok = true;
				else
					$tmp_cap_ok = false;
				
				//$role_settings = 
				
				foreach($tmp_cap_list as $tmp_cap)
					{
					$tmp_name		= 'frontier_post_'.$key.'_'.$tmp_cap;
					
					if ( array_key_exists($tmp_cap, $tmp_role_settings))
						$tmp_value	= ( $saved_options[$key][$tmp_cap] ? $saved_options[$key][$tmp_cap] : "false" );
					else
						$tmp_value	= "false";
						
					if ( $tmp_value == "true" )
						$tmp_checked	= " checked"; 
					else
						$tmp_checked	= " "; 
					
					echo '<td><center>';
					// can only enable redirect for edit posts if role has the necessary capabilities
					if (( $tmp_cap == 'redir_edit' ) && (!$tmp_cap_ok) )
						echo _e('NA', 'frontier-post');
					else
						echo '<input value="true" type="checkbox" name="'.$tmp_name.'" id="' .$tmp_name. '" '. $tmp_checked.' />';
						
					echo '</center></td>';
					} // end cap
					echo '</tr>';
				} // end roles
			
			
			
			?>
			</table>
			
			<?php
			$tmp_cap_list	= $frontier_option_list;
			// show second part of options
			$tmp_cap_list	= array_slice($tmp_cap_list,$frontier_option_slice );
			//print_r($tmp_cap_list);
			
			?>
			<table border="1">
					<tr>
					<th colspan="7"></center><?php _e("Editor options", "frontier-post"); ?></center></th>
					<tr></tr>
					<tr></tr>
						<th width="10%"><?php _e("Role", "frontier-post")?></th>
						<th width="10%"><?php _e("Edit Excerpt", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Edit Tags", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Media Upload", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Editor Type", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Category", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Default Category", "frontier-post"); ?></th>
					</tr><tr>
					
			<?php
			
			
			foreach( $roles as $key => $item ) 
				{
				
				echo '<tr>';
				echo '<td>'.$item.'</td>';
				
				if ( !array_key_exists($key, $saved_options) )
					$saved_options[$key] = array();
				
				$tmp_role_settings = $saved_options[$key];
				
				$xrole = get_role($key);
				$xrole_caps = $xrole->capabilities;
				
				if (( array_key_exists($cap2check, $xrole_caps) ) && ($xrole_caps[$cap2check]) )
					$tmp_cap_ok = true;
				else
					$tmp_cap_ok = false;
				
				//$role_settings = 
				
				foreach($tmp_cap_list as $tmp_cap)
					{
					$tmp_name		= 'frontier_post_'.$key.'_'.$tmp_cap;
					
					if ( array_key_exists($tmp_cap, $tmp_role_settings))
						$tmp_value	= ( $saved_options[$key][$tmp_cap] ? $saved_options[$key][$tmp_cap] : "false" );
					else
						$tmp_value	= "false";
					
					
					if ($tmp_cap == 'editor' || $tmp_cap == 'category' || $tmp_cap == 'default_category')
						{
						echo '<td>';
						
						if ($tmp_cap == 'editor')
							$optionlist = $editor_types;
							
						if ($tmp_cap == 'category')
							$optionlist = $category_types;
						
						if ($tmp_cap == 'default_category')
							{
							//$optionlist = get_categories(array('hide_empty' => 0, 'orderby' => 'name', 'hierarchical' => true)); 
							if (empty($tmp_value))
								$tmp_value = get_option("default_category");
								
							wp_dropdown_categories(array('id'=>$tmp_name, 'hide_empty' => 0, 'name' => $tmp_name, 'orderby' => 'name', 'selected' => $tmp_value, 'hierarchical' => true)); 
							}
						else
							{
							?>	
							<select  id="<?php echo $tmp_name ?>" name="<?php echo $tmp_name ?>" >
							<?php foreach($optionlist as $desc => $id) : ?>   
								<option value='<?php echo $id ?>' <?php echo ( $id == $tmp_value) ? "selected='selected'" : ' ';?>>
									<?php echo $desc; ?>
								</option>
							<?php endforeach; ?>
							</select>
							<?php
							}
						echo '</td>';
						}
					else
						{
						if ( $tmp_value == "true" )
							$tmp_checked	= " checked"; 
						else
							$tmp_checked	= " "; 
					
						echo '<td><center>';
						// can only enable redirect for edit posts if role has the necessary capabilities
						if (( $tmp_cap == 'redir_edit' ) && (!$tmp_cap_ok) )
							echo _e('NA', 'frontier-post');
						else
							echo '<input value="true" type="checkbox" name="'.$tmp_name.'" id="' .$tmp_name. '" '. $tmp_checked.' />';
						
						echo '</center></td>';
						}
					
					} // end cap
					echo '</tr>';
				} // end roles
	
	
			?>
			</table>
			</br>
			</table>		
				</tr><tr>
					<td colspan="2">
					<b><?php _e("Notice", "frontier-post") ?></b></br><i>
					<?php _e("- Media upload is not available to Contributors and Subscribers by Wordpress capabilities", "frontier-post");?></br>
					<?php _e("- Wordpress standard rolemodel does not allow Contributors and Subscribers to add/edit/delete posts, but you can bypass this above", "frontier-post");?></br>
					<?php _e("- Frontier Edit means that if a user selects the dit link on a post, Frontier will be used to edit instead of backend", "frontier-post");?></br>
					<?php _e("-    - This is only if selected from frontend, if edit from backend, backend interface will be used", "frontier-post");?></br>
					</i></td>
				</tr>
			</table>
	
			
			<?php 
	
	} // end cap managed externally

	
			
			?>
			
			<h2><?php _e("Advanced Settings", "frontier-post") ?></h2>
			</br>
			<table border="1">
				<tr>
					<th align='left'><?php _e("Allow users to change status from Published", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_change_status" value="true" <?php echo ($frontier_post_change_status == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Once published users can change status back to draft/pending", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Add Frontier Author user role:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_author_role" value="true" <?php echo ($frontier_post_author_role == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Adds a new role: Frontend Author to Wordpress", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Use featured image:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_show_feat_img" value="true" <?php echo ($frontier_post_show_feat_img == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Enables selection of featured image from frontend form (does not work perfectly)", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Show link to login page:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_show_login" value="true" <?php echo ($frontier_post_show_login == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Shows link to wp-login.php after text: Please login", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Set Capabilities externally:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_external_cap" value="true" <?php echo ($frontier_post_external_cap == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("If checked capabilities (see below) will be managed from external plugin ex.: User Role Editor", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Show ID in category list:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_catid_list" value="true" <?php echo ($frontier_post_catid_list == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("If checked ID column will be added to the standard category list in admin panel", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Hide post status:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_hide_status" value="true" <?php echo ($frontier_post_hide_status == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Hide the post status on the entry form", "frontier-post"); ?></td>
				</tr><tr>
					<th align='left'><?php _e("Show add/update/delete messages:", "frontier-post"); ?>:</th>
					<td><center><input type="checkbox" name="frontier_post_show_msg" value="true" <?php echo ($frontier_post_show_msg == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Show message on the form confirming a post has been added/updated/deleted", "frontier-post"); ?></td>
				
				</tr><tr>
					<td><?php _e("Show submit buttons on post edit form", "frontier-post"); ?>:</td>
					<td colspan="2">&nbsp
						<?php _e("Save", "frontier-post"); ?>: 
						<input type="checkbox" name="frontier_submit_save" value="true" <?php echo ($frontier_submit_buttons['save'] == "true") ? 'checked':''; ?>>
						&nbsp;|&nbsp;<?php _e("Save & Return", "frontier-post"); ?>:
						<input type="checkbox" name="frontier_submit_savereturn" value="true" <?php echo ($frontier_submit_buttons['savereturn'] == "true") ? 'checked':''; ?>>
						&nbsp;|&nbsp;<?php _e("Save & Preview", "frontier-post"); ?>:
						<input type="checkbox" name="frontier_submit_preview" value="true" <?php echo ($frontier_submit_buttons['preview'] == "true") ? 'checked':''; ?>>
						&nbsp;|&nbsp;<?php _e("Cancel", "frontier-post"); ?>:
						<input type="checkbox" name="frontier_submit_cancel" value="true" <?php echo ($frontier_submit_buttons['cancel'] == "true") ? 'checked':''; ?>>
						
					</td>
				</tr><tr>
					<td><?php _e("Number of editor lines", "frontier-post"); ?>:</td>
					<td colspan="2"><input type="text" name="frontier_post_editor_lines" value="<?php echo $frontier_post_editor_lines; ?>" /></td>
				</tr><tr>
					<td><?php _e("Hide title on these pages", "frontier-post"); ?>:</td>
					<td colspan="2" ><input size="100" type="text" name="frontier_post_hide_title_ids" value="<?php echo $frontier_post_hide_title_ids; ?>" />
					<br> <?php _e("comma separated list of page IDs", "frontier-post"); ?></td>
				
								
				</tr><tr>
					<th align='left'><?php _e("Use custom editor buttons:", "frontier-post"); ?>:</th>
					<td></td>
					<td>Has been moved to a separate pluging: <a href="http://wordpress.org/plugins/frontier-buttons/" target="_blank">Frontier Buttons</a>
					</td>
				</tr><tr>
					<th align='left'><?php _e("Template directory:", "frontier-post"); ?>:</th>
					<td></td>
					<td>
					<?php 
					echo frontier_template_dir();  
					// check if frontuier post templates are used
					if (locate_template(array('plugins/frontier-post/'."frontier_form.php"), false, true))
						echo "<br /><strong> frontier_form.php ".__("exists in the template directory", "fontier-post")."</strong>";
					if (locate_template(array('plugins/frontier-post/'."frontier_list.php"), false, true))
						echo "<br /><strong> frontier_list.php ".__("exists in the template directory", "fontier-post")."</strong>";					
					if (locate_template(array('plugins/frontier-post/'."frontier_post.css"), false, true))
						echo "<br /><strong> frontier_post.css ".__("exists in the template directory", "fontier-post")."</strong>";					
					?> 
					</td>	
				</tr>
			</table>
			<br/>
			<br/>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
			

		</form>
		<hr>
		<h1> <?php _e("Additional info", "frontier-post"); ?> </h1>
		<table width="100%" border="2">
			<tr>
				<th>Shortcodes:</th>
				<th><?php _e('Parameters', 'frontier-post'); ?></th>
				<th>&nbsp;</th>
			</tr><tr>
				<td align="left"><pre>[frontier-post]</pre></td>
				<td></td>
				<td align="left"><?php _e('Will show the My post list and link to create new post', 'frontier-post'); ?></td>
			</tr><tr>
				<td></td>
				<td align="left"><pre>frontier_mode="add"</pre></td>
				<td align="left"><?php _e('Will show the add post form in the page where shortcode is entered', 'frontier-post'); ?></td>
			</tr><tr>
				<td></td>
				<td align="left"><pre>frontier_parent_cat_id=7</pre></td>
				<td align="left"><?php _e('Will limit the categories to the children of category with id=7', 'frontier-post'); ?></td>
			</tr><tr>
				<td></td>
				<td align="left"><pre>frontier_cat_id=24</pre></td>
				<td align="left"><?php _e('Will default to category with id=24 when a new post is created', 'frontier-post'); ?></td>
			</tr><tr>
				<td></td>
				<td align="left"><pre>frontier_list_all_posts="true"</pre></td>
				<td align="left"><?php _e('Will list all published posts, not only current users, can be combined with frontier_cat_id=24', 'frontier-post'); ?></td>
			</tr><tr>
				<td></td>
				<td align="left"><pre> frontier_list_cat_id=5</pre></td>
				<td align="left"><?php _e('Is used with frontier_list_all_posts to limit list to category with Id=5', 'frontier-post'); ?></td>
			</tr><tr>
				<td></td>
				<td align="left"><pre>frontier_return_text="Publish"</pre></td>
				<td align="left"><?php _e('Will change text on submit button to Publish', 'frontier-post'); ?></td>
			
			</tr>
		</table>
		<hr>
		<table width="100%">
			<tr>
			<th align="left"><strong><?php _e("Frontier Post Capabilities", "frontier-post"); ?> :</strong></th>
			</tr><tr>
			<td>	
				<?php 
				foreach($frontier_cap_list as $tmp_cap) : 
					echo $tmp_cap."<br>";
				endforeach; 
				?>
			</td>
			</tr>
		</table>
		<hr>	

	</div> <!-- frontier-admin-menu -->
	</div> <!-- wrap -->

	<?php 
	} // end function frontier_post_settings_page
	
	?>