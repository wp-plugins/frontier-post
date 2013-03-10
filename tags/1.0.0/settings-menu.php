<?php
/*
Admin settings menu - Frontier Post
*/


function frontier_post_settings_menu() 
	{
	//create new top-level menu
	add_options_page('Frontier Post', 'Frontier Post', 'administrator', __FILE__, 'frontier_post_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_frontier_post_settings' );
	}


function register_frontier_post_settings() 
	{
	register_setting( 'frontier-post-settings-group', 'frontier_edit_post_max_age' );
	register_setting( 'frontier-post-settings-group', 'frontier_delete_post_max_age' );
	}




function frontier_post_settings_page() 
	{
		include("include/frontier_post_defaults.php");
	
		//must check that the user has the required capability 
		if (!current_user_can('manage_options'))
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
	
		$frontier_post_edit_max_age = get_option('frontier_post_edit_max_age');
		$frontier_post_delete_max_age = get_option('frontier_post_delete_max_age');
	
		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if( isset($_POST[ "frontier_isupdated_hidden" ]) && $_POST[ "frontier_isupdated_hidden" ] == 'Y' ) 
			{
			
				// Read their posted value, and save it
				$frontier_post_edit_max_age = $_POST[ "frontier_post_edit_max_age" ];
				if(!is_numeric($frontier_post_edit_max_age))
					{
						$frontier_post_edit_max_age = $default_post_edit_max_age;
					}
				update_option("frontier_post_edit_max_age", $frontier_post_edit_max_age );
			
			
				$frontier_post_delete_max_age = $_POST[ "frontier_post_delete_max_age" ];
				if(!is_numeric($frontier_post_delete_max_age))
					{
						$frontier_post_delete_max_age = $default_post_delete_max_age;
					}
				update_option("frontier_post_delete_max_age", $frontier_post_delete_max_age );

				// Put an settings updated message on the screen
				?>
					<div class="updated"><p><strong><?php _e('Settings saved.', 'frontier-post' ); ?></strong></p></div>
				<?php
			}
		

	?>
	
	<div class="wrap">
	<div class="frontier-admin-menu">
		<h2>Frontier Post Settings</h2>

		<form name="frontier_post_settings" method="post" action="">
			<input type="hidden" name="frontier_isupdated_hidden" value="Y">
			<table>
				<tr>
					<td>
						<?php _e("Max age in days to allow edit of post", "frontier-post"); ?>:
					</td>
					<td>
						<input type="text" name="frontier_post_edit_max_age" value="<?php echo $frontier_post_edit_max_age; ?>" /> 
					</td>
				</tr><tr>
					<td>
						<?php _e("Max age in days to allow delete of post", "frontier-post"); ?>:
					</td>
					<td>
						<input type="text" name="frontier_post_delete_max_age" value="<?php echo $frontier_post_delete_max_age; ?>" /> 
					</td>
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