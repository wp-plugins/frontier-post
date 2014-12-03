<?php

/*
Used to delete options and remove capabilities when Frontier Post is deleted
*/

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	{
	exit ();
	}
	
	global $wpdb;
	global $wp_roles;
	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();
	
	$roles 			= $wp_roles->get_names();
	$tmp_cap_list	= Array('can_add', 'can_edit', 'can_publish', 'can_draft', 'can_delete', 'exerpt_edit', 'tags_edit', 'redir_edit', 'can_media');
	
		
	//error_log("Deleting options for Frontier Post");
	delete_option('frontier_post_edit_max_age');
	delete_option("frontier_post_delete_max_age");
	delete_option("frontier_post_ppp");
	delete_option("frontier_post_del_w_comments");
	delete_option("frontier_post_edit_w_comments");
	delete_option("frontier_post_page_id");
	delete_option("frontier_post_options");
	delete_option("frontier_post_version");
	delete_option("frontier_post_use_draft"  );
	delete_option("frontier_post_author_role"  );
	delete_option("frontier_post_mce_custom");
	delete_option("frontier_post_mail_to_approve");
	delete_option("frontier_post_mail_approved");
	delete_option("frontier_post_mail_address");
	delete_option("frontier_post_show_feat_img");
	delete_option("frontier_post_show_login");
	delete_option("frontier_post_change_status");
	delete_option("frontier_default_status");
	delete_option("frontier_post_external_cap");
	delete_option("frontier_default_editor");
	update_option("frontier_default_cat_select");
				
	//delete_option("frontier_post_use_draft");
	
	foreach( $roles as $key => $item )
		{
		$xrole = get_role($key);
					
		foreach($tmp_cap_list as $tmp_cap)
			{
				// delete option
				$tmp_option_name = 'frontier_post_'.$key.'_'.$tmp_cap;
				//error_log("Deleting option: ".$tmp_option_name);
				delete_option($tmp_option_name);
				// remove capability
				$tmp_cap_name = 'frontier_post_'.$tmp_cap;
				//error_log("Deleting option: ".$tmp_cap_name);
				$xrole->remove_cap( $tmp_cap_name );			
			} // End capabilities
		} // End roles
	
	
	


?>