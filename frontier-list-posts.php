<?php


function  frontier_user_post_list($frontier_post_shortcode_parms = array())
	{
	extract($frontier_post_shortcode_parms);
	 
	
	global $post;
	global $current_user;
	get_currentuserinfo();

	$tmp_p_id = get_the_id();
	
	
	$pagenum	= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
	$ppp		= (int) fp_get_option('fps_ppp',5);

	$args = array(
			'post_type' 		=> $frontier_list_post_types,
			'post_status' 		=> 'draft, pending, publish, private',
			'order'				=> 'DESC',
			'orderby' 			=> 'post_date', 
			'posts_per_page'    => $ppp,
			'paged'				=> $pagenum,
			);
	
	// add category from shortcode to limit posts
	if ( $frontier_list_cat_id > 0) 
		$args["cat"] = implode(",",$frontier_list_cat_id);

	
	//List all published posts
	if ( $frontier_list_all_posts == "true" )
		$args["post_status"] = "publish";
	else
		$args["author"] = $current_user->ID;
	
	
	$user_posts 	= new WP_Query( $args );

	include_once(frontier_load_form("frontier_post_form_list.php"));
		
	}  
?>