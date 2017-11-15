<?php
/*
Plugin Name: Comment Monger
Description: Disable comments based on category
Version:     1.0.0
Author:      Andrew J Klimek
Author URI:  https://github.com/andrewklimek
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Comment Monger is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free 
Software Foundation, either version 2 of the License, or any later version.

Comment Monger is distributed in the hope that it will be useful, but without 
any warranty; without even the implied warranty of merchantability or fitness for a 
particular purpose. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with 
Comment Monger. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Disable Comments
function commentmonger_disable_comments( $open, $post_id ) {

	$cats = explode( ',', get_option('commentmonger') );// don't worry about spaces after commas, term_exists() does trim()
	$cat_ids = array();
	
	foreach ( $cats as $cat ) {	
		$cat_id = (int) term_exists( $cat, 'category' )['term_id'];
		if ( $cat_id ) {
			$cat_ids[] = $cat_id;
			$children = get_term_children( $cat_id, 'category' );
			if ( $children && ! is_wp_error( $children ) ) {
				$cat_ids = array_merge( $cat_ids, $children );
			}
		}
	}
	poo($cat_ids);
	if ( has_term( $cat_ids, 'category' ) ) {
		$open = false;
	}
	return $open;
}
add_filter( 'comments_open', 'commentmonger_disable_comments', 10, 2 );

// Add setting for disabling comments
add_action( 'admin_init', 'commentmonger_settings_init' );

function commentmonger_settings_init() {
	
	register_setting( 'discussion', 'commentmonger' );
	
	add_settings_field( 'commentmonger_cat', 'Disable comments on categories', 'commentmonger_cat', 'discussion' );
	
}

function commentmonger_cat() {
	$setting = get_option('commentmonger');
    ?>
    <input type="text" name="commentmonger" class="regular-text" value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <p class="description" id="commentmonger-description">comma-seperated list of categoies</p>
	 <?php
}