<?php

function fps_cnv_general_options($suppress_output = false)
	{
	// Move values from old single options to new array based options
	
	include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
		

	$frontier_submit_buttons = get_option("frontier_post_submit_buttons", array('save' => 'true', 'savereturn' => 'true', 'preview' => 'true', 'cancel' => 'true' ));
	
	$cnv_table = array(
		'fps_edit_max_age' 				=> get_option('frontier_post_edit_max_age', 10),
		'fps_delete_max_age' 			=> get_option('frontier_post_delete_max_age', 3),
		'fps_ppp'						=> get_option('frontier_post_ppp', 25), 
		'fps_page_id'					=> get_option('frontier_post_page_id', 0),
		'fps_del_w_comments'			=> get_option("frontier_post_del_w_comments","false"), 
		'fps_edit_w_comments'			=> get_option("frontier_post_edit_w_comments", "false"), 
		'fps_author_role'				=> get_option("frontier_post_author_role", "false"), 
		'fps_mail_to_approve'			=> get_option("frontier_post_mail_to_approve", "false"), 
		'fps_mail_approved'				=> get_option("frontier_post_mail_approved", "false"), 
		'fps_mail_address'				=> get_option("frontier_post_mail_address", ""), 
		'fps_excl_cats'					=> get_option("frontier_post_excl_cats",""), 
		'fps_show_feat_img'				=> get_option("frontier_post_show_feat_img", "false"), 
		'fps_show_login'				=> get_option("frontier_post_show_login", "false"), 
		'fps_change_status'				=> get_option("frontier_post_change_status", "true"),
		'fps_catid_list' 				=> get_option("frontier_post_catid_list", ""),
		'fps_editor_lines' 				=> get_option('frontier_post_editor_lines', 300), 
		'fps_default_status'			=> get_option("frontier_default_status", "publish"),
		'fps_hide_status'				=> get_option("frontier_post_hide_status", "false"),
		'fps_show_msg'					=> get_option("frontier_post_show_msg", "false"),
		'fps_hide_title_ids'			=> get_option("frontier_post_hide_title_ids", ""), 
		'fps_default_editor'			=> get_option("frontier_default_editor", "full"), 
		'fps_default_cat_select'		=> get_option("frontier_default_cat_select", "multi"),
		'fps_external_cap'				=> get_option("frontier_post_external_cap", "false"),
		'fps_submit_save'				=> $frontier_submit_buttons['save'],
		'fps_submit_savereturn'			=> $frontier_submit_buttons['savereturn'],
		'fps_submit_preview'			=> $frontier_submit_buttons['preview'],
		'fps_submit_cancel'				=> $frontier_submit_buttons['cancel']
		);
		
		
		$fps_save_general_options['fps_frontier_post_version'] 	= FRONTIER_POST_VERSION;
		update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $cnv_table);
		
		//Update default values for settings that doesnt exists. 
		fp_post_set_defaults();

		// Rolebased settings
		
		$old_capabilities 		= get_option('frontier_post_options',array());
		$wp_roles				= new WP_Roles();
		$roles 	  				= $wp_roles->get_names();
		
		$tmp_array				= array_merge($fp_capability_list, $fp_role_option_list);
		
		$tmp_cap_list			= array_keys($tmp_array);	
		
		$saved_capabilities 	= frontier_post_get_capabilities();
		
	
		
		
		// Loop through the roles
		foreach( $roles as $key => $item ) 
			{
			$xrole = get_role($key);
			
			if ( !array_key_exists($key, $saved_capabilities) )
				$saved_capabilities[$key] = array(); 
			
			if ( !array_key_exists($key, $old_capabilities) )
				$old_capabilities[$key] = array(); 
			
			// set capabilities
			foreach($tmp_cap_list as $tmp_cap)
				{
				
				$xrole_old_cap	= $old_capabilities[$key];
				$xrole_cap		= $saved_capabilities[$key];
				
				$old_cap_name	= str_replace('frontier_post_', '', $tmp_cap);
				$def_value		= "false";
				
				
				if ($tmp_cap == 'fps_role_editor_type')
					{
					$def_value		= "minimal-visual";
					$old_cap_name	= 'editor';
					}
				
				if ($tmp_cap == 'fps_role_category_layout')
					{
					$def_value		= "multi";
					$old_cap_name	= 'category';
					}
				
				if ($tmp_cap == 'fps_role_default_category')
					{
					$def_value		= get_option("default_category");
					$old_cap_name	= 'default_category';
					}
					
					
				if ( array_key_exists($old_cap_name, $xrole_old_cap))
					{
					$saved_capabilities[$key][$tmp_cap] = $xrole_old_cap[$old_cap_name];
					}
				else
					{
					if ( !array_key_exists($tmp_cap, $xrole_cap))
						{
						$saved_capabilities[$key][$tmp_cap]	= $def_value;
						}
					}
				
				
				} //caps
			} // roles
			
		// Save options
		
		update_option(FRONTIER_POST_CAPABILITY_OPTION_NAME, $saved_capabilities);
		
		// Set Wordpress capabilities
		frontier_post_set_cap();
		
		//save to options that capabilities has been migrated
		$fps_general_options = frontier_post_get_settings();
		$fps_general_options['fps_options_migrated'] = "true";
		$fps_general_options['fps_options_migrated_version'] = FRONTIER_POST_VERSION;
		update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_general_options);
		
		$fp_last_upgrade = fp_get_option('fps_options_migrated_version', get_option("frontier_post_version", '0.0.0'));
		$fp_upgrade_msg = 'Frontier Post - Settings upgraded from version: '.$fp_last_upgrade.' to version: '.FRONTIER_POST_VERSION;
		if (!$suppress_output)
			{
			echo '<div class="updated"><p><strong>'.$fp_upgrade_msg.'</strong></p></div>';
			}
		
		// Finally delete frontier_post_version
		delete_option("frontier_post_version");
		
	}




?>