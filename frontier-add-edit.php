<?php

function frontier_post_add_edit()
	{
	
	if (!is_user_logged_in())
		{
			echo "<br>---- ";
			$frontier_show_login = get_option("frontier_post_show_login", "false");
			//echo "Show login: ".$frontier_show_login."<br>";
			if ($frontier_show_login == "true" )
				echo __("Please log in !", "frontier-post")."&nbsp;<a href=".wp_login_url().">".__("Login Page", "frontier-post")."</a>&nbsp;&nbsp;";
			else
				_e("Please log in !", "frontier-post");
					
			echo "------<br><br>";
		}
	
	global $current_user;
	
	require_once(ABSPATH . '/wp-admin/includes/post.php');    
	//include_once("include/frontier_post_defaults.php");
    $concat= get_option("permalink_structure")?"?":"&";  
    
	if(isset($_REQUEST['task']) && $_REQUEST['task']=="edit")
		{
        $thispost			= get_post($_REQUEST['postid']);
		$user_post_excerpt	= get_post_meta($thispost->ID, "user_post_excerpt");
        $tmp_task_new = false;
		}
    else
		{
		if ( empty($thispost->ID) )
			$thispost = get_default_post_to_edit( "post", true );
		
		$thispost->post_author = $current_user->ID;
		$_REQUEST['task']="new";
		$tmp_task_new = true;
		}
		
	$post_id = $thispost->ID;
	//$_REQUEST['this_post_id'] = $post_id;
	//error_log("post_id: ".$post_id);
		
	$frontier_task = $_REQUEST['task'] ? $_REQUEST['task'] :"?";
	
	// Get return page id for save option Save & Return
	$return_p_id = isset($_REQUEST['frontier_return_page_id']) ? $_REQUEST['frontier_return_page_id'] : 0;
	
		
	
	// get options
	$saved_options 		= get_option('frontier_post_options', array() );
	
	
	//get users role:
	$users_role 		= frontier_get_user_role();
	
	$preview_label 				= __("Preview", "frontier-post");
	
	if(!isset($thispost->post_type))
		{
			$thispost->post_type = 'post';
		}

	if(!isset($thispost->post_content))
		{
			$thispost->post_content = '';
		}

	frontier_media_fix( $post_id );
	
	$user_can_edit_this_post = true;
	
	if (!frontier_can_edit($thispost) == true)
		$user_can_edit_this_post = false;
		
	if ($thispost->post_author != $current_user->ID && (!current_user_can( 'edit_others_posts' )))
		$user_can_edit_this_post = false;
	
	if (($frontier_task == "new") && (!current_user_can( 'frontier_post_can_add' )))
		$user_can_edit_this_post = false;
	
	//build post status list based on current status and users capability
	$tmp_status_list = get_post_statuses( );
	$tmp_status_list = array_reverse($tmp_status_list);
	
	// Remove private status from array if not allowed
	if (!current_user_can('frontier_post_can_private'))
		unset($tmp_status_list['private']);
	
	// Remove draft status from array if user is not allowed to use drafts
	if (!current_user_can('frontier_post_can_draft'))
		unset($tmp_status_list['draft']);
	
	//print_r("Pre-Status: ".$thispost->post_status."</br>");
	// Set default status if new post
	if ( (isset($_REQUEST['task'])) && ($_REQUEST['task'] == "new") )
		{
		$tmp_default_status 	= get_option("frontier_default_status", "publish");
		// Check if the default status is in the allowed statuses, and if so set the default status
		if (array_key_exists($tmp_default_status , $tmp_status_list))
			$thispost->post_status	= $tmp_default_status;
		}
		
	$status_list 		= array();
	$tmp_post_status 	= $thispost->post_status ? $thispost->post_status : "unknown";
	
	$status_readonly = "";
	
	if ($tmp_post_status == "publish")
		{
		if (get_option("frontier_post_change_status", "false") != "true")
			{
			$status_readonly = "READONLY";
			//print_r("Readonly: ".$status_readonly."<br>");
			}
		else
			{
			if (current_user_can( 'frontier_post_can_publish' ))
				$status_list = $tmp_status_list;			
			}
		// somthings wrong with the following line ????
		$status_list[$tmp_post_status] = $tmp_status_list[$tmp_post_status];
			
		
		if (!current_user_can( 'frontier_post_can_publish' ))
			{
			$user_can_edit_this_post = false;
			}
		}
	else
		{
		$status_list = $tmp_status_list;
		if (!current_user_can( 'frontier_post_can_publish' ))
			{
			unset($status_list['publish']);
			}
		}
	
	// -- Setup wp_editor layout
	// full: full Tiny MCE
	// minimal-visual: Teeny layout
	// minimal-html: simple layout with html options
	// text: text only
	
	// setup editor
	
	// If capabilities is managed from other plugin, use default setting for all profiles
	if ( get_option("frontier_post_external_cap", "false") == "true" )
		$editor_type 				= get_option("frontier_default_editor", "full");
	else
		$editor_type 				= $saved_options[$users_role]['editor'] ? $saved_options[$users_role]['editor'] : "full"; 
	
	
	$editor_layout		 		= array('dfw' => false, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300 );


	
	
	if (!current_user_can( 'frontier_post_can_media' ))
		{
		$tmp = array('media_buttons' => false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
	
	if ($editor_type == "minimal-visual")
		{
		$tmp = array('teeny' => true, 'quicktags' => false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
		
	if ($editor_type == "minimal-html")
		{
		$tmp = array('teeny' => true, 'tinymce' => false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
		
	if ($editor_type == "text")	
		{
		$tmp = array('quicktags' => false, 'tinymce' =>false);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
		
	//************************************************************************
	// Setup category	
	//************************************************************************
	
	//error_log(print_r($saved_options[$users_role],true));
	
	// If capabilities is managed from other plugin, use default setting for all profiles
	if ( get_option("frontier_post_external_cap", "false") == "true" )
		$category_type 			= get_option("frontier_default_cat_select", "full");
	else
		$category_type 			= $saved_options[$users_role]['category'] ? $saved_options[$users_role]['category'] : "multi"; 
	
	$default_category			= $saved_options[$users_role]['default_category'] ? $saved_options[$users_role]['default_category'] : get_option("default_category"); 
	// Check if default category set in querystring or shortcode
	//error_log("Default Category:".$default_category);
	if ( $tmp_task_new == true )
		{
		// Category from shortcode
		if ( (isset( $_REQUEST['frontier_cat_id'] )) &&  $_REQUEST['frontier_cat_id'] > 0 )
			{
			$default_category =  $_REQUEST['frontier_cat_id'] ;
			//error_log("Default Category from shortcode:".$default_category);
			}
		// Category from widget
		if ( (isset( $_REQUEST['frontier_cat_id_from_catpage'] )) &&  $_REQUEST['frontier_cat_id_from_catpage'] > 0 )
			{
			$default_category = $_REQUEST['frontier_cat_id_from_catpage'];
			//error_log("Default Category from widget:".(isset( $_REQUEST['frontier_cat_id_from_catpage'] ) ? $_REQUEST['frontier_cat_id_from_catpage'] : -1));
			}
		}
	else
		{
		$cats_selected	= $thispost->post_category;
		}
	$frontier_post_excl_cats	= get_option("frontier_post_excl_cats", '');
	$parent_category 			= isset($_REQUEST['parent_cat']) ? $_REQUEST['parent_cat'] : "0";
	
	//echo "Parent cat: ".$parent_category."<br>";
	
	
	
	if (empty($cats_selected[0]))
		$cats_selected[0] = $default_category;
	
	// Build list of categories (3 levels)
	if ( ($category_type == "multi") || ($category_type == "checkbox") )
		{
				
		$catlist 		= array();
		$catlist 		= frontier_tax_list("category", $frontier_post_excl_cats, $parent_category );
		}
	
	
	
	if ($category_type == "single")
		{
		if(isset($thispost->ID) )
			{
			$postcategory=get_the_category($thispost->ID); 
			if (array_key_exists(0, $postcategory))
				$postcategoryid = $postcategory[0]->term_id;
			else
				$postcategoryid	= $default_category;				
			}
		else
			{
			$postcategoryid	= $default_category;				
			}	
		}
	
	// get category page from widget
	if ( (isset($_REQUEST['returncategory']) ? $_REQUEST['returncategory'] : '?') == "true" )
		$_REQUEST['return_category_archive'] = $cats_selected[0];
	else
		$_REQUEST['return_category_archive'] = 0;
		
	if ( current_user_can( 'frontier_post_tags_edit' )  )
		{
		$taglist = array();
		if (isset($thispost->ID))
			{
			$tmptags = get_the_tags($thispost->ID);
			if ($tmptags)
				{
				foreach ($tmptags as $tag) :
					array_push($taglist, $tag->name);
				endforeach;
				}
			}
		}
	
	$frontier_use_feat_img = get_option("frontier_post_show_feat_img") ? get_option("frontier_post_show_feat_img") : "false";
	
	include_once(frontier_load_form("frontier_form.php"));	
	} 



?>