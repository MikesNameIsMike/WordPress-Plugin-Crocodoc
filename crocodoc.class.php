<?php
abstract class Crocodoc {
	
	
	/**
 	*  Creates an attachment meta box on user selected post types.
 	*
 	*  @return void
 	*  @author Michael Doss
 	*/
	public static function add_meta_box($post_id) {
			//  Get the list of post types that should have attachments sent to Crocodoc.
		$post_types_val = get_option('croc-post_types');	
		$post_types = explode('|', $post_types_val);
			//  For each post type specified.
		foreach($post_types as $post_type) {
				//  Add the attachment meta field to the 'Create new Post' page.
			add_meta_box( 'crocodoc_attachment', 'Crocodoc Attachment', 'Crocodoc::get_meta_input', $post_type, 'normal' );
		}
	}
	

	/**
 	*  Creates an "Attach Document" button in the Crocodoc meta box.
 	*
 	*  @return void
 	*  @author Michael Doss
 	*/	
	public static function get_meta_input($post) {
		$media_upload_iframe_src = "media-upload.php?type=image&TB_iframe=1";
		$image_upload_iframe_src = apply_filters( 'image_upload_iframe_src', "$media_upload_iframe_src" );
		$current_attachment_id = get_post_meta($post->ID, 'croc_attachment_id', true);
		$croc_attachment = "";
			    //  If an attachment is already associated with this post...
		if ($current_attachment_id) {
		        //  Get document specific variables.
		    $attachment_src = wp_get_attachment_image_src($current_attachment_id, 'thumbnail', true);
		    $attachment_title = get_post_meta($post->ID, 'croc_attachment_title', true);
		    $editable = (get_post_meta($post->ID, 'croc_attachment_editable', true)=='true')?1:0;
		    $downloadable = (get_post_meta($post->ID, 'croc_attachment_downloadable', true)=='true')?1:0;
		        //  Build the attachment display.
		    $croc_attachment .= '<div class="croc-attachment-container cf">';
            $croc_attachment .= '   <img class="croc-attachment-thumb cf" src="'.$attachment_src[0].'" alt="Thumbnail" />';
            $croc_attachment .= '   <span class="croc-attachment-title">'.$attachment_title.'</span>';
            $croc_attachment .= '   <div class="croc-attachment-options cf">';
            $croc_attachment .= '       <input class="checkbox" type="checkbox" name="crocDocOptions[]" value="editable"';
                                        if($editable){ $croc_attachment.=' checked="checked"'; }
            $croc_attachment .= '       /><span class="check-label">Editable</span>';
            $croc_attachment .= '       <input class="checkbox" type="checkbox" name="crocDocOptions[]" value="downloadable"';
                                        if($downloadable){ $croc_attachment.=' checked="checked"'; }
            $croc_attachment .= '       /><span class="check-label">Downloadable</span>';
            $croc_attachment .= '   </div>';
            $croc_attachment .= '   <input type="hidden" name="croc_attachment_id" id="croc_attachment_id" value="'.$current_attachment_id.'" />';
            $croc_attachment .= '   <input type="hidden" name="croc_attachment_title" id="croc_attachment_title" value="'.$attachment_title.'" />';
            $croc_attachment .= '</div>';
	    }
		?>
		<div id="croc-upload-field">
		    <input type="hidden" name="croc_nonce" id="croc_nonce" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" />
			<a class="croc-attach-button button" href="<?php echo $image_upload_iframe_src; ?>&attachments_thickbox=1" title="Crocodoc" class="button">Attach A Document</a>
			<a class="croc-detach-button button" href="javascript:void(0)" title="Crocodoc" class="button">Detach Document</a>
			<?php echo($croc_attachment); ?>
		</div>
		<?php
	}


	/**
 	*  Hook used when a post is saved - Core functionality for saving/updating crocodoc uploads.
 	*
	*  @param string $post_id : The ID of the post being saved.
 	*  @return void
 	*  @author Michael Doss
 	*/
    public static function on_post_save($post_id) {
            //  Verify nonce.
        if ( !isset( $_POST['croc_nonce'] )) {
            return $post_id;
        }
        if ( !wp_verify_nonce( $_POST['croc_nonce'], plugin_basename(__FILE__)) ) {
            return $post_id;
        }
            //  Don't update if this is an autosave.
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post_id;
        }
            //  If this is a page...
        if( $_POST['post_type'] == 'page') {
                //  If user doesn't have permissions to edit pages...
            if( !current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        }
            //  If this is a post...
        else {
                //  If the user doesn't have permission to edit pasts...
            if( !current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
			//  Get the list of post types that should have attachments sent to Crocodoc.
		$post_types_val = get_option('croc-post_types');	
		$valid_post_types = explode('|', $post_types_val);
		$post_type = get_post_type($post_id);
	
			//  If this post should have its attachments sent to Crocodoc.
		if ( in_array($post_type, $valid_post_types) ) {
	
		        //  Get the document options.    
		    $selectedOptions = $_POST['crocDocOptions'];
		    $editable = $downloadable = 'false;';
            if ($selectedOptions) {
		        if(in_array('editable', $selectedOptions)) {
		            $editable = 'true';
		        }
		        if(in_array('downloadable', $selectedOptions)) {
		            $downloadable = 'true';
		        }
	        }
                //  If a crocodoc attachment exists for this post already...
    		if ( get_post_meta($post_id, 'croc_attachment_id', true) ) { 		    
    		    $stored_croc_attachment_id = get_post_meta($post_id, 'croc_attachment_id', true);
    		    
    		        //  If there is an attachment on the form...
    		    if ( isset($_POST['croc_attachment_id']) ) {
    		    
    		            //  If this attachment is different then currently saved attachment...
    		        if ( $_POST['croc_attachment_id'] != $stored_croc_attachment_id ) {
    		                //  Remove old attachment from post.
    		            self::delete_attachment($post_id);
                            //  Attach the new document to the post and upload to crocodoc.
    		            self::attach_attachment($post_id, $_POST['croc_attachment_id'], $_POST['croc_attachment_title']);
		            }
		                //  Make sure document options are up to date.
		            self::update_attachment_options($post_id, $editable, $downloadable);
    		    }
    		        // If there is no attachment on the form...
    		    else {
	                    //  Remove old attachment from post.
	                self::delete_attachment($post_id);
                }
		    }
    		    //  If there is not a pre-existing attachment on this post...
    		else {
    		      //  If there is an attachment on the form...
    		    if ( isset($_POST['croc_attachment_id']) ) {
                        //  Attach the document to the post and upload to crocodoc.
    		        self::attach_attachment($post_id, $_POST['croc_attachment_id'], $_POST['croc_attachment_title']);
    		            //  Set the document Options.
		            self::update_attachment_options($post_id, $editable, $downloadable);
		        } 
    		}
		}
		return $post_id;   
    }
    
    
	/**
	*
	*  Upload the given attachment to crocodoc and associate it with the given post.
	*
	*  @param string $post_id : The ID of the post being saved.
	*  @param string $attachment_id : The ID of the attachment to be associated with this post.
	*  @param string $attachment_title : The name of the attachment.
	*  @return void
	*  @author Michael Doss
	*/
	private static function attach_attachment($post_id, $attachment_id, $attachment_title="") {
	    
	        //  Define a list of all acceptable file extentions.
	    $valid_file_extentions = array(".pdf",".doc",".docx",".ppt",".pptx",".png",".jpg");
	    
	    global $wpdb;
	    	//  Get the file path of the new attachment.
		$query = "
			SELECT $wpdb->posts.guid
			FROM $wpdb->posts
			WHERE $wpdb->posts.ID = '".$attachment_id."'
		";
		$attachment_guid = $wpdb->get_results($query);
		    //  Get the field extention from the file name/path.	
		$attachment_file_type = $attachment_guid[0]->guid;
        $file_ext = strrchr($attachment_file_type, '.');    
        
            //  If the file type is not acceptable...
        if ( !(in_array($file_ext, $valid_file_extentions)) ) {
            self::set_admin_notice("error", "Crocodoc does not support <strong>".$file_ext."</strong> files.  
                Please select a file with one of the following extensions: <strong>.pdf, .doc, .docx, .ppt, .pptx, .png, .jpg</strong>");
              //  Delete from Media Library.
			if (get_option('croc-delete_media')) {
    		    self::delete_from_media_lib($attachment_id);
    		}
            return;
        }
        
			//  Get Crocodoc API token.
		$token = get_option('croc-api_key');
            //  Upload to croc.
		$json = self::upload_to_croc(wp_get_attachment_url($attachment_id), $attachment_title);
		    //  Get returned data from crocodoc.
		$uuid = $json->{'uuid'};
		$short_id = $json->{'shortId'};
            //  If Crocodoc upload was successful...
		if ($uuid && $short_id) {
			    //  Save the attachmentID, returned crocodoc shortID and uuid for this uploaded file.
		    update_post_meta( $post_id, 'croc_attachment_title', $attachment_title);
		    update_post_meta( $post_id, 'croc_attachment_id', $attachment_id);
		    update_post_meta( $post_id, 'croc_short_id', $short_id);
		    update_post_meta( $post_id, 'croc_uuid', $uuid);
		    self::set_admin_notice("updated", "Your document was successfully uploaded to Crocodoc.");
		    return;
        }
            //  If there was an error uploading the file...
        else{
            self::set_admin_notice("error", "There was an unexpected error uploading your document to Crocodoc.  
                The document was uploaded to your Media Library, but not to Crocodoc. There may be a problem with the file       
                you are trying to upload or with the connection to the Crocodoc API.");
            return;
        }
    }
  

	/**
	*
	*  Set/Update the Crocodoc attachment options for the given post.
	*
	*  @param string $post_id       :   The ID of the post being saved.
	*  @param string $editable      :   Is the document editable.
	*  @param string $downloadable  :   Is the document downloadable.
	*  @return void
	*  @author Michael Doss
	*/    
    private static function update_attachment_options($post_id, $editable, $downloadable) {
        if (get_post_meta($post_id, 'croc_attachment_id')) {
	    	update_post_meta($post_id, 'croc_attachment_editable', $editable);
    		update_post_meta($post_id, 'croc_attachment_downloadable', $downloadable);
        }
    }
    
	/**
	*
	*  Delete the crocodoc attachment meta associated with the given post.
	*  Optionally, delete the attachment from the media library and/or Crocodoc.
	*
	*  @param string $post_id : The ID of the POST that the croc attachment is associated with (not the attachment id).
	*  @return void
	*  @author Michael Doss
	*/
	public static function delete_attachment($post_id) {
            //  If the given post has a croc attachment.        
		if (get_post_meta($post_id, 'croc_attachment_id', true)) {
			    //  Delete from Crocodocs
			if (get_option('croc-delete_croc')) {
				self::delete_from_croc(get_post_meta($post_id, 'croc_uuid', true));
			}
			    //  Delete from Media Library.
			if (get_option('croc-delete_media')) {
    		    self::delete_from_media_lib(get_post_meta($post_id, 'croc_attachment_id', true));
    		}
    		    //  Remove meta data for this croc attachment.
			self::remove_croc_meta($post_id);
		}
	}
	
	
	/**
	*
	*  Callback used by the Shortcode '[crocodoc]'||'[crocodoc height="xxx" width="yyy"] to embed document into post.
	*
	*  @return String 			: String containing an iFrame with associated crocodoc document.
	*  @author Michael Doss
	*/	
	public static function get_crocodoc($atts, $content = null) {
	    $post_id = get_the_ID();
	    $post_type = get_post_type($post_id);
		$post_types_val = get_option('croc-post_types');	
		$valid_post_types = explode('|', $post_types_val);
			//  If this post type is associated with corcodocs...
		if ( in_array($post_type, $valid_post_types) ) {
	            //  Get passed in attributes, fillign in defaults where needed.
	        extract( shortcode_atts( array( 
	            'width' => '500', 
	            'height' => '700'), $atts ) 
	        );
	            //  Return the iframe with embedded doc.
            return self::get_embeded_doc($post_id, $width, $height);
        }
        else {
            return "";
        }
	}
	
	
	/**
	*
	*  Returns an iFrame with associated crocodoc file of the supplied post id.
	*
	*  @param String $post_id 	: The ID of the post associated with the document we want.
	*  @param String $width 	: The width of the returned iframe.
	*  @param String $height 	: The height of the returned iframe.
	*  @return String 			: An iFrame with associated crocodoc file.
	*  @author Michael Doss
	*/
	private static function get_embeded_doc($postID, $width='500', $height='700' ) {
		$uuid = get_post_meta($postID, 'croc_uuid', true);
		$returnString="";
		if($uuid) {
			$editable = get_post_meta($postID, 'croc_attachment_editable', true);
    		$downloadable = get_post_meta($postID, 'croc_attachment_downloadable', true);
			$session = self::get_session($uuid, $editable, $downloadable);
			$sessionId = $session->{'sessionId'};
			$viewURL = "https://crocodoc.com/view/?";
			$viewParams = "sessionId=".$sessionId;
			$returnString = '<iframe class="croc_window" src="'.$viewURL.$viewParams.'"';
			$returnString.= ' width="'.$width.'" height="'.$height.'" style="border: 1px solid #ddd;"></iframe>';
		}
		return $returnString;
	}	
	
	
	/**
	*
	*  Delete the crocodoc uuid, shortID, attachment ID, and attachment title for the given post.
	*
	*  @param string $post_id 		: The ID of the post being disassociated.
	*  @return void
	*  @author Michael Doss
	*/	
	private static function remove_croc_meta($post_id) {
	    delete_post_meta($post_id, 'croc_attachment_downloadable');
	    delete_post_meta($post_id, 'croc_attachment_editable');
		delete_post_meta($post_id, 'croc_attachment_title');	    
		delete_post_meta($post_id, 'croc_attachment_id');
		delete_post_meta($post_id, 'croc_short_id');
		delete_post_meta($post_id, 'croc_uuid');
	}
	
		
	/**
	*
	*  Delete the given attachment from the database.
	*
	*  @param string $post_id 		: The ID of the attachment to delete.
	*  @return void
	*  @author Michael Doss
	*/	
	private static function delete_from_media_lib($attachment_id) {
		global $wpdb;
		    //  Delete the attachment.
		$wpdb->query('DELETE FROM wp_posts WHERE ID='.$attachment_id);
	}

	
	/**
	*
	*  Deletes a document from Crocodoc.
	*
	*  @param string $uuid 		: The Crocodoc uuid for the document to delete.
	*  @return void
	*  @author Michael Doss
	*/
	private static function delete_from_croc($uuid) {
		$token = get_option('croc-api_key');
		$deleteURL = "https://crocodoc.com/api/v1/document/delete?";
		$deleteParams = "uuid=".$uuid."&token=".$token;
		file_get_contents($deleteURL.$deleteParams);
	}
	
	
	/**
	*
	*  Upload a file to crocodoc and returns the json response.
	*
	*  @param string $token 	: The Crocodoc uuid for the document to delete.
	*  @param string $file  	: URL of the file to up-laod.
	*  @param string $title		: Title Crocodoc will give the document.
	*  @return json 			: Example {"shortId": "svNROGy", "uuid": "2286b30c-c84d-4516-aaa7-adc7b9aec174"}
	*  @author Michael Doss
	*/	
	private static function upload_to_croc($file, $title) {
		$token = get_option('croc-api_key');		
		//$file='http://sv-dev.mediahive.com.php5-21.dfw1-1.websitetestlink.com/wp-content/uploads/2011/12/Test-PDF-01.pdf';
		$uploadURL = "https://crocodoc.com/api/v1/document/upload?";
		$uploadParams = "url=".$file."&token=".$token."&title=".$title;
		$response = file_get_contents($uploadURL.$uploadParams);
		return json_decode($response);
	}
		

	/**
	*
	*  Retrieve a Crocodoc viewing session with the desired option.
	*
	*  @param string $uuid     		: The Crocodoc uuid for the document to be viewed.
    *  @param string $editable		: 'true' || 'false'
	*  @param string $downloadable  : 'true' || 'false'
	*  @param string $shareable	    : 'true' || 'false'  Shareable is not a valid 'session/get' argument.
	*  @param string $name			:  The name of the user who will be viewing the document.
	*  @param string $admin			:  'true' || 'false'  :  Does the viewer have admin rights
	*  @return json 				:  Example {"sessionId": "fgH9qWEwnsJUeB0" }
	*  @author Michael Doss
	*/
	private static function get_session($uuid, $editable='false', $downloadable='false', $name='guest', $admin='false') {
		$token = get_option('croc-api_key');
		$sessionURL = "https://crocodoc.com/api/v1/session/get?";
		$sessionParams = "uuid=".$uuid."&token=".$token."&downloadable=".$downloadable;
		$sessionParams .= "&editable=".$editable."&name=".$name."&adimn=".$admin;
		$response = file_get_contents($sessionURL.$sessionParams);
		return json_decode($response);
	}


	/**
	*
	*  Set an admin message to be displayed, and a flag telling WP to display it on the next re-direct.
	*
	*  @param string $type     		:  The message type - 'updated' | 'error'
	*  @param string $message       :  The message displayed to the user. 
	*  @return void
	*  @author Michael Doss
	*/	
	private static function set_admin_notice($type, $message) {
	        //  Store the message to display.
	    update_option('croc_admin_message', $message);
        update_option('croc_admin_message_type', $type);
            //  Turn the display message on.
        update_option('display_croc_admin_message', 1);
	}
	
	
	/**
	*
	*  Display an admin message when required.
	*
	*  @param string $type     		:  The message type - 'updated' | 'error'
	*  @param string $message       :  The message displayed to the user. 
	*  @return void
	*  @author Michael Doss
	*/	
	public static function display_admin_notice() {
	    if (get_option('display_croc_admin_message')) {
	        $message = '<div id="message" class="'.get_option('croc_admin_message_type').'">
                            <p>'.get_option('croc_admin_message').'</p>
                        </div>';
            add_action('admin_notices' , create_function( '', "echo '".$message."';" ) );
            update_option('display_croc_admin_message', 0); // turn off the message
	    }
    }
}
	
?>