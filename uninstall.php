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
	$tmp_cap_list	= Array('can_add', 'can_edit', 'can_delete', 'exerpt_edit', 'tags_edit', 'redir_edit');
	
	// Remove capability edit_published_pages to allow authors to upload media if added on activation	
	$tmp_author_cap_set = get_option("frontier_post_author_cap_set") ? get_option("frontier_post_author_cap_set") : "false";
	if ($tmp_author_cap_set == "true")
		{
			$xrole = get_role('author');
			$xrole->remove_cap('edit_published_pages');
		}
		
	//error_log("Deleting options for Frontier Post");
	delete_option('frontier_post_edit_max_age');
	delete_option("frontier_post_delete_max_age");
	delete_option("frontier_ppp");
	delete_option("frontier_del_w_comments");
	delete_option("frontier_edit_w_comments");
	delete_option("frontier_post_page_id");
	
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
	
	// Remove capability edit_published_pages to allow authors to upload media if added on activation	
	$tmp_author_cap_set = get_option("frontier_post_author_cap_set") ? get_option("frontier_post_author_cap_set") : "false";
	if ($tmp_author_cap_set == "true")
		{
			$xrole = get_role('author');
			$xrole->remove_cap('edit_published_pages');
		}
	
	


?>