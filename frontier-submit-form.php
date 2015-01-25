<?php

function frontier_posting_form_submit($frontier_post_shortcode_parms = array())
	{
    extract($frontier_post_shortcode_parms);		
    
    //Get Frontier Post capabilities
	$fp_capabilities	= frontier_post_get_capabilities();
	
	//fp_log("fp cat id Submit: ".($frontier_cat_id ? $frontier_cat_id : "Unknown"));
	
	//$tmp_txt = isset($_GET['frontier_new_cat_widget']) ? "true" : "false";
	//fp_log("From widget (submit) ?: ".(isset($_GET['frontier_new_cat_widget']) ? "true" : "false"));
	
	if(isset($_POST['action'])&& $_POST['action']=="wpfrtp_save_post")
		{
		if ( !wp_verify_nonce( $_POST['frontier_add_edit_post_'.$_POST['postid']], 'frontier_add_edit_post'  ) )
			{
			wp_die(__("Security violation (Nonce check) - Please contact your Wordpress administrator", "frontier-post"));
			}
		
		
		if ( isset($_REQUEST['task']) && ($_REQUEST['task'] == "new") )
			$tmp_task_new = true;
		else	
			$tmp_task_new = false;
			
		//fp_log("New post ? : ".$tmp_task_new);
		
		if(isset($_POST['post_status']))
			$post_status = $_POST['post_status'];
		else
			$post_status = 'draft';
		
		$tmp_post_type = isset($_POST['posttype']) ? $_POST['posttype'] : 'post';
		
		$postid = $_POST['postid'];
		
		$tmp_title 	= trim( $_POST['user_post_title'] );
		if ( empty( $tmp_title ) ) 
			$tmp_title = __("No Title", "frontier-post");
		
		$tmp_title = trim( strip_tags( $tmp_title ));
	
		$tmp_content = trim( $_POST['user_post_desc'] );
		if ( empty( $tmp_content ) ) 
			$tmp_content = __("No content", "frontier-post");
		
		$tmp_excerpt = isset( $_POST['user_post_excerpt']) ? trim($_POST['user_post_excerpt'] ) : null;
		
		$users_role 	= frontier_get_user_role();
		
		//****************************************************************************************************
		// Manage Categories
		//****************************************************************************************************
		
		// Do not manage categories for page
		if ( $tmp_post_type != 'page' )
			{
			$category_type 		= $fp_capabilities[$users_role]['fps_role_category_layout'] ? $fp_capabilities[$users_role]['fps_role_category_layout'] : "multi"; 
			$default_category	= $fp_capabilities[$users_role]['fps_role_default_category'] ? $fp_capabilities[$users_role]['fps_role_default_category'] : get_option("default_category"); 
		
			$tmp_field_name = frontier_tax_field_name('category');
			if ( ($category_type != "hide") && ($category_type != "readonly") )
				$tmp_categorymulti = ( isset($_POST[$tmp_field_name]) ? $_POST[$tmp_field_name] : array() );
		
			/*
			if ($category_type == "single")
				{
				if(isset($_POST['cat']))
					{
					$tmp_category = $_POST['cat'];
					$tmp_categorymulti = array($tmp_category);
					}
				}
			*/
		
			// if no category returned from entry form, check for hidden field, if this is empty set default category 
			if ((!isset($tmp_categorymulti)) || (count($tmp_categorymulti)==0))
				{
				$tmp_categorymulti = ( isset($_POST['post_categories']) ? explode(',', $_POST['post_categories']) : array());
				// Do not use default category if post type = page 
				if ( $tmp_post_type != 'page' )
					$tmp_categorymulti = ((count($tmp_categorymulti) > 0) ? $tmp_categorymulti : array($default_category));
				}
			} // do not manage categories for pages
		
		//****************************************************************************************************
		// Update post
		//****************************************************************************************************
		
		
		$tmp_post = array(
			 'ID'				=> $postid,
			 'post_type'		=> $tmp_post_type,
			 'post_title' 		=> $tmp_title,
			 'post_status' 		=> $post_status,
			 'post_content' 	=> $tmp_content,				 
			 'post_excerpt' 	=> $tmp_excerpt
			);
		
		// Do not manage categories for page
		if ( $tmp_post_type != 'page' )
			{
			$tmp_post['post_category'] 	= $tmp_categorymulti;
			}
		
		
		//****************************************************************************************************
		// Apply filter before update of post 
		// filter:			frontier_post_pre_update
		// $tmp_post 		Array that holds the updated fields 
		// $tmp_task_new  	Equals true if the user is adding a post
		// $_POST			Input form			
		//****************************************************************************************************
		
		apply_filters( 'frontier_post_pre_update', $tmp_post, $tmp_task_new, $_POST );
		
		wp_update_post( $tmp_post );
		
		
		
		//****************************************************************************************************
		// Tags
		//****************************************************************************************************
		
		// Do not manage tags for page
		if ( current_user_can( 'frontier_post_tags_edit' ) && $tmp_post_type != 'page' )
			{
			$taglist = array();
			if (isset( $_POST['user_post_tag1']))
				array_push($taglist, $_POST['user_post_tag1']);
			
			if (isset( $_POST['user_post_tag2']))
				array_push($taglist, $_POST['user_post_tag2']);
			
			if (isset( $_POST['user_post_tag3']))
				array_push($taglist, $_POST['user_post_tag3']);
		
				wp_set_post_tags($postid, $taglist);
			}

		if ( $tmp_task_new == true )
			frontier_post_set_msg(__("Post added", "frontier-post").": ".$tmp_title);
		else	
			frontier_post_set_msg(__("Post updated", "frontier-post").": ".$tmp_title);
		
		
		//****************************************************************************************************
		// Taxonomies
		//****************************************************************************************************
		
		//error_log("Saving tax for: ".$tmp_title." - Post Type: ".$tmp_post_type );
		//error_log(print_r($frontier_custom_tax, true));
			
		
		// Do not manage taxonomies for page
		if ( $tmp_post_type != 'page' )
			{
			foreach ( $frontier_custom_tax as $tmp_tax_name ) 
				{
				if ( !empty($tmp_tax_name) && ($tmp_tax_name != 'category') )
					{
					$tmp_field_name = frontier_tax_field_name($tmp_tax_name);
					$tmp_value = isset($_POST[$tmp_field_name]) ? $_POST[$tmp_field_name] : array();
					if ( is_array($tmp_value) )
						$tmp_tax_selected = $tmp_value;
					else
						$tmp_tax_selected = array($tmp_value);
				
					wp_set_post_terms( $postid, $tmp_tax_selected, $tmp_tax_name );
	
					}
				}	
			} // end do not manage taxonomies for pages
	
		//****************************************************************************************************
		// End updating post
		//****************************************************************************************************
				
		//Get the updated post
		$my_post = get_post($postid);
		
		//****************************************************************************************************
		// Avtion fires after add/update of post, and after taxonomies are updated
		// Do action 		frontier_post_post_save
		// $my_post 		Post object for the post just updated 
		// $tmp_task_new  	Equals true if the user is adding a post
		// $_POST			Input form			
		//****************************************************************************************************
		
		do_action('frontier_post_post_save', $my_post, $tmp_task_new, $_POST);
		
		
		$tmp_return = isset($_POST['user_post_submit']) ? $_POST['user_post_submit'] : "savereturn";
		
		//If save, set task to edit
		if ( $tmp_return == "save" )
			{
			$_REQUEST['task'] = "edit";
			$_REQUEST['postid'] = $postid;
			}
		
		// if shortcode frontier_mode=add, return to add form instead of list
		if ( $frontier_mode == "add" && $tmp_return == "savereturn")
			$tmp_return = "add";
			
		
		switch( $tmp_return )
			{
			case 'preview':
				frontier_preview_post($postid);
				break;
			
			case 'add':
				frontier_post_add_edit($frontier_post_shortcode_parms);
				break;
			
			case 'savereturn':
				frontier_user_post_list($frontier_post_shortcode_parms);
				break;
				
			case 'save':
				frontier_post_add_edit($frontier_post_shortcode_parms);
				break;
				
			
			default:
				frontier_user_post_list($frontier_post_shortcode_parms);
				break;
			} 
	
		} 
	else
		{
		frontier_post_set_msg(__("Error - Unable to save post", "frontier-post"));
		frontier_user_post_list($frontier_post_shortcode_parms);
		} // end isset post
} // end function frontier_posting_form_submit


?>