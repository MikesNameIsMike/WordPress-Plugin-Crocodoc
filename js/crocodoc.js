var croc_thickbox_updater = "";

jQuery(document).ready(function () {
        //  If no document is attached to this post...
    if (jQuery('.croc-attachment-container').length) {
            //  Remove the attach button.
       croc_remove_attach_button();  
    }
    else {
       croc_remove_detach_button(); 
    }  
        //  Add click event to the attach button.
	jQuery('#croc-upload-field .croc-attach-button').live('click', function (event) {
        croc_open_media_browser(event, jQuery(this));
        return false;
    });
        //  Add click event to the detach button
	jQuery('#croc-upload-field .croc-detach-button').live('click', function (event) {
		croc_detach_file_from_post();
		return false;
	});
});
function croc_open_media_browser(event, parent) {
    tb_show('Select a Document', event.target.href, false);
	X = jQuery('#TB_iframeContent').bind('load', function() {
	   croc_update_thickbox_labels();
	});
}
function croc_update_thickbox_labels() {
        //  Change thinkbox heading.
    if(jQuery('#TB_iframeContent').contents().find('.media-title').length) {
        jQuery('#TB_iframeContent').contents().find('.media-title').html("Select a document from from your computer.");
    }
        //  Change how-to message. 
    if(jQuery('#TB_iframeContent').contents().find('.howto').length) {
        jQuery('#TB_iframeContent').contents().find('.howto').html("Ater a file has been uploaded, you can attach it to your post.");
    }
		//  Remove save button.
	if (jQuery('#TB_iframeContent').contents().find('.savebutton #save').length) {
		jQuery('#TB_iframeContent').contents().find('.savebutton #save').remove();
	}
	    //  Define functionality on thickbox X click.
    jQuery('#TB_iframeContent').parent().contents().find('a#TB_closeWindowButton').unbind('click').click( function(e) {
        croc_close_thickbox();
    });
        //  Close thickbox when overlay is clicked.
    jQuery('#TB_overlay').unbind('click').click( function(e) {
        croc_close_thickbox();
    });
    croc_thickbox_updater = setInterval('croc_update_thickbox()', 500);
    
}
function croc_update_thickbox() {
		//  Define on-click function of the 'Attach Document' button.
	jQuery('#TB_iframeContent').contents().find('td.savesend input').unbind('click').click(function(e) {
	        //  Handle the file attachment.   
	    parentObject = jQuery(this).parent().parent().parent();
        docTitle = parentObject.find('tr.post_title td.field input').val();
        docID = parentObject.parent().find('.media-item-info').attr('id').replace('media-head-', '');
        docThumb = parentObject.parent().parent().find('img.pinkynail').attr('src');
		croc_close_thickbox();
        croc_attach_file_to_form(docTitle, docThumb, docID);
	});
		//  Change the 'add to post' button to read 'Attach Document'
	if (jQuery('#TB_iframeContent').contents().find('.media-item .savesend input[type=submit], #insertonlybutton').length) {
        jQuery('#TB_iframeContent').contents().find('.media-item .savesend input[type=submit], #insertonlybutton').val('Attach Document');
    }
		//  Remove standard info elements.
    if (jQuery('#TB_iframeContent').contents().find('#tab-type_url').length) {
        jQuery('#TB_iframeContent').contents().find('#tab-type_url').hide();
    }
    if (jQuery('#TB_iframeContent').contents().find('tr.post_title').length) {
        jQuery('#TB_iframeContent').contents().find('tr.image-size input[value="full"]').prop('checked', true);
        jQuery('#TB_iframeContent').contents().find('tr.post_title,tr.image_alt,tr.post_excerpt,tr.image-size,tr.post_content,tr.url,tr.align,tr.submit>td>a.del-link').hide();
    }
}
function croc_close_thickbox() {
    	//  Remove the ThickBox
	jQuery('#TB_overlay').fadeOut('fast').remove();
	jQuery('#TB_window').fadeOut('fast').remove();
    clearInterval(croc_thickbox_updater);
    return false;
}
function croc_attach_file_to_form(title, thumb, id, editable, downloadable) {
    croc_remove_attach_button();
    croc_show_detach_button();
    croc_attachment = '';
    croc_attachment += '<div class="croc-attachment-container cf">';
    croc_attachment += '    <img class="croc-attachment-thumb cf" src="'+thumb+'" alt="Thumbnail" />';
    croc_attachment += '    <span class="croc-attachment-title cf">'+title+'</span>';
    croc_attachment += '    <div class="croc-attachment-options cf">';
    croc_attachment += '        <input class="checkbox" type="checkbox" name="crocDocOptions[]" value="editable"';
                                if(editable){ croc_attachment+=' checked="checked"'; }
    croc_attachment += '        /><span class="check-label">Editable</span>';
    croc_attachment += '        <input class="checkbox" type="checkbox" name="crocDocOptions[]" value="downloadable"';
                                if(downloadable){ croc_attachment+=' checked="checked"'; }
    croc_attachment += '        /><span class="check-label">Downloadable</span>';
    croc_attachment += '    </div>';    
    croc_attachment += '    <input type="hidden" name="croc_attachment_id" id="croc_attachment_id" value="' + id + '" />';
    croc_attachment += '    <input type="hidden" name="croc_attachment_title" id="croc_attachment_title" value="' + title + '" />';
    
    croc_attachment += '</div>';
    jQuery('#croc-upload-field').append(croc_attachment);
}
function croc_detach_file_from_post() {
    jQuery('.croc-attachment-container').fadeOut('fast').remove();
    croc_remove_detach_button();
    croc_show_attach_button();
}
function croc_show_detach_button() {
		//  Show the attach button.
	if (jQuery('#croc-upload-field .croc-detach-button').length > 0 ) {
		jQuery('#croc-upload-field .croc-detach-button').show();
	}
}
function croc_remove_detach_button() {
		//  Remove the delete button.
    	if (jQuery('#croc-upload-field .croc-detach-button').length > 0 ) {
    		jQuery('#croc-upload-field .croc-detach-button').hide();
	} 
}
function croc_show_attach_button() {
		//  Show the attach button.
	if (jQuery('#croc-upload-field .croc-attach-button').length > 0 ) {
		jQuery('#croc-upload-field .croc-attach-button').show();
	}	
}
function croc_remove_attach_button() {
		//  Remove the delete button.
    	if (jQuery('#croc-upload-field .croc-attach-button').length > 0 ) {
    		jQuery('#croc-upload-field .croc-attach-button').hide();
	} 
}
function croc_admin_notice(type, message) {
    message = '<div id="message" class="'+type+'"><p>'+message+'</p></div>';
    jQuery('#wpbody-content').append(message);
}
