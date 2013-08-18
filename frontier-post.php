<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://wordpress.org/extend/plugins/frontier-post/
Description: Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - My Posts Widget
Author: finnj
Version: 1.6.2
Author URI: http://wordpress.org/extend/plugins/frontier-post/
*/

// define constants
define('FRONTIER_POST_VERSION', "1.6.2"); 
define('FRONTIER_POST_DIR', dirname( __FILE__ )); //an absolute path to this directory


//session_start();

include("include/frontier_post_defaults.php");
include("include/frontier_post_validation.php");

function  frontier_user_post_list()
	{
	
		global $post;
		global $current_user;
		get_currentuserinfo();
		
		$pagenum	= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
		$ppp		= get_option('frontier_post_ppp') ? get_option('frontier_post_ppp') : 5;
	
		$args = array(
				'post_type' 		=> 'post',
				'post_status' 		=> 'draft, pending, publish',
				'author'			=>	$current_user->ID,
				'order'				=> 'DESC',
				'orderby' 			=> 'post_date', 
				'posts_per_page'    => $ppp,
				'paged'				=> $pagenum,
				);
		
		$user_posts 	= new WP_Query( $args );
		//print_r("Last SQL-Query: {$user_posts->request}");
		include("forms/frontier_list.php");
		
	}  


function frontier_posting_form_submit()
	{
    global $current_user;
	//get_currentuserinfo();	
			
    if(isset($_POST['action'])&&$_POST['action']=="wpfrtp_save_post")
		{
        if($_POST['user_post_title'])
			{
			
			if(isset($_POST['post_status']))
				$post_status = $_POST['post_status'];
			else
				$post_status = 'draft';
				
			$tmp_title 	= trim( $_POST['user_post_title'] );
			if ( empty( $tmp_title ) ) 
				$tmp_title = __("No Title", "frontier-post");
			
			$tmp_title = trim( strip_tags( $tmp_title ));
        
			$tmp_content = trim( $_POST['user_post_desc'] );
			if ( empty( $tmp_content ) ) 
				$tmp_content = __("No content", "frontier-post");
			
			$tmp_excerpt = isset( $_POST['user_post_excerpt']) ? trim($_POST['user_post_excerpt'] ) : null;

			$saved_options 		= get_option('frontier_post_options', array() );
			$users_role 		= frontier_get_user_role();
			$category_type 		= $saved_options[$users_role]['category'] ? $saved_options[$users_role]['category'] : "multi"; 
			$default_category	= $saved_options[$users_role]['default_category'] ? $saved_options[$users_role]['default_category'] : get_option("default_category"); 
			
			if ($category_type == "multi")
				$tmp_categorymulti = $_POST['categorymulti'];
				
			if ($category_type == "single")
				{
				if(isset($_POST['cat']))
					{
					$tmp_category = $_POST['cat'];
					//error_log("Category from form: ".$tmp_category);
					$tmp_categorymulti = array($tmp_category);
					}
				}
				
			if ((!isset($tmp_categorymulti)) || (count($tmp_categorymulti)==0))
				$tmp_categorymulti = array($default_category);
				
			$taglist = array();
			if (isset( $_POST['user_post_tag1']))
				array_push($taglist, $_POST['user_post_tag1']);
			if (isset( $_POST['user_post_tag2']))
				array_push($taglist, $_POST['user_post_tag2']);
			if (isset( $_POST['user_post_tag3']))
				array_push($taglist, $_POST['user_post_tag3']);
			
			
            $my_post = array(
                 'post_title' 		=> $tmp_title,
				 'post_status' 		=> $post_status,
                 'post_content' 	=> $tmp_content,				 
                 'post_category' 	=> $tmp_categorymulti,
				 'post_excerpt' 	=> $tmp_excerpt,
				);
				
            if($_REQUEST['task']=="new")
				{
                // Insert the post into the database
                $postid=wp_insert_post( $my_post );
				}
        
			if($_REQUEST['task']=="edit")
				{
				// update the post into the database   
				$my_post['ID']=$_REQUEST['postid'];
				wp_update_post( $my_post );
				$postid= $_REQUEST['postid']; 
				}
	
			// Set tags
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				wp_set_post_tags($postid, $taglist);
	
			$upload_dir = wp_upload_dir();
			
			if(isset( $_POST['filename'] ))
				{
				$filenames= $_POST['filename'];
								
				if(is_array($filenames))
					{
					foreach($filenames as $value)
						{
						$wp_filetype = wp_check_filetype(basename($value), null );
						$attachment = array(
							'post_mime_type' => $wp_filetype['type'],
							'post_title' => preg_replace('/\.[^.]+$/', ' ', basename($value)),
							'post_content' => '',
							'guid' => $upload_dir['url']."/".$value,
							'post_status' => 'inherit'
							);
						$attach_id = wp_insert_attachment( $attachment, $value, $postid );
						set_post_thumbnail( $postid, $attach_id );
						}
					}    
				}
			}
    
		header("location: ".$_POST['home']);
		die();
        }
	}


function frontier_user_post_form()
	{
	global $current_user;
	require_once(ABSPATH . '/wp-admin/includes/post.php');    
	//include("include/frontier_post_defaults.php");
    $concat= get_option("permalink_structure")?"?":"&";  
        
    if($_REQUEST['task']=="edit")
		{
        $thispost			= get_post($_REQUEST['postid']);
		$user_post_excerpt	= get_post_meta($thispost->ID, "user_post_excerpt");
        }
    else
		{
		$thispost = get_default_post_to_edit( "post", true );	
		$thispost->post_author = $current_user->ID;
		$_REQUEST['task']="new";
		}
     
    include_once("forms/frontier_form.php");
	} 
 
function get_file_extension($file_name)
	{
          return substr(strrchr($file_name,'.'),1);
	}
  
//post_status_fix - Removed in v 1.6.1
/*
function frontier_status_fix()
	{
	// check if admin
	if (!current_user_can("manage_options"))
		{
		wp_die("You do not have access to this");
		}
	include("forms/frontier_fix_list.php");
	
	} // End post_status fix
*/

function frontier_user_posts()
	{    
		global $wp_roles;
		global $current_user;
		
		ob_start();
    
    
        if(is_user_logged_in())
			{  
            if(!(is_single()||is_page())) 
				{
				return;         
				}
            if(isset($_GET['task']))
				{
				$post_task = $_GET['task'];
				}
			else
				{
				$post_task = "notaskset";
				}
            switch( $post_task )
				{
                case 'new':
                case 'edit':
                    frontier_user_post_form();
                    break;
                case 'delete':
                    frontier_delete_post();
                    frontier_user_post_list();
                    break;    
                case '':
                default:
                    frontier_user_post_list();
                    break;
				} 
			}
			else
			{
				echo "<br>---- ";
				_e("Please log in !", "frontier-post");
				echo "------<br><br>";
			}

			$data = ob_get_contents();
        ob_clean();
        return $data;
    }

function frontier_delete_post()
	{
	if(isset($_REQUEST['task']))
		{
		$post_task = $_REQUEST['task'];
		}
	else
		{
		$post_task = "notaskset";
		}
    if($post_task == "delete" )
		{
		if($_REQUEST['postid'])
			{
			$cur_user 		= wp_get_current_user();
			$thispost		= get_post($_REQUEST['postid']);
			
			$post_author	= $thispost->post_author;
			
			//double check current user is equal to author (in case directly with param)
			if ( $cur_user->ID == $post_author )
				{
					//Move post to recycle bin
					wp_trash_post($_REQUEST['postid']);
				} 
			}
		}
	}

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
	
	/*	
	$tmp_buttons = array();
	$tmp_buttons[0]	= (isset($_POST[ "frontier_post_mce_button1"]) ? $_POST[ "frontier_post_mce_button1"] : '' );
	$tmp_buttons[1]	= (isset($_POST[ "frontier_post_mce_button2"]) ? $_POST[ "frontier_post_mce_button2"] : '' );
	$tmp_buttons[2]	= (isset($_POST[ "frontier_post_mce_button3"]) ? $_POST[ "frontier_post_mce_button3"] : '' );
	$tmp_buttons[3]	= (isset($_POST[ "frontier_post_mce_button4"]) ? $_POST[ "frontier_post_mce_button4"] : '' );
	*/
	add_option(frontier_post_mce_button ,array($frontier_mce_buttons_1, $frontier_mce_buttons_2, $frontier_mce_buttons_3, $frontier_mce_buttons_4 )); 
				
	
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

					
				if ($tmp_cap == 'editor')
					$tmp_option  = "full";
							
				if ($tmp_cap == 'category')
					$tmp_option  = "multi";
				
				if ($tmp_cap == 'default_category')
					$tmp_option  = get_option("default_category");
					
				//Check if option already exists, if not, set it (we will not overwrite existing settings
				if ( !array_key_exists($tmp_cap, $tmp_role_settings) || empty($saved_options[$key][$tmp_cap]))
					$saved_options[$key][$tmp_cap] = $tmp_option;
					
									
				// set capability, but not for editor and catory as they are not capabilities
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

register_activation_hook( __FILE__ , 'frontier_post_set_defaults');
	
function frontier_get_user_role() 
	{
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	return $user_role ? $user_role : 'unkown';
	}
	
	
include('settings-menu.php');


//Link for Frontier add post	
function frontier_post_add_link() 
	{
	$url = '';
	$concat= get_option("permalink_structure")?"?":"&";    
	//set the permalink for the page itself
	$frontier_permalink = get_permalink(get_option('frontier_post_page_id'));
	$url = $frontier_permalink.$concat."task=new";
	return $url;
	} 	


function frontier_enqueue_scripts()
	{
	wp_enqueue_style('frontierpost',plugins_url('frontier-post/frontier-post.css'));
	} 

add_action("wp_enqueue_scripts","frontier_enqueue_scripts");  
add_action("init","frontier_get_user_role"); 
add_action("init","frontier_posting_form_submit"); 
add_action("init","frontier_delete_post");  

// Load tinymce plugins if enabled in frontier settings
$frontier_post_mce_custom = get_option("frontier_post_mce_custom", "false");
if ($frontier_post_mce_custom == "true") 
	add_filter('mce_external_plugins', 'frontier_tinymce_plugins');

function frontier_tinymce_plugins () 
	{
	$plugins = array('emotions', 'table', 'searchreplace'); 
	$plugins_array = array();
	//Build the response - the key is the plugin name, value is the URL to the plugin JS
	foreach ($plugins as $plugin ) 
		{
		$plugins_array[ $plugin ] = plugins_url('tinymce/', __FILE__) . $plugin . '/editor_plugin.js';
		}
	return $plugins_array;
	}
	
// Hide admin bar for user role based on settings
function frontier_admin_bar()
	{
	if (!current_user_can( 'frontier_post_show_admin_bar' ))
		show_admin_bar(false);
	}
add_action("init","frontier_admin_bar");  
	
//error_log("Log message : ");

function frontier_edit_post_link( $url, $post_id ) 
	{
	if ( is_admin() || (get_post_type($post_id) != 'post') ) // Administrator in admin dashboard, dont change url and only change for type = post
		{
			return $url;
		}
	else
		{
			if ( current_user_can( 'frontier_post_redir_edit' )	)
				{
					$frontier_edit_page = (int) get_option('frontier_post_page_id');
					$url = '';
					$concat= get_option("permalink_structure")?"?":"&";    
					//set the permalink for the page itself
					$frontier_permalink = get_permalink($frontier_edit_page);
					$url = $frontier_permalink.$concat."task=edit&postid=".$post_id;
				}
        }
		return $url;
    }

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
    //$settings['post']['nonce'] = wp_create_nonce( 'update-post_' . $frontier_post_id );
	
	return $settings;
	} 	


//widgets	
include("include/frontier_my_posts_widget.php");
include("include/frontier_approvals_widget.php");

//add translation files
function frontier_post_init() 
	{
	load_plugin_textdomain('frontier-post', false, dirname( plugin_basename( __FILE__ ) ).'/language');
	}
	
add_action('plugins_loaded', 'frontier_post_init');

add_filter( 'get_edit_post_link', 'frontier_edit_post_link', 10, 2 );
add_action('admin_menu', 'frontier_post_settings_menu');
add_shortcode("frontier-post","frontier_user_posts");


//post_status_fix - Removed in version 1.6.1
//add_shortcode("frontier-status-fix","frontier_status_fix");
?>