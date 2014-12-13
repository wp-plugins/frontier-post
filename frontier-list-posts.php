<?php


function  frontier_user_post_list($frontier_post_shortcode_parms = array())
	{
	extract($frontier_post_shortcode_parms);
	
	global $post;
	global $current_user;
	get_currentuserinfo();

	
	fp_log("fp cat id List: ".($frontier_cat_id ? $frontier_cat_id : "Unknown"));
	//fp_log("fp cat id List test: ".(isset($fp_cat_id) ? $fp_cat_id : "Unknown"));		
	//fp_log($frontier_post_shortcode_parms);		
	
	$pagenum	= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
	$ppp		= (int) get_option('frontier_post_ppp',5);

	$args = array(
			'post_type' 		=> 'post',
			'post_status' 		=> 'draft, pending, publish, private',
			'order'				=> 'DESC',
			'orderby' 			=> 'post_date', 
			'posts_per_page'    => $ppp,
			'paged'				=> $pagenum,
			);
	
	// add category from shortcode to limit posts
	if ( $frontier_list_cat_id > 0) 
		$args["cat"] = $frontier_list_cat_id;
	
	//List all published posts
	if ( $frontier_list_all_posts == "true" )
		$args["post_status"] = "publish";
	else
		$args["author"] = $current_user->ID;
	
	
	
	$user_posts 	= new WP_Query( $args );
	//print_r("Last SQL-Query: {$user_posts->request}");

	include_once(frontier_load_form("frontier_list.php"));
		
	}  
?>