<?php


function frontier_post_set_defaults()
	{
	include("include/frontier_post_defaults.php");
	//error_log("Setting Frontier Post application Defaults ");
	
	global $wpdb;
	global $wp_roles;
	global $tmp_cap_list;
	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();
				
	$roles 			= $wp_roles->get_names();
		
	
	//$role_list		= Array('administrator', 'editor', 'author', 'contributor', 'subscriber');
	
	
	//print_r('building default WP options');
	add_option("frontier_post_edit_max_age", 10 );
	add_option("frontier_post_delete_max_age", 3 );
	add_option("frontier_post_ppp", 15 );
	add_option("frontier_post_del_w_comments", "false"  );
	add_option("frontier_post_use_draft", "false"  );
	add_option("frontier_post_author_role", "false"  );
	add_option("frontier_post_mce_custom",  "false" );
	add_option("frontier_post_mail_to_approve", "false");
	add_option("frontier_post_mail_approved", "false");
	add_option("frontier_post_mail_address","");
	add_option("frontier_post_show_feat_img", "false");
	add_option("frontier_post_show_login", "false");
	add_option("frontier_post_change_status", "true");
	add_option("frontier_default_status", "publish");
	add_option("frontier_post_external_cap", "false" );
	add_option("frontier_default_editor", "full" );
	add_option("frontier_default_cat_select", "checkbox" );

	add_option("frontier_post_submit_buttons", array('save' => 'true', 'savereturn' => 'true', 'preview' => 'true', 'cancel' => 'true' ));
				
			
	
	$tmp_cap_list	= $frontier_option_list;			
	$saved_options = get_option('frontier_post_options', array() );
	foreach( $roles as $key => $item )
		{
		if ( !array_key_exists($key, $saved_options) )
			$saved_options[$key] = array();
				
		$tmp_role_settings = $saved_options[$key];
		
		//error_log('Setting up role: '.$role_name);
		$xrole = get_role($key);
		$xrole_caps = $xrole->capabilities;
		
			
		foreach($tmp_cap_list as $tmp_cap)
			{
			
				$tmp_option  = "false";
				
				// Only enable all defaults for Administrator, Editor & Author
				if ( ($key == 'administrator') || ($key == 'editor') )
					{
					$tmp_option  = "true";
					}
				else
					{
					// except author who can add, edit and use the edit redir functionality
					if ( ($key == 'author') && (($tmp_cap == 'can_add') || ($tmp_cap == 'can_edit') || ($tmp_cap == 'redir_edit') ) )
						$tmp_option  = "true";
					}

				if ($tmp_cap == 'can_draft')
					$tmp_option  = "true";
				
				if ($tmp_cap == 'redir_edit')
					$tmp_option  = "true";
					
				if ( ($tmp_cap == 'can_private') && ($key != 'administrator') )
					$tmp_option  = "false";
					
				if ($tmp_cap == 'exerpt_edit'  && ($key != 'administrator') )
					$tmp_option  = "false";
								
				if ($tmp_cap == 'editor')
					$tmp_option  = "full";
							
				if ($tmp_cap == 'category')
					$tmp_option  = "checkbox";
				
				if ($tmp_cap == 'default_category')
					$tmp_option  = get_option("default_category");
					
				//Check if option already exists, if not, set it (we will not overwrite existing settings
				if ( !array_key_exists($tmp_cap, $tmp_role_settings) || empty($saved_options[$key][$tmp_cap]))
					$saved_options[$key][$tmp_cap] = $tmp_option;
				
				// do not change capabilities if managed externally
				if ( get_option("frontier_post_external_cap", "false") != "true" )					
					{
					// set capability, but not for editor and category as they are not capabilities
					if ($tmp_cap != 'editor' && $tmp_cap != 'category' && $tmp_cap != 'default_category')
						{
						$tmp_value		= ( $saved_options[$key][$tmp_cap] ? $saved_options[$key][$tmp_cap] : "false" );
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
			} // End capabilities
		} // End roles
		
		// save options
		update_option('frontier_post_options', $saved_options);
		//error_log(var_dump($saved_options));

		
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
                 'post_title' 		=> 'My Posts',
                 'post_content' 	=> '[frontier-post]',				 
                 'post_status' 		=> 'publish',
				 'comment_status' 	=> 'closed',
                 'post_type' 		=> 'page',
				);
				
		// Insert the page into the database
        $tmp_id = wp_insert_post( $my_page );
		//print_r("</br>Create page - tmp id: ".$tmp_id."</br>");
		}
	
	add_option("frontier_post_page_id", $tmp_id );
	
	// Set version
	update_option("frontier_post_version", FRONTIER_POST_VERSION);
	
	} // end function



?>