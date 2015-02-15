<?php


function frontier_post_set_defaults()
	{
	if (!defined('FRONTIER_POST_SETTINGS_OPTION_NAME'))
		{
		define('FRONTIER_POST_SETTINGS_OPTION_NAME', "frontier_post_general_options");
		}
	if (!defined('FRONTIER_POST_CAPABILITY_OPTION_NAME'))
		define('FRONTIER_POST_CAPABILITY_OPTION_NAME', "frontier_post_capabilities");
	
	include(FRONTIER_POST_DIR.'/include/frontier_post_defaults.php');	
	
	

	
	$fp_last_upgrade = fp_get_option('fps_options_migrated_version', get_option("frontier_post_version", '0.0.0'));
	
	
	// Upgrade old versions, but dont run upgrade if fresh install
	if ( ($fp_last_upgrade != '0.0.0') && version_compare($fp_last_upgrade, '3.3.0') < 0)
		{
		include(FRONTIER_POST_DIR.'/admin/frontier-post-convert-options.php');
		fps_cnv_general_options(true);
		$fp_upgrade_msg = 'Frontier Post - Settings upgraded from version: '.$fp_last_upgrade.' to version: '.FRONTIER_POST_VERSION;
		}
	else
		{
		
		//******************************************************************************
		// add settings if not already there
		//******************************************************************************
	
		if (!fp_get_option_bool('fps_keep_options_uninstall', false))
			{
			
			// Set default capabilities
			$saved_capabilities = frontier_post_get_capabilities();
				
			// administrators capabilities
			$tmp_administrator_cap = array(
				'frontier_post_can_add' 		=> 'true', 
				'frontier_post_can_edit' 		=> 'true', 
				'frontier_post_can_delete' 		=> 'true', 
				'frontier_post_can_publish'		=> 'true',  	
				'frontier_post_can_draft' 		=> 'true',  
				'frontier_post_can_pending' 	=> 'true',  
				'frontier_post_can_private' 	=> 'true', 	
				'frontier_post_redir_edit' 		=> 'true', 
				'frontier_post_show_admin_bar' 	=> 'true',  	
				'frontier_post_exerpt_edit' 	=> 'true',  
				'frontier_post_tags_edit' 		=> 'true',  
				'frontier_post_can_media'		=> 'true', 
				'frontier_post_can_page'		=> 'true', 
				'fps_role_editor_type'		 	=> 'full',
				'fps_role_category_layout'		=> 'multi',
				'fps_role_default_category'		=> get_option("default_category"),
				'fps_role_allowed_categories' 	=> '',
			
				);
		
			// editor
			$tmp_editor_cap 	= $tmp_administrator_cap;
		
			// Author
			$tmp_author_cap 	= $tmp_editor_cap;
		
			$tmp_author_cap['frontier_post_can_private']		= 'false';
			$tmp_author_cap['frontier_post_show_admin_bar']		= 'false';
			$tmp_author_cap['frontier_post_can_page']			= 'false';
		
			// Contributor
			$tmp_contributor_cap 	= $tmp_author_cap;
		
			$tmp_contributor_cap['frontier_post_can_delete']	= 'false';
			$tmp_contributor_cap['frontier_post_can_publish']	= 'false';
			$tmp_contributor_cap['frontier_post_redir_edit']	= 'false';
			$tmp_contributor_cap['frontier_post_tags_edit']		= 'false';
			$tmp_contributor_cap['frontier_post_can_media']		= 'false';
			$tmp_contributor_cap['fps_role_editor_type']		= 'minimal-visual';
		
			// Subscriber
			$tmp_subscriber_cap 	= $tmp_contributor_cap;
		
			$tmp_subscriber_cap['frontier_post_can_add']		= 'false';
			$tmp_subscriber_cap['frontier_post_can_edit']		= 'false';
			$tmp_subscriber_cap['frontier_post_can_pending']	= 'false';
			$tmp_subscriber_cap['frontier_post_can_draft']		= 'false';
		
		
		
			$wp_roles			= new WP_Roles();
			$roles 	  			= $wp_roles->get_names();
		
			$saved_capabilities = frontier_post_get_capabilities();
		
		
		
			foreach( $roles as $key => $item ) 
				{
			
				switch ($key)
					{
					case 'administrator':
						$tmp_cap_list = $tmp_administrator_cap;
						break;
					
					case 'editor':
						$tmp_cap_list = $tmp_editor_cap;
						break;
					
					case 'author':
						$tmp_cap_list = $tmp_author_cap;
						break;
					
					case 'frontier-author':
						$tmp_cap_list = $tmp_author_cap;
						break;
					
					case 'contributor':
						$tmp_cap_list = $tmp_contributor_cap;
						break;
				
					case 'subscriber':
						$tmp_cap_list = $tmp_subscriber_cap;
						break;	
					
					default:
						$tmp_cap_list = $tmp_contributor_cap;
						break;
					}
				
				$saved_capabilities[$key] = $tmp_cap_list;
			
				} // roles
			
			// Save options
			update_option(FRONTIER_POST_CAPABILITY_OPTION_NAME, $saved_capabilities);
		
		
	
			} // end update settings if not saved from during previous uninstall
		} //end Upgrade or not
	// update default settings
	fp_post_set_defaults();
	

	// Set Wordpress capabilities
	frontier_post_set_cap();
	
	
	
	
	global $wpdb;
	
	// Check if page containing [frontier-post] exists already, else create it
	$tmp_id = $wpdb->get_var(
		"SELECT id 
		  FROM $wpdb->posts 
		  WHERE post_type='page' AND 
		  post_status='publish' AND 
		 post_content LIKE '%[frontier-post]%'
		");
	
	if ( ((int)$tmp_id) <= 0)
		{
		// Add new page
		$my_page = array(
                 'post_title' 		=> __('My Posts', 'frontier-post'),
                 'post_content' 	=> '[frontier-post]',				 
                 'post_status' 		=> 'publish',
				 'comment_status' 	=> 'closed',
                 'post_type' 		=> 'page',
                 'ping_status'		=>	'closed'
				);
				
		// Insert the page into the database
        $tmp_id = wp_insert_post( $my_page );
		// save page id
		$fps_save_general_options 								= frontier_post_get_settings();
		$fps_save_general_options['fps_page_id'] 				= $tmp_id;			
		update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_save_general_options);
		}
	else
		{
		if (fp_get_option_int('fps_page_id', 0) === 0)
			{
			// save page id
			$fps_save_general_options 								= frontier_post_get_settings();
			$fps_save_general_options['fps_page_id'] 				= $tmp_id;			
			update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_save_general_options);
			}
		}
		
	//save to options that capabilities has been migrated
	$fps_general_options = frontier_post_get_settings();
	$fps_general_options['fps_options_migrated'] = "true";
	$fps_general_options['fps_options_migrated_version'] = FRONTIER_POST_VERSION;
	update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_general_options);
	
	
	
	// Put an updated message on the screen - NO NO, Cant do that in activation script.
	//echo '<div class="updated"><p><strong>'.__("Frontier Post - Default settings and capabilities set - Please review settings and capabilities", 'frontier-post' ).'</strong></p></div>';
	
	} // end function



?>