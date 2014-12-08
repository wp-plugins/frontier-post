<?php

	
	
	// Template exmple of using multiple template forms
	// Example to load a simple form if the user is on a mobile device
	//if ( wp_is_mobile() == true )
	if ( true )
		{
		include_once(frontier_load_form("frontier_form_simple.php"));
		}
	else
		{
		include_once(frontier_load_form("frontier_form_standard.php"));
		}
	
	
	// end form file
?>