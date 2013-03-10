<?php
/*
Validations for Frontier Post plugin
*/

function frontier_post_age($tmp_post_date)
	{
	return round((time() - strtotime($tmp_post_date))/(24*60*60));					
	}

function frontier_can_edit($tmp_post_date)
	{
	if (frontier_post_age($tmp_post_date) > get_option('frontier_post_edit_max_age'))
		{
		return false;
		}
	else
		{
		return true;
		}
	}	

function frontier_can_delete($tmp_post_date)
	{
	if (frontier_post_age($tmp_post_date) > get_option('frontier_post_delete_max_age'))
		{
		return false;
		}
	else
		{
		return true;
		}
	}	



?>