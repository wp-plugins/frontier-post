<?php
/*
Test page for pagination
*/

function  wpfrtp_pagination_test()
	{

		global $post;
		$pagenum 			= isset( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
		$ppp	 			= get_option('frontier_ppp') ? get_option('frontier_ppp') : 10;
	
		$args = 	array(
				'post_type' 		=> 'post',
				'post_status' 		=> 'publish',
				'orderby' 			=> 'post_date', 
				'order'				=> 'DESC',
				'posts_per_page'    => $ppp,
				'paged'				=> $pagenum,
				);
		
		//$user_posts=get_posts($args);
		$user_posts 	= new WP_Query( $args );
		if ( $user_posts->have_posts() ) 
			{ 
		?>
	
			<table class="frontier-list" id="user_post_list">
			<caption>Test of pagination</caption>
			<thead>
			<tr>
				<th>Date</th>
				<th>Title</th>
				<th>Category</th>	
			</tr>
			</thead> 
			<tbody>
			<?php while ($user_posts->have_posts()) 
				{
					$user_posts->the_post();
			?>
					<tr>
						<td><?php echo mysql2date('Y-m-d', $post->post_date); ?></td>
						<td><a href="<?php echo post_permalink($post->ID);?>"><?php echo $post->post_title;?></a></td>
						<td><?php $category=get_the_category( $post->ID ); echo $category[0]->cat_name;?></td>
					</tr>
			<?php 
				} 
			?>
			</tbody>
			</table>

	<?php
			$pagination = paginate_links( array(
                    'base' => add_query_arg( 'pagenum', '%#%' ),
                    'format' => '',
                    'prev_text' => __( '&laquo;', 'frontier-post' ),
                    'next_text' => __( '&raquo;', 'frontier-post' ),
                    'total' => $user_posts->max_num_pages,
                    'current' => $pagenum
                        ) );

			if ( $pagination ) 
				{
					echo $pagination;
				}
			
		
		} // end if have post
	}  // end function
 

?>