<?php
/*
Admin settings menu - Frontier Post
*/


function frontier_post_settings_menu() 
	{
	//create new top-level menu
	add_options_page('Frontier Post', 'Frontier Post', 'administrator', __FILE__, 'frontier_post_settings_page');
	}

function frontier_post_settings_page() 
	{
	
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
		$tmp_cap_list	= Array('can_add', 'can_edit', 'can_publish', 'can_delete', 'exerpt_edit', 'tags_edit', 'redir_edit');
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
				
				//save capability settings
		
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
						$tmp_option_id	= 'frontier_post_'.$key.'_'.$tmp_cap;
						if (isset($_POST[ $tmp_name]))
							$tmp_value		= ( $_POST[ $tmp_name] ? $_POST[ $tmp_name] : "false" );
						else
							$tmp_value		= "false";
						//print_r('Update: '.$tmp_option_id.' --> '.$tmp_name.' --> '.$tmp_value.'</br>');
						update_option($tmp_option_id,  $tmp_value);
						
						// set capability
						if ( $tmp_value == "true" )
							{
								$xrole->add_cap( 'frontier_post_'.$tmp_cap );
							}
						else
							{
								$xrole->remove_cap( 'frontier_post_'.$tmp_cap );
							}
					
						}
					}
		
		
				
				// Put an settings updated message on the screen
				?>
					<div class="updated"><p><strong><?php _e('Settings saved.', 'frontier-post' ); ?></strong></p></div>
				<?php
			}
		
		// get values from db
		$frontier_post_edit_max_age 		= get_option('frontier_post_edit_max_age');
		$frontier_post_delete_max_age 		= get_option('frontier_post_delete_max_age');
		$frontier_post_ppp						= get_option('frontier_post_ppp');
		$frontier_post_page_id				= get_option('frontier_post_page_id');
		$frontier_post_del_w_comments			= (get_option("frontier_post_del_w_comments")) ? get_option("frontier_post_del_w_comments") : "false";
		$frontier_post_edit_w_comments			= (get_option("frontier_post_edit_w_comments")) ? get_option("frontier_post_edit_w_comments") : "false";
		
		
	?>
	
	<div class="wrap">
	<div class="frontier-admin-menu">
		<h2>Frontier Post Settings</h2>

		<form name="frontier_post_settings" method="post" action="">
			<input type="hidden" name="frontier_isupdated_hidden" value="Y">
			<table>
				<tr>
					<td><?php _e("Max age in days to allow edit of post:", "frontier-post");?>:</td>
					<td><input type="text" name="frontier_post_edit_max_age" value="<?php echo $frontier_post_edit_max_age; ?>" /></td>
				</tr><tr>
					<td><?php _e("Allow edit of posts with comments:", "frontier-post"); ?>:</td>
					<td><input type="checkbox" name="frontier_post_edit_w_comments" value="true" <?php echo ($frontier_post_edit_w_comments == "true") ? 'checked':''; ?>></td>
				</tr><tr>
					<td><?php _e("Max age in days to allow delete of post:", "frontier-post"); ?>:</td>
					<td><input type="text" name="frontier_post_delete_max_age" value="<?php echo $frontier_post_delete_max_age; ?>" /></td>
				</tr><tr>
					<td><?php _e("Allow deletion of posts with comments:", "frontier-post"); ?>:</td>
					<td><input type="checkbox" name="frontier_post_del_w_comments" value="true" <?php echo ($frontier_post_del_w_comments == "true") ? 'checked':''; ?>></td>
				</tr><tr>
					<td><?php _e("Number of post per page:", "frontier-post"); ?>:</td>
					<td><input type="text" name="frontier_post_ppp" value="<?php echo $frontier_post_ppp; ?>" /></td>
				</tr><tr>
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
						<th width="10%"><?php _e("Can Delete", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Edit Excerpt", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Edit Tags", "frontier-post"); ?></th>
						<th width="10%"><?php _e("Frontier Edit", "frontier-post"); ?></th>
					
					</tr><tr>
					
			<?php
			
			//Build table based on values from options
		
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
				
				
				foreach($tmp_cap_list as $tmp_cap)
					{
					
					$tmp_name		= 'frontier_post_'.$key.'_'.$tmp_cap;
					$tmp_option_id	= 'frontier_post_'.$key.'_'.$tmp_cap;
					$tmp_value		= ( get_option($tmp_option_id) ? get_option($tmp_option_id) : "false" );
					if ( $tmp_value == "true" )
						{
							$tmp_checked	= " checked"; 
						}
						else
						{
							$tmp_checked	= " "; 
						}
						
					echo '<td><center>';
					
					// can only enable redirect for edit posts if role has the neccessary capabilities
					if (( $tmp_cap == 'redir_edit' ) && (!$tmp_cap_ok) )
						{
						echo _e('NA', 'frontier-post');
						}
					else
						{
						echo '<input value="true" type="checkbox" name="'.$tmp_name.'" id="' .$tmp_name. '" '. $tmp_checked.' />';
						}
						
					echo '</center></td>';
					}
				//echo '<td>'.$tmp_name.'<td>';
				echo '</tr>';
					
				}
			
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
	} 
	
	?>