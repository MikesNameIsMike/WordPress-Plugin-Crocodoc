<?php

    	//  If the user does not have the required permissions...
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

		//  Get list of all custom post types to display on the page.
	$customPostTypes  = get_post_types(array('_builtin' => false), 'objects');
    	// Get Crocodoc plug-in options from database.
    $key_option_val = get_option('croc-api_key');
	$delete_media_val = get_option('croc-delete_media');
	$delete_croc_val = get_option('croc-delete_croc');	
	$selected_post_types_val = get_option('croc-post_types');	
	$selected_post_types = explode('|',$selected_post_types_val);
	
    	//  If data was posted to the page...
    if( isset($_POST['croc_submit_hidden']) && $_POST['croc_submit_hidden'] == 'Y') {
     		//  Save the API key to the Options table.
		$key_option_val = $_POST[ 'api-key_input'];
		update_option( 'croc-api_key', $key_option_val );
		
			//  Save the option to delete media from the library.
		$delete_media_val = isset($_POST['delete_media_check']);
		update_option('croc-delete_media', $delete_media_val );	
			//  Save the option to delete files from crocodoc.
		$delete_croc_val = isset($_POST['delete_croc_check']);	
		update_option('croc-delete_croc', $delete_croc_val );			
			
			//  Get the selected post types.
		$selected_post_types = $_POST['postTypesCheck'];
		$selected_string = "";
		$first = true;
			//  Build a string containing all selected post types.
		foreach($selected_post_types as $selected_type) {
			if ($first){
				$selected_string .= $selected_type;
				$first = false;
			}
			else {
				$selected_string .= '|'.$selected_type;
			}
		}
			//  Save the post types to the Options table.
		update_option( 'croc-post_types', $selected_string );
        
        	// Display an 'updated' message.
		?>
		<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
		<?php
    }
?>

<div class="wrap crocOptions">
	
 	<a href="http://crocodoc.com/accounts/profile/" target="_blank" id="icon-crocodoc" class="icon32"></a>
	<h2>Crocodoc Options</h2>

	<form name="form" method="post" action="">
		
		<input type="hidden" name="croc_submit_hidden" value="Y">

		<h3>API Settings</h3>
		<p>Use this API token:
		<input size=25 type="text" name="api-key_input" value="<?php echo $key_option_val; ?>" size="20"> <a class="get-token" href="http://crocodoc.com/api/" target="_blank">Get Token</a></p>
		
		<h3>Post Type Settings</h3>
		<h4>Allow Crocodoc uploads on these post types:</h4>
		<p>Word-Press Post types:</p>
		<input class="checkbox" type="checkbox" name="postTypesCheck[]" value="post" <?php if(in_array('post',$selected_post_types) ){echo('checked="checked"');}?> /><span class="check-label">Posts</span><br />
		<input class="checkbox" type="checkbox" name="postTypesCheck[]" value="page" <?php if(in_array('page',$selected_post_types) ){echo('checked="checked"');}?> /><span class="check-label">Pages</span><br />
		
		<?php if ($customPostTypes ) {?>
			<p>Custom Post types:</p><?php
			foreach ($customPostTypes as $customPostType) {?>
				<input class="checkbox" type="checkbox" name="postTypesCheck[]" value="<?=$customPostType->name?>" <?php if(in_array($customPostType->name,$selected_post_types)){echo('checked="checked"');}?>/><span class="check-label"><?=$customPostType->labels->name?></span><br /><?php
			}
		}?>
		
		<h3>Miscellaneous Settings</h3>
		<h4>When detaching a document from a post, or deleting a post with an attached document, also delete document from:</h4>
		<input class="checkbox" type="checkbox" name="delete_croc_check" value="post" <?php if($delete_croc_val) {echo('checked="checked"');}?> /><span class="check-label">Crocodoc</span><br />
		<input class="checkbox" type="checkbox" name="delete_media_check" value="post" <?php if($delete_media_val) {echo('checked="checked"');}?> /><span class="check-label">My WordPress Media Library</span><br />
		<hr />
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
		
	</form>
	
</div>