<?php

function frontier_posting_form_submit($frontier_post_shortcode_parms = array())
	{
    extract($frontier_post_shortcode_parms);		
    
	fp_log("fp cat id Submit: ".($frontier_cat_id ? $frontier_cat_id : "Unknown"));
	
	//$tmp_txt = isset($_GET['frontier_new_cat_widget']) ? "true" : "false";
	fp_log("From widget (submit) ?: ".(isset($_GET['frontier_new_cat_widget']) ? "true" : "false"));
	
	if(isset($_POST['action'])&&$_POST['action']=="wpfrtp_save_post")
		{
		
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'frontier_add_edit_post' ) )
			{
			wp_die(__(" Security violation - Please contact your webmaster", "frontier-post"));
			}
		
        if($_POST['user_post_title'])
			{
			
			if ( isset($_REQUEST['task']) && ($_REQUEST['task'] == "new") )
				$tmp_task_new = true;
			else	
				$tmp_task_new = false;
				
			fp_log("New post ? : ".$tmp_task_new);
			
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
			
			$fp_options 	= get_option('frontier_post_options', array() );
			$users_role 	= frontier_get_user_role();
			
			//****************************************************************************************************
			// Manage Categories
			//****************************************************************************************************
			$category_type 		= $fp_options[$users_role]['category'] ? $fp_options[$users_role]['category'] : "multi"; 
			$default_category	= $fp_options[$users_role]['default_category'] ? $fp_options[$users_role]['default_category'] : get_option("default_category"); 
			
			if ( ($category_type == "multi") || ($category_type == "checkbox") )
				$tmp_categorymulti = ( isset($_POST['categorymulti']) ? $_POST['categorymulti'] : array() );
			
			if ($category_type == "single")
				{
				if(isset($_POST['cat']))
					{
					$tmp_category = $_POST['cat'];
					$tmp_categorymulti = array($tmp_category);
					}
				}
			
			// if no category returned from entry form, check for hidden field, if this is empty set default category 
			if ((!isset($tmp_categorymulti)) || (count($tmp_categorymulti)==0))
				{
				$tmp_categorymulti = ( isset($_POST['post_categories']) ? explode(',', $_POST['post_categories']) : array());
				$tmp_categorymulti = ((count($tmp_categorymulti) > 0) ? $tmp_categorymulti : array($default_category));
				}
			
			
			//****************************************************************************************************
			// Update post
			//****************************************************************************************************
			
			
			$tmp_post = array(
            	 'post_title' 		=> $tmp_title,
				 'post_status' 		=> $post_status,
                 'post_content' 	=> $tmp_content,				 
                 'post_category' 	=> $tmp_categorymulti,
				 'post_excerpt' 	=> $tmp_excerpt
				);
			
			
			$postid = $_REQUEST['postid'];
			
			if( empty($postid) )
				{
				$tmp_post = array_merge($tmp_post, array('post_type' => 'post'));
				$postid = wp_insert_post( $tmp_post );
				}
			else
				{
				$tmp_post = array_merge($tmp_post, array('ID' => $postid));
				wp_update_post( $tmp_post );
				}
			
			//****************************************************************************************************
			// Tags
			//****************************************************************************************************
			
			$taglist = array();
			if (isset( $_POST['user_post_tag1']))
				array_push($taglist, $_POST['user_post_tag1']);
			if (isset( $_POST['user_post_tag2']))
				array_push($taglist, $_POST['user_post_tag2']);
			if (isset( $_POST['user_post_tag3']))
				array_push($taglist, $_POST['user_post_tag3']);
			
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				wp_set_post_tags($postid, $taglist);

			if ( $tmp_task_new == true )
				frontier_post_set_msg(__("Post added", "frontier-post").": ".$tmp_title);
			else	
				frontier_post_set_msg(__("Post updated", "frontier-post").": ".$tmp_title);
			
			$my_post = get_post($postid);
			
			} // end if user_post_title
		
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
				
			fp_log("Frontier_mode: ".$frontier_mode);
			fp_log("return: ".$tmp_return);
	
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
		
		
        } // end isset post
	} // end function frontier_posting_form_submit


?>