<?php


function  frontier_user_post_list($tmp_cat_id = 0, $tmp_page_id = 0, $tmp_button_txt = "false", $tmp_list_all_posts = "false")
	{
		
		
		//$tmp_page_id = get_permalink();
		$_REQUEST['frontier_return_page_id']	= $tmp_page_id; 
		$_REQUEST['frontier_return_text']		= $tmp_button_txt;
		
		// if no specifc return page is set, set the page to the current page
		if ( $tmp_page_id == 0 )
			 $tmp_page_id = get_the_ID();
		
		/*
		echo "<br>";
		echo "cat id: ".$tmp_cat_id;	
		
		
		echo "<br>";
		echo "return id: ".$tmp_page_id;	
		echo "<br>";
		echo "page id: ".get_the_ID();
		echo "<br>";
		*/
		
		global $post;
		global $current_user;
		get_currentuserinfo();
		
		$pagenum	= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
		$ppp		= get_option('frontier_post_ppp') ? get_option('frontier_post_ppp') : 5;
	
		$args = array(
				'post_type' 		=> 'post',
				'post_status' 		=> 'draft, pending, publish, private',
				'order'				=> 'DESC',
				'orderby' 			=> 'post_date', 
				'posts_per_page'    => $ppp,
				'paged'				=> $pagenum,
				);
		
		// add category from shortcode to limit posts
		if ( isset($tmp_cat_id) && ($tmp_cat_id > 0) )
			$args["cat"] = $tmp_cat_id;
		
		//List all published posts
		if ( $tmp_list_all_posts == "true" )
			$args["post_status"] = "publish";
		else
			$args["author"] = $current_user->ID;
		
		
		
		$user_posts 	= new WP_Query( $args );
		//print_r("Last SQL-Query: {$user_posts->request}");
	
		include_once(frontier_load_form("frontier_list.php"));
		
	}  
?>