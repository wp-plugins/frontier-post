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
				
				// Read their posted value, and save it
				update_option("frontier_post_edit_max_age", ( (int) $_POST[ "frontier_post_edit_max_age" ] ) );
				update_option("frontier_post_delete_max_age", ( (int) $_POST[ "frontier_post_delete_max_age" ] ) );
				update_option("frontier_post_ppp", ( (int) $_POST[ "frontier_post_ppp"] ) );
				update_option("frontier_post_page_id", ( (int) $_POST[ "frontier_post_page_id"] ) );
				update_option("frontier_post_del_w_comments", ( isset($_POST[ "frontier_post_del_w_comments"]) ? $_POST[ "frontier_post_del_w_comments"] : "false" ) );
				update_option("frontier_post_edit_w_comments", ( isset($_POST[ "frontier_post_edit_w_comments"]) ? $_POST[ "frontier_post_edit_w_comments"] : "false" ) );
				update_option("frontier_post_use_draft", ( isset($_POST[ "frontier_post_use_draft"]) ? $_POST[ "frontier_post_use_draft"] : "false" ) );
				
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
						
						if (($tmp_cap != 'editor') && ($tmp_cap != 'category'))
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
				update_option('frontier_post_options', $saved_options);
				
				
				
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
		//$frontier_post_use_draft			= (get_option("frontier_post_use_draft")) ? get_option("frontier_post_use_draft") : "false";
		
		//$frontier_options =
		?>
	
		<div class="wrap">
		<div class="frontier-admin-menu">
		<h2>Frontier Post Settings</h2>

		<form name="frontier_post_settings" method="post" action="">
			<input type="hidden" name="frontier_isupdated_hidden" value="Y">
			<table border="1">
				<tr>
					<td><?php _e("Allow edit of posts with comments:", "frontier-post"); ?>:</td>
					<td><center><input type="checkbox" name="frontier_post_edit_w_comments" value="true" <?php echo ($frontier_post_edit_w_comments == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Max age in days to allow edit of post:", "frontier-post");?>:</td>
					<td><input type="text" name="frontier_post_edit_max_age" value="<?php echo $frontier_post_edit_max_age; ?>" /></td>
				</tr><tr>
					<td><?php _e("Allow deletion of posts with comments:", "frontier-post"); ?>:</td>
					<td><center><input type="checkbox" name="frontier_post_del_w_comments" value="true" <?php echo ($frontier_post_del_w_comments == "true") ? 'checked':''; ?>></center></td>
					<td><?php _e("Max age in days to allow delete of post:", "frontier-post"); ?>:</td>
					<td><input type="text" name="frontier_post_delete_max_age" value="<?php echo $frontier_post_delete_max_age; ?>" /></td>
				</tr><tr>
					<td><?php _e("Number of post per page:", "frontier-post"); ?>:</td>
					<td><input type="text" name="frontier_post_ppp" value="<?php echo $frontier_post_ppp; ?>" /></td>
					<td><?php _e("Page containing [frontier-post] shortcode:", "frontier-post"); ?></td>
					<td>
						<?php 
						wp_dropdown_pages(array('id'=>'frontier_post_page_id', 'dept' => 1, 'hide_empty' => 0, 'name' => 'frontier_post_page_id', 'selected' => $frontier_post_page_id, 'hierarchical' => true, 'show_option_none' => __('None'))); 
						?>
					</td>
				</td>
				</tr>
			</table>
			
			<table border="1">
					<tr>
					<th colspan="7"></center><?php _e("Capabilities by user role", "frontier-post"); ?></center></th>
					<tr></tr>
					<tr></tr>
						<th width="30%"><?php _e("Role", "frontier-post")?></th>
						<th width="10%"><?php _e("Can Add", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Can Edit", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Can Publish", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Allow Drafts", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Can Delete", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Frontier Edit", "frontier-post"); ?></th>
						
					
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
					$tmp_value		= ( $saved_options[$key][$tmp_cap] ? $saved_options[$key][$tmp_cap] : "false" );
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
						<th width="30%"><?php _e("Role", "frontier-post")?></th>
						<th width="10%"><?php _e("Edit Excerpt", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Edit Tags", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Media Upload", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Editor Type", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Category", "frontier-post"); ?></th>
					</tr><tr>
					
			<?php
			
			
			foreach( $roles as $key => $item ) 
				{
				
				echo '<tr>';
				echo '<td>'.$item.'</td>';
				
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
					$tmp_value		= ( $saved_options[$key][$tmp_cap] ? $saved_options[$key][$tmp_cap] : "false" );
					
					if ($tmp_cap == 'editor' || $tmp_cap == 'category')
						{
						if ($tmp_cap == 'editor')
							$optionlist = $editor_types;
							
						if ($tmp_cap == 'category')
							$optionlist = $category_types;
							
						echo '<td>';
						?>	
						<select  id="post_status" name="<?php echo $tmp_name ?>" >
						<?php foreach($optionlist as $desc => $id) : ?>   
							<option value='<?php echo $id ?>' <?php echo ( $id == $tmp_value) ? "selected='selected'" : ' ';?>>
								<?php echo $desc; ?>
							</option>
						<?php endforeach; ?>
						</select>
						
						<?php
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
				</tr><tr>
					<td colspan="2"><b>Notice:</b></br><i>
					<?php _e("- Media upload is not available to Contributors and Subscribers by Wordpress capabilities", "frontier-post");?></br>
					<?php _e("- Wordpress standard rolemodel does not allow Contributors and Subscribers to add/edit/delete posts, but you can bypass this above", "frontier-post");?></br>
					<?php _e("- Frontier Edit means that if a user selects the dit link on a post, Frontier will be used to edit instead of backend", "frontier-post");?></br>
					<?php _e("-    - This is only if selected from frontend, if edit from backend, backend interface will be used", "frontier-post");?></br>
					</i></td>
				</tr>
			</table>
			<br/>
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>

		</form>

	</div> <!-- frontier-admin-menu -->
	</div> <!-- wrap -->

	<?php 
	} // end function frontier_post_settings_page
	
	?>