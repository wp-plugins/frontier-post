<?php

function frontier_post_add_edit($frontier_post_shortcode_parms = array())
	{
	require_once(ABSPATH . '/wp-admin/includes/post.php');
	global $current_user;
	global $wpdb;
	//add_thickbox();
	
	$frontier_permalink = get_permalink();
	$concat				= get_option("permalink_structure")?"?":"&";
	//set start of output debug query
	$qlog 		= $wpdb->queries;
	$qlog_start = count($qlog);
	
	
	$fps_access_check_msg 		= "";
	$user_can_edit_this_post 	= false;
	
	//Reset access message
	$fps_access_check_msg = "";
	
	//Get Frontier Post capabilities
	$fp_capabilities	= frontier_post_get_capabilities();
	
	//$fp_settings		= frontier_post_get_settings()
	
	// Get vars from shortcode 
	extract($frontier_post_shortcode_parms);
	
	
	if (!is_user_logged_in())
		{
			echo fp_login_text();
		}
	else	
		{
		// Check if new, and if Edit that current users is allowed to edit
		if(isset($_REQUEST['task']) && $_REQUEST['task']=="edit")
			{
			$thispost			= get_post($_REQUEST['postid']);
			$user_post_excerpt	= get_post_meta($thispost->ID, "user_post_excerpt");
			$tmp_task_new 		= false;
			if ( frontier_can_edit($thispost) == true )
				$user_can_edit_this_post = true;
			}
		else
			{
			$tmp_post_type	= post_type_exists($frontier_add_post_type) ? $frontier_add_post_type : 'post';
			if ( frontier_can_add($tmp_post_type) == true )
				{
				if ( empty($thispost->ID) )
					{					
					$thispost 				= get_default_post_to_edit( "$tmp_post_type", true );
					$thispost->post_author 	= $current_user->ID;
					$thispost->post_type	= $tmp_post_type;
					//echo "New post for edit: ".$thispost->ID."<br>";
					}
				$_REQUEST['task']="new";
				$tmp_task_new = true;
				$user_can_edit_this_post = true;
				
				}
			}
			
		}
		
	
	
	
	// Do not proceed with all the processing if user is not able to add/edit
	if ( $user_can_edit_this_post == true )	
		{
		
		$post_id = $thispost->ID;
		
		
	
		//get users role:
		$users_role 		= frontier_get_user_role();
		
		// get list of taxonomies
		$tax_form_lists		= frontier_get_tax_lists($frontier_page_id, intval($frontier_parent_cat_id), intval($fps_cache_time_tax_lists) );
		
		//******************************************************************************************
		// Set defaults, so post can be saved without errors
		//******************************************************************************************
		if(!isset($thispost->post_type))
			$thispost->post_type = 'post';
		
		if(!isset($thispost->post_content))
			$thispost->post_content = '';
		
		// Call media fix (to support older versions) 
		frontier_media_fix( $post_id );
		
		//******************************************************************************************
		// Manage post status
		//******************************************************************************************
		
		//build post status list based on current status and users capability
		$tmp_status_list = get_post_statuses( );
		$tmp_status_list = array_reverse($tmp_status_list);
		
		// Remove private status from array if not allowed
		if (!current_user_can('frontier_post_can_private'))
			unset($tmp_status_list['private']);
		
		// Remove draft status from array if user is not allowed to use drafts
		if (!current_user_can('frontier_post_can_draft'))
			unset($tmp_status_list['draft']);
		
		// Remove pending status from array if user is not allowed to use pending status or if it is a page we are editing
		if ( !current_user_can('frontier_post_can_pending') || ($thispost->post_type == 'page') )
			unset($tmp_status_list['pending']);
		
		
		// Remove publish status from array if not allowed
		if (!current_user_can( 'frontier_post_can_publish' ))
			unset($tmp_status_list['publish']);
			
		
		// Set default status if new post - Check if the default status is in the allowed statuses, and if so set the default status
		$tmp_default_status 	= fp_get_option("fps_default_status", "publish");
		
		if ( ($tmp_task_new == true) && array_key_exists($tmp_default_status , $tmp_status_list))
			$thispost->post_status	= $tmp_default_status;
			
		$status_list 		= array();
		$tmp_post_status 	= $thispost->post_status ? $thispost->post_status : $tmp_default_status;
		
		// if The deafult status is not in the list, set default status to the first in the list
		if ( !in_array($tmp_post_status, array_keys($tmp_status_list)) )
			$tmp_post_status = current(array_keys($tmp_status_list));

		$status_list = $tmp_status_list;
		
		
		
		//**************************************************************************************************
		// -- Setup wp_editor layout
		// full: full Tiny MCE
		// minimal-visual: Teeny layout
		// minimal-html: simple layout with html options
		// text: text only
		//**************************************************************************************************
		
		
		// If capabilities is managed from other plugin, use default setting for all profiles
		if ( get_option("frontier_post_external_cap", "false") == "true" )
			$editor_type 				= fp_get_option("fps_default_editor", "full");
		else
			$editor_type 				= $fp_capabilities[$users_role]['fps_role_editor_type'] ? $fp_capabilities[$users_role]['fps_role_editor_type'] : "full"; 
		
		$editor_layout		 		= array('dfw' => false, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300 );
		$frontier_media_button		= current_user_can( 'frontier_post_can_media' ) ? current_user_can( 'frontier_post_can_media' ) : false;
		
		
		// Call to wp_editor in done in entry form
		
		//************************************************************************
		// Setup category	
		//************************************************************************
		
		// Do not manage categories for page
		if ( $thispost->post_type != 'page' )
			{
		
			// If capabilities is managed from other plugin, use default setting for all profiles
			if ( fp_get_option("fps_external_cap", "false") == "true" )
				$category_type 			= fp_get_option("fps_default_cat_select", "multi");
			else
				$category_type 			= $fp_capabilities[$users_role]['fps_role_category_layout'] ? $fp_capabilities[$users_role]['fps_role_category_layout'] : "multi"; 
	
		
			$default_category			= $fp_capabilities[$users_role]['fps_role_default_category'] ? $fp_capabilities[$users_role]['fps_role_default_category'] : get_option("default_category"); 
	
			// set default category, if new and category parsed from shortcode, 
			if ( $tmp_task_new  )
				{
				$cats_selected = $frontier_cat_id;
				if ( count($frontier_cat_id) > 0 && $frontier_cat_id[0] > 0 )
					$default_category =  $frontier_cat_id[0];
				}
			else
				{
				$cats_selected	= $thispost->post_category;
				}
	
			// if no category selected (from post), insert default category.
			// removed in version 3.5.7, as default category is set on save
			/*
			if (empty($cats_selected[0]))
				$cats_selected[0] = $default_category;
			*/
			
			
			// Build list of categories (3 levels)
			// removed in version 3.5.7
			/*
			if ( ($category_type == "multi") || ($category_type == "checkbox") )
				{
				$catlist 		= array();
				$catlist 		= frontier_tax_list("category", fp_get_option("fps_excl_cats", ''), $frontier_parent_cat_id );
				}
			*/
			
			}	
		else
			{
			$cats_selected = array();
			} // end exclude categories for pages
		
			
		// Set variable for hidden field, if category field is removed from the form
		$cats_selected_txt = implode(',', $cats_selected);
		//echo "Cats selected: ".$cats_selected_txt."<hr>";
		
		//***************************************************************************************
		//* Set tags
		//***************************************************************************************
		
		$fp_tag_count	= fp_get_option_int("fps_tag_count",3);
		
		if ( current_user_can( 'frontier_post_tags_edit' ) && ($thispost->post_type != 'page') )
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
		
		$hide_post_status = ( fp_get_option("fps_hide_status", "false") == "true" ) ? true : false;
		
		$frontier_use_feat_img = fp_get_option("fps_show_feat_img", "false");
		
		//***************************************************************************************
		//* Get post moderation fields
		//***************************************************************************************
		
		if ( fp_get_option_bool("fps_use_moderation") && (current_user_can("edit_others_posts") || $current_user->ID == $thispost->post_author))
			{
			$fp_moderation_comments = get_post_meta( $post_id, 'FRONTIER_POST_MODERATION_TEXT', true );
			}
		
		} // end if OK to Edit
		
		
	if ($user_can_edit_this_post)
		{
		
		$fp_form = $frontier_edit_form;
		
		if ($thispost->post_type == 'page')
			$fp_form = "page";
		
		//echo "Form: ".$fp_form."<br>";
		
		switch($fp_form)
			{
			case "standard":
				include(frontier_load_form("frontier_post_form_standard.php"));	
				break;
			
			case "old":
				include(frontier_load_form("frontier_post_form_old.php"));	
				break;
			
			case "simple":
				include(frontier_load_form("frontier_post_form_simple.php"));
				break;
			
			case "page":
				include(frontier_load_form("frontier_post_form_page.php"));	
				break;
			
			default:
				include(frontier_load_form("frontier_post_form_standard.php"));	
				break;
			
			
			}
		//output debug query
		if ( 1 === 2)
			{
			error_log('---------------- SQL LOG START ('.$qlog_start.')---------------');
			global $wpdb;
			$q_log = $wpdb->queries;
			error_log("Queries");
			error_log(print_r($q_log, true));
			$l = 0;
			echo "<hr>Queries<hr>";
			foreach ($q_log as $tmp_sql)
				{
				if ($l >= $qlog_start)
					//error_log('('.zeroise($l,3).') '.$tmp_sql[1].' '.$tmp_sql[0]);
					echo ('('.zeroise($l,3).') '.$tmp_sql[1].' '.$tmp_sql[0])."<hr>";
				$l++;
				}
			error_log('---------------- SQL LOG END---------------');
			}		
		
		}
		
	else
		{
		// Echo reason why user cant add/edit post.
		global $fps_access_check_msg;
		if ( empty($fps_access_check_msg) || ($fps_access_check_msg < " ") )
			echo __("You are not allowed to edit this post, sorry ", "frontier-post");
		else
			echo "<br>".$fps_access_check_msg;
		
		//Reset message once displayed
		$fps_access_check_msg = "";
		
		}

	
	} // end function



?>