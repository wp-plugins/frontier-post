<?php
//*****************************************************************************
// Admin settings menu - Frontier Post - capabilities
//*****************************************************************************




function frontier_post_admin_page_capabilities() 
	{
	
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
	
	//include("../include/frontier_post_defaults.php");
		

	
	echo '<strong>Frontier Post version: '.FRONTIER_POST_VERSION.'</strong>';

	
			
	
	// ****************************************************************************
	// Save settings
	//*******************************************************************************

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ "frontier_isupdated_capabilities_hidden" ]) && $_POST[ "frontier_isupdated_capabilities_hidden" ] == 'Y' ) 
		{
		
		
		// ****************************************************************************
		// Update option for capabilities per role
		//*******************************************************************************

		
		// Do not update capabilities if capabilities are managed externally
		if ( !fp_get_option_bool("fps_external_cap") )
				{
				
				// Reinstate roles
				$wp_roles			= new WP_Roles();
				$roles 	  			= $wp_roles->get_names();
				
				$tmp_cap_list		= array_merge($fp_capability_list, $fp_role_option_list);			
				$saved_capabilities = frontier_post_get_capabilities();
				
				
				
				foreach( $roles as $key => $item ) 
					{
					$xrole = get_role($key);
					$xrole_caps = $xrole->capabilities;
					
					foreach($tmp_cap_list as $tmp_cap => $tmp_cap_name)
						{
						
						$tmp_name		= $key.'_'.$tmp_cap;
						$def_value		= "false";
						
						if ($tmp_cap == 'fps_role_editor_type')
							$def_value		= "minimal-visual";
						
						if ($tmp_cap == 'fps_role_category_layout')
							$def_value		= "multi";
						
						if ($tmp_cap == 'fps_role_default_category' )
							$def_value		= get_option("default_category");
							
						if ($tmp_cap == 'fps_role_allowed_categories' )
							$def_value		= '';
						
						
						if (isset($_POST[ $tmp_name]))
							$tmp_value		= ( $_POST[ $tmp_name] ? $_POST[ $tmp_name] : $def_value );
						else
							$tmp_value		= $def_value;
							
						
						$saved_capabilities[$key][$tmp_cap] = $tmp_value;
						
						} //caps
					
					} // roles
					
				
				update_option(FRONTIER_POST_CAPABILITY_OPTION_NAME, $saved_capabilities);
				
				// Put an settings updated message on the screen
				echo '<div class="updated"><p><strong>'.__("Settings saved.", 'frontier-post' ).'</strong></p></div>';
		
				// Set Wordpress capabilities
				frontier_post_set_cap();
				// Put an settings updated message on the screen
				echo '<div class="updated"><p><strong>'.__("Capabilities set.", 'frontier-post' ).'</strong></p></div>';
		
				} // End external managed capabilities
				
				
		} // end update options
	
	
		
	echo '<div class="wrap">';
	echo '<div class="frontier-admin-menu">';
	echo '<h2>'.__("Frontier Post - Capabilities & Role based settings", "frontier-post").'</h2>';
	echo '<hr>'.__("Documentation", "frontier_post").': <a href="http://wpfrontier.com/frontier-post-profiles-capabilities/" target="_blank">Profiles & Capabilities</a>';
	echo ' - <a href="http://wpfrontier.com/frontier-post-role-based-settings/" target="_blank">Role based settings</a><hr>';	
		
	echo '<form name="frontier_post_settings" method="post" action="">';
		echo '<input type="hidden" name="frontier_isupdated_capabilities_hidden" value="Y">';
		
		//*****************************************************************************
		// Start capability listing
		//*****************************************************************************
		
		if ( fp_get_option_bool("fps_external_cap") )
			{
			echo '<i><strong>'.__('Capabilities managed externally', 'frontier-post').'</strong></i>';
			}
		else
			{
			echo '<table border="1" cellspacing="0" cellpadding="0">';		
			echo "<tr>";
				echo '<th colspan="16"></center>'.__("Capabilities by user role", "frontier-post").'</center></th>';
			echo "</tr><tr>";
			echo '<th width="6%">'.__("Role", "frontier-post").'</th>';
			
			foreach ( $fp_capability_list as $tmp_cap => $tmp_cap_name )
				{
				echo '<th width="6%">'.$tmp_cap_name.'</th>';	
				}
			
			echo "</tr><tr>";
			
			global $wp_roles;
			if ( !isset( $wp_roles ) )
				$wp_roles = new WP_Roles();
				
			$roles 					= $wp_roles->get_names();
			$saved_capabilities 	= frontier_post_get_capabilities();
			
			// loop through each role
			foreach( $roles as $key => $item ) 
				{
				echo '<tr><td>'.$item.'</td>';
				
				//If Role does not exists, create it in the frontier post array
				if ( !array_key_exists($key, $saved_capabilities) )
					$saved_capabilities[$key] = array();
				
				$tmp_role_settings 	=  $saved_capabilities[$key];
				if (!is_array($tmp_role_settings))
					$tmp_role_settings = array();
					
				
				foreach($fp_capability_list as $tmp_cap => $tmp_cap_name)
					{
					$tmp_name		= $key.'_'.$tmp_cap;
					
					if ( array_key_exists($tmp_cap, $tmp_role_settings))
						$tmp_value	= ( $saved_capabilities[$key][$tmp_cap] ? $saved_capabilities[$key][$tmp_cap] : "false" );
					else
						$tmp_value	= "false";
					
					if ( $tmp_value == "true" )
						$tmp_checked	= " checked"; 
					else
						$tmp_checked	= " "; 
					
					
						
						
					echo '<td><center>';
					//echo $key."<br>".$tmp_cap."<br>";
					//Hide can_media for subscribers and contributors
					if ($tmp_cap == "frontier_post_can_media" && ($key=="subscriber" || $key=="contributor")) 
						{
						echo 'N/A';
						}
					else
						{
						echo '<input value="true" type="checkbox" name="'.$tmp_name.'" id="' .$tmp_name. '" '. $tmp_checked.' />';  
						}
					
					echo '</center></td>';
					} // end cap
					echo '</tr>';
				} // end roles
			echo '</table>';
			echo '* '.__("Wordpress standard does not allow Contributors  and Subscribers to upload media", "frontier-post");
			} // endfps_external_cap
		//*****************************************************************************
		// Start Role Based settings
		//*****************************************************************************
		
		echo '<hr>';
		echo '<table border="1" cellspacing="0" cellpadding="0">';		
			echo "<tr>";
				echo '<th colspan="5"></center>'.__("Role based settings", "frontier-post").'</center></th>';
			echo "</tr><tr>";
			echo '<th width="6%">'.__("Role", "frontier-post").'</th>';
			
			foreach ( $fp_role_option_list as $tmp_role_option => $tmp_role_option_name )
				{
				echo '<th width="6%">'.$tmp_role_option_name.'</th>';	
				}
			
			echo "</tr><tr>";
			
			global $wp_roles;
			if ( !isset( $wp_roles ) )
				$wp_roles = new WP_Roles();
				
			$roles 					= $wp_roles->get_names();
			$saved_capabilities 	= frontier_post_get_capabilities();
			$tmp_role_option_list	= array_keys($fp_role_option_list);
		
			// loop through each role
			foreach( $roles as $key => $item ) 
				{
				
				echo '<tr><td>'.$item.'</td>';
				
				if ( !array_key_exists($key, $saved_capabilities) )
					$saved_capabilities[$key] = array();
				
				$tmp_role_options = $saved_capabilities[$key];
				
				if (!is_array($tmp_role_options))
					$tmp_role_options = array();
					
				
				foreach($fp_role_option_list as $tmp_role_option => $tmp_role_option_name)
					{
					$tmp_name		= $key.'_'.$tmp_role_option;
					
					if ( array_key_exists($tmp_role_option, $tmp_role_options))
						$tmp_value	= ( $saved_capabilities[$key][$tmp_role_option] ? $saved_capabilities[$key][$tmp_role_option] : "false" );
					else
						$tmp_value	= "";
					
					
					echo '<td>';
					
					switch ($tmp_role_option) 
						{
			
						case 'fps_role_editor_type':
							$optionlist = array_flip($editor_types);
							?>	
							<select  id="<?php echo $tmp_name ?>" name="<?php echo $tmp_name ?>" >
							<?php foreach($optionlist as $id => $desc) : ?>   
								<option value='<?php echo $id ?>' <?php echo ( $id == $tmp_value) ? "selected='selected'" : ' ';?>>
									<?php echo $desc; ?>
								</option>
							<?php endforeach; ?>
							</select>
							<?php
						break;
						
						case 'fps_role_category_layout':
							$optionlist = array_flip($category_types);
							?>	
							<select  id="<?php echo $tmp_name ?>" name="<?php echo $tmp_name ?>" >
							<?php foreach($optionlist as $id => $desc) : ?>   
								<option value='<?php echo $id ?>' <?php echo ( $id == $tmp_value) ? "selected='selected'" : ' ';?>>
									<?php echo $desc; ?>
								</option>
							<?php endforeach; ?>
							</select>
							<?php
						break;
						
						case 'fps_role_default_category':
							wp_dropdown_categories(array('id'=>$tmp_name, 'hide_empty' => 0, 'name' => $tmp_name, 'orderby' => 'name', 'selected' => $tmp_value, 'hierarchical' => true)); 		
						break;
						
						case 'fps_role_allowed_categories':

							if ($tmp_value == "false")
								$tmp_value = "";
							echo '<input type="text" name="'.$tmp_name.'" value="'.$tmp_value.'">';		
						break;
						}
						
						
					echo '</td>';
				
				
					} // end option
					echo '</tr>';
				} // end roles
	
		
		echo '</table>';
		
		
		echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="'.__('Save Changes').'"></p>';
	echo '</form>';
	echo '<hr>';
		
	echo '</div>'; //frontier-admin-menu 
	echo '</div>'; //wrap 

	} // end function frontier_post_admin_page_capabilities
	
	?>