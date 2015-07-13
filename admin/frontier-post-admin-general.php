<?php
//*****************************************************************************
// Admin settings menu - Frontier Post - General settings
//*****************************************************************************




function frontier_post_admin_page_general() 
	{
	
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	require(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
	
	//include("../include/frontier_post_defaults.php");
		

	
	echo '<strong>Frontier Post  - Version: '.FRONTIER_POST_VERSION.'</strong>';

	
			
	
	// ****************************************************************************
	// Save settings
	//*******************************************************************************

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ "frontier_isupdated_general_hidden" ]) && $_POST[ "frontier_isupdated_general_hidden" ] == 'Y' ) 
		{
		$fps_save_general_options = frontier_post_get_settings();
		
		
		foreach($fps_general_option_list as $tmp_option_name)
			{
			if ( !key_exists($tmp_option_name, $fps_save_general_options) )
				$fps_save_general_options[$tmp_option_name] = $fps_general_defaults[$tmp_option_name];	
			
			$fps_save_general_options[$tmp_option_name] = isset($_POST[$tmp_option_name]) ? $_POST[$tmp_option_name] : "";
			//echo "Saving. ".$tmp_option_name." - Value: ".$fps_save_general_options[$tmp_option_name]."<br>";
			}
		
		update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_save_general_options);
		
		
		// Put an settings updated message on the screen
		echo '<div class="updated"><p><strong>'.__('Settings saved.', 'frontier-post' ).'</strong></p></div>';
				
		
		
		
		} // end update options
	
	
	$fps_general_options		= frontier_post_get_settings();
	
	$fps_post_status_list 		= get_post_statuses();
	
	
		
	echo '<div class="wrap">';
	echo '<div class="frontier-admin-menu">';
	echo '<h2>'.__("Frontier Post Settings", "frontier-post").'</h2>';
	echo '<hr>'.__("Documentation", "frontier_post").': <a href="http://wpfrontier.com/frontier-post-settings/" target="_blank">General Settings</a>';
	echo ' - <a href="http://wpfrontier.com/frontier-post-shortcodes/" target="_blank">Shortcodes</a><hr>';	
	echo '<form name="frontier_post_settings" method="post" action="">';
		echo '<input type="hidden" name="frontier_isupdated_general_hidden" value="Y">';
		echo '<table border="1" cellspacing="0" cellpadding="0">';
				
			echo "<tr>";
				echo "<td>".__("Allow edit of posts with comments", "frontier-post")."</td>";
				fps_html_field("fps_edit_w_comments", 'checkbox', $fps_general_options, true);
				echo "<td>".__("Max age in days to allow edit of post", "frontier-post")."</td>";
				fps_html_field("fps_edit_max_age", 'text', $fps_general_options, true);
		
			echo "</tr><tr>";
				echo "<td>".__("Allow deletion of posts with comments", "frontier-post")."</td>";
				fps_html_field("fps_del_w_comments", 'checkbox', $fps_general_options, true);
				echo "<td>".__("Max age in days to allow delete of post", "frontier-post")."</td>";
				fps_html_field("fps_delete_max_age", 'text', $fps_general_options, true);
		
			echo "</tr><tr>";
				echo "<td>".__("Number of post per page", "frontier-post")."</td>";
				fps_html_field("fps_ppp", 'text', $fps_general_options, true);
				echo "<td>".__("Page containing [frontier-post] shortcode", "frontier-post")."</td>";
				echo "<td>";
					wp_dropdown_pages(array('name' => 'fps_page_id', 'id'=>'fps_page_id', 'dept' => 1, 'hide_empty' => 0, 'selected' => $fps_general_options['fps_page_id'], 'hierarchical' => true, 'show_option_none' => __('None'))); 
				echo "</td>";
						
			
			echo "</tr><tr>";
				echo "<td>".__("Default status for new posts", "frontier-post")."</td>";
				fps_html_field("fps_default_status", 'select', $fps_general_options, true, 1, $fps_post_status_list );
				echo "<td>".__("Page for pending posts ", "frontier-post")."</td>";
				echo "<td>";
					wp_dropdown_pages(array('name' => 'fps_pending_page_id', 'id'=>'fps_pending_page_id', 'dept' => 1, 'hide_empty' => 0, 'selected' => $fps_general_options['fps_pending_page_id'], 'hierarchical' => true, 'show_option_none' => __('None'))); 
				echo "</td>";
				
			
			
		echo '</tr></table><hr>';
		
		
		
		//*****************************************************************************
		// Additional options
		//*****************************************************************************
		
		//echo '<hr>';
		//echo '<h2>'.__("Additional options", "frontier-post").'</h2>';
		
		echo '<table border="1" cellspacing="	"2" cellpadding="1">';
			echo "<tr>";
				echo '<th colspan="3"></center>'.__("Additional options", "frontier-post").'</center></th>';
			echo "</tr><tr>";
			
				echo "<td>".__("Allow users to change status from Published", "frontier-post")."</td>";
				fps_html_field("fps_change_status", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("Once published users can change status back to draft/pending", "frontier-post")."</td>";
				
			echo "</tr><tr>";
				echo "<td>".__("Use featured image", "frontier-post")."</td>";
				fps_html_field("fps_show_feat_img", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("Enables selection of featured image from frontend form ", "frontier-post")."(does not work perfectly)</td>";
			
			echo "</tr><tr>";
				echo "<td>".__("Show link to login page", "frontier-post")."</td>";
				fps_html_field("fps_show_login", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("Shows link to wp-login.php after text: Please login", "frontier-post")."</td>";
			
			echo "</tr><tr>";
				echo "<td>".__("Show add/update/delete messages", "frontier-post")."</td>";
				fps_html_field("fps_show_msg", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("Show message on the form confirming a post has been added/updated/deleted", "frontier-post")."</td>";
			
			echo "</tr><tr>";
				echo "<td>".__("Show edit/delete/view icons in list", "frontier-post")."</td>";
				fps_html_field("fps_use_icons", 'checkbox', $fps_general_options, true, 1);
				$tmptext = "<td>".__("Show icons instead of text for edit/delete/view in list", "frontier-post");
				$tmptext .="&nbsp".'<img height="12px" src="'.FRONTIER_POST_URL.'/images/edit.png'.'"></img>';
				$tmptext .="&nbsp".'<img height="12px" src="'.FRONTIER_POST_URL.'/images/delete.png'.'"></img>';
				$tmptext .="&nbsp".'<img height="12px" src="'.FRONTIER_POST_URL.'/images/view.png'.'"></img>';
				$tmptext .="</td>";
				echo $tmptext;
			
			echo "</tr><tr>";
				echo "<td>".__("Hide Add New Post link on list", "frontier-post")."</td>";
				fps_html_field("fps_hide_add_on_list", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("Hide add new post on list form", "frontier-post")."</td>";
			
			echo "</tr><tr>";
				echo "<td>".__("Show submit buttons on post edit form", "frontier-post")."</td>";
				echo "<td></td>";
				echo '<td>';
				echo '&nbsp;'.__("Save", "frontier-post").'&nbsp;'.fps_checkbox_field("fps_submit_save", $fps_general_options['fps_submit_save']);
				echo '&nbsp;|&nbsp'.__("Save & Return", "frontier-post").'&nbsp;'.fps_checkbox_field("fps_submit_savereturn", $fps_general_options['fps_submit_savereturn']);
				echo '&nbsp;|&nbsp'.__("Save & Preview", "frontier-post").'&nbsp;'.fps_checkbox_field("fps_submit_preview", $fps_general_options['fps_submit_preview']);
				echo '&nbsp;|&nbsp'.__("Cancel", "frontier-post").'&nbsp;'.fps_checkbox_field("fps_submit_cancel", $fps_general_options['fps_submit_cancel']);
				echo "</td>";
			
			
			echo "</tr><tr>";
				echo "<td>".__("Allowed Post Types", "frontier-post")."</td>";
				echo "<td></td>";
				echo "<td><strong>".__("Post Types", "frontier-post").":</strong><br>";
				echo fps_checkbox_select_field("fps_custom_post_type_list[]", $fps_general_options["fps_custom_post_type_list"], fp_get_post_type_list())."</td>";
		
			echo "</tr><tr>";
				echo "<td>".__("List Layout", "frontier-post")."</td>";
				echo "<td></td>";
				fps_html_field("fps_default_list", 'select', $fps_general_options, true, 1, $frontier_list_forms );
		
		
			echo "</tr><tr>";
				echo "<td>".__("Exclude categories", "frontier-post")."</td>";
				echo "<td></td>";
				echo "<td>";
					echo fps_text_field("fps_excl_cats",  $fps_general_options['fps_excl_cats'], 100);
					echo '<br>'.__('comma separated list of IDs', 'frontier-post');
				echo "</td>";		
			
			
			
	
	
		echo '</tr></table>';
	
		echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="'.__('Save Changes').'"></p>';
	echo '</form>';
	echo '<hr>';
		
	echo '</div>'; //frontier-admin-menu 
	echo '</div>'; //wrap 

	} // end function frontier_post_admin_page_general
	
?>