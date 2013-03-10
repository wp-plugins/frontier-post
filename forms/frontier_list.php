<?php 

$concat= get_option("permalink_structure")?"?":"&";    
//set the permalink for the page itself
$frontpost_permalink = get_permalink();
?>

<table class="frontier-menu" >
	<tr class="frontier-menu">
		
		<th class="frontier-menu" >
			&nbsp;
		</th>
		<th class="frontier-menu" >
			<a href='<?php the_permalink();?><?php echo $concat;?>task=new'><?php _e("Add new post", "frontier-post"); ?></a>
		</th>
		<th class="frontier-menu" >
			&nbsp;
		</th>
	</tr>
</table>
</br>
<?php


?>

<table class="frontier-list" id="user_post_list">
 
	<thead>
		<tr>
			<th><?php _e("Date", "frontier-post"); ?></th>
			<th><?php _e("Title", "frontier-post"); ?></th>
			<th><?php _e("Category", "frontier-post"); ?></th>
			<th><?php _e("Action", "frontier-post"); ?></th>
		</tr>
	</thead> 
	<tbody>
		<?php foreach( $user_posts as $post ) : setup_postdata($post);?>
			<tr>
				<?php
				
				?>
				<td><?php echo mysql2date('Y-m-d', $post->post_date); ?></td>
				<td>
					<a href="<?php echo post_permalink($post->ID);?>"><?php echo $post->post_title;?></a>
				</td>
				
				<td>
					<?php  $category=get_the_category( $post->ID ); echo $category[0]->cat_name;?>
				</td>
				
					
				<td>
					<?php
						if (frontier_can_edit($post->post_date) == true)
							{
								?>
									<a href="<?php echo $frontpost_permalink; ?><?php echo $concat;?>task=edit&postid=<?php echo $post->ID;?>">Edit</a>
								<?php
							}
												
						if (frontier_can_delete($post->post_date) == true)
							{
								?>
									&nbsp;&nbsp;<a href="#" onclick="if(confirm('Are you sure?')){location.href='<?php echo $frontpost_permalink;?><?php echo $concat;?>task=delete&postid=<?php echo $post->ID;?>'}" >Delete</a>
								<?php
							}
					?>
					&nbsp;
				</td>
				
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
