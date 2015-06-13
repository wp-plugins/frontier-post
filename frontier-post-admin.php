<?php

//*************************************************************
// Admin settings menu - Frontier Post
//***********************************************************

// Include admin menu pages
include("admin/frontier-post-admin-general.php");
include("admin/frontier-post-admin-capabilities.php");
include("admin/frontier-post-admin-advanced.php");

add_action( 'admin_menu', 'frontier_post_admin_menu' );

function frontier_post_admin_menu() 
	{
	//create new top-level menu
	//add_options_page('Frontier', 'Frontier', 'administrator', __FILE__, 'frontier_post_settings_page');
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position )
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function )
	add_menu_page( 'Frontier Settings', 'Frontier', 'manage_options', 'frontier_admin_menu', 'frontier_post_admin_page_general' );
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - General Settings', 'Frontier Post Settings', 'manage_options', 'frontier_admin_menu', 'frontier_post_admin_page_general'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Capabilties & Rolebased settings', 'Frontier Post Capabilities', 'manage_options', 'frontier_post_admin_capabilities', 'frontier_post_admin_page_capabilities'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Advanced Settings', 'Frontier Post Advanced', 'manage_options', 'frontier_post_admin_advanced', 'frontier_post_admin_page_advanced'); 
	//add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Convert Settings', 'Frontier Post Convert', 'manage_options', 'frontier_post_admin_convert_settings', 'frontier_post_admin_convert'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Debug Info', 'Debug Info', 'manage_options', 'frontier_post_admin_list_capabilities', 'frontier_post_admin_list_cap'); 
	
	/*
	add_menu_page( 'Frontier Settings', 'Frontier Settings', 'manage_options', 'frontier_admin_menu');
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post general settings', 'General', 'manage_options', 'frontier_post_admin_page_general', 'frontier_post_admin_page_general'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Manage Capabilities Externally', 'External Capabilities', 'manage_options', 'frontier_post_admin_page_ext_cap', 'frontier_post_admin_extern_cap'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Convert settings', 'Convert Settings', 'manage_options', 'frontier_post_admin_convert_settings', 'frontier_post_admin_convert'); 
	
	add_submenu_page( 'frontier_admin_menu', 'Frontier Buttons - Settings', 'Frontier Buttons Settings', 'manage_options', 'frontier_post_admin_list_capabilities', 'frontier_buttons_settings_menu'); 

	// General settings
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - List Capabilities', 'List Capabilities', 'manage_options', 'frontier_post_admin_list_capabilities', 'frontier_post_admin_list_cap'); 
	*/
 
	}

//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function); 



function frontier_post_admin_page_main() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	

	
	echo '<strong>Frontier Post version: '.FRONTIER_POST_VERSION.'</strong>';
		
	 
	} // end function frontier_admin_page_main
	
	
	
	
	
	
function frontier_post_admin_advanced() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	echo '<strong>This is the external capabilities options page</strong>';
	
	 
	} // end function frontier_admin_external_cap
	

function frontier_post_admin_list_cap() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	 
	
	
	
	global $wpdb;
	
	// Show content statistics
	$fp_sql = "SELECT post_status, post_type, count(*) as post_count FROM $wpdb->posts GROUP BY post_type, post_status ORDER BY post_type, post_status;";
	$fp_stat = $wpdb->get_results($fp_sql);
	echo '<hr>';
	echo '<h2>Post DB content breakdown</strong></h2>';
	echo '<table border="1" cellpadding="2" cellspacing="4"><tr><th>Post Type</th><th>Post Status</th><th>record count</th></tr>';
	foreach ($fp_stat as $stat)
		{
		echo '<tr>';
		echo '<td>'.$stat->post_type.'</td>';
		echo '<td>'.$stat->post_status.'</td>';
		echo '<td align="right">'.$stat->post_count.'</td>';
		echo '</tr>';
		}
	echo '</table>';
	
	
	echo '<h2>Frontier Option values per role</strong></h2>';
	echo '<hr>';
	echo '<table border="1" cellpadding="2" cellspacing="4"><tr><th>key</th><th>Value</th></tr>';
	
	$fps_general_options		= frontier_post_get_settings();
	
	foreach($fps_general_options as $key => $value)
		{
		echo '<tr>';
		echo '<td>'.$key.'</td>';
		if (is_array($value))
			echo '<td>'.print_r($value, true).'</td>';
		else
			echo '<td>'.$value.'</td>';
		
		echo '</tr>';
		}
	echo '</table>';
	
	echo '<h2>List capabilties per role</strong></h2>';
	echo '<hr>';
	
	
	// Reinstate roles
	$wp_roles	= new WP_Roles();
	$roles 	  	= $wp_roles->get_names();
	$roles		= array_reverse($roles);
	
	foreach( $roles as $key => $item ) 
		{
		$xrole = get_role($key);
		$xrole_caps = $xrole->capabilities;
		echo '<strong>'.$item.'</strong><br>';
		
		foreach($xrole_caps as $tmp_cap_name => $tmp_cap)
			{
			//echo 'pos: '.strpos($tmp_cap_name, "rontier_post").'  -  ';
			if ( strpos($tmp_cap_name, "rontier_post") == 1 )
				echo '<strong>'.$tmp_cap_name.'</strong><br>';
			else
				echo $tmp_cap_name.'<br>';
			}
		echo '<hr>';
		}	
	
	echo '<hr>';
	echo '<h2>List frontier options</strong></h2>';
	
	
	$fp_sql 	= "SELECT option_name  FROM $wpdb->options WHERE option_name LIKE 'frontier_post%';";
	$fp_options = $wpdb->get_results($fp_sql);
	
	
	foreach ($fp_options as $option)
		{
		echo $option->option_name.'<br>';
		}
	echo '<hr>';
	
	
	
	
	} // end function frontier_admin_page_main
	

function frontier_post_admin_convert() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	include("admin/frontier-post-convert-options.php");
	
	if( isset($_POST[ "frontier_isupdated_cnv_hidden" ]) && $_POST[ "frontier_isupdated_cnv_hidden" ] == 'Y' ) 
		{
	
		fps_cnv_general_options();
	
		}
	 
	
	echo '<strong>This is the convert options page</strong><br>';
	/*
	$t1 = 'frontier_post_can_add';
	echo $t1.'<br>';
	echo str_replace('frontier_post_', '', $t1);
	*/
	echo '<h2>'.__("Convert options from previous format", "frontier-post").'</h2>';
		
	echo '<form name="frontier_post_settings" method="post" action="">';
		echo '<input type="hidden" name="frontier_isupdated_cnv_hidden" value="Y">';
		
	echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="'.__('Convert options').'"></p>';
	echo '</form>';
	echo '<hr>';
	
	
	
	
	} // end function frontier_admin_page_main
	
	