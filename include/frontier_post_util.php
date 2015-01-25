<?php
/*
Utilities for Frontier Post plugin
*/


	
// Build a 3 level deep hieracical list of taxonomies for use in form fields
function frontier_tax_list($tmp_tax_name = "category", $exclude_list = array(), $parent_tax = 0, $force_simple = false	)
	{
	$tmp_tax_list 		= array();
	$level_sep			= "-- ";
	$cat_incl_txt		= "";
	
	$fp_capabilities 			 = frontier_post_get_capabilities();
	
	// special for categories to respect settings for category
	if ($tmp_tax_name == 'category')
		{
		$tmp_layout_list['category'] = $fp_capabilities[frontier_get_user_role()]['fps_role_category_layout'] ? $fp_capabilities[frontier_get_user_role()]['fps_role_category_layout'] : "multi";
		$cat_incl 					 = fp_list2array($fp_capabilities[frontier_get_user_role()]['fps_role_allowed_categories']);
		
		// if allowed categories is set and valid, disregard parent category and excluded categories
		if ( count($cat_incl) > 0 )
			{
			$cat_incl_txt		= count($cat_incl)>0 ? implode(",", $cat_incl) : "";
			$exclude_list 		= array(); 
			$parent_tax			= 0;
			}
		}
	//error_log("Allowed categories");
	//error_log(print_r($cat_incl, true));
	
	//Just return simple list if parent category or allowed categories is set 
	if ( ($force_simple) || ($cat_incl_txt <= " ") || ($parent_tax > 0) )
		{
		foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $parent_tax, 'include' => $cat_incl_txt, 'show_count' => true)) as $tax1) :
			$tmp_tax_list[$tax1->cat_ID] = $tax1->cat_name;
		endforeach; //Level 1	
		}
	else	
		{	
		foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $parent_tax, 'exclude' => $exclude_list, 'show_count' => true)) as $tax1) :
			$tmp_tax_list[$tax1->cat_ID] = $tax1->cat_name;
			foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $tax1->cat_ID, 'exclude' => $exclude_list, 'show_count' => true)) as $tax2) :
				$tmp_tax_list[$tax2->cat_ID] = $level_sep.$tax2->cat_name;
				foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $tax2->cat_ID, 'exclude' => $exclude_list, 'show_count' => true)) as $tax3) :
					$tmp_tax_list[$tax3->cat_ID] = $level_sep.$level_sep.$tax3->cat_name;
				endforeach; // Level 3
			endforeach; // Level 2
		endforeach; //Level 1
		}
	//error_log(print_r($tmp_tax_list, true));
		
	return $tmp_tax_list;
	}

//********************************************************************************
// Output taxonomy html
//********************************************************************************

function frontier_tax_input($tmp_post_id, $tmp_tax_name, $input_type = 'checkbox', $tmp_selected = array(), $tmp_shortcode_parms)
	{
	if ( !empty($tmp_tax_name) )
		{
		if ( $input_type == "readonly" )
			$force_simple = true;
		else
			$force_simple = false;
		
		if ($tmp_tax_name == 'category')
			{
			$tmp_tax_list 	= frontier_tax_list($tmp_tax_name, fp_get_option("fps_excl_cats", ''), intval($tmp_shortcode_parms['frontier_parent_cat_id']), $force_simple);
			}
		else
			{
			$tmp_tax_list 	= frontier_tax_list($tmp_tax_name, "", 0, $force_simple);
			}
		
		//$tmp_selected 			= wp_get_post_terms( $tmp_post_id, $tmp_tax_name, array("fields" => "ids"));		
		$tmp_tax_heading		= $tmp_tax_name;
		$tmp_field_name			= frontier_tax_field_name($tmp_tax_name);
		$tmp_input_field_name	= $tmp_field_name.'[]';
		
				
		switch ($input_type) 
			{
			
			case "single":
				wp_dropdown_categories(array('taxonomy' => $tmp_tax_name, 'id'=>$tmp_field_name, 'hide_empty' => 0, 'name' => $tmp_input_field_name, 'orderby' => 'name', 'selected' => $tmp_selected, 'hierarchical' => true, 'show_count' => true, 'class' => 'frontier_post_dropdown')); 
				break;
		
			case "multi":
				echo frontier_post_tax_multi($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name, 10);
				//echo '</br><div class="frontier_helptext">'.__("Select category, multible can be selected using ctrl key", "frontier-post").'</div>';
				break;

			case "checkbox":
				echo frontier_post_tax_checkbox($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;
			
			case "radio":
				echo frontier_post_tax_radio($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;
				
			case "readonly":
				echo frontier_post_tax_readonly($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;
			} // switch
		
		} // if !empty()
			
	
	}	// function frontier_tax_input



//Build html multiselect dropdown for taxonomies
Function frontier_post_tax_multi($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id, $tmp_size)
	{
	$tmp_html = '<select class="frontier_post_dropdown" name="'.$tmp_name.'" id="'.$tmp_id.'" multiple="multiple" size="'.$tmp_size.'">';
	
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<option value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.'selected="selected"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'</option>';
	endforeach;
	$tmp_html = $tmp_html.'</select>';
	return $tmp_html;					 
	}

//Build html multiselect checkbox for taxonomies
Function frontier_post_tax_checkbox($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	
	$tmp_html = '';
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<input type="checkbox" ';
		//$tmp_html = $tmp_html.' id="'.$tmp_id.'"'; 
		$tmp_html = $tmp_html.' name="'.$tmp_name.'"';
		
		$tmp_html = $tmp_html.' value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.'checked="checked"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'<br>'.PHP_EOL;
		endforeach;
	return $tmp_html;	
	}		


//Build html radio button select for taxonomies
Function frontier_post_tax_radio($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	$tmp_html = '';
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<input type="radio" ';
		//$tmp_html = $tmp_html.' id="'.$tmp_id.'"'; 
		$tmp_html = $tmp_html.' name="'.$tmp_name.'"';
		
		$tmp_html = $tmp_html.' value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.'checked="checked"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'<br />';
		endforeach; 
	return $tmp_html;	
	}		

//Build html output for readonly taxonomy
Function frontier_post_tax_readonly($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	
	$tmp_html = '<ul>';
	if ( count($tmp_selected) == 0 )
		{
		$tmp_html = $tmp_html."<li>".__("None", "frontier-post")."</li>";
		}
	else
		{
		foreach ( $tmp_selected as $taxid ) :
			$tmp_html = $tmp_html."<li>".$tmp_cat_list[$taxid]."</li>";
		endforeach; 
		}
	$tmp_html = $tmp_html."</ul>";
	return $tmp_html;	
	}		


//********************************************************************************
// Out messages
//********************************************************************************


function frontier_post_set_msg($tmp_msg)
	{
	if ( ( isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '' ) != '' )
		$_REQUEST['frontier-post-msg'] = $_REQUEST['frontier-post-msg']."<br>".$tmp_msg;
	else
		$_REQUEST['frontier-post-msg'] = $tmp_msg;
	}

function frontier_post_output_msg()
	{
	if ( get_option("frontier_post_show_msg", "false") == "true" )
		{
		$tmp_msg = isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '';
		echo '<div class="frontier_post_msg">'.$tmp_msg.'</div>';
		}
	$_REQUEST['frontier-post-msg'] = null;
	}
	
	


//********************************************************************************
// Check post type functions
//********************************************************************************

// Return list of post types
function fp_get_post_type_list()
		{
		return get_post_types(array('public'   => true));
		}


//Default list of allowed post types for a user
function fp_default_post_type_list()
	{
	$tmp_pt_array = fp_get_option_array('fps_custom_post_type_list');
	if ( !current_user_can('frontier_post_can_page') )
		{
		if(($tmp_key = array_search('page', $tmp_pt_array)) !== false) 
			unset($tmp_pt_array[$tmp_key]);
		}
	return $tmp_pt_array;	
	}


// return allowed post types of $tmp_pt_array
function fp_validate_post_type_list($tmp_pt_array)
	{
	return array_intersect($tmp_pt_array, fp_default_post_type_list() );	
	}


// Check if user can add/edit/delete posts with this post_type	
function fp_check_post_type($tmp_post_type)
	{
	if ( array_search($tmp_post_type, fp_default_post_type_list() ) !== false)  
		return true;
	else
		return false;	
	}

// Get name (label) of post type (plural)
function fp_get_posttype_label($tmp_pt_name)
	{
	$tmp_pt = get_post_type_object($tmp_pt_name);
	//error_log(print_r($tmp_pt,true));
	return $tmp_pt->label;
	}

// Get singular name (label) of post type
function fp_get_posttype_label_singular($tmp_pt_name)
	{
	$tmp_pt = get_post_type_object($tmp_pt_name);
	return $tmp_pt->labels->singular_name;
	}

//********************************************************************************
// Check Taxonomy functions
//********************************************************************************

// return list of public taxonomies
function fp_get_tax_list()
	{
	return get_taxonomies(array('public'   => true, '_builtin' => false));
	}


//Default list of allowed post types for a user
function fp_default_tax_list()
	{
	return fp_get_option_array('fps_custom_tax_list');	
	}


// return allowed post types of $tmp_tax_array
function fp_validate_tax_list($tmp_tax_array)
	{
	return array_intersect($tmp_tax_array, fp_default_tax_list() );	
	}

// Check if it is an allowed taxonomy	
function fp_check_tax($tmp_tax)
	{
	if ( array_search($tmp_tax, fp_default_tax_list() ) !== false)  
		return true;
	else
		return false;	
	}
	
//Check that the number of elements in the layout array corresponds to the length of the array of taxonomies

function fp_get_tax_layout($tax_list, $layout_list = array())
	{
	$tmp_layout_list 	= array();
	
	if ( !array_key_exists('category', $tax_list) )
		{
		$fp_capabilities = frontier_post_get_capabilities();
		$tmp_layout_list['category'] = $fp_capabilities[frontier_get_user_role()]['fps_role_category_layout'] ? $fp_capabilities[frontier_get_user_role()]['fps_role_category_layout'] : "multi";
		}
	
	if ( count($tax_list) > 0  )
		{
		include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
		$chk_layout = array_values($category_types);
		$s 			= 0;
		
		foreach ($tax_list as $tmp_tax)
			{
			if ( ($s >= count($layout_list)) || empty($layout_list[$s]) )
				$tmp_layout = fp_get_option('fps_default_tax_select', 'radio');
			else
				$tmp_layout = $layout_list[$s];
				
			// Check that it is a valid layout
			if ( !in_array($tmp_layout, $chk_layout, true) )
				$tmp_layout = fp_get_option('fps_default_tax_select', 'radio');
			
			$tmp_layout_list[$tmp_tax] = $tmp_layout;
			
			$s++;
			}
		}
	
	return $tmp_layout_list;	
	}
	

function frontier_tax_field_name($tmp_tax_name)
	{
	return 'fp_tax_'.$tmp_tax_name;
	}




// Get name (label) of taxonomy (plural)
function fp_get_tax_label($tmp_tax_name)
	{
	$tmp_tax = get_taxonomy($tmp_tax_name);
	return $tmp_tax->label;
	}

// Get singular name (label) of taxonomy
function fp_get_tax_label_singular($tmp_tax_name)
	{
	$tmp_tax = get_taxonomy($tmp_tax_name);
	return $tmp_tax->labels->singular_name;
	}



//********************************************************************************
// get comment icon for the list
//********************************************************************************



function frontier_get_comment_icon()
	{
	
	
	$comment_icon			= TEMPLATEPATH."/images/comments.png";
		
	//print_r("Comment icon: ".$comment_icon);
	
	
	if (file_exists($comment_icon))
		{
		$comment_icon_html	= "<img src='".get_bloginfo('template_directory')."/images/comments.png'></img>";
		}
	else
		{
		$comment_icon		= ABSPATH."/wp-includes/images/wlw/wp-comments.png";
		// if no icon in theme, check wp-includes, and if it isnt the use a space
		if (file_exists($comment_icon))
			{
			$comment_icon_html	= "<img src='".get_bloginfo('url')."/wp-includes/images/wlw/wp-comments.png'></img>";
			}
		else
			{
			// use the one from this plugin
			$comment_icon_html	= FRONTIER_POST_URL.'/include/comments.png';
			}
		}	
	return $comment_icon_html;
	}

//********************************************************************************
// Get settings/options
//********************************************************************************

function frontier_post_get_capabilities()
	{
	$fps_capabilities = get_option(FRONTIER_POST_CAPABILITY_OPTION_NAME, array() );
	if ( count($fps_capabilities) == 0 )
		error_log("Unable to load frontier_post_capabilities or empty");
	
	//error_log("Loading capabilities");
	//error_log(print_r($fps_capabilities, true));
	return $fps_capabilities;
	}

function frontier_post_get_settings()
	{
	$fps_settings = get_option(FRONTIER_POST_SETTINGS_OPTION_NAME, array() );
	if ( count($fps_settings) == 0 )
		error_log("Unable to load frontier_post_general_options or empty");
		
	return $fps_settings;
	}

function fp_get_option($tmp_option_name, $tmp_default = null)
	{
	$fp_settings = frontier_post_get_settings();
	
	if ( array_key_exists($tmp_option_name, $fp_settings) )
		return $fp_settings[$tmp_option_name];
	else
		{
		error_log($tmp_option_name." not present in frontier_post_general_options");
		return $tmp_default;
		}
	}

function fp_get_option_int($tmp_option_name, $tmp_default = 0)
	{
	$fp_settings = frontier_post_get_settings();
	
	if ( array_key_exists($tmp_option_name, $fp_settings) )
		return intval($fp_settings[$tmp_option_name]);
	else
		{
		error_log($tmp_option_name." not present in frontier_post_general_options");
		return intval($tmp_default);
		}
	}

function fp_get_option_array($tmp_option_name, $tmp_default = array())
	{
	$fp_settings = frontier_post_get_settings();
	
	if ( is_array($fp_settings[$tmp_option_name]) )
		return $fp_settings[$tmp_option_name];
	else
		return array($fp_settings[$tmp_option_name]);
		
	}

function fp_get_option_bool($tmp_option_name)
	{
	$fp_settings = frontier_post_get_settings();	
	if ( array_key_exists($tmp_option_name, $fp_settings) )
		{
		$tmp_value = ($fp_settings[$tmp_option_name] ? $fp_settings[$tmp_option_name] : "false");
		//$tmp_value = "true";
		//error_log("from fp_get_option_bool: ".$tmp_value);
		if ( in_array($tmp_value, array('true', 'True', 'TRUE', 'yes', 'Yes', 'y', 'Y', '1','on', 'On', 'ON', true, 1), true) )
			return true;
		else
			return false;
		}
	else
		{
		error_log($tmp_option_name." not present in frontier_post_general_options");
		return false;
		}
	}

//********************************************************************************
// Editor
//********************************************************************************



function frontier_post_wp_editor_args($editor_type = "full", $media_button = true, $editor_lines = 300, $dfw = false)
	{
	$editor_layout	= array('dfw' => $dfw, 'editor_height' => $editor_lines, 'media_buttons' => $media_button );
	
	// Get tinymce button layout from Frontier Buttons
	if ( ($editor_type == 'full') && (function_exists('frontier_buttons_full_buttons')) )
		{
		$tinymce_buttons = frontier_buttons_full_buttons();
		$tmp = array('tinymce' => $tinymce_buttons);
		array_merge($editor_layout, $tmp);
		}
	
	
	if ($editor_type == "minimal-visual")
		$editor_layout = array_merge($editor_layout, array('teeny' => true, 'quicktags' => false));
	
	if ($editor_type == "minimal-html")
		$editor_layout = array_merge($editor_layout, array('teeny' => true, 'tinymce' => false));
		
	if ($editor_type == "text")	
		$editor_layout = array_merge($editor_layout, array('quicktags' => false, 'tinymce' =>false));
	
	//error_log(print_r($editor_layout, true));
	return $editor_layout;
	}



function fp_list2array($tmp_list)
	{
	if ($tmp_list > " ")
		$tmp_array = explode(",", $tmp_list);
	else
		$tmp_array = array();
		
	return $tmp_array;
	}



?>