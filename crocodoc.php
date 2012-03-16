<?php

/*
	Plugin Name: Crocodoc
	Plugin URI: http://www.mediahive.com
	Description: Integrate basic Crocodoc services into your wordpress site.
	Version: 1.0
	Author: Michael Doss @ Mediahive
	Author URI: http://www.mediahive.com
	
	Copyright 2012  MediaHive (email : mike.doss@mediahive.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if( IS_ADMIN ) {
		//  Include Crocodoc funcitons.
	include 'crocodoc.class.php';
		//  Add WP admin UI elements.
	add_action('admin_menu', 'croc_init');
		//  Add javascrips functions to footer.
    add_action('admin_footer', 'croc_include_js');
	    //  Add css on user facing pages.
	add_action('wp_head', 'include_font_end_styles');
		//  Upload file to crocodoc when saving a post.
	add_action('save_post', 'Crocodoc::on_post_save');
		//  Add attachment meta field to selected post types.
	add_action('add_meta_boxes', 'Crocodoc::add_meta_box');
		//  Delete file from crocodoc (and optionaly, media library) when deleting post.
	add_action('before_delete_post', 'Crocodoc::delete_attachment');
        //  After re-direct, add croc admin notices.
    add_action('admin_head-post.php', 'Crocodoc::display_admin_notice');
	    //  Add shortcode functionality.
    add_shortcode('crocodoc', 'Crocodoc::get_crocodoc');

}

/**
*  Initialize Plug in.
*
*  @return void
*  @author Michael Doss
*/
function croc_init() {
	croc_create_menuItem();
	wp_enqueue_style( 'crocodoc', WP_PLUGIN_URL.'/crocodoc/css/crocodoc_admin.css' );
}


/**
*  Include css on front end pages that may have crocodoc attachments embedded in them.
*
*  @return void
*  @author Michael Doss
*/
function include_font_end_styles() {
    wp_enqueue_style( 'crocodoc', WP_PLUGIN_URL.'/crocodoc/css/crocodoc_front_end.css' );
}


/**
*  Creates the 'Crocodoc' sub-menu item under 'Settings' in WordPress Admin.
*
*  @return void
*  @author Michael Doss
*/
function croc_create_menuItem() {
	add_options_page('Crocodoc Options', 'Crocodoc', 'manage_options', 'croc-ops', 'croc_create_optionsPage');
}

	
/**
*  Creates the Crocodoc options page and handles storing Crocodoc options.
*
*  @return void
*  @author Michael Doss
*/
function croc_create_optionsPage() {
	include 'crocodoc.options.php';
}


/**
 *  Adds the required Javascript to the admin side.
 *  
 *  @return void
 *  @author Michael Doss
 */
function croc_include_js() {
	$uri = $_SERVER['REQUEST_URI'];
	$file   = basename( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
	if($file == 'post.php' || $file == 'post-new.php') {
		echo('<script type="text/JavaScript" src="'.WP_PLUGIN_URL.'/crocodoc/js/crocodoc.js"></script>');
	}
}



?>
