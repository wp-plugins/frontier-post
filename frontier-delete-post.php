<?php

function frontier_prepare_delete_post()
	{
	$post_task 		= isset($_GET['task']) ? $_GET['task'] : "notaskset";	
	$post_action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : "Unknown";		
    
	if($post_task == "delete" )
		{
		if($_REQUEST['postid'])
			{
			$thispost		= get_post($_REQUEST['postid']);
			
			$post_author	= $thispost->post_author;
			
			//double check current user is equal to author (in case directly with param)
			if ( frontier_can_delete($thispost) == true )
				{
				
				?>
					<div class="frontier_post_delete_form"> 
					<table>
					
					<form action="" method="post" name="frontier_delete_post" id="frontier_delete_post" enctype="multipart/form-data" >
						<input type="hidden" name="action" value="wpfrtp_delete_post"> 
						<input type="hidden" name="task" value="delete">
						<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
						<?php wp_nonce_field( 'frontier_delete_post' ); ?>
						
						<tr><td>
						<center>
						<button class="button" type="submit" name="submit_delete" 		id="submit_delete" 	value="deletego"><?php _e("Delete post", "frontier-post"); ?></button>
						<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
						</center>
						</td></tr>
					</form>	
					</table>	
					
					<hr>
					<?php 
					echo "<table>";
					echo "<tr>";
					
					
					echo "<td><h1>".$thispost->post_title."</h1></td>"; 
					
					
					
					echo "</tr><tr><td>";
					$content = $thispost->post_content;
					//$content = apply_filters( 'the_content', $content);
					$content = str_replace( ']]>', ']]&gt;', $content );
					echo $content;
					echo "</td>";
					
					// echo $thispost->post_content; 
					
					echo "</tr></table>";
					?>
					</div>
					
				<?php
						
				
				}

			}
	
		}
	
	} // end prepare delete	

function frontier_execute_delete_post()
	{
	if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'frontier_delete_post' ) )
		{
		wp_die(__(" Security violation - Please contact your webmaster", "frontier-post"));
		}
		
	$post_task 		= isset($_GET['task']) ? $_GET['task'] : "notaskset";	
	$post_action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : "Unknown";
	$submit_delete 	= isset($_POST['submit_delete']) ? $_POST['submit_delete'] : "Unknown";
	$postid			= isset($_REQUEST['postid']) ? $_REQUEST['postid'] : 0;
	
    if( ($post_action == "wpfrtp_delete_post") && ($postid !=0) )
		{
		$thispost		= get_post($postid);	
		//double check current user is equal to author (in case directly with param)
		if ( ($submit_delete = "deletego") && (frontier_can_delete($thispost) == true) )
			{
			//Move post to recycle bin
			$tmp_title = $thispost->post_title;
			wp_trash_post($_REQUEST['postid']);
			frontier_post_set_msg(__("Post deleted", "frontier-post").": ".$tmp_title);
			}
		}
	}



?>