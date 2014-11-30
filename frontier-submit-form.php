<?php

function frontier_posting_form_submit()
	{
    global $current_user;
	//get_currentuserinfo();	
			
    if(isset($_POST['action'])&&$_POST['action']=="wpfrtp_save_post")
		{
		
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'frontier_add_edit_post' ) )
			{
			wp_die(__(" Security violation - Please contact your webmaster", "frontier-post"));
			}
		
        if($_POST['user_post_title'])
			{
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

			$saved_options 		= get_option('frontier_post_options', array() );
			$users_role 		= frontier_get_user_role();
			$category_type 		= $saved_options[$users_role]['category'] ? $saved_options[$users_role]['category'] : "multi"; 
			$default_category	= $saved_options[$users_role]['default_category'] ? $saved_options[$users_role]['default_category'] : get_option("default_category"); 
			
			if ( ($category_type == "multi") || ($category_type == "checkbox") )
				$tmp_categorymulti = $_POST['categorymulti'];
			
			if ($category_type == "single")
				{
				if(isset($_POST['cat']))
					{
					$tmp_category = $_POST['cat'];
					$tmp_categorymulti = array($tmp_category);
					}
				}
				
			if ((!isset($tmp_categorymulti)) || (count($tmp_categorymulti)==0))
				$tmp_categorymulti = array($default_category);
				
			$taglist = array();
			if (isset( $_POST['user_post_tag1']))
				array_push($taglist, $_POST['user_post_tag1']);
			if (isset( $_POST['user_post_tag2']))
				array_push($taglist, $_POST['user_post_tag2']);
			if (isset( $_POST['user_post_tag3']))
				array_push($taglist, $_POST['user_post_tag3']);
			
			$postid = $_REQUEST['postid'];
			if( empty($postid) )
				{
				$my_post = get_default_post_to_edit( "post", true );
				$postid = $my_post->ID;
				}
			
			$tmp_post = array(
                 'ID'				=> $postid,
				 'post_title' 		=> $tmp_title,
				 'post_status' 		=> $post_status,
                 'post_content' 	=> $tmp_content,				 
                 'post_category' 	=> $tmp_categorymulti,
				 'post_excerpt' 	=> $tmp_excerpt,
				);
			
			wp_update_post( $tmp_post );
			$my_post = get_post($postid);
			
			
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				wp_set_post_tags($postid, $taglist);
				
			
		
			} // end if user_post_title
				
		$concat= get_option("permalink_structure")?"?":"&";		
		
		$tmp_page_id 		= intval(isset($_POST['return_p_id']) ? $_POST['return_p_id'] : 0 );
		$tmp_cat_return_id 	= intval(isset($_POST['return_category_archive']) ? $_POST['return_category_archive'] : 0);
		
		//error_log("Return cat id:".$tmp_cat_return_id);
		
		// Check if return is to category page
		if ( $tmp_cat_return_id != 0 )
			{
			$tmp_link = get_category_link(intval($_POST['return_category_archive']));
			}
		else
			{
			if ( $tmp_page_id > 0 )
				$tmp_link	= get_permalink($tmp_page_id);
			else
				$tmp_link	= get_permalink(get_option('frontier_post_page_id'));
			}

		//error_log("Return link:".$tmp_link);
		
		$tmp_return = "location: ".$tmp_link;
		
		if (isset($_POST['user_post_preview']))
			{
			$tmp_return = "location: ".site_url()."/?p=".$postid."&preview=true";
			}
			
		if (isset($_POST['user_post_save']))
			{
			$tmp_return = "location: ".$tmp_link.$concat."task=edit&postid=".$postid;
			}
			
		if (isset($_POST['user_post_home']))
			{
			$tmp_return = "location: ".home_url();
			}
		header($tmp_return);
		exit;
				
        } // end isset post
	} // end function frontier_posting_form_submit


?>