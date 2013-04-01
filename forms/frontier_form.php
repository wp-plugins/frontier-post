<?php

	
	if(!isset($thispost->post_type))
		{
			$thispost->post_type = 'post';
		}

	if(!isset($thispost->post_content))
		{
			$thispost->post_content = '';
		}
	
	if (isset($thispost->ID))
		{
		$post_id = $thispost->ID;
		}
	
	frontier_media_fix( $post_id );
	
	//print_r(var_dump($thispost));
	//print_r("<hr>");

	
	// Build list of categories (3 levels)
	$catlist = array();
	foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => 0)) as $category1) :
		$tmp = Array('cat_ID' => $category1->cat_ID, 'cat_name' => $category1->cat_name);
		array_push($catlist, $tmp);
		foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $category1->cat_ID)) as $category2) :
			$tmp = Array('cat_ID' => $category2->cat_ID, 'cat_name' => "-- ".$category2->cat_name);
			array_push($catlist, $tmp);
			foreach ( get_categories(array('hide_empty' => 0, 'hierarchical' => 1, 'parent' => $category2->cat_ID)) as $category3) :
				$tmp = Array('cat_ID' => $category3->cat_ID, 'cat_name' => "-- -- ".$category3->cat_name);
				array_push($catlist, $tmp);
			endforeach; // Level 3
		endforeach; // Level 2
	endforeach; //Level 1

	if ( current_user_can( 'frontier_post_tags_edit' ) )
		{
		$taglist = array();
		if (isset($thispost->ID))
			{
			$tmptags = get_the_tags($thispost->ID);
			if ($tmptags)
				{
				foreach ($tmptags as $tag) :
					array_push($taglist, $tag->name);
				endforeach;
				}
			}
		}
	
	if ($thispost->post_author == $current_user->ID)
	{
?>	
	<script type="text/javascript">
		var filenames="";
	</script>

	<div class="frontier_post_form"> 

	<table >
	<tbody>
	<form action="" method="post" name="wppu" id="wppu" enctype="multipart/form-data" >
		<input type="hidden" name="home" value="<?php the_permalink(); ?>" > 
		<input type="hidden" name="action" value="wpfrtp_save_post"> 
		<input type="hidden" name="task" value="<?php echo $_REQUEST['task'];?>">
		<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
	<tr>
		<td class="frontier_no_border">
			<?php _e("Title", "frontier-post");?>:&nbsp;
			<input class="frontier-formtitle"  placeholder="Enter title here" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="user_post_title" >			
		</td>
	</tr><tr>
		<td> 
			<?php
			wp_editor($thispost->post_content, 'user_post_desc', array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300) );
			?>
		</td>
	</tr><tr>
		<td><table><tbody>
		<tr>
			<th class="frontier_heading" width="50%"><?php _e("Category", "frontier-post"); ?></th>
			<?php if ( current_user_can( 'frontier_post_tags_edit' ) )
				{ ?>
			<th class="frontier_heading" width="50%"><?php _e("Tags", "frontier-post"); ?></th>
			<?php } else 
				{ ?>
				  <th class="frontier_heading" width="50%">&nbsp;</th>
			<?php } ?>	  
		</tr><tr>
			<!-- Category Multi select 
			source: http://wordpress.stackexchange.com/questions/62993/category-list-in-theme-options-page
			-->
			<td class="frontier_border" width="50%">
				<select name="categorymulti[]" id="frontier_categorymulti" multiple="multiple" size="8">
				<?php  
				$cats_selected = $thispost->post_category;
				foreach ( $catlist as $category1) : ?>
					<option value="<?php echo $category1['cat_ID']; ?>" <?php if ( $cats_selected && in_array( $category1['cat_ID'], $cats_selected ) ) { echo 'selected="selected"'; }?>><?php echo $category1['cat_name']; ?></option>
				<?php endforeach; ?>
				</select>
				</br><div class="frontier_helptext"><?php _e("Select category, multible can be selected using ctrl key", "frontier-post"); ?></div>
			</td>
			<?php if ( current_user_can( 'frontier_post_tags_edit' ) )
				{ ?>
				<td class="frontier_border" width="50%">
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[0]))echo $taglist[0];?>" name="user_post_tag1" id="user_post_tag" ></br>
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[1]))echo $taglist[1];?>" name="user_post_tag2" id="user_post_tag" ></br>
					<input placeholder="<?php _e("Enter tag here", "frontier-post"); ?>" type="text" value="<?php if(isset($taglist[2]))echo $taglist[2];?>" name="user_post_tag3" id="user_post_tag" ></br>
				</td>
			<?php } ?>
		</tr>
		</tbody></table></td>
	</tr><tr>
		<td class="frontier_no_border">
			<?php
			//$select_cats = wp_dropdown_categories( array( 'echo' => 0 ) );
			//$select_cats = str_replace( "name='cat' id=", "name='cat[]' multiple='multiple' id=", $select_cats );
			//echo $select_cats;
			?>
		</td>
	</tr><tr>
		<?php if ( current_user_can( 'frontier_post_exerpt_edit' ) )
				{ ?>
				<td>
					<?php _e("Excerpt", "frontier-post")?>:</br>
					<textarea name="user_post_excerpt" id="user_post_excerpt"  cols="8" rows="2"><?php if(!empty($thispost->post_excerpt))echo $thispost->post_excerpt;?></textarea>
				</td>
				</tr><tr>
			<?php 	} ?>
		<td>
			<input type="submit"   name="user_post_submit" id="user_post_submit" value=<?php _e("Publish", "frontier-post"); ?>>
			<input type="reset" value=<?php _e("Cancel", "frontier-post"); ?>  name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
		</td>
	</tr>
	</form> 
	</tbody>
	</table>

	</div> <!-- ending div -->  
<?php
	}
	else
	{
	_e("You are not allowed to edit other users posts !","frontier-post");
	}
	// end form file
?>