<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://wordpress.org/extend/plugins/frontier-post/
Description: Fast, easy & secure Front End management of posts. Add, Edit, Delete posts from frontend - My Posts Widget
Author: finnj
Version: 1.3.2
Author URI: http://wordpress.org/extend/plugins/frontier-post/
*/

// define constants
define('FRONTIER_POST_VERSION', "1.3.2"); 
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
				$tmp_title = "No Title";
			
			$tmp_title = trim( strip_tags( $tmp_title ));
        
			$tmp_content = trim( $_POST['user_post_desc'] );
			if ( empty( $tmp_content ) ) 
				$tmp_content = "No content";
			
			$tmp_excerpt = isset( $_POST['user_post_excerpt']) ? trim($_POST['user_post_excerpt'] ) : null;

			$tmp_categorymulti = $_POST['categorymulti'];
			if ((!isset($tmp_categorymulti)) || (count($tmp_categorymulti)==0))
				$tmp_categorymulti = Array(get_option("default_category"));
				
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
	
	//error_log("Setting Frontier Post application Defaults ");
	
	global $wpdb;
	
	
	$role_list		= Array('administrator', 'editor', 'author', 'contributor', 'subscriber');
	$tmp_cap_list	= Array('can_add', 'can_edit', 'can_delete', 'exerpt_edit', 'tags_edit', 'redir_edit');
	
	//print_r('building default WP options');
	add_option("frontier_post_edit_max_age", 10 );
	add_option("frontier_post_delete_max_age", 3 );
	add_option("frontier_post_ppp", 15 );
	add_option("frontier_post_del_w_comments", "false"  );
	add_option("frontier_post_edit_w_comments", "false"  );
	
	
	foreach( $role_list as $role_name )
		{
		
		//error_log('Setting up role: '.$role_name);
		$xrole = get_role($role_name);
					
		foreach($tmp_cap_list as $tmp_cap)
			{
				// Only enable all defaults for Administrator, Editor & Author
				if ( ($role_name == 'administrator') || ($role_name == 'editor') )
					{
					$tmp_option  = "true";
					}
				else
					{
					// all options false for other profiles
					$tmp_option = "false";
					// except author who can add, edit and use the edit redir functionality
					if ( ($role_name == 'author') && (($tmp_cap == 'add_edit') || ($tmp_cap == 'can_edit') || ($tmp_cap == 'redir_edit') ) )
						{
						$tmp_option  = "true";
						}
					}
				
				$tmp_option_id = 'frontier_post_'.$role_name.'_'.$tmp_cap;
				
				// add option (will not overwrite value if allready defined
				add_option('frontier_post_'.$role_name.'_'.$tmp_cap, $tmp_option);
				
				// set capability (Based on option, so previous settings are respected is set)
				$tmp_value		= ( get_option($tmp_option_id) ? get_option($tmp_option_id) : "false" );
				if ( $tmp_value == "true" )
					{
						$xrole->add_cap( 'frontier_post_'.$tmp_cap );
					}
				else
					{
						$xrole->remove_cap( 'frontier_post_'.$tmp_cap );
					}
				
			} // End capabilities
		} // End roles

	// Add capability edit_published_pages to allow authors to upload media if not present already
	$xrole = get_role("author");
	$xrole_caps = $xrole->capabilities;
	/* Not neccessary 
	if (!array_key_exists("edit_published_pages", $xrole_caps))
		{
			$xrole->add_cap( 'edit_published_pages');
			//Set option to use on unistall to remove capability again
			add_option("frontier_post_author_cap_set", "true");
		}
	*/	
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
	
	}

register_activation_hook( __FILE__ , 'frontier_post_set_defaults');
	
function frontier_get_user_role() 
	{
	$tmp_role = 'unkown';
	
	if (current_user_can( 'administrator' ))
		{ $tmp_role = 'administrator'; }
	if (current_user_can( 'editor' ))
		{ $tmp_role = 'editor'; }
	if (current_user_can( 'author' ))
		{ $tmp_role = 'author'; }
	if (current_user_can( 'contributor' ))
		{ $tmp_role = 'contributor'; }
	if (current_user_can( 'subscriber' ))
		{ $tmp_role = 'subscriber'; }
	
	return $tmp_role;
	
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

//error_log("Log message : ");

function frontier_edit_post_link( $url, $post_id ) 
	{
	if ( is_admin()  ) // Administrator in admin dashboard, dont change url
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
?>