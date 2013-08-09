<?php 

/*
Fix to retrieve posts with wrong status

*/

global $current_user, $wpdb, $r;
$tmp_sql 			=  " SELECT * 
						 FROM $wpdb->posts 
						 WHERE $wpdb->posts.post_status NOT IN ('auto-draft', 'draft', 'future', 'inherit','pending', 'publish', 'trash')" ;
						
$r 	= $wpdb->get_results($tmp_sql);
						
$concat= get_option("permalink_structure")?"?":"&";    
//set the permalink for the page itself
$frontier_permalink = get_permalink(get_option('frontier_post_page_id'));


			
	
if( $r )
	{
	
	$tmp_status_list = get_post_statuses( );

?>

<table class="frontier-list" id="user_post_list">
 
	<thead>
		<tr>
			<th><?php _e("Date", "frontier-post"); ?></th>
			<th><?php _e("Title", "frontier-post"); ?></th>
			<th><?php _e("Status", "frontier-post"); ?></th>
			<th><?php _e("Category", "frontier-post"); ?></th>
			<th><?php _e("Cmt", "frontier-post"); ?></th> <!--number of comments-->
			<th><?php _e("Action", "frontier-post"); ?></th>
		</tr>
	</thead> 
	<tbody>
	<?php 
	foreach ( $r as $post) 
		{
			
	?>
			<tr>
				<td><?php echo mysql2date('Y-m-d', $post->post_date); ?></td>
				<td>
				<a href="<?php echo post_permalink($post->ID);?>"><?php echo $post->post_title;?></a>
				</td>
				<td><?php  echo $post->post_status;  ?></td>
				<td><?php  
					// List categories
					$categories=get_the_category( $post->ID );
					$cnt = 0;
					foreach ($categories as $category) :
						$cnt = $cnt+1;
						if ($cnt > 1)
							echo "</br>".$category->cat_name; 
						else
							echo $category->cat_name; 
					endforeach;
				?></td>
				<td><?php  echo $post->comment_count;?></td>
				<td>
					<?php
						if (frontier_can_edit($post->post_date, $post->comment_count) == true)
							{
								?>
									<a href="<?php echo $frontier_permalink; ?><?php echo $concat;?>task=edit&postid=<?php echo $post->ID;?>">Edit</a>&nbsp;&nbsp;
								<?php
							}
												
						if (frontier_can_delete($post->post_date, $post->comment_count) == true)
							{
								?>
									<a href="#" onclick="if(confirm('<?php _e('Are you sure you want to delete this post?', 'frontier-post')?>')){location.href='<?php echo $frontier_permalink;?><?php echo $concat;?>task=delete&postid=<?php echo $post->ID;?>'}" >Delete</a>
								<?php
							}
						
						if ($post->post_status != "publish")
							{ 
							$tmp_post_link = site_url();
							$tmp_post_link = $tmp_post_link."/?p=".$post->ID."&preview=true"
							?>
							<a href="<?php echo $tmp_post_link;?>" target="_blank"><?php _e("Preview","frontier-post") ?></a>
							<?php		
							} 

					?>
					&nbsp;
				</td>
			</tr>
		<?php 
		} 
		?>
	</tbody>
</table>
<?php

	}
	
else
	{
		echo "</br><center>";
		_e('Sorry, No posts with wrong status', 'frontier-post');
		echo "</center><br></br>";
	} // end post count
	
//Re-instate $post for the page
wp_reset_postdata();

?>