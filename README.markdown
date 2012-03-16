=== Crocodoc ===
Contributors: MikesNameIsMike
Tags: crocodoc, crocodocs, croc, upload, embed, convert, pdf, document, doc, docx, ppt, pptx, png, jpg
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: trunk

The Crocodoc Plugin allows you to upload pdf, doc/docx, ppt/pptx, png and jpg files from Wordpress to Crocodoc and embed them in posts using ShortCode.


== Description ==

Display documents and images in your posts using the Crocodoc API.  Embed .PDF, .DOC, .PPT, .PNG, and .JPG files into your posts.  Supports standard pages and posts as well as custom post types.

The Crocodoc plugin will create a new input box on the edit/add post form of each desired post type.  The new input box allows for the inclusion of a document that will be sent to Crocodoc for conversion when the post is published or updated.  Through the use of ShortCode, you can embed the document into your post.

The Crocodoc Plugin was developed by Michael Doss of www.mediahive.com, which is in no way associated with www.crocodoc.com.  This plugin is not officially supported by Crocodoc.  Neither Crocodoc, or the developer will respond to inquiries or be updating the plugin on a regular basis.


==Usage==

The Crocodoc Plugin requires a valid Crocodoc API key.  A free key can be obtained from http://crocodoc.com/api/.  Crocodoc's API is free for non-commercial use. For commercial applications, visit http://crocodoc.com/partner-program/.

In the Crocodoc Setting page ('Settings' >> 'Crocodoc') enter the api key in the provided field, and select the post types you want Crocodoc to work with.  Crocodoc can work with standard posts and pages as well as any defined custom post types.  Next, select the actions you want the plugin to take when you remove an attachment from a post, or delete a post with a crocodoc attachment.  Be sure to click the 'Save Changes' button.

When creating a post, use the Crocodoc uploader to select a file.  Once a file is uploaded and selected, you will be given options to make the document downloadable and/or editable.  When the post is published, the file will be uploaded to Crocodoc and associated with the given post.

In the content section of your post use the ShortCode [crocodoc] to display the attached document.
The document will be embedded at the default width and height of 500 x 700.
Control the size of the document with parameters 'width' and 'height' : [crocodoc width="300" height="500"]


== Installation ==

1. Upload the folder `crocodoc` to the `/wp-content/plugins/` directory.
1. Activate the plugin 'Crocodoc' through the 'Plugins' menu in WordPress.
1. Select desired settings from the 'Settings' >> 'Crocodoc' screen.


== Frequently Asked Questions ==

= Do I need a Crocodoc account account to use this plugin? =

Yes, you need a unique Crocodoc API key, which you can obtain for free from here: https://crocodoc.com/signup/

= I don't see the Crocodoc section on my create/edit post screen. =

Go to the Crocodoc Setting page ('Settings' >> 'Crocodoc') and select the post types you want Crocodoc to work with.  The Crocodoc metabox will only appear on posts which you select on this screen.

= How do I display an uploaded attachment in my post? =

Use the ShortCode [crocodoc] in your post content to display the attached document.
Control the size of the document with parameters 'width' and 'height' : [crocodoc width="300" height="500"]

= Why does it take longer to publish a post with a crocodoc attachment? =

The Crocodoc plugin must send your file to the crocodoc api, which converts your file into html and stores it on a server, then sends a response back to your site, with information the plugin uses to associate the uploaded document to your post.  This association cannot be made without waiting for the response from crocodoc.com.

= How do I add more then one document to a post? =

Currently, the plugin only supports attaching one document per post.  

= How do I delete a document from crocodoc? =

You can delete documents from crocodoc by selecting the 'delete from crocodoc' option in the settings page, and then deleting the post the attachment is associated with.  Alternatively, you can log directly into Crocodoc.com and manage all the documents you have uploaded.

== Screenshots ==

1. The Crocodoc settings page.
2. The Crocodoc Meta Box in a new post.
3. The Crocodoc Meta Box with a selected file and options.
3. A PDF being displayed on a blog post using Crocodoc.

== Changelog ==

= 1.0 =
* Initial Release
