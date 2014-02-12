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
        }
    else
		{
		if ( empty($thispost->ID) )
			$thispost = get_default_post_to_edit( "post", true );
		$thispost->post_author = $current_user->ID;
		$_REQUEST['task']="new";
		}
		
	$post_id = $thispost->ID;
	//$_REQUEST['this_post_id'] = $post_id;
	//error_log("post_id: ".$post_id);
		
	$frontier_task = $_REQUEST['task'] ? $_REQUEST['task'] :"?";
	
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
	/*
	if (isset($thispost->ID))
		{
		$post_id = $thispost->ID;
		}
	*/
	frontier_media_fix( $post_id );
	
	$user_can_edit_this_post = true;

	if ($thispost->post_author != $current_user->ID && (!current_user_can( 'administrator' )))
		$user_can_edit_this_post = false;
	
	if (($frontier_task == "new") && (!current_user_can( 'frontier_post_can_add' )))
		$user_can_edit_this_post = false;
	
	//build post status list based on current status and users capability
	$tmp_status_list = get_post_statuses( );
	$tmp_status_list = array_reverse($tmp_status_list);
	
	// Remove private status from array
	unset($tmp_status_list['private']);
	
	// Remove draft status from array if user is not allowed to use drafts
	if (!current_user_can('frontier_post_can_draft'))
	unset($tmp_status_list['draft']);
	
	
	$status_list 		= array();
	$tmp_post_status 	= $thispost->post_status ? $thispost->post_status : "unknown";
	
	$status_readonly = "";
	
	if ($tmp_post_status == "publish")
		{
		$status_readonly = "readonly";
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
	$editor_type 				= $saved_options[$users_role]['editor'] ? $saved_options[$users_role]['editor'] : "full"; 
	$frontier_post_mce_custom	= (get_option("frontier_post_mce_custom")) ? get_option("frontier_post_mce_custom") : "disable";
	$frontier_post_mce_button	= get_option("frontier_post_mce_button", array());
	
	$editor_layout = array('dfw' => false, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300 );
	
		
	if ($editor_type == "full" && $frontier_post_mce_custom == "true")
		{
		$tinymce_options = array(
			'theme_advanced_buttons1' 	=> ($frontier_post_mce_button[0] ? $frontier_post_mce_button[0] : ''),
			'theme_advanced_buttons2' 	=> ($frontier_post_mce_button[1] ? $frontier_post_mce_button[1] : ''),
			'theme_advanced_buttons3' 	=> ($frontier_post_mce_button[2] ? $frontier_post_mce_button[2] : ''),
			'theme_advanced_buttons4' 	=> ($frontier_post_mce_button[3] ? $frontier_post_mce_button[3] : '')
			);
	
		$tmp = array('tinymce' => $tinymce_options);
		$editor_layout = array_merge($editor_layout, $tmp);
		}
	
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
		
	
	// Setup category	
	$category_type 				= $saved_options[$users_role]['category'] ? $saved_options[$users_role]['category'] : "multi"; 
	$default_category			= $saved_options[$users_role]['default_category'] ? $saved_options[$users_role]['default_category'] : get_option("default_category"); 
	$frontier_post_excl_cats	= get_option("frontier_post_excl_cats", '');
	$parent_category = isset($_REQUEST['parent_cat']) ? $_REQUEST['parent_cat'] : "0";
	
	//echo "Parent cat: ".$parent_category."<br>";
	
	
	// Build list of categories (3 levels)
	if ($category_type == "multi")
		{
		
		$cats_selected	= $thispost->post_category;
		if (empty($cats_selected[0]))
			$cats_selected[0] = $default_category;
			
		$catlist 		= array();
		foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $parent_category, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)) as $category1) :
			$tmp = Array('cat_ID' => $category1->cat_ID, 'cat_name' => $category1->cat_name);
			array_push($catlist, $tmp);
			foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $category1->cat_ID, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)) as $category2) :
				$tmp = Array('cat_ID' => $category2->cat_ID, 'cat_name' => "-- ".$category2->cat_name);
				array_push($catlist, $tmp);
				foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $category2->cat_ID, 'exclude' => $frontier_post_excl_cats, 'show_count' => true)) as $category3) :
					$tmp = Array('cat_ID' => $category3->cat_ID, 'cat_name' => "-- -- ".$category3->cat_name);
					array_push($catlist, $tmp);
				endforeach; // Level 3
			endforeach; // Level 2
		endforeach; //Level 1
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