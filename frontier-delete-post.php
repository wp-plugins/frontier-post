<?php

function frontier_delete_post()
	{
	if(isset($_REQUEST['task']))
		{
		$post_task = $_REQUEST['task'];
		}
	else
		{
		$post_task = "notaskset";
		}
    if($post_task == "delete" )
		{
		if($_REQUEST['postid'])
			{
			$cur_user 		= wp_get_current_user();
			$thispost		= get_post($_REQUEST['postid']);
			
			$post_author	= $thispost->post_author;
			
			//double check current user is equal to author (in case directly with param)
			if ( $cur_user->ID == $post_author )
				{
					//Move post to recycle bin
					wp_trash_post($_REQUEST['postid']);
				} 
			}
		}
	}



?>