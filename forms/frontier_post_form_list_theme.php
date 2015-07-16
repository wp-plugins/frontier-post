<?php 

// list of users post based on current theme settings
	
$concat= get_option("permalink_structure")?"?":"&";    
//set the permalink for the page itself
$frontier_permalink = get_permalink();

$tmp_status_list = get_post_statuses( );

$tmp_info_separator = " | ";

//Display before text from shortcode
if ( strlen($frontier_list_text_before) > 1 )
	echo '<div id="frontier_list_text_before">'.$frontier_list_text_before.'</div>';

// Dummy translation of ago for human readable time
$crap = __("ago", "frontier-post");


//Display message
frontier_post_output_msg();


if (frontier_can_add() && !fp_get_option_bool("fps_hide_add_on_list"))
	{
	if (strlen(trim($frontier_add_link_text))>0)
		$tmp_add_text = $frontier_add_link_text;
	else
		$tmp_add_text = __("Create New", "frontier-post")." ".fp_get_posttype_label_singular($frontier_add_post_type);
		
	?>
	<fieldset class="frontier-new-menu">
		<a id="frontier-post-add-new-link" href='<?php echo frontier_post_add_link($tmp_p_id) ?>'><?php echo $tmp_add_text; ?></a>
	</fieldset>

	<?php
	
	} // if can_add



if( $user_posts->found_posts > 0 )
	{
	while ($user_posts->have_posts()) 
		{
		$user_posts->the_post();
		
		$fp_templates = array("content.php");
								
								
		locate_template(array($fp_templates), true);
		
		} 
	
	
	
	
	$pagination = paginate_links( 
			array(
				'base' => add_query_arg( 'pagenum', '%#%'),
				'format' => '',
				'prev_text' => __( '&laquo;', 'frontier-post' ),
				'next_text' => __( '&raquo;', 'frontier-post' ),
				'total' => $user_posts->max_num_pages,
				'current' => $pagenum,
				'add_args' => false  //due to wp 4.1 bug (trac ticket 30831)
				) 
			);

	if ( $pagination ) 
		{
			echo $pagination;
		}
	if ( $frontier_list_all_posts != "true" )
		echo "</br>".__("Number of posts already created by you: ", "frontier-post").$user_posts->found_posts."</br>";
	}
else
	{
		echo "</br><center>";
		_e('Sorry, you do not have any posts (yet)', 'frontier-post');
		echo "</center><br></br>";
	} // end post count
	
//Re-instate $post for the page
wp_reset_postdata();

?>