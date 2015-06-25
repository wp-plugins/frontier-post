<?php

/*
Used to delete options and remove capabilities when Frontier Post is deleted
*/

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	{
	exit ();
	}
	
	//********************* Warning *************************
	//
	//Do not use functions from the plugin itself
	//
	//*******************************************************
	
	//******************************************************************************
	// Remove options if not to keep
	//******************************************************************************
	
	$fp_settings 	= get_option('frontier_post_general_options', array());
	$fp_keep		= array_key_exists('fps_keep_options_uninstall', $fp_settings) ? $fp_settings['fps_keep_options_uninstall'] : "false";
	
	if ($fp_keep == "true")
		{
		echo '<div class="updated"><p><strong>'.__("Options not deleted on uninstall, as keep setting enabled", "frontier-post").'</strong></p></div>';
		}
	else
		{
		delete_option('frontier_post_general_options');
		delete_option('frontier_post_capabilities');		
		}
	
	//******************************************************************************
	// Remove capabilities
	//******************************************************************************
	
	global $wp_roles;
	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$roles 			= $wp_roles->get_names();
	
	$tmp_cap_list	= Array(
	'frontier_post_can_pending',
	'frontier_post_can_add',
	'frontier_post_can_edit',
	'frontier_post_can_delete',
	'frontier_post_can_publish',
	'frontier_post_can_draft',
	'frontier_post_can_private',
	'frontier_post_redir_edit',
	'frontier_post_show_admin_bar',
	'frontier_post_tags_edit',
	'frontier_post_can_media',
	'frontier_post_can_page',
	'frontier_post_exerpt_edit',
	
	);
		
	
	foreach( $roles as $key => $item )
		{
		$xrole = get_role($key);
		
		delete_option('frontier_post_role_'.$key);
		
		foreach($tmp_cap_list as $tmp_cap)
			{		
				$xrole->remove_cap( $tmp_cap);
						
			} // End capabilities
		} // End roles

	
		

	// for old pre 3.1.0 version, cleanup old options
	$fp_old_options = array(
	
	'frontier_post_author_role',
	'frontier_post_catid_list',
	'frontier_post_change_status',
	'frontier_post_delete_max_age',
	'frontier_post_del_w_comments',
	'frontier_post_editor',
	'frontier_post_editor_lines',
	'frontier_post_edit_max_age',
	'frontier_post_edit_w_comments',
	'frontier_post_excl_cats',
	'frontier_post_external_cap',
	'frontier_post_hide_status',
	'frontier_post_hide_title_ids',
	'frontier_post_mail_address',
	'frontier_post_mail_approved',
	'frontier_post_mail_to_approve',
	'frontier_post_mce_button',
	'frontier_post_mce_custom',
	'frontier_post_options',
	'frontier_post_page_id',
	'frontier_post_ppp',
	'frontier_post_role_administrator',
	'frontier_post_role_author',
	'frontier_post_role_contributor',
	'frontier_post_role_editor',
	'frontier_post_role_pending',
	'frontier_post_role_subscriber',
	'frontier_post_show_feat_img',
	'frontier_post_show_login',
	'frontier_post_show_msg',
	'frontier_post_submit_buttons',
	'frontier_post_use_draft',
	
	);
	
	// This one waits
	// frontier_post_version
	
	
	foreach ($fp_old_options as $option_name)
		{
		delete_option($option_name);
		}
	
	// ** End Clean up old options
	
	// need to delete transient data TO BE CONTINUED


?>