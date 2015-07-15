<?php

function frontier_prepare_delete_post($frontier_post_shortcode_parms = array())
	{
	extract($frontier_post_shortcode_parms);
	
	$frontier_permalink = get_permalink();
	$concat				= get_option("permalink_structure")?"?":"&";
	
	//$post_task 		= isset($_GET['task']) ? $_GET['task'] : "notaskset";	
	//$post_action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : "Unknown";		
    
    if (isset($_POST['task']))
				{
				$post_task 	= $_POST['task'];
				}
			else
				{
				if (isset($_GET['task']))
					{
					$post_task 	= $_GET['task'];
					}
				else 
					{
					$post_task 	="notaskset";
					}
				}
		
	$post_action 	= isset($_POST['action']) ? $_POST['action'] : "Unknown";
    
	if($post_task == "delete" )
		{
		if($_REQUEST['postid'])
			{
			$thispost		= get_post($_REQUEST['postid']);
			
			$post_author	= $thispost->post_author;
			
			//double check current user is equal to author (in case directly with param)
			if ( frontier_can_delete($thispost) == true )
				{
				
				echo '<div id="frontier-post-alert">'.__("Delete", "frontier-post").':&nbsp;'.fp_get_posttype_label_singular($thispost->post_type).'</div>';
				echo '<br><br>';
				?>
					<div class="frontier_post_delete_form"> 
					<table>
					
					<form action="<?php echo $frontier_permalink; ?>" method="post" name="frontier_delete_post" id="frontier_delete_post" enctype="multipart/form-data" >
						<input type="hidden" name="action" value="wpfrtp_delete_post"> 
						<input type="hidden" name="task" value="delete">
						<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
						<?php wp_nonce_field( 'frontier_delete_post', 'frontier_delete_post_'.$thispost->ID ); ?>
		
						<tr>
						</tr><tr>
						<td><center>
						<button class="button" type="submit" name="submit_delete" 		id="submit_delete" 	value="deletego"><?php _e("Delete", "frontier-post"); ?></button>
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

function frontier_execute_delete_post($frontier_post_shortcode_parms = array())
	{
	extract($frontier_post_shortcode_parms);
	
	//$post_task 		= isset($_GET['task']) ? $_GET['task'] : "notaskset";	
	//$post_action 	= isset($_REQUEST['action']) ? $_REQUEST['action'] : "Unknown";
	if (isset($_POST['task']))
				{
				$post_task 	= $_POST['task'];
				}
			else
				{
				if (isset($_GET['task']))
					{
					$post_task 	= $_GET['task'];
					}
				else 
					{
					$post_task 	="notaskset";
					}
				}
		
	$post_action 	= isset($_POST['action']) ? $_POST['action'] : "Unknown";
    
	$submit_delete 	= isset($_POST['submit_delete']) ? $_POST['submit_delete'] : "Unknown";
	$postid			= isset($_POST['postid']) ? $_POST['postid'] : 0;
	
    if( ($post_action == "wpfrtp_delete_post") && ($postid !=0) )
		{
		if ( !wp_verify_nonce( $_POST['frontier_delete_post_'.$_POST['postid']], 'frontier_delete_post' ) )
			{
			wp_die(__("Security violation (Nonce check) - Please contact your Wordpress administrator", "frontier-post"));
			}
		
		
		
		$thispost		= get_post($postid);	
		//double check current user is equal to author (in case directly with param)
		if ( ($submit_delete = "deletego") && (frontier_can_delete($thispost) == true) )
			{
			//Move post to recycle bin
			$tmp_title = $thispost->post_title;
			wp_trash_post($_REQUEST['postid']);
			frontier_post_set_msg(__("Post deleted", "frontier-post").": ".$tmp_title);
			frontier_user_post_list($frontier_post_shortcode_parms);
			}
		}
	}



?>