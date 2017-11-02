<?php
/**
 * @license GPL 2.0+
 */
 
defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );
define('PB_HIDE_COVER_PROMO', true);

function omniana_theme_setup() {
	// Add theme support for special features here.
}
add_action( 'after_setup_theme', 'omniana_theme_setup' );


$omniana_dir = get_stylesheet_directory();
include_once( $omniana_dir.'/inc/taxonomies.php' );


