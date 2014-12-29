<?php

function frontier_post_add_edit($frontier_post_shortcode_parms = array())
	{
	require_once(ABSPATH . '/wp-admin/includes/post.php');
	global $current_user;
	
	$fps_access_check_msg 		= "";
	$user_can_edit_this_post 	= false;
	
	//Reset access message
	$fps_access_check_msg = "";
	
	//Get Frontier Post settings
	
	$fp_options 		= get_option('frontier_post_options', array() );
	
	if (!is_user_logged_in())
		{
			echo "<br>---- ";
			if (get_option("frontier_post_show_login", "false") == "true" )
				echo __("Please log in !", "frontier-post")."&nbsp;<a href=".wp_login_url().">".__("Login Page", "frontier-post")."</a>&nbsp;&nbsp;";
			else
				_e("Please log in !", "frontier-post");
					
			echo "------<br><br>";
		}
	else	
		{
		// Check if new, and if Edit that current users is allowed to edit
		if(isset($_REQUEST['task']) && $_REQUEST['task']=="edit")
			{
			$thispost			= get_post($_REQUEST['postid']);
			$user_post_excerpt	= get_post_meta($thispost->ID, "user_post_excerpt");
			$tmp_task_new = false;
			if ( frontier_can_edit($thispost) == true )
				$user_can_edit_this_post = true;
			}
		else
			{
			if ( frontier_can_add($fps_access_check_msg) == true )
				{
				$user_can_edit_this_post = true;
				if ( empty($thispost->ID) )
					{
					$thispost = get_default_post_to_edit( "post", true );
					$thispost->post_author = $current_user->ID;
					$thispost->post_status = get_option("frontier_default_status", "publish");
					}
				$_REQUEST['task']="new";
				$tmp_task_new = true;
				}
			}
			$post_id = $thispost->ID;
		}
		
	
	
	
	// Do not proceed with all the processing if user is not able to add/edit
	if ( $user_can_edit_this_post == true )	
		{
		// Get vars from shortcode 
		extract($frontier_post_shortcode_parms);
		$concat= get_option("permalink_structure")?"?":"&";
	
		//get users role:
		$users_role 		= frontier_get_user_role();
		
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
		
		// Remove publish status from array if not allowed
		if (!current_user_can( 'frontier_post_can_publish' ))
			unset($tmp_status_list['publish']);
			
		
		// Set default status if new post - Check if the default status is in the allowed statuses, and if so set the default status
		$tmp_default_status 	= get_option("frontier_default_status", "publish");
		
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
			$editor_type 				= get_option("frontier_default_editor", "full");
		else
			$editor_type 				= $fp_options[$users_role]['editor'] ? $fp_options[$users_role]['editor'] : "full"; 
		
		$editor_layout		 		= array('dfw' => false, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300 );
		$frontier_media_button		= current_user_can( 'frontier_post_can_media' ) ? current_user_can( 'frontier_post_can_media' ) : false;
		$frontier_editor_lines 		= get_option('frontier_post_editor_lines', 300);
		
		// Call to wp_editor in done in entry form
		
		//************************************************************************
		// Setup category	
		//************************************************************************
		
		// If capabilities is managed from other plugin, use default setting for all profiles
		if ( get_option("frontier_post_external_cap", "false") == "true" )
			$category_type 			= get_option("frontier_default_cat_select", "multi");
		else
			$category_type 			= $fp_options[$users_role]['category'] ? $fp_options[$users_role]['category'] : "multi"; 
		
		$default_category			= $fp_options[$users_role]['default_category'] ? $fp_options[$users_role]['default_category'] : get_option("default_category"); 
		
		// set default category, if new and category parsed from shortcode, 
		if ( $tmp_task_new  )
			{
			$cats_selected = $frontier_cat_id;
			if ( $frontier_cat_id[0] > 0 )
				$default_category =  $frontier_cat_id[0];
			}
		else
			{
			$cats_selected	= $thispost->post_category;
			}
		
		// if no category selected (from post), insert default category
		if (empty($cats_selected[0]))
			$cats_selected[0] = $default_category;
		
		// Set variable for hidden field, if category field is removed from the form
			$cats_selected_txt = implode(',', $cats_selected);
		
		// Set categories to be excluded
		$frontier_post_excl_cats	= get_option("frontier_post_excl_cats", '');
		
		// Build list of categories (3 levels)
		if ( ($category_type == "multi") || ($category_type == "checkbox") )
			{
			$catlist 		= array();
			$catlist 		= frontier_tax_list("category", $frontier_post_excl_cats, $frontier_parent_cat_id );
			}	
			
		// Set tags
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
		
		$hide_post_status = ( get_option("frontier_post_hide_status", "false") == "true" ) ? true : false;
		
		$frontier_use_feat_img = get_option("frontier_post_show_feat_img", "false");
		
		} // end if OK to Edit
		
		
	if ($user_can_edit_this_post)
		{
		include_once(frontier_load_form("frontier_post_form.php"));	
		}
		
	else
		{
		// Echo reason why user cant add/edit post.
		echo $fps_access_check_msg;
		
		//Reset message once displayed
		$fps_access_check_msg = "";
		
		}

		
	} // end function



?>