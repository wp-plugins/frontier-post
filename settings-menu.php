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
		// WP editor tinyMCE has been changed, and wont work - New plugin Frontier Buttons should be used instead
		
		global $wp_version;
		global $wp_roles;
		global $tmp_cap_list;
		
		if ($wp_version >= "3.9")
			{
			update_option("frontier_post_mce_custom", "false");
			}
		
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
				update_option("frontier_post_mce_custom", ( isset($_POST[ "frontier_post_mce_custom"]) ? $_POST[ "frontier_post_mce_custom"] : "false" ) );
				update_option("frontier_post_mail_to_approve", ( isset($_POST[ "frontier_post_mail_to_approve"]) ? $_POST[ "frontier_post_mail_to_approve"] : "false" ) );
				update_option("frontier_post_mail_approved", ( isset($_POST[ "frontier_post_mail_approved"]) ? $_POST[ "frontier_post_mail_approved"] : "false" ) );
				update_option("frontier_post_mail_address", ( isset($_POST[ "frontier_post_mail_address"]) ? $_POST[ "frontier_post_mail_address"] : "false" ) );
				update_option("frontier_post_excl_cats", $_POST[ "frontier_post_excl_cats"]);
				update_option("frontier_post_show_feat_img", ( isset($_POST[ "frontier_post_show_feat_img"]) ? $_POST[ "frontier_post_show_feat_img"] : "false" ) );
				update_option("frontier_post_show_login", ( isset($_POST[ "frontier_post_show_login"]) ? $_POST[ "frontier_post_show_login"] : "false" ) );
				update_option("frontier_post_change_status", ( isset($_POST[ "frontier_post_change_status"]) ? $_POST[ "frontier_post_change_status"] : "false" ) );
				update_option("frontier_default_status", ( isset($_POST[ "frontier_default_status"]) ? $_POST[ "frontier_default_status"] : "publish" ) );
				update_option("frontier_default_editor", ( isset($_POST[ "frontier_default_editor"]) ? $_POST[ "frontier_default_editor"] : "full" ) );
				update_option("frontier_default_cat_select", ( isset($_POST[ "frontier_default_cat_select"]) ? $_POST[ "frontier_default_cat_select"] : "checkbox" ) );
				
				
				$tmp_buttons = array();
				$tmp_buttons[0]	= (isset($_POST[ "frontier_post_mce_button1"]) ? $_POST[ "frontier_post_mce_button1"] : '' );
				$tmp_buttons[1]	= (isset($_POST[ "frontier_post_mce_button2"]) ? $_POST[ "frontier_post_mce_button2"] : '' );
				$tmp_buttons[2]	= (isset($_POST[ "frontier_post_mce_button3"]) ? $_POST[ "frontier_post_mce_button3"] : '' );
				$tmp_buttons[3]	= (isset($_POST[ "frontier_post_mce_button4"]) ? $_POST[ "frontier_post_mce_button4"] : '' );
				update_option("frontier_post_mce_button" ,$tmp_buttons); 
				
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
		$frontier_post_edit_max_age 		= get_option('frontier_post_edit_max_age');
		$frontier_post_delete_max_age 		= get_option('frontier_post_delete_max_age');
		$frontier_post_ppp					= get_option('frontier_post_ppp');
		$frontier_post_page_id				= get_option('frontier_post_page_id');
		$frontier_post_del_w_comments		= (get_option("frontier_post_del_w_comments")) ? get_option("frontier_post_del_w_comments") : "false";
		$frontier_post_edit_w_comments		= (get_option("frontier_post_edit_w_comments")) ? get_option("frontier_post_edit_w_comments") : "false";
		$frontier_post_author_role			= (get_option("frontier_post_author_role")) ? get_option("frontier_post_author_role") : "false";
		$frontier_post_mce_custom			= (get_option("frontier_post_mce_custom")) ? get_option("frontier_post_mce_custom") : "false";
		$frontier_post_mce_button			= get_option("frontier_post_mce_button", array());
		$frontier_post_mail_to_approve		= (get_option("frontier_post_mail_to_approve")) ? get_option("frontier_post_mail_to_approve") : "false"; 
		$frontier_post_mail_approved		= (get_option("frontier_post_mail_approved")) ? get_option("frontier_post_mail_approved") : "false"; 
		$frontier_post_mail_address			= (get_option("frontier_post_mail_address")) ? get_option("frontier_post_mail_address") : get_option("admin-email"); 
		$frontier_post_excl_cats			= get_option("frontier_post_excl_cats") ;
		$frontier_post_show_feat_img		= (get_option("frontier_post_show_feat_img")) ? get_option("frontier_post_show_feat_img") : "false";
		$frontier_post_show_login			= (get_option("frontier_post_show_login")) ? get_option("frontier_post_show_login") : "false";
		$frontier_post_change_status		= (get_option("frontier_post_change_status")) ? get_option("frontier_post_change_status") : "false";
		$frontier_default_status			= get_option("frontier_default_status", "publish");
		$frontier_post_external_cap			= (get_option("frontier_post_external_cap")) ? get_option("frontier_post_external_cap") : "false";
		$frontier_default_editor			= get_option("frontier_default_editor", "full");
		$frontier_default_cat_select		= get_option("frontier_default_cat_select", "checkbox");
				
		$tmp_status_list = get_post_statuses( );
		
		
	
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
					<td><?php _e("Exclude categories (comma separated. list of IDs)", "frontier-post"); ?>:</td>
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
					<?php foreach($editor_types as $key => $value) : ?>   
						<option value='<?php echo $key ?>' <?php echo ( $key == $frontier_default_editor) ? "selected='selected'" : ' ';?>>
							<?php echo $value; ?>
						</option>
					<?php endforeach; ?>
				</select></td>
				<td><?php _e("Default category select", "frontier-post"); ?>:</td>
				<td><select  id="frontier_default_cat_select" name="frontier_default_cat_select" >
					<?php foreach($category_types as $key => $value) : ?>   
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
	
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
			
			<?php 
	
	} // end cap managed externally

	
			// WP editor tinyMCE has been changed, and wont work - New plugin Frontier Buttons should be used instead
			if (($wp_version >= "3.9"))
				{
				update_option("frontier_post_mce_custom", "false");
				$frontier_post_mce_custom = false;
				$mce_readonly = "READONLY";
				//echo("version: ");
				//echo($wp_version);
				}
			else
				{
				$mce_readonly = " ";
				}
			
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
			
			
			
			
			<?php 
			// WP editor tinyMCE has been changed, and wont work - New plugin Frontier Buttons should be used instead
			if (($wp_version >= "3.9"))
				{
				?>
				</tr><tr>
					<th align='left'><?php _e("Use custom editor buttons:", "frontier-post"); ?>:</th>
					<td></td>
					<td><?php _e("From wordpress version 3.9 you need to use separate pluging Frontier Buttons to manage editor buttons", "frontier-post");  ?> &nbsp;
					<a href="http://wordpress.org/plugins/frontier-post/faq/" target="_blank"><?php _e("Additional info: FAQ on plugin site", "frontier-post"); ?>
					</td>
				</tr><tr>
					<th align='left'><?php _e("Template directory:", "frontier-post"); ?>:</th>
					<td></td>
					<td><?php 
					echo frontier_template_dir();  
					// check if frontuier post templates are used
					if (locate_template(array('plugins/frontier-post/'."frontier_form.php"), false, true))
						echo "<br /><strong> frontier_form.php ".__("exists in the template directory", "fontier-post")."</strong>";
					if (locate_template(array('plugins/frontier-post/'."frontier_list.php"), false, true))
						echo "<br /><strong> frontier_list.php ".__("exists in the template directory", "fontier-post")."</strong>";
					
					?> 
					</td>	
			<?php
				}
			else
				{
			?>
				</tr><tr>
					<th align='left'><?php _e("Use custom editor buttons:", "frontier-post"); ?>:</th>
					<td><center><input <?php echo $mce_readonly; ?> type="checkbox" name="frontier_post_mce_custom" value="true" <?php echo ($frontier_post_mce_custom == "true") ? 'checked':''; echo $mce_readonly; ?>></center></td>
					<td><?php _e("Control the buttons showed in the editor (only in frontend)", "frontier-post");  ?> &nbsp;
					<a href="http://wordpress.org/plugins/frontier-post/faq/" target="_blank"><?php _e("Additional info: FAQ on plugin site", "frontier-post"); ?>
					</td>
				</tr><tr>
					<td><?php _e("Custom button row", "frontier-post"); ?>&nbsp;1:</td>
					<td colspan='2'><input type="text" name="frontier_post_mce_button1" value="<?php echo $frontier_post_mce_button[0]; ?>" size='200'></td>
				</tr><tr>
					<td><?php _e("Custom button row", "frontier-post"); ?>&nbsp;2:</td>
					<td colspan='2'><input type="text" name="frontier_post_mce_button2" value="<?php echo $frontier_post_mce_button[1]; ?>" size='200'></td>
				</tr><tr>
					<td><?php _e("Custom button row", "frontier-post"); ?>&nbsp;3:</td>
					<td colspan='2'><input type="text" name="frontier_post_mce_button3" value="<?php echo $frontier_post_mce_button[2]; ?>" size='200'></td>
				</tr><tr>
					<td><?php _e("Custom button row", "frontier-post"); ?>&nbsp;4:</td>
					<td colspan='2'><input type="text" name="frontier_post_mce_button4" value="<?php echo $frontier_post_mce_button[3]; ?>" size='200'></td>
				
			<?php } ?>		
				</tr>
			</table>
			</br>
			<?php
			if (($wp_version < "3.9"))
				{
			?>
			<b><?php _e("Suggested buttons", "frontier-post") ?>:</b></br><i>
					<?php _e("Row", "frontier-post");?>&nbsp;1: bold, italic, underline, strikethrough, bullist, numlist, blockquote, justifyleft, justifycenter, justifyright, link, unlink, wp_more, spellchecker, fullscreen, wp_adv</br>
					<?php _e("Row", "frontier-post");?>&nbsp;2: emotions, formatselect, justifyfull, forecolor, pastetext, pasteword, removeformat, charmap, outdent, indent, undo, redo, wp_help</br>
					<?php _e("Row", "frontier-post");?>&nbsp;3: search,replace,|,tablecontrols</br>
			<?php } ?>
			<hr>
			<br/>
			<br/>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
			

		</form>
		<hr>
		<h1> <?php _e("Additional info", "frontier-post"); ?> </h1>
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
			<td valign="top">
				<table valign="top">
					<tr>
					<th>Shortcodes: </th>
					</tr><tr>
						<td align="left"><pre>[frontier-post]</pre></td>
						<td align="left">Will show the My post list and link to create new post</td>
					</tr><tr>
						<td align="left"><pre>[frontier-post frontier_mode=add]</pre></td>
						<td align="left">Will show the add post form in the page where shortcode is entered</td>
					</tr><tr>
						<td align="left"><pre>[frontier-post frontier_parent_cat_id=7]</pre></td>
						<td align="left">Will limit the categories to the children of category with id=7</td>
					</tr><tr>
						<td align="left"><pre>[frontier-post frontier_cat_id=24]</pre></td>
						<td align="left">Will default to category with id=24</td>
						
					
					
					
					</tr>
				</table>
			</td>
			</tr>
		</table>
		<hr>	

	</div> <!-- frontier-admin-menu -->
	</div> <!-- wrap -->

	<?php 
	} // end function frontier_post_settings_page
	
	?>