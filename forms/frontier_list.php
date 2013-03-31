<?php 

$concat= get_option("permalink_structure")?"?":"&";    
//set the permalink for the page itself
$frontier_permalink = get_permalink();

//$role =	frontier_get_user_role();
//echo "Role: ".$role."</br>"

//print_r("allow edit with comments: ".get_option("frontier_edit_w_comments")."</br>");
//print_r("allow delete with comments: ".get_option("frontier_del_w_comments")."</br>");

if (frontier_can_add() )
	{
?>

	<table class="frontier-menu" >
		<tr class="frontier-menu">
			<th class="frontier-menu" >&nbsp;</th>
			<th class="frontier-menu" ><a href='<?php echo frontier_post_add_link() ?>'><?php _e("Create New Post", "frontier-post"); ?></a></th>
			<th class="frontier-menu" >&nbsp;</th>
		</tr>
	</table>
	</br>
<?php
	
	} // if can_add

if( $user_posts->found_posts > 0 )
	{


?>

<table class="frontier-list" id="user_post_list">
 
	<thead>
		<tr>
			<th><?php _e("Date", "frontier-post"); ?></th>
			<th><?php _e("Title", "frontier-post"); ?></th>
			<th><?php _e("Category", "frontier-post"); ?></th>
			<th><?php _e("Cmt", "frontier-post"); ?></th> <!--number of comments-->
			<th><?php _e("Action", "frontier-post"); ?></th>
		</tr>
	</thead> 
	<tbody>
	<?php 
	while ($user_posts->have_posts()) 
		{
			$user_posts->the_post();
	?>
			<tr>
				<td><?php echo mysql2date('Y-m-d', $post->post_date); ?></td>
				<td><a href="<?php echo post_permalink($post->ID);?>"><?php echo $post->post_title;?></a></td>
				
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
									<a href="#" onclick="if(confirm('Are you sure?')){location.href='<?php echo $frontier_permalink;?><?php echo $concat;?>task=delete&postid=<?php echo $post->ID;?>'}" >Delete</a>
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

	$pagination = paginate_links( 
			array(
				'base' => add_query_arg( 'pagenum', '%#%' ),
				'format' => '',
				'prev_text' => __( '&laquo;', 'frontier-post' ),
				'next_text' => __( '&raquo;', 'frontier-post' ),
				'total' => $user_posts->max_num_pages,
				'current' => $pagenum
				) 
			);

	if ( $pagination ) 
		{
			echo $pagination;
		}
	}
else
	{
		echo "</br><center>";
		_e('Sorry, you do not have any posts (yet)', 'frontier-post');
		echo "</center><br></br>";
	} // end post count
?>