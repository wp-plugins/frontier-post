<?php
/*
Default values for Frontier Post plugin
*/

//global $default_post_edit_max_age;
//global $default_post_delete_max_age;


		
$default_post_edit_max_age		= 7;
$default_post_delete_max_age	= 3;



$frontier_option_list 	= array('can_add', 'can_edit', 'can_publish', 'can_private', 'can_draft', 'can_delete',  'redir_edit', 'show_admin_bar', 'exerpt_edit', 'tags_edit',  'can_media', 'can_page', 'editor', 'category', 'default_category');
$frontier_cap_list 		= array('frontier_post_can_add', 'frontier_post_can_edit', 'frontier_post_can_publish', 'frontier_post_can_private', 'frontier_post_can_draft', 'frontier_post_can_delete', 'frontier_post_redir_edit', 'frontier_post_show_admin_bar', 'frontier_post_exerpt_edit', 'frontier_post_tags_edit', 'frontier_post_can_media', 'frontier_post_can_page');
$editor_types 			= array(__('Full Editor', 'frontier-post') => 'full', __('Minimal Visual', 'frontier-post') => 'minimal-visual', __('Minimal-Html', 'frontier-post') => 'minimal-html', __('Text Only', 'frontier-post') => 'text');
$category_types 		= array(__('Multi select', 'frontier-post') => 'multi', __('Radio Button', 'frontier-post') => 'radio', __('Checkbox', 'frontier-post') => 'checkbox', __('Single select', 'frontier-post') => 'single', __('Hide', 'frontier-post') => 'hide', __('Read only', 'frontier-post') => 'readonly');
$frontier_option_slice 	= 8;

$std_mce_buttons_1		= 'bold, italic, strikethrough, bullist, numlist, blockquote, justifyleft, justifycenter, justifyright, link, unlink, wp_more, fullscreen, wp_adv';
$std_mce_buttons_2		= 'formatselect, underline, justifyfull, forecolor, pastetext, pasteword, removeformat, charmap, outdent, indent, undo, redo, wp_help';
$std_mce_buttons_3		= '';
$std_mce_buttons_4		= '';

$frontier_mce_buttons_1	= 'bold, italic, underline, strikethrough, bullist, numlist, blockquote, justifyleft, justifycenter, justifyright, link, unlink, wp_more, fullscreen, wp_adv';
$frontier_mce_buttons_2	= 'emotions, formatselect, justifyfull, forecolor, pastetext, pasteword, removeformat, charmap, outdent, indent, undo, redo, wp_help';
$frontier_mce_buttons_3 = 'search,replace,|,tablecontrols';
$frontier_mce_buttons_4 = '';

$frontier_author_default_caps = array('delete_posts' => true, 'delete_published_posts' => true, 'edit_posts' => true, 'edit_published_posts' => true, 'publish_posts' => true, 'private_posts' => false, 'read' => true, 'upload_files' => true);
$frontier_author_role_name	  = "frontier-author";

$frontier_default_submit = array('save' => 'true', 'savereturn' => 'true', 'preview' => 'true', 'cancel' => 'true' );

//$frontier_default_login_txt = __("Please log in !", "frontier-post").'&nbsp;<a href="'.wp_login_url().'">'.__("Login Page", "frontier-post").'</a>';
$frontier_default_login_txt = '<a href="'.wp_login_url().'">'.__("Please log in !", "frontier-post").'</a>';

// Cache time selection

	
$fp_cache_time_list = array(
	-1			=> __('Caching Disabled', 'frontier-post'),
	60			=> '01 '.__('minute', 'frontier-post'),
	3*60		=> '03 '.__('minutes', 'frontier-post'),
	5*60		=> '05 '.__('minutes', 'frontier-post'),
	10*60		=> '10 '.__('minutes', 'frontier-post'),
	15*60		=> '15 '.__('minutes', 'frontier-post'),
	30*60		=> '30 '.__('minutes', 'frontier-post'),
	1*60*60		=> '01 '.__('hour', 'frontier-post'),
	2*60*60		=> '02 '.__('hours', 'frontier-post'),
	6*60*60		=> '06 '.__('hours', 'frontier-post'),
	12*60*60	=> '12 '.__('hours', 'frontier-post'),
	24*60*60	=> '24 '.__('hours', 'frontier-post'),
	
);


	
$fp_tag_transform_list = array(
	'none'					=> __('No transformation', 'frontier-post'),
	'lower'					=> __('lower case', 'frontier-post'),
	'upper'					=> __('UPPER CASE', 'frontier-post'),
	'ucwords'				=> __('First Letter Capitalized', 'frontier-post'),

);

$frontier_post_forms = array(
	'standard'	=> __("Standard Form (with Taxonomies)", "frontier-post"),
	'simple'	=> __("Simpel Form (only title, status, content & submit)", "frontier-post"),
	'old'		=> __("Old Form (for backwards compatibility)", "frontier-post")
	);

$frontier_list_forms = array(
	'simple'		=> __("Simple List (one line per record)", "frontier-post"),
	'list'			=> __("List", "frontier-post"),
	'excerpt'		=> __("Excerpt", "frontier-post"),
	'full_post'		=> __("Full Post", "frontier-post")
	);


$fp_capability_list 	= array(
	'frontier_post_can_add' 		=> __("Can Add", "frontier-post"), 	
	'frontier_post_can_edit' 		=> __("Can Edit", "frontier-post"), 	
	'frontier_post_can_delete' 		=> __("Can Delete", "frontier-post"), 
	'frontier_post_can_publish'		=> __("Can Publish", "frontier-post"), 	
	'frontier_post_can_draft' 		=> __("Can Drafts", "frontier-post"), 
	'frontier_post_can_pending' 	=> __("Can Pending", "frontier-post"), 
	'frontier_post_can_private' 	=> __("Private Posts", "frontier-post"), 	
	'frontier_post_redir_edit' 		=> __("Frontier Edit", "frontier-post"), 
	'frontier_post_show_admin_bar' 	=> __("Show admin bar", "frontier-post"), 	
	'frontier_post_exerpt_edit' 	=> __("Edit Excerpt", "frontier-post"), 
	'frontier_post_tags_edit' 		=> __("Edit Tags", "frontier-post"), 
	'frontier_post_can_media'		=> __("Media Upload", "frontier-post"),
	'frontier_post_can_page'		=> __("Can Pages", "frontier-post")
	);
	
$fp_role_option_list 	= array(
	'fps_role_editor_type'			=> __("Editor Type", "frontier-post"), 
	'fps_role_category_layout'		=> __("Category Layout", "frontier-post"), 
	'fps_role_default_category'		=> __("Default Category", "frontier-post"),
	'fps_role_allowed_categories'	=> __("Allowed Categories", "frontier-post"),
	
	);

		

//*******************************************************************************************
// Admin menu defaults
//*******************************************************************************************

// List variables used for General settings;
	$fps_general_option_list = array(
		'fps_edit_max_age',
		'fps_delete_max_age', 
		'fps_ppp', 
		'fps_page_id', 
		'fps_pending_page_id', 
		'fps_del_w_comments', 
		'fps_edit_w_comments', 
		'fps_excl_cats', 
		'fps_show_feat_img', 
		'fps_show_login', 
		'fps_default_status',
		'fps_show_msg', 
		'fps_submit_save',
		'fps_submit_savereturn',
		'fps_submit_preview',
		'fps_submit_cancel',
		'fps_change_status',
		'fps_custom_post_type_list',
		'fps_use_icons',
		'fps_hide_add_on_list',
		'fps_default_list'
		
		);
		
		
// List variables used for Advanced settings;
	$fps_advanced_option_list = array(
		'fps_hide_title_ids', 
		'fps_default_editor', 
		'fps_default_cat_select',
		'fps_external_cap',
		'fps_author_role', 
		'fps_editor_lines', 
		'fps_hide_status', 
		'fps_mail_to_approve', 
		'fps_mail_approved', 
		'fps_mail_address',  
		'fps_catid_list', 
		'fps_hide_title_ids',
		'fps_allow_custom_tax',
		'fps_custom_tax_list',
		'fps_default_tax_select',
		'fps_use_tax_form',
		'fps_keep_options_uninstall',
		'fps_default_form',
		'fps_use_custom_login_txt', 
		'fps_custom_login_txt',
		'fps_disable_abar_ctrl',
		'fps_tag_count',
		'fps_tags_transform',
		'fps_use_moderation',
		'fps_mod_default_email',
		'fps_cache_time_tax_lists'
		);
		
	
	// Default values
	$fps_general_defaults = array(		
		'fps_edit_max_age' 				=> 10,
		'fps_delete_max_age' 			=> 3,
		'fps_ppp'						=> 25, 
		'fps_page_id'					=> 0,
		'fps_pending_page_id'			=> 0,
		'fps_del_w_comments'			=> "false", 
		'fps_edit_w_comments'			=> "false", 
		'fps_author_role'				=> "false", 
		'fps_mail_to_approve'			=> "false", 
		'fps_mail_approved'				=> "false", 
		'fps_mail_address'				=> "", 
		'fps_excl_cats'					=> "", 
		'fps_show_feat_img'				=> "false", 
		'fps_show_login'				=> "false", 
		'fps_change_status'				=> "true",
		'fps_catid_list' 				=> "",
		'fps_editor_lines' 				=> 300, 
		'fps_default_status'			=> "publish",
		'fps_hide_status'				=> "false",
		'fps_show_msg'					=> "true",
		'fps_hide_title_ids'			=> "", 
		'fps_default_editor'			=> "full", 
		'fps_default_cat_select'		=> "multi",
		'fps_submit_save'				=> "true",
		'fps_submit_savereturn'			=> "true",
		'fps_submit_preview'			=> "true",
		'fps_submit_cancel'				=> "true",
		'fps_external_cap'				=> "false",
		'fps_allow_custom_tax'			=> "false",
		'fps_custom_tax_list'			=> "",
		'fps_default_tax_select'		=> "radio",
		'fps_allow_custom_post_type'	=> "false",
		'fps_custom_post_type_list'		=> "post",
		'fps_use_tax_form'				=> "false",
		'fps_keep_options_uninstall'	=> "false",
		'fps_default_form'				=> "standard",
		'fps_custom_login_txt'			=> $frontier_default_login_txt,
		'fps_use_custom_login_txt' 		=> "false",
		'fps_disable_abar_ctrl'			=> "false",
		'fps_use_icons'					=> "false",
		'fps_tag_count'					=> 3,
		'fps_tags_transform'			=> "none",
		'fps_use_moderation'			=> "false",
		'fps_mod_default_email'			=> "false",
		'fps_hide_add_on_list'			=> "false",
		'fps_default_list'				=> "list",
		'fps_cache_time_tax_lists'		=> -1
		);



?>