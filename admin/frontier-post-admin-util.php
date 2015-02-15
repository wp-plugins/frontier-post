<?php
/*
Admin Utilities for Frontier Post plugin
*/



// Load default values for new options (inserts settings that doesnt exists, does not update existing)
function fp_post_set_defaults()
	{
	include(FRONTIER_POST_DIR.'/include/frontier_post_defaults.php');	
	
	$fps_save_general_options 	= frontier_post_get_settings();
	$tmp_option_list 			= array_keys($fps_general_defaults);
		
	foreach($tmp_option_list as $tmp_option_name)
		{
		if ( !key_exists($tmp_option_name, $fps_save_general_options) )
			$fps_save_general_options[$tmp_option_name] = $fps_general_defaults[$tmp_option_name];			
		}
	$fps_save_general_options['fps_frontier_post_version'] 	= FRONTIER_POST_VERSION;				
	update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_save_general_options);
	}

function frontier_post_set_cap()
		{
		
		include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
		

		$fps_saved_capabilities = frontier_post_get_capabilities();
		
		// Reinstate roles
		$fps_roles	= new WP_Roles();
		$role_list 	= $fps_roles->get_names();
		
		foreach( $role_list as $key => $item ) 
			{
			$xrole 			= get_role($key);
			
			$tmp_caplist 	= $fps_saved_capabilities[$key];
			
			
			foreach($tmp_caplist as $tmp_cap => $tmp_value)
				{
				$fps_cap_name = $tmp_cap;
				// Check that the name is a capability (not editor or category) 
				if ( array_key_exists($fps_cap_name, $fp_capability_list) == true )
					{
					
					if ( $tmp_value == "true" )
						$xrole->add_cap( $tmp_cap );
					else
						$xrole->remove_cap( $tmp_cap );
						
					$xrole->remove_cap( 'frontier_post_'.$tmp_cap );
					}
				else
					{
					
					}
				}// end tmp_caplist
				
				
			} // end role_list		
					
		
		} //end frontier_post_set_cap() funtion 

//***************************************************************************
//* Functions for admin menu html output
//***************************************************************************


// generates html output for checkbox field
function fps_html_field($tmp_name, $tmp_field_type = 'text', $tmp_option_values = array(), $tmp_echo_output = false, $tmp_colspan = "1", $tmp_list = array())
	{
	$tmp_output = "";
	$tmp_value	= !empty($tmp_option_values[$tmp_name]) ? $tmp_option_values[$tmp_name] : "";
	
	switch( $tmp_field_type )
		{
        case 'checkbox':
			$tmp_output = '<td colspan="'.$tmp_colspan.'"><center>'.fps_checkbox_field($tmp_name, $tmp_value).'</center></td>';
			break;
		
		case 'text':
			$tmp_output = '<td colspan="'.$tmp_colspan.'">'.fps_text_field($tmp_name, $tmp_value, 0).'</td>';
			break;
		
		case 'text100':
			$tmp_output = '<td colspan="'.$tmp_colspan.'">'.fps_text_field($tmp_name, $tmp_value, 100).'</td>';
			break;
		
		case 'select':
			$tmp_output = '<td colspan="'.$tmp_colspan.'">'.fps_select_field($tmp_name, $tmp_value, $tmp_list).'</td>';
			break;
			
		default:
			$tmp_output = '<td colspan="'.$tmp_colspan.'">'.fps_text_field($tmp_name, $tmp_value, 0).'</td>';
			break;
		}
		
	if ( $tmp_echo_output == true )
		echo $tmp_output;
	else
		return $tmp_output;
	
	}


// generates html output for checkbox field
function fps_checkbox_field($tmp_name, $tmp_current_value)
	{
	$tmp_html = '<input type="checkbox" name="'.$tmp_name.'" id="'.$tmp_name.'"  value="true"';
	if  ( $tmp_current_value == "true" )
		$tmp_html = $tmp_html.' checked >';
	else	
		$tmp_html = $tmp_html.'>';
	
	return $tmp_html;
	}

// generates html output for checkbox field
function fps_text_field($tmp_name, $tmp_current_value, $tmp_size = 0)
	{
	if ( $tmp_size > 0 )
		 $tmp_size_txt = ' size="100" ';
	else	 
		 $tmp_size_txt = '';
	
	
	$tmp_html = '<input type="text" name="'.$tmp_name.'" id="'.$tmp_name.'" value="'.$tmp_current_value.'" '.$tmp_size_txt.'>';
	
	return $tmp_html;
	}

function fps_select_field($tmp_name, $tmp_current_value, $tmp_list)
	{
	$tmp_html = '<select name="'.$tmp_name.'" id="'.$tmp_name.'" >';
	foreach($tmp_list as $key => $value) :    
		$tmp_html = $tmp_html.'<option value="'.$key.'"';
		if ( $key == $tmp_current_value )
			$tmp_html = $tmp_html.' selected="selected"';
		
		$tmp_html = $tmp_html.'>'.$value.'</option>';	
	endforeach;
	$tmp_html = $tmp_html.'</select>';
	
	return $tmp_html;
	}

Function fps_checkbox_select_field($tmp_name, $tmp_current_value, $tmp_list)
	{
	$tmp_html = '';
	if ( !is_array($tmp_current_value) )
		$tmp_current_value = array($tmp_current_value);
	
	
	foreach($tmp_list as $key => $value) :  
		
	
		$tmp_html = $tmp_html.'<input type="checkbox" ';
		$tmp_html = $tmp_html.' name="'.$tmp_name.'" id="'.$tmp_name.'" ';
		$tmp_html = $tmp_html.' value="'.$key.'"'; 
		if ( in_array($key, $tmp_current_value) )
			{
			
			$tmp_html = $tmp_html.' checked="checked"';
			}
		$tmp_html = $tmp_html.'>'.$value.'<br />';	
	endforeach;
	
	return $tmp_html;
	}


?>