<?php

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
			
			// test checkbox category
			//$zzz = $_POST['check_list'];
			//error_log("Checlist");
			//error_log(print_r($zzz, true));
			 
			
			if ( ($category_type == "multi") || ($category_type == "checkbox") )
				{
				//$tmp_categorymulti = $_POST['check_list'];
				$tmp_categorymulti = $_POST['categorymulti'];
				}
			if ($category_type == "single")
				{
				if(isset($_POST['cat']))
					{
					$tmp_category = $_POST['cat'];
					//error_log("Category from form: ".$tmp_category);
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
			
			
            $my_post = array(
                 'post_title' 		=> $tmp_title,
				 'post_status' 		=> $post_status,
                 'post_content' 	=> $tmp_content,				 
                 'post_category' 	=> $tmp_categorymulti,
				 'post_excerpt' 	=> $tmp_excerpt,
				);
				
			$postid= $_REQUEST['postid'];
			if( empty($postid) )
				{
                // Insert the post into the database
                $postid = wp_insert_post( $my_post );
				}
			else
				{
				// update the post into the database   
				$my_post['ID']=$postid;
				wp_update_post( $my_post ); 
				}
			
			
			// Set tags
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				wp_set_post_tags($postid, $taglist);
				
			
	
			$upload_dir = wp_upload_dir();
			
			if(isset( $_POST['filename'] ))
				{
				
				$filenames= $_POST['filename'];
				
				// Attach the files uploaded to the post
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
						if (!has_post_thumbnail( $postid ))
							set_post_thumbnail( $postid, $attach_id );
						}
					}    
				}
/*
			//If no Featured Image (Thumbnail)
			if (!has_post_thumbnail($postid))  
				{
				//, set the first image as featured image 
				$attached_image = get_children( array("post_parent" => $postid, "post_type" => "attachment", "post_mime_type" => "image", "numberposts" => 1) );
				if ($attached_image) 
					{
					foreach ($attached_image as $attachment_id => $attachment) 
						{
						set_post_thumbnail($postid, $attachment_id);
						}
					}
				else
					{
					// If no image linked to the postgGet image ids (images in the content from the image gallery) and set the first as featured image
					$inlineImages = array(); 
					preg_match_all( '/wp-image-([^"]*)"/i', $tmp_content, $inlineImages ) ;
					if ( count($inlineImages>0) )
						set_post_thumbnail($postid, $inlineImages [1][0]);
					}
				}
				
				// Testing:
				$used_images 	= array(); 
				$used_image_ids	= array();
				preg_match_all( '/wp-image-([^"]*)"/i', $tmp_content, $used_images ) ;
				if ( count($used_images [1])>0 )
					{
					foreach ($used_images [1] as $tmp_image_id)
						{
						// remove trailing /
						$tmp_image_id = substr($tmp_image_id, 0, -1);
						array_unshift($used_image_ids, $tmp_image_id);
						}
					}
				error_log(print_r($used_images, true));
				error_log(print_r($used_image_ids, true));
*/				
				
			}
			
		$concat= get_option("permalink_structure")?"?":"&";		
		
		if (isset($_POST['user_post_preview']))
			{
			header("location: ".site_url()."/?p=".$postid."&preview=true");
			die();
			}
		else
			{
			if (isset($_POST['user_post_save']))
				{
				$hdrloc = "location: ".get_permalink(get_option('frontier_post_page_id')).$concat."task=edit&postid=".$postid;
				//error_log("Header: ".$hdrloc ? $hdrloc : "?");
				header($hdrloc);
				die();
				}
				else
				{
				header("location: ".get_permalink(get_option('frontier_post_page_id')));
				die();
				}
			}
		
        }
	}


?>