<?php


function  frontier_user_post_list($frontier_post_shortcode_parms = array())
	{
	extract($frontier_post_shortcode_parms);
	 
	//$tmp_x = frontier_post_get_settings();
	//error_log(print_r($tmp_x, true));
	
	global $post;
	global $current_user;
	get_currentuserinfo();

	$tmp_p_id = get_the_id();
	
	//fp_log("fp cat id List: ".($frontier_cat_id ? $frontier_cat_id : "Unknown"));
	//fp_log("fp cat id List test: ".(isset($fp_cat_id) ? $fp_cat_id : "Unknown"));		
	//fp_log($frontier_post_shortcode_parms);		
	
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
	
	//("List_posts");
	//error_log(print_r($args, true));
	
	$user_posts 	= new WP_Query( $args );
	//error_log(print_r("Last SQL-Query: {$user_posts->request}", true));

	include_once(frontier_load_form("frontier_post_form_list.php"));
		
	}  
?>