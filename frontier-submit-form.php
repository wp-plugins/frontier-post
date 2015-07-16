<?php

function frontier_posting_form_submit($frontier_post_shortcode_parms = array())
	{
    extract($frontier_post_shortcode_parms);		
    global $current_user;
    //Get Frontier Post capabilities
	$fp_capabilities	= frontier_post_get_capabilities();
	
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
		// check empty title, and set status to draft if status is empty
		if ( empty( $tmp_title ) )
			{
			$tmp_title = __("No Title", "frontier-post");
			$post_status = 'draft';
			frontier_post_set_msg('<div id="frontier-post-alert">'.__("Warning", "frontier-post").': '.__("Title was empty", "frontier-post").' - '.__("Post status set to draft", "frontier-post").'</div>');
			}
		$tmp_title = trim( strip_tags( $tmp_title ));
	
		$tmp_content = trim( $_POST['user_post_desc'] );
		if ( empty( $tmp_content ) ) 
			{
			$tmp_content = __("No content", "frontier-post");
			$post_status = 'draft';
			frontier_post_set_msg('<div id="frontier-post-alert">'.__("Warning", "frontier-post").': '.__("Content was empty", "frontier-post").' - '.__("Post status set to draft", "frontier-post").'</div>');
			}
			
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
		
		
			
			//frontier_post_set_msg("Default Category: ".$default_category);
			//frontier_post_set_msg("Post Categories: ".( isset($_POST['post_categories']) ? $_POST['post_categories'] : "NONE"));
			
		
			// if no category returned from entry form, check for hidden field, if this is empty set default category 
			if ((!isset($tmp_categorymulti)) || (count($tmp_categorymulti)==0) )
				{
				$tmp_categorymulti = ( isset($_POST['post_categories']) ? explode(',', $_POST['post_categories']) : array());
				// Do not use default category if post type = page 
				if ( $tmp_post_type != 'page' )
					$tmp_categorymulti = ((count($tmp_categorymulti) > 0 && isset($tmp_categorymulti[0]) && $tmp_categorymulti[0] > 0) ? $tmp_categorymulti : array($default_category));
				}
			//frontier_post_set_msg("Category from POST: ".print_r($tmp_categorymulti,true));
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
		
		$tmp_post = apply_filters( 'frontier_post_pre_update', $tmp_post, $tmp_task_new, $_POST );
		
		//force save with draft status first, if new post and status is set to published to align with wordpress standard
		if ( ($tmp_task_new == true) && ($post_status == "publish") )
			{
			$tmp_post['post_status'] = "draft";
			wp_update_post( $tmp_post );
			$tmp_post = array('ID'	=> $postid, 'post_status' => $post_status);
			wp_update_post( $tmp_post );
			}
		else
			{
			wp_update_post( $tmp_post );
			}
		
		
		//****************************************************************************************************
		// Tags
		//****************************************************************************************************
		
		// Do not manage tags for page
		if ( current_user_can( 'frontier_post_tags_edit' ) && $tmp_post_type != 'page' )
			{
			$fp_tag_count	= fp_get_option_int("fps_tag_count",3);
			$taglist = array();
			for ($i=0; $i<$fp_tag_count; $i++)
				{
				if (isset( $_POST['user_post_tag'.$i]))
					{
					array_push($taglist, fp_tag_transform($_POST['user_post_tag'.$i]));
					}
				}
				wp_set_post_tags($postid, $taglist);
			}

		

		//****************************************************************************************************
		// Add/Update message
		//****************************************************************************************************
		

		if ( $tmp_task_new == true )
			frontier_post_set_msg(__("Post added", "frontier-post").": ".$tmp_title);
		else	
			frontier_post_set_msg(__("Post updated", "frontier-post").": ".$tmp_title);
		
		
		//****************************************************************************************************
		// Taxonomies
		//****************************************************************************************************
		
			
		
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
		
		// Delete users cache for My Posts widget
		fp_delete_my_posts_cache($current_user->ID);
		//fp_delete_my_approvals_cache($current_user->ID);
		
		//***************************************************************************************
		//* Save post moderation fields
		//***************************************************************************************
		
		if ( fp_get_option_bool("fps_use_moderation") && (current_user_can("edit_others_posts") || $current_user->ID == $my_post->post_author))
			{
			if (isset($_POST['frontier_post_moderation_new_text']))
				{
				$fp_moderation_comments_new = $_POST['frontier_post_moderation_new_text'];
				//$fp_moderation_comments_new = trim(stripslashes(strip_tags($fp_moderation_comments_new)));
				$fp_moderation_comments_new = wp_strip_all_tags($fp_moderation_comments_new);
				$fp_moderation_comments_new = nl2br($fp_moderation_comments_new);
				$fp_moderation_comments_new = stripslashes($fp_moderation_comments_new);
				$fp_moderation_comments_new = trim($fp_moderation_comments_new);
				if (strlen($fp_moderation_comments_new) > 0)
					{
					global $current_user;
					
					$fp_moderation_comments_old = get_post_meta( $my_post->ID, 'FRONTIER_POST_MODERATION_TEXT', true );
					$fp_moderation_comments  = current_time( 'mysql')." - ".$current_user->user_login.":<br>";
					$fp_moderation_comments .= $fp_moderation_comments_new."<br>";
					$fp_moderation_comments .= '<hr>'."<br>";
					$fp_moderation_comments .= $fp_moderation_comments_old."<br>";
					update_post_meta( $my_post->ID, 'FRONTIER_POST_MODERATION_TEXT', $fp_moderation_comments );
					update_post_meta( $my_post->ID, 'FRONTIER_POST_MODERATION_DATE', current_time( 'mysql'));
					update_post_meta( $my_post->ID, 'FRONTIER_POST_MODERATION_FLAG', 'true');
					// Email author on moderation comments
					if (isset($_POST['frontier_post_moderation_send_email']) && $_POST['frontier_post_moderation_send_email'] == "true")
						{
						$to      		= get_the_author_meta( 'email', $my_post->post_author );
						$subject 		= __("Moderator has commented your pending post", "frontier-post")." (".get_bloginfo( "name" ).")";
						$body    		= __("Moderator has commented your pending post", "frontier-post").": ".$my_post->post_title ." (".get_bloginfo( "name" ).")"."\r\n\r\n";
						$body    		.= "Comments: ".$_POST['frontier_post_moderation_new_text']."\r\n\r\n";
		
		
						if( !wp_mail($to, $subject, $body ) ) 
							frontier_post_set_msg(__("Message delivery failed - Recipient: (", "frontier-post").$to.")");
						}
					}
				}
			
			}
		
		
		
		
		//****************************************************************************************************
		// Action fires after add/update of post, and after taxonomies are updated
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