<?php
/*
Default values for Frontier Post plugin
*/

global $default_post_edit_max_age;
global $default_post_delete_max_age;


		
$default_post_edit_max_age		= 7;
$default_post_delete_max_age	= 3;

$frontier_option_list 	= Array('can_add', 'can_edit', 'can_publish', 'can_draft', 'can_delete',  'redir_edit', 'exerpt_edit', 'tags_edit',  'can_media', 'editor', 'category');
$editor_types 			= array(__('Full Editor') => 'full', __('Minimal Visual') => 'minimal-visual', __('Minimal-Html') => 'minimal-html', __('Text Only') => 'text');
$category_types 		= array(__('Multi select') => 'multi', __('Single select') => 'single', __('Hide') => 'hide');
$frontier_option_slice 	= 6;


?>