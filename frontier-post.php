<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://wordpress.org/extend/plugins/frontier-post/
Description: Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - My Posts Widget.
Author: finnj
Version: 3.3.1
Author URI: http://wpfrontier.com
*/

// define constants
define('FRONTIER_POST_VERSION', "3.3.1"); 
define('FRONTIER_POST_DIR', dirname( __FILE__ )); //an absolute path to this directory
define('FRONTIER_POST_URL', plugin_dir_url( __FILE__ )); //url path to this directory
define('FRONTIER_POST_DEBUG', false);

define('FRONTIER_POST_SETTINGS_OPTION_NAME', "frontier_post_general_options");
define('FRONTIER_POST_CAPABILITY_OPTION_NAME', "frontier_post_capabilities");


include("include/frontier_post_defaults.php");
include("include/frontier_post_validation.php");
include("include/frontier_post_util.php");
include("include/frontier_email_notify.php");

include('admin/frontier-post-admin-util.php');
	

include("frontier-list-posts.php");
include("frontier-submit-form.php");
include("frontier-delete-post.php");
include("frontier-set-defaults.php");
include("frontier-add-edit.php");
include("frontier-preview-post.php");

// Settings menu
//include('settings-menu.php');
include('frontier-post-admin.php');


//widgets	
include("include/frontier_my_posts_widget.php");
include("include/frontier_approvals_widget.php");
include("include/frontier_new_category_post_widget.php");

add_action("init","frontier_get_user_role"); 

/*
function get_file_extension($file_name)
	{
          return substr(strrchr($file_name,'.'),1);
	}
*/

//**********************************************************************************
// Check upgrade
//
//**********************************************************************************



if ( is_admin() )
	{
	//error_log(print_r(frontier_post_get_capabilities(), true));
	
	$fp_last_upgrade = fp_get_option('fps_options_migrated_version', get_option("frontier_post_version", '0.0.0'));

	// Upgrade old versions, but dont run upgrade if fresh install
	if ( ($fp_last_upgrade != '0.0.0') && version_compare($fp_last_upgrade, '3.1.0') < 0)
		{
		include(FRONTIER_POST_DIR."/admin/frontier-post-convert-options.php");
		// run the migration 
		fps_cnv_general_options();
		
		}
	
	// Normal version update to capture new settings etc
	$fp_version = fp_get_option('fps_frontier_post_version', '0.0.0');

	// Update defaults, but dont if fresh install - Must be the activation trigger
	if ( ($fp_version != '0.0.0') && version_compare($fp_version, 'FRONTIER_POST_VERSION') < 0)
		{
		fp_post_set_defaults();
		}
	}
//**********************************************************************************
// Main program
//
//**********************************************************************************
 

function frontier_user_posts($atts)
	{    
	global $wp_roles;
	global $current_user;
	global $post;
	
	

	if ( has_shortcode( $post->post_content, 'frontier-post') && ($post->post_type == 'page') )
		{
		if( is_user_logged_in() )
			{  
			//if( (!is_single()) || (!is_page(get_the_id())) ) 
			//error_log("Called from page id: ".get_the_id());
		
		
			if ( !is_page(get_the_id()) )
				{
				die('<center><h1>ERROR: '.__("frontier-post Shortcode only allowed in pages", "frontier-post").'</h1></center>');
				return;         
				}
		
			$post_task 		= isset($_GET['task']) ? $_GET['task'] : "notaskset";	
			$post_action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : "Unknown";
		
			$frontier_post_shortcode_parms = shortcode_atts( array (
				'frontier_mode' 				=> 'none',
				'frontier_parent_cat_id' 		=> 0,
				'frontier_cat_id' 				=> 0,
				'frontier_list_cat_id' 			=> 0,
				'frontier_list_all_posts'		=> 'false',
				'frontier_list_text_before'		=> '',
				'frontier_edit_text_before'		=> '',
				'frontier_myid'					=> get_the_id(),
				'frontier_return_text'			=> __("Save & Return", "frontier-post"),
				'frontier_add_post_type'		=> 'post',
				'frontier_list_post_types'		=> 'post',
				'frontier_custom_tax'			=> '',
				'frontier_custom_tax_layout'	=> ''
				), $atts );
		
		
		
			//If Category parsed from widget assign it instead of category from shortcode
			if ( isset($_GET['frontier_new_cat_widget']) && $_GET['frontier_new_cat_widget'] == "true" )
				{
				$_REQUEST['frontier_new_cat_widget'] = "true";
				$frontier_post_shortcode_parms['frontier_cat_id'] = isset($_GET['frontier_cat_id']) ? $_GET['frontier_cat_id'] : 0;
				}
			
			//Change Categories to array
			$frontier_post_shortcode_parms['frontier_cat_id'] = fp_list2array($frontier_post_shortcode_parms['frontier_cat_id']);
			$frontier_post_shortcode_parms['frontier_list_cat_id'] = fp_list2array($frontier_post_shortcode_parms['frontier_list_cat_id']);
			$frontier_post_shortcode_parms['frontier_list_post_types'] = fp_list2array($frontier_post_shortcode_parms['frontier_list_post_types']);
			$frontier_post_shortcode_parms['frontier_custom_tax'] = fp_list2array($frontier_post_shortcode_parms['frontier_custom_tax']);
			$frontier_post_shortcode_parms['frontier_custom_tax_layout'] = fp_list2array($frontier_post_shortcode_parms['frontier_custom_tax_layout']);
		
		
			
			//fp_log($frontier_post_shortcode_parms['frontier_cat_id']);
			//error_log(print_r($frontier_post_shortcode_parms,true));
		
			extract($frontier_post_shortcode_parms);
		
			// if mode is add, go directly to show form - enables use directly on several pages
			if ($frontier_mode == "add")
				$post_task = "new";
		
		
			ob_start();
		
			switch( $post_task )
				{
				case 'new':
					if ( $post_action == "wpfrtp_save_post" )
						frontier_posting_form_submit($frontier_post_shortcode_parms);
					else	
						frontier_post_add_edit($frontier_post_shortcode_parms);
					break;
			
				case 'edit':
					if ( $post_action == "wpfrtp_save_post" )
						frontier_posting_form_submit($frontier_post_shortcode_parms);
					else	
						frontier_post_add_edit($frontier_post_shortcode_parms);
					break;
			
				case 'delete':
					if ( $post_action == "wpfrtp_delete_post" )
						frontier_execute_delete_post($frontier_post_shortcode_parms);
					else	
						frontier_prepare_delete_post($frontier_post_shortcode_parms);
					break;    
			
				default:
					frontier_user_post_list($frontier_post_shortcode_parms);
					break;
				}

			//return content to shortcode for output
			$fp_content = ob_get_contents();
			ob_end_clean();
			return $fp_content;
			}
			else
			{
			echo fp_login_text();
			} // user_logged_in
		}
		else
		{
			//Shortcode called from post, not allowed
			if ( is_singular() )
				{
				// Only show warning if single post
				echo '<br><div id="frontier-post-alert">frontier-post shortcode '.__("not allowed in posts, only pages !", "frontier-post").'</div><br>';
				return;
				}
		} // has_shortcode
	
	
    } // end function frontier_user_posts


register_activation_hook( __FILE__ , 'frontier_post_set_defaults');
	
function frontier_template_dir()
	{
 	// get frontier dir in theme or child-theme	
	return get_stylesheet_directory().'plugins/frontier-post/';		
	}	
	
function frontier_load_form($frontier_form_name)
	{
 	// Check if template is located in theme or child-theme
	$located = locate_template(array('plugins/frontier-post/'.$frontier_form_name), false, true);
	
	if(!$located )
		{
		// if not found in theme folders, load native fronpier form
		$located = FRONTIER_POST_DIR."/forms/".$frontier_form_name;
		}
	//error_log("Form: ".$located);	
	return $located;		
	}

// Load css from plugin form directory in theme if exists	
function frontier_enqueue_scripts()
	{
 	// Check if css is located in theme or child-theme
	$located = locate_template(array('plugins/frontier-post/frontier-post.css'), false, false);
	
	
	if($located )
		{
		$located = get_stylesheet_directory_uri().'/plugins/frontier-post/frontier-post.css';
		}
	else
		{
		// if not found in theme folders, load native frontier form
		$located = plugins_url('frontier-post/frontier-post.css');
		}
	//error_log($located);
	wp_enqueue_style('frontierpost', $located);
	} 

add_action("wp_enqueue_scripts","frontier_enqueue_scripts");  

	
function frontier_get_user_role() 
	{
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	return $user_role ? $user_role : 'unkown';
	}
	


//Link for Frontier add post	
function frontier_post_add_link($tmp_p_id = null, $tmp_cat_id = null) 
	{
	$url = '';
	$concat= get_option("permalink_structure")?"?":"&";    
	//set the permalink for the page itself if not parsed
	if ( !isset($tmp_p_id) )
		$tmp_p_id = fp_get_option('fps_page_id');
	
	$frontier_permalink = get_permalink($tmp_p_id);
	$url = $frontier_permalink.$concat."task=new";
	if ( isset($tmp_cat_id) && $tmp_cat_id > 0 )
		$url = $url."&frontier_cat_id=".$tmp_cat_id;
	
	return $url;
	} 	

//*******************************************************************************************	
// Hide admin bar for user role based on settings
//*******************************************************************************************

function frontier_admin_bar()
	{
	if (!current_user_can( 'frontier_post_show_admin_bar' ))
		show_admin_bar(false);
	else
		show_admin_bar(true);
	}
add_action("init","frontier_admin_bar");  

//*******************************************************************************************
// Redirect standard link for edit post from backend (admin interface) to frontend
//*******************************************************************************************

function frontier_edit_post_link( $url, $post_id ) 
	{
	
	
	// Redirect to frontier post unless is called from admin panel or it is a post type not allowed in frontier post
	if ( is_admin() || !fp_check_post_type(get_post_type($post_id)) )
		{
			return $url;
		}
	else
		{
			if ( current_user_can( 'frontier_post_redir_edit' )	)
				{
					$frontier_edit_page = (int) fp_get_option('fps_page_id');
					$url = '';
					$concat= get_option("permalink_structure")?"?":"&";    
					//set the permalink for the page itself
					$frontier_permalink = get_permalink($frontier_edit_page);
					$url = $frontier_permalink.$concat."task=edit&postid=".$post_id;
				}
        }
		return $url;
    }

add_filter( 'get_edit_post_link', 'frontier_edit_post_link', 10, 2 );

//***********************************************************************************
//* Hide page title on specific pages - Only activate filter if there is any pages to hide (perfomance)
//***********************************************************************************	


function frontier_post_hide_title($fp_tmp_title, $fp_tmp_id = 0)
	{
	$fp_tmp_id = (int) $fp_tmp_id;
	
	// only execute and hide title if id been parsed, if it is a page and if the page is in the list of pages where title should be hidden.... 
	if ( $fp_tmp_id > 0 && is_page($fp_tmp_id))
		{
		//fp_log("ID: ".$fp_tmp_id." Is singular: ".is_singular()." Title: ".$fp_tmp_title);
	
		$fp_tmp_id_list = explode(",", fp_get_option("fps_hide_title_ids", ""));
		if (in_array($fp_tmp_id, $fp_tmp_id_list) )
			{
			$fp_tmp_title = "";
			}
		}
	return $fp_tmp_title;
	}
	
$fp_tmp_id_list = explode(",", fp_get_option("fps_hide_title_ids", ""));

if ( (count($fp_tmp_id_list) > 0) && ( (int) $fp_tmp_id_list[0] > 0) )
	add_filter("the_title", "frontier_post_hide_title", 99, 2);
	
//***********************************************************************************
//* Add Id to Category list
//***********************************************************************************	

if ( fp_get_option("fps_catid_list", "false") == "true" )
	{
	function frontier_add_categoryid_list($columns) 
		{
		$tmp = array( "cat_id" => "ID" );
		$columns = array_merge($columns, $tmp);
		//$columns['catID'] = __('ID');
		return $columns;
		}

	function frontier_add_categoryid_row( $value, $name, $cat_id )
		{
		if( $name == 'cat_id' ) 
			echo $cat_id;
		}

	function frontier_post_cat_column_width()	
		{
		// Tags page, exit earlier
		if( $_GET['taxonomy'] != 'category' )
			return;
		echo '<style>.column-cat_id {width:5%}</style>';
		}
		
	add_filter( 'manage_edit-category_columns', 'frontier_add_categoryid_list' );
	add_filter( 'manage_category_custom_column', 'frontier_add_categoryid_row', 10, 3 );
	add_action( 'admin_head-edit-tags.php', 'frontier_post_cat_column_width' );
	}
	
//***********************************************************************************
//* Post media fixes
//***********************************************************************************	

function frontier_media_fix( $post_id ) 
	{
	global $frontier_post_id;
	global $post_ID; 
	
	/* WordPress 3.4.2 fix */
	$post_ID = $post_id; 
	
	// WordPress 3.5.1 fix
	$frontier_post_id = $post_id;	
    add_filter( 'media_view_settings', 'frontier_media_fix_filter', 10, 2 ); 
	} 


	
//Fix insert media editor button filter
 
function frontier_media_fix_filter( $settings, $post ) 
	{
	global $frontier_post_id;
	
    $settings['post']['id'] = $frontier_post_id;
	
	return $settings;
	} 	



//add translation files
function frontier_post_init() 
	{
	load_plugin_textdomain('frontier-post', false, dirname( plugin_basename( __FILE__ ) ).'/language');
	}
	
add_action('plugins_loaded', 'frontier_post_init');


//add_action('admin_menu', 'frontier_post_settings_menu');

add_shortcode("frontier-post","frontier_user_posts");

?>