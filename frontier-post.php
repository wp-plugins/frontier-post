<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://wordpress.org/extend/plugins/frontier-post/
Description: Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - My Posts Widget
Author: finnj
Version: 2.6.1
Author URI: http://wordpress.org/extend/plugins/frontier-post/
*/

// define constants
define('FRONTIER_POST_VERSION', "2.6.1"); 
define('FRONTIER_POST_DIR', dirname( __FILE__ )); //an absolute path to this directory


//session_start();

include("include/frontier_post_defaults.php");
include("include/frontier_post_validation.php");

include("frontier-list-posts.php");
include("frontier-submit-form.php");
include("frontier-delete-post.php");
include("frontier-set-defaults.php");
include("frontier-add-edit.php");

//widgets	
include("include/frontier_my_posts_widget.php");
include("include/frontier_approvals_widget.php");


 
function get_file_extension($file_name)
	{
          return substr(strrchr($file_name,'.'),1);
	}
  

function frontier_user_posts($atts)
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
			
			//Get shortcode parms
			extract( shortcode_atts( array (
				'frontier_mode' => 'none',
				'frontier_parent_cat_id' => 0
				), $atts ) );	
				
			//error_log("frontier_mode: ".$frontier_mode);
			//error_log("frontier_cat_id: ".$frontier_cat_id);
			
			// if mode is add, go directly to show form - enables use directly on several pages
			if ($frontier_mode == "add")
				$post_task = "new";
			
			$_REQUEST['task'] 		= $post_task;
			$_REQUEST['parent_cat']	= $frontier_parent_cat_id;
			
            switch( $post_task )
				{
                case 'new':
					frontier_post_add_edit();
					break;
                case 'edit':
                    frontier_post_add_edit();
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
				$frontier_show_login = get_option("frontier_post_show_login", "false");
				//echo "Show login: ".$frontier_show_login."<br>";
				if ($frontier_show_login == "true" )
					echo __("Please log in !", "frontier-post")." <a href=".wp_login_url()."?redirect_to=".get_permalink(get_option('frontier_post_page_id')).">".__("Login Page", "frontier-post")."</a>  ";
					//echo __("Please log in !", "frontier-post")."&nbsp;<a href=".wp_login_url().">".__("Login Page", "frontier-post")."</a>&nbsp;&nbsp;";
				else
					_e("Please log in !", "frontier-post");
					
				echo "------<br><br>";
			}

			$data = ob_get_contents();
        ob_clean();
        return $data;
    }


register_activation_hook( __FILE__ , 'frontier_post_set_defaults');
	
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


// Send email when a post changes status to pending
function frontier_email_on_transition(  $new_status, $old_status, $post ) 
	{
	
    if( $post->post_type !== 'post' )
        return;    //Don't touch anything that's not a post (i.e. ignore links and attachments and whatnot )

			
    //If some variety of a draft is being published, dispatch an email
    if(  $old_status != 'pending'  && $new_status == 'pending' && get_option("frontier_post_mail_to_approve", "false") == "true") 
		{
		$author_name	= get_the_author_meta( 'display_name', $post->post_author );
        $to      		= get_option("frontier_post_mail_address") ? get_option("frontier_post_mail_address") : get_settings("admin_email");
        $subject 		= __("Post for approval from", "frontier-post").": ".$author_name ." (".get_bloginfo( "name" ).")";
        $body    		= 		__("Post for approval from", "frontier-post").": ".$author_name ." (".get_bloginfo( "name" ).")"."\r\n\r\n";
		$body    		= $body."Title:: ".$post->post_title."\r\n\r\n";
		$body    		= $body."Link to approvals: ".site_url('/wp-admin/edit.php?post_status=pending&post_type=post')."\r\n\r\n";

		//error_log('sending email: '.$subject.' To: '.$to);
		
        if( !wp_mail($to, $subject, $body ) ) 
			error_log(__("Message delivery failed - Recipient: (", "frontier-post").$to.")");
			
		}
		
	if(  $old_status == 'pending'  && $new_status == 'publish' && get_option("frontier_post_mail_approved", "false") == "true"  )
		{
		if ( $post->post_author == get_current_user_id() )
			return; // no reason to send email if current user is able to publish :)
		
		$to      		= get_the_author_meta( 'email', $post->post_author );
        $subject 		= __("Your post has been approved", "frontier-post")." (".get_bloginfo( "name" ).")";
        $body    		= __("Your post has been approved", "frontier-post").": ".$post->title ." (".get_bloginfo( "name" ).")"."\r\n\r\n";
		$body    		= $body."Title:: ".$post->post_title."\r\n\r\n";
		
		//error_log('sending email: '.$subject.' To: '.$to);
		
        if( !wp_mail($to, $subject, $body ) ) 
			error_log(__("Message delivery failed - Recipient: (", "frontier-post").$to.")");
		
		}
	}


add_action('transition_post_status', 'frontier_email_on_transition', 10, 3);



// WP editor tinyMCE has been changed, and wont work - New plugin Frontier Buttons should be used instead
global $wp_version;
if ($wp_version >= "3.9")
	{
	update_option("frontier_post_mce_custom", "false");
	}

// Load tinymce plugins if enabled in frontier settings.
$frontier_post_mce_custom = get_option("frontier_post_mce_custom", "false");
	
if ($frontier_post_mce_custom == "true") 
	add_filter('mce_external_plugins', 'frontier_tinymce_plugins');
else
	remove_filter('mce_external_plugins', 'frontier_tinymce_plugins');

function frontier_tinymce_plugins () 
	{
	//$plugins = array('emotions', 'table', 'searchreplace', 'wordcount');
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
	else
		show_admin_bar(true);
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



//add translation files
function frontier_post_init() 
	{
	load_plugin_textdomain('frontier-post', false, dirname( plugin_basename( __FILE__ ) ).'/language');
	}
	
add_action('plugins_loaded', 'frontier_post_init');

add_filter( 'get_edit_post_link', 'frontier_edit_post_link', 10, 2 );
add_action('admin_menu', 'frontier_post_settings_menu');

add_shortcode("frontier-post","frontier_user_posts");
//add_shortcode("frontier-post-add","frontier_post_add_edit");


//post_status_fix - Removed in version 1.6.1
//add_shortcode("frontier-status-fix","frontier_status_fix");
?>