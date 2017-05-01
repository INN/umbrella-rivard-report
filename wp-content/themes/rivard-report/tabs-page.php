<?php
/**
 * Template Name: Full Width Page plus jQuery Tabs functionality
 *
 * @since May 2017
 */

add_action( 'wp_enqueue_scripts', function() {
	// these are already contained within WordPress
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_style( 'jquery-ui-core' );
	wp_enqueue_style( 'jquery-ui-tabs' );

	wp_enqueue_script( 'tabs-page', get_stylesheet_directory_uri() . '/js/tabs-page.js', array( 'jquery-ui-tabs' ) );
} );
locate_template( 'full-page.php', true );
