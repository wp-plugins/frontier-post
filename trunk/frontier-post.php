<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://http://wordpress.org/extend/plugins/frontier-post/
Description: Effective and secure plugin that enables adding, deleting and editing standard posts from frontend. Add the shortcode [frontier-post] in a page, and you are ready to go.
Author: finnj
Version: 1.1
Author URI: http://http://wordpress.org/extend/plugins/frontier-post/
*/



session_start();

include("include/frontier_post_defaults.php");
include("include/frontier_post_validation.php");

function  wpfrtp_user_post_list()
	{
	
		global $post;
		global $current_user;
		get_currentuserinfo();
		$pagenum	= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
		$ppp		= get_option('frontier_ppp') ? get_option('frontier_ppp') : 5;
	
		$args = array(
				'post_type' 		=> 'post',
				'post_status' 		=> 'publish',
				'author'			=>	$current_user->ID,
				'order'				=> 'DESC',
				'orderby' 			=> 'post_date', 
				'posts_per_page'    => $ppp,
				'paged'				=> $pagenum,
				);
		
		$user_posts 	= new WP_Query( $args );
		
		include("forms/frontier_list.php");
		
	}  


function wpfrtp_posting_form_submit()
	{
    
    if(isset($_POST['action'])&&$_POST['action']=="wpfrtp_save_post")
		{
        if($_POST['user_post_title'])
			{
			// Only handle published posts, rest must be done from admin panel
            $post_status = 'publish';
			
			if(isset($_POST['cat']))
				$tmp_category = $_POST['cat'];
				
			
			if(!is_numeric($tmp_category) || $tmp_category <= 0)
				$tmp_category = get_option("default_category");
				
			$tmp_title 	= trim( $_POST['user_post_title'] );
			if ( empty( $tmp_title ) ) 
				$tmp_title = "No Title";
			
			$tmp_title = trim( strip_tags( $tmp_title ));
        
			$tmp_content = trim( $_POST['user_post_desc'] );
			
			$tmp_excerpt = trim($_POST['user_post_excerpt'] );
			
			if ( empty( $tmp_content ) ) 
				$tmp_content = "No content";
			
			global $current_user;
			get_currentuserinfo();	
			
            $my_post = array(
                 'post_title' => $tmp_title,
                 'post_content' => $tmp_content,				 
                 'post_status' => $post_status,
                 'post_author' => $current_user->ID,				 
                 'post_category' => array( $tmp_category ),
				 'post_excerpt' => $tmp_excerpt,
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


function wpfrtp_user_post_form()
	{
    $concat= get_option("permalink_structure")?"?":"&";  
        
    if($_REQUEST['task']=="edit")
		{
        $thispost			= get_post($_REQUEST['postid']);
		$user_post_excerpt	= get_post_meta($thispost->ID, "user_post_excerpt");
        }
    else
		{
		$_REQUEST['task']="new";
		}
     
    include_once("forms/frontier_form.php");
	} 
 
function get_file_extension($file_name)
	{
          return substr(strrchr($file_name,'.'),1);
	}
  


function wpfrtp_user_posts()
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
                    wpfrtp_user_post_form();
                    break;
                case 'delete':
                    wpfrtp_delete_post();
                    wpfrtp_user_post_list();
                    break;    
                case '':
                default:
                    wpfrtp_user_post_list();
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

function wpfrtp_delete_post()
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
	$tmp_cap_list	= Array('can_add', 'can_edit', 'can_delete', 'exerpt_edit', 'redir_edit');
	
	//print_r('building default WP options');
	add_option("frontier_post_edit_max_age", 10 );
	add_option("frontier_post_delete_max_age", 3 );
	add_option("frontier_ppp", 15 );
	add_option("frontier_del_w_comments", "false"  );
	add_option("frontier_edit_w_comments", "false"  );
	
	
	foreach( $role_list as $role_name )
		{
		
		//error_log('Setting up role: '.$role_name);
		$xrole = get_role($role_name);
					
		foreach($tmp_cap_list as $tmp_cap)
			{
				if ( ($tmp_cap == 'exerpt_edit') || ($tmp_cap == 'can_delete') || ($role_name == 'subscriber') ) 
					$tmp_option = "false";
				else
					$tmp_option  = "true";
				
				add_option('frontier_post_'.$role_name.'_'.$tmp_cap, $tmp_option);
				
				// set capability
				if ( $tmp_option == "true" )
					{
						$xrole->add_cap( 'frontier_post_'.$tmp_cap );
					}
				
			} // End capabilities
		} // End roles

	// Add capability edit_published_pages to allow authors to upload media if not present already
	$xrole = get_role("author");
	$xrole_caps = $xrole->capabilities;
	if (!array_key_exists("edit_published_pages", $xrole_caps))
		{
			$xrole->add_cap( 'edit_published_pages');
			//Set option to use on unistall to remove capability again
			add_option("frontier_post_author_cap_set", "true");
		}
		
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
                 'post_title' => 'My Posts',
                 'post_content' => '[frontier-post]',				 
                 'post_status' => 'publish',
                 'post_type' => 'page',
				);
				
		// Insert the page into the database
        $tmp_id = wp_insert_post( $my_page );
		//print_r("</br>Create page - tmp id: ".$tmp_id."</br>");
		}
	
	add_option("frontier_post_page_id", $tmp_id );
	
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


function wpfrtp_enqueue_scripts()
	{
	wp_enqueue_style('frontierpost',plugins_url('frontier-post/frontier-post.css'));
	} 

add_action("wp_enqueue_scripts","wpfrtp_enqueue_scripts");  
add_action("init","frontier_get_user_role"); 
add_action("init","wpfrtp_posting_form_submit"); 
add_action("init","wpfrtp_delete_post");  

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

add_filter( 'get_edit_post_link', 'frontier_edit_post_link', 10, 2 );



add_action('admin_menu', 'frontier_post_settings_menu');


add_shortcode("frontier-post","wpfrtp_user_posts");
?>