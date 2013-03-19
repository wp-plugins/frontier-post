<?php

// Avoid error messages in the log due to empty variables
	if(isset($thispost->ID))
		{
			$postcategory=get_the_category($thispost->ID); 
			$postcategoryid = $postcategory[0]->term_id;
		}
	else
		{
			$postcategoryid	= -1;				
		}

	if(!isset($thispost->post_content))
		{
			$thispost->post_content = '';
		}

	if(!isset($thispost->post_type))
		{
			$thispost->post_type = 'post';
		}
	
?>
<script type="text/javascript">
	var filenames="";
</script>

<div class="wrap"> 

<table width="90%">
	<tbody>
	<tr>
	<td>

	<form action="" method="post" name="wppu" id="wppu" enctype="multipart/form-data" >
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
 
		<table class="frontier-form" >  
			<tbody>
			<tr>
				<td class="frontier-form">
					<?php _e("Title", "frontier-post");?>:&nbsp;
					<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
				</td>
			</tr><tr>
				<td class="frontier-form">
					<?php _e("Category", "frontier-post"); ?>:&nbsp; 
					<?php 
						wp_dropdown_categories(array('id'=>'cat', 'hide_empty' => 0, 'name' => 'cat', 'orderby' => 'name', 'selected' => $postcategoryid, 'hierarchical' => true, 'show_option_none' => __('None'))); 
					?>
				</td>
			</tr><tr>
				<td class="frontier-form"> 
				<?php
					wp_editor($thispost->post_content, 'user_post_desc', array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300) );
				?>
				</td>
			</tr><tr>
			<?php if ( current_user_can( 'frontier_post_exerpt_edit' ) )
					{ ?>
					<td class="frontier-form">
						<?php _e("Excerpt", "frontier-post")?>:</br>
							<textarea name="user_post_excerpt" id="user_post_excerpt"  cols="8" rows="2"><?php if(!empty($thispost->post_excerpt))echo $thispost->post_excerpt;?></textarea>
						</td>
					</tr><tr>
			<?php 	} ?>
				<td class="frontier-form">
					<input type="submit"   name="user_post_submit" id="user_post_submit" value=<?php _e("Submit", "frontier-post"); ?>>
					<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
				</td>
			</tr>
			</tbody>
		</table>
	</form> 

	</td>
	</tr>
	</tbody>
	</table>

 </div> <!-- ending div for wrapper-->  
<?php
	// end form file
?>