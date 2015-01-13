<?php


if ( strlen($frontier_edit_text_before) > 1 )
	echo '<div id="frontier_edit_text_before">'.$frontier_edit_text_before.'</div>';



frontier_post_output_msg();
/*
$x = array('post', 'page', 'location');
error_log(print_r($x, true));
$x = fp_validate_tax_list($x);

error_log("Efter clean");
error_log(print_r($x, true));


$z = get_taxonomy( 'article-type' );
error_log(print_r($z, true));
$z_labels = $z->labels;
error_log(print_r($z_labels, true));
error_log($z->label);
error_log($z_labels->singular_name);
error_log($z->labels->singular_name);

error_log("Title: ".$thispost->post_title);

$selected_at = wp_get_post_terms( $thispost->ID, "article-type", array("fields" => "ids"));
error_log("Articvle types: ".$thispost->post_title);
error_log(print_r($selected_at, true));

$fp_capabilities	= frontier_post_get_capabilities();
$x = $fp_capabilities[$users_role];
error_log(print_r($x, true));

echo "Post_ type: ".$thispost->post_type."<br>";
echo "Post type validate: ".fp_check_post_type($thispost->post_type)."<br>";
//echo "Post type validate xxx: ".fp_check_post_type("xxx")."<br>";
echo "FB exists: ".function_exists('frontier_buttons_full_buttons')."<br>";
*/

//***************************************************************************************
//* Start form
//***************************************************************************************


?>	
	<div class="frontier_post_form"> 
	<form action="" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >
		<!-- Leave hidden fields in form as they are used in the control of the shortcode abilities -->
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
		<input type="hidden" name="posttype" id="posttype" value="<?php echo (isset($thispost->post_type) ? $thispost->post_type : 'post'); ?>">
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<?php wp_nonce_field( 'frontier_add_edit_post', 'frontier_add_edit_post_'.$thispost->ID ); ?>
		<!-- Keep selected categories if no category field on form -->
		<input  type="hidden" name="post_categories" value="<?php echo $cats_selected_txt ;?>">
		<table class="frontier-post-taxonomies"><tbody><tr>
		<fieldset class="frontier_post_fieldset">
			<legend><?php _e("Title", "frontier-post"); ?></legend>
			<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
			<?php if ( $hide_post_status )
					{
					echo '<input type="hidden" id="post_status" name="post_status" value="'.$thispost->post_status.'"  >';
					}
				  else
					{
					echo ' '.__("Status", "frontier-post").': '; 
					?> 
					<select  id="post_status" name="post_status" >
						<?php foreach($status_list as $key => $value) : ?>   
							<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
								<?php echo $value; ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php } ?>	
		</fieldset>
		<fieldset class="frontier_post_fieldset">
			<legend><?php _e("Content", "frontier-post"); ?></legend>	
			<div id="frontier_editor_field"> 
			<?php
			wp_editor($thispost->post_content, 'user_post_desc', frontier_post_wp_editor_args($editor_type, $frontier_media_button, $frontier_editor_lines, false));
			printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' );
			?>
			</div>
		</fieldset>
		<?php
		
		
		
		//**********************************************************************************
		//* Taxonomies
		//**********************************************************************************
				
		//$tax_list = array("category", "group", "article-type");
		$tax_list 			= $frontier_custom_tax;
		$tax_layout_list 	= fp_get_tax_layout($frontier_custom_tax, $frontier_custom_tax_layout);
		
		
		echo '<table class="frontier-post-taxonomies"><tbody><tr>';
		foreach ( $tax_layout_list as $tmp_tax_name => $tmp_tax_layout) 
			{
			if ($tmp_tax_layout != "hide")
				{
				// Cats_selected is set from skript, but only for category
				if ($tmp_tax_name != 'category')
					$cats_selected = wp_get_post_terms($thispost->ID, $tmp_tax_name, array("fields" => "ids"));;
				
				echo '<td class="frontier-post-tax">';
				echo '<fieldset class="frontier_post_fieldset_tax">';
				echo '<legend>'.fp_get_tax_label($tmp_tax_name).'</legend>';
				echo '<div class="frontier-tax-box">';
				frontier_tax_input($thispost->ID, $tmp_tax_name, $tmp_tax_layout, $cats_selected, $frontier_post_shortcode_parms);
				echo '</div>';
				echo '</td>';
				echo '</fieldset>';
				echo PHP_EOL;
				}
			}
		echo '</tr></tbody></table>';
		
		
		if ( current_user_can( 'frontier_post_tags_edit' ) || fp_get_option_bool("fps_show_feat_img") )
			{
			echo '<table class="frontier-post-taxonomies"><tbody><tr>';
			
		
			if ( current_user_can( 'frontier_post_tags_edit' ) )
				{ ?>
				<td class="frontier-post-tags">
				
				<fieldset class="frontier_post_fieldset">
					<legend><?php _e("Tags", "frontier-post"); ?></legend>
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[0]))echo $taglist[0];?>" name="user_post_tag1" id="user_post_tag" >
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[1]))echo $taglist[1];?>" name="user_post_tag2" id="user_post_tag" >
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[2]))echo $taglist[2];?>" name="user_post_tag3" id="user_post_tag" >
				</fieldset></td>
			<?php } 
		
			if ( fp_get_option_bool("fps_show_feat_img") )
				{
				?>
				<td class="frontier_featured_image">
				
				<fieldset class="frontier_post_fieldset">
				<legend><?php _e("Featured image", "frontier-post"); ?></legend>
				<?php
				$FeatImgLinkHTML = '<a title="Select featured Image" href="'.site_url('/wp-admin/media-upload.php').'?post_id='.$post_id.'&amp;type=image&amp;TB_iframe=1'.'" id="set-post-thumbnail" class="thickbox">';
				if (has_post_thumbnail($post_id))
					$FeatImgLinkHTML = $FeatImgLinkHTML.get_the_post_thumbnail($post_id, 'thumbnail').'<br>';
			
				$FeatImgLinkHTML = $FeatImgLinkHTML.__("Select featured image", "frontier-post").'</a>';
		
				echo $FeatImgLinkHTML."<br>";
				_e("Featured image (or new featured image) not visible until post is saved", "frontier-post");
				echo '</fieldset></td>';
				}
			echo '</tr></tbody></table>';
			}
			
		if ( current_user_can( 'frontier_post_exerpt_edit' ) )
				{ ?>
				<fieldset class="frontier_post_fieldset">
					<label><?php _e("Excerpt", "frontier-post")?>:</label>
					<textarea name="user_post_excerpt" id="user_post_excerpt"  cols="8" rows="2"><?php if(!empty($thispost->post_excerpt))echo $thispost->post_excerpt;?></textarea>
				</fieldset>
				
		<?php 	} ?>
		
		
		
			<fieldset class="frontier_post_fieldset">
			<legend><?php _e("Actions", "frontier-post"); ?></legend>
			<?php
				
			if ( fp_get_option_bool("fps_submit_save") )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 		id="user_post_save" 	value="save"><?php _e("Save", "frontier-post"); ?></button>
			<?php }
			if ( fp_get_option_bool("fps_submit_savereturn") )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $frontier_return_text; ?></button>
			<?php }
			if ( fp_get_option_bool("fps_submit_preview") )
			{ ?>
				<button class="button" type="submit" name="user_post_submit" 	id="user_post_preview" 	value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
			<?php } 
			if ( fp_get_option_bool("fps_submit_cancel") )
			{ ?>
			<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
			<?php } ?>
		</fieldset>
	
	</form> 
	
	</div> <!-- ending div -->  
<?php
	
	// end form file
?>