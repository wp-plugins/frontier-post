<?php


function  frontier_user_post_list()
	{
	
		global $post;
		global $current_user;
		get_currentuserinfo();
		
		$pagenum	= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
		$ppp		= get_option('frontier_post_ppp') ? get_option('frontier_post_ppp') : 5;
	
		$args = array(
				'post_type' 		=> 'post',
				'post_status' 		=> 'draft, pending, publish',
				'author'			=>	$current_user->ID,
				'order'				=> 'DESC',
				'orderby' 			=> 'post_date', 
				'posts_per_page'    => $ppp,
				'paged'				=> $pagenum,
				);
		
		$user_posts 	= new WP_Query( $args );
		//print_r("Last SQL-Query: {$user_posts->request}");
		//include("forms/frontier_list.php");
		include_once(frontier_load_form("frontier_list.php"));
		
	}  
?>