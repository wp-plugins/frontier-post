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
	echo '<div id="frontier-post-list_form">';
	while ($user_posts->have_posts()) 
		{
		$user_posts->the_post();
		
		
		
		?>
			<fieldset class="frontier-new-list">
			
			<table class="frontier-new-list">
				<tr>
				
				<?php
				// show status if pending or draft
				if ($post->post_status == "pending" || $post->post_status == "draft")
					echo '<td class="frontier-new-list" id="frontier-post-new-list-status" colspan=2>'.__("Status", "frontier-post").': '.$post->post_status.'</td></tr><tr>';
				?>
				
				<td class="frontier-new-list" id="frontier-post-new-list-thumbnail">
					<?php the_post_thumbnail( array(50,50) ); ?> 
				</td>
				
				<td class="frontier-new-list" id="frontier-post-new-list-title">
					<h2 class="frontier-new-list"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				</td>
				
				</tr>
				
				<?php
				if ($frontier_list_form == "full_post")
					{
					$tmp_content = apply_filters( 'the_content', $post->post_content );
					$tmp_content = str_replace( ']]>', ']]&gt;', $tmp_content );
					echo '<tr><td class="frontier-new-list" id="frontier-post-new-list-excerpt" colspan=2>';
					echo $tmp_content;
					echo '</td></tr>';
					}
				if ($frontier_list_form == "excerpt")
					{
					$tmp_content = $post->post_excerpt;
					if (strlen(trim($tmp_content)) == 0)
						$tmp_content = wp_trim_words($post->post_content);
					echo '<tr><td class="frontier-new-list" id="frontier-post-new-list-excerpt" colspan=2>';
					echo $tmp_content;
					echo '</td></tr>';
					}
				?>
				
				<tr>
				<td class="frontier-new-list" id="frontier-post-new-list-info" colspan=2 >
					
					<?php
					echo frontier_post_edit_link($post->ID, $fp_show_icons);
					echo frontier_post_delete_link($post->ID, $fp_show_icons);
					echo frontier_post_preview_link($post->ID, $fp_show_icons);
					
					
					
					echo __("Status", "frontier-post").': '.( isset($tmp_status_list[$post->post_status]) ? $tmp_status_list[$post->post_status] : $post->post_status );
					echo $tmp_info_separator;
					echo __("Author", "frontier-post").': ';
					the_author();
					echo $tmp_info_separator; 
					printf( _x( '%s ago', '%s = human-readable time difference', 'frontier-post' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); 
					echo $tmp_info_separator; 
					echo frontier_get_icon('comments2').'&nbsp;'.intval($post->comment_count);
					echo $tmp_info_separator; 
					echo __("Categories", "frontier-post").': ';
					the_category(', '); 
					echo $tmp_info_separator; 
					echo __("Tags", "frontier-post").': ';
					the_tags(', '); 
					/*
					echo '<br>';
					echo fp_get_tax_values($post->ID); 
					*/
					?>
					
					
				</td>
				</tr>
			</table>	
			</fieldset>
		
		
		<?php
		//echo '<hr>';
		
		} // end while have posts 
	
	
	
	
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
	
	echo '</div>';
	} // end if have posts
else
	{
		echo "</br><center>";
		_e('Sorry, you do not have any posts (yet)', 'frontier-post');
		echo "</center><br></br>";
	} // end post count
	
//Re-instate $post for the page
wp_reset_postdata();

?>