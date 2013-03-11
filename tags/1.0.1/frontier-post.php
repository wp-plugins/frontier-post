<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://http://wordpress.org/extend/plugins/frontier-post/
Description: WordPress Frontier Post Plugin enables adding, deleting and editing standard posts from frontend. Add the shortcode [frontier-post] in a page, and you are ready to go.
Author: finnj
Version: 1.0.0
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
		if ( $user_posts->have_posts() ) 
			{ 
	
				include("forms/frontier_list.php");
			
				$pagination = paginate_links( array(
                    'base' => add_query_arg( 'pagenum', '%#%' ),
                    'format' => '',
                    'prev_text' => __( '&laquo;', 'frontier-post' ),
                    'next_text' => __( '&raquo;', 'frontier-post' ),
                    'total' => $user_posts->max_num_pages,
                    'current' => $pagenum
                        ) );

				if ( $pagination ) 
					{
						echo $pagination;
					}
			else 
				{
				echo "/br>";
				_e('Sorry, you do not have any posts.', 'frontier-post');
				echo "/br>";
				}
			}
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
				
			//error_log("Category from form: ".$tmp_category);
			if(!is_numeric($tmp_category) || $tmp_category <= 0)
				$tmp_category = get_option("default_category");
				
			//error_log("Category after check: ".$tmp_category);
			//$tmp_category = get_option("default_category");
			
			$tmp_title 	= trim( $_POST['user_post_title'] );
			if ( empty( $tmp_title ) ) 
				$tmp_title = "No Title";
			
			$tmp_title = trim( strip_tags( $tmp_title ));
        
			$tmp_content = trim( $_POST['user_post_desc'] );
			if ( empty( $tmp_content ) ) 
				$tmp_content = "No content";
			
			global $current_user;
			get_currentuserinfo();	
			
            $my_post = array(
                 'post_title' => $tmp_title,
                 'post_content' => $tmp_content,				 
                 'post_status' => $post_status,
                 'post_author' => $current_user->ID,				 
                 'post_category' => array( $tmp_category )   
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
				//delete post attachment
				$posts=get_posts("post_type=attachment&post_parent=".$_REQUEST['postid']);
				//print_r($posts);
				if(count($posts)>0)
					{
					foreach($posts as $pst)
						{
						wp_delete_post($pst->ID);
						}
					}
					//delete post
					wp_delete_post($_REQUEST['postid']);
            
				} 
			}
		}
	}

function frontier_post_set_defaults()
	{
	if(!is_numeric(get_option("frontier_post_edit_max_age")))
		{
		update_option("frontier_post_edit_max_age", 7 );
		}
	if(!is_numeric(get_option("frontier_post_delete_max_age")))
		{
		update_option("frontier_post_delete_max_age", 3 );
		}
	}
	
include('settings-menu.php');

//$tmp_include_file = WP_PLUGIN_DIR .'/frontier-post/include/frontier-pagination-test.php';
//error_log("include file: ".$tmp_include_file);
//include($tmp_include_file);
include('include/frontier_pagination_test.php');

function wpfrtp_enqueue_scripts()
	{
	wp_enqueue_style('frontierpost',plugins_url('frontier-post/frontier-post.css'));
	} 

add_action("wp_enqueue_scripts","wpfrtp_enqueue_scripts");  
add_action("init","wpfrtp_posting_form_submit"); 
add_action("init","wpfrtp_delete_post");  

add_action('admin_menu', 'frontier_post_settings_menu');
register_activation_hook( __FILE__ , 'frontier_post_set_defaults');

add_shortcode("frontier-post","wpfrtp_user_posts");
add_shortcode("frontier-test-pagination","wpfrtp_pagination_test");
 
?>